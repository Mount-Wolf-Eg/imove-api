<?php

namespace App\Repositories\SQL;

use App\Constants\ConsultationPaymentTypeConstants;
use App\Constants\PaymentMethodConstants;
use App\Constants\PaymentStatusConstants;
use App\Models\{Consultation, Program, ProgramExercise};
use App\Models\GeneralSettings;
use App\Models\Package;
use App\Repositories\Contracts\ConsultationContract;
use App\Repositories\Contracts\CouponContract;
use App\Repositories\Contracts\DoctorContract;
use App\Repositories\Contracts\FileContract;
use App\Repositories\Contracts\NotificationContract;
use App\Repositories\Contracts\SubscriptionContract;
use App\Services\Repositories\ConsultationNotificationService;
use App\Services\Repositories\PaymentCalculator;
use Mpdf\Tag\Sub;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConsultationRepository extends BaseRepository implements ConsultationContract
{
    private ConsultationNotificationService $notificationService;

    /**
     * ConsultationRepository constructor.
     * @param Consultation $model
     * @param ConsultationNotificationService $notificationService
     */
    public function __construct(Consultation $model, ConsultationNotificationService $notificationService)
    {
        parent::__construct($model);
        $this->notificationService = $notificationService;
    }

    public function syncRelations($model, $relations): void
    {
        if (!empty($relations['attachments'])) {
            foreach ($relations['attachments'] as $attachment) {
                $fileModel = resolve(FileContract::class)->find($attachment);
                $model->attachments()->save($fileModel);
            }
        }

        if (!empty($relations['vendors']))
            $model->vendors()->sync($relations['vendors']);

        if (!empty($relations['questions'])) {
            $formatted = collect($relations['questions'])
                ->keyBy('consultation_question_id')
                ->map(function ($item) {
                    return ['answer' => $item['answer']];
                })
                ->toArray();

            $model->consultationQuestions()->sync($formatted);
        }

        // this is temporary, till payment gateway is implemented
        if ($model->amount && !$model->payment) {
            $userId     = $model->patient->user_id;
            $doctorId   = $model->doctor?->user_id;
            $baseAmount = $model->amount;

            // Default values before coupon
            $paymentData = [
                'payer_id'       => $userId,
                'beneficiary_id' => $doctorId,
                'amount'         => $baseAmount,
                'transaction_id' => rand(1000000000, 9999999999),
                'currency_id'    => 1,
                'payment_method' => PaymentMethodConstants::CREDIT_CARD->value,
            ];

            $finalAmount = $baseAmount;

            if (!empty($relations['coupon_code'])) {
                $coupon = resolve(CouponContract::class)->findBy('code', $relations['coupon_code'], false);

                if ($coupon?->isValidForUser($userId, $model->medical_speciality_id)) {
                    $finalAmount                    = $coupon->applyDiscount($baseAmount);
                    $paymentData['coupon_id']       = $coupon->id;
                    $paymentData['coupon_discount'] = $baseAmount - $finalAmount;
                }
            }
            $calculated = app(PaymentCalculator::class)->calc($finalAmount);

            // Append calculated values to both paymentData and model
            $paymentData = array_merge($paymentData, $calculated);

            // $doctor_amount = $baseAmount - ($paymentData['coupon_discount'] ?? 0);

            $model->update([
                'coupon_id'       => $paymentData['coupon_id'] ?? null,
                'coupon_discount' => $paymentData['coupon_discount'] ?? 0,
                'doctor_amount'   => $calculated['doctor_amount'],
                'app_amount'      => $calculated['app_amount'],
                'tax_amount'      => $calculated['tax_amount'],
                'total_amount'    => $calculated['total_amount'],
            ]);

            // If paying by wallet
            if ((int) request()->payment_type === ConsultationPaymentTypeConstants::WALLET->value && request()->package_id == null) {
                $paymentData['status'] = PaymentStatusConstants::COMPLETED->value;

                // Deduct from patient's wallet and add to doctor's wallet
                $model->patient?->user()->decrement('wallet', $calculated['total_amount']);
                $model->doctor?->user()->increment('wallet', $calculated['doctor_amount'],);

                $model->update(['is_active' => true]);
            }

            if ($model->package_id) {
                $package = resolve(SubscriptionContract::class)->findBy('package_id', $model->package_id);
                $package->increment('used_num_of_sessions', 1);
                $model->update(['is_active' => true]);
            }

            // Finally, create the payment record
            $model->payment()->create($paymentData);
        }

        // if ($model->status && $model->isCancelled() && $model->payment) {
        //     $model->payment->update([
        //         'status' => PaymentStatusConstants::REFUNDED->value
        //     ]);
        // }
    }

    protected function updateModelWithDefaultAmount($model): void
    {
        $calculated = app(PaymentCalculator::class)->calc($model->amount);

        $model->update([
            'doctor_amount' => $calculated['doctor_amount'],
            'app_amount'    => $calculated['app_amount'],
            'tax_amount'    => $calculated['tax_amount'],
            'total_amount'  => $calculated['total_amount'],
        ]);
    }

    public function refundAmount($model): void
    {
        $model->payment()->create([
            'payer_id'       => $model->doctor?->user_id,
            'beneficiary_id' => $model->patient->user_id,
            'amount'         => $model->total_amount,
            'transaction_id' => rand(1000000000, 9999999999),
            'currency_id'    => 1,
            'payment_method' => PaymentMethodConstants::WALLET->value,
            'status'         => PaymentStatusConstants::REFUNDED->value
        ]);

        $model->patient?->user()->increment('wallet', $model->total_amount);
        $model->doctor?->user()->decrement('wallet', $model->doctor_amount);
    }

    public function afterCreate($model, $attributes): void
    {
        // $this->notificationService->newConsultation($model);
    }



    // update Or Create Setting Program Exercises
    public function updateOrCreateSettingProgram(Consultation $consultation, array $programData): Program
    {
        return DB::transaction(function () use ($consultation, $programData) {
            // Update Or Create the program
            $program = Program::updateOrCreate(
                ['consultation_id' => $consultation->id], 
                array_merge($programData, ['patient_id' => $consultation->patient_id]) 
            );

            return $program;
        });
    }

    // update or create Diagnosis
    public function updateOrCreateDiagnosis(Consultation $consultation, array $programData): Program
    {
        return DB::transaction(function () use ($consultation, $programData) {
            // Update Or Create the program
            $program = Program::updateOrCreate(
                ['consultation_id' => $consultation->id], 
                array_merge($programData, ['patient_id' => $consultation->patient_id]) 
            );

            return $program;
        });
    }

    // add / Create Program Exercises  
    public function createProgramExercises(Consultation $consultation, array $attributes)
    {
        return DB::transaction(function () use ($consultation, $attributes) {
            // Update Or Create the program
            $program = Program::updateOrCreate(
                ['consultation_id' => $consultation->id], 
                ['patient_id' => $consultation->patient_id] 
            );
            // Create program exercises
            $syncData = [];
            foreach ($attributes['exercises'] as $exercise) {
                // return $exercise;
                $syncData[$exercise['exercise_id']] = [
                    'sets' => $exercise['sets'] ?? null,
                    'break_between_sets' => $exercise['break_between_sets'] ?? null,
                    'weight' => $exercise['weight'] ?? null,
                    'rep' => $exercise['rep'] ?? null,
                    'hold_duration' => $exercise['hold_duration'] ?? null,
                    'comments' => $exercise['comments'] ?? null,
                ];
            }
            $program->exercises()->sync($syncData);

            return $program;
        });
    }

    // delete Create Program Exercises
    public function deleteProgramExercises(Consultation $consultation, array $exerciseIds): Program
    {
        $program = $consultation->program;

        if ($program) {
            ProgramExercise::where('program_id', $program->id)
                ->whereIn('exercise_id', $exerciseIds)
                ->delete();
        }

        return $program;
    }

    public function getSettingProgram(Consultation $consultation): Program
    {
        $program = $consultation->program?? null;

        return $program;
    }


}

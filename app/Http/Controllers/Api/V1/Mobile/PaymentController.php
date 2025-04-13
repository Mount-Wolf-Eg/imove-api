<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Resources\PaymentResource;
use App\Models\GeneralSettings;
use App\Models\Payment;
use App\Repositories\Contracts\PaymentContract;
use Exception;
use Mpdf\Mpdf;

class PaymentController extends BaseApiController
{
    /**
     * PaymentController constructor.
     * @param PaymentContract $paymentContract
     */
    private PaymentContract $paymentContract;

    /**
     * PaymentController constructor.
     * @param PaymentContract $paymentContract
     */
    public function __construct(PaymentContract $paymentContract)
    {
        parent::__construct($paymentContract, PaymentResource::class);
        $this->relations = ['payer', 'beneficiary', 'currency', 'payable'];
    }

    /**
     * Display a listing of the resource.
     * @return mixed
     */
    public function doctorIndex()
    {
        $this->defaultScopes = ['beneficiary' => auth()->id()];
        $totalAmount = $this->contract->sumWithFilters($this->defaultScopes, 'amount');
        $appAmount = $totalAmount * GeneralSettings::getSettingValue('app_payment_percentage');
        $doctorAmount = $totalAmount - $appAmount;
        return parent::index(['total_amount' => $totalAmount, 'app_amount' => $appAmount, 'doctor_amount' => $doctorAmount]);
    }

    /**
     * Display a listing of the resource.
     * @return mixed
     */
    public function patientIndex()
    {
        $user = auth()->user();
        // $this->defaultScopes = ['payer' => $user->id];
        $this->defaultScopes = ['patient' => $user->id];
        return parent::index(['available_balance' => $user->wallet]);
    }

    public function refundRequest()
    {
        if (auth()->user()->wallet <= 0) {
            return $this->respondWithError(__('You have no available balance'), 422);
        }

        if (auth()->user()->bank === null) {
            return $this->respondWithError(__('You have no bank account'), 422);
        }

        $user = auth()->user();

        $model = $this->contract->refundRequest($user, $user->bank->id);

        $user->update(['wallet' => 0]);

        return $model;
    }

    public function destroy(Payment $payment)
    {
        try {
            $this->contract->remove($payment);
            return $this->respondWithSuccess(__('Deleted Successfully'));
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
    }

    public function exportPaymentInvoice(Payment $payment)
    {
        $html = view('invoice.show', compact('payment'))->render();

        $mpdf = new Mpdf([
            'mode'         => 'utf-8',
            'format'       => 'A4',
            'default_font' => 'dejavusans',
        ]);

        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="transactions.pdf"');
    }

    public function exportPaymentAllInvoice()
    {
        $transactions = \App\Models\Payment::ofPatient()
            ->latest()
            ->get();

        $html = view('invoice.all', compact('transactions'))->render();

        $mpdf = new \Mpdf\Mpdf([
            'mode'         => 'utf-8',
            'format'       => 'A4',
            'default_font' => 'dejavusans',
            'tempDir'      => storage_path('app/tmp'),
        ]);

        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="all.pdf"');
    }
}

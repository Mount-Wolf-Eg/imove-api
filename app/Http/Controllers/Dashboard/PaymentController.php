<?php

namespace App\Http\Controllers\Dashboard;

use App\Constants\PaymentMethodConstants;
use App\Constants\PaymentStatusConstants;
use App\Constants\PaymentTypeConstants;
use App\Http\Controllers\BaseWebController;
use App\Models\Payment;
use App\Repositories\Contracts\PatientContract;
use App\Repositories\Contracts\PaymentContract;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaymentController extends BaseWebController
{
    /**
     * PaymentController constructor.
     * @param PaymentContract $contract
     */
    public function __construct(PaymentContract $contract)
    {
        parent::__construct($contract, 'dashboard');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request): View|Factory|Application
    {
        $resources = $this->contract->search($request->all(), ['payer', 'beneficiary', 'currency']);
        $patients = resolve(PatientContract::class)->search([], ['user'], ['limit' => PHP_INT_MAX]);
        $statuses = collect(PaymentStatusConstants::valuesCollection());
        $methods = collect(PaymentMethodConstants::valuesCollection());
        return $this->indexBlade([
            'resources' => $resources,
            'patients' => $patients,
            'statuses' => $statuses,
            'methods' => $methods
        ]);
    }

    public function refundRequest(Request $request): View|Factory|Application
    {
        $resources = $this->contract->search($request->all() + ['type' => PaymentTypeConstants::REFUND->value], ['payer', 'beneficiary', 'currency']);
        $patients = resolve(PatientContract::class)->search([], ['user'], ['limit' => PHP_INT_MAX]);
        $statuses = collect(PaymentStatusConstants::valuesCollection());
        $methods = collect(PaymentMethodConstants::valuesCollection());
        return $this->customBlade('refund', [
            'resources' => $resources,
            'patients' => $patients,
            'statuses' => $statuses,
            'methods' => $methods
        ]);
    }

    public function accept(Request $request, Payment $payment): RedirectResponse
    {
        try {
            // Attempt to accept the payment using the contract
            $this->contract->accept($payment);

            // Redirect back with a success message
            return $this->redirectBack()->with('success', __('messages.actions_messages.update_success'));
        } catch (\DomainException $e) {
            // Handle logical errors (like non-pending status)
            return $this->redirectBack()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            // Handle unexpected errors
            \Log::error('Payment acceptance failed', ['error' => $e->getMessage()]);
            return $this->redirectBack()->with('error', __('messages.actions_messages.update_failed'));
        }
    }

    public function reject(Request $request, Payment $payment): RedirectResponse
    {
        try {
            // Attempt to reject the payment using the contract
            $this->contract->reject($payment);

            // Redirect back with a success message
            return $this->redirectBack()->with('success', __('messages.actions_messages.update_success'));
        } catch (\DomainException $e) {
            // Handle logical errors (like non-pending status)
            return $this->redirectBack()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            // Handle unexpected errors
            \Log::error('Payment rejection failed', ['error' => $e->getMessage()]);
            return $this->redirectBack()->with('error', __('messages.actions_messages.update_failed'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Payment $payment
     *
     * @return RedirectResponse
     */
    public function destroy(Payment $payment): RedirectResponse
    {
        $this->contract->remove($payment);
        return $this->redirectBack()->with('success', __('messages.actions_messages.delete_success'));
    }
}

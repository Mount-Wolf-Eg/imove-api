<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Constants\ConsultationStatusConstants;
use App\Constants\PaymentStatusConstants;
use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Services\Repositories\ConsultationNotificationService;
use App\Traits\BaseApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Contracts\View\View;
use MyFatoorah\Library\MyFatoorah;
use MyFatoorah\Library\API\Payment\MyFatoorahPayment;
use MyFatoorah\Library\API\Payment\MyFatoorahPaymentEmbedded;
use MyFatoorah\Library\API\Payment\MyFatoorahPaymentStatus;
use Exception;

class MyFatoorahController extends Controller
{
    use BaseApiResponseTrait;

    private ConsultationNotificationService $notificationService;

    /**
     * @var array
     */
    public $mfConfig = [];

    /**
     * Initiate MyFatoorah Configuration
     */
    public function __construct(ConsultationNotificationService $notificationService)
    {
        $this->mfConfig = [
            'apiKey'      => config('myfatoorah.api_key'),
            'isTest'      => config('myfatoorah.test_mode'),
            'countryCode' => config('myfatoorah.country_iso'),
        ];

        $this->notificationService = $notificationService;
    }

    /**
     * Redirect to MyFatoorah Invoice URL
     * Provide the getUrl method with the order id and (payment method id or session id)
     *
     * @return Response
     */
    public function getUrl()
    {
        $validatedData = request()->validate([
            'oid' => 'required|exists:consultations,id',
        ]);

        try {
            //For example: pmid=0 for MyFatoorah invoice or pmid=1 for Knet in test mode
            $orderId   = $validatedData['oid'];

            $paymentId = request('pmid') ?: 2;
            $sessionId = request('sid') ?: null;

            $curlData  = $this->getPayLoadData($orderId);

            $mfObj     = new MyFatoorahPayment($this->mfConfig);
            $payment   = $mfObj->getInvoiceURL($curlData, $paymentId, $orderId, $sessionId);

            return $this->respondWithSuccess(data: ['invoiceURL' => $payment['invoiceURL']]);
            // return redirect($payment['invoiceURL']);
        } catch (Exception $ex) {
            $exMessage = __('myfatoorah.' . $ex->getMessage());
            return response()->json(['IsSuccess' => 'false', 'Message' => $exMessage]);
        }
    }

    /**
     * Example on how to map order data to MyFatoorah
     * You can get the data using the order object in your system
     * 
     * @param int|string $orderId
     * 
     * @return array
     */
    private function getPayLoadData($orderId)
    {
        $callbackURL = route('payment.callback');
        $order       = Consultation::withoutGlobalScope('isActive')->findOrFail($orderId); // ->where(['patient_id' => auth()->user()->patient?->id])

        $phone = $order->patient?->user?->phone;

        if ($phone && str_starts_with($phone, '966')) {
            // Remove the '966' prefix
            $phone = substr($phone, 3);
        }

        return [
            'CustomerName'      => $order->patient?->user?->name,
            'InvoiceValue'      => $order->amount + 5, // TODO: handle the extra amount
            'CallBackUrl'       => $callbackURL . '?status=success',
            'ErrorUrl'          => $callbackURL . '?status=fail',
            'Language'          => 'ar',
            'MobileCountryCode' => '+966',
            'CustomerMobile'    => $phone,
            'CustomerReference' => $orderId,
        ];
    }

    /**
     * Get MyFatoorah Payment Information
     * Provide the callback method with the paymentId
     * 
     * @return Response
     */
    public function callback()
    {
        try {
            $paymentId = request('paymentId');

            $mfObj  = new MyFatoorahPaymentStatus($this->mfConfig);
            $data   = $mfObj->getPaymentStatus($paymentId, 'PaymentId');

            $status = $this->getStatus($data->InvoiceStatus);

            $order  = Consultation::withoutGlobalScope('isActive')->where('id', $data->CustomerReference)->first();
            $order?->update(['is_active' => true]);

            if ($status) {
                $order?->payment()->update(['transaction_id' => $paymentId, 'status' => PaymentStatusConstants::COMPLETED->value]);

                if ($order->status == ConsultationStatusConstants::URGENT_PATIENT_APPROVE_DOCTOR_OFFER->value)
                {
                    $this->notificationService->patientAcceptDoctorOffer($order);
                } else {
                    $this->notificationService->newConsultation($order);
                }
            } else {
                $order?->payment()->update(['transaction_id' => $paymentId, 'status' => PaymentStatusConstants::CANCELLED->value]);
                info(json_encode($data));
            }
        } catch (Exception $ex) {
            $exMessage = __('myfatoorah.' . $ex->getMessage());
            info($exMessage);
        }
    }

    /**
     * Example on how to Display the enabled gateways at your MyFatoorah account to be displayed on the checkout page
     * Provide the checkout method with the order id to display its total amount and currency
     * 
     * @return View
     */
    public function checkout()
    {
        try {
            //You can get the data using the order object in your system
            $orderId = request('oid') ?: 147;
            $order   = $this->getTestOrderData($orderId);

            //You can replace this variable with customer Id in your system
            $customerId = request('customerId');

            //You can use the user defined field if you want to save card
            $userDefinedField = config('myfatoorah.save_card') && $customerId ? "CK-$customerId" : '';

            //Get the enabled gateways at your MyFatoorah acount to be displayed on checkout page
            $mfObj          = new MyFatoorahPaymentEmbedded($this->mfConfig);
            $paymentMethods = $mfObj->getCheckoutGateways($order['total'], $order['currency'], config('myfatoorah.register_apple_pay'));

            if (empty($paymentMethods['all'])) {
                throw new Exception('noPaymentGateways');
            }

            //Generate MyFatoorah session for embedded payment
            $mfSession = $mfObj->getEmbeddedSession($userDefinedField);

            //Get Environment url
            $isTest = $this->mfConfig['isTest'];
            $vcCode = $this->mfConfig['countryCode'];

            $countries = MyFatoorah::getMFCountries();
            $jsDomain  = ($isTest) ? $countries[$vcCode]['testPortal'] : $countries[$vcCode]['portal'];

            return view('myfatoorah.checkout', compact('mfSession', 'paymentMethods', 'jsDomain', 'userDefinedField'));
        } catch (Exception $ex) {
            $exMessage = __('myfatoorah.' . $ex->getMessage());
            return view('myfatoorah.error', compact('exMessage'));
        }
    }

    /**
     * Example on how the webhook is working when MyFatoorah try to notify your system about any transaction status update
     */
    public function webhook(Request $request)
    {
        try {
            //Validate webhook_secret_key
            $secretKey = config('myfatoorah.webhook_secret_key');
            if (empty($secretKey)) {
                return response(null, 404);
            }

            //Validate MyFatoorah-Signature
            $mfSignature = $request->header('MyFatoorah-Signature');
            if (empty($mfSignature)) {
                return response(null, 404);
            }

            //Validate input
            $body  = $request->getContent();
            $input = json_decode($body, true);
            if (empty($input['Data']) || empty($input['EventType']) || $input['EventType'] != 1) {
                return response(null, 404);
            }

            //Validate Signature
            if (!MyFatoorah::isSignatureValid($input['Data'], $secretKey, $mfSignature, $input['EventType'])) {
                return response(null, 404);
            }

            //Update Transaction status on your system
            $result = $this->changeTransactionStatus($input['Data']);

            return response()->json($result);
        } catch (Exception $ex) {
            $exMessage = __('myfatoorah.' . $ex->getMessage());
            return response()->json(['IsSuccess' => false, 'Message' => $exMessage]);
        }
    }

    private function changeTransactionStatus($inputData)
    {
        //1. Check if orderId is valid on your system.
        $orderId = $inputData['CustomerReference'];

        //2. Get MyFatoorah invoice id
        $invoiceId = $inputData['InvoiceId'];

        //3. Check order status at MyFatoorah side
        if ($inputData['TransactionStatus'] == 'SUCCESS') {
            $status = 'Paid';
            $error  = '';
        } else {
            $mfObj = new MyFatoorahPaymentStatus($this->mfConfig);
            $data  = $mfObj->getPaymentStatus($invoiceId, 'InvoiceId');

            $status = $data->InvoiceStatus;
            $error  = $data->InvoiceError;
        }

        $message = $this->getMessage($status, $error);

        //4. Update order transaction status on your system
        return ['IsSuccess' => true, 'Message' => $message, 'Data' => $inputData];
    }

    private function getTestOrderData($orderId)
    {
        return [
            'total'    => 15,
            'currency' => 'KWD'
        ];
    }

    private function getMessage($status, $error)
    {
        if ($status == 'Paid') {
            return 'Invoice is paid.';
        } else if ($status == 'Failed') {
            return 'Invoice is not paid due to ' . $error;
        } else if ($status == 'Expired') {
            return $error;
        }
    }

    private function getStatus($status)
    {
        if ($status == 'Paid') {
            return true;
        } else {
            return false;
        }
    }
}

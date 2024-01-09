<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\FirstMerchant;
use App\Services\SecondMerchant;
use Illuminate\Http\Request;

class PaymentController
{
    function callback(Request $request)
    {
        $content_type = $request->header('Content-Type');

        if ($content_type == 'application/json') {
            $payment = Payment::findOrFail($request->payment_id);
            $first_merchant_service = new FirstMerchant($payment);
            $first_merchant_service->updatePaymentStatus();
        } elseif (str_contains($content_type, 'multipart/form-data')) {
            $payment = Payment::findOrFail($request->invoice);
            $first_merchant_service = new SecondMerchant($payment);
            $first_merchant_service->updatePaymentStatus();
        }
    }
}

<?php

namespace App\Services;

use App\Models\Payment;

class SecondMerchant
{
    private $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    public function getSign(): string
    {
        $array_for_sign = $this->getArrayForSign($this->payment) + request()->only('status', 'rand');
        ksort($array_for_sign);
        $pre_sign = implode('.', $array_for_sign) . env('SECOND_MERCHANT_KEY');

        return hash('md5', $pre_sign);
    }

    public function updatePaymentStatus(): bool
    {
        if ($this->getSign() == request()->header('Authorization')) {
            return $this->payment->update(['status' => request()->status]);
        }

        return false;
    }

    private function getArrayForSign(Payment $payment): array
    {
        return [
            'project' => env('SECOND_MERCHANT_ID'),
            'invoice' => $payment->id,
            'amount' => $payment->amount,
            'amount_paid' => $payment->amount,
        ];
    }
}

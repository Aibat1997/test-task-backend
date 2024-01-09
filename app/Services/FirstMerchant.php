<?php

namespace App\Services;

use App\Models\Payment;

class FirstMerchant
{
    private $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    public function getSign(): string
    {
        $array_for_sign = $this->getArrayForSign($this->payment) + request()->only('status', 'timestamp');
        ksort($array_for_sign);
        $pre_sign = implode(':', $array_for_sign) . env('FIRST_MERCHANT_KEY');

        return hash('sha256', $pre_sign);
    }

    public function updatePaymentStatus(): bool
    {
        if ($this->getSign() == request()->sign) {
            return $this->payment->update(['status' => request()->status]);
        }

        return false;
    }

    private function getArrayForSign(Payment $payment): array
    {
        return [
            'merchant_id' => env('FIRST_MERCHANT_ID'),
            'payment_id' => $payment->id,
            "amount" => $payment->amount,
            "amount_paid" => $payment->amount,
        ];
    }
}

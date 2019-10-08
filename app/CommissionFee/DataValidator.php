<?php

namespace App\CommissionFee;

use \Illuminate\Support\Facades\Validator as LaravelValidator;

trait DataValidator
{
    public function validateData($data, $test = false)
    {
        $validator = LaravelValidator::make($data, [
            'transaction_date' => 'required|date|date_format:Y-m-d',
            'user_id' => 'required|numeric',
            'user_type' => 'required|in:natural,legal',
            'operation_type' => 'required|in:cash_in,cash_out',
            'operation_amount' => 'required|numeric',
            'currency' => 'required|in:EUR',
        ]);

        if ($test) {
            return $validator;
        }

        if ($validator->fails()) {
            return false;
        }

        return true;
    }
}

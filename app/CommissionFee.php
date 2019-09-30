<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommissionFee extends Model
{
    const CURRENCY_TRANSACTION_FEE_PERCENTAGES = [
        'EUR' => [
            'cash_in' => '0.0003',
            'cash_out' => ['natural' => '0.003', 'legal' => '0.003']
        ]
    ];
    const CURRENCY_MAXIMUM_AND_MINIMUM_FEES = [
        'EUR' => [
            'cash_in' => ['min' => null, 'max' => '5.00'],
            'cash_out' => [
                'natural' => ['min' => null,'max' => null],
                'legal' => ['min' => '0.50', 'max' => null]
            ]
        ]
    ];
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string description
 * @property string code
 */
class Currency extends Model
{
    const COMMISSION_CHARGES = [
        'EUR' => [
            'cash_in' => ['natural' => '0.03%', 'legal' => '0.03%'],
            'cash_out' => ['natural' => '0.3%', 'legal' => '0.3%']
        ]
    ];

    const MIN_FEES = [
        'EUR' => [
            'cash_in' => [
                'natural' => null,
                'legal' => null,
            ],
            'cash_out' => [
                'natural' => null,
                'legal' => '0.50'
            ]
        ]
    ];
    const MAX_FEES = [
        'EUR' => [
            'cash_in' => [
                'natural' => 5.00,
                'legal' => 5.00,
            ],
            'cash_out' => [
                'natural' => null,
                'legal' => null,
            ]
        ]
    ];

    public function makeByCode($code) : Currency
    {
        $currency = new self();
        $currency->code = $code;

        return $currency;
    }

    public function getMaxFee($operationType, $userType) : float
    {
        return self::MAX_FEES[$this->code][$operationType][$userType] ?? 0.00;
    }

    public function getMinFee($operationType, $userType) : float
    {
        return self::MIN_FEES[$this->code][$operationType][$userType] ?? 0.00;
    }

    public function getCharge($operationType, $userType) : string
    {
        return self::COMMISSION_CHARGES[$this->code][$operationType][$userType] ?? 0.00;
    }

    public function computeChargeFee($operationAmount, $operationType, $userType) : float
    {
        $chargeFee = 0.00;

        $maxFee = $this->getMaxFee($operationType, $userType);
        $minFee = $this->getMinFee($operationType, $userType);
        $charge = $this->getCharge($operationType, $userType);

        if (strpos($charge, '%') !== false) {
            $charge = str_replace('%', '', $charge);
            if ($operationAmount > 0) {
                $chargeFee = ($charge * $operationAmount) / 100;
            }
        } else {
            $chargeFee = $charge;
        }

        if ($maxFee > 0 && $chargeFee > $maxFee) {
            $chargeFee = $maxFee;
        }

        if ($minFee > 0 && $chargeFee < $minFee) {
            $chargeFee = $minFee;
        }

        return $this->roundUp($chargeFee, 2);
    }

    public function roundUp($value, $places)
    {
        $mult = pow(10, abs($places));
        return $places < 0 ?
            ceil($value / $mult) * $mult :
            ceil($value * $mult) / $mult;
    }
}

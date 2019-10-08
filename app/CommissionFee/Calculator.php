<?php

namespace App\CommissionFee;

use App\Abstracts\CommissionCalculatorAbstract;
use App\Currency;

class Calculator extends CommissionCalculatorAbstract
{
    use dataValidator;

    protected $currency;
    protected $data;

    public function __construct(Currency $currency, array $data)
    {
            $this->data = $data;
            $this->currency = $currency;
    }

    public function execute()
    {
        if (!$isValid = $this->validateData($this->data)) {
            return [
                'success' => false,
                'error' => 'Invalid Data.'
            ];
        }

        $data = $this->data;
        $currency = $this->currency;

        $operationAmount = $data['operation_amount'] ?? 0.00;
        $operationType = $data['operation_type'] ?? null;
        $userType = $data['user_type'] ?? null;

        $commissionFee = $currency->computeChargeFee($operationAmount, $operationType, $userType);

        return [
            'success' => true,
            'data' => number_format($commissionFee, '2')
        ];
    }
}

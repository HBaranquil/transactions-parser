<?php

namespace App;

use App\Exceptions\InvalidOperationAmountException;
use App\Exceptions\InvalidOperationCurrencyException;
use App\Exceptions\InvalidOperationTypeException;
use App\Exceptions\InvalidTransactionDateException;
use App\Exceptions\InvalidTransactionDateFormatException;
use App\Exceptions\InvalidUserIdentifierException;
use App\Exceptions\InvalidUserTypeException;
use App\Exceptions\OperationAmountNotFoundException;
use App\Exceptions\OperationCurrencyNotFoundException;
use App\Exceptions\OperationTypeNotFoundException;
use App\Exceptions\TransactionDateNotFoundException;
use App\Exceptions\UserIdentifierNotFoundException;
use App\Exceptions\UserTypeNotFoundException;

class CommissionFeeCalculator
{
    protected $data;

    public function __construct(array  $data)
    {
        $this->data = $data;
    }

    public function execute()
    {
        $data = $this->data;
        $isValid = $this->validate($data);

        if (!$isValid['successful']) {
            return $isValid;
        }

        $commissionFee = $this->calculate($data);

        return [
            'successful' => true,
            'data' => $commissionFee
        ];
    }

    public function calculate(array $data) {
        $transactionDate = $data[0];
        $userId = $data[1];
        $userType = $data[2];
        $operation_type = $data[3];
        $operationAmount = $data[4];
        $currency = strtoupper($data[5]);

        switch ($operation_type){
            case 'cash_in': {
                $commission = CommissionFee::CURRENCY_TRANSACTION_FEE_PERCENTAGES[$currency][$operation_type];
                $commissionFee =  ($operationAmount * $commission);

                $min = CommissionFee::CURRENCY_MAXIMUM_AND_MINIMUM_FEES[$currency][$operation_type]['min'];
                $max = CommissionFee::CURRENCY_MAXIMUM_AND_MINIMUM_FEES[$currency][$operation_type]['max'];
                if ($min) {
                    if ($commissionFee < $min) {
                        $commissionFee = $min;
                    }
                }
                if ($max) {
                    if ($commissionFee > $max) {
                        $commissionFee = $max;
                    }
                }
            } break;
            case 'cash_out': {
                $commission = CommissionFee::CURRENCY_TRANSACTION_FEE_PERCENTAGES[$currency][$operation_type][$userType];
                $commissionFee =  ($operationAmount * $commission);
                $min = CommissionFee::CURRENCY_MAXIMUM_AND_MINIMUM_FEES[$currency][$operation_type][$userType]['min'];
                $max = CommissionFee::CURRENCY_MAXIMUM_AND_MINIMUM_FEES[$currency][$operation_type][$userType]['max'];

                if ($min) {
                    if ($commissionFee < $min) {
                        $commissionFee = $min;
                    }
                }
                if ($max) {
                    if ($commissionFee > $max) {
                        $commissionFee = $max;
                    }
                }
            } break;
            default: {
                $commissionFee = 0;
            }
        }

        $commissionFee = $this->round_up($commissionFee,2);
        return $commissionFee;
    }

    public function round_up($value, $places)
    {
        $mult = pow(10, abs($places));
        return $places < 0 ?
            ceil($value / $mult) * $mult :
            ceil($value * $mult) / $mult;
    }

    public function validate($data)
    {
       $is_valid = $this->validateTransactionDate($data);
       if($is_valid['successful'] == false) return $is_valid;

       $is_valid = $this->validateUserIdentification($data);
       if($is_valid['successful'] == false) return $is_valid;

       $is_valid = $this->validateUserType($data);
       if($is_valid['successful'] == false) return $is_valid;

        $is_valid = $this->validateOperationType($data);
        if($is_valid['successful'] == false) return $is_valid;

        $is_valid = $this->validateOperationAmount($data);
        if($is_valid['successful'] == false) return $is_valid;

        $is_valid = $this->validateOperationCurrency($data);
        if($is_valid['successful'] == false) return $is_valid;

        return $is_valid;
    }

    public function validateTransactionDate($data)
    {
        try {
            if (!isset($data[0])) {
                throw new TransactionDateNotFoundException();
            } else {
                $date = $data[0];
            }

            if ($date == '') {
                throw new TransactionDateNotFoundException();
            } elseif (!isValidDate($date)) {
                throw new InvalidTransactionDateException();
            } elseif (!isDateWithValidFormat('Y-m-d', $date)) {
                throw new InvalidTransactionDateFormatException();
            }
        } catch (TransactionDateNotFoundException $exception) {
            logger()->error($exception->getMessage());
            return  [
                'successful' => false,
                'message' => $exception->getMessage()
            ];
        } catch (InvalidTransactionDateException $exception){
            logger()->error($exception->getMessage());
            return  [
                'successful' => false,
                'message' => $exception->getMessage()
            ];
        } catch (InvalidTransactionDateFormatException $exception) {
            logger()->error($exception->getMessage());
            return  [
                'successful' => false,
                'message' => $exception->getMessage()
            ];
        }
        return [
            'successful' => true,
        ];
    }

    public function validateUserIdentification($data)
    {
        try {
            if (!isset($data[1])) {
                throw new UserIdentifierNotFoundException();
            } else {
                $user_id = $data[1];
            }

            if ($user_id == '') {
                throw new UserIdentifierNotFoundException();
            } elseif (!is_numeric($user_id)) {
                throw  new InvalidUserIdentifierException();
            }
        } catch (UserIdentifierNotFoundException $exception) {
            logger()->error($exception->getMessage());
            return [
                'successful' => false,
                'message' => $exception->getMessage()
            ];
        } catch (InvalidUserIdentifierException $exception) {
            logger()->error($exception->getMessage());
            return [
                'successful' => false,
                'message' => $exception->getMessage()
            ];
        }
        return [
            'successful' => true,
        ];
    }

    public function validateUserType($data)
    {
        try {
            if(!isset($data[2])){
                throw new UserTypeNotFoundException();
            } else {
                $user_type = $data[2];
            }

            if($user_type == ''){
                throw new UserTypeNotFoundException();
            } elseif ($user_type !== 'natural' && $user_type !== 'legal') {
                throw new InvalidUserTypeException();
            }
            return [
                'successful' => true,
            ];
        } catch (UserTypeNotFoundException $exception) {
            logger()->error($exception->getMessage());
            return [
                'successful' => false,
                'message' => $exception->getMessage()
            ];
        } catch (InvalidUserTypeException $exception) {
            logger()->error($exception->getMessage());
            return [
                'successful' => false,
                'message' => $exception->getMessage()
            ];
        }
    }

    public function validateOperationType($data)
    {
        try {
            if(!isset($data[3])){
                throw new OperationTypeNotFoundException();
            } else {
                $operation_type = $data[3];
            }

            if($operation_type == ''){
                throw new OperationTypeNotFoundException();
            } elseif ($operation_type !== 'cash_in' && $operation_type !== 'cash_out') {
                throw new InvalidOperationTypeException();
            }
            return [
                'successful' => true,
            ];
        } catch (OperationTypeNotFoundException $exception) {
            logger()->error($exception->getMessage());
            return [
                'successful' => false,
                'message' => $exception->getMessage()
            ];
        } catch (InvalidOperationTypeException $exception) {
            logger()->error($exception->getMessage());
            return [
                'successful' => false,
                'message' => $exception->getMessage()
            ];
        }
    }

    public function validateOperationAmount($data)
    {
        try {
            if(!isset($data[4])){
                throw new OperationAmountNotFoundException();
            } else {
                $operation_amount = $data[4];
            }

            if($operation_amount == ''){
                throw new OperationAmountNotFoundException();
            } elseif (!is_numeric($operation_amount)) {
                throw new InvalidOperationAmountException();
            }
            return [
                'successful' => true,
            ];
        } catch (OperationAmountNotFoundException $exception) {
            logger()->error($exception->getMessage());
            return [
                'successful' => false,
                'message' => $exception->getMessage()
            ];
        } catch (InvalidOperationAmountException $exception) {
            logger()->error($exception->getMessage());
            return [
                'successful' => false,
                'message' => $exception->getMessage()
            ];
        }
    }

    public function validateOperationCurrency($data)
    {
        try {
            if(!isset($data[5])){
                throw new OperationCurrencyNotFoundException();
            } else {
                $operation_currency = $data[5];
            }

            if($operation_currency == ''){
                throw new OperationCurrencyNotFoundException();
            } elseif (!in_array(strtoupper($operation_currency), Currency::OPERATION_CURRENCIES)) {
                throw new InvalidOperationCurrencyException();
            }
            return [
                'successful' => true,
            ];
        } catch (OperationCurrencyNotFoundException $exception) {
            logger()->error($exception->getMessage());
            return [
                'successful' => false,
                'message' => $exception->getMessage()
            ];
        } catch (InvalidOperationCurrencyException $exception) {
            logger()->error($exception->getMessage());
            return [
                'successful' => false,
                'message' => $exception->getMessage()
            ];
        }
    }
}

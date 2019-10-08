<?php

namespace App\CommissionFee;

trait DataFormatter
{
    public function formatData($row) : array
    {
        return [
            'transaction_date' => $row[0] ?? '',
            'user_id' => $row[1] ?? '',
            'user_type' => $row[2] ?? '',
            'operation_type' => $row[3] ?? '',
            'operation_amount' => $row[4] ?? '',
            'currency' => $row[5] ?? '',
        ];
    }
}

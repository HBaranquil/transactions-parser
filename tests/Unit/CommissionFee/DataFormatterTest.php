<?php

namespace Tests\Unit;

use App\CommissionFee\DataFormatter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DataFormatterTest extends TestCase
{
    use DataFormatter;

    /** @test */
    public function returnsCorrectDataFormat()
    {
        $row = ['2019-01-01', '2','natural','cash_in_out','1200.00','EUR'];

        $expectedData = [
            "transaction_date" => "2019-01-01",
            "user_id" => "2",
            "user_type" => "natural",
            "operation_type" => "cash_in_out",
            "operation_amount" => "1200.00",
            "currency" => "EUR",
        ];

        $formatted = $this->formatData($row);

        $this->assertEquals($formatted, $expectedData);
    }
}

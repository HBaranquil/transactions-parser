<?php

namespace Tests\Unit;

use App\CommissionFee\Calculator;
use App\CommissionFee\DataFormatter;
use App\CommissionFee\dataValidator;
use App\Currency;
use Tests\TestCase;

class CalculatorTest extends TestCase
{
    use  dataValidator, DataFormatter;

    /** @test */
    public function shouldReturnAnErrorMessageIfDataGivenIsInvalid()
    {
        $row = ['2019-01-01', '2','natural','cash_in_out','1200.00','EUR'];
        $data = $this->formatData($row);
        $currency = resolve(Currency::class)->makeByCode('EUR');

        $calculator = new Calculator($currency, $data);
        $result = $calculator->execute();

        $this->assertFalse($result['success']);
        $this->assertEquals($result['error'], 'Invalid Data.');
    }

    /** @test */
    public function shouldReturnCorrectComputedAmountOfCommissionFeeForCashIn()
    {
        $row = ['2019-01-01',2,'natural','cash_in','100.00','EUR'];
        $data = $this->formatData($row);
        $currency = resolve(Currency::class)->makeByCode('EUR');

        $calculator = new Calculator($currency, $data);
        $results = $calculator->execute();

        $this->assertEquals("0.03", $results['data']);
    }

    /** @test */
    public function commissionFeeForCashInMustNotMoreThanTheMaximumFee()
    {
        $row = ['2019-01-01',2,'natural','cash_in','300000.00','EUR'];
        $data = $this->formatData($row);
        $currency = resolve(Currency::class)->makeByCode('EUR');

        $calculator = new Calculator($currency, $data);
        $results = $calculator->execute();

        $this->assertTrue($results['data'] <= '5.00');
        $this->assertTrue($results['data'] == '5.00');
    }

    /** @test */
    public function shouldComputeCorrectAmountOfCommissionFeeForCashOutForNaturalUsers()
    {
        $row = ['2019-01-01',2,'natural','cash_out','1000.00','EUR'];
        $data = $this->formatData($row);
        $currency = resolve(Currency::class)->makeByCode('EUR');

        $calculator = new Calculator($currency, $data);
        $results = $calculator->execute();
        $this->assertEquals("3.00", $results['data']);
    }

    /** @test */
    public function cashOutCommissionFeeForLegalUsersMustNotLessThanTheMinimumFee()
    {
        $row = ['2019-01-01',2,'legal','cash_out','100.00','EUR'];
        $data = $this->formatData($row);
        $currency = resolve(Currency::class)->makeByCode('EUR');

        $calculator = new Calculator($currency, $data);
        $results = $calculator->execute();

        $this->assertTrue($results['data'] >= '0.50');
    }

    /** @test */
    public function commissionFeeShouldBeRoundedToUpperBoundCeiled()
    {
        $row = ['2019-01-01',2,'natural','cash_out','8.00','EUR'];
        $data = $this->formatData($row);
        $currency = resolve(Currency::class)->makeByCode('EUR');

        $calculator = new Calculator($currency, $data);
        $results = $calculator->execute();

        $this->assertEquals('0.03', $results['data']);
    }
}

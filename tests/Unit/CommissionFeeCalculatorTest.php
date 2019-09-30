<?php

namespace Tests\Unit;

use App\CommissionFeeCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Mockery\Mock;
use Monolog\Logger;
use Tests\TestCase;

class CommissionFeeCalculatorTest extends TestCase
{
    /** @test */
    public function will_not_push_through_if_any_of_input_data_is_invalid() {
        $data = ['2019-01-01', '2','natural','cash_in_out','1200.00','EUR'];

        $calculator = new CommissionFeeCalculator($data);
        $results = $calculator->execute();

        $this->assertFalse($results['successful']);
    }

    /** @test */
    public function transaction_date_is_required()
    {
        $data = ['', '2','natural','cash_out','1200.00','EUR'];

        Log::shouldReceive('error')
            ->with('Transaction date not found.')
            ->once();

        $calculator = new CommissionFeeCalculator($data);
        $results = $calculator->validate($data);

        $this->assertFalse($results['successful']);
        $this->assertEquals('Transaction date not found.', $results['message']);

        $data = ['2019-01-01', '2','natural','cash_out','1200.00','EUR'];

        $calculator = new CommissionFeeCalculator($data);
        $results = $calculator->validate($data);

        $this->assertTrue($results['successful']);
    }

    /** @test */
    public function transaction_date_should_be_a_valid_date()
    {
        $data = ['2019-14-01', '2','natural','cash_out','1200.00','EUR'];

        Log::shouldReceive('error')
            ->with('Invalid transaction date.')
            ->once();
        $calculator = new CommissionFeeCalculator($data);
        $results = $calculator->validate($data);

        $this->assertFalse($results['successful']);
        $this->assertEquals('Invalid transaction date.', $results['message']);

        $data = ['2019-01-01', '2','natural','cash_out','1200.00','EUR'];

        $calculator = new CommissionFeeCalculator($data);
        $results = $calculator->validate($data);

        $this->assertTrue($results['successful']);
    }

    /** @test */
    public function transaction_date_should_follow_correct_date_format()
    {
        $data = ['01-10-2019', '2','natural','cash_out','1200.00','EUR'];

        Log::shouldReceive('error')
            ->with('Invalid transaction date format.')
            ->once();
        $calculator = new CommissionFeeCalculator($data);
        $results = $calculator->validate($data);

        $this->assertFalse($results['successful']);
        $this->assertEquals('Invalid transaction date format.', $results['message']);

        $data = ['2019-01-01', '2','natural','cash_out','1200.00','EUR'];

        $calculator = new CommissionFeeCalculator($data);
        $results = $calculator->validate($data);

        $this->assertTrue($results['successful']);
    }

    /** @test */
    public function user_identifier_is_required()
    {
        $data = ['2019-01-01',null,'natural','cash_out','1200.00','EUR'];

        Log::shouldReceive('error')
            ->with('User identifier not found.')
            ->once();
        $calculator = new CommissionFeeCalculator($data);
        $results = $calculator->validate($data);

        $this->assertFalse($results['successful']);
        $this->assertEquals('User identifier not found.', $results['message']);
    }

    /** @test */
    public function user_identifier_should_be_a_number()
    {
        Log::shouldReceive('error')
            ->with('Invalid user identifier.')
            ->once();

        $data = ['2019-01-01','not a number','natural','cash_out','1200.00','EUR'];
        $calculator = new CommissionFeeCalculator($data);
        $results = $calculator->validate($data);

        $this->assertFalse($results['successful']);
        $this->assertEquals('Invalid user identifier.', $results['message']);

        $data = ['2019-01-01','4','natural','cash_out','1200.00','EUR'];
        $calculator = new CommissionFeeCalculator($data);
        $results = $calculator->validate($data);

        $this->assertTrue($results['successful']);
    }

    /** @test */
    public function user_type_is_required()
    {
        $data = ['2019-01-01',2,'','cash_out','1200.00','EUR'];

        Log::shouldReceive('error')
            ->with('User type not found.')
            ->once();
        $calculator = new CommissionFeeCalculator($data);
        $results = $calculator->validate($data);

        $this->assertFalse($results['successful']);
        $this->assertEquals('User type not found.', $results['message']);
    }

    /** @test */
    public function user_type_should_be_natural_or_legal_only()
    {
        $data = ['2019-01-01',2,'nature','cash_out','1200.00','EUR'];

        Log::shouldReceive('error')
            ->with('Invalid user type.')
            ->once(); $calculator = new CommissionFeeCalculator($data);
        $results = $calculator->validate($data);

        $this->assertFalse($results['successful']);
        $this->assertEquals('Invalid user type.', $results['message']);

        $data = ['2019-01-01',2,'natural','cash_out','1200.00','EUR'];

        $calculator = new CommissionFeeCalculator($data);
        $results = $calculator->validate($data);

        $this->assertTrue($results['successful']);

        $data = ['2019-01-01',2,'legal','cash_out','1200.00','EUR'];

        $calculator = new CommissionFeeCalculator($data);
        $results = $calculator->validate($data);

        $this->assertTrue($results['successful']);
    }

    /** @test */
    public function operation_type_is_required()
    {
        $data = ['2019-01-01',2,'natural','','1200.00','EUR'];

        Log::shouldReceive('error')
            ->with('Operation type not found.')
            ->once();
        $calculator = new CommissionFeeCalculator($data);
        $results = $calculator->validate($data);

        $this->assertFalse($results['successful']);
        $this->assertEquals('Operation type not found.', $results['message']);

        $data = ['2019-01-01',2,'natural','cash_in','1200.00','EUR'];

        $calculator = new CommissionFeeCalculator($data);
        $results = $calculator->validate($data);

        $this->assertTrue($results['successful']);
    }

    /** @test */
    public function operation_type_should_be_cash_in_or_cash_out_only()
    {
        $data = ['2019-01-01',2,'natural','cash_only','1200.00','EUR'];

        Log::shouldReceive('error')
            ->with('Invalid operation type.')
            ->once();   $calculator = new CommissionFeeCalculator($data);
        $results = $calculator->validate($data);

        $this->assertFalse($results['successful']);
        $this->assertEquals('Invalid operation type.', $results['message']);

        $data = ['2019-01-01',2,'natural','cash_in','1200.00','EUR'];

        $calculator = new CommissionFeeCalculator($data);
        $results = $calculator->validate($data);

        $this->assertTrue($results['successful']);

        $data = ['2019-01-01',2,'natural','cash_out','1200.00','EUR'];

        $calculator = new CommissionFeeCalculator($data);
        $results = $calculator->validate($data);

        $this->assertTrue($results['successful']);
    }

    /** @test */
    public function operation_amount_is_required()
    {
        $data = ['2019-01-01',2,'natural','cash_in','','EUR'];

        Log::shouldReceive('error')
            ->with('Operation amount not found.')
            ->once();
        $calculator = new CommissionFeeCalculator($data);
        $results = $calculator->validate($data);

        $this->assertFalse($results['successful']);
        $this->assertEquals('Operation amount not found.', $results['message']);

        $data = ['2019-01-01',2,'natural','cash_in','1200.00','EUR'];

        $calculator = new CommissionFeeCalculator($data);
        $results = $calculator->validate($data);

        $this->assertTrue($results['successful']);
    }

    /** @test */
    public function operation_amount_should_be_a_decimal_or_whole_number()
    {
        $data = ['2019-01-01',2,'natural','cash_in','asdf','EUR'];

        Log::shouldReceive('error')
            ->with('Invalid operation amount.')
            ->once();
        $calculator = new CommissionFeeCalculator($data);
        $results = $calculator->validate($data);

        $this->assertFalse($results['successful']);
        $this->assertEquals('Invalid operation amount.', $results['message']);

        $data = ['2019-01-01',2,'natural','cash_in','100.40','EUR'];

        $calculator = new CommissionFeeCalculator($data);
        $results = $calculator->validate($data);

        $this->assertTrue($results['successful']);

        $data = ['2019-01-01',2,'natural','cash_in','100','EUR'];

        $calculator = new CommissionFeeCalculator($data);
        $results = $calculator->validate($data);

        $this->assertTrue($results['successful']);
    }

    /** @test */
    public function operation_currency_is_required()
    {
        $data = ['2019-01-01',2,'natural','cash_in','100.00',''];

        Log::shouldReceive('error')
            ->with('Operation currency not found.')
            ->once();
        $calculator = new CommissionFeeCalculator($data);
        $results = $calculator->validate($data);

        $this->assertFalse($results['successful']);
        $this->assertEquals('Operation currency not found.', $results['message']);

        $data = ['2019-01-01',2,'natural','cash_in','100.00','EUR'];

        $calculator = new CommissionFeeCalculator($data);
        $results = $calculator->validate($data);

        $this->assertTrue($results['successful']);
    }

    /** @test */
    public function it_accepts_EUR_currency_only()
    {
        $data = ['2019-01-01',2,'natural','cash_in','100.00','PHP'];

        Log::shouldReceive('error')
            ->with('Invalid operation currency.')
            ->once();
        $calculator = new CommissionFeeCalculator($data);
        $results = $calculator->validate($data);

        $this->assertFalse($results['successful']);
        $this->assertEquals('Invalid operation currency.', $results['message']);

        $data = ['2019-01-01',2,'natural','cash_in','100.00','EUR'];

        $calculator = new CommissionFeeCalculator($data);
        $results = $calculator->validate($data);

        $this->assertTrue($results['successful']);
    }

    /** @test */
    public function should_provide_correct_computed_amount_of_commission_fee_for_cash_in()
    {
        $data = ['2019-01-01',2,'natural','cash_in','100.00','EUR'];
        $calculator = new CommissionFeeCalculator($data);
        $results = $calculator->execute();

        $this->assertEquals("0.03", $results['data']);
    }


    /** @test */
    public function commission_fee_for_cash_in_must_not_more_than_five_euro()
    {
        $data = ['2019-01-01',2,'natural','cash_in','300000.00','EUR'];
        $calculator = new CommissionFeeCalculator($data);
        $results = $calculator->execute();

        $this->assertTrue($results['data'] <= '5.00');
        $this->assertTrue($results['data'] == '5.00');
    }

    /** @test */
    public function should_compute_correct_amount_of_commission_fee_for_cash_out_of_natural_users()
    {
        $data = ['2019-01-01',2,'natural','cash_out','1000.00','EUR'];
        $calculator = new CommissionFeeCalculator($data);
        $results = $calculator->execute();

        $this->assertEquals("3.00", $results['data']);
    }


    /** @test */
    public function cash_out_commission_fee_for_legal_users_must_not_less_than_five_50_cents_euro()
    {
        $data = ['2019-01-01',2,'legal','cash_out','100.00','EUR'];
        $calculator = new CommissionFeeCalculator($data);
        $results = $calculator->execute();

        $this->assertTrue($results['data'] >= '0.50');
    }

    /** @test */
    public function commission_fee_should_be_rounded_to_upper_bound_ceiled()
    {
        $data = ['2019-01-01',2,'natural','cash_out','8.00','EUR'];
        $calculator = new CommissionFeeCalculator($data);
        $results = $calculator->execute();

        $this->assertEquals('0.03', $results['data']);
    }
}

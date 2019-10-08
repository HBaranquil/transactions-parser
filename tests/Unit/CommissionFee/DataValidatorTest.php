<?php

namespace Tests\Unit;

use App\CommissionFee\DataFormatter;
use App\CommissionFee\dataValidator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DataValidatorTest extends TestCase
{
    use DataValidator, DataFormatter;

    /** @test */
    public function itFailsIfTransactionDateDoesNotExists()
    {
        $row = ['', '2','natural','cash_in','1200.00','EUR'];
        $data = $this->formatData($row);

        $result = $this->validateData($data, true);
        $errors = $result->errors()->messages();

        $this->assertTrue($result->fails());
        $this->assertTrue(array_key_exists('transaction_date', $errors));
    }

    /** @test */
    public function itFailsIfTransactionDateIsNotAValidDate()
    {
        $row = ['2019-13-01', '2','natural','cash_in','1200.00','EUR'];
        $data = $this->formatData($row);

        $result = $this->validateData($data, true);
        $errors = $result->errors()->messages();

        $this->assertTrue($result->fails());
        $this->assertTrue(array_key_exists('transaction_date', $errors));
    }

    /** @test */
    public function itFailsIfTransactionDateIsNotInAProperDateFormat()
    {
        $row = ['01-01-2019', '2','natural','cash_in','1200.00','EUR'];
        $data = $this->formatData($row);

        $result = $this->validateData($data, true);
        $errors = $result->errors()->messages();

        $this->assertTrue($result->fails());
        $this->assertTrue(array_key_exists('transaction_date', $errors));
    }

    /** @test */
    public function itFailsIfUserTypeIsNotOneOfNaturalOrLegal()
    {
        $row = ['2019-01-01', '2','naturalista','cash_in','1200.00','EUR'];
        $data = $this->formatData($row);

        $result = $this->validateData($data, true);
        $errors = $result->errors()->messages();

        $this->assertTrue($result->fails());
        $this->assertTrue(array_key_exists('user_type', $errors));
    }

    /** @test */
    public function itFailsIfOperationTypeIsNotOneOfCashInOrCashOut()
    {
        $row = ['2019-01-01', '2','natural','cash_inside','1200.00','EUR'];
        $data = $this->formatData($row);

        $result = $this->validateData($data, true);
        $errors = $result->errors()->messages();

        $this->assertTrue($result->fails());
        $this->assertTrue(array_key_exists('operation_type', $errors));
    }

    /** @test */
    public function itFailsIfUserIndicatorIsNotANumericId()
    {
        $row = ['2019-01-01', 'invalid_id','natural','cash_in','1200.00','EUR'];
        $data = $this->formatData($row);

        $result = $this->validateData($data, true);
        $errors = $result->errors()->messages();

        $this->assertTrue($result->fails());
        $this->assertTrue(array_key_exists('user_id', $errors));
    }
}

<?php

namespace App\Console\Commands;

use App\CommissionFee\Calculator;
use App\CommissionFee\DataFormatter;
use App\CommissionFee\dataValidator;
use App\Currency;
use Illuminate\Console\Command;

class TransactionsParser extends Command
{
    use dataValidator, DataFormatter;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transactions:parse {file_path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Accepts a file and parse to compute commission charge';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        session()->put('results', []);

        $filepath = $this->argument('file_path');

        if (!file_exists($filepath)) {
            $this->error('Oops! Sorry, file not found');
            exit();
        }

        $file = fopen($filepath, 'r');

        while ($row = fgetcsv($file)) {
            $data = $this->formatData($row);
            $isValidData = $this->validateData($data);

            if (!$isValidData) {
                $this->storeResult('Invalid Data.');
                continue;
            }

            $currency = resolve(Currency::class)->makeByCode($data['currency']);
            $calculator = new Calculator($currency, $data);
            $result = $calculator->execute();

            if ($result['success']) {
                $this->storeResult($result['data']);
            } else {
                $this->storeResult($result['error']);
            }
        }
        $this->displayResults();
    }

    private function storeResult($result)
    {
        session()->push('results', $result);
    }

    private function displayResults()
    {
        $results = session()->get('results');

        foreach ($results as $result) {
            $this->line($result);
        }
    }
}

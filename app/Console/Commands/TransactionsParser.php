<?php

namespace App\Console\Commands;

use App\CommissionFeeCalculator;
use Illuminate\Console\Command;


class TransactionsParser extends Command
{
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
    protected $description = 'Parse a file to compute the commission fee based on the user type, operation type, operation amount, and currency.';

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
            $calculator = new CommissionFeeCalculator($row);
            $result = $calculator->execute();
            if ($result['successful'] == false) {
                $this->storeResult($result['message']);
            } else {
                $this->storeResult(number_format($result['data'],'2','.',','));
            }
        }

        $this->displayResults();
    }

    private function storeResult($result)
    {
        session()->push('results',$result);
    }

    private function displayResults()
    {
        $results = session()->get('results');

        foreach ($results as $result) {
            $this->line($result);
        }
    }
}

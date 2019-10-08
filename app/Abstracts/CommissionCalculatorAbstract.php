<?php

namespace App\Abstracts;

abstract class CommissionCalculatorAbstract
{
    protected $currency = null;

    public function __construct(Currency $currency)
    {
        $this->setCurrency($currency);
    }

    private function setCurrency(Currency $currency)
    {
        $this->currency = $currency;
        return $this;
    }

    abstract public function execute();
}

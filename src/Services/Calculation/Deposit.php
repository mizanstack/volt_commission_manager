<?php

namespace Volt\Services\Calculation;

class Deposit
{
    // type = deposit
    // deposit = 0.03%
    
    protected $commission = 0.03;

    public function getCommission($trans): float
    {
        return $trans->getAmount() * $this->commission / 100;
    }

}

<?php

namespace Volt\Contracts;

interface CommissionInterface
{
    public function getAllTransaction(): array;

    public function getResult(): array;
    
    public function processDataForCommission();
}

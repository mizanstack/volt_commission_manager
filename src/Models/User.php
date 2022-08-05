<?php

namespace Volt\Models;
use Volt\Services\Currency;

class User
{
    private $id;
    private $date;
    private $user_id;
    private $user_type;
    private $operation_type;
    private $amount;
    private $currency;

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setDate(string $date)
    {
        $this->date = $date;
    }

    public function getDate(): string
    {
        return $this->date;
    }


    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id)
    {
        return $this->user_id = $user_id;
    }


    public function getUserType(): string
    {
        return $this->user_type;
    }

    public function setUserType(string $user_type)
    {
        return $this->user_type = $user_type;
    }


    public function getOperationType(): string
    {
        return $this->operation_type;
    }

    public function setOperationType(string $operation_type) 
    {
        return $this->operation_type = $operation_type;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount)
    {
        return $this->amount = $amount;
    }

    public function getAmountInEuro(): float
    {
        return Currency::convertToEuro($this->getAmount(), $this->getCurrency());
    }

    public function getEuroAmountInUserCurrency($amount): float
    {
        return Currency::convertEuroToCurrency($amount, $this->getCurrency());
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency)
    {
        return $this->currency = $currency;
    }
}

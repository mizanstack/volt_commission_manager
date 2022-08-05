<?php

namespace Volt\Services;

class Currency
{
    protected static $config_type = 'file'; // file or url
    protected static $config_json = 'Currency.json';
    protected static $config_url = 'https://developers.paysera.com/tasks/api/currency-exchange-rates';

    public static function convertToEuro(float $amount, string $currency): float
    {
        return (self::getCurrencyRate('EUR') / self::getCurrencyRate($currency)) * $amount;
    }

    public static function convertEuroToCurrency(float $amount, string $currency): float
    {
    	$currency_rate = self::getCurrencyRate($currency);
    	return $amount * $currency_rate;
    }

    public static function getCurrencyRate(string $currency): float
    {
        return self::getCurrency()['rates'][$currency];
    }

    public static function getCurrency(): array
    {
        if(self::$config_type == 'file'){
            return self::getConfigByFile();
        } else if(self::$config_type == 'url'){
            return self::getConfigByUrl();
        }
    }

    public static function getConfigByFile(): array
    {
        $currency_config = file_get_contents(self::$config_json);
        $data = json_decode($currency_config, true);
        return $data;
    }

    public static function getConfigByUrl(): array
    {
        $data = self::curl(self::$config_url);
        return $data;
    }

    public static function curl(string $url): array
    {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		$result = json_decode($response);
		curl_close($ch);
		return object2array($result);
    }


}
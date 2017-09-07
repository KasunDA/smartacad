<?php

/**
 * Created by PhpStorm.
 * User: Kheengz
 * Date: 07/09/2017
 * Time: 7:36 PM
 */
namespace App\Helpers;

class CurrencyHelper
{
    
    const NAIRA = '&#8358;';
    
    public static function format(Float $number, Int $decimal=0)
    {
        return number_format($number, $decimal);
    }
}

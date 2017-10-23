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
    
    public static function format(Float $number, Int $decimal=0, $symbol=false)
    {
        return ($symbol) ? self::NAIRA . ' '. number_format($number, $decimal) : number_format($number, $decimal);
    }

    public static function discount(Float $number, Int $percent)
    {
        if($percent == 0){
            return $number;
        }
        
        return  ($number - (($percent / 100) * $number));
    }
}

<?php

/**
 * Created by PhpStorm.
 * User: Kheengz
 * Date: 07/09/2017
 * Time: 7:36 PM
 */
namespace App\Helpers;

class LabelHelper
{
    
    const VALUE = 'n/a';

    public static function success(String $value=self::VALUE)
    {
        return self::label('success', $value);
    }
    
    public static function danger(String $value=self::VALUE)
    {
        return self::label('danger', $value);
    }

    public static function info(String $value=self::VALUE)
    {
        return self::label('info', $value);
    }

    public static function warning(String $value=self::VALUE)
    {
        return self::label('warning', $value);
    }

    public static function label(String $class, String $value=self::VALUE)
    {
        return "<span class='label label-{$class} label-sm'>{$value}</span>";
    }
}

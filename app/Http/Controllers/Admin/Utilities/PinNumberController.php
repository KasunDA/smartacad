<?php

namespace App\Http\Controllers\Admin\Utilities;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Admin\PinNumbers\Pin;
use App\Models\Admin\PinNumbers\PinNumber;

class PinNumberController extends Controller
{
    /**
     *
     * Make sure the user is logged in
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     * @param Int $number
     * @return Response
     */
    public function index($number = 50)
    {
        $out = '';
        $old = PinNumber::count() + 1;
        for($j = 0; $j < $number; $j++) {
            for ($ran = mt_rand(1, 9), $i = 1; $i < PinNumber::NUMBER_OF_DIGITS; $i++) {
                $ran .= mt_rand(1, 9);
            }
            $space = (PinNumber::SPACING > 0) ? (PinNumber::NUMBER_OF_DIGITS / PinNumber::SPACING) : 4;
            $no = '';
            for($k=0; $k < $space; $k++){
                $no .= substr($ran, ($k * $space), $space) . ' ';
            }
//            $o = substr($ran, 0, 4) . ' ' . substr($ran, 4, 4) . ' ' . substr($ran, 8, 4);
            $serial = trim(str_pad($old + $j,  8, '0', STR_PAD_LEFT));
            $serial1 = substr($serial, 0, 4) . ' ' . substr($serial, 4, 4);
            $out .= 'Serial:'.$serial1.' Pin: '.trim($no) . '<br>';
        }
        var_dump($out);
    }

    /**
     * Display a listing of the resource.
     * @param Int $number
     * @return Response
     */
    public function getGenerate($number = null)//http://localhost:8000/pin-numbers/generate/200
    {
        if ($number) {
            $out = '';
            $old = PinNumber::count() + 1;
            for ($j = 0; $j < $number; $j++) {
                $space = (PinNumber::SPACING > 0) ? (PinNumber::NUMBER_OF_DIGITS / PinNumber::SPACING) : 4;
                $no = '';

                for ($ran = mt_rand(1, 9), $i = 1; $i < PinNumber::NUMBER_OF_DIGITS; $i++) {
                    $ran .= mt_rand(1, 9);
                }
                for ($k=0; $k < $space; $k++) {
                    $no .= substr($ran, ($k * $space), $space) . ' ';
                }

                $serial = trim(str_pad($old + $j,  8, '0', STR_PAD_LEFT));
                $serial1 = substr($serial, 0, 4) . ' ' . substr($serial, 4, 4);
                $out .= 'Serial:'.$serial1.' Pin: '.trim($no) . '<br>';
                Pin::create(['pin' => trim($no), 'serial' => trim($serial1)]);
            }
            var_dump($out);
        } else {
            var_dump(
                '<strong>Kindly put the number of random numbers to be generated /random-numbers/generate/50</strong>'
            );
        }
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function insert()//http://localhost:8000/pin-numbers/insert
    {
        $count = 0;
        $randoms = Pin::whereNotIn('pin', PinNumber::get(['pin_number'])
            ->toArray())
            ->get();

        foreach ($randoms as $random) {
            $ran_no = new PinNumber();
            $ran_no->pin_number = $random->pin;
            $ran_no->serial_number = $random->serial;
            $ran_no->save();
            $count++;
        }
        var_dump(
            '<strong>'.$count.' Random Numbers have been inserted out of '.count($randoms).' generated</strong>'
        );
    }
}

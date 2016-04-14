<?php

namespace App\Http\Controllers;

use Hashids\Hashids;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Set The HashIds Secret Key, Length and Possible Characters Combinations
     * @return Hashids
     */
    public function getHashIds()
    {
        return new Hashids(env('APP_KEY'), 15, env('APP_CHAR'));
    }

    /**
     * @param  string  $msg
     * @param int $type
     * @return void
     */
    public function setFlashMessage($msg, $type)
    {
        $class1 = 'alert-info';
        $class2 = 'fa fa-info fa-2x';

        if($type == 1){
            $class1 = 'alert-success';
            $class2 = 'fa fa-thumbs-o-up fa-2x';
        }elseif($type == 2){
            $class1 = 'alert-danger';
            $class2 = 'fa fa-thumbs-o-down fa-2x';
        }

        $output =   '<div class="alert '.$class1.'" id="flash_message" role="alert">
                        <button type="button" class="close" data-dismiss="alert">
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only">Close</span>
                        </button>
                        <i class="'.$class2.'"></i> <strong>' . $msg . '</strong>'.
            '</div>';
        \Session::flash('flash_message', $output);
    }
}

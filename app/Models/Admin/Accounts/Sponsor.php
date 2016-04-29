<?php

namespace App\Models\Admin\Accounts;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
    /**
     * The table users primary key
     * @var string
     */
    protected $primaryKey = 'sponsor_id';

    /**
     * User Type ID
    */
    const USER_TYPE = 4;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sponsors';

    /**
     * Generate Sponsor No
     * @return String
     */
//    public function generateNo(){
//        return trim('SPN'. str_pad($this->sponsor_id, 5, '0', STR_PAD_LEFT));
//    }
}

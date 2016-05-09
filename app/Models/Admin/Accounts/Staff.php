<?php

namespace App\Models\Admin\Accounts;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    //
    /**
     * The table users primary key
     * @var string
     */
    protected $primaryKey = 'staff_id';

    /**
     * User Type ID
     */
    const USER_TYPE = 4;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'staffs';
}

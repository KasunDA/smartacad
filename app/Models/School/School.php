<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $connection = 'admin_mysql';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'schools';

    /**
     * The table Menus primary key
     *
     * @var int
     */
    protected $primaryKey = 'schools_id';

    /**
     * Path to the files
     */
    public $logo_path = 'uploads/schools/logos/';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'full_name',
        'phone_no',
        'email',
        'motto',
        'website',
        'address',
        'logo',
        'admin_id',
        'status_id',
    ];

    /**
     * School Logo full path
     */
    public function getLogoPath(){
        return ($this->logo) ? DIRECTORY_SEPARATOR . $this->logo_path . $this->logo : false;
    }

    /**
     * A School has one Database
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */

    public function database(){
        return $this->hasOne('App\Models\School\SchoolDatabase', 'schools_id');
    }
}

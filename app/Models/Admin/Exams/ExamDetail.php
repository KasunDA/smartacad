<?php

namespace App\Models\Admin\Exams;

use Illuminate\Database\Eloquent\Model;

class ExamDetail extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'exam_details';
    /**
     * The table permissions primary key
     * @var int
     */
    protected $primaryKey = 'exam_detail_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'student_id',
        'ca',
        'exam',
        'exam_id'
    ];

    /**
     * disable the time stamps
     * @var bool
     */
    public $timestamps = false;

    /**
     * An Assessment Detail Belongs To A Student
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student(){
        return $this->belongsTo('App\Models\Admin\Accounts\Students\Student', 'student_id');
    }

    /**
     * An Assessment Detail Belongs To An Assessment
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function exam(){
        return $this->belongsTo('App\Models\Admin\Exams\Exam', 'exam_id');
    }
}

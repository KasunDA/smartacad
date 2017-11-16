<?php

namespace App\Models\Admin\Items;

use App\Models\Admin\Accounts\Students\Student;
use App\Models\Admin\MasterRecords\AcademicTerm;
use App\Models\Admin\MasterRecords\Classes\ClassRoom;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemVariable extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'price',
        'item_id',
        'student_id',
        'classroom_id',
        'academic_term_id',
    ];

    /**
     * An Item Variable belongs to an Item
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item(){
        return $this->belongsTo(Item::class, 'item_id');
    }

    /**
     * An Item Variable might belongs to a Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student(){
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * An Item Variable might belongs to a class room
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classRoom(){
        return $this->belongsTo(ClassRoom::class, 'classroom_id');
    }

    /**
     * An Item Variable belongs to an Academic Term
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function academicTerm(){
        return $this->belongsTo(AcademicTerm::class, 'academic_term_id');
    }
}

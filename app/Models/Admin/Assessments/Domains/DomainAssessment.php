<?php

namespace App\Models\Admin\Assessments\Domains;

use App\Models\Admin\Accounts\Students\Student;
use App\Models\Admin\MasterRecords\AcademicTerm;
use Illuminate\Database\Eloquent\Model;

class DomainAssessment extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'domain_assessments';
    /**
     * The table permissions primary key
     * @var int
     */
    protected $primaryKey = 'domain_assessment_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['academic_term_id', 'student_id'];

    /**
     * disable the time stamps
     * @var bool
     */
    public $timestamps = false;

    /**
     * A Domain Assessment Has Many Domain Detail
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function domainDetails(){
        return $this->hasMany(DomainDetail::class, 'domain_assessment_id');
    }

    /**
     * An Domain Assessment Belongs To An Academic Term
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function academicTerm(){
        return $this->belongsTo(AcademicTerm::class, 'academic_term_id');
    }

    /**
     * An Domain Assessment Belongs To A Student
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student(){
        return $this->belongsTo(Student::class, 'student_id');
    }
}

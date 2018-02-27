<?php

namespace App\Models\Admin\Exams;

use App\Models\Admin\Accounts\Students\Student;
use App\Models\Admin\MasterRecords\Subjects\SubjectClassRoom;
use App\Models\School\Setups\Subjects\Subject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use PDO;

class ExamDetailView extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'exams_detailsviews';
    /**
     * The table permissions primary key
     * @var int
     */
    protected $primaryKey = 'exam_detail_id';

    /**
     * Marked Exams
     *
     * @param $query
     */
    public function scopeMarked($query){
        return $query->where('marked', 1);
    }

    /**
     * UnMarked Exams
     *
     * @param $query
     */
    public function scopeUnMarked($query){
        return $query->where('marked', '<>', 1);
    }

    /**
     * An exam belongs to a subject
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function subject(){
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * An exam belongs to a student
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function student(){
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * An Assessment Detail Belongs To A Student
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subjectClassroom(){
        return $this->belongsTo(SubjectClassRoom::class, 'subject_classroom_id');
    }

    /**
     * Prepare the broad sheet query
     *
     * @param int $classID
     * @param int $yearID
     * 
     * @return mixed
     */
    public static function prepareBroadSheet($classID, $yearID)
    {
        $pdo = DB::connection()->getPdo();
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

        return DB::select('call sp_examBroadSheet(?,?)', [$classID, $yearID]);
    }
}

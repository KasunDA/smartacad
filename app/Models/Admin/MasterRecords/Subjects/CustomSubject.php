<?php

namespace App\Models\Admin\MasterRecords\Subjects;

use Baum\Node;

/**
* Menu
*/
class CustomSubject extends Node {

  /**
   * Table name.
   *
   * @var string
   */
  protected $table = 'custom_subjects';

  /**
   * The table users primary key
   * @var string
   */
  protected $primaryKey = 'custom_subject_id';

  /**
   * The attributes that are mass assignable.
   * @var array
   */
  protected $fillable = ['name', 'subject_id', 'classgroup_id', 'abbr'];

    /**
     * A Subject Class Room belongs to Subjects
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subject(){
        return $this->belongsTo('App\Models\School\Setups\Subjects\Subject', 'subject_id');
    }

    /**
     * A Class Level Belongs To Class Group
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function classGroup(){
        return $this->belongsTo('App\Models\Admin\MasterRecords\Classes\ClassGroup', 'classgroup_id');
    }
  //////////////////////////////////////////////////////////////////////////////

  //
  // Below come the default values for Baum's own Nested Set implementation
  // column names.
  //
  // You may uncomment and modify the following fields at your own will, provided
  // they match *exactly* those provided in the migration.
  //
  // If you don't plan on modifying any of these you can safely remove them.
  //

  // /**
  //  * Column name which stores reference to parent's node.
  //  *
  //  * @var string
  //  */
  // protected $parentColumn = 'parent_id';

  // /**
  //  * Column name for the left index.
  //  *
  //  * @var string
  //  */
  // protected $leftColumn = 'lft';

  // /**
  //  * Column name for the right index.
  //  *
  //  * @var string
  //  */
  // protected $rightColumn = 'rgt';

  // /**
  //  * Column name for the depth field.
  //  *
  //  * @var string
  //  */
  // protected $depthColumn = 'depth';

   /**
    * Column to perform the default sorting
    *
    * @var string
    */
   protected $orderColumn = 'name';

  // /**
  // * With Baum, all NestedSet-related fields are guarded from mass-assignment
  // * by default.
  // *
  // * @var array
  // */
  // protected $guarded = array('id', 'parent_id', 'lft', 'rgt', 'depth');

  //
  // This is to support "scoping" which may allow to have multiple nested
  // set trees in the same database table.
  //
  // You should provide here the column names which should restrict Nested
  // Set queries. f.ex: company_id, etc.
  //

  // /**
  //  * Columns which restrict what we consider our Nested Set list
  //  *
  //  * @var array
  //  */
  // protected $scoped = array();

  //////////////////////////////////////////////////////////////////////////////

  //
  // Baum makes available two model events to application developers:
  //
  // 1. `moving`: fired *before* the a node movement operation is performed.
  //
  // 2. `moved`: fired *after* a node movement operation has been performed.
  //
  // In the same way as Eloquent's model events, returning false from the
  // `moving` event handler will halt the operation.
  //
  // Please refer the Laravel documentation for further instructions on how
  // to hook your own callbacks/observers into this events:
  // http://laravel.com/docs/5.0/eloquent#model-events

}

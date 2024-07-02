<?php
namespace App\Models;

use CodeIgniter\Model;

class WorkAssignModel Extends Model{
    
    protected $table = WORK_ASSIGN;
	
	protected $allowedFields = ['id', 'wsheet_id', 'program_id', 'course_id', 'user_email', 'user_parameters', 'is_days', 'created_at'];

	protected $primaryKey = 'id';

}



?>
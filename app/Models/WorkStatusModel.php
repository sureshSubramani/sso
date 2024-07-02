<?php
namespace App\Models;

use CodeIgniter\Model;

class WorkStatusModel Extends Model{
    
    protected $table = WORKSTATUS;
	
	protected $allowedFields = ['id', 'wsheet_id', 'program_id', 'course_id', 'user_email', 'start_date', 'days', 'observation', 'modified_at', 'created_at'];

	protected $primaryKey = 'id';

}


?>
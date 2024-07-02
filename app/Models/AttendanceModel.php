<?php
namespace App\Models;

use CodeIgniter\Model;

class AttendanceModel Extends Model{
    
    protected $table = ATTENDANCE;
	
	protected $allowedFields = ['attendance_id', 'program_id', 'user_id', 'reporting_person', 'days', 'comments', 'start_date', 'end_date', 'modified_at', 'created_at'];

	protected $primaryKey = 'attendance_id';
	
}



?>
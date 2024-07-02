<?php
namespace App\Models;

use CodeIgniter\Model;

class CalendarModel Extends Model{
    
    protected $table = CALAENDAR;
	
	protected $allowedFields = [ 'id', 'program_id', 'course_id', 'days', 'is_status', 'modified_at', 'created_at' ];

	protected $primaryKey = 'id';
}



?>
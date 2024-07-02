<?php
namespace App\Models;

use CodeIgniter\Model;

class CourseAllocationModel Extends Model{
    
    protected $table = COURSE_ALLOCATION;
	
	protected $allowedFields = ['id', 'course_id', 'company_id', 'users', 'start_date', 'end_date', 'exp_date', 'venue', 'part','is_form_send', 'is_video_send', 'is_file_send', 'is_form_status', 'is_video_status', 'is_file_status', 'is_users_status', 'is_status', 'modified_at', 'created_at'];

	protected $primaryKey = 'id';
}

?>
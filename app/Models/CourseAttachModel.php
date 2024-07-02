<?php
namespace App\Models;

use CodeIgniter\Model;

class CourseAttachModel Extends Model{
    
    protected $table = COURSE_ATTACHMENT;
	
	protected $allowedFields = ['attach_id', 'user_id', 'course_id', 'type', 'file_name', 'file_path', 'is_status', 'modified_at', 'created_at'];

	protected $primaryKey = 'attach_id';
}



?>
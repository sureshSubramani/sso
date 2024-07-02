<?php
namespace App\Models;

use CodeIgniter\Model;

class FormUserModel Extends Model{
    
    protected $table = FORMUSERS;
	
	protected $allowedFields = ['id', 'course_id', 'form_id', 'type', 'is_form_users', 'is_video_users', 'is_file_users', 'created_at'];

	protected $primaryKey = 'id';
}


?>
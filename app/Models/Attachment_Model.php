<?php
namespace App\Models;

use CodeIgniter\Model;

class Attachment_Model Extends Model{
    
    protected $table = COURSE_ATTACHMENT;
	
	protected $allowedFields = ['attach_id', 'course_id', 'file_name', 'file_path'];

	protected $primaryKey = 'attach_id';

}



?>
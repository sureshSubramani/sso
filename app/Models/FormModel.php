<?php
namespace App\Models;

use CodeIgniter\Model;

class FormModel Extends Model{
    
    protected $table = FORMS;
	
	protected $allowedFields = ['form_id', 'course_id', 'form_name', 'form_json', 'status', 'modified_at', 'created_at'];

	protected $primaryKey = 'form_id';
}



?>
<?php
namespace App\Models;

use CodeIgniter\Model;

class MetaAssignModel Extends Model{
    
    protected $table = METAFOR_ASSIGN;
	
	protected $allowedFields = ['id', 'meta_id', 'course_id', 'user_parameters', 'is_status', 'created_at'];

	protected $primaryKey = 'id';

}



?>
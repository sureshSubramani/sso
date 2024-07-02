<?php
namespace App\Models;

use CodeIgniter\Model;

class MetaForModel Extends Model{
    
    protected $table = METAFOR;
	
	protected $allowedFields = ['metafor_id', 'meta_id', 'course_id', 'user_id', 'rating1', 'rating2', 'before_observation', 'after_observation', 'assign_observation', 'created_at'];

	protected $primaryKey = 'metafor_id';

}



?>
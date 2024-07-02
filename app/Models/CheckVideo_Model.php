<?php
namespace App\Models;

use CodeIgniter\Model;

class CheckVideo_Model Extends Model{
    
    protected $table = CHECK_VIDEO;
	
	protected $allowedFields = ['id', 'submit_id', 'staff_id', 'post_id','form_id', 'rating', 'created_at'];

	protected $primaryKey = 'id';
	
}



?>
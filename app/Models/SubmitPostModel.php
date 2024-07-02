<?php
namespace App\Models;

use CodeIgniter\Model;

class SubmitPostModel Extends Model{
    
    protected $table = POSTS_SUBMIT;
	
	protected $allowedFields = ['submit_id', 'staff_id', 'post_id', 'form_id', 'file_name', 'json_data', 'status', 'created_at'];

	protected $primaryKey = 'submit_id';
}



?>
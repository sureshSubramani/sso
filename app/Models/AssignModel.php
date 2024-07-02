<?php
namespace App\Models;

use CodeIgniter\Model;

class AssignModel Extends Model{
    
    protected $table = ASSIGNEDPOSTS;
	
	protected $allowedFields = ['assign_id', 'post_id', 'json_form', 'json_videos', 'json_files', 'status', 'modified_at', 'created_at'];

	protected $primaryKey = 'assign_id';
}



?>
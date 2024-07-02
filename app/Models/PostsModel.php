<?php
namespace App\Models;

use CodeIgniter\Model;

class PostsModel Extends Model{
    
    protected $table = POSTS;
	
	protected $allowedFields = ['post_id', 'category_id', 'title', 'is_form', 'status', 'modified_at', 'created_at'];

	protected $primaryKey = 'post_id';
	
}



?>
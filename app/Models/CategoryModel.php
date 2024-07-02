<?php
namespace App\Models;

use CodeIgniter\Model;

class CategoryModel Extends Model{
    
    protected $table = CATEGORY;
	
	protected $allowedFields = ['category_id', 'name', 'is_status', 'modified_at', 'created_at'];

	protected $primaryKey = 'category_id';
}



?>
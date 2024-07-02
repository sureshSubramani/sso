<?php
namespace App\Models;

use CodeIgniter\Model;

class RolesModel Extends Model{
    
    protected $table = ROLES;
	
	protected $allowedFields = ['role_id', 'role_name', 'status', 'created_at'];

	protected $primaryKey = 'role_id';
}



?>
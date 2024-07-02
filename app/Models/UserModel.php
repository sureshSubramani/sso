<?php
namespace App\Models;

use CodeIgniter\Model;

class UserModel Extends Model{
    
    protected $table = USERS;
	
	protected $allowedFields = ['user_id','s_id','role_id','user_type','username','email','password','status','is_last_login','ip_address','ip_info','modified_at','created_at'];

	protected $primaryKey = 'user_id';
	
}


?>
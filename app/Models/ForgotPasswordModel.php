<?php
namespace App\Models;

use CodeIgniter\Model;

class ForgotPasswordModel Extends Model{
    
    protected $table = TOKEN;
	
	protected $allowedFields = ['id', 'email', 'token', 'status', 'created_at'];

	protected $primaryKey = 'id';

}



?>
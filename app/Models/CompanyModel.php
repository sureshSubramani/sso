<?php
namespace App\Models;

use CodeIgniter\Model;

class CompanyModel Extends Model{
    
    protected $table = COMPANY;
	
	protected $allowedFields = ['id', 'company_name', 'location', 'status', 'modified_at', 'created_at'];

	protected $primaryKey = 'id';
	
}



?>
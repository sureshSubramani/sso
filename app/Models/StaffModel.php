<?php
namespace App\Models;

use CodeIgniter\Model;

class StaffModel Extends Model{
    
    protected $table = STAFF;
	
	protected $allowedFields = ['s_id', 'company_id', 'emp_id', 'full_name', 'designation', 'dob', 'gender', 'mobile', 'email', 'profile', 'rollno', 'rep_name', 'rep_mail', 'rep_mobile', 'work_experience', 'challenges', 'learn_develop', 'locations', 'status', 'modified_at', 'created_at'];

	protected $primaryKey = 's_id';
}


?>
<?php
namespace App\Models;

use CodeIgniter\Model;

class MenuPreferenceModel Extends Model{
    
    protected $table = MENU_PREFERENCES;
	
	protected $allowedFields = ['preference_id', 'role_id', 'menu_preference', 'status', 'modified_by', 'modified_at', 'created_at'];

	protected $primaryKey = 'preference_id';
}



?>
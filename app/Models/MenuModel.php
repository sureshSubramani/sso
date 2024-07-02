<?php
namespace App\Models;

use CodeIgniter\Model;

class MenuModel Extends Model{
    
    protected $table = MENUS_DETAILS;
	
	protected $allowedFields = ['menu_id', 'menu_position', 'menu_name', 'menu_icon', 'menu_url', 'status', 'modified_by', 'modified_at', 'created_by', 'created_at'];

	protected $primaryKey = 'menu_id';
}



?>
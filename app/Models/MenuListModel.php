<?php
namespace App\Models;

use CodeIgniter\Model;

class MenuListModel Extends Model{
    
    protected $table = MENU_LIST;
	
	protected $allowedFields = ['menu_list_id', 'menu_position', 'menu_id', 'child_name', 'child_url', 'status', 'modified_by', 'modified_at', 'created_by', 'created_at'];

	protected $primaryKey = 'menu_list_id';
}



?>
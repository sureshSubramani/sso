<?php
namespace App\Models;

use CodeIgniter\Model;

class WorksheetModel Extends Model{
    
    protected $table = WORKSHEET;
	
	protected $allowedFields = ['wsheet_id', 'type', 'parameters', 'practice_name', 'is_status', 'modified_at', 'created_at'];

	protected $primaryKey = 'wsheet_id';

}



?>
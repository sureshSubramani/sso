<?php
namespace App\Models;

use CodeIgniter\Model;

class ObservationModel Extends Model{
    
    protected $table = OBSERVATIONS;
	
	protected $allowedFields = ['id', 's_id', 'observation'];

	protected $primaryKey = 'id';
}


?>
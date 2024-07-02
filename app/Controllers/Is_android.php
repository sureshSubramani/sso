<?php

namespace App\Controllers;
use CodeIgniter\Controller;  

class Is_android extends Controller
{
    function __construct(){
        date_default_timezone_set('Asia/Kolkata');
        helper(['form', 'url']);
        $this->db = db_connect(); // Loading database
		$this->session	= \Config\Services::session();
    }

    public function index(){

        $isAndroid = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]),"android")); 

        if($isAndroid && @$_POST)
        echo "It's android";
        else 
        echo "It's system";
        
    }

}


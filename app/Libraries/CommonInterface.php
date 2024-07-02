<?php

namespace App\Controllers;
namespace App\Libraries;
use CodeIgniter\Controller;

class CommonInterface{

    function __construct() {
        $this->db = db_connect(); // Loading database
		$this->session	= \Config\Services::session();	

		
  		#require('router.php');

		if(@$_SESSION['LAST_ACTIVITY'] && (time() - session()->get('LAST_ACTIVITY') > 1800)){
			session()->remove('LAST_ACTIVITY');
			session()->remove('user_details');
			header('Location: '.base_url('login'));
			exit();
		}	
				
		session()->set('LAST_ACTIVITY',time()); //update last activity time stamp
	}

	public function fetch_all_menu1(){

		$get_preference = $menu_array = $primary_menu = array();
		#echo '<pre>'; print_r(session('user_details')['role_id']); die;

		//$this->db->table(MENU_PREFERENCES)->where('role_id',json_decode(session('user_details')['role_id']))->select('menu_preference')->get()->getRowArray();

		//echo $this->db->getLastQuery(); die;

		if(@trim(session()->get('LAST_ACTIVITY'))){	

			if(@$get_preference = $this->db->table(MENU_PREFERENCES)->where('role_id',json_decode(session('user_details')['role_id']))->select('menu_preference')->get()->getRowArray()){
				#echo '<pre>'; print_r($get_preference); die;	
				$decode = json_decode($get_preference['menu_preference'],true);
				#echo '<pre>'; print_r($decode); die;

				foreach ($decode as $key1 => $menu) {
					//echo $menu; die;
					$primary_menu = $this->db->table(MENUS_DETAILS)->where('menu_id',$menu['main_menu'])->select('menu_id,menu_position,menu_name,menu_icon,menu_url')->get()->getRowArray();
		
					$get_sub_menu = array();		
					if($menu['main_menu']>0){								
						if(!empty($menu['sub_menu'])){					
							foreach($menu['sub_menu'] as $key2 => $value) {
								$get_sub_menu[] = $this->db->table(MENU_LIST)->where('menu_list_id',$value)->select('menu_list_id,menu_id,child_name,child_url')->get()->getRowArray();
							}
						}
					}
		
					$menu_array[$key1] = array_merge($primary_menu,array('child_menu'=>$get_sub_menu));
				}
			}
			 
		}else{
			return redirect()->to(base_url('login'));
		}
		
		#echo '<pre>'; print_r($menu_array); die;

		return $menu_array;
    }

	public function fetch_all_url(){

		$get_preference = $all_url_array = $primary_url = $child_urls = $menu_array = array();

		if(@trim(session()->get('LAST_ACTIVITY'))){

			if(@$get_preference = $this->db->table(MENU_PREFERENCES)->where('role_id',json_decode(session('user_details')['role_id']))->select('menu_preference')->get()->getRowArray()){
				
				$decode = json_decode($get_preference['menu_preference'],true);

				foreach ($decode as $key1 => $menu) {			
					$primary_url = $this->db->table(MENUS_DETAILS)->where('menu_id',$menu['main_menu'])->select('menu_url')->get()->getRowArray();
					if(!empty($primary_url['menu_url'])) 
					$all_url_array[] = $primary_url['menu_url'];

					$get_child_url = array();
					if($menu['main_menu']>0){				
						if(!empty($menu['sub_menu'])){
							foreach($menu['sub_menu'] as $key2 => $value) {
								$get_child_url = $this->db->table(MENU_LIST)->where('menu_list_id',$value)->select('child_url')->get()->getRowArray();
								array_push($all_url_array,$get_child_url['child_url']);
							}
						}
					}

				}
			}

		}else{
			return redirect()->to(base_url('login'));
		}
		
		return $all_url_array;
    }
	
}
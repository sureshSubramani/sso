<?php

namespace App\Controllers;
use CodeIgniter\Controller;

use App\Models\UserModel; 
use App\Models\RolesModel;
use App\Models\MenuModel; 
use App\Models\MenuListModel; 
use App\Models\MenuPreferenceModel;
use App\Models\CollegeModel;
use App\Models\StaffModel; 
use App\Models\ActionModel;
use App\Models\DepartmentsModel;
use App\Models\StaffDetailsModel;
use App\Models\HolidaysModel;
use App\Models\StaffAttendanceModel;
use App\Models\AttendanceLogModel;
use App\Models\AttendancePermissionModel;
use App\Models\LeaveManagementModel;
use App\Models\StaffLeaveModel;
use App\Models\StaffAlterLeaveModel;
use App\Models\LeaveModel;
use App\Models\LeaveLogsModel;
use App\Models\CommonMailModel;

use App\Libraries\CommonInterface;

class Profile extends Controller
{
    
    function __construct(){

        $this->userModel  = new UserModel();
        $this->rolesModel = new RolesModel();
        $this->actionModel = new ActionModel();
        $this->menuModel = new MenuModel();
        $this->menuListModel = new MenuListModel();
        $this->menuPreferenceModel = new MenuPreferenceModel();
        $this->staffModel = new StaffModel();
        $this->collegeModel = new CollegeModel();
        $this->departmentsModel = new DepartmentsModel();
        $this->staffDetailsModel = new StaffDetailsModel();
        $this->holidaysModel = new HolidaysModel();
        $this->staffAttendanceModel = new StaffAttendanceModel();
        $this->attendanceLogModel = new AttendanceLogModel();
        $this->leaveManagementModel = new LeaveManagementModel();
        $this->attendancePermissionModel = new AttendancePermissionModel();
        $this->staffLeaveModel = new StaffLeaveModel();
        $this->staffAlterLeaveModel = new StaffAlterLeaveModel();
        $this->leaveModel = new LeaveModel();
        $this->leaveLogsModel = new LeaveLogsModel();
        $this->commonMailModel = new CommonMailModel();

        $this->commonInterface = new CommonInterface();

        helper(['form', 'url']);
        $this->db = db_connect(); // Loading database
		$this->session	= \Config\Services::session(); 

    }

    public function index(){

        //echo '<pre>'; print_r(session('user_details')); die;
        $data = array();
        $data['title'] = 'Mahendra Educational Institutions';
        
        echo view('common/header',$data);
        echo view('profile',$data);
        echo view('common/footer');
        //return view('dashboard');
    }

    public function menu_management(){

        $data = array();
        $data['title'] = 'Menu Management';

        //echo '<pre>'; print_r(session('user_details')); die;
        
        $data['all_menus']=$this->fetch_all_menu();

        if($this->request->getPost()){

                $insert_menus = array();
    
                for($i=0; $i<count($this->request->getVar('main_menus')); $i++){
    
                    if(@$this->request->getVar('sub_menus_'.$this->request->getVar('main_menus')[$i])){
                        $sub_menus = $this->request->getVar('sub_menus_'.$this->request->getVar('main_menus')[$i]);
                    }else{
                        $sub_menus = '';
                    }
                    $insert_menus[] = array('main_menu'=>$this->request->getVar('main_menus')[$i],'sub_menu'=>$sub_menus);    
                }

            $isExist=$this->menuPreferenceModel->where('role_id',$this->request->getVar('preference_id'))->get()->getNumRows();

            $insert_array = array('role_id'=>$this->request->getVar('preference_id'),'menu_preference'=>json_encode($insert_menus));

            if($isExist){
                $this->menuPreferenceModel->where('role_id',$this->request->getVar('preference_id'))->set(array('menu_preference'=>json_encode($insert_menus),'modified_by'=>session('user_details')['staff_id'],'modified_at'=>date('Y-m-d')))->update(); 
                session()->setFlashdata('success','Successfully menu permissions updated..');
            }else{
                $this->menuPreferenceModel->save($insert_array);
                session()->setFlashdata('success','Successfully menu permissions added..');
            }

            return redirect()->to(base_url('menu_management')); 
        }
        
        echo view('common/header',$data);
        echo view('menu_management',$data);
        echo view('common/footer');
    }

    public function fetch_role_details(){

        $rowperpage = 10;
        $start = 0;
        $draw = 9;
        $totalRecords = $columnName = $columnSortOrder = 0;
        $response = $records = $data = array();

        if($this->request->getVar('type')==='roles'){

			if(@$this->request->getVar('draw'))
			$draw = $this->request->getVar('draw');

			if(@$this->request->getVar('start'))
			$start = $this->request->getVar('start');

			if(@$this->request->getVar('length'))
			$rowperpage = $this->request->getVar('length'); // Rows display per page

			if(@$this->request->getVar('order')[0]['column'])
			$columnIndex = $this->request->getVar('order')[0]['column']; // Column index

			if(@$this->request->getVar('columns')[$columnIndex]['data'])
			$columnName = $this->request->getVar('columns')[$columnIndex]['data']; // Column name

			if(@$this->request->getVar('order')[0]['dir'])
			$columnSortOrder = $this->request->getVar('order')[0]['dir']; // asc or desc

			if(@$this->request->getVar('search')['value'])
			$searchValue = $this->request->getVar('search')['value']; // Search value           

            $records = $this->db->table($this->rolesModel->table.' as r')
                        //->join($this->collegeModel->table.' as c','c.college_id = r.college_id','LEFT')
                        ->select('r.role_id,r.role_name,r.status')
                        ->orderBy($columnName, $columnSortOrder)->groupBy('r.role_name')->limit($rowperpage, $start)->get()->getResult();

            $totalRecords = $this->db->table($this->rolesModel->table.' as r')->countAllResults();

            $i=1;
            foreach ($records as $k=>$r) {
                $data[] = array( 
                    "sno"=>$i++,
                    "role_id"=>$r->role_id,
                    "role_name"=>$r->role_name,
                    "status"=>$r->status
                    );
            }

        }
                    
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecords,
            "data" => $data
            );

        echo json_encode($response);

    }

    public function fetch_action_details(){

        $rowperpage = 10;
        $start = 0;
        $draw = 9;
        $totalRecords = $columnName = $columnSortOrder = 0;
        $response = $records = $data = array();

        if($this->request->getVar('type')==='actions'){

			if(@$this->request->getVar('draw'))
			$draw = $this->request->getVar('draw');

			if(@$this->request->getVar('start'))
			$start = $this->request->getVar('start');

			if(@$this->request->getVar('length'))
			$rowperpage = $this->request->getVar('length'); // Rows display per page

			if(@$this->request->getVar('order')[0]['column'])
			$columnIndex = $this->request->getVar('order')[0]['column']; // Column index

			if(@$this->request->getVar('columns')[$columnIndex]['data'])
			$columnName = $this->request->getVar('columns')[$columnIndex]['data']; // Column name

			if(@$this->request->getVar('order')[0]['dir'])
			$columnSortOrder = $this->request->getVar('order')[0]['dir']; // asc or desc

			if(@$this->request->getVar('search')['value'])
			$searchValue = $this->request->getVar('search')['value']; // Search value           

            $records = $this->db->table($this->actionModel->table.' as a')
                   ->select('a.action_id,a.action_name,a.status')
                    ->orderBy($columnName, $columnSortOrder)->groupBy('action_name')->limit($rowperpage, $start)->get()->getResult();

            $totalRecords = $this->db->table($this->actionModel->table.' as a')->countAllResults();

            $i=1;
            foreach ($records as $k=>$r) {
                $data[] = array( 
                    "sno"=>$i++,
                    "action_id"=>$r->action_id,
                    "action_name"=>$r->action_name,
                    "status"=>$r->status
                    );
            }
        }
                    
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecords,
            "data" => $data
            );

        echo json_encode($response);

    }

    public function fetch_menu_details(){

        $rowperpage = 10;
        $start = 0;
        $draw = 9;
        $totalRecords = $columnName = $columnSortOrder = 0;
        $response = $records = $data = array();
        
        $columnName='menu_position';

        if($this->request->getVar('type')==='menus'){

			if(@$this->request->getVar('draw'))
			$draw = $this->request->getVar('draw');

			if(@$this->request->getVar('start'))
			$start = $this->request->getVar('start');

			if(@$this->request->getVar('length'))
			$rowperpage = $this->request->getVar('length'); // Rows display per page

			if(@$this->request->getVar('order')[0]['column'])
			$columnIndex = $this->request->getVar('order')[0]['column']; // Column index

			if(@$this->request->getVar('columns')[$columnIndex]['data'])
			$columnName = $this->request->getVar('columns')[$columnIndex]['data']; // Column name

			if(@$this->request->getVar('order')[0]['dir'])
			$columnSortOrder = $this->request->getVar('order')[0]['dir']; // asc or desc

			if(@$this->request->getVar('search')['value'])
			$searchValue = $this->request->getVar('search')['value']; // Search value 

            $records = $this->menuModel->select('menu_id,menu_position,menu_name,menu_icon,menu_url,status')->orderBy($columnName, $columnSortOrder)->limit($rowperpage, $start)->get()->getResult();

            $totalRecords = $this->db->table($this->menuModel->table.' as m')->countAllResults();
           
            //print_r($records); die;
            //$i=1;
            foreach ($records as $k=>$r) {

                $data[] = array( 
                    "menu_position"=>$r->menu_position,
                    "menu_id"=>$r->menu_id,
                    "menu_name"=>$r->menu_name,
                    "menu_icon"=>$r->menu_icon,
                    "menu_url"=>$r->menu_url,
                    "status"=>$r->status
                    );
            }
        }
                    
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecords,
            "data" => $data
            );

        echo json_encode($response);
    }

    public function add_menu(){

        if(@$this->request->getVar('role_name')){

            try{

                if($this->request->getVar('role_id')===''){
                    $res = $this->rolesModel->save(array('role_name'=>$this->request->getVar('role_name')));
                    if($res)
                    echo json_encode(array('isSuccess'=>1));
                    else
                    echo json_encode(array('isSuccess'=>0));                    
                }else{
                    $res = $this->rolesModel->where('role_id',$this->request->getVar('role_id'))->set(array('role_name'=>$this->request->getVar('role_name')))->update();
                    if($res)
                    echo json_encode(array('isSuccess'=>2));
                    else
                    echo json_encode(array('isSuccess'=>0));
                }

            }catch(\Exception $e){
                print_r($e->getMessage()); //die();
                echo json_encode(array('isError'=>$e->getMessage()));
            }

        }else if(@$this->request->getVar('menu_name')){
                    
            try{

                if($this->request->getVar('menu_id')===''){
                    $data = array(
                            'menu_name'=>$this->request->getVar('menu_name'),
                            'menu_icon'=>$this->request->getVar('menu_icon'),
                            'menu_url'=>$this->request->getVar('menu_url'),
                            'created_by'=>session()->get('user_details')['user_id']
                        );
                    $res = $this->menuModel->save($data);
                    if($res)
                    echo json_encode(array('isSuccess'=>1));
                    else
                    echo json_encode(array('isSuccess'=>0));                    
                }else{

                    $data = array(
                        'menu_name'=>$this->request->getVar('menu_name'),
                        'menu_icon'=>$this->request->getVar('menu_icon'),
                        'menu_url'=>$this->request->getVar('menu_url'),
                        'modified_by'=>session()->get('user_details')['user_id'],
                        'modified_at'=>date('Y-m-d H:i:s')
                    );

                    $res = $this->menuModel->where('menu_id',$this->request->getVar('menu_id'))->set($data)->update();
                    if($res)
                    echo json_encode(array('isSuccess'=>2));
                    else
                    echo json_encode(array('isSuccess'=>0));
                }

            }catch(\Exception $e){
                print_r($e->getMessage()); //die();
                echo json_encode(array('isError'=>$e->getMessage()));
            }

        }else if(@$this->request->getVar('action_name')){
                    
            try{

                if($this->request->getVar('action_id')===''){
                    $data = array(
                            'action_name'=>$this->request->getVar('action_name')
                        );
                    $res = $this->actionModel->save($data);
                    if($res)
                    echo json_encode(array('isSuccess'=>1));
                    else
                    echo json_encode(array('isSuccess'=>0));                    
                }else{

                    $data = array(
                        'action_name'=>$this->request->getVar('action_name'),
                        'modified_at'=>date('Y-m-d H:i:s')
                    );

                    $res = $this->actionModel->where('action_id',$this->request->getVar('action_id'))->set($data)->update();
                    if($res)
                    echo json_encode(array('isSuccess'=>2));
                    else
                    echo json_encode(array('isSuccess'=>0));
                }

            }catch(\Exception $e){
                print_r($e->getMessage()); //die();
                echo json_encode(array('isError'=>$e->getMessage()));
            }

        }else if(@$this->request->getVar('department_name')){
            try{

                if($this->request->getVar('department_id')===''){
                    $data = array(
                            'college_id'=>$this->request->getVar('college_id'),
                            'department_name'=>$this->request->getVar('department_name')
                        );
                    $res = $this->departmentsModel->save($data);
                    if($res)
                    echo json_encode(array('isSuccess'=>1));
                    else
                    echo json_encode(array('isSuccess'=>0));                    
                }else{

                    $data = array(
                        'college_id'=>$this->request->getVar('college_id'),
                        'department_name'=>$this->request->getVar('department_name')
                    );

                    $res = $this->departmentsModel->where('department_id',$this->request->getVar('department_id'))->set($data)->update();
                    if($res)
                    echo json_encode(array('isSuccess'=>2));
                    else
                    echo json_encode(array('isSuccess'=>0));
                }

            }catch(\Exception $e){
                print_r($e->getMessage()); //die();
                echo json_encode(array('isError'=>$e->getMessage()));
            }

        }
    }

    public function update_menu(){

        if($this->request->getMethod()=='post'){
            $data = array();
            if($this->request->getVar('role_id')){
                $data = $this->rolesModel->select('role_id,role_name')->where('role_id',$this->request->getVar('role_id'))->get()->getRowArray();

                echo json_encode($data);
            }else if($this->request->getVar('menu_id')){

                $data = $this->menuModel->select('menu_id,menu_name,menu_icon,menu_url')->where('menu_id',$this->request->getVar('menu_id'))->get()->getRowArray();

                echo json_encode($data);
            }else if($this->request->getVar('menu_list_id')){

                $data = $this->menuListModel->select('menu_list_id,child_name,child_url')->where('menu_list_id',$this->request->getVar('menu_list_id'))->get()->getRowArray();

                echo json_encode($data);
            }else if($this->request->getVar('action_id')){

                $data = $this->actionModel->select('action_id,action_name')->where('action_id',$this->request->getVar('action_id'))->get()->getRowArray();

                echo json_encode($data);
            }else if($this->request->getVar('college_id')){

                $data = $this->collegeModel->select('*')->where('college_id',$this->request->getVar('college_id'))->get()->getRowArray();

                echo json_encode($data);
            }else if($this->request->getVar('department_id')){

                $data = $this->departmentsModel->select('*')->where('department_id',$this->request->getVar('department_id'))->get()->getRowArray();

                echo json_encode($data);
            }else if($this->request->getVar('s_id')){

                $data = $this->staffModel->select('*')->where('s_id',$this->request->getVar('s_id'))->get()->getRowArray();

                echo json_encode($data);
            }
        }
        
    }

    public function fetch_sub_menu(){

        if($this->request->getMethod()=='post'){
            $data = array();
            if($this->request->getVar('menu_id')){

                $data = $this->menuListModel->select('menu_list_id,menu_id,menu_id,child_name,child_url,status')->where('menu_id',$this->request->getVar('menu_id'))->get()->getResultArray();

                echo json_encode($data);
            }

        }
        
    }

    public function fetch_menu_list(){

        $rowperpage = 10;
        $start = 0;
        $draw = 9;
        $totalRecords = $columnName = $columnSortOrder = 0;
        $response = $records = $data = array();

        if(@$this->request->getVar('menu_id')){

			if(@$this->request->getVar('draw'))
			$draw = $this->request->getVar('draw');

			if(@$this->request->getVar('start'))
			$start = $this->request->getVar('start');

			if(@$this->request->getVar('length'))
			$rowperpage = $this->request->getVar('length'); // Rows display per page

			if(@$this->request->getVar('order')[0]['column'])
			$columnIndex = $this->request->getVar('order')[0]['column']; // Column index

			if(@$this->request->getVar('columns')[$columnIndex]['data'])
			$columnName = $this->request->getVar('columns')[$columnIndex]['data']; // Column name

			if(@$this->request->getVar('order')[0]['dir'])
			$columnSortOrder = $this->request->getVar('order')[0]['dir']; // asc or desc

			if(@$this->request->getVar('search')['value'])
			$searchValue = $this->request->getVar('search')['value']; // Search value           

            $records = $this->menuListModel->select('menu_list_id,menu_id,child_name,child_url,status')->where('menu_id',$this->request->getVar('menu_id'))->orderBy($columnName, $columnSortOrder)->limit($rowperpage, $start)->get()->getResult();

            $totalRecords = $this->db->table($this->menuModel->table.' as m')->where('menu_id',$this->request->getVar('menu_id'))->countAllResults();

            $i=1;
            foreach ($records as $k=>$r) {
                $data[] = array( 
                "sno"=>$i++,
                "menu_list_id"=>$r->menu_list_id,
                "menu_id"=>$r->menu_id,
                "child_name"=>$r->child_name,
                "child_url"=>$r->child_url,
                "status"=>$r->status
                );
            }

        }
                    
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecords,
            "data" => $data
            );

        echo json_encode($response);
    } 

    public function add_menu_list(){

        if($this->request->getMethod()=='post'){
            try{
                $data = array();
                if($this->request->getVar('menu_list_id')===''){

                    $data = array(
                        'menu_id'=>$this->request->getVar('menu_id'),
                        'child_name'=>$this->request->getVar('menu_name'),
                        'child_url'=>$this->request->getVar('menu_url'),
                        'created_by'=>session()->get('user_details')['user_id']
                    );
                    $res=$this->menuListModel->save($data);
                    if($res)
                    echo json_encode(array('isSuccess'=>1));
                    else
                    echo json_encode(array('isSuccess'=>0));
                }else{
                    $data = array(
                        'menu_id'=>$this->request->getVar('menu_id'),
                        'child_name'=>$this->request->getVar('menu_name'),
                        'child_url'=>$this->request->getVar('menu_url'),
                        'modified_by'=>session()->get('user_details')['user_id']
                    );
                    $res=$this->menuListModel->where('menu_list_id',$this->request->getVar('menu_list_id'))->set($data)->update();
                    if($res)
                    echo json_encode(array('isSuccess'=>2));
                    else
                    echo json_encode(array('isSuccess'=>0));
                }
            }catch(\Exception $e){
                print_r($e->getMessage()); //die();
                echo json_encode(array('isError'=>$e->getMessage()));
            }

        }
        
    }

    public function disable_menu(){

        if($this->request->getMethod()=='post'){

            if($this->request->getVar('role_id')){
                $get_status = $this->rolesModel->select('status')->where('role_id',$this->request->getVar('role_id'))->get()->getRowArray();
                if($get_status['status']!=1){
                    $res = $this->rolesModel->where('role_id',$this->request->getVar('role_id'))->set(array('status'=>1))->update();
                    echo 1;
                }else{
                    $res =  $this->rolesModel->where('role_id',$this->request->getVar('role_id'))->set(array('status'=>0))->update();
                    echo 0;
                }
            }else if($this->request->getVar('menu_id')){
                $get_status = $this->menuModel->select('status')->where('menu_id',$this->request->getVar('menu_id'))->get()->getRowArray();
                if($get_status['status']!=1){
                    $res = $this->menuModel->where('menu_id',$this->request->getVar('menu_id'))->set(array('status'=>1))->update();
                    echo 1;
                }else{
                    $res =  $this->menuModel->where('menu_id',$this->request->getVar('menu_id'))->set(array('status'=>0))->update();
                    echo 0;
                }
            }else if($this->request->getVar('menu_list_id')){
                $get_status = $this->menuListModel->select('status')->where('menu_list_id',$this->request->getVar('menu_list_id'))->get()->getRowArray();
                if($get_status['status']!=1){
                    $res = $this->menuListModel->where('menu_list_id',$this->request->getVar('menu_list_id'))->set(array('status'=>1))->update();
                    echo 1;
                }else{
                    $res =  $this->menuListModel->where('menu_list_id',$this->request->getVar('menu_list_id'))->set(array('status'=>0))->update();
                    echo 0;
                }
            }else if($this->request->getVar('action_id')){
                $get_status = $this->actionModel->select('status')->where('action_id',$this->request->getVar('action_id'))->get()->getRowArray();
                if($get_status['status']!=1){
                    $res = $this->actionModel->where('action_id',$this->request->getVar('action_id'))->set(array('status'=>1))->update();
                    echo 1;
                }else{
                    $res =  $this->actionModel->where('action_id',$this->request->getVar('action_id'))->set(array('status'=>0))->update();
                    echo 0;
                }
            }else if($this->request->getVar('college_id')){
                $get_status = $this->collegeModel->select('status')->where('college_id',$this->request->getVar('college_id'))->get()->getRowArray();
                if($get_status['status']!=1){
                    $res = $this->collegeModel->where('college_id',$this->request->getVar('college_id'))->set(array('status'=>1))->update();
                    echo 1;
                }else{
                    $res =  $this->collegeModel->where('college_id',$this->request->getVar('college_id'))->set(array('status'=>0))->update();
                    echo 0;
                }
            }else if($this->request->getVar('department_id')){
                $get_status = $this->departmentsModel->select('status')->where('department_id',$this->request->getVar('department_id'))->get()->getRowArray();
                if($get_status['status']!=1){
                    $res = $this->departmentsModel->where('department_id',$this->request->getVar('department_id'))->set(array('status'=>1))->update();
                    echo 1;
                }else{
                    $res =  $this->departmentsModel->where('department_id',$this->request->getVar('department_id'))->set(array('status'=>0))->update();
                    echo 0;
                }
            }else if($this->request->getVar('s_id')){
                $get_status = $this->staffModel->select('status')->where('s_id',$this->request->getVar('s_id'))->get()->getRowArray();
                if($get_status['status']!=1){
                    $res = $this->staffModel->where('s_id',$this->request->getVar('s_id'))->set(array('status'=>1))->update();
                    echo 1;
                }else{
                    $res =  $this->staffModel->where('s_id',$this->request->getVar('s_id'))->set(array('status'=>0))->update();
                    echo 0;
                }
            }

        }
        
    } 

    public function fetch_role_list(){

        try{
            $data = array();
            
            $data = $this->db->table($this->rolesModel->table.' as r')->select('role_id,role_name')->where('status',1)->orderBy('role_name','asc')->get()->getResultArray();
            
            $dataList = '<option value="">-- Select Role --</option>';
            if(count($data)>0){
                foreach ($data as $key => $value) {
                    $dataList.='<option value="'.$value['role_id'].'">'.ucfirst($value['role_name']).'</option>';
                }
            }else{
                $dataList = '<option value="">No Roles';
            }

            echo  $dataList;

        }catch(\Exception $e){
            print_r($e->getMessage()); //die();
            echo json_encode(array('isError'=>$e->getMessage()));
        }

    }

    public function fetch_all_menu(){

        try{

            $json_decode = $main_menus = $list = $all_menu_list = array();
            
            $main_menus = $this->db->table($this->menuModel->table.' as m')                        
                        ->select('m.menu_id,m.menu_name,m.menu_icon,m.menu_url')
                        ->where('m.status',1)
                        ->orderBy('m.menu_position','asc')                        
                        ->get()->getResultArray(); 
                        //echo $this->db->getLastQuery(); die;            
            
            foreach ($main_menus as $key => $m) {
                
                $list['sub_menu'] = $this->db->table($this->menuListModel->table.' as l')                        
                        ->select('l.menu_list_id,l.child_name,l.child_url')
                        ->where('l.status',1) 
                        ->where('l.menu_id',$m['menu_id'])                       
                        ->get()->getResultArray();
                
                $all_menu_list[]=array_merge($m,$list);
            }

            //print_r(json_encode($all_menu_list));

            return $all_menu_list;

        }catch(\Exception $e){
            print_r($e->getMessage()); //die();
            echo json_encode(array('isError'=>$e->getMessage()));
        }

    }

    public function fetch_menu_preference(){

        try{

            if(@$this->request->getVar('role_id')){
                
                $preferences=array();

                $preferences = $this->db->table($this->menuPreferenceModel->table.' as m')                        
                        ->select('menu_preference')
                        ->where('m.status',1)
                        ->where('m.role_id',$this->request->getVar('role_id'))                        
                        ->get()->getRowArray();

                $role_menus['menu_preference'] = json_decode($preferences['menu_preference']);

                if(count($preferences)>0){
                    echo json_encode($role_menus);
                }else{
                    echo json_encode(array());
                }
        }

        }catch(\Exception $e){
            print_r($e->getMessage()); die();
            echo json_encode(array('isError'=>$e->getMessage()));
        }

    }

    public function is_password(){

        try{           

            if(@$this->request->getVar('old_password')){

            $isExist = $this->db->table(USERS)                        
                ->where('user_id',session()->get('user_details')['user_id'])   
                ->select('password')->get()->getRowArray();

            if(password_verify($this->request->getVar('old_password'),$isExist['password']))
            echo json_encode(array('isSuccess'=>1));
            else
            echo json_encode(array('isSuccess'=>0));
        }

        }catch(\Exception $e){
            print_r($e->getMessage()); exit();
            echo json_encode(array('isError'=>$e->getMessage()));
        }
    }

    public function change_password(){

        $data = array();
        $data['title'] = 'Change Password';

        if(@$this->request->getPost()){
            
            $password=password_hash($this->request->getVar('new_password'), PASSWORD_DEFAULT);

            $this->db->table(USERS)                        
                ->where('user_id',session()->get('user_details')['user_id'])                        
                ->set(array('password'=>$password,'password_vaildation'=>1))->update();

            echo json_encode(array('isSuccess'=>1)); exit;                    
        }
        echo view('common/header',$data);
        echo view('change_password',$data);
        echo view('common/footer');
    } 

    public function row_reorder(){
        //print_r($this->request->getPost()); die;
        if(@$this->request->getPost('row')){

            foreach ($this->request->getPost('row') as $key => $value) { 
                echo $key+1,':'.$value.' '; 
                $res = $this->db->table(MENUS_DETAILS)                        
                ->where('menu_id',$key+1)                        
                ->set(array('menu_position'=>$value))->update();
            }
            //print_r($this->request->getPost()); die;
            if($res)
            echo json_encode(array('isSuccess'=>1)); exit;                    
        }
    } 

    public function fetch_college_details(){

        $rowperpage = 10;
        $start = 0;
        $draw = 9;
        $totalRecords = $columnName = $columnSortOrder = 0;
        $response = $records = $data = array();

        if($this->request->getVar('type')==='college'){

			if(@$this->request->getVar('draw'))
			$draw = $this->request->getVar('draw');

			if(@$this->request->getVar('start'))
			$start = $this->request->getVar('start');

			if(@$this->request->getVar('length'))
			$rowperpage = $this->request->getVar('length'); // Rows display per page

			if(@$this->request->getVar('order')[0]['column'])
			$columnIndex = $this->request->getVar('order')[0]['column']; // Column index

			if(@$this->request->getVar('columns')[$columnIndex]['data'])
			$columnName = $this->request->getVar('columns')[$columnIndex]['data']; // Column name

			if(@$this->request->getVar('order')[0]['dir'])
			$columnSortOrder = $this->request->getVar('order')[0]['dir']; // asc or desc

			if(@$this->request->getVar('search')['value'])
			$searchValue = $this->request->getVar('search')['value']; // Search value 

            $db = \Config\Database::connect();
            $builder = $db->table(COLLEGE);

            if(@session()->get('user_details')['college_id'] && session()->get('user_details')['college_id']!=0){
                $builder->where('college_id',session()->get('user_details')['college_id']);
            }
            
            $records = $builder->select('college_id,college_name,college_short_name,college_addr,status')->orderBy($columnName, $columnSortOrder)->limit($rowperpage, $start)->get()->getResult();

            //$records = $this->collegeModel->select('college_id,college_name,college_short_name,college_addr,status')->orderBy($columnName, $columnSortOrder)->limit($rowperpage, $start)->get()->getResult();

            $totalRecords = !empty($records)?count($records):0; //$builder->countAllResults();

            $i=1;
            foreach ($records as $k=>$r) {
                $data[] = array( 
                    "sno"=>$i++,
                    "college_id"=>$r->college_id,
                    "college_name"=>$r->college_name,
                    "college_short_name"=>$r->college_short_name,
                    'college_addr'=>$r->college_addr,
                    "status"=>$r->status
                    );
            }
        }
                    
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecords,
            "data" => $data
            );

        echo json_encode($response);
    }

    public function fetch_college_list(){

        try{
            $data = array();

            $db = \Config\Database::connect();
            $builder = $db->table(COLLEGE);

            if(@session()->get('user_details')['college_id'] && session()->get('user_details')['college_id']!=0){
                $builder->where('college_id',session()->get('user_details')['college_id']);
            }
            
            $data = $builder->select('college_id,college_name')->where('status',1)->get()->getResultArray();
            
            $dataList = '<option value="">-- Select College --</option>';
            if(count($data)>0){
                foreach ($data as $key => $value) {
                    $dataList.='<option value="'.$value['college_id'].'">'.ucfirst($value['college_name']).'</option>';
                }
            }else{
                $dataList = '<option value="">No Colleges';
            }

            echo  $dataList;

        }catch(\Exception $e){
            print_r($e->getMessage()); //die();
            echo json_encode(array('isError'=>$e->getMessage()));
        }

    }

    public function fetch_department_list(){

        try{
            $data = array();
            
            $data = $this->db->table(DEPARTMENTS)->select('department_id,department_name')->where('college_id',$this->request->getVar('college_id'))->where('status',1)->groupBy('department_name')->get()->getResultArray();

            //echo $this->db->getLastQuery(); die;
            
            $dataList = '<option value="">-- Select Department --</option>';
            if(count($data)>0){
                foreach ($data as $key => $value) {
                    $dataList.='<option value="'.$value['department_id'].'">'.ucfirst($value['department_name']).'</option>';
                }
            }else{
                $dataList = '<option value="">No Departments';
            }

            echo  $dataList;

        }catch(\Exception $e){
            print_r($e->getMessage()); //die();
            echo json_encode(array('isError'=>$e->getMessage()));
        }

    }

    public function fetch_department_details(){

        $rowperpage = 10;
        $start = 0;
        $draw = 9;
        $totalRecords = $columnName = $columnSortOrder = 0;
        $response = $records = $data = array();

        if($this->request->getVar('type')==='department'){

			if(@$this->request->getVar('draw'))
			$draw = $this->request->getVar('draw');

			if(@$this->request->getVar('start'))
			$start = $this->request->getVar('start');

			if(@$this->request->getVar('length'))
			$rowperpage = $this->request->getVar('length'); // Rows display per page

			if(@$this->request->getVar('order')[0]['column'])
			$columnIndex = $this->request->getVar('order')[0]['column']; // Column index

			if(@$this->request->getVar('columns')[$columnIndex]['data'])
			$columnName = $this->request->getVar('columns')[$columnIndex]['data']; // Column name

			if(@$this->request->getVar('order')[0]['dir'])
			$columnSortOrder = $this->request->getVar('order')[0]['dir']; // asc or desc

			if(@$this->request->getVar('search')['value'])
			$searchValue = $this->request->getVar('search')['value']; // Search value 

            $db = \Config\Database::connect();
            $builder = $db->table(COLLEGE.' as c');
            $builder->join(DEPARTMENTS.' as d','d.college_id=c.college_id','left');

            if(@session()->get('user_details')['college_id'] && session()->get('user_details')['college_id']!=0){
                $builder->where('c.college_id',session()->get('user_details')['college_id']);
            }

            $records = $builder->select('d.department_id,c.college_name,d.department_name,d.status')->orderBy($columnName, $columnSortOrder)->limit($rowperpage, $start)->get()->getResult();

            //$records = $this->db->table(COLLEGE.' as c')->join(DEPARTMENTS.' as d','d.college_id=c.college_id','left')->select('d.department_id,c.college_name,d.department_name,d.status')->orderBy($columnName, $columnSortOrder)->limit($rowperpage, $start)->get()->getResult();

            $totalRecords = !empty($records)?count($records):0; //$builder->countAllResults();

            $i=1;
            foreach ($records as $k=>$r) {
                $data[] = array( 
                    "sno"=>$i++,
                    "department_id"=>$r->department_id,
                    "college_name"=>$r->college_name,
                    "department_name"=>$r->department_name,
                    "status"=>$r->status
                    );
            }
        }
                    
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecords,
            "data" => $data
            );

        echo json_encode($response);
    }

    public function college_management(){

        $data = array();
        $data['title'] = 'College Management';

        // echo '<pre>';
        // print_r(session()->get('user_details'));
        // die;

        if(@$this->request->getPost()){
            $holidays = array();            
            //print_r($this->request->getPost());
            $dates = $this->request->getPost('event_date');
            $events = $this->request->getPost('event_name');
            foreach ($events as $key => $e) {
                if(!empty($e)){
                    //echo $dates[$key].'<br>';
                    $holidays[] = array('college_id'=>session()->get('user_details')['college_id'],'holiday_date'=>$dates[$key],'holiday_name'=>$e,'holiday_category'=>'GH','month'=>date('Y-m',strtotime($dates[$key])),'attendance_role'=>$this->request->getVar('attendance_role'));
                }
            }

            //echo '<pre>'; print_r($this->request->getPost()); die;

            if(count($holidays)>0){   
                foreach ($holidays as $key => $holiday) {
                    $isEvent = $this->db->table(HOLIDAYS)->where('college_id',$holiday['college_id'])->where('holiday_date',$holiday['holiday_date'])->where('attendance_role',$holiday['attendance_role'])->countAllResults();

                    if($isEvent>0){
                        $this->holidaysModel->where('college_id',$holiday['college_id'])->where('holiday_date',$holiday['holiday_date'])->where('attendance_role',$holiday['attendance_role'])->set($holiday)->update();
                    }else{
                        $this->holidaysModel->save($holiday);
                    }
                }           
                //$this->holidaysModel->insertBatch($holidays); 
                //echo $this->db->getLastQuery(); die;
                session()->setFlashdata('success','Successfully college holidays updated..');
            }else{
                session()->setFlashdata('success','Holiday name missing..');
            }

            return redirect()->to(base_url('college_management')); 
        }        
        
        echo view('common/header',$data);
        echo view('college_management',$data);
        echo view('common/footer');
    }

    public function fetch_staff_details(){

        $rowperpage = 25;
        $start = 0;
        $draw = 9;
        $totalRecords = $columnName = $columnSortOrder = 0;
        $response = $records = $data = array();        

        if($this->request->getVar('type')==='staff'){
            // echo "<pre>";print_r($this->request->getVar('order')[0]['column']);die;

            if(@$this->request->getVar('draw'))
            $draw = $this->request->getVar('draw');

            if(@$this->request->getVar('start'))
            $start = $this->request->getVar('start');

            if(@$this->request->getVar('length'))
            $rowperpage = $this->request->getVar('length'); // Rows display per page

            if(@$this->request->getVar('order')[0]['column'])
            $columnIndex = $this->request->getVar('order')[0]['column']; // Column index

            if(@$this->request->getVar('columns')[$columnIndex]['data'])
            $columnName = $this->request->getVar('columns')[$columnIndex]['data']; // Column name

            if(@$this->request->getVar('order')[0]['dir'])
            $columnSortOrder = $this->request->getVar('order')[0]['dir']; // asc or desc

            $db = \Config\Database::connect();
            $builder = $db->table(STAFF.' as s');

            if(@session()->get('user_details')['college_id'] && session()->get('user_details')['college_id']!=0){
                $builder->where('c.college_id',session()->get('user_details')['college_id']);
            }

            if(@$this->request->getVar('search')['value']){
                $searchValue = $this->request->getVar('search')['value']; // Search value 
                $builder->like('c.college_short_name',$searchValue);
                $builder->orLike('d.department_name',$searchValue);
                $builder->orLike('r.role_name',$searchValue);
                $builder->orLike('s.staff_id',$searchValue);
                $builder->orLike('s.fname',$searchValue);
                $builder->orLike('s.mobile',$searchValue);
            }
            
            $records = $builder->join(COLLEGE.' as c','c.college_id=s.college_id','left')->join(DEPARTMENTS.' as d','d.department_id=c.college_id','left')->join(ROLES.' as r','r.role_id=s.role_id','left')->select('s.s_id,s.profile_image,s.college_id,s.staff_id,s.fname,c.college_short_name,s.department_id,d.department_name,r.role_name,s.role_id,s.gender,s.mobile,s.email,s.status')->groupBy('s.staff_id')->orderBy($columnName, $columnSortOrder)->limit($rowperpage, $start)->get()->getResult();

            $totalRecords = !empty($records)?count($builder->countAllResults()):0; //$builder->countAllResults();

            //$records = $this->db->getLastQuery();// die;

            $i=1;
            foreach ($records as $k=>$r) {
                $data[] = array( 
                    "sno"=>$i++,
                    "s_id"=>$r->s_id,
                    "profile_image"=>$r->profile_image,
                    "college_id"=>$r->college_id,
                    "college_name"=>$r->college_short_name,                  
                    "staff_id"=>$r->staff_id,
                    "fname"=>$r->fname,
                    "department_name"=>$r->department_name,
                    "department_id"=>$r->department_id,
                    "role_name"=>$r->role_name,
                    "gender"=>$r->gender,
                    "mobile"=>$r->mobile,
                    "email"=>$r->email,
                    "status"=>$r->status
                    );
            }
            // echo "<pre>";print_r($data);die;
        }
                    
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecords,
            "data" => $data
        );

        // echo "<pre>";print_r($response);die;
        echo json_encode($response);
    }

    public function get_reportingPerson_1(){	
        
        $users = [];
		if ($this->request->getPost()) {
            $reporting_person = $this->db->table(STAFF.' as s')->join(ROLES.' as r','s.role_id = r.role_id','LEFT')->join(DEPARTMENTS.' as d','s.department_id = d.department_id','LEFT')->select('s.staff_id,s.fname,s.lname,r.role_name,d.department_name')->where('s.college_id',$this->request->getVar('college_id'))->like('s.staff_id',$this->request->getVar('staff_id'))->orLike('s.fname',$this->request->getVar('staff_id'))->get()->getResultArray();
		}
        //print_r($reporting_person); die;
		//echo $this->vehicleModel->getLastQuery();
		$html = '';
		if(!empty($reporting_person)) {
			$html = "<ul class='list-group'>";			
			foreach($reporting_person as $key=>$r){
				$staff_id=$r["staff_id"];
				$html.="<li class='list-group-item' onClick='select_user_1($staff_id)'>".$r["staff_id"].' - '.$r["fname"].' '.$r["lname"].' ('.$r["department_name"].' - '.$r["role_name"].')'."</li>";
			}
			$html.="</ul>";
		}else if(empty($reporting_person)){
			$html="<ul class='list-group' onClick='select_user_1()'>
			<li class='list-group-item'><span class='text-danger'>No Reporting Person</span></li>
			</ul>";
		}

		echo $html;
	}

    public function get_reportingPerson_2(){	
        
        $users = [];
		if ($this->request->getPost()) {
            $reporting_person = $this->db->table(STAFF.' as s')->join(ROLES.' as r','s.role_id = r.role_id','LEFT')->join(DEPARTMENTS.' as d','s.department_id = d.department_id','LEFT')->select('s.staff_id,s.fname,s.lname,r.role_name,d.department_name')->where('s.college_id',$this->request->getVar('college_id'))->like('s.staff_id',$this->request->getVar('staff_id'))->orLike('s.fname',$this->request->getVar('staff_id'))->get()->getResultArray();
		}
        //print_r($reporting_person); die;
		//echo $this->vehicleModel->getLastQuery();
		$html = '';
		if(!empty($reporting_person)) {
			$html = "<ul class='list-group'>";			
			foreach($reporting_person as $key=>$r){
				$staff_id=$r["staff_id"];
				$html.="<li class='list-group-item' onClick='select_user_2($staff_id)'>".$r["staff_id"].' - '.$r["fname"].' '.$r["lname"].' ('.$r["department_name"].' - '.$r["role_name"].')'."</li>";
			}
			$html.="</ul>";
		}else if(empty($reporting_person)){
			$html="<ul class='list-group' onClick='select_user_2()'>
			<li class='list-group-item'><span class='text-danger'>No Reporting Person</span></li>
			</ul>";
		}

		echo $html;
	}

    public function staff_details(){

        if(@$this->request->getPost()){
            //echo '<pre>'; print_r($this->request->getPost()); die;

            if($this->request->getVar('s_id')==''){
                $image_path='';
                if(@$this->request->getFile('profile_image')){
                    $new_name = $this->request->getVar('mobile').'.jpg';
                    
                    $url = 'assets/uploads/profile_images/';
                    $img = $this->request->getFile('profile_image');
                    
                    $img->move(FCPATH.$url,$new_name);

                    $image_path = $url.$this->request->getVar('mobile').'.jpg';
                }

                $staff = array(
                    'staff_id' => $this->request->getVar('staff_id'),
                    'college_id' => $this->request->getVar('college_id'),
                    'role_id' => $this->request->getVar('role_id'),
                    'department_id' => $this->request->getVar('department_id'),
                    'teaching_type' => $this->request->getVar('teaching_type'),
                    'staff_attendance_type' => $this->request->getVar('attendance_type'),
                    'leave_expiry_days' => $this->request->getVar('leave_expiry_days'),
                    'attendance_role' => $this->request->getVar('attendance_role'),
                    'approve_type' => $this->request->getVar('approve_type'),
                    'reporting_person_1' => $this->request->getVar('reporting_person_1'),
                    'reporting_person_2' => $this->request->getVar('reporting_person_2'),
                    'status' => $this->request->getVar('status'),
                    'fname' => $this->request->getVar('fname'),
                    'lname' => $this->request->getVar('lname'),
                    'dob' => $this->request->getVar('dob'),
                    'gender' => $this->request->getVar('gender'),
                    'mobile' => $this->request->getVar('mobile'),
                    'email' => $this->request->getVar('email'),
                    'address' => $this->request->getVar('address'),
                    'profile_image'=>!empty($image_path)?$image_path:''
                );

                    $user = array(
                        'staff_id'=>$this->request->getVar('staff_id'),
                        'college_id'=>$this->request->getVar('college_id'),
                        'username'=>$this->request->getVar('fname'),
                        'password'=>password_hash($this->request->getVar('mobile'), PASSWORD_DEFAULT),
                        'user_type'=>$this->request->getVar('attendance_role')
                    );

                $res = $this->staffModel->save($staff);
                $res = $this->userModel->save($user);

                if($res){
                    $res = 1;
                }else{
                    $res = 1;
                }            

                if(@$this->request->getVar('email')){

                $body = '<strong>Mr. '.$this->request->getVar('fname').',</strong><br><br>
                    <style>
                    table, td, th {
                    border: none;
                    text-align: left;
                    padding:5px;
                    }            
                    table {
                    border-collapse: collapse;
                    }
                    </style>         
                    <table> 
                    <tr><th colspan="2" style="background:#cbcbcb;color:#000000;text-align:center;padding:5px;"> Login Authentication Details </th></tr>
                    <tr>
                        <th>Username</th>
                        <td>: '.$this->request->getVar('fname').'</td>
                    </tr> 
                    <tr>
                        <th>Password</th>
                        <td>: '.$this->request->getVar('mobile').'</td>
                    </tr>
                    <tr>
                        <th>Login Link</th>
                        <td>: <a href="http://eattendance.mahendra.org/attendance">SignIn</a></td>
                    </tr>         
                    </table>';

                    $email = \Config\Services::email();
                        
                    $email->setTo('suresh.mnw1877@gmail.com');
                    //$email->setTo($this->request->getVar('email'));
                    $email->setFrom('mahendraeducation@gmail.com');
                    
                    $email->setSubject('Welecome To Mahendra Institution', 'Confirm Registration');
                    $email->setMessage($body);

                    if ($email->send()){
                        echo $res;
                    } else {
                        $res = $email->printDebugger(['headers']);
                        print_r($res);
                    }
                }
            }else{

                $staff = array(
                    'staff_id' => $this->request->getVar('staff_id'),
                    'college_id' => $this->request->getVar('college_id'),
                    'role_id' => $this->request->getVar('role_id'),                    
                    'department_id' => $this->request->getVar('department_id'),
                    'teaching_type' => $this->request->getVar('teaching_type'),
                    'staff_attendance_type' => $this->request->getVar('attendance_type'),
                    'leave_expiry_days' => $this->request->getVar('leave_expiry_days'),
                    'attendance_role' => $this->request->getVar('attendance_role'),
                    'approve_type' => $this->request->getVar('approve_type'),
                    'reporting_person_1' => $this->request->getVar('reporting_person_1'),
                    'reporting_person_2' => $this->request->getVar('reporting_person_2'),
                    'status' => $this->request->getVar('status'),
                    'fname' => $this->request->getVar('fname'),
                    'lname' => $this->request->getVar('lname'),
                    'dob' => $this->request->getVar('dob'),
                    'gender' => $this->request->getVar('gender'),
                    'mobile' => $this->request->getVar('mobile'),
                    'email' => $this->request->getVar('email'),
                    'address' => $this->request->getVar('address')
                );

                $res = $this->staffModel->set($staff)->update();
                
                if($res)
                echo 2;
                else
                echo 0;
            }
        }

    }

    public function fetch_holidays(){
        $holidays = array();
        if($this->request->getPost()){
            $holidays = $this->db->table(HOLIDAYS)->select('DATE_FORMAT(holiday_date,"%d") as holiday_date,holiday_name,attendance_role')->where('college_id',session()->get('user_details')['college_id'])->where('attendance_role',$this->request->getVar('attendance_role'))->where('month',date('Y-m',strtotime($this->request->getVar('year_month'))))->get()->getResultArray();
            //echo $this->db->getLastQuery(); die;
        }
        echo json_encode($holidays);
    }

    public function insert_holidays(){

        if(@$this->request->getPost()){
            $holidays = array();            
            //print_r($this->request->getPost('event_date')); die;
            $dates = $this->request->getPost('event_date');
            $events = $this->request->getPost('event_name');
            foreach ($events as $key => $e) {
                if(!empty($e)){
                    //echo $dates[$key].'<br>';
                    $holidays[] = array('college_id'=>session()->get('user_details')['college_id'],'holiday_date'=>$dates[$key],'holiday_name'=>$e,'holiday_category'=>'GH','month'=>date('Y-m',strtotime($dates[$key])),'attendance_role'=>$this->request->getVar('attendance_role'));
                }
            }

            if(count($holidays)>0){   
                foreach ($holidays as $key => $holiday) {
                    $isEvent = $this->db->table(HOLIDAYS)->where('college_id',$holiday['college_id'])->where('holiday_date',$holiday['holiday_date'])->where('attendance_role',$holiday['attendance_role'])->countAllResults();

                    if($isEvent>0){
                        $this->holidaysModel->where('college_id',$holiday['college_id'])->where('holiday_date',$holiday['holiday_date'])->where('attendance_role',$holiday['attendance_role'])->set($holiday)->update();
                    }else{
                        $this->holidaysModel->save($holiday);
                    }
                }           
                //$this->holidaysModel->insertBatch($holidays); 
                //echo $this->db->getLastQuery(); die;
                echo json_encode(array('success'=>1));
            }else{
                echo json_encode(array('success'=>0));
            }
 
        } 
    }

    public function show_holidays(){
        $holidays = array();
        if($this->request->getPost()){
            $holidays = $this->db->table(HOLIDAYS)->select('DATE_FORMAT(holiday_date,"%d") as holiday_date,holiday_name,attendance_role')->where('college_id',session()->get('user_details')['college_id'])->where('attendance_role',session()->get('user_details')['attendance_role'])->where('month',date('Y-m',strtotime($this->request->getVar('year_month'))))->get()->getResultArray();
            //echo $this->db->getLastQuery(); die;
        }
        echo json_encode($holidays);
    }

    public function attendance_log(){

        ini_set('max_execution_time',0);

        $data = array();

        $data['title'] = 'Attendance Log Management';

        if(isset($this->request->getPost()['attendance_log'])){

			$data['from_date'] = $this->request->getPost()['from_date'];
			$data['to_date'] = $this->request->getPost()['to_date'];
			
            if(isset($this->request->getPost()['attendance_log'])){
				$this->fetch_attendance_log($this->request->getPost());
			}            			
		}

		/*
        if(@$this->request->getPost()['college_name']){

			$data['from_date'] = $this->request->getPost()['from_date'];

			$data['to_date'] = $this->request->getPost()['to_date'];
			
			if($data['from_date'] == '' && $data['to_date'] == ''){
				echo "<script> alert('Please Choose Date..!') </script>"; 
			}else if($data['from_date'] >= $data['to_date']){
				echo "<script> alert('Please Choose Valid Date..!') </script>"; 
			}else{
				$this->fetch_attendance_log($this->request->getPost());
			}
		}
        */

        echo view('common/header',$data);
        echo view('attendance_log',$data);
        echo view('common/footer');

    }

    public function fetch_attendance_log($data){

            //echo '<pre>'; print_r($POST); die;

            $get_college_staff = $this->staffModel->where('status',1)->where('college_id',$data['college_name'])->select('s_id,staff_id,staff_attendance_type,approve_type,attendance_role')->get()->getResultArray();	

            //echo '<pre>'; print_r($get_college_staff); //die;
    
            $get_college_details =  $this->collegeModel->where('status',1)->where('college_id',$data['college_name'])->get()->getRowArray();

            //echo '<pre>'; print_r($get_college_details); die;
    
            $clg_lat = $get_college_details['college_latitude'];
    
            $clg_lon = $get_college_details['college_longitude'];
    
            $radius = $get_college_details['college_radius'];
     
            $fromdate = $data['from_date'];
    
            $todate = $data['to_date'];
    
            $from_month = date('Y-m', strtotime($fromdate));
    
            $to_month = date('Y-m', strtotime($todate));
    
            $all_rec = array();		 
    
            foreach($get_college_staff as $staffs){
    
                if($from_month == $to_month){
    
                    $month_1 = $this->staffAttendanceModel->where('staff_id',$staffs['staff_id'])->where('month',$from_month)->countAllResults(); 
    
                    $insert_fst = array('staff_id'=>$staffs['staff_id'],'month'=>$from_month,'college_id'=>$data['college_name']);
    
                    if($month_1 == 0){
                        $this->staffAttendanceModel->save($insert_fst);
                    }

                }else{
    
                    $month_1 = $this->staffAttendanceModel->where('staff_id',$staffs['staff_id'])->where('month',$from_month)->countAllResults();
    
                    $month_2 = $this->staffAttendanceModel->where('staff_id',$staffs['staff_id'])->where('month',$to_month)->countAllResults();
    
                    $insert_fst = array('staff_id'=>$staffs['staff_id'],'month'=>$from_month,'college_id'=>$data['college_name']);                
    
                    $insert_lst = array('staff_id'=>$staffs['staff_id'],'month'=>$to_month,'college_id'=>$data['college_name']);
    
                    if($month_1 == 0){
                        $this->staffAttendanceModel->save($insert_fst);
                    }
                    
                    if($month_2 == 0){ 
                        $this->staffAttendanceModel->save($insert_lst);
                    }			 
                }
    
                $num_dates = 0;
    
                $late_entry = 0;
                $permission = 0;

                $late_entry_out = 0;
                $permission_out = 0;
    
                $get_permission_cnt = 0;
    
                for ($j=strtotime($fromdate."00:00:00"); $j<=strtotime($todate."23:59:59"); $j+=86400) { 
                    //echo $num_dates."<br>";
                    $num_dates += 1;
                    
                    $late_entry_details = $this->attendancePermissionModel->where('status',1)->where('staff_id',$staffs['staff_id'])->where('attd_date',date("Y-m-d", $j))->where('late_entry',1)->limit(1)->orderBy('attd_date','ASC')->get()->getResultArray();   
                                           
                    $permission_details = $this->attendancePermissionModel->where('status',1)->where('staff_id',$staffs['staff_id'])->where('attd_date',date("Y-m-d", $j))->where('permission',1)->limit(1)->orderBy('attd_date','ASC')->get()->getResultArray();
                        
                    //print_r($staffs);
                    foreach($late_entry_details as $le){
                        
                        if($staffs['staff_attendance_type'] == 0){
                            $late_entry += 1;
    
                            $late_entry_out += 1;
                        }else{

                            if($le['permission_lat'] != '' && $le['permission_lon'] != ''){
                                $radius_data = $this->getDistance($clg_lat,$clg_lon,$le['permission_lat'],$le['permission_lon']);
                            
                                if($radius_data < $radius){
                                    $late_entry += 1;
                                }
                                else{
                                    $late_entry_out += 1;
                                }
                            }
                            else{
                                $late_entry += 0;
    
                                $late_entry_out += 0;
                            }
                        }
                         
                     }
    
                     foreach($permission_details as $pr){
                        
                        if($staffs['staff_attendance_type'] == 0){
                            $permission += 1;
    
                            $permission_out += 1;
                        }
                        else{
    
                            if($pr['permission_lat'] != '' && $pr['permission_lon'] != ''){
                                $radius_data = $this->getDistance($clg_lat,$clg_lon,$pr['permission_lat'],$pr['permission_lon']);
                                if($radius_data < $radius){
                                    $permission += 1;
                                }
                                else{
                                    $permission_out += 1;
                                }
                            }
                            else{
                                $permission += 0;
    
                                $permission_out += 0;
                            }
                        }	
                     }
                }
    
                $get_rec_permission = $this->attendancePermissionModel->where('status',1)->where('staff_id',$staffs['staff_id'])->orderBy('attd_date','ASC')->limit($num_dates,4)->get()->getResultArray();

                //echo '<pre>'; print_r($get_rec_permission); die;

                $all_rec = array();

                $college_out_all_rec = array();
                $college_in_all_rec = array();

                foreach($get_rec_permission as $res){
                    $all_rec[$res['attd_date']] = $res;     
                }    
             
                $insert_array = array();
                //echo date("y-m-d h:m:s",strtotime($fromdate)).','.date("Y-m-d h:m:s",strtotime($todate)); die;
                //$todate = date('Y-m-d', strtotime($todate . ' +1 day'));
                for ($i=strtotime($fromdate."00:00:00"); $i<=strtotime($todate."23:59:59"); $i+=86400) {  
                    $all_dates =  date("Y-m-d", $i); 
                    //echo $i."-".$all_dates;	
                    
                    $db = \Config\Database::connect();
                    $builder = $db->table(STAFF_LEAVE);
    
                    $builder->where('status',1);
                    $builder->where('staff_id',$staffs['staff_id']);

                    $builder->groupStart();		 
                    $builder->orWhere("'$all_dates' BETWEEN from_date AND to_date ");				 
                    $builder->groupEnd();

                    $builder->groupStart();    
                    if($staffs['approve_type'] == 2){
                        $builder->where('approve_status',3);
                    }
                    else if($staffs['approve_type'] == 1){
                        $builder->where('approve_status',3);
                    } 
                    else if($staffs['approve_type'] == 0){
                        $builder->where('approve_status',3);
                    } 				
                    $builder->groupEnd();	

                    $num_rows_applied_leave = $builder->countAllResults();
                    $rows_applied_leave = $builder->get()->getRowArray();

                    $get_leave_gl = $this->holidaysModel->where('status',1)->where('college_id',$data['college_name'])->where('holiday_date',$all_dates)->limit(1)->countAllResults();

                    $select_leave_mng = $this->leaveModel->where('status',1)->where('college_id',$data['college_name'])->where('staff_id',$staffs['staff_id'])->get()->getRowArray(); 
        
                    $get_attd_log = $this->attendanceLogModel->where('status',1)
                    ->where('college_id',$data['college_name'])
                    ->where('staff_id',$staffs['staff_id'])	
                    ->where('curr_date',$all_dates)
                    ->orderBy('attd_id','ASC')
                    ->get()->getResultArray();

                    // echo '<Pre>'; print_r($get_attd_log); die;                    
                    //echo "<pre>";print_r($get_attd_log);
                    //echo $staffs['staff_id'].'----'.count($get_attd_log).'-----'.$get_leave_gl.'<br>';
                    //print_r($get_attd_log);
    
                    $attd_per_day = '';
    
                    $leave_type = '';
    
                    $get_permission = 0;                     
    
                    if(isset($all_rec[$all_dates]['permission']) || isset($all_rec[$all_dates]['late_entry'])){
    
                        //print_r($all_rec[$all_dates]);    
                        if($all_rec[$all_dates]['permission'] == 1 ||   $all_rec[$all_dates]['late_entry'] == 1 ){
                        $get_permission = 0;    
                        $get_permission_cnt += 1; 
                        }
                    } 
    
                    //echo $all_dates.'-----'.$get_permission_cnt.'----'.$staffs['staff_id'].'<br>';
    
                    $logout_lst_rec_key = count($get_attd_log) - 1;
     
                    if(isset($get_attd_log[$logout_lst_rec_key])){
                        
                        if($get_attd_log[$logout_lst_rec_key]['log_shot_code'] == 'PE' || $get_attd_log[$logout_lst_rec_key]['log_shot_code'] == 'L' || $get_attd_log[$logout_lst_rec_key]['log_shot_code'] == 'E'){
    
                            $log_lst_rec_time = date('H:i:s', strtotime($get_attd_log[0]['created_on']));
                            $lst_rec_time = date('H:i:s', strtotime($get_attd_log[$logout_lst_rec_key]['created_on']));
                            $lst_diff = $this->get_time_difference($log_lst_rec_time,$lst_rec_time);
    
                            //echo $all_dates.'-----'.$lst_diff.'----'.$staffs['staff_id'].'<br>';
                            $add_total_hrs = round($lst_diff);
                         }
                         else{
    
    
                            $log_lst_rec_time = date('H:i:s', strtotime($get_attd_log[0]['created_on']));
                            //$lst_rec_time = date('H:i:s', strtotime('16:30:00'));
                            $lst_rec_time = date('H:i:s', strtotime($get_attd_log[$logout_lst_rec_key]['created_on']));
                            $lst_diff = $this->get_time_difference($log_lst_rec_time,$lst_rec_time);
                                 
                            $add_total_hrs = round($lst_diff);
                         }
                    }
                     else{
                         $add_total_hrs = 0; 
                     }
                    
                    if(count($get_attd_log) == 1){
                     
                        $invalid_punch_time = date('A', strtotime($get_attd_log[0]['created_on']));
    
                        if($invalid_punch_time == 'AM'){
                            $leave_type = 'EIP';
                        }
                        else{
                            $leave_type = 'MIP';
                        }
                    }
                     else if(count($get_attd_log) >= 2 && $get_leave_gl == 0){
                         //print_r($get_attd_log);
    
                         foreach($get_attd_log as $key=>$log){
                          
                             if($log['log_shot_code'] == 'E'){
    
                                 $lst_rec_key = $key + 1;
    
                                 if(isset($get_attd_log[$lst_rec_key]['created_on'])){
                                    $lst_rec_time = date('H:i:s', strtotime($get_attd_log[$lst_rec_key]['created_on']));
                                    $current_rec_time = date('H:i:s', strtotime($log['created_on'])); 
                                    $lst_diff = $this->get_time_difference($current_rec_time,$lst_rec_time);
                                 }
                                 else{
                                    $lst_rec_time = date('H:i:s', strtotime('16:30:00'));
                                    $current_rec_time = date('H:i:s', strtotime($log['created_on'])); 
                                    $lst_diff = $this->get_time_difference($current_rec_time,$lst_rec_time);
                                 } 
                                   
                                 $add_total_hrs += round($lst_diff);
         
                             } 
    
                        $add_total_hrs += ($get_permission / count($get_attd_log));
                    
                    }
                         
                        //echo $add_total_hrs;
                          
                         if($add_total_hrs >=3 && $add_total_hrs <=5){
    
                             if($num_rows_applied_leave == 1){
                                 $leave_type = 'H'.$rows_applied_leave['leave_type'] ;
    
                                 if($rows_applied_leave['leave_type'] == 'CCL'){
                                     $add_leaves = $select_leave_mng['ccl_taken'] + 0.5;
     
                                 }
                                 else if($rows_applied_leave['leave_type'] == 'CL'){
                                     $add_leaves = $select_leave_mng['cl_taken'] + 0.5;
    
                                      
                                 } 
    
                             }
                             else{
                                 $leave_type = 'HD';
                             } 
                         }
                         else if($add_total_hrs >=6){
                             if($num_rows_applied_leave == 1){
                                 $leave_type =  $rows_applied_leave['leave_type'] ;
    
                                 if($rows_applied_leave['leave_type'] == 'CCL'){
                                     $add_leaves = $select_leave_mng['ccl_taken'] + 1;
     
                                 }
                                 else if($rows_applied_leave['leave_type'] == 'CL'){
                                     $add_leaves = $select_leave_mng['cl_taken'] + 1;
     
                                 }
                             }
                             else{
                                 $leave_type = 'P';
                             } 
                         }
                         else{
                            $leave_type = 'UL';
                         } 
                      
                     }else if(count($get_attd_log) >= 2 && $get_leave_gl == 1){
        
                         foreach($get_attd_log as $key=>$log){     
                          
                             if($log['log_shot_code'] == 'E'){
    
                                 $lst_rec_key = $key + 1;
    
                                 if(isset($get_attd_log[$lst_rec_key]['created_on'])){
                                     $lst_rec_time = date('H:i:s', strtotime($get_attd_log[$lst_rec_key]['created_on']));
                                     $current_rec_time = date('H:i:s', strtotime($log['created_on'])); 
                                     $lst_diff = $this->get_time_difference($current_rec_time,$lst_rec_time);
                                 }
                                 else{
                                     $lst_rec_time = date('H:i:s', strtotime('16:30:00'));
                                     $current_rec_time = date('H:i:s', strtotime($log['created_on'])); 
                                     $lst_diff = $this->get_time_difference($current_rec_time,$lst_rec_time);
                                 } 
    
                                  
                                   
                                 $add_total_hrs += round($lst_diff);
         
                             }
                               
    
                              $add_total_hrs += ($get_permission / count($get_attd_log));
                             
                         }
    
    
                         
    
                         if($add_total_hrs >=3 && $add_total_hrs <=6){
    
                             $leave_type = 'HCCL';
    
                             $add_ccl = $select_leave_mng['ccl_total'] + 0.5;
    
     
                              
                         }
                         else if($add_total_hrs >=6 && $num_rows_applied_leave == 1){
                             $leave_type = 'OFF';
                         }
                         else{
                            $leave_type = 'PCCL';
    
                            $add_ccl = $select_leave_mng['ccl_total'] + 1;
     
                         }
         
                     }
                     else if(count($get_attd_log) == 0 && $get_leave_gl == 1){
                         $leave_type = 'OFF';
                     }
                     else if(count($get_attd_log) == 0 && $get_leave_gl == 0){
                              
                         if($num_rows_applied_leave == 1){
    
                             $leave_type = $rows_applied_leave['leave_type'] ;
    
                             if($rows_applied_leave['leave_type'] == 'CCL'){
                                 $add_leaves = $select_leave_mng['ccl_taken'] + 1;
    
                             }
                             else if($rows_applied_leave['leave_type'] == 'CL'){
                                 $add_leaves = $select_leave_mng['cl_taken'] + 1;
    
                             
                             } 
    
                             
    
                         }
                         else{
                             $leave_type = 'UL';
                         } 
                     }    
     
                     if($leave_type == ''){
                         $leave_type = 'NA';
                     }
                     else{
                         $leave_type = $leave_type;
                     }
    
                     //echo $all_dates.'-----'.$get_permission_cnt.'----'.$staffs['staff_id'].'<br>';
                     // echo $get_permission_cnt.'<br>';
    
                     $up_array = array('A'.date('d', strtotime($all_dates))=>$leave_type,'num_late_entry'=>$late_entry_out,'num_permission'=>$permission_out,'permission_cnt'=>$get_permission_cnt);
                        
                     //print_r($up_array);
                     $this->staffAttendanceModel->where('staff_id',$staffs['staff_id'])->where('month',date('Y-m', strtotime($all_dates)))->set($up_array)->update();
                       
                    /*
                    if($leave_type == 'LOP' || $leave_type == 'CL' || $leave_type == 'CCL' || $leave_type == 'UL'){                         
                        $this->calculateLP($all_dates,$staffs['staff_id'],date('Y-m', strtotime($all_dates)));                        
                    }
                    */                     
                }  
                
            } 
     
            $this->download_csv_1($from_month,$to_month,$data['college_name'],$all_dates); 
    }

    public function working_hours_log(){

        ini_set('max_execution_time',0);

        $data = array();

        $data['title'] = 'Working Hours Log';

		if(@$this->request->getPost()['college_name']){

			$data['from_date'] = $this->request->getPost()['from_date'];

			$data['to_date'] = $this->request->getPost()['to_date'];
			
            if($data['from_date'] >= $data['to_date']){
				echo "0|Please Choose Valid Date.."; exit;
			}else{
				$this->fetch_working_log($this->request->getPost());
			}            			
		}

        echo view('common/header',$data);
        echo view('working_hours_log',$data);
        echo view('common/footer');

    }

    public function fetch_working_log($data){
		
        $db = \Config\Database::connect();
        $builder = $db->table(STAFF.' as s');

        $builder->where("s.college_id",$data['college_name']);
        //$builder->where('s.staff_id','11130')
        $builder->join(COLLEGE.' as c','c.college_id = s.college_id','INNER');
        $builder->join(DEPARTMENTS.' as d','d.department_id = s.department_id','LEFT');
        $builder->orderBy('s.staff_id','ASC');
        $builder->select('s.staff_id,s.fname,s.lname,c.college_name,d.department_name');
        $select_all_clg_staffs =  $builder->get()->getResultArray();

		$get_clg_details =  $this->collegeModel->where('status',1)->where('college_id',$data['college_name'])->get()->getRowArray();

		$clg_lat = $get_clg_details['college_latitude'];

	 	$clg_lon = $get_clg_details['college_longitude'];

	 	$radius = $get_clg_details['college_radius'];
 
		$fromdate = $data['from_date'];

		$todate = $data['to_date'];

		$from_month = date('Y-m', strtotime($fromdate));

		$to_month = date('Y-m', strtotime($todate));

		$all_rec = array();

		foreach($select_all_clg_staffs as $staffs){

			$total_hrs = $total_full = $total_half = 0;

			$date_wise = array();		

			for ($j=strtotime($fromdate); $j<=strtotime($todate); $j+=86400) { 
				
				$attd_Date = date("Y-m-d", $j);
				$att_day = date("d", strtotime($j));

                $builder = $db->table(ATTENDANCE);
				$builder->where('staff_id',$staffs['staff_id']);
				$builder->where('curr_date',$attd_Date);
				$builder->select('*'); 
				$attd_bio_log = $builder->get()->getResultArray();

				if(count($attd_bio_log) > 1){
					$intime = date('H:i:s', strtotime($attd_bio_log[0]['created_on']));
					$out_time = date('H:i:s', strtotime($attd_bio_log[count($attd_bio_log)-1]['created_on']));

					$time1 = $intime;
					$time2 = $out_time;
					list($hours, $minutes) = explode(':', $time1);
					$firstTimestamp = mktime($hours, $minutes);

					list($hours, $minutes) = explode(':', $time2);
					$secondTimestamp = mktime($hours, $minutes);

					$firstTimestamp > $secondTimestamp?$seconds = $firstTimestamp - $secondTimestamp:$seconds = $secondTimestamp - $firstTimestamp;

					$minutes = ($seconds / 60) % 60;
					$hours = floor($seconds / (60 * 60));

					$float_hsr = $hours.'.'.$minutes;

					$total_hrs += $float_hsr;

					//echo $float_hsr;

					if($float_hsr >= 6){
						$total_full += 1;
					}
					else if($float_hsr >= 3 && $float_hsr <= 6){
						$total_half += 1;
					}
					
			 		$lst_diff = "$hours:$minutes";

				}
				else if(count($attd_bio_log) == 1){
					$total_hrs += 0;

					$total_full += 0;
					$total_half += 0;
					
			 		$lst_diff = "In time Only";
				}
				else{
					$total_hrs += 0;

					$total_full += 0;
					$total_half += 0;
					
			 		$lst_diff = "No Records";
				}

				$date_wise[$attd_Date] = $lst_diff;			 	
			}

			$total_days = (($total_hrs*60)/450);

			$all_rec[] = $staffs + $date_wise + array('full_day'=>$total_full,'half_day'=>$total_half,'total_hrs'=>$total_hrs,'total_days'=>round($total_days)); 
 
		} 

		$this->download_csv_2($from_month,$to_month,$all_rec);
        
	}

    public function download_csv_1($from_month,$to_month,$college_id,$all_dates){
        
        $db = \Config\Database::connect();

        $fields="A01,',',A02,',',A03,',',A04,',',A05,',',A06,',',A07,',',A08,',',A09,',',A10,',',A11,',',A12,',',A13,',',A14,',',A15,',',A16,',',A17,',',A18,',',A19,',',A20,',',A21,',',A22,',',A23,',',A24,',',A25,',',A26,',',A27,',',A28,',',A29,',',A30,',',A31";

        if($from_month == $to_month){

            $builder = $db->table(STAFF_ATTENDANCE.' as a');
            $builder->join(STAFF.' as s','s.staff_id = a.staff_id','INNER');
            $builder->join(COLLEGE.' as c','c.college_id = a.college_id','INNER');
            $builder->join(DEPARTMENTS.' as d','d.department_id = s.department_id','LEFT');
            $builder->orderBy('a.staff_id','ASC');
            $builder->select('s.staff_id,s.fname,s.lname,c.college_name,d.department_name,a.A01,a.A02,a.A03,a.A04,a.A05,a.A06,a.A07,a.A08,a.A09,a.A10,a.A11,a.A12,a.A13,a.A14,a.A15,a.A16,a.A17,a.A18,a.A19,a.A20,a.A21,a.A22,a.A23,a.A24,a.A25,a.A26,a.A27,a.A28,a.A29,a.A30,a.A31,a.num_permission,num_late_entry,a.permission_cnt,a.month');
            $builder->where("a.college_id",$college_id);
            $builder->where('a.month',$from_month);
            $query = $builder->get()->getResultArray();

        }else{

            $builder = $db->table(STAFF_ATTENDANCE.' as a');
            $builder->join(STAFF.' as s','s.staff_id = a.staff_id','INNER');
            $builder->join(COLLEGE.' as c','c.college_id = a.college_id','INNER');
            $builder->join(DEPARTMENTS.' as d','d.department_id = s.department_id','LEFT');
            $builder->orderBy('a.staff_id','ASC');
            $builder->select('s.staff_id,s.fname,s.lname,c.college_name,d.department_name,a.A01,a.A02,a.A03,a.A04,a.A05,a.A06,a.A07,a.A08,a.A09,a.A10,a.A11,a.A12,a.A13,a.A14,a.A15,a.A16,a.A17,a.A18,a.A19,a.A20,a.A21,a.A22,a.A23,a.A24,a.A25,a.A26,a.A27,a.A28,a.A29,a.A30,a.A31,a.num_permission,num_late_entry,a.permission_cnt,a.month');
            $builder->where("a.college_id",$college_id);
            $builder->where('a.month',$from_month);
            $query_1 = $builder->get()->getResultArray();

            $builder = $db->table(STAFF_ATTENDANCE.' as a');
            $builder->join(STAFF.' as s','s.staff_id = a.staff_id','INNER');
            $builder->join(COLLEGE.' as c','c.college_id = a.college_id','INNER');
            $builder->join(DEPARTMENTS.' as d','d.department_id = s.department_id','LEFT');
            $builder->orderBy('a.staff_id','ASC');
            $builder->select('s.staff_id,s.fname,s.lname,c.college_name,d.department_name,a.A01,a.A02,a.A03,a.A04,a.A05,a.A06,a.A07,a.A08,a.A09,a.A10,a.A11,a.A12,a.A13,a.A14,a.A15,a.A16,a.A17,a.A18,a.A19,a.A20,a.A21,a.A22,a.A23,a.A24,a.A25,a.A26,a.A27,a.A28,a.A29,a.A30,a.A31,a.num_permission,num_late_entry,a.permission_cnt,a.month');
            $builder->where("a.college_id",$college_id);
            $builder->where('a.month',$to_month);
            $query_2 = $builder->get()->getResultArray();

            $query = array_merge($query_1,$query_2); 

        // $num_attd = array_merge($num_attd_1,$num_attd_2);
        }

        $all_array = array(); 	

        foreach($query as $cal_days_val){

            $n = 0;

            $no_ul = $no_cl = $no_ccl = $no_present = $no_hd = $no_off = $no_od = $no_emip = $no_lop = 0;

            for($i=1; $i<=31; $i++){
                $i = str_pad($i, 2, '0', STR_PAD_LEFT);
                if($cal_days_val['A'.$i] != ''){

                    if($cal_days_val['A'.$i] == 'P'){
                        $no_present += 1;
                    }	 				
                    else if($cal_days_val['A'.$i] == 'HD'){
                        $no_hd += 1;
                    }
                    else if($cal_days_val['A'.$i] == 'CCL'){
                        $no_ccl += 1;
                    }
                    else if($cal_days_val['A'.$i] == 'CL'){
                        $no_cl += 1;
                    }
                    else if($cal_days_val['A'.$i] == 'UL'){
                        $no_ul += 1;
                    }
                    else if($cal_days_val['A'.$i] == 'OFF'){
                        $no_off += 1;
                    }
                    else if($cal_days_val['A'.$i] == 'OD'){
                        $no_od += 1;
                    }
                    else if($cal_days_val['A'.$i] == 'EIP'){
                        $no_emip += 1;
                    }
                    else if($cal_days_val['A'.$i] == 'MIP'){
                        $no_emip += 1;
                    }
                    else if($cal_days_val['A'.$i] == 'LOP'){
                        $no_lop += 1;
                    }

                }
            }

            if($cal_days_val['permission_cnt'] >= 4){
                $cal_days_val['permission_cnt'] = ($cal_days_val['permission_cnt'] - 4);
            }
            else{
                $cal_days_val['permission_cnt'] = 0;
            }

            $no_hd = $no_hd +$cal_days_val['permission_cnt'];
            $get_staff_id = $cal_days_val['staff_id'];
            $get_month = $cal_days_val['month'];

            $all_array[] = $cal_days_val + array('no_od'=>$no_od,'no_ul'=>$no_ul,'no_cl'=>$no_cl,'no_ccl'=>$no_ccl,'no_present'=>$no_present,
            'no_hd'=>$no_hd,'no_off'=>$no_off,'no_mip_eip'=>$no_emip,'no_lop'=>$no_lop);

            $leave_counts = array('staff_id'=>$get_staff_id,'no_of_od'=>$no_od,'no_ul'=>$no_ul,'no_cl'=>$no_cl,'no_ccl'=>$no_ccl,'no_p'=>$no_present,
            'no_hd'=>$no_hd,'no_off'=>$no_off,'no_lop'=>$no_emip,'no_lop'=>$no_lop); 
            //echo $no_ul .'---'. $no_cl.'---'. $no_ccl.'---'. $no_present.'---'. $no_hd.'<br>';

            $s = $this->staffAttendanceModel->where('staff_id',$get_staff_id)->where('month',date('Y-m', strtotime($all_dates)))->set($leave_counts)->update();

        }
    
       //file name 
       $filename = 'users_'.$from_month.'-'.$to_month.'.csv'; 
       header("Content-Description: File Transfer"); 
       header("Content-Disposition: attachment; filename=$filename"); 
       header("Content-Type: application/csv; ");
       
       // get data 
       $usersData = $all_array;

       // file creation 
       $file = fopen('php://output', 'w');
     
       $header = array("Bio ID","Firstname","Lastname","Collegename","Department","A01","A02","A03","A04","A05","A06","A07","A08","A09","A10","A11","A12","A13","A14","A15","A16","A17","A18","A19","A20","A21","A22","A23","A24","A25","A26","A27","A28","A29","A30","A31","No Permission","Num Late Entry","Month","NO.of OD","NO.of UL","NO.of CL","NO.of CCL","NO.of P","NO.of HD","No.of OFF","NO.of MIP/EIP"); 
       fputcsv($file, $header);

       foreach ($usersData as $key=>$line){ 
           unset($line['permission_cnt']);
           fputcsv($file,$line); 
       }
       fclose($file); 
       exit; 
    } 

    public function download_csv_2($from_month,$to_month,$staff_recs){
                		
        // file name 
        $filename = 'hrsLog_'.$from_month.'-'.$to_month.'.csv'; 
        header("Content-Description: File Transfer"); 
        header("Content-Disposition: attachment; filename=$filename"); 
        header("Content-Type: application/csv; ");
        
        // get data 
        //$usersData = $all_array;

        // file creation 
        $file = fopen('php://output', 'w');
        
        $header = array("Bio ID","Firstname","Lastname","Collegename","Department","A01","A02","A03","A04","A05","A06","A07","A08","A09","A10","A11","A12","A13","A14","A15","A16","A17","A18","A19","A20","A21","A22","A23","A24","A25","A26","A27","A28","A29","A30","A31","Full Day","Half Day","Total Hrs","Total Working Days"); 
        fputcsv($file, $header);

        foreach ($staff_recs as $key=>$line){ 
        //unset($line['permission_cnt']);
            fputcsv($file,$line); 
        }
        fclose($file); 
        exit;  
    } 

    public function get_time_difference($time1, $time2){
		$time1 = strtotime("1/1/1980 $time1");
		$time2 = strtotime("1/1/1980 $time2");

		if ($time2 < $time1)
		{
			$time2 = $time2 + 86400;
		}

		return ($time2 - $time1) / 3600;
	}

    public function getDistance($latitude1, $longitude1, $latitude2, $longitude2 ) {  

		//echo $latitude1.'---'.$longitude1.'---'.$latitude2.'---'. $longitude2;

	    $earth_radius = 6371;

	    $dLat = deg2rad( $latitude2 - $latitude1 );  
	    $dLon = deg2rad( $longitude2 - $longitude1 );  

	    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon/2) * sin($dLon/2);  
	    $c = 2 * asin(sqrt($a));  
	    $d = $earth_radius * $c;  

	    return $d; 

	}

    public function apply_leave(){

        $data = array();

        $data['title'] = 'Leave Requests';

        if(@$this->request->getPost()){
            if(isset($this->request->getPost()['leave_reason'])){

                //echo '<pre>';  print_r($_FILES); print_r($this->request->getPost());  exit;
                //$this->staffLeaveModel->save($this->request->getPost());

                $j=1;
                $staff_insert_array = array();
                $alter_staff_status = array();
 
				for($i=0; $i<=6;$i++){
				 
					if(isset($_POST['department'][$i]) && $_POST['alter_staff'][$i] != ''  && $_POST['no_class'] == 1){
	 
						$alter_staff = $_POST['alter_staff'][$i];
						$dept_staff_hrs = $_POST['tot_hours'][$i]; 

						$staff_insert_array[] = array('college_id'=>session()->get('user_details')['college_id'],'alter_staff'=>$alter_staff,'alter_staff_type'=>($i + 1),'dept_staff_hrs'=>$dept_staff_hrs,'total_approval'=>1);

						$alter_staff_status[] = 0;
						$total_approval = 1;
					}
					else{
						$alter_staff = 0;	 
					    $alter_staff_status[] = 1;
						$total_approval = 0;
					}

					$j++;
				}

                //print_r($staff_insert_array); die;

                if($_FILES['leave_attachement']['name'] != ''){

                    $filename  = $_FILES['leave_attachement']['name'];
                    $file_type = $_FILES['leave_attachement']['type'];
                    $file_size = $_FILES['leave_attachement']['size'];
                    $extension = explode('.', $filename);
                    $ext = end($extension);
                    $tmpName  = $_FILES['leave_attachement']['tmp_name'];
                    $fileName = uniqid().".".$ext; 

                    $path = 'assets/uploads/docs/'.$fileName;  
                    move_uploaded_file($tmpName,$path);
                    
                }else{
                    $fileName = '';
                    $file_type = '';
                    $file_size = '';
                }
  
			    $insert_array = array(
                    'staff_id'=>session()->get('user_details')['staff_id'],
                    'apply_date'=>date('Y-m-d'),
                    'leave_subject'=>'Leave Request',
                    'from_date'=>$_POST['from_date'],
                    'to_date'=>$_POST['to_date'],
                    'total_days'=>$_POST['no_days'],
                    'leave_type'=>$_POST['leave_type'],

                    'od_topic'=>$_POST['od_leave_topic'],
                    'od_location'=>$_POST['od_location'],
                    'od_accommdation'=>$_POST['od_accommodation'],
                    'od_finance'=>$_POST['od_finance'],	

                    'leave_day_type'=>$_POST['leave_day_type'],
                    'reason'=>$_POST['leave_reason'],				  
                    'file_name'=>$fileName,
                    'file_type'=>$file_type,
                    'file_size'=>$file_size, 
                    'alter_staff1_status'=>$alter_staff_status[0],
                    'alter_staff2_status'=>$alter_staff_status[1],
                    'alter_staff3_status'=>$alter_staff_status[2],
                    'alter_staff4_status'=>$alter_staff_status[3],
                    'alter_staff5_status'=>$alter_staff_status[4],
                    'alter_staff6_status'=>$alter_staff_status[5],
                    'alter_staff7_status'=>$alter_staff_status[6], 
                    'approve_status'=>1
                );

                //print_r($insert_array); die;		 	 
                //$this->insert_leave_data($insert_array,$staff_insert_array);
                
                if($this->insert_leave_data($insert_array,$staff_insert_array)){			    
                    echo '1|Successfully applied leave';
                }else{
                    echo '0|'.$_POST['leave_type'].' Leave Type Not Available';
                }

                exit;

			}           			
        }

        echo view('common/header',$data);
        echo view('apply_leave',$data);
        echo view('common/footer');

    }

    public function cancel_leave(){

        if(@$this->request->getPost()){
           
            if(isset($this->request->getPost()['leave_id'])){

                //echo '<pre>'; print_r($this->request->getPost()); die;
                $isData = $this->staffLeaveModel->where('emp_leave_id',$this->request->getPost()['leave_id'])->get()->getRowArray();
                //echo '<pre>'; print_r($isData); die;
                $res = 0;
                if($isData['approve_status'] == 1 && $isData['cancel_status']== 0 ){
                    $this->staffLeaveModel->where('emp_leave_id',$this->request->getPost()['leave_id'])->set(array('reject_reason'=>$this->request->getPost()['cancel_reason'],'approve_status'=>0,'cancel_status'=>1))->update();
                    $this->leaveLogsModel->save($isData);
                    $res = 1;
                }else if($isData['approve_status']==2 || $isData['approve_status']== 3 && $isData['cancel_status']== 0){
                    $this->staffLeaveModel->where('emp_leave_id',$this->request->getPost()['leave_id'])->set(array('reject_reason'=>$this->request->getPost()['cancel_reason'],'approve_status'=>0))->update();
                    $res = 1;
                }

                echo $res;

            }
        }    

    }

    public function cancel_leave_requests(){

        $data = array();

        $data['title'] = 'Cancel Leave Requests';

        //echo '<pre>'; print_r($this->fetch_cancel_leave_request()); die;

        echo view('common/header',$data);
        echo view('cancel_leave_requests',$data);
        echo view('common/footer');

    }

    public function fetch_cancel_leave_request(){

        $rowperpage = 25;
        $start = 0;
        $draw = 9;
        $totalRecords = $columnName = $columnSortOrder = 1;
        $response = $records = $data = array();        
        //if($this->request->getVar('type')==='staff'){
            // echo "<pre>";print_r($this->request->getVar('order')[0]['column']);die;

            if(@$this->request->getVar('draw'))
            $draw = $this->request->getVar('draw');

            if(@$this->request->getVar('start'))
            $start = $this->request->getVar('start');

            if(@$this->request->getVar('length'))
            $rowperpage = $this->request->getVar('length'); // Rows display per page

            if(@$this->request->getVar('order')[0]['column'])
            $columnIndex = $this->request->getVar('order')[0]['column']; // Column index

            if(@$this->request->getVar('columns')[$columnIndex]['data'])
            $columnName = $this->request->getVar('columns')[$columnIndex]['data']; // Column name

            if(@$this->request->getVar('order')[0]['dir'])
            $columnSortOrder = $this->request->getVar('order')[0]['dir']; // asc or desc

            $db = \Config\Database::connect();
            $builder = $db->table(CANCEL_LEAVE_LOGS.' as sl');

            $user_type_id = session()->get('user_details')['staff_id'];
            $college_id = session()->get('user_details')['college_id'];
            $user_type = session()->get('user_details')['user_type'];       

            if($user_type == 'super_admin'){
                $builder->where('sl.status ',1); 
                $builder->where('sl.report1',0);
                $builder->where('sl.report2',0);
                $builder->join(''.STAFF.' as s','sl.staff_id = s.staff_id AND s.status = 1');
                $builder->orderBy('sl.emp_leave_id','DESC');
                $records = $builder->get()->getResult();
            }else{

                $get_emp_staff_id = $this->staffModel->where('staff_id',$user_type_id)->where('college_id',$college_id)->select('staff_id')->get()->getRowArray();

                //echo '<pre>'; print_r($get_emp_staff_id); die;

                //get_level 1 
                $builder->where('sl.approve_type',2);
                $builder->where('sl.status ',1); 
                $builder->where('sl.report1',$get_emp_staff_id['staff_id']);
                $builder->join(STAFF.' as s','sl.staff_id = s.staff_id AND s.status = 1');
                $builder->orderBy('sl.emp_leave_id','DESC');
                $get_cancel_level1 = $builder->get()->getResult(); 

                //get_level 2 
                $builder->where('sl.approve_type >=',1); 	 
                $builder->where('sl.status ',1); 
                $builder->where('sl.report1',0);
                $builder->where('sl.report2',$get_emp_staff_id['staff_id']);
                $builder->join(STAFF.' as s','sl.staff_id = s.staff_id AND s.status = 1');
                $builder->orderBy('sl.emp_leave_id','DESC');
                $get_cancel_level2 = $builder->get()->getResult();

                $records = array_merge($get_cancel_level1,$get_cancel_level2);	
            }

            if(@session()->get('user_details')['staff_id'] && session()->get('user_details')['staff_id']!=0){
                $builder->where('staff_id',session()->get('user_details')['staff_id']);
            }

            $totalRecords = !empty($records)?$builder->countAllResults():0; 

            $i=1;
            foreach ($records as $k=>$r) {

                $data[] = array( 
                    "sno"=>$i++,
                    "leave_log_id"=>$r->leave_log_id,
                    "emp_leave_id"=>$r->emp_leave_id,
                    "staff_id"=>$r->staff_id,
                    "fname"=>$r->fname.' '.$r->lname,
                    "apply_date"=>$r->apply_date,
                    "leave_subject"=>$r->leave_subject,                  
                    "staff_id"=>$r->staff_id,
                    "from_date"=>$r->from_date,
                    "to_date"=>$r->to_date,
                    "total_days"=>$r->total_days,
                    "leave_type"=>$r->leave_type,
                    "leave_day_type"=>$r->leave_day_type,
                    "reason"=>$r->reason,
                    "approve_type"=>$r->approve_type,
                    "approve_status"=>$r->approve_status,
                    //"cancel_status"=>$r->cancel_status,
                    "status"=>$r->status
                    );
            }
                    
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecords,
            "data" => $data
        );

        // echo "<pre>";print_r($response);die;
        echo json_encode($response);

    }

    public function insert_leave_data($leave_data,$staff_data){
	 
	    $total_leaves = $leave_data['total_days'];
        	    
    	$select_leave_mng = $this->leaveManagementModel->where('status',1)->where('staff_id',$leave_data['staff_id'])->get()->getRowArray();

    		$avi_cl = $avi_ccl = 0; 
    		
    		if($select_leave_mng != null){   		    
    		    $avi_cl = $select_leave_mng['cl_total'] - $select_leave_mng['cl_taken'];
    		    $avi_ccl = $select_leave_mng['ccl_total'] - $select_leave_mng['ccl_taken'];
    		}
    		
    		$condition = '';
    		
    	    if($leave_data['leave_type'] == 'CCL'){
    	        
    	        if($avi_ccl >= $total_leaves){
    	            $condition = 1;
    	        }
    	        else{
    	            $condition = 0;
    	        }    	        
    	        
    	    }else if($leave_data['leave_type'] == 'CL'){
    	        
    	        if($avi_cl >= $total_leaves){
    	            $condition = 1;
    	        }
    	        else{
    	            $condition = 0;
    	        }    	        
    	    }
    	    else{
    	        $condition = 1;
    	    }
    	    
    	    if($condition == 1){

                $db = \Config\Database::connect();
                $builder = $db->table(STAFF.' as s');
        		$builder->where('s.staff_id',session()->get('user_details')['staff_id']);
        		$builder->where('s.status',1);
        		$builder->where('s.college_id',session()->get('user_details')['college_id']);
        		$builder->join(ROLES.' as r','s.role_id = r.role_id AND r.status = 1' ,'LEFT');
        	    $builder->join(DEPARTMENTS.' as d','s.department_id = d.department_id AND d.status = 1','LEFT');
				//$this->db->select('sl.approve_type,sl.staff_type,sl.reporting_person1_role,sl.reporting_person2_role,sl.staff_attendance_type','r.role','sl.role_id');
        		$get_approve_type = $builder->get()->getRowArray();
								 
        		if($get_approve_type['approve_type'] == 2){
        			$leave_data['approve_status'] = 1;
        		}else if($get_approve_type['approve_type'] == 1){	
        			$leave_data['approve_status'] = 2;
        		}         
        		        
        		$leave_data =  $leave_data + array('approve_type'=>$get_approve_type['approve_type'],'report1'=>$get_approve_type['reporting_person_1'],'report2'=>$get_approve_type['reporting_person_2']); 
                
        		$db->table(STAFF_LEAVE)->insert($leave_data);                
        		$insert_id = $this->db->insertID(); 

        		if(count($staff_data) != 0){
					
					ignore_user_abort(true); 
				
                    $title = 'LEAVE  NOTIFICATION';
            
                    $path_to_fcm='https://fcm.googleapis.com/fcm/send';

                    $server_key='AAAAOBZJZAc:APA91bERj5LAo6dq2kpRz3faDSfUd0e1wYwuiTXVNzRgJb_giqytoc9i_WjM93ctuhabHIgOuJI-IkDz2MOqT_sWL9bx_9n4p_CFpfAV9fkffWcIq1KxTgCUD5zOBcFBOTWem1KkRVeq';

                    $tempor = base_url().'assets/images/logo.png';

                    $article_title = strtoupper($get_approve_type['fname'].' '.$get_approve_type['lname'].'('.$get_approve_type['department_name'].') Waiting For your approval'); 
					
					//print_r($get_approve_type); die;
        
        			$insert_staff_array = array();
        
        			foreach($staff_data as $staff){
                        
						$builder = $db->table(MOBILE_CLIENT_TOKEN);
						$builder->where('client_mail_id',$staff['alter_staff']);
						$builder->distinct('client_token');
						$builder->select('client_token ');
						$mobile_tokens = $builder->get()->getRowArray();
						
						//print_r($mobile_tokens);
						
						if($mobile_tokens != null){
							$key=$mobile_tokens['client_token'];
							$headers = [
							  'Authorization: key=' . $server_key,
							  'Content-Type: application/json'
							];
							
							$fields = array('to' =>$key ,'data'=>array('image' =>$tempor,'title' =>$title ,'body'=> $article_title));														 
							$payload=json_encode($fields);
							
							$curl_session=curl_init();
							curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
							curl_setopt($curl_session, CURLOPT_POST, true);
							curl_setopt($curl_session, CURLOPT_HTTPHEADER, $headers);
							curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
							curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
							curl_setopt($curl_session, CURLOPT_POSTFIELDS, $payload);
							$result=curl_exec($curl_session);
							
							//print_r($result); die;						  
							curl_close($curl_session);
						}					 
        
        			    $insert_staff_array[] = $staff + array('emp_leave_id'=>$insert_id,'college_id'=>session()->get('user_details')['college_id']);
        			}
                    $builder = $db->table(STAFF_ALTER_LEAVE);
                    $builder->insertBatch($insert_staff_array);					
					//echo $this->db->getLastQuery();
                    //print_r($insert_staff_array); die;                    
        		}       					 
        
				if( strtoupper($get_approve_type['role_name']) == 'PRINCIPAL' || strtoupper($get_approve_type['role_name']) == 'ED' || strtoupper($get_approve_type['role_name']) == 'EO' || strtoupper($get_approve_type['role_name']) == 'FO' || strtoupper($get_approve_type['role_name']) == 'DEAN' || strtoupper($get_approve_type['role_name']) == 'S_COORDINATIOR'){
		
                    $builder = $db->table(STAFF.' as s');
					$builder->where('s.status',1);
					$builder->where('s.staff_id',$leave_data['staff_id']);
					$builder->where('sl.emp_leave_id',$insert_id);		
					$builder->select('s.staff_id,s.fname,s.lname,s.email,s.s_id,sl.apply_date,sl.leave_subject,sl.from_date,sl.to_date,sl.total_days,sl.reason,r.role_name,c.college_name');
					$builder->join(STAFF_LEAVE.' as sl','sl.staff_id = s.staff_id','INNER'); 
					$builder->join(ROLES.' as r','r.role_id = s.role_id AND r.status = 1' ,'LEFT');
					$builder->join(COLLEGE.' as c','c.college_id = s.college_id AND c.status = 1' ,'INNER');
					$leave_management = $builder->get()->getRowArray();
                    //print_r($leave_management); die; 
					$this->send_mail($leave_management,$insert_id);						
				} 

    	    }
    	    
    	return $condition;
	}

    public function send_mail($data,$insert_id){
        
        $email_data = $this->commonMailModel->where('status',1)->where('user_type','SM')->get()->getResultArray();
        $to_mail = $this->commonMailModel->where('status',1)->where('user_type','MD')->get()->getResultArray();
        
        //print_r($email_data); print_r($to_mail); die;
                            
        $name = $data['fname'].' '.$data['lname'] ;
        $college_name = $data['college_name'];
        $staff_id = $data['s_id'];
        $staff_name_id = $data['staff_id'];

        $applay_date = date('d-m-Y',strtotime($data['apply_date']));
        $from_date = date('d-m-Y',strtotime($data['from_date']));
        $to_date = date('d-m-Y',strtotime($data['to_date']));
        $total_days = $data['total_days'];
        $subject = $data['leave_subject'];
        $reason = $data['reason'];
        $email_id=  $data['email'];
        $role = $data['role_name'];
    
        if($total_days > 1)
        {
            $day_title = 'Total Days';
        }else{

            $day_title = 'Total Day';      
        }
                                    
        foreach($to_mail as $to){    
    
            $subject = "".$name." (".$staff_name_id.") (".$from_date." To ".$to_date.") Leave Request";
            $message = '<p style="font-size: 18px;">Name : '.$name.' </p>
                    <p style="font-size: 18px;">Designation : '.$role.'</p>
                    <p style="font-size: 18px;">College : '.$college_name.' </p>
                    <p style="font-size: 18px;">Apply Date : '.$applay_date.' </p>
                    <p style="font-size: 18px;">From Date : '.$from_date.' </p> 
                    <p style="font-size: 18px;">To Date : '.$to_date.' </p>
                    <p style="font-size: 18px;">'.$day_title.' : '.$total_days.' </p>
                    <p style="font-size: 18px;">Reason : '.$reason.' </p>
                    <br> 
                    <a href="'.base_url().'nonteaching_approval?leave_id='.$insert_id.'&staff_id='.$staff_name_id.'&type=leave_req&status=3&email_id='.$to['approve_mail_name'].'"> 
                    <button type="button" style= "cursor:pointer;background-color: #00a65a;color: white;padding: 8px;border: 1px solid #00a65a;">Accept</button></a>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="'.base_url().'nonteaching_approval?leave_id='.$insert_id.'&staff_id='.$staff_name_id.'&type=leave_req&status=0&email_id='.$to['approve_mail_name'].'"> 
                    <button type="button" style= "cursor:pointer;background-color:#d54b3d;color: white;padding: 8px;border: 1px solid #d54b3d;">Reject</button></a>';                    
                    
            /*require_once("mailer/class.phpmailer.php"); // include the class name                    
            $mail = new PHPMailer(); // create a new object
            $mail->IsSMTP(); // enable SMTP
            $mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
            $mail->SMTPAuth = true; // authentication enabled
            $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
            $mail->Host = "smtp.gmail.com";
            $mail->Port = 465; // or 587
            $mail->IsHTML(true);    
            foreach($email_data as $sm){
                $mail->Username = $sm['email_id'];
                $mail->Password = $sm['password'];
                $mail->SetFrom($sm['email_id']);
            }    
            $mail->AddAddress($to['email_id']);                  
            $mail->Subject = $subject;
            $mail->Body = $message;
            if(!$mail->Send()){
                echo "Mailer Error: " . $mail->ErrorInfo;
            }*/

            $email = \Config\Services::email();
                
            $email->setTo('suresh.mnw1877@gmail.com');
            //$email->setTo($to['email_id']);
            //foreach($email_data as $sm){
                $email->setFrom($email_data[0]['email_id']);
            //}
            
            $email->setSubject($subject);
            $email->setMessage($message);

            if (!$email->send()){
                $res = $email->printDebugger(['headers']);
                print_r($res);
            }                
        
        }
                    
    }

    public function is_exist_leave(){

        $db = \Config\Database::connect();
        $res = '1';

		if(session()->get('user_details')['college_id'] == 6 && $data['college_leave_type'] == 'OD'){

            $builder = $db->table(STAFF_LEAVE);
			$builder->where('status',1);
		 	$builder->where('staff_id',session()->get('user_details')['staff_id']);	 	 
		 	$builder->where('leave_type',$_POST['leave_type']);
			$builder->where("DATE_FORMAT(from_date,'%Y')",date('Y'));			
			$builder->groupStart();
			$builder->where('approve_status !=',0);	 
			$builder->groupEnd();	
			$builder->groupBy('staff_id');
			$builder->select('sum(total_days) as total_days');
		 	$get_dept_leave = $builder->get()->getRowArray();		 	 

		 	if($get_dept_leave['total_days'] > 0){
		 		$total_leaves = $_POST['no_days']  + $get_dept_leave['total_days'];
		 		if($total_leaves > 7){
                    $res = 'Exist';
		 		}		 		 
		 	}
		}
	 	
		if($_POST['leave_type'] == 'CL' || $_POST['leave_type'] == 'CCL'){


            $builder = $db->table(LEAVE);
			$builder->where('status',1);
			$builder->where('staff_id', session()->get('user_details')['staff_id']);
			$leave_management = $builder->get()->getRowArray();
			
            $builder = $db->table(STAFF_LEAVE);
			$builder->where('status',1);
		 	$builder->where('staff_id',session()->get('user_details')['staff_id']);	 	 
		 	$builder->where('leave_type',$_POST['leave_type']);	
			$builder->groupStart();
			$builder->where('approve_status !=',0);	
			$builder->where('approve_status !=',3);
			$builder->groupEnd();			
			$builder->groupStart();
				$builder->orWhere('YEAR(from_date) = YEAR(NOW())');
				$builder->orWhere('YEAR(to_date) = YEAR(NOW())');
			$builder->groupEnd();	
			$builder->groupBy('staff_id');
			$builder->select('sum(total_days) as total_days');
		 	$get_dept_leave = $builder->get()->getRowArray();

            //echo $this->db->getLastQuery(); 

            //print_r($get_dept_leave); die;
						 
                if(isset($get_dept_leave['total_days'])){
		 		    $total_leaves = $_POST['no_days']  + $get_dept_leave['total_days'];
                }else{
                    $total_leaves = $_POST['no_days'];
                }

		 		$leave_management_td_cl = ($leave_management['cl_total'] + $leave_management['cl_last_total']);

		 		$leave_management_td_ccl = ($leave_management['ccl_total'] + $leave_management['ccl_last_total']);

		 		//echo $leave_management_td_ccl.'--'.$total_leaves; die;
 
		 		if($leave_management_td_cl < $total_leaves && $_POST['leave_type'] == 'CL'){
                    $res = 'CL_Exist';                    
		 		}else if($leave_management_td_ccl < $total_leaves && $_POST['leave_type'] == 'CCL'){
                    $res = 'CCL_Exist';
		 	}		 	
		}

        echo $res;
			 	 
	}

    public function fetch_leaves(){

        $rowperpage = 25;
        $start = 0;
        $draw = 9;
        $totalRecords = $columnName = $columnSortOrder = 1;
        $response = $records = $data = array();        
        //if($this->request->getVar('type')==='staff'){
            // echo "<pre>";print_r($this->request->getVar('order')[0]['column']);die;

            if(@$this->request->getVar('draw'))
            $draw = $this->request->getVar('draw');

            if(@$this->request->getVar('start'))
            $start = $this->request->getVar('start');

            if(@$this->request->getVar('length'))
            $rowperpage = $this->request->getVar('length'); // Rows display per page

            if(@$this->request->getVar('order')[0]['column'])
            $columnIndex = $this->request->getVar('order')[0]['column']; // Column index

            if(@$this->request->getVar('columns')[$columnIndex]['data'])
            $columnName = $this->request->getVar('columns')[$columnIndex]['data']; // Column name

            if(@$this->request->getVar('order')[0]['dir'])
            $columnSortOrder = $this->request->getVar('order')[0]['dir']; // asc or desc

            $db = \Config\Database::connect();
            $builder = $db->table(STAFF_LEAVE.' as l');

            if(@session()->get('user_details')['staff_id'] && session()->get('user_details')['staff_id']!=0){
                $builder->where('s.staff_id',session()->get('user_details')['staff_id']);
            }

            if(@$this->request->getVar('search')['value']){
                $searchValue = $this->request->getVar('search')['value']; // Search value 
                $builder->like('l.staff_id',$searchValue);
                $builder->orLike('l.apply_date',$searchValue);
                $builder->orLike('l.leave_subject',$searchValue);
                $builder->orLike('s.fname',$searchValue);
            }
            
            $records = $builder->join(STAFF.' as s','s.staff_id=l.staff_id','LEFT')->select('l.emp_leave_id,l.staff_id,s.fname,s.lname,l.apply_date,l.leave_subject,l.from_date,l.to_date,l.total_days,l.leave_type,l.leave_day_type,l.reason,l.approve_type,l.approve_status,l.cancel_status,l.status')->orderBy($columnName, $columnSortOrder)->limit($rowperpage, $start)->get()->getResult();

            if(@session()->get('user_details')['staff_id'] && session()->get('user_details')['staff_id']!=0){
                $builder->where('staff_id',session()->get('user_details')['staff_id']);
            }

            $totalRecords = !empty($records)?$builder->countAllResults():0; 

            $i=1;
            foreach ($records as $k=>$r) {
                $data[] = array( 
                    "sno"=>$i++,
                    "emp_leave_id"=>$r->emp_leave_id,
                    "staff_id"=>$r->staff_id,
                    "fname"=>$r->fname.' '.$r->lname,
                    "apply_date"=>$r->apply_date,
                    "leave_subject"=>$r->leave_subject,                  
                    "staff_id"=>$r->staff_id,
                    "from_date"=>$r->from_date,
                    "to_date"=>$r->to_date,
                    "total_days"=>$r->total_days,
                    "leave_type"=>$r->leave_type,
                    "leave_day_type"=>$r->leave_day_type,
                    "reason"=>$r->reason,
                    "approve_type"=>$r->approve_type,
                    "approve_status"=>$r->approve_status,
                    "cancel_status"=>$r->cancel_status,
                    "status"=>$r->status
                    );
            }
            //echo "<pre>";print_r($columnSortOrder);die;
        //}
                    
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecords,
            "data" => $data
        );

        // echo "<pre>";print_r($response);die;
        echo json_encode($response);
    }

    public function get_leave_policy(){
	  
        //print_r($_POST); die;
		if($_POST){	 
			$from_date = $_POST['from_date'];
			$to_date = $_POST['to_date'];

			$data = array('from_date'=>$from_date,'to_date'=>$to_date,'no_days'=>$_POST['no_days'],'staff_id'=>session()->get('user_details')['staff_id'],'college_id'=>session()->get('user_details')['college_id']);
		}	
 		else{
			$data = json_decode(file_get_contents('php://input'),true);			
			$data = $data + array('college_id'=>session()->get('user_details')['college_id'],'staff_id'=>session()->get('user_details')['staff_id']);
		}
		 
		$op = $this->get_LeavePolicy($data);
		echo $op;		
	}

    public function get_LeavePolicy($data){

		$fromdate = $data['from_date'];
		$todate = $data['to_date'];
		$no_days = $data['no_days']; 
		$f_month_year_date = date('Y-m',strtotime($data['from_date']));
		$t_month_year_date = date('Y-m',strtotime($data['to_date'])); 
		$total_cl=0;
		$all_dates = array();

		for ($i=strtotime($fromdate); $i<=strtotime($todate); $i+=86400) {  
		    $all_dates[] = date("Y-m-d", $i); 
           
		}

        //print_r($all_dates); die;

        $db = \Config\Database::connect();
        $builder = $db->table(STAFF_LEAVE.' as l');
	 	$builder->where('status',1);
	 	$builder->where('staff_id',$data['staff_id']);
	 	$builder->groupStart();
		$builder->where("DATE(from_date) IN ('".implode("','", $all_dates)."')");
		$builder->orWhere("DATE(to_date) IN ('".implode("','", $all_dates)."')");
		$builder->orWhere("'$fromdate' BETWEEN from_date AND to_date ");
		$builder->orWhere(" '$todate' BETWEEN from_date AND to_date");
		$builder->groupEnd();
		$builder->groupStart();
		$builder->where('approve_status !=',0);	 
		$builder->groupEnd();	
        $row_array = $builder->get()->getRowArray();

        $builder = $db->table(STAFF_LEAVE.' as l');
	 	$builder->where('status',1);
	 	$builder->where('staff_id',$data['staff_id']);
	 	$builder->groupStart();
		$builder->where("DATE(from_date) IN ('".implode("','", $all_dates)."')");
		$builder->orWhere("DATE(to_date) IN ('".implode("','", $all_dates)."')");
		$builder->orWhere("'$fromdate' BETWEEN from_date AND to_date ");
		$builder->orWhere(" '$todate' BETWEEN from_date AND to_date");
		$builder->groupEnd();
		$builder->groupStart();
		$builder->where('approve_status !=',0);	 
		$builder->groupEnd();	

        $row_count = $builder->countAllResults();
        		
		$db_count_cl =  $db->table(STAFF_LEAVE)->where('status',1)
                        ->where('staff_id',$data['staff_id'])
                        ->where('leave_type','CL')
                        ->where("DATE_FORMAT(from_date,'%Y-%m')",$f_month_year_date)
                        ->where("DATE_FORMAT(to_date,'%Y-%m')",$t_month_year_date)
                        ->select('COALESCE(SUM(total_days),0) as count_cl')
                        ->get()->getRowArray();
		
		$total_cl = $db_count_cl['count_cl']+$no_days;
		// return json_encode($leave_array['row_count']=$total_cl); die;
		 
		if($row_count == 1){
							
			if($row_array['total_days'] == 0.5){
				$leave_management = $db->table(LEAVE)->where('status',1)->where('staff_id', $data['staff_id'])->get()->getRowArray();

				$avi_leave_cl = ($leave_management['cl_last_total'] + $leave_management['cl_total']) - $leave_management['cl_taken'];
				$avi_leave_ccl = ($leave_management['ccl_last_total'] + $leave_management['ccl_total']) - $leave_management['ccl_taken'];

				//echo $avi_leave_cl.''.$avi_leave_ccl;

				$leave_array = array('LOP','OD','SPELL','HD');  //'SPECIAL LEAVE'

				if($row_array['total_days'] <= $avi_leave_cl && $no_days <= 3){

					if($total_cl<=3 && $total_cl>=0){
						$leave_array[] = 'CL'; 
					}  

				}
				if($row_array['total_days'] <= $avi_leave_ccl && $no_days <= 2){
					$leave_array[] = 'CCL'; 
				}

				return json_encode($leave_array);
			}
			else{
				return 'Already_Exist';
			} 	 			
			
		}else if($row_count == 0){
			
			$leave_management = $db->table(LEAVE)->where('status',1)->where('staff_id', $data['staff_id'])->get()->getRowArray();
			//$this->db->where('college_id',$data['college_id']);

			$avi_leave_cl = ($leave_management['cl_last_total'] + $leave_management['cl_total']) - $leave_management['cl_taken'];
			$avi_leave_ccl = ($leave_management['ccl_last_total'] + $leave_management['ccl_total']) - $leave_management['ccl_taken'];

			//echo $avi_leave_cl.''.$avi_leave_ccl;

			if($data['college_id'] == 6){
				$leave_array = array('LOP','SPELL');  //'SPECIAL LEAVE'

				if($no_days <= 7){
					$leave_array[] = 'OD'; 
				}
			}
			else{
				$leave_array = array('LOP','OD','SPELL'); //'SPECIAL LEAVE' 
			}

			if($no_days <= $avi_leave_cl && $no_days <= 3){
				if($total_cl<=3 && $total_cl>=0){
					$leave_array[] = 'CL'; 
				} 
			}
			
			if($avi_leave_cl == 0.5 && $no_days == 1){

				if($total_cl<=3 && $total_cl>=0){
					$leave_array[] = 'CL'; 
				}					 
			}
			
			if($no_days <= $avi_leave_ccl && $no_days <= 2){
			
				$leave_array[] = 'CCL'; 
			}
			
			if($avi_leave_ccl == 0.5 && $no_days == 1){
				$leave_array[] = 'CCL'; 
			}
			
			return json_encode($leave_array);
		}	
		else{
			return 'Already_Exist';
		}

	}

    public function get_department_list(){

        try{
            $data = array();
            
            $data = $this->db->table(DEPARTMENTS)->select('department_id,department_name')->where('college_id',session()->get('user_details')['college_id'])->where('status',1)->groupBy('department_name')->get()->getResultArray();

            //echo $this->db->getLastQuery(); die;
            
            $dataList = '<option value="">-- Select Department --</option>';
            if(count($data)>0){
                foreach ($data as $key => $value) {
                    $dataList.='<option value="'.$value['department_id'].'">'.ucfirst($value['department_name']).'</option>';
                }
            }else{
                $dataList = '<option value="">No Departments';
            }

            echo  $dataList;

        }catch(\Exception $e){
            print_r($e->getMessage()); //die();
            echo json_encode(array('isError'=>$e->getMessage()));
        }

    }

    public function get_deprtment_staff(){

        $db = \Config\Database::connect();

		$staff_id = session()->get('user_details')['staff_id'];
        $college_id = session()->get('user_details')['college_id'];
		$get_all_staffs =  $db->table(STAFF)->where('college_id',$college_id)
                            ->where('staff_id !=',$staff_id)
                            ->where('status',1)
                            ->where('teaching_type',0)
                            ->where('department_id',$_POST['dept_id'])
                            ->get()->getResultArray(); 

		echo json_encode($get_all_staffs);

	}

}
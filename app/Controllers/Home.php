<?php

namespace App\Controllers;
use CodeIgniter\Controller;

use App\Models\UserModel; 
use App\Models\RolesModel;
#use App\Models\MenuModel; 
#use App\Models\MenuListModel; 
#use App\Models\MenuPreferenceModel;
use App\Libraries\CommonInterface;
#use App\Models\ForgotPasswordModel;
#use App\Models\CalendarModel;
use ZipArchive;

class Home extends Controller{
    
    function __construct(){

        date_default_timezone_set("Asia/Kolkata");

        $this->userModel  = new UserModel();
        $this->rolesModel = new RolesModel();
        #$this->menuModel = new MenuModel();
        #$this->menuListModel = new MenuListModel();
        #$this->menuPreferenceModel = new MenuPreferenceModel();     
        $this->commonInterface = new CommonInterface();
 	    #$this->forgotPasswordModel = new ForgotPasswordModel();

        helper(['form', 'url']);
        $this->db = db_connect(); // Loading database
		$this->session	= \Config\Services::session(); 

    }

    public function index(){
        $data = array();
        $data['title'] = 'Dashboard';
        #$data['info'] = $this->dashboard();

        echo view('common/header',$data);
        echo view('dashboard',$data);
        echo view('common/footer');
    }

    public function dashboard(){

        if(@session('curr_date'))
        $data['curr_date']=$_SESSION['curr_date'];
        else
        $data['curr_date']=date('m/d/Y');

        $data['total_users'] = $data['total_courses'] = $data['total_companies'] = $data['total_assessments'] = $data['total_process'] = $data['total_processed'] = $data['total_expired'] = array();

        $data['total_users'] = $this->formUserModel->select("id,is_form_users")->get()->getResultArray();

        if(@$data['total_users']){
            $to_array = array();
            foreach ($data['total_users'] as $k => $user) {
               $exp_user = explode(',',$user['is_form_users']);
               foreach ($exp_user as $key => $value) {
                array_push($to_array,$value);
               }               
            }

            $data['total_users'] = count($to_array);
        }

        //echo '<Pre>'; print_r($data['total_users']); die;        
        $data['total_courses'] = $this->courseAllocationModel->select('course_id')->where('is_users_status!=',NULL)->where('is_status',1)->groupBy('course_id')->countAllResults();
        $data['total_companies'] = $this->companyModel->select("course_id")->groupBy('company_name')->countAll();
        $data['total_assessments'] = $this->courseAllocationModel->select("course_id")->countAll();
        $data['total_process'] = $this->courseAllocationModel->select("DATE_FORMAT(STR_TO_DATE(start_date,'%m/%d/%Y'),'%m/%d/%Y') as start_date")->where("DATE_FORMAT(STR_TO_DATE(end_date,'%m/%d/%Y'),'%m/%d/%Y')<=",$data['curr_date'])->where("DATE_FORMAT(STR_TO_DATE(end_date,'%m/%d/%Y'),'%m/%d/%Y')>=",$data['curr_date'])->where('is_status',1)->countAllResults();
        $data['total_processed'] = $this->courseAllocationModel->select("DATE_FORMAT(STR_TO_DATE(start_date,'%m/%d/%Y'),'%m/%d/%Y') as start_date")->where("DATE_FORMAT(STR_TO_DATE(end_date,'%m/%d/%Y'),'%m/%d/%Y')<",$data['curr_date'])->where('is_users_status!=',NULL)->where('is_status',1)->countAllResults();
        $data['total_expired'] = $this->courseAllocationModel->select("DATE_FORMAT(STR_TO_DATE(exp_date,'%m/%d/%Y'),'%m/%d/%Y') as exp_date")->where("DATE_FORMAT(STR_TO_DATE(exp_date,'%m/%d/%Y'),'%m/%d/%Y')<",$data['curr_date'])->where('is_users_status',NULL)->where('is_status',1)->countAllResults();

        //echo '<pre>'; print_r($data); die;
        return $data;
    }

    public function lms(){

        $data = array();
        $data['title'] = 'LMS';
        #$data['info'] = $this->dashboard();

        echo view('common/header',$data);
        echo view('lms',$data);
        echo view('common/footer');
    }
    
}
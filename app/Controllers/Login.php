<?php

namespace App\Controllers;
use CodeIgniter\Controller;
use App\Models\UserModel; 
use App\Models\RolesModel; 
#use App\Models\StaffModel;  
#use App\Services\OktaApiService as Okta;
require('vendor/autoload.php');
use Auth0\SDK\Auth0;
use Auth0\SDK\Configuration\SdkConfiguration;
    
class Login extends Controller{

    function __construct(){
        date_default_timezone_set('Asia/Kolkata');
        $this->userModel  = new UserModel();
        $this->rolesModel = new RolesModel();
        #$this->staffModel = new StaffModel();
        #$this->okta = new Okta;
        helper(['form', 'url']);
        $this->db = db_connect(); // Loading database
		$this->session	= \Config\Services::session();

        $this->configuration = new SdkConfiguration(
            domain: 'dev-44uhudulet0f7kxq.us.auth0.com',
            clientId: 'hmIaenviRl0HPyVkpparSVEyYlEoSP1z',
            clientSecret: '-nMtt7GQko5WuWMT12z_MQpqRHYCMAGmhoAb4v94CbUTLbSYXEle7s0QwJ8G31-N',
            redirectUri: base_url('callback'),
            cookieSecret: '4f60eb5de6b5904ad4b8e31d9193e7ea4a3013b476ddb5c259ee9077c05e1457'
            );
        
       $this->sdk = new Auth0($this->configuration);
        
    }

    public function index(){

        if(@session()->getFlashdata('error')){
            return view('login');
            exit();
        }

        if(!$_REQUEST){           
            return redirect()->to($this->sdk->login());
        }else{

            echo '<pre>'; print_r($this->request->getGet());
            print_r($this->sdk->getCredentials()); die;

            if(@$this->request->getGet('SAMLRequest')){
                #echo '<pre>'; print_r($sdk->getCredentials()); die;
                if(@$this->sdk->getCredentials()){
                    session()->set('user_details',(array)$this->sdk->getCredentials());
                    return redirect()->to(base_url('profile'));
                }

                session()->setFlashdata('error','Invalid Authorization Provider');
                
                return redirect()->to(base_url('login'));
            }
            #echo '<pre>'; print_r(session()->get('user_details')); die;
        }
    
        #echo '<pre>'; print_r($this->sdk); die;
        
    }

    public function profile(){

        $data = array();
        $data['title'] = 'Profile';	
        
        #echo '<pre>'; print_r(session('user_details')); die;       

        if(@$this->request->getPost()){

            $data = $this->request->getPost('image');
            $file_path = FCPATH.'/uploads/profile/';
            //echo $this->request->getVar('s_id'); die;

            $image_array_1 = explode(";", $data);
            $image_array_2 = explode(",", $image_array_1[1]);
            
            $data = base64_decode($image_array_2[1]);
            $imageName = 'profile_'.time() . '.png';
            //$imageName = $this->request->getPost('file_name');
            if(file_put_contents($file_path.$imageName,$data)){
                $this->staffModel->where('s_id',$this->request->getVar('s_id'))->set(array('profile'=>$imageName))->update();
            }

            echo '<img src="uploads/profile/'.$imageName.'" class="img-thumbnail" />';
            exit;
        }

        echo view('common/header',$data);
		echo view('profile',$data);
		echo view('common/footer');	

    }

    public function logout(){
        if(@session()->remove('user_details'))
        session()->remove('user_details');

        return redirect()->to('login');
    }

    public function callback(){

        #echo '<pre>'; print_r(session('user_details'));
        #echo '<Pre>'; print_r($_REQUEST); die;
        
        /**
         * Upon returning from the Auth0 Universal Login, we need to perform a code exchange using the `exchange()` method
         * to complete the authentication flow. This process configures the session for use by the application.
         * If successful, the user will be redirected back to the index route.
         */

        $hasAuthenticated = isset($_GET['state']) && isset($_GET['code']);
        $hasAuthenticationFailure = isset($_GET['error']);

        // The end user will be returned with ?state and ?code values in their request, when successful.
        if($hasAuthenticated) {
            try {
            $this->sdk->exchange();
            } catch (\Throwable $th) {
            printf('Unable to complete authentication: %s', $th->getMessage());
            exit;
            }
        }

        // When authentication was unsuccessful, the end user will be returned with an ?error in their request.
        if($hasAuthenticationFailure) {
            printf('Authentication failure: %s', htmlspecialchars(strip_tags(filter_input(INPUT_GET, 'error'))));
            exit;
        }

        //print_r($this->sdk->getCredentials()); die;

        if(@$_REQUEST)
        session()->set('user_details',(array)$this->sdk->getCredentials());
        // Nothing to do: redirect to index route.
        return redirect()->to(base_url('profile')); 
        
    }

}
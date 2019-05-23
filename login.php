<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

	/**
	 * Login Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://website.com/index.php/login
	 *	- or -  
	 * 		http://example.com/index.php/login/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 */
	 
	 
	 
	function __construct(){
		parent::__construct();
		$this->load->library("form_validation");
		$this->load->library("Mobile_Detect");
		$this->load->model(array('user_model','userlog_model','common_model'));
		
		if($this->session->userdata('user_id')) {
		    redirect(site_url("dashboard"));	
		}
		
	} 
	 
	 
	 
	public function index(){
		
			   if(isset($_COOKIE['remember_me']) && isset($_COOKIE['session_uid'])){
				   
					$profiledata = $this->user_model->getUser_All_Details($_COOKIE['session_uid']);
					$data = array(
						'user_id' => $_COOKIE['session_uid'],
						'user_email' => $profiledata['user_email'],
						'user_fullname' => $profiledata['fullname'],
						'user_credit' => $profiledata['user_credit'],
						'logged_in' => TRUE
					);
				    $this->session->set_userdata($data);
				    redirect("dashboard");
					
			   } else {
			   
		           if(isset($_POST['btnLogin'])){
				   
		            $this->form_validation->set_rules('email', 'Email Address', 'required|trim|valid_email|xss_clean');
			        $this->form_validation->set_rules('password', 'Password', 'required|trim|xss_clean');
			   
			   
					if($this->form_validation->run() == false) {
	
							  $this->data['error'] = validation_errors();
								
							   
					} else {
							 
							 
								$user_email = strtolower($this->input->post('email'));
								$user_pwd = $this->input->post('password');
								
								$login = $this->user_model->login($user_email, $user_pwd); 
								
								if($login == 'Active'){
									
									   ######### Set cookie if Stay logged in ###########
									   if($this->input->post('remember_me') == 'Yes'){
				   
										 setcookie('remember_me', "Yes", time()+3600, "/");
										 setcookie('session_uid', $this->session->userdata('user_id'), time()+3600, "/");
										//setcookie('remember_me', "Yes", time()+315360000, "/");
										
									   }
									   
									   ######################## END ################
									   
									   
									   ################ SET INTO LOG TABLE ############
									   
									    $user_agent = $_SERVER['HTTP_USER_AGENT']; 
										if (preg_match('/MSIE/i', $user_agent)) { $browser = "Internet Explorer";} 
										elseif (preg_match('/Firefox/i', $user_agent)){$browser = "Mozilla Firefox";} 
										elseif (preg_match('/Chrome/i', $user_agent)){$browser = "Google Chrome";} 
										elseif (preg_match('/Safari/i', $user_agent)){$browser = "Safari";} 
										elseif (preg_match('/Opera/i', $user_agent)){$browser = "Opera";}
										elseif (preg_match('/Netscape/i', $user_agent)){$browser = "Netscape";}
										else {$browser = "Other";}
									   
									   $detect = new Mobile_Detect;
								       $deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
									   $last_login_ip_data =  $this->common_model->getLocationInfoByIp();
									   
									   $last_login_data  = array(	
										  'log_user_id'              => $this->session->userdata('user_id'),
										  'login_date'               => date("Y-m-d H:i:s"),
										  'ip_address'               => $last_login_ip_data['ip'],
										  'location'                 => $last_login_ip_data['city'],
										  'country'                  => $last_login_ip_data['country'],
										  'countrycode'              => $last_login_ip_data['country_code'],
										  'browsername'              => $browser,
										  'devicetype'               => ucfirst($deviceType)
									   );
								 
								       $this->userlog_model->insert_userlog($last_login_data);
									   ###################### END #######################
									   redirect("dashboard"); 
				   
									  
								}elseif($login == 'Inactive'){
									
									   $err_textmsg = "Your account is inactive.";
								       $this->data['message'] =  $err_textmsg;
									  
								} else {
								  
									  $err_textmsg = "Your email or password is incorrect.";
								      $this->data['message'] =  $err_textmsg;
									
								}
							 
							 
						 }
			   
			   
		        }
				
			       $this->load->view('users/login', $this->data);
			   
			   }
		   
	}
	
	
	public function verifyuser(){
		
		
			$ajax_result = array();
			
			
				 
					 if($this->form_validation->run() == false) {

						    $ajax_result['login_failed']  = "Your email or password is incorrect.";
							echo json_encode($ajax_result);
							
						   
					 } else {
						 
						 
							$user_email = $this->input->post('email');
							$user_pwd = $this->input->post('password');
							
							$login = $this->user_model->login($user_email, $user_pwd); 
							
							if($login == 'Yes'){
								 
								 $ajax_result['login_sucess']  = "Success Login";
								 echo json_encode($ajax_result);
								 
								  
							} else {
							  
								$ajax_result['login_failed']  = "Your email or password is incorrect.";
								echo json_encode($ajax_result);
								
							}
						 
						 
					 }
	}
	
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/login.php */
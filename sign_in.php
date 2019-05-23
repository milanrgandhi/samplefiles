<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sign_in extends CI_Controller {

	/**
	 * Sign in Page for this controller.
	 *
	 *
	 *
	 */
	 
	 
	function __construct(){
		parent::__construct();
		$this->load->helper(array('form', 'url'));
		$this->load->library('upload');
		$this->load->library("form_validation");
		$this->load->model(array('property_model','user_model','common_model','country_model','email_model'));

	}
	
	
	
	public function index(){
		
		if(!$this->session->userdata('user_id')) {
			
			    
				  if(isset($_POST['btnSubmit'])){
					  
						
								$this->form_validation->set_rules('create_account', 'Create account', 'required|trim|xss_clean');
								
								if($this->input->post('create_account') === 'Yes') {
									
									$this->form_validation->set_rules('firstname', 'Firstname', 'required|trim|xss_clean');
									$this->form_validation->set_rules('lastname', 'Lastname', 'required|trim|xss_clean');
									$this->form_validation->set_rules('phone', 'Phone', 'required|trim|xss_clean');
									$this->form_validation->set_rules('emailid', 'Email Address', 'required|trim|valid_email|xss_clean');
									$this->form_validation->set_rules('pwd', 'Password', 'required|trim|xss_clean');
									$this->form_validation->set_rules('company_name', 'Company', 'trim|xss_clean');
										
								} elseif($this->input->post('create_account') === 'No') {
									$this->form_validation->set_rules('emailid', 'Email Address', 'required|trim|valid_email|xss_clean');
									$this->form_validation->set_rules('pwd', 'Password', 'required|trim|xss_clean');	
									
								}
								 
								
							
								if($this->form_validation->run() == false) {
									
									$this->data['error'] = validation_errors();
									$this->data['title'] = 'Sign In - Stay Square';
									$this->load->view("sign-in/sign-in", $this->data);	
									
									
								} else {
									
									
									if($this->input->post('create_account') === 'No') {
										
												$emailaddress = $this->input->post('emailid');
												$password = $this->common_model->encrypt_decrypt('encrypt', $this->input->post('pwd'));
												$login = $this->user_model->login($emailaddress, $password); 
											
											  
											
																	if($login == 'Active'){
																
																	if($this->session->userdata('referal_page')){
																			
																		if($this->session->userdata('referal_page') == 'services/order'){
																			redirect("services/make-payment");	
																		}elseif($this->session->userdata('referal_page') == 'reservation'){                                                          
																			redirect("reservation");	
																		}else{
																			redirect("member/dashboard");	
																		}
																	}
																	
																	}elseif($login == 'Inactive') {
																
																	
																	$err_textmsg = "Your account is deactivated, please <a href='".site_url('contact')."' target='_blank' style='color:#fff;'>contact</a> Stay Square to activate your account.";
																
																	$this->data['message'] = $err_textmsg;
																	$this->load->view("sign-in/sign-in", $this->data);	
																
																
																}elseif($login == 'Suspended') {
																		
																		$err_textmsg = "Your account is suspended, please <a href='".site_url('contact')."' target='_blank' style='color:#fff;'>contact</a> Stay Square to activate your account.";
									
																		$this->data['message'] = $err_textmsg;
																		$this->load->view("sign-in/sign-in", $this->data);	
																			
															
																} else {
															
																		$err_textmsg = "Either the username or password is wrong. Please try again! <br/>
										If you forgot your password <a href='".site_url('member/forgot-password')."' target='_blank' style='color:#fff'>click here to reset your password</a>";
																		$this->data['message'] = $err_textmsg;
																		$this->load->view("sign-in/sign-in", $this->data);	
																
																}
									 
									 
									 
									 
									 } elseif ($this->input->post('create_account') === 'Yes') {
										 
										 
										
										
											############# Check email address is available or not #############
											$get_email = $this->user_model->check_email($this->input->post('emailid'));
										
											if($get_email) {
											
													
												$pwd = $this->common_model->encrypt_decrypt('encrypt', $this->input->post('pwd'));
												
												$data_user = array(	
												'email_address' 			=> $this->input->post('emailid'),
												'password'    				=> $pwd,
												'firstname' 				=> $this->input->post('firstname'),
												'lastname'    				=> $this->input->post('lastname'),
												'phone'      				=> $this->input->post('phone'),
												'country'    			    => $this->input->post('selcountry'),
												'company_name' 				=> $this->input->post('company_name'),	
												'last_login'    			=> date("Y-m-d H:i:s"),
												'account_status'            => 'Active'
												);
												
												$this->user_model->insert_user($data_user);
												$user_insert_id = $this->user_model->lastinsert_record();
															
												$data = array(
															'user_id'       => $user_insert_id,
															'email_address' => $this->input->post('emailid'),
															'firstname'     => $this->input->post('firstname'),
															'lastname'      => $this->input->post('lastname'),
															'user_type'     => "guest",
															'logged_in'     => TRUE
														   );
												$this->session->set_userdata($data);
															
															
															
												################ Email Send ###################
												$email_name = 'users_signin';
												
												$fname = $this->input->post('firstname');
												$lname = $this->input->post('lastname');
												$email_id = $this->input->post('emailid');
												$pwd = $this->input->post('pwd');
												
												$splVars    = array("{Fname}" => $fname,"{email}" => $email_id, "{password}" => $pwd);
												$splVars_admin    = array("{Fname}" => "Admin","{email}" => $email_id, "{password}" => "*****");
												
												//Send Mail to User
												$this->email_model->sendMail($email_id,'','',$email_name,$splVars);
											
												//Send Mail to Admin
												$this->email_model->sendMail(ADMIN_EMAIL,'','',$email_name,$splVars_admin);
												
												############################### END #############
												
														
												if($this->session->userdata('referal_page')){
													
													if($this->session->userdata('referal_page') == 'services/order'){
														 redirect("services/make-payment");	
													}elseif($this->session->userdata('referal_page') == 'reservation'){
													     redirect("reservation");	
													}else{
													    redirect("member/dashboard");	
													}
													
												}		
													
											
											} else {
											 
												 $this->session->set_flashdata('email_message', 'Email is already available');
												 $this->data['email_message'] = $this->session->flashdata('email_message');
												 $this->load->view("sign-in/sign-in", $this->data);
											 
											}
				
									
									 
									 
									 } else {
										 
										  
											exit;			
										   
										 
									 }
									 
									
									
								}
							
							
						
						
						
				  } else {
					
							$this->data['title'] = 'Sign In';
							$this->load->view("sign-in/sign-in", $this->data);	
				  }
		 
		
		
		
		} else {
		   
		        
				exit;
				
		}
		
		
		
	}
	
	
}

/* End of file sign_in.php */
/* Location: ./application/controllers/sign_in.php */
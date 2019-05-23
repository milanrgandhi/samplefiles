<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class User_Model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}
	
	
	public function insert_user($data)
	{
		$this->db->insert('user', $data);	
		$id = $this->db->insert_id();
		return (isset($id)) ? $id : FALSE;
	}
	
	public function lastinsert_record(){
		
		return $id = $this->db->insert_id();
	}
	
	
	public function getUser($managerid){  
		$this->db->select('firstname');
		$this->db->select('lastname');
		$this->db->select('image');
		$this->db->where('user_id', $managerid);
		$query = $this->db->get('user');
		return $query->row_array();		
	}
	
	public function login($email,$password){
		
		// grab user input
		$email = $this->security->xss_clean($email);
        $password = $this->security->xss_clean($password);
		$this->db->select('*');
		$this->db->where("email_address",$email);
        $this->db->where("password",$password);
           
		// Run the query   
        $query=$this->db->get("user");
		$check_prpty_cnt = "";
		$user_type = "";
		// Let's check if there are any results
        if($query->num_rows == 1){
			
			//If there is a user, then create session data
            $row = $query->row();
			$status = $row->account_status;
			
			############# to check whether user has active property or not ####################
			$check_prpty_cnt = $this->property_model->get_properties_count_byuser($row->user_id);
			
			if($check_prpty_cnt>0){
				 $user_type = "manager";	
			}else{
				 $user_type = "guest";
			}
			
			if($status == 'Active'){
				 $data = array(
                    'user_id'       => $row->user_id,
                    'email_address' => $row->email_address,
                    'firstname'     => $row->firstname,
					'lastname'      => $row->lastname,
					'user_type'     => $user_type,
                    'logged_in'     => TRUE
                    );
            	$this->session->set_userdata($data);
            	return $status;
				
			} else {
			    return $status;	
			}
		
        }
		
		//If the previous process did not validate
        //then return false.
		return false;
    }
	
	function forgotpwd($email,$data){
		
		// grab user input
		$email = $this->security->xss_clean($email);
       
		// Prep the query
		$this->db->where("email_address",$email);
           
		// Run the query   
        $query=$this->db->get("user");
		
		//$this->output->enable_profiler(TRUE);
		
		$new_pwd = end($data);
		$data = array(
					'password' => $data['password']
			     );
	
		// Let's check if there are any results
        if($query->num_rows == 1)
        {
            //If there is a user, then update pwd
            $this->db->where('email_address', $email);
			$this->db->update('user', $data);
            return true;
        } else {
			
		    return false;	
		}
		
		//If the previous process did not validate
        //then return false.
		return false;
    }
	
	
	######## Get the Manager Firstname & Lastname For forgot password Email template ##########
	public function getNamefromEmail($email) {
		
		$this->db->select('firstname');
		$this->db->select('lastname');
		$this->db->where("email_address",$email);
		$query = $this->db->get("user");
		return $query->row_array();
	}
		
			

	public function check_email_availablity()
	{
		$email = trim($this->input->post('email'));
		$email = strtolower($email);    
		$query = $this->db->query('SELECT * FROM user WHERE email_address="'.$email.'"');
		
		if($query->num_rows() > 0)
		return false;
		else
		return true;
	}
	
	
	
	
	
	
	##########################################################################################
	###################### Admin Panel - Manager Listing ##################################
	##########################################################################################
	
	function get_all_managers($limit, $start, $searchword, $account_status) {
		
		
		$sql = "SELECT * ";
		$sql .= " FROM user WHERE 1 ";
		
		
		if($account_status) {
		   $sql .= " AND `account_status` =  '$account_status'";
		}
		
		
		if($searchword) {
		   $sql .= " AND `firstname`  LIKE '%$searchword%'
					 OR  `lastname`  LIKE '%$searchword%'
					 OR  `email_address`  LIKE '%$searchword%'
					 OR   CONCAT(`firstname`, ' ', `lastname`) LIKE '%$searchword%' OR CONCAT(`firstname`, ' ', `lastname`) LIKE '%$searchword%'";
		}
		
		############################ END ##############################################
		$sql .= " LIMIT ".$start.','.$limit;
		$query = $this->db->query($sql); 
		
		return $query->result_array();
		
	}
	
	
	
	function get_all_managerscount($searchword, $account_status) {
		
		$sql = "SELECT * ";
		$sql .= " FROM user WHERE 1 ";
		
		
		if($account_status) {
		   $sql .= " AND `account_status` =  '$account_status'";
		}
		
		
		if($searchword) {
		   $sql .= " AND `firstname`  LIKE '%$searchword%'
					 OR  `lastname`  LIKE '%$searchword%'
					 OR  `email_address`  LIKE '%$searchword%'
					 OR   CONCAT(`firstname`, ' ', `lastname`) LIKE '%$searchword%' OR CONCAT(`firstname`, ' ', `lastname`) LIKE '%$searchword%'";
		}
		
		############################ END ##############################################
		$query = $this->db->query($sql); 
		return $query->num_rows();
		
	}
	
	
	function count_managers(){
        return $this->db->count_all('user');
    }
	
	
	

	
	
	//Delete Profile Image	 
	public function delete_profile_photo($user_id, $image) {
		
		unlink('uploads/property_manager/'.$user_id.'/'.$image);
		unlink('uploads/property_manager/'.$user_id.'/50X50/50X50_'.$image);
		unlink('uploads/property_manager/'.$user_id.'/80X80/80X80_'.$image);
		unlink('uploads/property_manager/'.$user_id.'/100X100/100X100_'.$image);		
		$this->db->set('image', 'NULL', FALSE);		
		$this->db->where('user_id', $user_id);
		$this->db->update('user');
		return true;
	}
	
	public function insert_last_login_data($data)
	{
		$this->db->insert('last_login', $data);	
		
	}
	
	// suspend and unsuspend manager account for admin
	 public function update_manager_account_status($manager_id, $status) {    
			 if($status == "Suspended"){
			     $status = 'Active';
			 } else {
			     $status = 'Suspended';
			 }
			 $this->db->set('account_status', $status);
			 $this->db->where('user_id', $manager_id);
			 $this->db->update('user');
			 return true;
	 }
	 
	
}
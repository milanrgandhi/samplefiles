<?php
/*
    Login for the taxi user type
	Table name : system_taxi_profile_s
	Input/POST parameters:
	- email
	- password 
	- local date
	- local time
	- timezone_hr (User's time zone hours )(for an example 5, 6 1,2 etc..)
	- timezone_min (User's time zone minutes)(for an example 30,15,0 etc..)
	- timezone_details (Entire details)(Asia/Kolkata +05:30)
*/
	
//***************************************************

	require_once('config.php');

	$response = array();
	$records_taxiprofile = array();
	$email = ''; 
	$password = ''; 
	$date = '';
	$time = '';
	$timezone_fulldetails = '';
	$timezone_hrs = '';
	$timezone_mins = '';
	
    
    if(isset($_POST['email'])){ $email = trim($_POST['email']); }
	if(isset($_POST['password'])){ $password = trim($_POST['password']); }
	
	if(isset($_POST['date'])){ $date = trim($_POST['date']); }
	if(isset($_POST['time'])){ $time = trim($_POST['time']); }
	if(isset($_POST['timezone_fulldetails'])){ $timezone_fulldetails = trim($_POST['timezone_fulldetails']); }
	if(isset($_POST['timezone_hrs'])){ $timezone_hrs = trim($_POST['timezone_hrs']); }
	if(isset($_POST['timezone_mins'])){ $timezone_mins = trim($_POST['timezone_mins']); }

	$local_date = $date; 
	$local_time = $time;
	$utc_date = set_utc_date();
	$utc_time = set_utc_time();

    
	if($email == '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $password == '' || $timezone_fulldetails == '' || $date == '' || $time == '')
	//if($email == '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $password == '')
	{
		$statusCode = 200;
		setHttpHeaders($statusCode);
		$response['success'] = false;
	    $response['message'] = 'Input parameter missing or invalid.';
	}
	else
	{

        $bindval     = array(':loginemail'=>$email,':isactive'=>'y');
        $login_qry   = "SELECT id, first_name, last_name, email, password, picture, login_attempt, status FROM system_taxi_profile_s WHERE email=:loginemail AND active =:isactive";
        $recordCount = $db->getRecord_Count($login_qry,$bindval);

        if($recordCount == 1){

            $login_user_data = $db->getSingle_Record($login_qry,$bindval);   
                     
            if(password_verify($password, $login_user_data['password'])){

					$user_lastlogindata = array(
							'last_login_date'     => $local_date,
							'last_login_time'     => $local_time,
							'last_login_utc_date' => $utc_date,
							'last_login_utc_time' => $utc_time,
							'login_attempt'       => 0,
							'timezone_hour'       => $timezone_hrs,
							'timezone_minute'     => $timezone_mins,
							'timezone_details'    => $timezone_fulldetails
					);

					$condition = array('where' => "id ='".$login_user_data['id']."'");
					$update = $db->update('system_taxi_profile_s', $user_lastlogindata,$condition); 

					if($update === 'SQL Error'){	

							$statusCode = 500;
							setHttpHeaders($statusCode);
							$response['success'] = false;
							$response['message'] = 'Database or SQL query error';
							

					}else if($update === true){

							if(!empty($login_user_data['picture'])){
								 $profilepic_path = SITE_URL."uploads/taxi/".$login_user_data['id']."/".$login_user_data['picture'];
							} else {
								 $profilepic_path = '';
							}
							
							$records_taxiprofile['taxi_id']            = $login_user_data['id'];
							$records_taxiprofile['email']              = $login_user_data['email'];
							$records_taxiprofile['first_name']         = $login_user_data['first_name'];
							$records_taxiprofile['last_name']          = $login_user_data['last_name'];
							$records_taxiprofile['full_name']          = $login_user_data['first_name']." ".$login_user_data['last_name'];
							$records_taxiprofile['status']             = $login_user_data['status'];
							$records_taxiprofile['profile_pic']        = $login_user_data['picture'];
							$records_taxiprofile['profilepic_path']    = $profilepic_path;
							
							$statusCode = 200;
							setHttpHeaders($statusCode);
							$response['success'] = true;
							$response['message'] = 'Logged in successfully.';
							$response['data'] = $records_taxiprofile;

					} else {

							$statusCode = 500;
							setHttpHeaders($statusCode);
							$response['success'] = false;
							$response['message'] = 'Failed to update';
							

					}


            } else {


                    if($login_user_data['login_attempt'] > 10){

								$blocked_reason = 'Acccount locked. Too many login attempts. Request for a new password.';

								$userdata = array(
									'active'                          => 'n',
									'status'                          => BLOCKED_STATUS,
									'profile_blocked'                 => 'y',
									'profile_blocked_by'              => $login_user_data['id'],
									'profile_blocked_reason'          => $blocked_reason,
									'profile_blocked_date'            => $local_date,
									'profile_blocked_time'            => $local_time,
								);
			
								$condition = array('where' => "id ='".$login_user_data['id']."'");
								$update = $db->update('system_taxi_profile_s',$userdata,$condition); 

								if($update === 'SQL Error'){	

									$statusCode = 500;
									setHttpHeaders($statusCode);
									$response['success'] = false;
									$response['message'] = 'Database or SQL query error';
									
									

								}else if($update === true){

									$statusCode = 200;
									setHttpHeaders($statusCode);
									$response['success'] = false;
									$response['message'] = 'Acccount locked. Too many login attempts. Request for a new password.';
									

								}else {

									$statusCode = 500;
									setHttpHeaders($statusCode);
									$response['success'] = false;
									$response['message'] = 'Failed to update';
									;
								}	


					} else {

						        ################# Update value for failed login #######
								$userdata = array(
									'login_attempt' => $login_user_data['login_attempt']+1
								);

								$condition = array('where' => "id ='".$login_user_data['id']."'");
								$update = $db->update('system_taxi_profile_s',$userdata,$condition); 
								################# End ###############################
								
								if($update === 'SQL Error'){	

									$statusCode = 500;
									setHttpHeaders($statusCode);
									$response['success'] = false;
									$response['message'] = 'Database or SQL query error';
									
									

								}else if($update === true){

									$statusCode = 200;
									setHttpHeaders($statusCode);
									$response['success'] = false;
									$response['message'] = 'Invalid email or password';
									

								}else {

									$statusCode = 500;
									setHttpHeaders($statusCode);
									$response['success'] = false;
									$response['message'] = 'Failed to update';
									
								}	


					}
					
				
            }


        } else {

            $statusCode = 200;
			setHttpHeaders($statusCode);
			$response['success'] = false;
			$response['message'] = 'Invalid email or password';
			

        }


    } 
	
	echo json_encode($response);
	
?>
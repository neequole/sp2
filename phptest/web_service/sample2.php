<?php
	include("class/user_stud.php");
	include("class/user_admin.php");
	include("class/booking.php");
	include("../include/config.php");
?>

<?php
	$method = $_SERVER['REQUEST_METHOD'];
	$uri = $_SERVER['REQUEST_URI'];
	
	//check if METHOD exists
	if(isset($method) && $method!="" && ($method=="POST" || $method == "GET" || $method == "PUT")){
		
		//check URI exists
		$exists = remoteFileExists("http://localhost" . $uri);
		if ($exists) {
				//check if class exists
				$resource = explode("/", $uri);		//@return [0] => "" [1] => app folder [2] => web service subfolder [3] => file loc [4] => object class
				if(!isset($resource[4]) || $resource[4] == "")
					echo "Error in CLASS";
				else{
					if (class_exists($resource[4])) {
						if($resource[4] == "UserStudent"){
							if(isset($resource[5]) && $resource[5] != ""){	//[5] => User Id || username
								$results = array();
								if($method == "GET"){
									if(is_numeric($resource[5])) $results['student'] = UserStudent::getUserById($resource[5]); //if given is id
									else $results['student'] = UserStudent::getUserByUsername($resource[5]);		//if given is username
								}
								else if($method == "PUT"){
									if(is_numeric($resource[5])) $results['student'] = UserStudent::changeStatusById($resource[5]); //if given is id
									else $results['student'] = UserStudent::changeStatusByName($resource[5]);		//if given is username
								}
								$row = $results['student'];
								if($row)
									echo json_encode($row);
								else
									echo json_encode(array('error'=>'true', 'error_message'=>'Student not found.'));
							}
							else echo json_encode(array('error'=>'true', 'error_message'=>'Not enough information were given.'));
						}
						else if($resource[4] == "UserAdmin"){	//useradmin
							if(isset($resource[5]) && $resource[5] != ""){	//[5] => User Id || username
									$results = array();
									if($method == "GET"){
										if(is_numeric($resource[5])) $results['admin'] = UserAdmin::getUserById($resource[5]); //if given is id
										else $results['admin'] = UserAdmin::getUserByUsername($resource[5]);		//if given is username
									}
									$row = $results['admin'];
									if($row)
										echo json_encode($row);
									else
										echo json_encode(array('error'=>'true', 'error_message'=>'Adminstrator not found.'));
							}
							else echo json_encode(array('error'=>'true', 'error_message'=>'Not enough information were given.'));
						}
						else if($resource[4] == "Booking"){	//booking
							if(isset($resource[5]) && $resource[5] != ""){	//[5] => User Id || username
										$results = array();
										if($method == "GET"){
											if(is_numeric($resource[5])) $results['booking'] = Booking::getBookingByUserId($resource[5]); //if given is id
											else $results['booking'] = Booking::getBookingByUsername($resource[5]);		//if given is username
										}
										$row = $results['booking'];
										if($row)
											echo json_encode($row);
										else
											echo json_encode(array('error'=>'true', 'error_message'=>'Error in fetching student bookings.'));
								}
							else echo json_encode(array('error'=>'true', 'error_message'=>'Not enough information were given.'));
						}
					}
					else echo json_encode(array('error'=>'true', 'error_message'=>'Class does not exists.'));
				}
		} else {
			echo json_encode(array('error'=>'true', 'error_message'=>'Error in URI'));   
		}
	}

	else echo json_encode(array('error'=>'true', 'error_message'=>'Error in REQUEST METHOD'));

	
//function for checking uri	
function remoteFileExists($url) {
    $curl = curl_init($url);

    //don't fetch the actual page, you only want to check the connection is ok
    curl_setopt($curl, CURLOPT_NOBODY, true);

    //do request
    $result = curl_exec($curl);

    $ret = false;

    //if request did not fail
    if ($result !== false) {
        //if request was ok, check response code
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);  

        if ($statusCode == 200) {
            $ret = true;   
        }
    }

    curl_close($curl);

    return $ret;
}
?>

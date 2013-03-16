<?php
	include("class/user_stud.php");
	include("../include/config.php");
?>

<?php
	$method = $_SERVER['REQUEST_METHOD'];
	$uri = $_SERVER['REQUEST_URI'];
	
	//check if METHOD exists
	if(isset($method) && $method!="" && ($method=="POST" || $method == "GET")){
		
		//check URI exists
		$exists = remoteFileExists("http://localhost" . $uri);
		if ($exists) {
				//check if class exists
				$resource = explode("/", $uri);		//@return [0] => "" [1] => app folder [2] => web service subfolder [3] => file loc [4] => object class
				if(!isset($resource[4]) || $resource[4] == "")
					echo "Error in CLASS";
				else{
					if (class_exists($resource[4])) {
						if(isset($resource[5]) && $resource[5] != ""){
						$results = array();
						if(is_numeric($resource[5])) $results['student'] = UserStudent::getUserById($resource[5]); //if given is id
						else $results['student'] = UserStudent::getUserByUsername($resource[5]);		//if given is username
						$row = $results['student'];
						echo json_encode($row);
						}
						else echo "Error in USER ID";
					}
					else echo "CLASS does not exists.";
					
				//if class exists, get parameters, use class methods
	
				//return json
				}
		} else {
			echo "Error in URI";   
		}
	}

	else "Error in REQUEST METHOD";

	
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

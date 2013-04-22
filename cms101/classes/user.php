<?php
// Include database connection settings
/**
 * Class to handle User
*/
 
class User
{
 
// Properties

//id of user
  public $id = null;
 
//first name
  public $fname = null;
 
//middle name
  public $mname = null;
 
//last name
  public $lname = null;
 
//email used as username
  public $email = null;
  
//password
	public $pwd = null;

//user role
	public $role = null;
//registration date
	public $regDate = null;

//captcha
	public $captcha = null;
//confirmation date
	public $cDate = null;	 
  /**
  * Sets the object's properties using the values in the supplied array
  *
  * @param assoc The property values
  */
 //CONSTRUCTOR for class user
  public function __construct( $data=array() ) {
    if ( isset( $data['usr_id'] ) )$this->id = $data['usr_id'];
    if ( isset( $data['usr_fname'] ) ) $this->fname = ucwords(strtolower($data['usr_fname']));
    if ( isset( $data['usr_mname'] ) ) $this->mname = ucwords(strtolower($data['usr_mname']));
	if ( isset( $data['usr_lname'] ) ) $this->lname = ucwords(strtolower($data['usr_lname']));
	if ( isset( $data['usr_email'] ) ) $this->email = $data['usr_email'];
    if ( isset( $data['usr_pwd'] ) ) $this->pwd = $data['usr_pwd'];
	if ( isset( $data['usr_role'] ) ) $this->role = $data['usr_role'];
	if ( isset( $data['usr_regDate'] ) ) $this->regDate = $data['usr_regDate'];
	if ( isset( $data['captcha'] ) ) $this->captcha = $data['captcha'];
	if ( isset( $data['usr_cDate'] ) ) $this->cDate = $data['usr_cDate'];
  }
 
 
  /**
  * Sets the object's properties
  * @param assoc The form post values
  */
 
  public function storeFormValues ( $params ) {		
	
    // Store all the parameters
    $this->__construct( $params );

  }
 
   //functions
  public function checkAlpha($str) {
		return preg_match("/^[A-Z][a-zA-Z -]+$/",$str);
	}
 
  /**
  * Returns a Blog object matching the given article ID
  *
  * @param int The blog ID
  * @return Blog|false The blog object, or false if the record was not found or there was a problem
  */
 
  public static function getUserById( $id ) {
  		$sql = "SELECT * FROM user WHERE usr_id = $id";
		$result = mysql_query($sql) or die(mysql_error());
    	$row = mysql_fetch_array($result);
   		if ( $row ) return new User( $row );
  }
 
   /**
  * Returns a Blog object matching the given article ID
  *
  * @param int The user email and password
  * @return Blog|false The blog object, or false if the record was not found or there was a problem
  */
 
  public static function findUser( $log_email, $log_pwd ) {
  		$result = mysql_query("SELECT * FROM user where usr_email='".$log_email."' and usr_pwd='".$log_pwd."'") or die(mysql_error());
   		$row = mysql_fetch_array($result);
   		if ( $row ) return new User( $row );
		
  }
   
  /**
  * Returns all (or a range of) Blog objects in the DB
  *
  * @param int Optional The number of rows to return (default=all)
  * @param string Optional column by which to order the blog (default="publicationDate DESC")
  * @return Array|false A two-element array : results => array, a list of Article objects; totalRows => Total number of articles
  */
 
  public static function getList( $start_from,$numRows, $order="publicationDate DESC" ) {

  }
 
 
  /**
  * Inserts the current Blog object into the database, and sets its ID property.
  */
 
  public function insertUser() {
 			//check constraints
			//check if e-mail is valid
			//1 = blank email, 2 = invalid email, 3 = blank password, 4 = email already exists, 5 = name contains non-alpha char, 6 = password must be min 6, 7 = name not complete, 8 captcha error
			//checks if email is valid/not blank
			if(!empty($this->captcha) && $this->captcha!=''){
				if ($this->captcha != $_SESSION['vercode'])
						$error[8]="1";
			}
			else $error[8]="1";
			if(!empty($this->email)){
				if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) $error[2]="1";
			}
			else $error[1]="1";
			//checks if e-mail already exists/not blank
			if(!empty($this->pwd)){
				$info1 = mysql_query("SELECT * FROM user where usr_email='".$this->email."'");
				if(mysql_num_rows($info1)!=0) $error[4]="1";
				if(strlen($this->pwd)<6) $error[6]="1";
			}
			else $error[3]="1";
			if(empty($this->fname) || empty($this->mname) || empty($this->lname)) $error[7] = "1";
			if($this->checkAlpha($this->fname)==false || $this->checkAlpha($this->mname)==false || $this->checkAlpha($this->lname)==false ) $error[5] = "1";
			
			if(!empty($error)){
				echo "<table style='border: 1px solid red; margin: 0 auto;'>";
				if(isset($error[8]) && $error[8] == "1") echo "<tr><td style='color:red;' class='smallText'>Please re-enter your verification code.</td></tr>";
        		if(isset($error[1]) && $error[1] == "1") echo "<tr><td style='color:red;' class='smallText'>E-mail cannot be blank.</td></tr>";
				if(isset($error[2]) && $error[2] == "1") echo "<tr><td style='color:red;' class='smallText'>Invalid E-mail.</td></tr>";
				if(isset($error[3]) && $error[3] == "1") echo "<tr><td style='color:red;' class='smallText'>Password cannot be blank.</td></tr>";
				if(isset($error[6]) && $error[6] == "1") echo "<tr><td style='color:red;' class='smallText'>Password must be minimum of 6 characters.</td></tr>";
				if(isset($error[4]) && $error[4] == "1") echo "<tr><td style='color:red;' class='smallText'>E-mail already exist.</td></tr>";
				if(isset($error[5]) && $error[5] == "1") echo "<tr><td style='color:red;' class='smallText'>Full Name must contain letters, dashes and spaces only and must start with upper case letter.</td></tr>";
				if(isset($error[7]) && $error[7] == "1") echo "<tr><td style='color:red;' class='smallText'>Name must be full.</td></tr>";
				echo "</table>";
			}
			else{
				$sql="INSERT INTO user (usr_id, usr_cDate ,usr_email, usr_pwd, usr_fname, usr_mname, usr_lname, usr_role,usr_regDate) VALUES('',null,'$this->email','$this->pwd','$this->fname','$this->mname','$this->lname','typical',now())";
			
				if (!mysql_query($sql))
				  {
				  die('Error: ' . mysql_error());
				  }
				echo "<p class='smallText' style='color:red;'>Your account is successfully created.</p>";
			}
}
 
 
  /**
  * Updates the current Blog object in the database.
  */
 
  public function confirmUser($id) {
    $sql = "UPDATE user set usr_cDate=now() where usr_id=$id";
	if (!mysql_query($sql))
	{
		die('Error: ' . mysql_error());
	}
	echo "<p>User confirmed.</p>";
  }
 
 
  /**
  * Deletes the current Blog object from the database.
  */
 
  public function deleteUser($id) {
		$sql = "DELETE FROM user WHERE usr_id = $id LIMIT 1";
		if (!mysql_query($sql))
		{
			die('Error: ' . mysql_error());
		}
		if(isset($_SESSION['id']) && $_SESSION['id'] == $_POST['usr_id']) echo "1";
		else echo "<p class='smallText'>User is successfully removed.</p>";
  }
  
  public function editUser(){
  	$sql = "UPDATE user set usr_email='$this->email', usr_pwd='$this->pwd', usr_fname='$this->fname', usr_mname='$this->mname', usr_lname = '$this->lname', usr_role = '$this->role' where usr_id=$this->id";
	if (!mysql_query($sql))
	{
		die('Error: ' . mysql_error());
	}
	echo "<p>User edited.</p>";
  }
  

  public function addUser() {
 			//check constraints
			//check if e-mail is valid
			//1 = blank email, 2 = invalid email, 3 = blank password, 4 = email already exists, 5 = name contains non-alpha char, 6 = password must be min 6, 7 = name not complete
			if(!empty($this->email)){
				if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) $error[2]="1";
			}
			else $error[1]="1";
			//checks if e-mail already exists/not blank
			if(!empty($this->pwd)){
				$info1 = mysql_query("SELECT * FROM user where usr_email='".$this->email."'");
				if(mysql_num_rows($info1)!=0) $error[4]="1";
				if(strlen($this->pwd)<6) $error[6]="1";
			}
			else $error[3]="1";
			if(empty($this->fname) || empty($this->mname) || empty($this->lname)) $error[7] = "1";
			if($this->checkAlpha($this->fname)==false || $this->checkAlpha($this->mname)==false || $this->checkAlpha($this->lname)==false ) $error[5] = "1";
			
			if(!empty($error)){
				echo "<table style='border: 1px solid red; margin: 0 auto;'>";
        		if(isset($error[1]) && $error[1] == "1") echo "<tr><td style='color:red;' class='smallText'>E-mail cannot be blank.</td></tr>";
				if(isset($error[2]) && $error[2] == "1") echo "<tr><td style='color:red;' class='smallText'>Invalid E-mail.</td></tr>";
				if(isset($error[3]) && $error[3] == "1") echo "<tr><td style='color:red;' class='smallText'>Password cannot be blank.</td></tr>";
				if(isset($error[6]) && $error[6] == "1") echo "<tr><td style='color:red;' class='smallText'>Password must be minimum of 6 characters.</td></tr>";
				if(isset($error[4]) && $error[4] == "1") echo "<tr><td style='color:red;' class='smallText'>E-mail already exist.</td></tr>";
				if(isset($error[5]) && $error[5] == "1") echo "<tr><td style='color:red;' class='smallText'>Full Name must contain letters, dashes and spaces only and must start with upper case letter.</td></tr>";
				if(isset($error[7]) && $error[7] == "1") echo "<tr><td style='color:red;' class='smallText'>Name must be full.</td></tr>";
				echo "</table>";
			}
			else{
				$sql="INSERT INTO user (usr_id, usr_cDate ,usr_email, usr_pwd, usr_fname, usr_mname, usr_lname, usr_role,usr_regDate) VALUES('',now(),'$this->email','$this->pwd','$this->fname','$this->mname','$this->lname','$this->role',now())";
			
				if (!mysql_query($sql))
				  {
				  die('Error: ' . mysql_error());
				  }
				echo "<p class='smallText' style='color:red;'>Your account is successfully created.</p>";
			}
}
 
}
 
?>

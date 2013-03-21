<?php
// Include database connection settings
/**
 * Class to handle User
*/
 
class UserStudent
{
 
// Properties of user_student
  public $id = null;
  public $usrname = null;
  public $pwd = null;
  public $fname = null;
  public $mname = null;
  public $lname = null;
  public $suffix = null;
  public $sex = null;
  public $cnum = null;
  public $email = null;
  public $type = null;
  public $stud_no = null;
  public $college = null;
  public $course = null;
  public $stud_status = null;
  
  /**
  * Sets the object's properties using the values in the supplied array
  *
  * @param assoc The property values
  */
 //CONSTRUCTOR for class user
  public function __construct( $data=array() ) {
    if ( isset( $data['id'] ) )$this->id = $data['id'];
    if ( isset( $data['usrname'] ) ) $this->usrname = $data['usrname'];
    if ( isset( $data['pwd'] ) ) $this->pwd = $data['pwd'];
	if ( isset( $data['fname'] ) ) $this->fname = ucwords(strtolower($data['fname']));
	if ( isset( $data['mname'] ) ) $this->mname = ucwords(strtolower($data['mname']));
    if ( isset( $data['lname'] ) )$this->lname = ucwords(strtolower($data['lname']));
	if ( isset( $data['suffix'] ) ) $this->suffix = $data['suffix'];
	if ( isset( $data['sex'] ) ) $this->sex = $data['sex'];
	if ( isset( $data['cnum'] ) ) $this->cnum = $data['cnum'];
	if ( isset( $data['email'] ) ) $this->email = $data['email'];
	if ( isset( $data['type'] ) ) $this->type = $data['type'];
	if ( isset( $data['stud_no'] ) ) $this->stud_no = $data['stud_no'];
	if ( isset( $data['college'] ) ) $this->college = $data['college'];
	if ( isset( $data['course'] ) ) $this->course = $data['college'];
	if ( isset( $data['stud_status'] ) ) $this->stud_status = $data['stud_status'];
  }
 
 
  /**
  * Sets the object's properties
  * @param assoc The form post values
  */
 
  public function storeFormValues ( $params ) {		
	
    // Store all the parameters
    $this->__construct( $params );

  }
  
   public static function getUserById($id) {
  		$sql = "SELECT * FROM user u INNER JOIN user_stud s ON u.id = s.id where u.id=".$id;
		$result = mysql_query($sql) or die(mysql_error());
    	$row = mysql_fetch_array($result);
   		if ( $row ) return new UserStudent( $row );
  }
  
  public static function getUserByUsername($usrname) {
  		$sql = "SELECT * FROM user u INNER JOIN user_stud s ON u.id = s.id where u.usrname='".$usrname."'";
		$result = mysql_query($sql) or die(mysql_error());
    	$row = mysql_fetch_array($result);
   		if ( $row ) return new UserStudent( $row );
  }
  
  public static function changeStatusById($id) {
  		$sql = "UPDATE user_stud SET stud_status='activated' where id=".$id;
		$result = mysql_query($sql) or die(mysql_error());
		$sql = "SELECT * FROM user u INNER JOIN user_stud s ON u.id = s.id where u.id=".$id;	//return updated
		$result = mysql_query($sql) or die(mysql_error());
    	$row = mysql_fetch_array($result);
   		if ( $row ) return new UserStudent( $row );
  }
  
  public static function changeStatusByName($usrname) {
  		$sql = "UPDATE user_stud SET stud_status='activated' where id=".$usrname;
		$result = mysql_query($sql) or die(mysql_error());
		$sql = "SELECT * FROM user u INNER JOIN user_stud s ON u.id = s.id where u.usrname='".$usrname."'";
		$result = mysql_query($sql) or die(mysql_error());
    	$row = mysql_fetch_array($result);
   		if ( $row ) return new UserStudent( $row );
  }
  
}
 
?>

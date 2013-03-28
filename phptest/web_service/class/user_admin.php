<?php
// Include database connection settings
/**
 * Class to handle User
*/
 
class UserAdmin
{
 
// Properties of user_admin
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
  		$sql = "SELECT * FROM user where id=".$id." and type='admin'";
		$result = mysql_query($sql) or die(mysql_error());
    	$row = mysql_fetch_array($result);
   		if ( $row ) return new UserAdmin( $row );
  }
  
  public static function getUserByUsername($usrname) {
  		$sql = "SELECT * FROM user where usrname='".$usrname."' and type='admin'";
		$result = mysql_query($sql) or die(mysql_error());
    	$row = mysql_fetch_array($result);
   		if ( $row ) return new UserAdmin( $row );
  }
  
}
 
?>

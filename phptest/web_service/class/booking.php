<?php
// Include database connection settings
/**
 * Class to handle User
*/
 
class Booking
{
 
// Properties of booking
	public $book_id = null;
	public $title = null;
	public $e_date = null;
	public $e_stime = null;
	public $e_etime = null;
	public $venue_name = null;
	public $e_tclass = null;
	public $status = null;
	public $fname = null;
    public $mname = null;
    public $lname = null;
	public $stud_no = null;
  
  /**
  * Sets the object's properties using the values in the supplied array
  *
  * @param assoc The property values
  */
 //CONSTRUCTOR for class booking
  public function __construct( $data=array() ) {
    if ( isset( $data['book_id'] ) )$this->book_id = $data['book_id'];
    if ( isset( $data['title'] ) ) $this->title = $data['title'];
	if ( isset( $data['e_date'] ) ) $this->e_date = $data['e_date'];
	if ( isset( $data['e_stime'] ) ) $this->e_stime = $data['e_stime'];
	if ( isset( $data['e_etime'] ) ) $this->e_etime = $data['e_etime'];
	if ( isset( $data['venue_name'] ) ) $this->venue_name = $data['venue_name'];
	if ( isset( $data['e_tclass'] ) ) $this->e_tclass = $data['e_tclass'];
	if ( isset( $data['status'] ) ) $this->status = $data['status'];
	if ( isset( $data['fname'] ) ) $this->fname = ucwords(strtolower($data['fname']));
	if ( isset( $data['mname'] ) ) $this->mname = ucwords(strtolower($data['mname']));
    if ( isset( $data['lname'] ) )$this->lname = ucwords(strtolower($data['lname']));
	if ( isset( $data['stud_no'] ) ) $this->stud_no = $data['stud_no'];	
	
  }
 
 
  /**
  * Sets the object's properties
  * @param assoc The form post values
  */
 
  public function storeFormValues ( $params ) {		
	
    // Store all the parameters
    $this->__construct( $params );

  }
  
  public static function getBookingById($id) {
  		$sql = "SELECT * FROM booking b INNER JOIN e_sched e ON b.e_sched_id=e.e_sched_id INNER JOIN event v ON e.e_id = v.id INNER JOIN venue z ON v.venue = z.venue_id where book_id=".$id;
		$result = mysql_query($sql) or die(mysql_error());
		$row = mysql_fetch_array($result);
   		if ( $row ) return new Booking($row);
  }
  
   public static function getBookingByUserId($id) {
  		$sql = "SELECT * FROM booking b INNER JOIN e_sched e ON b.e_sched_id=e.e_sched_id INNER JOIN event v ON e.e_id = v.id INNER JOIN venue z ON v.venue = z.venue_id where user_id=".$id;
		$result = mysql_query($sql) or die(mysql_error());
		$stack = array();
		while( $row = mysql_fetch_array($result)) {
			array_push($stack, new Booking( $row ));
		}
   		if ( $stack ) return $stack;
  }
  
  public static function getBookingByEventSchedId($id) {
  		$sql = "SELECT * FROM event e INNER JOIN e_sched s ON e.id=s.e_id INNER JOIN booking b ON s.e_sched_id = b.e_sched_id INNER JOIN user u on b.user_id=u.id INNER JOIN venue z ON e.venue = z.venue_id INNER JOIN user_stud h on u.id=h.id where s.e_sched_id=".$id;
		$result = mysql_query($sql) or die(mysql_error());
		$stack = array();
		while( $row = mysql_fetch_array($result)) {
			array_push($stack, new Booking( $row ));
		}
   		if ( $stack ) return $stack;
  }
  
  public static function getBookingByBookSched($bookId, $schedId){
	$sql = "SELECT * FROM booking where book_id=".$bookId." and e_sched_id=".$schedId." and status='activated'";
	$result = mysql_query($sql) or die(mysql_error());
	$row = mysql_fetch_array($result);
	if ( $row ) return new Booking($row);
  }
  
    public static function getBookingByUserIdPending($id) {
  		$sql = "SELECT * FROM booking b INNER JOIN e_sched e ON b.e_sched_id=e.e_sched_id INNER JOIN event v ON e.e_id = v.id INNER JOIN venue z ON v.venue = z.venue_id where user_id=".$id." and status='pending'";
		$result = mysql_query($sql) or die(mysql_error());
		$stack = array();
		while( $row = mysql_fetch_array($result)) {
			array_push($stack, new Booking( $row ));
		}
   		if ( $stack ) return $stack;
  }
  
  public static function getBookingByUsername($usrname) {

  }
  
  public static function changeStatusById($id){
		$sql = "UPDATE booking SET status='activated' where book_id=".$id;
		$result = mysql_query($sql) or die(mysql_error());
		$sql = "SELECT * FROM booking b INNER JOIN e_sched e ON b.e_sched_id=e.e_sched_id INNER JOIN event v ON e.e_id = v.id INNER JOIN venue z ON v.venue = z.venue_id where book_id=".$id;
		$result = mysql_query($sql) or die(mysql_error());
    	$row = mysql_fetch_array($result);
   		if ( $row ) return new Booking( $row );
  }
    
}
 
?>

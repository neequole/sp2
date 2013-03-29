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
	public $status = null;
  
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
	if ( isset( $data['status'] ) ) $this->status = $data['status'];	
  }
 
 
  /**
  * Sets the object's properties
  * @param assoc The form post values
  */
 
  public function storeFormValues ( $params ) {		
	
    // Store all the parameters
    $this->__construct( $params );

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

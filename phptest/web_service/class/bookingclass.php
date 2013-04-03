<?php
// Include database connection settings
/**
 * Class to handle User
*/
 
class BookingClass
{
 
// Properties of booking
	public $id = null;
	public $booking_id = null;
	public $class_id = null;
	public $timein = null;
	public $timeout = null;
	
  /**
  * Sets the object's properties using the values in the supplied array
  *
  * @param assoc The property values
  */
 //CONSTRUCTOR for class booking
  public function __construct( $data=array() ) {
    if ( isset( $data['id'] ) )$this->id = $data['id'];
    if ( isset( $data['booking_id'] ) ) $this->booking_id = $data['booking_id'];
	if ( isset( $data['class_id'] ) ) $this->class_id = $data['class_id'];
	if ( isset( $data['timein'] ) ) $this->timein = $data['timein'];
	if ( isset( $data['timeout'] ) ) $this->timeout = $data['timeout'];	
  }
 
 
  /**
  * Sets the object's properties
  * @param assoc The form post values
  */
 
  public function storeFormValues ( $params ) {		
	
    // Store all the parameters
    $this->__construct( $params );

  }
  
  public static function updateEntry($id,$date){
		$sql = "UPDATE booking_class SET timein='".$date."' where booking_id=".$id;
		$result = mysql_query($sql) or die(mysql_error());
		$sql = "SELECT * FROM booking_class where booking_id=".$id;
		$result = mysql_query($sql) or die(mysql_error());
		$row = mysql_fetch_array($result);
   		if ( $row ) return new BookingClass($row);
  }
  
  public static function updateExit($id,$date){
		mysql_query("START TRANSACTION");
		$string1 = "UPDATE booking SET status='done' where book_id=".$id." and status='activated'";
		$qry1 =  mysql_query($string1) or die(mysql_error());
		$string2 = "UPDATE booking_class SET timeout='".$date."' where booking_id=".$id;
		$qry2 =  mysql_query($string2) or die(mysql_error());
		if ($qry1 and $qry2) {
			$sql = "SELECT * FROM booking_class where booking_id=".$id;
			$result = mysql_query($sql) or die(mysql_error());
			$row = mysql_fetch_array($result);
			mysql_query("COMMIT");
		}
		else mysql_query("ROLLBACK");
		if ( $row ) return new BookingClass($row);
  }
    
}
 
?>

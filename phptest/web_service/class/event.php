<?php
// Include database connection settings
/**
 * Class to handle User
*/
 
class Event
{
 
// Properties
public $id = null;
public $title = null;
public $venue_name = null;
public $e_date = null;
public $e_stime = null;
public $e_etime = null;
public $e_sched_id = null;
  /**
  * Sets the object's properties using the values in the supplied array
  *
  * @param assoc The property values
  */
 //CONSTRUCTOR for class user
  public function __construct( $data=array() ) {
    if ( isset( $data['id'] ) )$this->id = $data['id'];
    if ( isset( $data['title'] ) ) $this->title = $data['title'];
	if ( isset( $data['venue_name'] ) ) $this->venue_name = $data['venue_name'];
	if ( isset( $data['e_date'] ) ) $this->e_date = $data['e_date'];
	if ( isset( $data['e_stime'] ) ) $this->e_stime = $data['e_stime'];
	if ( isset( $data['e_etime'] ) ) $this->e_etime = $data['e_etime'];
	if ( isset( $data['e_sched_id'] ) ) $this->e_sched_id = $data['e_sched_id'];
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
  public static function getAllEvent() {
  		$sql = "SELECT * FROM event e INNER JOIN e_sched s ON e.id=s.e_id INNER JOIN venue v on e.venue=v.venue_id";
		$result = mysql_query($sql) or die(mysql_error());
		$stack = array();
		while( $row = mysql_fetch_array($result)) {
			array_push($stack, new Event( $row ));
		}
   		if ( $stack ) return $stack;
  }
 
}
 
?>

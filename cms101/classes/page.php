<?php
// Include database connection settings
/**
 * Class to handle blogs
*/
 
class Page
{
 
// Properties

//id of page
  public $page_id = null;
 
//page title serve as key
  public $page_title = null;
 
//page body content
  public $page_body = null;
 
//page sidebar content
  public $page_sidebar = null;
 
//page status: published or draft
  public $page_status = null;
//record of last time updated
	public $page_lastupdate = null;
//page protection: 0- public 1-for admin	
	public $page_protection = null;
//parent : keep track if subchild	
	public $page_parentid = null;
//the web title
 	public $page_headtitle = null;
  /**
  * Sets the object's properties using the values in the supplied array
  *
  * @param assoc The property values
  */
 //CONSTRUCTOR for class blog
  public function __construct( $data=array() ) {
    if ( isset( $data['page_id'] ) )$this->page_id = $data['page_id'];
    if ( isset( $data['page_title'] ) ) $this->page_title = $data['page_title'];
    if ( isset( $data['page_body'] ) ) $this->page_body = $data['page_body'];
    if ( isset( $data['page_sidebar'] ) ) $this->page_sidebar = $data['page_sidebar'];
    if ( isset( $data['page_status'] ) ) $this->page_status = $data['page_status'];
	if ( isset( $data['page_lastupdate'] ) ) $this->page_lastupdate = $data['page_lastupdate'];
	if ( isset( $data['page_protection'] ) ) $this->page_protection = $data['page_protection'];
	if ( isset( $data['page_parentid'] ) ) $this->page_parentid = $data['page_parentid'];
	if ( isset( $data['page_headtitle'] ) ) $this->page_headtitle = $data['page_headtitle'];
  }
 
 
  /**
  * Sets the object's properties using the edit form post values in the supplied array
  *
  * @param assoc The form post values
  */
 
  public function storeFormValues ( $params ) {
 
    // Store all the parameters
    $this->__construct( $params );

  }
 
 
  /**
  * Returns a Blog object matching the given article ID
  *
  * @param int The blog ID
  * @return Blog|false The blog object, or false if the record was not found or there was a problem
  */
 
  public static function getPageById( $id ) {
	$sql = "SELECT * FROM page WHERE page_id = '".$id."'";
	$result = mysql_query($sql) or die(mysql_error());
    $row = mysql_fetch_array($result);
    if ( $row ) return new Page( $row );
  }
  
  public static function getPageByTitle( $title ) {
	$sql = "SELECT * FROM page WHERE page_title = '".$title."'";
	$result = mysql_query($sql) or die(mysql_error());
    $row = mysql_fetch_array($result);
    if ( $row ) return new Page( $row );
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
  
  /*
  Updates page
  */
  public function updatePage() {
    	$sql = "UPDATE page SET page_title='$this->page_title', page_body='$this->page_body', page_sidebar='$this->page_sidebar', page_status='$this->page_status',page_protection=$this->page_protection,page_headtitle='$this->page_headtitle' WHERE page_id = $this->page_id";
		if (!mysql_query($sql))
		{
			 die('Error: ' . mysql_error());
		}
		echo "1";
  }
  
  public function deletePage($page_id) {

    // Delete the Article
    $sql = "DELETE FROM page WHERE page_id = $page_id LIMIT 1";
   	if (!mysql_query($sql))
	{
		die('Error: ' . mysql_error());
	}
	echo "<p>Your page is successfully removed.</p>";
  }

}
 
?>


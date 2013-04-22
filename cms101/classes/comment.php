<?php
// Include database connection settings
/**
 * Class to handle blogs
*/
 
class Comment
{
 
// Properties

//id of blog
  public $cBlog_id = null;
 
//when was the blog published
  public $cDate = null;
 
//title of blog
  public $cId = null;
 
//blog summary
  public $cAuthor_id = null;
 
//blog content
  public $cText = null;

  /**
  * Sets the object's properties using the values in the supplied array
  *
  * @param assoc The property values
  */
 //CONSTRUCTOR for class blog
  public function __construct( $data=array() ) {
    if ( isset( $data['cBlog_id'] ) )$this->cBlog_id = $data['cBlog_id'];
    if ( isset( $data['cDate'] ) ) $this->cDate = $data['cDate'];
    if ( isset( $data['cId'] ) ) $this->cId = $data['cId'];
    if ( isset( $data['cAuthor_id'] ) ) $this->cAuthor_id = ($data['cAuthor_id']);
	 if ( isset( $data['cText'] ) ) $this->cText = mysql_real_escape_string($data['cText']);
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
 
  public static function getCommentById( $id ) {
    $sql = "SELECT * FROM comment WHERE cId = $id";
	$result = mysql_query($sql) or die(mysql_error());
    $row = mysql_fetch_array($result);
    if ( $row ) return new Comment( $row );
  }
  
  public static function getCommentByAuthorId( $start_from,$numRows,$id,$order="cDate DESC" ) {
  	$list = array();
    $sql = "SELECT * FROM comment WHERE cAuthor_id = $id ORDER BY " . mysql_escape_string($order) . " LIMIT $start_from,$numRows";
	$result = mysql_query($sql) or die(mysql_error());
    while( $row = mysql_fetch_array($result)){
	  $comment = new Comment( $row );
      $list[] = $comment;
    }
 
    // Now get the total number of blogs that matched the criteria
    $sql = "SELECT COUNT(cId) from comment WHERE cAuthor_id = $id";
	$result = mysql_query($sql) or die(mysql_error());
	$totalRows = mysql_fetch_array($result);
    return ( array ( "results" => $list, "totalRows" => $totalRows[0]) );
  }
 
 
  /**
  * Returns all (or a range of) Blog objects in the DB
  *
  * @param int Optional The number of rows to return (default=all)
  * @param string Optional column by which to order the blog (default="publicationDate DESC")
  * @return Array|false A two-element array : results => array, a list of Article objects; totalRows => Total number of articles
  */
 
  public static function getList( $start_from,$numRows, $order="cDate DESC" ) {
  	$list = array();
    $sql = "SELECT * FROM comment ORDER BY " . mysql_escape_string($order) . " LIMIT $start_from,$numRows";
	$result = mysql_query($sql) or die(mysql_error());
 
    while( $row = mysql_fetch_array($result)){
	  $comment = new Comment( $row );
      $list[] = $comment;
    }
 
    // Now get the total number of blogs that matched the criteria
    $sql = "SELECT COUNT(cId) from comment";
	$result = mysql_query($sql) or die(mysql_error());
	$totalRows = mysql_fetch_array($result);
    return ( array ( "results" => $list, "totalRows" => $totalRows[0] ) );
  }
  
  public static function getListByBlog( $id, $start_from,$numRows, $order="cDate DESC" ) {
  	$list = array();
    $sql = "SELECT * FROM comment where cBlog_id=".$id." ORDER BY " . mysql_escape_string($order) . " LIMIT $start_from,$numRows";
	$result = mysql_query($sql) or die(mysql_error());
 
    while( $row = mysql_fetch_array($result)){
	  $comment = new Comment( $row );
      $list[] = $comment;
    }
 
    // Now get the total number of blogs that matched the criteria
    $sql = "SELECT COUNT(cId) from comment where cBlog_id=$id";
	$result = mysql_query($sql) or die(mysql_error());
	$totalRows = mysql_fetch_array($result);
    return ( array ( "results" => $list, "totalRows" => $totalRows[0] ) );
  }
 
 
  /**
  * Inserts the current Blog object into the database, and sets its ID property.
  */
 
  public function insertComment() {
 	if(empty($this->cBlog_id)) $error[1] = "1";
	if(!isset($_SESSION['id'])) $error[2] = "1";
	if(!empty($error)){
				echo "<table style='border: 1px solid red;'>";
        		if(isset($error[1]) && $error[1] == "1") echo "<tr><td style='color:red;' class='smallText'>Blog not found.</td></tr>";
				if(isset($error[2]) && $error[2] == "1") echo "<tr><td style='color:red;' class='smallText'>You need to be logged-in to comment.</td></tr>";
				echo "</table>";
	}
	else{
		// Insert the Article
		$sql = "INSERT INTO comment(cId, cBlog_id,cAuthor_id,cText,cDate) VALUES ('',".$this->cBlog_id.",".$_SESSION['id'].",'".$this->cText."', now())";
		if (!mysql_query($sql))
		{
			 die('Error: ' . mysql_error());
		}
		echo "Your comment is successfully created.";
	}
  }
 
 
  /**
  * Updates the current Blog object in the database.
  */
 
  public function editComment($comment_id,$comment_txt) {
     $sql = "Update comment set cText='".mysql_real_escape_string($comment_txt)."' where cId=".$comment_id;
   	if (!mysql_query($sql))
	{
		die('Error: ' . mysql_error());
	}
	echo "Your comment is successfully edited.";
  }
 
  /**
  * Deletes the current Blog object from the database.
  */
 
  public function deleteComment($comment_id) {
	// Delete the Comment
    $sql = "DELETE FROM comment WHERE cId = $comment_id LIMIT 1";
   	if (!mysql_query($sql))
	{
		die('Error: ' . mysql_error());
	}
	echo "Your comment is successfully removed.";

  }
 
}
 
?>

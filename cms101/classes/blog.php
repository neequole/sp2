<?php
// Include database connection settings
/**
 * Class to handle blogs
*/
 
class Blog
{
 
// Properties

//id of blog
  public $id = null;
 
//when was the blog published
  public $publicationDate = null;
 
//title of blog
  public $title = null;
 
//blog summary
  public $summary = null;
 
//blog content
  public $content = null;
//blog author
	public $author_id = null;
 
 
  /**
  * Sets the object's properties using the values in the supplied array
  *
  * @param assoc The property values
  */
 //CONSTRUCTOR for class blog
  public function __construct( $data=array() ) {
    if ( isset( $data['blog_id'] ) )$this->id = $data['blog_id'];
    if ( isset( $data['publicationDate'])) $this->publicationDate = $data['publicationDate'];
    if ( isset( $data['blog_title'] ) ) $this->title = mysql_real_escape_string($data['blog_title']);
    if ( isset( $data['blog_summary'] ) ) $this->summary = mysql_real_escape_string($data['blog_summary']);
    if ( isset( $data['blog_content'] ) ) $this->content = mysql_real_escape_string($data['blog_content']);
	 if ( isset( $data['author_id'] ) ) $this->author_id = $data['author_id'];
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
 
  public static function getBlogById( $id ) {
    $sql = "SELECT * FROM blog WHERE blog_id = $id";
	$result = mysql_query($sql) or die(mysql_error());
    $row = mysql_fetch_array($result);
    if ( $row ) return new Blog( $row );
  }
  
  public static function getBlogByAuthorId( $start_from,$numRows,$id,$order="publicationDate DESC" ) {
  	$list = array();
    $sql = "SELECT * FROM blog WHERE author_id = $id ORDER BY " . mysql_escape_string($order) . " LIMIT $start_from,$numRows";
	$result = mysql_query($sql) or die(mysql_error());
    while( $row = mysql_fetch_array($result)){
	  $blog = new Blog( $row );
      $list[] = $blog;
    }
 
    // Now get the total number of blogs that matched the criteria
    $sql = "SELECT COUNT(blog_id) from blog WHERE author_id = $id";
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
 
  public static function getList( $start_from,$numRows, $order="publicationDate DESC" ) {
  	$list = array();
    $sql = "SELECT * FROM blog where publicationDate is not null ORDER BY " . mysql_escape_string($order) . " LIMIT $start_from,$numRows";
	$result = mysql_query($sql) or die(mysql_error());
 
    while( $row = mysql_fetch_array($result)){
	  $blog = new Blog( $row );
      $list[] = $blog;
    }
 
    // Now get the total number of blogs that matched the criteria
    $sql = "SELECT COUNT(blog_id) from blog where publicationDate is not null";
	$result = mysql_query($sql) or die(mysql_error());
	$totalRows = mysql_fetch_array($result);
    return ( array ( "results" => $list, "totalRows" => $totalRows[0] ) );
  }
 
 
  /**
  * Inserts the current Blog object into the database, and sets its ID property.
  */
 
  public function insertBlog() {
 	if(empty($this->title)) $error[1] = "1";
	if(empty($this->summary))	$error[2] = "1";
	if(empty($this->content))	$error[3] = "1";
	if(empty($this->author_id))	$error[4] = "1";
	if(!empty($error)){
				echo "<table style='border: 1px solid red;'>";
        		if(isset($error[1]) && $error[1] == "1") echo "<tr><td style='color:red;' class='smallText'>Title cannot be blank.</td></tr>";
				if(isset($error[2]) && $error[2] == "1") echo "<tr><td style='color:red;' class='smallText'>Summary cannot be blank.</td></tr>";
				if(isset($error[3]) && $error[3] == "1") echo "<tr><td style='color:red;' class='smallText'>Content cannot be blank.</td></tr>";
				if(isset($error[4]) && $error[4] == "1") echo "<tr><td style='color:red;' class='smallText'>Author undefined.</td></tr>";
				echo "</table>";
	}
	else{
		// Insert the Article
		$sql = "INSERT INTO blog ( blog_id, publicationDate, blog_title, blog_summary, blog_content, author_id) VALUES ('', null, '$this->title', '$this->summary', '$this->content',$this->author_id)";
		if (!mysql_query($sql))
		{
			 die('Error: ' . mysql_error());
		}
		echo 1;
	}
  }
 
 
  /**
  * Updates the current Blog object in the database.
  */
 
  public function updateBlog() {
     	if(empty($this->title)) $error[1] = "1";
		if(empty($this->summary))	$error[2] = "1";
		if(empty($this->content))	$error[3] = "1";
		if(!empty($error)){
				echo "<table style='border: 1px solid red;'>";
        		if(isset($error[1]) && $error[1] == "1") echo "<tr><td style='color:red;' class='smallText'>Title cannot be blank.</td></tr>";
				if(isset($error[2]) && $error[2] == "1") echo "<tr><td style='color:red;' class='smallText'>Summary cannot be blank.</td></tr>";
				if(isset($error[3]) && $error[3] == "1") echo "<tr><td style='color:red;' class='smallText'>Content cannot be blank.</td></tr>";
				echo "</table>";
		}
		else{
    			// Update the Article
    			if(isset($this->publicationDate) && $this->publicationDate == 'true' ) $sql = "UPDATE blog SET publicationDate=now(), blog_title='$this->title', blog_summary='$this->summary', blog_content='$this->content' WHERE blog_id = $this->id";
				else $sql = "UPDATE blog SET publicationDate=null, blog_title='$this->title', blog_summary='$this->summary', blog_content='$this->content' WHERE blog_id = $this->id";
				if (!mysql_query($sql))
				{
			 		die('Error: ' . mysql_error());
				}
				echo "1";
		}
  }
 
  /**
  * Deletes the current Blog object from the database.
  */
 
  public function deleteBlog($blog_id) {

    // Delete the Article
    $sql = "DELETE FROM blog WHERE blog_id = $blog_id LIMIT 1";
   	if (!mysql_query($sql))
	{
		die('Error: ' . mysql_error());
	}
	echo "Your blog is successfully removed.";
  }
 
}
 
?>

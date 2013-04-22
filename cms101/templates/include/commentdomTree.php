<?php
/*
echo '<div class="popbox"><a href="#" title="Add Comment" class="open"><img src="include/images/add.png" class="eyelet"/></a><div class="collapse"><div class="box"><div class="arrow"></div><div class="arrow-border"></div>Content in PopBox goes here<a href="#" class="close">close</a></div></div></div>';
*/
?>
<ul id="dhtmlgoodies_tree4" class="dhtmlgoodies_tree">
<?php 
	$rs = mysql_query("SELECT * FROM blog") or die(mysql_error());
    while($row = mysql_fetch_array($rs)){
		echo '<li id="'.$row['blog_id'].'" noDrag="true" noSiblings="true" noDelete="true" noRename="true"><a href="#">'.$row['blog_title'].'</a><div class="popbox"><a href="#" title="Add Comment" class="open"><img src="include/images/add.png" class="eyelet"/></a><div class="collapse"><div class="box"><div class="arrow"></div><div class="arrow-border"></div><form><textarea id="adminComment'.$row['blog_id'].'" placeholder="Put your comment here" required maxlength="1000"></textarea><br/><input type="button" value="Post!" class="submitAdminComment" title="'.$row['blog_id'].'"/><a href="#" class="close">close</a></form></div></div></div>';	
        $result = mysql_query("Select * from comment where cBlog_id=".$row['blog_id']) or die(mysql_error());
		if(mysql_num_rows($result) > 0){
			echo "<ul>";
			while($row2 = mysql_fetch_array($result)){
				$result2 = mysql_query("SELECT usr_email FROM user where usr_id=".$row2['cAuthor_id']) or die(mysql_error());
				$row3 = mysql_fetch_row($result2);
				echo '<li id="'.$row2['cId'].'" noDrag="true" noRename="true"><a href="#" class="editable" title="Edit comment">'.$row2['cText'].'</a><p class="smallText"> commented by '.$row3[0].' on '.$row2['cDate'].'<a href="#" title="Delete Comment" id="adminDeleteComment"><img src="include/images/delete.png" class="eyelet"/></a></p>';
			}
			echo "</ul>";
		}
		echo "</li>";
		echo "<hr/>";  
	}
?>
</ul> 
   
<script type="text/javascript">	
	treeObj = new JSDragDropTree();
    treeObj.setTreeId('dhtmlgoodies_tree4');
    treeObj.setMaximumDepth(7);
    treeObj.setMessageMaximumDepthReached('Maximum depth reached'); // If you want to show a message when maximum depth is reached, i.e. on drop.
   	treeObj.initTree();
    treeObj.expandAll(); 
</script>
 <!--END OF BLOG DOM TREE-->

<ul id="dhtmlgoodies_tree3" class="dhtmlgoodies_tree">
<?php
	$rs = mysql_query("SELECT * FROM user where usr_cDate is not null") or die(mysql_error());
    while($row = mysql_fetch_array($rs)){
		echo '<li id="'.$row['usr_id'].'" noDrag="true" noSiblings="true" noDelete="true" noRename="true"><a href="#">'.$row['usr_email'].'</a><a href="#" title="Add Blog" id="adminAddBlog"><img src="include/images/add.png" class="eyelet"/></a>';
        $result = mysql_query("Select * from blog where author_id=".$row['usr_id']) or die(mysql_error());
		if(mysql_num_rows($result) > 0){
			echo "<ul>";
			while($row2 = mysql_fetch_array($result)){
				echo '<li id="'.$row2['blog_id'].'" noDrag="true" noRename="true"><a href="#">'.$row2['blog_title'].'</a><a href="#" title="Delete Blog" id="adminDeleteBlog"><img src="include/images/delete.png" class="eyelet"/></a><a href="#" title="Edit Blog" id="adminEditBlog"><img src="include/images/edit.png" class="eyelet"/></a>';
				if($row2['publicationDate']==null || !isset($row2['publicationDate'])) echo "<p class='smallText'>not published</p>";
				else echo "<p class='smallText'>".$row2['publicationDate']."</p>";
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
    treeObj.setTreeId('dhtmlgoodies_tree3');
    treeObj.setMaximumDepth(7);
    treeObj.setMessageMaximumDepthReached('Maximum depth reached'); // If you want to show a message when maximum depth is reached, i.e. on drop.
   	treeObj.initTree();
    treeObj.expandAll(); 
</script>
 <!--END OF BLOG DOM TREE-->


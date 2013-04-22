<div id='createBlogPage'>
<h3>Edit Blog</h3>
<form >
        <div id='editBlogError'>
        	<!--This is where to put error messages-->
        </div>
        <ul>
              <li>
                <label for="title">Blog Title*</label>
                <input type="hidden" id="htitle" value ="<?php echo $results['blog']->title?>"/>
                <input type="hidden" id="hcontent" value ="<?php echo $results['blog']->content?>"/>
                <input type="hidden" id="hsummary" value ="<?php echo $results['blog']->summary?>"/>
                 <input type="hidden" id="hdate" value ="<?php echo $results['blog']->publicationDate?>"/>
                <input type="text" name="title" id="edittitle" placeholder="Blog Title" value="<?php echo $results['blog']->title?>" required autofocus maxlength="255" style="height: 2em; width:100%";/>
              </li>
     			<br/>
              <li>
                <label for="summary">Blog Summary*</label><br/>
                <textarea name="summary" id="editsummary"  placeholder="Brief description of your blog" required maxlength="1000" style="height: 5em; width:100%;"><?php echo $results['blog']->summary?></textarea>
              </li>
     			<br/>
              <li>
                <label for="content">Blog Content*</label><br/>
                <textarea name="content" id="editcontent" placeholder="Your blog content" required maxlength="100000" style="height: 30em;width:100%;"><?php echo $results['blog']->content?></textarea>
              </li>
        </ul>
 		<div id='adminCheck'></div>
        <div class="buttons">
        	<p>*required.</p>
          <input type="button" value="Save" id="submitEditBlog" title="<?php echo $results['blog']->id;?>"/>
          <input type="button" value="Cancel" id="cancelEditBlog" title="myBlog"/>
        </div>
</form> 
</div>



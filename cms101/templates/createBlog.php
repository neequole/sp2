<div id='createBlogPage'>
<h3>Create Blog</h3>
<form >
        <input type="hidden" name="articleId" value="<?php echo $results['article']->id ?>"/>
        <input type="hidden" id="loginBlogAuthor" value="<?php echo $_SESSION['id'] ?>"/>
        <input type='hidden' id='blogAuthor'/>
        <div id='createBlogError'>
        	<!--This is where to put error messages-->
        </div>
        <ul>
              <li>
                <label for="title">Blog Title*</label>
                <input type="text" name="title" id="title" placeholder="Blog Title" required autofocus maxlength="255" style="height: 2em; width:100%";/>
              </li>
     			<br/>
              <li>
                <label for="summary">Blog Summary*</label><br/>
                <textarea name="summary" id="summary" placeholder="Brief description of your blog" required maxlength="1000" style="height: 5em; width:100%;"></textarea>
              </li>
     			<br/>
              <li>
                <label for="content">Blog Content*</label><br/>
                <textarea name="content" id="content" placeholder="Your blog content" required maxlength="100000" style="height: 30em;width:100%;"></textarea>
              </li>
        </ul>
 
        <div class="buttons">
        	<p>*required.</p>
          <input type="button" value="Save" id="addBlog"/>
          <input type="button" value="Cancel" id="cancelAddBlog" title="blogHomepage"/>
        </div>
</form> 
</div>



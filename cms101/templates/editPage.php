<script>
	$(function() {
		$( "#editPageTabs" ).tabs();
		$("#sidepage").click(function(){
			if(CKEDITOR.instances['editSidepage']) {
					delete CKEDITOR.instances['editSidepage'];
					$('#editSidepage').ckeditor();
				}
				else {
					$('#editSidepage').ckeditor();
				}
		});
		
	});
</script>

<div id="editPageSegment">
	<form>
	<h3>EDIT PAGE</h3>
    <input type="hidden" value='<?php echo $data->page_id;?>' id='pageId'/>
    <input type="hidden" value='<?php echo $data->page_title;?>' id='pageTitle'/>
    <div id='editPageError'></div>
    <p>
       <label for="page_title">Page Title</label>
       <input type="hidden" value='<?php echo $data->page_headtitle;?>' id='heditTitle'/>
       <input type="text" value='<?php echo $data->page_headtitle;?>' id='editTitle'/>
    </p>
	<div id="editPageTabs">
        <ul>
            <li><a href="#tabsBody">Body</a></li>
            <li><a href="#tabsSidepage" id='sidepage'>Side Page</a></li>
        </ul>
        <div id="tabsBody">
        	<input type="hidden" value='<?php echo $data->page_body;?>' id='heditBody'/>
            <textarea class='editor' required maxlength="100000" style="height: 30em;width:100%;" id='editBody'><?php echo $data->page_body;?></textarea>
        </div>
        <div id="tabsSidepage">
        	<input type="hidden" value='<?php echo $data->page_sidebar;?>' id='heditSidepage'/>
            <textarea class='editor' required maxlength="100000" style="height: 30em;width:100%;" id='editSidepage'><?php echo $data->page_sidebar;?></textarea>
        </div>
	</div>
    <input type="hidden" value='<?php echo $data->page_status;?>' id='heditStatus'/>
    <input type="hidden" value='<?php echo $data->page_protection;?>' id='heditProtection'/>
    <table>
    	<tr>
    		<td>
            <label for="page_status">Page Status: </label>
            </td>
            <td>
            <select id='editStatus'>
				<option value="draft">Draft</option>
                <option value="published">Published</option>
            </select>
            </td>
            <td>
            <label for="page_protection">Log-in: </label>
            </td>
            <td>
            <select id='editProtection'>
				<option value="1">Required</option>
                <option value="0">Not Required</option>
            </select>
            </td>
    	<tr>
        </table>
        <table>
        <tr>
        	<td>
            <input type='button' value='Save' id='saveEditPage'/>
        	</td>
            <td>
            <input type='button' value='Cancel' id='cancelEditPage'/>
        	</td>
        </tr>
    </table>
    </form>
</div>
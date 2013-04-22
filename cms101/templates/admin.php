<?php
	session_start();
	if (!isset($_SESSION['id'])) {
	header('Location: homepage.php');
	}
	include( "../config.php" );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Administrator Page</title>
    <link rel="stylesheet" type="text/css" href="include/adminStyle.css" />
    <link rel="stylesheet" href="include/css/drag-drop-folder-tree.css" type="text/css"></link>
    <link rel="stylesheet" href="include/css/context-menu.css" type="text/css"></link>
    <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
    <link rel='stylesheet' href='include/css/popbox.css' type='text/css'>  
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
  	<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
    <script type='text/javascript' charset='utf-8' src='include/js/popbox.js'></script>  
    <script type="text/javascript" src="js/organictabs.jquery.js"></script>
    <script type="text/javascript" src="include/organicTab.js"></script>
    <script type="text/javascript" src="include/js/ajax.js"></script>
    <script type="text/javascript" src="include/js/jquery.inlineedit.js"></script>
    <script type="text/javascript" src="include/adminJS.js"></script>
	<script type="text/javascript" src="include/js/context-menu.js"></script><!-- IMPORTANT! INCLUDE THE context-menu.js FILE BEFORE drag-drop-folder-tree.js -->
	<script type="text/javascript" src="include/js/drag-drop-folder-tree.js"></script>
    <script type="text/javascript" src="/cms101/ckeditor/ckeditor.js"></script> <!--CK editor-->
	<script type="text/javascript" src="/cms101/ckeditor/adapters/jquery.js"></script>
   
   	<style type="text/css">
	/* CSS for the demo */
	img{
		border:0px;
	}
	</style>
    
	<script type="text/javascript">
		//--------------------------------
		// Save functions
		//--------------------------------
		var ajaxObjects = new Array();
		
		// Use something like this if you want to save data by Ajax.
		function saveMyTree()
		{
				saveString = treeObj.getNodeOrders();
				var ajaxIndex = ajaxObjects.length;
				ajaxObjects[ajaxIndex] = new sack();
				var url = 'saveNodes.php?saveString=' + saveString;
				ajaxObjects[ajaxIndex].requestFile = url;	// Specifying which file to get
				ajaxObjects[ajaxIndex].onCompletion = function() { saveComplete(ajaxIndex); } ;	// Specify function that will be executed after file has been found
				ajaxObjects[ajaxIndex].runAJAX();		// Execute AJAX function			
			
		}
		function saveComplete(index)
		{
			alert(ajaxObjects[index].response);			
		}
	
		
		// Call this function if you want to save it by a form.
		function saveMyTree_byForm()
		{
			document.myForm.elements['saveString'].value = treeObj.getNodeOrders();
			document.myForm.submit();		
		}	
		
		
	</script>
	<script type='text/javascript'>
		$(document).ready(function(){
			
			$( "#userTabs" ).tabs();
			$('.popbox').popbox();
			$('#accordion').accordion();
			//$("#adminTab").organicTabs();
			$("#adminTab").organicTabs({
				"speed": 200
			});
			$('.editable').inlineEdit({control: 'textarea'});
			//modal
			$('.adminEditUser').click(function(e) {
				//Cancel the link behavior
				e.preventDefault();
				//Get the A tag
				var id = $(this).attr('href');
			 	//put value on forms
					var row = $(this).closest('tr').attr('id');
					$('#hEditId').attr('value',row);
					$.ajax({
						url: "/cms101/userFunc.php",
						data: "action=searchById&usr_id="+row,
						dataType: "html",
						type: "post",
						success:function(data) {
							var obj  = jQuery.parseJSON( data );
							// Now the two will work
							$.each(obj, function(key, value) {
    							if(key=='email') {$('#adminEditemail').attr('value', value);$('#hadminEditemail').attr('value', value);}
								if(key=='pwd') {$('#adminEditpwd').attr('value', value);$('#hadminEditpwd').attr('value', value);}
								if(key=='fname') {$('#adminEditfname').attr('value', value);$('#hadminEditfname').attr('value', value);}
								if(key=='mname') {$('#adminEditmname').attr('value', value);$('#hadminEditmname').attr('value', value);}
								if(key=='lname') {$('#adminEditlname').attr('value', value);$('#hadminEditlname').attr('value', value);}
								if(key=='role') {$('#adminEditRole').val(value);$('#hadminEditRole').val(value);}
							});
							
							
						}
					});	
				//Get the screen height and width
				var maskHeight = $(document).height();
				var maskWidth = $(window).width();
			 
				//Set height and width to mask to fill up the whole screen
				$('#mask').css({'width':maskWidth,'height':maskHeight});
				 
				//transition effect     
				$('#mask').fadeIn(1000);    
				$('#mask').fadeTo("slow",0.8);  
			 
				//Get the window height and width
				var winH = $(window).height();
				var winW = $(window).width();
					   
				//Set the popup window to center
				$(id).css('top',  winH/2-$(id).height()/2);
				$(id).css('left', winW/2-$(id).width()/2);
			 
				//transition effect
				$(id).fadeIn(2000); 
			 
			});
			 
			//if close button is clicked
			$('.window .close, #adminEditUser').click(function (e) {
				//Cancel the link behavior
				e.preventDefault();
				$('#mask, .window').hide();
			});     
			 
			//if mask is clicked
			$('#mask').click(function () {
				$(this).hide();
				$('.window').hide();
			});         
			//modal
			
			//search
			$("#kwd_search").keyup(function(){
			// When value of the input is not blank
				if( $(this).val() != "")
				{
					var id = $('#userTabs .ui-tabs-selected > a').attr('href');
				// Show only matching TR, hide rest of them
					$(id + "> .my-table tbody>tr").hide();
					$(".my-table td:contains-ci('" + $(this).val() + "')").parent("tr").show();
				}
				else
				{
				// When there is no input or clean again, show everything back
					$(".my-table tbody>tr").show();
				}
			});
			//search 
		});
		
		//used in matching search
		$.extend($.expr[":"],
		{
    		"contains-ci": function(elem, i, match, array)
			{
				return (elem.textContent || elem.innerText || $(elem).text() || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
			}
		});

	</script>
</head>
	
<!--BODY-->
<body>
	<!--HEADER-->
    <div id='try'>
        <div id='adminHead'>
            <div id='logo'>
             <img src='include/logo.png' id='img1'/>
            </div>
            <div id='adminLink'>
            	<?php echo "<p style='padding-top:150px;'>Welcome, <a href='#'>".$_SESSION['name']."</a>! | <a href='#' id='adminlogoutButton'>Logout</a> |  <a href='homepage.php' target='_blank'>View Site</a></p>";?>
            </div>
        </div>
    </div>
    <!--END OF HEADER-->
    <!--MAIN BODY-->
    <div id='adminMain'>
    	<!--TAB INTERFACE-->
    	<div id="adminTab">
            <ul class="nav">
                        <li class="nav-one"><a href="#page" class="current" title="View Page" id='viewwww'>Page</a></li>
                        <li class="nav-two"><a href="#blog" title="Edit Blog">Blog</a></li>
                        <li class="nav-three"><a href="#comment" title="Edit Comment">Comment</a></li>
                        <li class="nav-four"><a href="#config" title="Edit Config">Config</a></li>
                        <li class="nav-four last"><a href="#user" title="Edit User">User</a></li>
            </ul>
        	<div class='content'>
            	<div id='editMsg'></div>
                <div class="list-wrap">                      
                    <ul id="page">
                            <!--LIST-->
                           <?php include('include/domTree.php') ?>
                    </ul>
            
                     <ul id="blog" class="hide">
                        <?php include('include/blogdomTree.php') ?>
                        
                     </ul>
            
                     <ul id="comment" class="hide">
                        <?php include('include/commentdomTree.php') ?>
                     </ul>
            
                     <ul id="config" class="hide">
                        <li>4!</li>
                     </ul>
                     
                     <ul id="user" class="hide">
                     	<?php include('include/userList.php') ?>
                     </ul>
            
                </div> <!-- END List Wrap -->
              </div> <!-- END Content -->
         </div>  <!--END Organic Tabs (Example One) -->
    </div>
    <!--END OF MAIN CONTENT-->
</body>
<!--END OF BODY-->
</html>

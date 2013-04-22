	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
	<script type="text/javascript" src="include/script.js"></script>
    <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
	<script language="javascript" type="text/javascript" src="jquery.js"></script>

	<script>
	  $(document).ready(function() {

    	//Execute the slideShow
    	slideShow();
		$( "#tabs" ).tabs();
		$("#demo4 nav li a").click(function() {
			if($(this).hasClass('active')){
				return;
			}
			else{
				var id = $(this).attr("title");
				if(id=='login') window.location.replace("userLogin.php");
				else{
				//ajax the php code
					$.ajax({
						url: "/cms101/index.php",
						data: "action="+id+"&page=1",
						dataType: "html",
						type: "get",
						success:function(data) {	
							$("#main").html(data);
						}
					});
				}
			}
		});
		
		$(".page").die('click').live('click',function() {
			var id = $(this).attr("title");
			$.ajax({
				url: "/cms101/index.php",
				data: "action=blogHomepage&page="+id,
				dataType: "html",
				type: "get",
				success:function(data) {
					$("#main").html(data);
				}
			});
		});
		
		$(".page2").die('click').live('click',function() {
			var id = $(this).attr("title");
			$.ajax({
				url: "/cms101/index.php",
				data: "action=myBlog&page="+id,
				dataType: "html",
				type: "get",
				success:function(data) {
					$("#main").html(data);
				}
			});
		});
		
		$(".page3").die('click').live('click',function() {
			var id = $(this).attr("title");
			var blogId = $('#hBlogId').attr("value");
			$.ajax({
				url: "/cms101/index.php",
				data: "action=getComment&page="+id+"&blogId="+blogId,
				dataType: "html",
				type: "get",
				success:function(data) {
					$("#allComment").html(data);
				}
			});
		});
		
		$(".viewBlog").die('click').live('click',function() {
			var id = $(this).attr("title");
			//$('#sidebar').remove();
			//$('#output').css({"width":"100%"});
			$.ajax({
				url: "/cms101/index.php",
				data: "action=viewBlog&blogId="+id+"&page=1",
				dataType: "html",
				type: "get",
				success:function(data) {
					$("#main").html(data);
				}
			});
		});	
		/* This function is located at loginJS.js
		$(".register").die('click').live('click',function() {
			
			var email = $("#reg_email").attr("value");
			var pwd = $("#reg_pwd").attr("value");
			var fname = $("#reg_fname").attr("value");
			var mname = $("#reg_mname").attr("value");
			var lname = $("#reg_lname").attr("value");
			var captcha = $("#captcha").attr("value");
				$.ajax({
					url: "/cms101/userFunc.php",
					data: "action=create&usr_email="+email+"&usr_pwd="+pwd+"&usr_fname="+fname+"&usr_mname="+mname+"&usr_lname="+lname+"&captcha="+captcha,
					dataType: "html",
					type: "post",
					success:function(data) {
						$("#errorReg").html(data);
					}
				});
		});
		
		$(".loginButton").die('click').live('click',function() {
		alert('heell');
			var email = $("#log_email").attr("value");
			var pwd = $("#log_pwd").attr("value");
			$.ajax({
				url: "/cms101/userFunc.php",
				data: "action=search&usr_email="+email+"&usr_pwd="+pwd,
				dataType: "html",
				type: "post",
				success:function(data) {
					if(data == '1'){
						$("#errorLog").html("<p class='smallText'>Username and Password does not match</p>");
					}
					else{
						$("#output").html("<p class='smallText'> You are now logged-in.</p>");
						//$("#tabs-1").html("<p class='smallText'> You are now logged-in.</p>");
						$(".login").html("<p class='smallText'> Log-out ("+data+")</p>");
						$(".login").attr('title','logout');
						$(".login").attr('class','logoutButton');
						//add subchild in navigation
						$(".blog").attr("title","Blog");
						$(".blog").addClass("active");
						$(".blog").after("<nav class='subChild'><ul><li><a title='myBlog'>My Blog</a></li><li><a title='blogHomepage'>View All Blog</a></li><li><a title='createBlog'>Create Blog</a></li></ul></nav>");
						$(".web").html("My Website</a></li>");
						$(".web").attr('title','myWebsite');
						$(".web").attr('href','include/web_templates/genericwebsitetemplate/index.php');
						$(".web").attr('target','_blank');	//show website in other window
					}
				}
			});
		});	
		*/
		$(".logoutButton").die('click').live('click',function() {
			$.ajax({
				url: "/cms101/userFunc.php",
				data: "action=logout",
				dataType: "html",
				type: "post",
				success:function(data) {
						alert("You are logged-out.");
						$(".logoutButton").html("Log-in");
						$(".logoutButton").attr('title','login');
						$(".logoutButton").attr('class','login');
						$(".blog").attr("title","blogHomepage");
						$(".blog").html("</a>Blog</li>");
						$(".blog").removeClass("active");
						$(".subChild").remove();
				}
			});
		});	
		
		$(".subChild ul li a, #cancelAddBlog, #cancelEditBlog").die('click').live('click',function() {
			var id = $(this).attr("title");
			$.ajax({
				url: "/cms101/index.php",
				data: "action="+id+"&page=1",
				dataType: "html",
				type: "get",
				success:function(data) {
					$("#main").html(data);
				}
			});
		});
		
		$("#addBlog").die('click').live('click',function() {
			//alert('hello');
			var title = $("#title").attr("value");
			var summary = $("#summary").attr("value");
			var content = $("#content").attr("value");
			var author = $("#loginBlogAuthor").attr("value");
			$.ajax({
				url: "/cms101/blogFunc.php",
				data: "action=addBlog&blog_title="+title+"&blog_summary="+summary+"&blog_content="+content+"&author_id="+author,
				dataType: "html",
				type: "post",
				success:function(data) {
					if(data == 1) $("#output").html("<p class='smallText'>Your blog is successfully created.</p>");
					else $("#createBlogError").html(data);
				}
			});
		});
		
		$("#editBlog").die('click').live('click',function() {
			var id = $(this).attr("title");
			$.ajax({
				url: "/cms101/blogFunc.php",
				data: "action=editBlog&blogId="+id,
				dataType: "html",
				type: "post",
				success:function(data) {
					$("#output").html(data);
				}
			});
		});
		
		$("#deleteBlog").die('click').live('click',function() {
			var id = $(this).attr("title");
			var answer = confirm("Do you really want to delete this blog?");
			if(answer){
				$.ajax({
					url: "/cms101/blogFunc.php",
					data: "action=deleteBlog&blogId="+id,
					dataType: "html",
					type: "post",
					success:function(data) {
						$("#output").html(data);
					}
				});
			}
			else return;
		});
		
		$("#deleteComment").die('click').live('click',function() {
			var id = $(this).attr("title");
			var answer = confirm("Do you really want to delete your comment?");
			if(answer){
				$.ajax({
					url: "/cms101/commentFunc.php",
					data: "action=deleteComment&cId="+id,
					dataType: "html",
					type: "post",
					success:function(data) {
						$("#output").html(data);
					}
				});
			}
			else return;
		});
		
		$("#submitEditBlog").die('click').live('click',function() {
			var id = $(this).attr("title");
			var htitle = $("#htitle").attr("value");
			var hsummary = $("#hsummary").attr("value");
			var hcontent = $("#hcontent").attr("value");
			var title = $("#edittitle").attr("value");
			var summary = $("#editsummary").attr("value");
			var content = $("#editcontent").attr("value");
			if(htitle == title && hsummary == summary && hcontent==content) return;
			$.ajax({
				url: "/cms101/blogFunc.php",
				data: "action=alterBlog&blog_id="+id+"&blog_title="+title+"&blog_summary="+summary+"&blog_content="+content,
				dataType: "html",
				type: "post",
				success:function(data) {
					if(data == 1) $("#output").html("<p class='smallText'>Your blog is successfully edited.</p>");
					else $("#editBlogError").html(data);
				}
			});
		});
		
		$("#submitComment").die('click').live('click',function() {
			var id = $(this).attr("title");
			var content = $("#comment").attr("value");
			if(content == null || content == '') return;
			$.ajax({
				url: "/cms101/commentFunc.php",
				data: "action=addComment&cBlog_id="+id+"&cText="+content,
				dataType: "html",
				type: "post",
				success:function(data) {
				$("#output").html(data);
				}
			});
		});	
				//on page refresh
		window.onbeforeunload = function() {
		var c = $('#main').html();
			$.ajax({
   				url: "/cms101/onReload.php",
				data: "content="+c,
				dataType: "html",
				type: "post",
				async : false,
				success:function(data) {
					$("#main").html(data);
				}
			});
		}
//end
});
	function slideShow() {
	 
		//Set the opacity of all images to 0
		$('#gallery a').css({opacity: 0.0});
		 
		//Get the first image and display it (set it to full opacity)
		$('#gallery a:first').css({opacity: 1.0});
		 
		//Set the caption background to semi-transparent
		$('#gallery .caption').css({opacity: 0.7});
	 
		//Resize the width of the caption according to the image width
		$('#gallery .caption').css({width: $('#gallery a').find('img').css('width')});
		 
		//Get the caption of the first image from REL attribute and display it
		$('#gallery .content2').html($('#gallery a:first').find('img').attr('rel'))
		.animate({opacity: 0.7}, 400);
		 
		//Call the gallery function to run the slideshow, 6000 = change to next image after 6 seconds
		setInterval('gallery()',6000);
		 
	}
	 
	function gallery() {
		 
		//if no IMGs have the show class, grab the first image
		var current = ($('#gallery a.show')?  $('#gallery a.show') : $('#gallery a:first'));
	 
		//Get next image, if it reached the end of the slideshow, rotate it back to the first image
		var next = ((current.next().length) ? ((current.next().hasClass('caption'))? $('#gallery a:first') :current.next()) : $('#gallery a:first'));   
		 
		//Get next image caption
		var caption = next.find('img').attr('rel'); 
		 
		//Set the fade in effect for the next image, show class has higher z-index
		next.css({opacity: 0.0})
		.addClass('show')
		.animate({opacity: 1.0}, 1000);
	 
		//Hide the current image
		current.animate({opacity: 0.0}, 1000)
		.removeClass('show');
		 
		//Set the opacity to 0 and height to 1px
		$('#gallery .caption').animate({opacity: 0.0}, { queue:false, duration:0 }).animate({height: '1px'}, { queue:true, duration:300 }); 
		 
		//Animate the caption, opacity to 0.7 and heigth to 100px, a slide up effect
		$('#gallery .caption').animate({opacity: 0.7},100 ).animate({height: '100px'},500 );
		 
		//Display the content
		$('#gallery .content2').html(caption);
			 
	}
</script>
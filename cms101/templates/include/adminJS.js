// JavaScript Document
//admin

	$(".editContent").die('click').live('click',function(){
		var id = $(this).closest('li').attr('id');
		$.ajax({
			url: "/cms101/pageFunc.php",
			data: "action=editPage&page_id="+id,
			dataType: "html",
			type: "post",
			success:function(data) {
				$(".list-wrap").css("height","50em");
				$("#page").html(data);
				//$( 'textarea.editor' ).ckeditor();
				if(CKEDITOR.instances['editBody']) {
					delete CKEDITOR.instances['editBody'];
					$('#editBody').ckeditor();
				}
				else {
					$('#editBody').ckeditor();
				}
				
				//dropdown
				var hpage_status = $("#heditStatus").attr('value');
				var hpage_protection = $("#heditProtection").attr('value');
				if(hpage_status == 'published') $('#editStatus').val('published');
				else $('#editStatus').val('draft');
				if(hpage_protection == '0') $('#editProtection').val('0');
				else $('#editProtection').val('1');
			}
		});
	});
	
	$("#saveEditPage").die('click').live('click',function(){
		var page_id= $("#pageId").attr('value') ;
		var page_title= $("#pageTitle").attr('value') ;
		var page_headtitle= $("#editTitle").attr('value') ;
		var page_body= $("#editBody").val();
		var page_sidebar= $("#editSidepage").val();
		var page_status = $("#editStatus").attr('value');
		var page_protection = $("#editProtection").attr('value');
		var hpage_headtitle= $("#heditTitle").attr('value') ;
		var hpage_body= $("#heditBody").attr('value');
		var hpage_sidebar= $("#heditSidepage").attr('value');
		var hpage_status = $("#heditStatus").attr('value');
		var hpage_protection = $("#heditProtection").attr('value');
		if(page_headtitle == hpage_headtitle && page_body == hpage_body && page_sidebar == hpage_sidebar && page_status == hpage_status && page_protection == hpage_protection)
			return;
		else{
			$.ajax({
			url: "/cms101/pageFunc.php",
			data: "action=submitEditPage&page_id="+page_id+"&page_title="+page_title+"&page_headtitle="+page_headtitle+"&page_body="+page_body+"&page_sidebar="+page_sidebar+"&page_status="+page_status+"&page_protection="+page_protection,
			dataType: "html",
			type: "post",
			success:function(data) {
				if(data == 1) {
					//$("#page").html("<p>Page has been saved!</p>");
					alert("Page has been saved!");
					//location.reload();
					callToLoad('page');
				}
				else $("#editPageError").html(data);
			}
		});
		}
	});
	
	
	$("#cancelEditPage").die('click').live('click',function(){
			callToLoad('page');
	});
	
	//to add callToLoad('page');
	$(".deletePage").die('click').live('click',function(){
		var id = $(this).closest('li').attr('id');
		var answer = confirm("Do you really want to delete this page?");
			if(answer){
				$.ajax({
					url: "/cms101/pageFunc.php",
					data: "action=deletePage&page_id="+id,
					dataType: "html",
					type: "post",
					success:function(data) {
						$("#page").html(data);
					}
				});
			}
			else return;
	});
	
	
	$("#adminAddBlog").die('click').live('click',function(){
		var id = $(this).closest('li').attr('id');
		$.ajax({
			url: "/cms101/index.php",
			data: "action=adminCreateBlog",
			dataType: "html",
			type: "get",
			success:function(data) {
				$("#blog").html(data);
				$("#blogAuthor").attr("value",id);
			}
		});
	});
	
	$("#addBlog").die('click').live('click',function(){
		var author = $("#blogAuthor").attr('value');
		var title = $("#title").attr("value");
		var summary = $("#summary").attr("value");
		var content = $("#content").attr("value");
		alert(author+title+summary+content);
		$.ajax({
			url: "/cms101/blogFunc.php",
			data: "action=addBlog&blog_title="+title+"&blog_summary="+summary+"&blog_content="+content+"&author_id="+author,
			dataType: "html",
			type: "post",
			success:function(data) {
				if(data == 1){
					alert('Your blog is successfully created');
					callToLoad('blog');
				}
				else $("#createBlogError").html(data);
			}
		});
	});
	
	$("#cancelAddBlog, #cancelEditBlog").die('click').live('click',function(){
		callToLoad('blog');
	});
	
	$("#adminEditBlog").die('click').live('click',function(){
		var id = $(this).closest('li').attr('id');
		$.ajax({
				url: "/cms101/blogFunc.php",
				data: "action=editBlog&blogId="+id,
				dataType: "html",
				type: "post",
				success:function(data) {
					$("#blog").html(data);
					$("#adminCheck").html('<form><input type="checkbox" id="published" value="yes" /> Publish</form>');
				}
			});
	});
	
	$("#submitEditBlog").die('click').live('click',function() {
			var date = 	$("#published").is(":checked");
			var hdate = $("#hdate").attr("value");
			var id = $(this).attr("title");
			var htitle = $("#htitle").attr("value");
			var hsummary = $("#hsummary").attr("value");
			var hcontent = $("#hcontent").attr("value");
			var title = $("#edittitle").attr("value");
			var summary = $("#editsummary").attr("value");
			var content = $("#editcontent").attr("value");
			if(htitle == title && hsummary == summary && hcontent==content){
				if(date == true && hdate != null && hdate != '') return;
				else if(date == false && (hdate == null || hdate == '')) return;
			}
			$.ajax({
				url: "/cms101/blogFunc.php",
				data: "action=alterBlog&blog_id="+id+"&blog_title="+title+"&blog_summary="+summary+"&blog_content="+content+"&publicationDate="+date,
				dataType: "html",
				type: "post",
				success:function(data) {
					if(data == 1){
						//$("#blog").html("<p class='smallText'>Your blog is successfully edited.</p>");
						alert('Blog successfully edited!');
						callToLoad('blog');
					}
					else $("#editBlogError").html(data);
				}
			});
	});
	
	$("#adminDeleteBlog").die('click').live('click',function(){
		var id = $(this).closest('li').attr('id');
		var answer = confirm("Do you really want to delete this blog?");
			if(answer){
				$.ajax({
					url: "/cms101/blogFunc.php",
					data: "action=deleteBlog&blogId="+id,
					dataType: "html",
					type: "post",
					success:function(data) {
						alert(data);
						callToLoad('blog');
					}
				});
			}
			else return;
	});
	
	$("#adminDeleteComment").die('click').live('click',function(){
		var id = $(this).closest('li').attr('id');
		var answer = confirm("Do you really want to delete your comment?");
			if(answer){
				$.ajax({
					url: "/cms101/commentFunc.php",
					data: "action=deleteComment&cId="+id,
					dataType: "html",
					type: "post",
					success:function(data) {
						alert(data);
						callToLoad('comment');
					}
				});
			}
			else return;
	});
	
	$(".submitAdminComment").die('click').live('click',function(){
		var id = $(this).attr("title");
		var content = $("#adminComment"+id).val();
		if(content == null || content == '') return;
			$.ajax({
				url: "/cms101/commentFunc.php",
				data: "action=addComment&cBlog_id="+id+"&cText="+content,
				dataType: "html",
				type: "post",
				success:function(data) {
					alert(data);
					callToLoad('comment');
				}
			});
	});
	
	$("#adminAddUser").die('click').live('click',function(){
		var lname = $('#addLname').attr('value');
		var mname = $('#addMname').attr('value');
		var fname = $('#addFname').attr('value');
		var email = $('#addEmail').attr('value');
		var pwd = $('#addPwd').attr('value');
		var cpwd = $('#addCPwd').attr('value');
		var role = $('#addRole').attr('value');
		
		$('#adminAdd').find(':input').each(function(){
				$(this).css("border-color","#ffffff");
		})
		
		if(email != '' && pwd != '' && cpwd != '' && fname != '' && mname != '' && lname != ''){
				$.ajax({
					url: "/cms101/userFunc.php",
					data: "action=adminCreate&usr_email="+email+"&usr_pwd="+pwd+"&usr_fname="+fname+"&usr_mname="+mname+"&usr_lname="+lname+"&usr_cpwd="+cpwd+"&usr_role="+role,
					dataType: "html",
					type: "post",
					success:function(data) {
						//$('#adminAddError').html("<p class='smallText'>"+data+"</p>");
						alert(data);
						callToLoad('user');
					}
				});	
			}
		else{
			$('#adminAdd').find(':input').each(function(){
  				if($(this).val() == '') $(this).css("border-color","red");
			})	
		}	
	});
	
	$("#adminCancelUser").die('click').live('click',function(){
		$('#addLname').attr('value','');
		$('#addMname').attr('value','');
		$('#addFname').attr('value','');
		$('#addEmail').attr('value','');
		$('#addPwd').attr('value','');
		$('#addCPwd').attr('value','');
		$('#addRole').val('typical');
	});
	
	$(".adminConfirmUser").die('click').live('click',function(){
		var id = $(this).closest('tr').attr('id');
		$.ajax({
			url: "/cms101/userFunc.php",
			data: "action=confirmUser&usr_id="+id,
			dataType: "html",
			type: "post",
			success:function(data) {
				alert(data);
				callToLoad('user');
			}
		});	
	});
	
	$(".adminDeleteUser").die('click').live('click',function(){
		var id = $(this).closest('tr').attr('id');
		var answer = confirm("Do you really want to delete this user?");
		if(answer){
			$.ajax({
				url: "/cms101/userFunc.php",
				data: "action=deleteUser&usr_id="+id,
				dataType: "html",
				type: "post",
				success:function(data) {
					if(data == 1){
						alert("You are logged-out.");
						window.location.replace('homepage.php');
					}
					else{
					alert(data);
					callToLoad('user');
					}
				}
			});	
		} else return;
	});
	
	$("#adminEditUser").die('click').live('click',function(){
		var email = $('#adminEditemail').attr('value');
		var hemail = $('#hadminEditemail').attr('value');
		var pwd = $('#adminEditpwd').attr('value');
		var hpwd = $('#hadminEditpwd').attr('value');
		var fname = $('#adminEditfname').attr('value');
		var hfname = $('#hadminEditfname').attr('value');
		var mname = $('#adminEditmname').attr('value');
		var hmname = $('#hadminEditmname').attr('value');
		var lname = $('#adminEditlname').attr('value');
		var hlname = $('#hadminEditlname').attr('value');
		var role = $('#adminEditRole').attr('value');
		var hrole = $('#hadminEditRole').attr('value');
		var id = $('#hEditId').attr('value');
		if(email == hemail && pwd == hpwd && fname == hfname && mname == hmname && lname == hlname && role == hrole) return;
		else{
			$.ajax({
					url: "/cms101/userFunc.php",
					data: "action=edit&usr_id="+id+"&usr_email="+email+"&usr_pwd="+pwd+"&usr_fname="+fname+"&usr_mname="+mname+"&usr_lname="+lname+"&usr_role="+role,
					dataType: "html",
					type: "post",
					success:function(data) {
						alert(data);
						callToLoad('user');
					}
				});
		}
	});
	
	$("#adminlogoutButton").die('click').live('click',function(){
			$.ajax({
				url: "/cms101/userFunc.php",
				data: "action=logout",
				dataType: "html",
				type: "post",
				success:function(data) {
					window.location.replace('homepage.php');
				}
			});	
	});
	
	function callToLoad(index){
		$.ajax({
			url: "/cms101/pageFunc.php",
			data: "action="+index,
			dataType: "html",
			type: "post",
			success:function(data) {
				$("#"+index).html(data);
			}
		});
	}
	
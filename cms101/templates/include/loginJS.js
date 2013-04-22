$(document).ready(function() {
	/*
	number of fieldsets
	*/
	var fieldsetCount = $('#formElem').children().length;
	
	/*
	current position of fieldset / navigation link
	*/
	var current 	= 1;
    
	/*
	sum and save the widths of each one of the fieldsets
	set the final sum as the total width of the steps element
	*/
	var stepsWidth	= 0;
    var widths 		= new Array();
	$('#steps .step').each(function(i){
        var $step 		= $(this);
		widths[i]  		= stepsWidth;
        stepsWidth	 	+= $step.width();
    });
	$('#steps').width(stepsWidth);
	
	/*
	to avoid problems in IE, focus the first input of the form
	*/
	$('#formElem').children(':first').find(':input:first').focus();	
	
	/*
	show the navigation bar
	*/
	$('#navigation').show();
	
	/*
	when clicking on a navigation link 
	the form slides to the corresponding fieldset
	*/
    $('#navigation a').bind('click',function(e){
		var $this	= $(this);
		var prev	= current;
		$this.closest('ul').find('li').removeClass('selected');
        $this.parent().addClass('selected');
		/*
		we store the position of the link
		in the current variable	
		*/
		current = $this.parent().index() + 1;
		/*
		animate / slide to the next or to the corresponding
		fieldset. The order of the links in the navigation
		is the order of the fieldsets.
		Also, after sliding, we trigger the focus on the first 
		input element of the new fieldset
		If we clicked on the last link (confirmation), then we validate
		all the fieldsets, otherwise we validate the previous one
		before the form slided
		*/
        $('#steps').stop().animate({
            marginLeft: '-' + widths[current-1] + 'px'
        },500,function(){	
		});
        e.preventDefault();
    });
	
	$(".register").die('click').live('click',function() {
			
			var email = $("#reg_email").attr("value");
			var pwd = $("#reg_pwd").attr("value");
			var fname = $("#reg_fname").attr("value");
			var mname = $("#reg_mname").attr("value");
			var lname = $("#reg_lname").attr("value");
			var captcha = $("#captcha").attr("value");
			
			$('#reg').find(':input').each(function(){
				$(this).css("border-color","#ffffff");
			})
			
			if(email != '' && pwd != '' && fname != '' && mname != '' && lname != '' && captcha != ''){
				$.ajax({
					url: "/cms101/userFunc.php",
					data: "action=create&usr_email="+email+"&usr_pwd="+pwd+"&usr_fname="+fname+"&usr_mname="+mname+"&usr_lname="+lname+"&captcha="+captcha,
					dataType: "html",
					type: "post",
					success:function(data) {
						$("#errorReg").html(data);
					}
				});	
			}
			else{
				$('#reg').find(':input').each(function(){
  					if($(this).val() == '') $(this).css("border-color","red");
				})	
			}
		});
		
	$(".loginButton").die('click').live('click',function() {
			var email = $("#log_email").attr("value");
			var pwd = $("#log_pwd").attr("value");
			$('#log').find(':input').each(function(){
				$(this).css("border-color","#ffffff");
			})
			if(email!='' && pwd!=''){
				$.ajax({
					url: "/cms101/userFunc.php",
					data: "action=search&usr_email="+email+"&usr_pwd="+pwd,
					dataType: "html",
					type: "post",
					success:function(data) {
						if(data.toString()==1){
							$("#errorLog").html("<p class='smallText' style='color:red;'>Username and Password does not match</p>");
						}
						else if(data.toString()== 2 ){
							$("#errorLog").html("<p class='smallText' style='color:red;'>Your account is not yet activated.</p>");
						}
						else{
							alert("Welcome to SYS CMS!");
							var result = data.match(/admin/i);
							if(result) {
								window.location.replace("admin.php");
							}
							else window.location.replace("homepage.php");
							/*
							$("#output").html("<p class='smallText' > You are now logged-in.</p>");
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
							*/
						}
					}
				});
			}
			else{
				$('#log').find(':input').each(function(){
  					if($(this).val() == '') $(this).css("border-color","red");
				})
			}
		});	
	
});
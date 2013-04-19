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
	
	
	$("#studtypeButton").live("click",function() {
				var row = '<p><label for="studnum">Student no*</label><input type="text" name="studnum1" id="reg_studnum1" class="required" maxlength="4" pattern="[0-9]+" placeholder="XXXX" style="width:50px" required/><input type="text" name="studnum2" id="reg_studnum2" class="required" maxlength="5" pattern="[0-9]+" placeholder="XXXXX" style="width:50px" required/></p><p><label for="college">College*</label><input type="text" name="college" id="reg_college" class="required" pattern="[A-Za-z ]+" required/></p><p><label for="course">Course*</label><input type="text" name="course" id="reg_course" class="required" pattern="[A-Za-z ]+" required/></p>';
				$("#usertype").html(row);
	});
	
	$("#admintypeButton").live("click",function() {
				$("#usertype").html("");
	});
	
	$("#facultytypeButton").live("click",function() {
				$("#usertype").html("");
	});
	
	
		
	$("#formElem").submit(function(event) {

			/* stop form from submitting normally */
			event.preventDefault(); 
			$("#ajax_result").html("");
			var err = 0;

			//check if blank
			$('#log').find(':input.required').each(function(){
				if ($(this).val() == ""){
					$(this).css("border-color","red");
					err = 1;
				}
				else $(this).css("border-color","#ddd");
			})
			
			//if not blank
			if(err==0){
			var uname = $("#log_uname").attr("value");
			var pwd = $("#log_pwd").attr("value");
			var dataString = "uname="+uname+"&pwd="+pwd;
			
				$.ajax({
					url: "php/checklogin.php",
					data: dataString,
					dataType: "html",
					type: "post",
					success:function(data) {
						if(data.toString()==1){
							$("#ajax_result2").html("<p>Username and Password does not match. Try again.</p>");
							$("#ajax_result2 p").css("background-color","#F5DEB3");
						}
						else if(data.toString()==2){
							$("#ajax_result2").html("<p>Account is still not activated.</p>");
							$("#ajax_result2 p").css("background-color","#F5DEB3");
						}
						else if(data.toString()=="admin"){
							alert("You are now logged-in.");
							window.location.replace("admin.php");
						}
						else if(data.toString()=="student"){
							alert("You are now logged-in.");
							window.location.replace("index.php");
						}
						else if(data.toString()=="faculty"){
							alert("You are now logged-in.");
							window.location.replace("faculty.php");
						}
						else{
						$("#ajax_result2").html("<p>Error in processing request. Try again.</p>"); 
						$("#ajax_result2 p").css("background-color","#F5DEB3");
						}
					}
				});
				/*$("#ajax_result2").append("<p>Error in processing request. Try again.</p>"); 
				$("#ajax_result2 p").css("background-color","#F5DEB3");
				*/
				return false;

			}
			else{
				$("#ajax_result2").append("<p>Username and Password should not be blank.</p>"); 
				$("#ajax_result2 p").css("background-color","#F5DEB3");
				return false;
			}
		});	
		
		$("#form_reg").submit(function(event) {	
		
		/* stop form from submitting normally */
        event.preventDefault(); 
		
			var err = 0;
			$("#ajax_result").html("");
			$('#reg').find(':input.required').each(function(){
				if ($(this).val() == ""){
					$(this).css("border-color","red");
					err = 1;
				}
				else $(this).css("border-color","#ddd");
			})
			
			if(err == 1) return false;
			else{
			var uname = $("#reg_uname").attr("value");
			var pwd = $("#reg_pwd").attr("value");
			var fname = $("#reg_fname").attr("value");
			var mname = $("#reg_mname").attr("value");
			var lname = $("#reg_lname").attr("value");
			var suffix = $("#reg_suffix").attr("value");
			var email = $("#reg_email").attr("value");
			var cnum = $("#reg_cnum").attr("value");
			var sex = $("#form_reg input[name='sex']:checked").val();
			var type = $("#form_reg input[name='type']:checked").val();
			if(type=="student"){
				var studno1 = $("#reg_studnum1").attr("value");
				var studno2 = $("#reg_studnum2").attr("value");
				var studno = studno1 + studno2;
				var college = $("#reg_college").attr("value");
				var course = $("#reg_course").attr("value");
				
				//alert(uname + " " + pwd + " " + fname + " " + mname + " " + lname + " " +suffix + " " +email + " " +cnum + " " +sex+ " " +type + " " + studno + " " + college + " " + course);
				
				//Verify data fetched
				if(alphanumeric($("#reg_uname"),"Username",uname) && alphanumeric($("#reg_pwd"),"Password",pwd) && allLetterSpace($("#reg_fname"),"Firstname",fname) && allLetterSpace($("#reg_mname"),"Middlename",mname) && allLetterSpace($("#reg_lname"),"Lastname",fname) && allDigit($("#reg_cnum"),"Contact no",cnum) && ValidateEmail($("#reg_email"),email) && allDigit($("#reg_studnum1"),"Student number",studno1) && allDigit($("#reg_studnum2"),"Student number",studno2) && checkLength($("#reg_studnum1"),"Student no",studno1,4,4) && checkLength($("#reg_studnum2"),"Student no",studno2,5,5) && allLetterSpace($("#reg_college"),"College",college) && allLetterSpace($("#reg_course"),"Course",course)){
					//process data fetched
					var dataString = "username=" + uname + "&password=" + pwd + "&fname=" + fname + "&mname=" + mname + "&lname=" + lname + "&suffix=" +suffix + "&email=" +email + "&cnum=" +cnum + "&sex=" +sex+ "&type=" +type + "&studnum1=" + studno1 + "&studnum2=" + studno2 + "&college=" + college + "&course=" + course;
					//alert(dataString);
					$.ajax({
						type: "POST",
						url: "php/user_register.php",  //where u want to send the data.
						data: dataString,
						dataType: "html",
						success: function(data)
						{
							//write ur code which u want to perform after submittion of form.
							$("#ajax_result").html(data);
							$("#ajax_result p").css("background-color","#F5DEB3");
							//$("#ajax_result").focus();

						}	
					});
					return false;
				}
				else{
					$("#ajax_result p").css("background-color","#F5DEB3");
					return false;
				}
			
			}
			else{
				//alert(uname + " " + pwd + " " + fname + " " + mname + " " + lname + " " +suffix + " " +email + " " +cnum + " " +sex+ " " +type);
				
								//Verify data fetched
				if(alphanumeric($("#reg_uname"),"Username",uname) && alphanumeric($("#reg_pwd"),"Password",pwd) && allLetterSpace($("#reg_fname"),"Firstname",fname) && allLetterSpace($("#reg_mname"),"Middlename",mname) && allLetterSpace($("#reg_lname"),"Lastname",fname) && allDigit($("#reg_cnum"),"Contact no",cnum) && ValidateEmail($("#reg_email"),email)){
					var dataString = "username=" + uname + "&password=" + pwd + "&fname=" + fname + "&mname=" + mname + "&lname=" + lname + "&suffix=" +suffix + "&email=" +email + "&cnum=" +cnum + "&sex=" +sex+ "&type=" +type;
					//alert(dataString);
					$.ajax({
						type: "POST",
						url: "php/user_register.php",  //where u want to send the data.
						data: dataString,
						dataType: "html",
						success: function(data)
						{
							//write ur code which u want to perform after submittion of form.
							$("#ajax_result").html(data);
							$("#ajax_result p").css("background-color","#F5DEB3");
							//$("#ajax_result").focus();
							
						}	
					});
					return false;
				}
				else{
					$("#ajax_result p").css("background-color","#F5DEB3");
					return false;
				}
			}
			}
			
		});
		
//end of document ready	
});

function checkLength(form,f,s,min,max)  
{  
    var len = s.length;  
    if (len == 0 || len > max || len < min)  
    {  
    $("#ajax_result").append("<p>" + f + " should only have length between " + min + "-" + max + ".</p>");   
	form.focus();
    return false;  
    }  
    return true;  
}  

//check alpha
function alphanumeric(form,f,s)  
{   
	var letters = /^[0-9a-zA-Z]+$/; 
	if(s.match(letters))  
	{  
	return true;  
	}  
	else  
	{  
	$("#ajax_result").append("<p>" + f+' must have alphanumeric characters only.</p>'); 
	form.focus();	
	return false;  
	}  
}


function allLetter(form,f,s)  
{   
	var letters = /^[A-Za-z]+$/;  
	if(s.match(letters))  
	{  
	return true;  
	}  
	else  
	{  
	$("#ajax_result").append("<p>" + f+' must have alphabet characters only.</p>');  
	form.focus();	
	return false;  
	}  
} 

function allLetterSpace(form,f,s)  
{   
	var letters = /^[A-Za-z ]+$/;  
	if(s.match(letters))  
	{  
	return true;  
	}  
	else  
	{  
	$("#ajax_result").append("<p>" + f+' must have alphabet and space characters only.</p>');  
	form.focus();	
	return false;  
	}  
} 

function allDigit(form,f,s){
	var letters = /^\d+$/;
	if(s.match(letters))  
	{  
	return true;  
	}  
	else  
	{  
	$("#ajax_result").append("<p>" + f+' must have numeric characters only.</p>'); 
	form.focus();	
	return false;  
	}  
}

function ValidateEmail(form,uemail)  
{  
    var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;  
    if(uemail.match(mailformat))  
    {  
    return true;  
    }  
    else  
    {  
    $("#ajax_result").append("<p>You have entered an invalid email address!</p>"); 
	form.focus();	
    return false;  
    }  
} 


$(document).ready(function() {
	$("#nav-tabs li a").click(function() {
	$('#nav-tabs li a').css('background-color','#A4C639');
	$(this).css('background-color','#99CC99');
	if($(this).text() == "Log-out") {}		//excempt log-out from ajax
	else if($(this).text() == "Preview") {}
	else{
        $("#ajax-content").empty().append("<div id='loading'><img src='images/loading.gif' alt='Loading...'/></div>");
        $("#nav li a").removeClass('current');
        $(this).addClass('current');
 
        $.ajax({ url: this.href, success: function(php) {
            $("#ajax-content").empty().append(php);
			$( ".accordion" ).accordion();
            }
    });
    return false;
    }});
 
    $("#ajax-content").empty().append("<div id='loading'><img src='images/loading.gif' alt='Loading...' /></div>");
		$.ajax({ url: 'ajax/browse.php', success: function(php) {
				$("#nav-tabs li a:first").css('background-color','#99CC99');
				$("#ajax-content").empty().append(php);
							$( ".accordion" ).accordion();
		}
		});
	
	$("#tabs li").live("click",function() {
        //  First remove class "active" from currently active tab
        $("#tabs li").removeClass('active');
 
        //  Now add class "active" to the selected/clicked tab
        $(this).addClass("active");
 
        //  Hide all tab content
        $(".tab_content").hide();
 
        //  Here we get the href value of the selected tab
        var selected_tab = $(this).find("a").attr("href");
 
        //  Show the selected tab content
        $(selected_tab).fadeIn();
 
        //  At the end, we add return false so that the click on the link is not executed
        return false;
    });
	
	$('body').on('click', 'input.datepicker',function() {
		$(this).datepicker({ minDate: '0' }).focus();
	});
	
	/*$('body').on('click', 'input.timepicker',function() {
			$(this).ptTimeSelect().focus();
	});
	*/
	
	$('body').on('click', 'input.timepicker_start',function() {
			$(this).timepicker({
			showLeadingZero: false,
			onHourShow: tpStartOnHourShowCallback,
			onMinuteShow: tpStartOnMinuteShowCallback
			}).focus();
	});
	
	
    $('body').on('click', 'input.timepicker_end',function() {
		$(this).timepicker({
        showLeadingZero: false,
        onHourShow: tpEndOnHourShowCallback,
        onMinuteShow: tpEndOnMinuteShowCallback
		}).focus();
	});
	
	 $(function() {
		$( ".accordion" ).accordion();
	});
	
	$(function() {
    $( "#eventtabs" ).tabs();
  });
	
	
	$(function() {
	  $(".accordion2 tr:not(.accordionbaby)").hide();
	  $(".accordion2 tr.accordionbaby").show();

	  $(".accordion2 tr.accordionbaby").click(function(){
		  $(this).next("tr:not(.accordionbaby)").toggle(500);
	  });
	});
	
	
	$("#addDate").live("click",function() {
	var newRow = '<tr><td><input type="text" name="date[]" class="datepicker required" pattern="(0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01])[- /.](19|20)[0-9][0-9]" readonly /></td><td><input type="text" name="start[]" style="width: 70px" class="timepicker_start required" pattern="([0-9]|1[0-9]|2[0-3]):([0-5][0-9])" readonly /></td><td>to</td><td><input type="text" name="end[]" style="width: 70px" class="timepicker_end required" pattern="([0-9]|1[0-9]|2[0-3]):([0-5][0-9])" readonly /></td><td><input type="number" name="max[]" class="required" pattern="[1-9][0-9]*" required /></td><td><a href="#" class="deleteDate">Delete</a></td></tr>';
	$('table#eventDate').append(newRow);
    });

	$(".deleteDate").live("click", function(){
		var rowCount = $('#eventDate tr').length;
		if(rowCount > 2) $(this).parent().parent().remove();
	});
	
	
	//function for adding new ticket class
	$("#addcClass").live("click", function (e) {
			e.preventDefault();
			var t_class = $("#t_class").val();
			var t_price = $("#t_price").val();

			//check if blank and check if double
			if(t_class==="" || t_price=="")
			{
				$('#error_tab2').attr("class", "errorDiv");
				$("#error_tab2").text("Please fill in the Class and Price field.");
				return false;
			}
			
			if(t_price.match(/^\d+\.\d+$/) || t_price.match(/^\d+$/)) {
				var myData = 'class='+ t_class + '&price=' + t_price; //build a post data structure
				jQuery.ajax({
					type: "POST", // Post / Get method
					url: "php/addTicketClass.php", //Where form data is sent on submission
					dataType:"text", // Data type, HTML, json etc.
					data:myData, //Form variables
					success:function(response){
						$("#ticket_class tr:last").before(response);
						$('#error_tab2').attr("class", "successDiv");
						$("#error_tab2").text("New ticket class successfully added.");
						$("#t_class").val('');
						$("#t_price").val('');
					},
					error:function (xhr, ajaxOptions, thrownError){
						$('#error_tab2').attr("class", "errorDiv");
						$("#error_tab2").text(thrownError);
					}
				});
			}
			else{
			$('#error_tab2').attr("class", "errorDiv");
			$("#error_tab2").text("Price is not float.");
			return false;
			}
	});
	//end function
	
	//function for adding new venue
	$("#addVenue").live("click", function (e) {
			e.preventDefault();
			var venue = $("#v_name").val();

			//check if blank and check if double
			if(venue=="")
			{
				$('#errorVenue').attr("class", "errorDiv");
				$("#errorVenue").text("Please fill in the Venue field.");
				return false;
			}
				var myData = 'venue='+ venue; //build a post data structure
				jQuery.ajax({
					type: "POST", // Post / Get method
					url: "php/addVenue.php", //Where form data is sent on submission
					dataType:"text", // Data type, HTML, json etc.
					data:myData, //Form variables
					success:function(response){
						$("#table_venue tr:last").before(response);
						$('#errorVenue').attr("class", "successDiv");
						$("#errorVenue").text("New Venue class successfully added.");
						$("#v_name").val('');
					},
					error:function (xhr, ajaxOptions, thrownError){
						$('#errorVenue').attr("class", "errorDiv");
						$("#errorVenue").text(thrownError);
					}
				});
			
	});
	//end function
	//function for creating event
	$(document).on('submit', '#form_eDetails', function(e){
		e.preventDefault();
		
		var date_array = new Array();
		var start_array = new Array();
		var end_array = new Array();
		var max_array = new Array();
		var class_array = new Array();
		
		//check schedule
			var rows = $("#eventDate tr:gt(0)"); // skip the header row
			rows.each(function(index){
				var date = $("td:nth-child(1) input", this).val();
				var start = $("td:nth-child(2) input", this).val();
				var end = $("td:nth-child(4) input", this).val();
				var max = $("td:nth-child(5) input", this).val();
				if(date=="" || start=="" || end == ""){
				$("#error_edetails").html("<p style='background-color:#F5DEB3;'>Please complete schedule details.</p>");
				exit;
				}
				date_array.push(date);
				start_array.push(start);
				end_array.push(end);
				max_array.push(max);
								
				$("td:nth-child(7) input", this).val(date);
				$("td:nth-child(8) input", this).val(start);
				$("td:nth-child(9) input", this).val(end);
				$("td:nth-child(10) input", this).val(max);
			});

				$("#form_eDetails input:checkbox").each(function () {
					if ($(this).is(":checked")) {
						class_array.push($(this).val());
					}
				});

				if(jQuery('#form_eDetails input[type=checkbox]:checked').length<1){
					$("#error_edetails").html("<p style='background-color:#F5DEB3;'>Select at least one ticket class for the event.</p>");
					return false;
				}
				else{
		//show dialog box
				var info = "<h3>Event information</h3><table class='table_center'><tr><td>Venue</td></tr><tr><td>"+ $("#event_venue option:selected").text(); +"</td></tr><tr><td>Name</td></tr><tr><td>"+ $('#event_name').val() +"</td></tr>";
				var i;
				if($('#event_desc').val() != "") info = info + "<tr><td>Description</td></tr><tr><td>"+ $('#event_desc').val() +"</td></tr>";
				info = info + "<tr><td>Schedule</td></tr>";
				info = info + "<tr><td><ul>";
				for(i = 0; i< date_array.length; i++){
					info = info + "<li>"+ date_array[i] +" | "+ start_array[i] +" - "+ end_array[i] +" | "+ max_array[i] +"</li>";
				}
				info = info + "</ul></td></tr>";
				info = info + "<tr><td>Ticket class:</td></tr>";
				info = info + "<tr><td><ol>";
				for(i=0;i<class_array.length;i++){
					info = info + "<li>" + class_array[i] + "</li>";
				}
				info = info + "</ol></td></tr>";
				if($('#event_image').val()!= "") info = info + "<tr><td>Image URL: </td></tr><tr><td>"+$('#event_image').val()+"</td></tr>";
				if($('#lat').val()!= "") info = info + "<tr><td>Latitude: </td></tr><tr><td>"+$('#lat').val()+"</td></tr>";
				if($('#long').val()!= "") info = info + "<tr><td>Longitude: </td></tr><tr><td>"+$('#long').val()+"</td></tr>";
				info = info + "</table>";
								
				$("#event_info").html(info + '<button id="final_eButton">Ok</button><input type="button" value="Cancel" id="cancel_eButton">');
				el = document.getElementById("overlay");
				el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";
				$("#overlay").css("height",$(document).height());
				return false;
				}
	});
	
	$("#cancel_eButton").live("click",function(){
		el = document.getElementById("overlay");
		el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";
	});

	
	$("#final_eButton").live("click",function(){
	
		el = document.getElementById("overlay");
		el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";
		document.forms["form_eDetails"].submit();
		
			/*var e_venue = $("#event_venue option:selected").val();	//get venue, must not be null
			
			var e_name = $("#event_name").val();
			var e_desc = $("#event_desc").val();
			var intRegex = /^\d+$/;
			var date_array = new Array();
			var start_array = new Array();
			var end_array = new Array();
			var max_array = new Array();
			var class_array = new Array();

		//check schdedule
			var rows = $("#eventDate tr:gt(0)"); // skip the header row
			rows.each(function(index){
				var date = $("td:nth-child(1) input", this).val();
				var start = $("td:nth-child(2) input", this).val();
				var end = $("td:nth-child(4) input", this).val();
				var max = $("td:nth-child(5) input", this).val();
				date_array.push(date);
				start_array.push(start);
				end_array.push(end);
				max_array.push(max);
			});
			$("#form_tClass input:checkbox").each(function () {
				if ($(this).is(":checked")) {
					class_array.push($(this).val());
				}
			});
					
			var edate = JSON.stringify(date_array);
			//edate = encodeURIComponent(edate);
			var estart = JSON.stringify(start_array);
			//estart = encodeURIComponent(estart);
			var eend = JSON.stringify(end_array);
			//eend = encodeURIComponent(eend);
			var emax = JSON.stringify(max_array);
			//emax = encodeURIComponent(emax);
			var eclass = JSON.stringify(class_array);
		*/	
		/*
			$.ajax({
				type: "POST",
				url: "php/addEvent2.php",
				//data : { venue : e_venue, name : e_name, desc : e_desc, date : edate, start : estart, end : eend, max : emax, eclass : eclass},
				data: $("#form_eDetails").serialize(),
				success: function(msg){
				el = document.getElementById("overlay");
				el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";
				$("#error_edetails").html(msg);
				}
			});
	*/
	});
	
	
//for browsing event
 $('.book_ticket').live('click',function(){
	//alert($(this).attr('name'));
			var row = $(this).parent().parent(); // skip the header row
			var date = $("td:nth-child(1)", row).html();
			var time = $("td:nth-child(2)", row).html();
			el = document.getElementById("overlay2");
			$('#event_info2 h4').html(date+" "+time);
			el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";
			$("#overlay2").css("height",$(document).height());
			$("#eventSched_id").val($(this).parent().parent().attr('name'));

 });
 
  $('#cancel_book').live('click',function(){
			el = document.getElementById("overlay2");
			el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";

 });
//end for browsing event
	$('#cancel_booking2').live('click',function(){
		var id = $(this).attr('name');
		 $( "#dialog-confirm" ).dialog({
				resizable: false,
				height:140,
				modal: true,
				buttons: {
				"Delete": function() {
					$( this ).dialog( "close" );
					var myData = "book_id="+id;
					jQuery.ajax({
						type: "POST", // Post / Get method
						url: "php/cancel_book.php", //Where form data is sent on submission
						dataType:"text", // Data type, HTML, json etc.
						data:myData, //Form variables
						success:function(response){
							if(response == "1"){					//cancel_book.php returns 1=cancelled
								alert("Booking cancelled!");
								window.location.replace("admin.php");
							}
							else alert("Error in cancellatin of Booking.");	//2=not
						}
					/*error:function (xhr, ajaxOptions, thrownError){
						
					}*/
					});
				},
				Cancel: function() {
				$( this ).dialog( "close" );
				}
				}
				});
	});

	
	$('.viewClass').live('click',function(){
		var row = $(this).parent().parent(); // skip the header row
		var id = $(this).parent().parent().attr("id"); // skip the header row
		var studId = $("#studidClass").val();
		var myData = "id="+id+"&studId="+studId;
		//$(this).parent().parent().after("<tr><td>sample</td></tr><tr><td>sample</td></tr>");
		jQuery.ajax({
						type: "POST", // Post / Get method
						url: "php/viewStudClass.php", //Where form data is sent on submission
						dataType:"text", // Data type, HTML, json etc.
						data:myData, //Form variables
						success:function(response){
							alert(id);
							row.innerHTML = row.innerHTML + "<tr><td>haha/td></tr>";
						}
					/*error:function (xhr, ajaxOptions, thrownError){
						
					}*/
					});
	});
	
	$('#cancel_booking').live('click',function(){
		var id2 = $(this).attr('name');
		var row = $(this).parent().parent(); // skip the header row
		var event = $("td:nth-child(1)", row).html();
		var date = $("td:nth-child(2)", row).html();
		var time1 = $("td:nth-child(3)", row).html();
		var dataString = "<input type='hidden' value='"+id2+"' id='cancel_book_id'/>" + event + " " + date + " " + time1;
		$("#book_infoHead").html(dataString);
		/*el = document.getElementById("overlay3");
		el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";
		$("#overlay3").css("height",$(document).height());*/
		$( "#dialog-confirm4" ).dialog({
				resizable: false,
				height:200,
				modal: true,
				buttons: {
				"Delete": function() {
					$( this ).dialog( "close" );
					var id = $("#cancel_book_id").val();
					var myData = "book_id="+id;
					jQuery.ajax({
								type: "POST", // Post / Get method
								url: "php/cancel_book.php", //Where form data is sent on submission
								dataType:"text", // Data type, HTML, json etc.
								data:myData, //Form variables
								success:function(response){
									if(response == "1"){					//cancel_book.php returns 1=cancelled
										alert("Booking cancelled!");
										$("#listBooking tr#" + id).remove();
									}
									else alert("Error in cancellatin of Booking.");	//2=not
								}
								/*error:function (xhr, ajaxOptions, thrownError){
									
								}*/
					});
				},
				Cancel: function() {
				$( this ).dialog( "close" );
				}
				}
				});
	});
	
	$('.edit_event').live('click',function(){
		var id = $(this).attr('name');
	        $("#ajax-content").empty().append("<div id='loading'><img src='images/loading.gif' alt='Loading...'/></div>");
            $.ajax({ 
				url: 'ajax/editEvent.php',
				data: 'id='+id, //Form variable
				success: function(php) {
					$("#ajax-content").empty().append(php);
					$( ".accordion" ).accordion();
				}
			})
	});
	
	$('.delete_event').live('click',function(){
	var id = $(this).attr("name");
				$( "#dialog-confirm2" ).dialog({
				resizable: false,
				height:140,
				modal: true,
				buttons: {
				"Delete": function() {
					$( this ).dialog( "close" );
					var myData = "event_id="+id;
					jQuery.ajax({
						type: "POST", // Post / Get method
						url: "php/delete_event.php", //Where form data is sent on submission
						dataType:"text", // Data type, HTML, json etc.
						data:myData, //Form variables
						success:function(response){
							if(response == "1"){					//cancel_book.php returns 1=cancelled
								alert("Event cancelled!");
								window.location.replace("admin.php");
							}
							else alert("Error in cancellatin of Event.");	//2=not
						}
					/*error:function (xhr, ajaxOptions, thrownError){
						
					}*/
					});
				},
				Cancel: function() {
				$( this ).dialog( "close" );
				}
				}
				});
	});
	
	$('.delete_stud').live('click',function(){
	var id = $(this).attr("name");
				$( "#dialog-confirm3" ).dialog({
				resizable: false,
				height:140,
				modal: true,
				buttons: {
				"Delete": function() {
					$( this ).dialog( "close" );
					var myData = "user_id="+id+"&type=stud";
					jQuery.ajax({
						type: "POST", // Post / Get method
						url: "php/delete_user.php", //Where form data is sent on submission
						dataType:"text", // Data type, HTML, json etc.
						data:myData, //Form variables
						success:function(response){
							if(response == "1"){					//cancel_book.php returns 1=cancelled
								alert("User deleted!");
								$.ajax({ 
									url: 'ajax/userMgt.php',
									success: function(php) {
										$("#ajax-content").empty().append(php);
										$( ".accordion" ).accordion();
									}
								})
							}
							else alert("Error in deletion of User.");	//2=not
						}
					/*error:function (xhr, ajaxOptions, thrownError){
						
					}*/
					});
				},
				Cancel: function() {
				$( this ).dialog( "close" );
				}
				}
				});
	});
	
	$('.delete_fac').live('click',function(){
	var id = $(this).attr("name");
				$( "#dialog-confirm3" ).dialog({
				resizable: false,
				height:140,
				modal: true,
				buttons: {
				"Delete": function() {
					$( this ).dialog( "close" );
					var myData = "user_id="+id+"&type=fac";
					jQuery.ajax({
						type: "POST", // Post / Get method
						url: "php/delete_user.php", //Where form data is sent on submission
						dataType:"text", // Data type, HTML, json etc.
						data:myData, //Form variables
						success:function(response){
							if(response == "1"){					//cancel_book.php returns 1=cancelled
								alert("User deleted!");
								$.ajax({ 
									url: 'ajax/userMgt.php',
									success: function(php) {
										$("#ajax-content").empty().append(php);
										$( ".accordion" ).accordion();
									}
								})
							}
							else alert("Error in deletion of User.");	//2=not
						}
					/*error:function (xhr, ajaxOptions, thrownError){
						
					}*/
					});
				},
				Cancel: function() {
				$( this ).dialog( "close" );
				}
				}
				});
	});
	
	
	$('#approve_stud').live('click',function(){
		var row = $(this).parent().parent();
		var id = $(this).parent().parent().attr("id"); // skip the header row
		var myData = "user_id="+id;
		$("#loading2").empty().append("<img src='images/loader.gif' alt='Loading...'/>");
		jQuery.ajax({
					type: "POST", // Post / Get method
					url: "php/approveUser.php", //Where form data is sent on submission
					dataType:"text", // Data type, HTML, json etc.
					data:myData, //Form variables
					success:function(response){
						$("#loading2").empty();
						if(response == "1"){
							alert("User approved");
							$("td:nth-child(5)", row).html("approved");
							$("td:nth-child(6)", row).html("");
						}
						else if(response == "3") alert("Error in sending mail, requests not approved.");
						else alert("Error in approving requests.");
					}
					/*error:function (xhr, ajaxOptions, thrownError){
						
					}*/
				});
		
	});
	
	$(document).on('submit', '#addFaculty', function(event) {
		event.preventDefault(); 
		$("#loading3").empty().append("<img src='images/loader.gif' alt='Loading...'/>");
		$("#fac_result").html("");
	
		$.ajax({
						type: "POST",
						url: "php/user_register2.php",  //where u want to send the data.
						data: $(this).serialize(),
						dataType: "html",
						success: function(data)
						{
							//write ur code which u want to perform after submittion of form.
							if(data == "1"){
							$("#fac_result").html("<tr><td colspan=4><p>Username already exists!</p></td></tr>");
							$("#fac_result p").css("background-color","#F5DEB3");
							}
							else if(data == "2"){
							$("#fac_result").html("<tr><td colspan=4><p>Name already exists!</p></td></tr>");
							$("#fac_result p").css("background-color","#F5DEB3");
							}
							else if(data == "3"){
							$("#fac_result").html("<tr><td colspan=4><p>Error in adding faculty, try again later!</p></td></tr>");
							$("#fac_result p").css("background-color","#F5DEB3");
							}								
							else $("#facList tr:last").after(data);
							
							//$("#ajax_result").focus();
						}	
		});
		$("#loading3").empty();   
		return false;
		
	});
	
	$(document).on('submit', '#assocClassForm', function(event) {
		event.preventDefault(); 
		$("#class_result").html("");
		if(jQuery('#assocClassForm input[type=checkbox]:checked').length<1) {
			$("#class_result").html("<p>Check at least one event to associate with your class.</p>");
			$("#class_result p").css("background-color","#F5DEB3");
		}
		else{
			$.ajax({
						type: "POST",
						url: "php/assocClass.php",  //where u want to send the data.
						data: $(this).serialize(),
						dataType: "html",
						success: function(data)
						{
							//write ur code which u want to perform after submittion of form.
							$("#class_result").html(data);
							
						}	
			});
		}
	});
	
	
}); //document.ready function

function show_alert(msg) {
            alert(msg);
}

function validateBooking(){
	$("#error_bdetails").text("");
	var err=0;
	var tclass = $('input[name=ticketclass]:checked', '#book_form').val();
	var fname = $('#u_fname').val();		//from session
	var mname = $('#u_mname').val();
	var lname = $('#u_lname').val();
	var gender = $('#gender_row input[name=u_gender]:checked').val();
	var num = $('#u_num').val();
	var email = $('#u_email').val();
	var snum1 = $('#u_snum1').val();		//from session
	var snum2 = $('#u_snum2').val();
	var intRegex = /^\d+$/;
	var emailRegex = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$/;
	//alert(tclass + " " + fname + " " + mname + " " + lname + " " + gender + " " + num + " " + email + " " + snum1 + " " + snum2);
	$("#book_form input.required").each(function() {
			$(this).css("border","solid 1px #fff");
			if($(this).val()==""){
				$(this).css("border","solid 1px red");
				err = 1;
			}
	});
	
	if(err==1) return false;
	else if(!intRegex.test(num)){
				$('#error_bdetails').attr("class", "errorDiv");
				$("#error_bdetails").text("Contact number should only contain numeric characters.");
				return false;
	}
	else if(!emailRegex.test(email)){
				$('#error_bdetails').attr("class", "errorDiv");
				$("#error_bdetails").text("Invalid email.");
				return false;
	}
	return true;
}

function tpStartOnHourShowCallback(hour) {
	var row = $(this).parent().parent();
    var tpEndHour = $('.timepicker_end',row).timepicker('getHour');
    // all valid if no end time selected
    if ($('.timepicker_end',row).val() == '') { return true; }
	//if(tpEndHour == "-1"){ return true; }
    // Check if proposed hour is prior or equal to selected end time hour
    if (hour <= tpEndHour) { return true; }
    // if hour did not match, it can not be selected
    return false;
}
function tpStartOnMinuteShowCallback(hour, minute) {
	var row = $(this).parent().parent();
    var tpEndHour = $('.timepicker_end',row).timepicker('getHour');
    var tpEndMinute = $('.timepicker_end',row).timepicker('getMinute');
    // all valid if no end time selected
    if ($('.timepicker_end',row).val() == '') { return true; }
    // Check if proposed hour is prior to selected end time hour
    if (hour < tpEndHour) { return true; }
    // Check if proposed hour is equal to selected end time hour and minutes is prior
    if ( (hour == tpEndHour) && (minute < tpEndMinute) ) { return true; }
    // if minute did not match, it can not be selected
    return false;
}

function tpEndOnHourShowCallback(hour) {
	var row = $(this).parent().parent();
    var tpStartHour = $('.timepicker_start',row).timepicker('getHour');
    // all valid if no start time selected
    if ($('.timepicker_start',row).val() == '') { return true; }
    // Check if proposed hour is after or equal to selected start time hour
    if (hour >= tpStartHour) { return true; }
    // if hour did not match, it can not be selected
    return false;
}
function tpEndOnMinuteShowCallback(hour, minute) {
	var row = $(this).parent().parent();
    var tpStartHour = $('.timepicker_start',row).timepicker('getHour');
    var tpStartMinute = $('.timepicker_start',row).timepicker('getMinute');
    // all valid if no start time selected
    if ($('.timepicker_start',row).val() == '') { return true; }
    // Check if proposed hour is after selected start time hour
    if (hour > tpStartHour) { return true; }
    // Check if proposed hour is equal to selected start time hour and minutes is after
    if ( (hour == tpStartHour) && (minute > tpStartMinute) ) { return true; }
    // if minute did not match, it can not be selected
    return false;
}

//add map
var marker,map;
function placeMarker(location) {

  if ( marker ) {
    marker.setPosition(location);
  } else {
    marker = new google.maps.Marker({
      position: location,
      map: map
    });
  }
}

function initialize() {

  var mapOptions = {
    zoom: 18,
    center: new google.maps.LatLng(14.16754, 121.24328),
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };
  map = new google.maps.Map(document.getElementById('map-canvas'),
      mapOptions);
	  placeMarker(mapOptions.center);
	  google.maps.event.addListener(map, 'click', function(event){
		placeMarker(event.latLng);
		document.getElementById("lat").value = event.latLng.lat();
		document.getElementById("long").value = event.latLng.lng();
		});
}

function initialize2(lat,lng) {
  var mapOptions = {
    zoom: 18,
    center: new google.maps.LatLng(lat, lng),
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };
  map = new google.maps.Map(document.getElementById('map-canvas2'),
      mapOptions);
  placeMarker(mapOptions.center);
  $( "#dialog-map" ).dialog({
				resizable: false,
				height:500,
				width:500,
				modal: true,
				buttons: {
				"Ok": function() {
					$( this ).dialog( "close" );
				},
				Cancel: function() {
				$( this ).dialog( "close" );
				}
				}
	});
}

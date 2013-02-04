$(document).ready(function() {
	$("#nav-tabs li a").click(function() {
	if($(this).text() == "Log-out") {}		//excempt log-out from ajax
	else{
        $("#ajax-content").empty().append("<div id='loading'><img src='images/loading.gif' alt='Loading...'/></div>");
        $("#nav li a").removeClass('current');
        $(this).addClass('current');
 
        $.ajax({ url: this.href, success: function(php) {
            $("#ajax-content").empty().append(php);
            }
    });
    return false;
    }});
 
    $("#ajax-content").empty().append("<div id='loading'><img src='images/loading.gif' alt='Loading...' /></div>");
    $.ajax({ url: 'ajax/browse.php', success: function(php) {
            $("#ajax-content").empty().append(php);
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
		$(this).datepicker().focus();
	});
	
	$('body').on('click', 'input.timepicker',function() {
			$(this).ptTimeSelect().focus();
	});
	
	$("#addDate").live("click",function() {
	var newRow = jQuery('<tr><td><input type="text" class="datepicker required"/></td><td><input type="text" class="timepicker required"/></td><td>to</td><td><input type="text" class="timepicker required"/></td><td><input type="text" class="required"/></td><td><a href="#" class="deleteDate">Delete</a></tr>');
    jQuery('table#eventDate').append(newRow);
    });

	$(".deleteDate").live("click", function(){
		var rowCount = $('#eventDate tr').length;
		if(rowCount > 2) $(this).parent().parent().remove();
	});
	
	
	//function for adding new ticket class
	$("#addTicketClass").live("click", function (e) {
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
	$("#create_eButton").live("click", function(){
		var err = 0;
		var listItem = $( '#tabs li' ).first(); // tab 1
		var nextSibling = listItem.next(); // tab 2
		var date_array = new Array();
		var start_array = new Array();
		var end_array = new Array();
		var max_array = new Array();
		var class_array = new Array();
		
		
		$("#tabs li").removeClass('active');
		listItem.addClass("active");
		$(".tab_content").hide();
		var selected_tab = listItem.find("a").attr("href");
		$(selected_tab).fadeIn();
		
		//highlight fields that are blank
		$("#form_eDetails input.required").each(function() {
			$(this).css("border","solid 1px #999");
			if($(this).val()==""){
				$(this).css("border","solid 1px red");
				err = 1;
			}
		});
		
		if(err == 1 ){
			$("#error_edetails").attr("class", "errorDiv");
			$("#error_edetails").text("Please complete the required fields.");
			return false;
		}

		else{
			var e_venue = $("#event_venue option:selected").val();	//get venue, must not be null
			var e_name = $("#event_name").val();
			var e_desc = $("#event_desc").val();
			var intRegex = /^\d+$/;
		
		//check schdedule
			var rows = $("#eventDate tr:gt(0)"); // skip the header row
			rows.each(function(index){
				var date = $("td:nth-child(1) input", this).val();
				var start = $("td:nth-child(2) input", this).val();
				var end = $("td:nth-child(4) input", this).val();
				var max = $("td:nth-child(5) input", this).val();
				
				
				//window.alert(date + " " + start + " " + end + " " + max);
				/*check if schedule is valid*/
				if(!intRegex.test(max)){
					$("#error_edetails").attr("class", "errorDiv");
					$("#error_edetails").text("Max ticket should be a number.");
					err = 1;
					return false;
				}
				date_array.push(date);
				start_array.push(start);
				end_array.push(end);
				max_array.push(max);
				
			});
			if(err == 0){
		//check ticket class
				var atLeastOneChecked = false;
				$("#form_tClass input:checkbox").each(function () {
				if ($(this).is(":checked")) {
					class_array.push($(this).val());
					if(atLeastOneChecked == false) atLeastOneChecked = true;
				}
				});
				if(atLeastOneChecked == false){
				window.alert("not check");
					$("#tab li").removeClass('active');
					nextSibling.addClass("active");
					$(".tab_content").hide();
					var selected_tab = nextSibling.find("a").attr("href");
					$(selected_tab).fadeIn();
					$("#error_tab2").attr("class", "errorDiv");
					$("#error_tab2").text("Select at least one ticket class for the event.");
					return false;
				}
		//show dialog box
				var info = "<p>Event information:</p><table><tr><td>Venue: </td><td>"+ $("#event_venue option:selected").text(); +"</td></tr><tr><td>Name: </td><td>"+ e_name+"</td></tr>";
				var i;
				if(e_desc!="") info = info + "<tr><td>Description: </td><td>"+ e_desc +"</td></tr>";
				info = info + "<tr><td>Schedule:</td></tr>";
				for(i = 0; i< date_array.length; i++){
					info = info + "<tr><td>"+ date_array[i] +"</td><td>"+ start_array[i] +"</td><td>"+ end_array[i] +"</td><td>"+ max_array[i] +"</td></tr>";
				}
				info = info + "<tr><td>Ticket class:</td></tr>";
				for(i=0;i<class_array.length;i++){
					info = info + "<tr><td>"+ class_array[i] +"</td></tr>";
				}
				if($('#event_image').val()!= "") info = info + "<tr><td>Image URL: </td><td>"+$('#event_image').val()+"</td></tr>";
				info = info + "</table>";
								
				$("#event_info").html(info + '<form name="savemyevent" id="savemyevent" method="post"><input type="button" value="Ok" id="final_eButton"></form><input type="button" value="Cancel" id="cancel_eButton">');
				el = document.getElementById("overlay");
				el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";
				return false;
				}
				
		}

		
	});
	
	$("#cancel_eButton").live("click",function(){
		el = document.getElementById("overlay");
		el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";
	});
	
	$("#final_eButton").live("click",function(){

			var e_venue = $("#event_venue option:selected").val();	//get venue, must not be null
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
			
			$.ajax({
				type: "POST",
				url: "php/addEvent.php",
				data : { venue : e_venue, name : e_name, desc : e_desc, date : edate, start : estart, end : eend, max : emax, eclass : eclass},
				success: function(msg){
				el = document.getElementById("overlay");
				el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";
				alert( msg );
				window.location = "admin.php";
				}
			});
	
	});	

}); //document.ready function
	


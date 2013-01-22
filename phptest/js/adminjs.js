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
	var newRow = jQuery('<tr><td><input type="text" class="datepicker"/></td><td><input type="text" class="timepicker"/>to<input type="text" class="timepicker"/></td><td><input type="text"/></td><td><a href="#">Delete</a></tr>');
    jQuery('table#eventDate').append(newRow);
    });
	
});	


	


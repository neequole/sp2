/* Set serviceMode to true to create your own shapes: */
var serviceMode = false;

$(document).ready(function(){
	/* This code is executed after the DOM has been completely loaded */

	var str=[];
	var perRow = 16;
	
	/* Generating the dot divs: */
	
	for(var i=0;i<152;i++)	/*152 boxes*/
	{
		str.push('<div class="dot" id="d-'+i+'" />');
	}
	
	/* Joining the array into a string and adding it to the inner html of the stage div: */
	
	$('#stage').html(str.join(''));
	
	/* Using the hover method: */

	$('#demo4 nav li a').hover(function(e){
	
		/* serviceDraw is a cut-out version of the draw function, used for shape editing and composing: */
		var first = $(this).attr('class').split(" ")[0];
		if(serviceMode)
			serviceDraw(first);
		else
			draw(first);
	}, function(e){
		
	});
	
	/* Caching the dot divs into a variable for performance: */
	dots = $('.dot');
	
	if(serviceMode)
	{
		/* If we are in service mode, show borders around the dot divs, add the export link, and listen for clicks: */
		
		dots.css({
			border:'1px solid black',
			width:dots.eq(0).width()-2,
			height:dots.eq(0).height()-2,
			cursor:'pointer'
		})
		
		$('<div/>').css({
			position:'absolute',
			bottom:-20,
			right:0
		}).html('<a href="" onclick="outputString();return false;">[Export Shape]</a>').appendTo('#stage');
		
		dots.click(function(){
			$(this).toggleClass('active');
		});
	}
	
});

var shapes={
	
	/* Each shape is described by an array of points. You can add your own shapes here,
	   just don't forget to add a coma after each array, except for the last one */
	
	/*house:[22,37,38,39,52,53,54,55,56,67,68,69,70,71,72,73,82,83,84,85,86,87,88,89,90,99,100,104,105,115,116,120,121,131,132,136,137,147,148,150,151,152,153,163,164,166,167,168,169],*/
	house:[19,22,24,25,26,27,29,33,35,36,37,38,41,43,46,48,49,51,52,54,57,58,59,60,62,65,67,69,71,73,74,75,76,79,81,84,86,90,92,95,98,100,101,102,103,105,109,111,112,113,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151],
	blog:[19,20,21,22,24,29,30,31,32,34,35,36,37,38,41,43,48,51,53,57,,58,59,60,62,67,70,72,74,75,76,79,81,86,89,91,94,95,96,97,98,100,101,102,103,105,106,107,108,110,111,112,113,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151],
	envelope:[34,35,36,37,38,39,40,41,42,43,44,50,51,52,58,59,60,66,68,69,73,74,76,82,85,86,88,89,92,98,102,103,104,108,114,119,124,130,140,146,147,148,149,150,151,152,153,154,155,156],
	web:[20,24,27,28,29,30,33,34,35,36,39,43,46,52,55,58,60,62,65,66,67,71,72,73,74,77,79,81,84,90,93,96,97,98,99,100,103,104,105,106,109,110,111,112,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151],
	aboutus:[19,20,21,23,24,25,27,28,29,31,33,35,36,37,38,40,42,44,46,48,50,52,55,57,58,59,61,62,63,65,67,69,71,74,76,78,80,82,84,86,88,90,93,95,97,99,100,101,103,104,105,107,108,109,112,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151],
	logoutButton:[20,21,22,23,24,26,30,32,33,34,35,36,39,43,46,48,51,58,59,60,61,62,66,70,71,72,73,74,77,81,85,89,96,97,98,99,100,104,108,109,110,111,112,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151],
	login:[19,23,24,25,27,28,29,30,32,34,37,38,42,44,46,51,53,54,56,57,61,63,65,67,68,70,72,74,75,76,80,82,84,87,89,91,94,95,96,97,99,100,101,103,104,105,106,108,110,113,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151]
}

var stopCounter = 0;
var dots;

function draw(shape)
{
	/* This function draws a shape from the shapes object */
	
	stopCounter++;
	var currentCounter = stopCounter;

	dots.removeClass('active').css('opacity',0);
	
	$.each(shapes[shape],function(i,j){
		setTimeout(function(){
							
			/* If a different shape animaton has been started during the showing of the current one, exit the function  */
			if(currentCounter!=stopCounter) return false;
			
			dots.eq(j).addClass('active').fadeTo('slow',0.4);
			
			/* The fade animation is scheduled for 10*i millisecond in the future: */
		},10*i);

	});
}

function serviceDraw(shape)
{
	/* A cut out version of the draw function, used in service mode */
	
	dots.removeClass('active');
	
	$.each(shapes[shape],function(i,j){
		dots.eq(j).addClass('active');
	});
}

function outputString()
{
	/* Outputs the positions of the active dot divs as a comma-separated string: */
	
	var str=[];
	$('.dot.active').each(function(){
		
		str.push(this.id.replace('d-',''));
	})
	
	prompt('Insert this string as an array in the shapes object',str.join(','));
}
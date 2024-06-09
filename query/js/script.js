/*
	Helper functions
*/
function setAsActive(elementClass)
{
	$('.step.active, .content.active').removeClass('active');
	$('.' + elementClass).addClass('active visited');
	$('#options-stats').removeClass('active');
	
	if (elementClass === 'confirm')
	{
		$('#options-stats').addClass('active');
	}
}

function onchange()
{
	// Declare and initialize variables
	var parent_id			= $(this).closest('div').attr('id');
	var linked_option_stats = $('.option-stats.' + parent_id);
	var this_value;
	
	if ($(this).attr('type') === 'checkbox') // Is checkbox
	{
		// Declare and initialize variables
		var is_checked = $(this).checked;
		
		this_value = $(this).attr('value');
		
		
		// Toggle 'changed' class on this element and linked option stats 
		// container
		if ($(this).hasClass('changed'))
		{
			$(this).removeClass('changed');
			linked_option_stats.removeClass('changed');
		}
		else
		{
			$(this).addClass('changed');
			linked_option_stats.addClass('changed');
		}
		
		// Toggle value on this element
		if (this_value === '1')
		{
			$(this).attr('value', '0');
		}
		else
		{
			$(this).attr('value', '1');
		}
		
		// Update value for linked option stat
		if (is_checked)
		{
			linked_option_stats.find('.option-value').text('No');
		}
		else
		{
			linked_option_stats.find('.option-value').text('Yes');
		}
	} 
	else														// Is text input
	{
		// Declare and initialize variables
		var original_value 	= linked_option_stats.find('.option-original-value').text();
		
		this_value			= $(this).val();
		
		
		if (this_value !== original_value)
		{
			$(this).attr('value', this_value);
			$(this).addClass('changed');
			linked_option_stats.find('.option-value').text(this_value);
			linked_option_stats.addClass('changed');
		}
		else
		{
			$(this).attr('value', original_value);
			$(this).removeClass('changed');
			linked_option_stats.find('.option-value').text(original_value);
			linked_option_stats.removeClass('changed');
		}
	}
}

function hasExtension(this_ext, exts) 
{
	return $.inArray(this_ext, exts) > -1;
}

function hasWhitespace(str)
{
	return str.indexOf(' ') !== -1;
}


/*
	Global variables
*/
var _valid_exts = ["gw", "el"];

$(document).ready(function(){

	console.log("inside document.ready()");

// initialization

	// make the active link in the nav the "query" link

	$('.query').addClass('active');


	$('#steps').on('click', '.step.visited', function(e)
	{
		setAsActive(this.className.split(/\s+/)[1]);
	});

	/**
	$(".next-page, .prev-page").on("click", function() {
    		var current = $('.content').index($('.content.active'));
    		var next = current + ($(this).hasClass('next-page') ? 1 : -1);
    		var nextSection = ['select-version','select-networks', 'options', 'confirm'][next];
    		
		if (nextSection) {
        		$(".content").removeClass("active"); // Remove active class from all sections
    			$("#" + nextSection).addClass("active"); 
        		history.pushState({ section: nextSection }, "", window.location.href); // Push state into history
    		}
	});
	**/

	$('.page-flipper > div').click(function(e)
	{
		var current = $('.content').index($('.content.active'));
		var next = current + (this.className === 'next-page button radius' ? 1 : -1);
		current = ['select-version','select-networks', 'options', 'confirm'][current];
		next = ['select-version','select-networks', 'options', 'confirm'][next];
		
		switch(current)
		{
			case "select-version":
				//history.pushState({state: "select-version"}, "", window.location.href);
				break;
			case "select-networks":
				//history.pushState({state: "select-networks"}, "", window.location.href);
				if(this.className === "next-page button radius")
				{
					if ($("#network-file-1-input")[0].files[0] === undefined)
					{
						return alert("The first file has not been selected.");
					}
				
					if ($("#network-file-2-input")[0].files[0] === undefined)
					{
						return alert("The second file has not been selected.");
					}
					//check for esim files and make sure files inputted matches esim count
				}
				break;
				
			case "options":
				//history.pushState({state: "options"}, "", window.location.href);
				var version = document.getElementById("version").value;
                                console.log("inside options block, version: " + version);
				
                                if(version == 'SANA 2.0'){
                                        var tolerance = document.getElementById('tolerance').querySelector('input');
                                        tolerance = parseFloat(tolerance.value);

                                        if( this.className === 'next-page button radius' && tolerance < 0.02){
                                                return alert("Invalid tolerance. Target tolerance must be greater than or equal to 0.02");
                                        }

                                } else {
                                        var runTime = document.getElementById('t').querySelector('input');
                                        runTime = parseFloat(runTime.value);
                                        if( this.className === 'next-page button radius' && (runTime < 0 || runTime > 20)){
                                                return alert("Invalid run time. Runtime must be less than or equal to 20 minutes");
                                        }

                                }
				break;

			case "confirm":
				//history.pushState({state: "confirm"}, "", window.location.href);
				if (this.className === "next-page button radius") 
				{
					$("li.step.visited").css("cursor", "default");
					$("ul#steps").off("click", "li.step.visited");
					setAsActive("process");
					$(window).bind("beforeunload", function(){return "The networks have not been aligned yet.";});
					var queryData = new FormData($("form")[0]);
					var version = document.getElementById("version").value;
					queryData.append('version', version);

					$.ajax(
					{
						url: "/template/process/preprocess.php",
						type: "POST",
						data: queryData,
						async: true,
						cache: false,
 					        contentType: false,
					        processData: false
					}).done(function(data, status, something)
					{
						$(window).unbind("beforeunload");
						data = JSON.parse(data);
						if (data['success'])
							window.location = data["data"]['url'];
						else {
							alert(data['status']);
							$("li.step.visited").css("cursor", "pointer");
							setAsActive("confirm");
							$(".process").removeClass("visited");
							$("ul#steps").on("click", "li.step.visited", function(){
								setAsActive(this.className.split(/\s+/)[1]);
							});
						}
					}).fail(function(x, y, z){
						alert(data['status']);
						$("li.step.visited").css("cursor", "pointer");
						setAsActive("confirm");
						$(".process").removeClass("visited");
						$("ul#steps").on("click", "li.step.visited", function(){
							setAsActive(this.className.split(/\s+/)[1]);
						});
					});
					
					return false;
				}
				
				break;
		}

		setAsActive(next);
	});

	/*
	// convert 0, 1 in checkbox inputs to true and false values

	$("input[type=checkbox]").each(function(){
		this.checked = $(this).attr("value") === 1 ? true : false;
	});
	*/


// select-networks

	/*
		Validate selected input
	*/
	$("#network-file-1-input").on('change', function(event)
	{				
		var selected_file 	= this.files[0].name;
		var extension 		= selected_file.split('.').pop().toLowerCase();

		//updating menu with selected file
		document.getElementById('select-networks').innerHTML = "select networks: " + selected_file;
		
		if (selected_file.length > 0)
	    {
		    // Validate that filename does not contain whitespace characters
		    if (hasWhitespace(selected_file))
		    {
			    // Send alert
				alert("The first selected file contains whitespace characters. Please rename " +
					  "the file or select a different file and try again.");
				
				// Reset selected file
				$("#submit-new-job-form").get(0).reset();
		    }
		    // Validate selected file extension
			else if (!hasExtension(extension, _valid_exts)) 
			{
				// Send alert
				alert("The first selected file is not a network file. Please select a different file.");
				
				// Reset selected file
				$("#submit-new-job-form").get(0).reset();
			}
			// Validate file size does not exceed 5MB
			else if(this.files[0].size / (1024 * 1024) > 5)
			{
				// Send alert
				alert("The first network file is larger than 5MB. Please select a different file.");
								
				// Reset selected file
				$("#submit-new-job-form").get(0).reset();
			}
	    }
	    else
	    {
		    alert("Whoops! You forgot to select the first network file.");
	    }
	});
	
	$("#network-file-2-input").on('change', function(event)
	{				
		var selected_file 	= this.files[0].name;
		var extension 		= selected_file.split('.').pop().toLowerCase();
		
		//update menu with network file names that are selected
		var prevHTML            = document.getElementById('select-networks').innerHTML;
                document.getElementById('select-networks').innerHTML = prevHTML + ", " + selected_file;

		if (selected_file.length > 0)
	    {
		    // Validate that filename does not contain whitespace characters
		    if (hasWhitespace(selected_file))
		    {
			    // Send alert
				alert("The second selected file contains whitespace characters. Please rename " +
					  "the file or select a different file and try again.");
				
				// Reset selected file
				$("#submit-new-job-form").get(0).reset();
		    }
		    // Validate selected file extension
			else if (!hasExtension(extension, _valid_exts)) 
			{
				// Send alert
				alert("The second selected file is not a network file. Please select a different file.");
				
				// Reset selected file
				$("#submit-new-job-form").get(0).reset();
			}
			// Validate file size does not exceed 5MB
			else if(this.files[0].size / (1024 * 1024) > 5)
			{
				// Send alert
				alert("The second network file is larger than 5MB. Please select a different file.");
				
				// Reset selected file
				$("#submit-new-job-form").get(0).reset();
			}
	    }
	    else
	    {
		    alert("Whoops! You forgot to select the second network file.");
	    }
	});



/*
	.options
*/
	// Toggle visibility of dropdown menu for advanced options on click of the section header
	$(".content.options .options-section:not(#standard-options) header").click(function(e)
	{
		var options_section = $(this).siblings('.options-section-wrapper');
		var expander_show 	= $(this).find('.expander-show');
		var expander_hide	= $(this).find('.expander-hide');
		
		if ($(options_section).hasClass('hidden'))
		{
			$(options_section).removeClass('hidden');
			$(expander_show).addClass('hidden');
			$(expander_hide).removeClass('hidden');
		}
		else
		{
			$(options_section).addClass('hidden');
			$(expander_show).removeClass('hidden');
			$(expander_hide).addClass('hidden');
		}
	});

	// Toggle visibility options help menu
	$('#options-help-menu-button').click(function(e)
	{
		// var options_help_menu = $('#options-help-menu');
		
		if ($('#options-help-menu').hasClass('active'))
		{
			$('#options-help-menu').removeClass('active');
			$('#options-help-menu-sections').addClass('hidden');
			$('#options-help-menu-button span').text('+ Show Options Help Menu');
		}
		else
		{
			$('#options-help-menu').addClass('active');
			$('#options-help-menu-sections').removeClass('hidden');
			$('#options-help-menu-button span').text('- Hide Options Help Menu');
		}
	});

	$('div.content.options input').change(onchange);


});

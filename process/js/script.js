$(window).load(function()
{
	/*
		// Scroll to bottom of execution log output on page load
		// NOTE: For some reason, this is not working...
		// $('#exec-log-file-output').scrollTop($('#exec-log-file-output').prop('scrollHeight'));
		// So, using animate({scrollTop}) with speed set to 1 ms	
	*/
	$('html, body').animate({scrollTop: $('#exec-log-file-output').prop('scrollHeight')}, 1);
	
});

$(document).ready(function()
{
	$.ajax({
		url: "/template/process/process.php",
		type: "POST",
		data: {id: queryID},
		async: true,
		cache: false
    	}).done(function(data, status, something){
		data = JSON.parse(data);
		if (data["status"] == "Image successfully processed.")
			window.location = data["data"]['url'];
	}).fail(function(x, y, z){
	});
});

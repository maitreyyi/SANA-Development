<?php

/*
 * returnProcessingState
 * 
 * Will echo a JSON object with the following structure:
 * 
 * {
 * 		"success" : bool,
 * 		"status" : str,
 * 		"data" : {
 * 			("key" : "value")*
 * 		}
 * }
 * 
 * then will delete the session cookie (equivalent to setting
 * cookie expiration before request time.)
 */

function returnProcessingState($success, $status, $data=array())
{
	$result = json_encode(array("success" => $success,"status" => $status,"data" => $data));
	echo $result;
	setcookie(session_name(), '', time() - 3600);
	die();
}

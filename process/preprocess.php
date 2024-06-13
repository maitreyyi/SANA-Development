<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);


// Redirect non-POST requests
if($_SERVER['REQUEST_METHOD'] != 'POST')
{
	header('Location: /template/');
	die();
}


/* 
 * preprocessing.php	
 * 
 * This script will:
 * 
 * 1) Sanitize input received to the options form
 * 2) Validate that two files were uploaded
 * 3) Validate that there are no white space characters in the files' names
 * 4) Validate that the extensions of the two uploaded files are of the same, accepted network type
 * 5) Create a job ID hash
 * 6) Create an array containing info about the job
 * 7) Create directories for the job
 * 8) Move the two network files into their respective directories
 * 
 */
 

/*
 *	Functions
 */
 
function returnProcessingState($success, $status, $data=array())
{
	$result = json_encode(array('success' => $success,'status' => $status,'data' => $data));
	echo $result;
	die();
}

/*
 *	1) Sanitize input received to the options form
 */

//assign json file based on version

$version = $_POST['version'];
$v_json = 'SANA2.json';


switch($version){
	case "SANA 1.0":
		$v_json = "SANA1.json";
		break;
	case "SANA 1.1":
		$v_json = "SANA1_1.json";
		break;
}



$default_options_info 		= json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] 
							  . '/template/query/versions/'.$v_json));
$default_options_info_array = array_merge($default_options_info->standard, $default_options_info->advanced);


foreach($default_options_info_array as $option)
{
	if (!isset($_POST['options_inputs'][$option[0]]))
	{
		$_POST['options_inputs'][$option[0]] = $option[1] != 'checkbox' ? $option[2] : 0;
	}
}
//error_log("User input error", E_ERROR);

foreach($_POST['options_inputs'] as $option => $value)
{
	if (!is_numeric($value))
	{
		returnProcessingState(false,
		 					  'One or more of the selected options was invalid. Please try again.', 
		 					  array('data' => $_POST['options_inputs']));
	}
	if ($option == 't')
	{ 
		if (!is_numeric($value) || $value < 1 || $value > 20)
		{
			returnProcessingState(false,
		 					  	  'Running time must be an integer between 1 and 20, inclusive. ' 
		 					  	  . 'Please try again.', 
		 					      array('data' => $_POST['options_inputs']));

		}
	}
}

/*
 *	2) Validate that two files were uploaded
 */
 
if (!isset($_FILES['network-files']['tmp_name'][0]))
{
	returnProcessingState(false, 
						  'There was an issue with uploading the first file: ' 
						  . $_FILES['network-files']['name'][0],
						  array('data' => $_POST['options_inputs']));
}
else
{
	$network_1_pathinfo = pathinfo($_FILES['network-files']['name'][0]);
}

if (!isset($_FILES['network-files']['tmp_name'][1]))
{
	returnProcessingState(false, 
						  'There was an issue with uploading the second file: ' 
						  . $_FILES['network-files']['name'][1]);
}
else
{
	$network_2_pathinfo = pathinfo($_FILES['network-files']['name'][1]);
}

/*
 * 2a) Validate that esimfile count matches number of files uploaded
 */

$esim_files_arr = array();
/**
if(isset($_POST['options_inputs']['esim']){
	$esim_count = $_POST['options_inputs']['esim'];

	for($i = 0; $i < $esim_count; $i++){
		if(!isset($_FILES['esim-files']['tmp_name'][$i])){
			returnProcessingState(false, "There was an issue with uploading the esim file: ". $_FILES['esim-files']['name'][$i]);
		}
		else {
			$esim_file = pathinfo($_FILES['esim-files']['name'][$i]);
			array_push($esim_files_arr, $esim_file);
		}
	}
}
**/

/*
 *	3) Validate that there are no white space characters in the files' names
 */


$network_1_name = $network_1_pathinfo['filename'];
					  
if (preg_match('/\s/', $network_1_name))
{
	returnProcessingState(false, 
						  'The first selected file\' name contains whitespace characters. ' .
						  'Please rename the file or select a different file and try again');
}

$network_2_name = $network_2_pathinfo['filename'];

if (preg_match('/\s/', $network_2_name))
{
	returnProcessingState(false, 
						  'The second selected file\' name contains whitespace characters. ' .
						  'Please rename the file or select a different file and try again');
}
 
foreach($esim_files_arr as $esim_file){
	$network_name = $esim_file['filename'];
        if (preg_match('/\s/', $network_name))
	{
        	returnProcessingState(false,
                                                  'The selected file\' name contains whitespace characters. ' .
                                                  'Please rename the file or select a different file and try again. File: '. $i . ' ');
	}
}

/*
 *	4) Validate that the extensions of the two uploaded files are of accepted network types
 */
 
$valid_extensions = array('gw', 'el'); // Add additional extensions to allow for different file types

// Validate first network's extension
$network_1_ext = $network_1_pathinfo['extension'];

if (!in_array($network_1_ext, $valid_extensions))
{
	returnProcessingState(false, 
						  'The first network file was not of a valid extension: ' 
						  . $_FILES['network-files']['name'][0]);
}

// Validate second network's extension
$network_2_ext = $network_2_pathinfo['extension'];
if (!in_array($network_1_ext, $valid_extensions))
{
	returnProcessingState(false, 
						  'The second network file was not of a valid extension: ' 
						  . $_FILES['network-files']['name'][1]);
}

// Validate that networks are of the same extension
if ($network_1_ext != $network_2_ext)
{
	returnProcessingState(false, 'The two network files were not of the same extension.');
}

//Validate esim file extensions if they were passed 


/*
 *	5) Create a job ID hash
 */

$job_id	= md5(time() . $network_1_name . $network_2_name);


/*
 *	6) Create an array containing info about the job
 */
 
$job_data = array
(
	'id' 			=> $job_id,
	'job_location' 		=> '../process/runs/' . $job_id,
	'extension' 	 	=> $network_1_ext,
	'network_1_name'	=> $network_1_name,
	'network_2_name'	=> $network_2_name
);

/*
 *	7) Create directories for the job
 */
 
// Create directory for job
if(!mkdir($job_data['job_location'],0777,true))
{
	returnProcessingState(false, 
			      		  'Processing directory ' . $job_data['job_location'] 
			      		  . ' could not be created.');
}

// Create directory for networks
if(!mkdir($job_data['job_location'] . '/networks', 0777, true))
{
	returnProcessingState(false, 
			      		  'Processing directory ' . $job_data['job_location'] 
			      		  . '/networks could not be created.');
}

//Create directory for esim files
//if(!mkdir($job_data['job_location'] . '/esim-files',0777,true))
//{
//	returnProcessingState(false,
//					'Processing directory ' . $job_data['job_location']
//					. '/esim-files could not be created.');
//}
//Create directory for each esim file
//for( $i =0; $i < esim_count; $i++){
//	if(!mkdir($job_data['job_location'] . '/esim-files/'. $job_data['esim_'.$i.''], 0777, true))
//	{
//		returnProcessingState(false, 'Processing directory '.$job_data['job_location']
//					. '/networks/' . $job_data['esim_'.$i.''] . 'could not be created.');
//	}
//}
// Create directory for first network

if(!mkdir($job_data['job_location'] . '/networks/' . $job_data['network_1_name'], 0777, true))
{
	returnProcessingState(false, 
			      		  'Processing directory ' . $job_data['job_location'] 
			      		  . '/networks/' . $job_data['network_1_name'] . ' could not be created.');
}

// If networks are not the same, create directory for second network
if ($job_data['network_1_name'] != $job_data['network_2_name'])
{
	if(!mkdir($job_data['job_location'] . '/networks/' . $job_data['network_2_name'], 0777, true))
	{
		returnProcessingState(false, 
				      		  'Processing directory ' . $job_data['job_location'] . '/networks/' 
				      		  . $job_data['network_2_name'] . ' could not be created.');
	}
}


/*
 *	8) Move the network files into their respective directories (and validate file format: 3-column format and no white-spaces in name)
 *	for($i =0; $i < $esim_count; $i++){
 *		$esim_file_location = $job_data['job_location'] . '/esim/' . $job_data['esim_'.$i.''].'/'.$job_data['esim_'.$i.''].'.'.$job_data['extension'];
 *		
 *		if(!move_uploaded_file($_FILES['esim-files']['tmp_name'][i], $esim_file_location))
 *		{
 *			returnProcessingState(false,
 *					'First file '. $_FILES['esim-files']['name'][i].' could not be moved to '. $esim_file_location);
 *		}
 *	}
 **/
$network_1_location = $job_data['job_location'] . '/networks/' . $job_data['network_1_name'] . '/' 
					  . $job_data['network_1_name'] . '.' . $job_data['extension'];

if (!move_uploaded_file($_FILES['network-files']['tmp_name'][0], $network_1_location))
{
	returnProcessingState(false, 
						  'First file ' . $_FILES['network-files']['name'][0] 
						  . ' could not be moved to ' . $network_1_location);
}

if ($job_data['network_1_name'] != $job_data['network_2_name'])
{
	$network_2_location = $job_data['job_location'] . '/networks/' . $job_data['network_2_name'] 
						  . '/' . $job_data['network_2_name'] . '.' . $job_data['extension'];
		
	if (!move_uploaded_file($_FILES['network-files']['tmp_name'][1], $network_2_location))
	{
		returnProcessingState(false, 
							  'Second file ' . $_FILES['network-files']['name'][1] 
							  . ' could not be moved to ' . $network_2_location);
	}
}
/*    9) Move the esim files into their respective directories
 *
 *
 **/
$status = fopen($job_data['job_location'] . '/info.json', 'w');

fwrite($status, json_encode(array(		
	'status' 	=> 'preprocessed',
	'data' 		=> $job_data,
	'options'       => $_POST,
	'version'       => $_POST['version']
)));

fclose($status);

returnProcessingState(true,
                                          'Can move on to actual processing',
  					  array('url' => '/template/process?id=' . $job_data['id']));


?>

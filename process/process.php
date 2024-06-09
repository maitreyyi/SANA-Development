<?php //include $_SERVER["DOCUMENT_ROOT"].'/php/includes.php';
echo 'here inside processphp';
ignore_user_abort(true);

function returnProcessingState($success, $status, $data=array())
{
	$result = json_encode(array("success" => $success,"status" => $status,"data" => $data));
	echo $result;
	die();
}

/* processing
 * 
 * This script will:
 * 
 * 1 check for the ID variable
 * 
 * 	return a failure for a POST
 * 	redirect for anything else
 * 
 * 1 create a query string using aggregated options
 * 4 run the query string through the SpArcFiRe binary using the shell
 * 5 create a ZIP containing all files
 * 6 create a JSON file containing info on the query
 * The script will respond with a json object in all cases of failure 
 * and success, structured as:
 * 
 * {
 * 		"success" : bool,
 * 		"status" : str,
 * 		"data" : {
 * 			("key" : "value")*
 * 		}
 * }
 *
 */

// redirect non-POST requests

if ($_SERVER["REQUEST_METHOD"] != "POST")
{
	header("Location: /");
	die();
}

// step 1: 
// 
// check that there is an id supplied, 
// 	
//	if not, call returnProcessingState with a false success
// 	if so, but no such query exists, redirect to equivalent result page

if (!isset($_POST['id']))
{
	returnProcessingState(false, 'No Job ID supplied.');
} 
else if(!is_dir($_POST['id']))
{
	returnProcessingStatus(true, 
						   'Job does not exist.', 
						   array('url' => '/results?id=' . $_POST['id']));
}

// step 2: 
//
// check that the image is not already processed. if so, redirect to
// equivalent result page. if the image is being processed, return a
// JSON response that is true without a URL; this shouldn't happen
// since the process/index.php page also checks that the file isn't 
// being processed or is already processed, but just in case...

$info = json_decode(file_get_contents($_POST['id'] . '/info.json'));
echo $info->status;

if ($info->status == "processed" || $info->status == "failed")
{
	echo 'here inside processed/failed';
	returnProcessingStatus(true, 
						   "Networks already aligned.", 
						   array("url" => "/results?id=".$_POST["id"]));
} 
else if ($info->status == "processing")
{
	echo 'here inside processing';
	returnProcessingState(true, "Networks are still being aligned.");
}


// step 2: import the job_data and options from the info.json file

$options 	= $info->options;
$job_data 	= $info->data;
//$version      = $info->version;

$output_info = fopen($job_data->job_location . "/info.json", "w");
fwrite($output_info, json_encode(array("status" => "processing")));
fclose($output_info);


// step 2:
//
// generate a string that PHP will run. This version will use
// the SANA program located at $HOME/bin/


// start with binary location
// sana1.1, sana2.0
$version = 'sana2.0';
$option_string .= 'cd ' . $job_data->job_location . ' && /home/sana/bin/'.$version. ' ';
//networks
if ($job_data->extension == 'el') 
{
	$option_string .= '-fg1 networks/' . $job_data->network_1_name . '/' . $job_data->network_1_name . '.el ';
	$option_string .= '-fg2 networks/' . $job_data->network_2_name . '/' . $job_data->network_2_name . '.el ';
}
else
{
	$option_string .= '-g1 ' . $job_data->network_1_name . ' ';
	$option_string .= '-g2 ' . $job_data->network_2_name . ' ';
}
//esim files (all esim files will have 3-column format)
//for($i=0; $i < $esim_count; $i++){
//	$name = 'esim'.$i.'';
//	$option_string .= '__ esim-files/' . $job_data->$name . '/' . $job_data->$name.'.el ';
//}

$option_string .= '-tinitial auto ';
$option_string .= '-tdecay auto';

// append SANA execution options
foreach($options->options_inputs as $option => $value)
{
	$option_string .= ' -' . $option . ' ' . $value . ' ';
}

// step 3: run the script
$exitCode 	= 0;
$passArray 	= array();

//echo "<b>Option string: </b>" + $option_string;

exec($option_string . " &> run.log ", $passArray, $exitCode);
	
// success conditions go past here. success is indicated by exit_code
// if exit_code is equal to 0, this should indicate proper operation of
// the SANA script.

// if running SANA was successful, the exit_code should be 0

if ($exitCode)
{
	$output_info = fopen($job_data->job_location . '/info.json', 'w');
	
	fwrite($output_info, 
		   json_encode(array('status' 	=> 'failed',
		   					 'log' 		=> $job_data->job_location . '/error.log',
		   					 'command'	=> $option_string)));
		   					 
	fclose($output_info);

	returnProcessingState(false, 
						  'Networks could not be aligned.', 
						  array('url' => '/results?query=' . $job_data->id));
} 
else 
{
	
	// step 4:
	//
	// create a zip for the files. the zip will be created in /outDir
	// as galaxy_[imageId]_data.zip with two folders inside: images/
	// for the images and tables/ for the comma/tab separated value
	// tables.
	
	// Source: http://stackoverflow.com/questions/1334613/how-to-recursively-zip-a-directory-in-php
	function Zip($source, $destination, $include_dir = false)
	{
	
	    if (!extension_loaded('zip') || !file_exists($source)) {
	        return false;
	    }
	
	    if (file_exists($destination)) {
	        unlink ($destination);
	    }
	
	    $zip = new ZipArchive();
	    if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
	        return false;
	    }
	    $source = str_replace('\\', '/', realpath($source));
	
	    if (is_dir($source) === true)
	    {
	
	        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
	
	        if ($include_dir) {
	
	            $arr = explode("/",$source);
	            $maindir = $arr[count($arr)- 1];
	
	            $source = "";
	            for ($i=0; $i < count($arr) - 1; $i++) { 
	                $source .= '/' . $arr[$i];
	            }
	
	            $source = substr($source, 1);
	
	            $zip->addEmptyDir($maindir);
	
	        }
	
	        foreach ($files as $file)
	        {
	            $file = str_replace('\\', '/', $file);
	
	            // Ignore "." and ".." folders
	            if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
	                continue;
	
	            $file = realpath($file);
	
	            if (is_dir($file) === true)
	            {
	                $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
	            }
	            else if (is_file($file) === true)
	            {
	                $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
	            }
	        }
	    }
	    else if (is_file($source) === true)
	    {
	        $zip->addFromString(basename($source), file_get_contents($source));
	    }
	
	    return $zip->close();
	}
	
	$zip_name = "SANA_alignment_output_" . $job_data->id . ".zip";
	
	Zip($job_data->job_location, $zip_name, true);
	
	// Move created .zip from /process to /process/<$job_data->id>
	if (copy($zip_name, $job_data->job_location . '/' . $zip_name))
	{
	    unlink($zip_name);
	}
	
	
	
	// step 5: create a json file containing information on the network alignment results
	
	$output_info = fopen($job_data->job_location . "/info.json", "w");
	
	fwrite($output_info, json_encode(array(
		"status" 	=> "processed",
		"zip_name" 	=> $zip_name,
		"command" 	=> $option_string
	)));	
	
	fclose($output_info);
	
	returnProcessingState(true, "Networks successfully processed.", array(
		"url" => "/results?id=" . $job_data->id
	));
}
?>

<?php 

	include $_SERVER['DOCUMENT_ROOT'] . '/template/inc/base.php';
	$_THIS_DIR 		= realpath(dirname(__FILE__));
	
	$job_id			= $_GET['id'];
	$job_dir		= $_JOBS . '/' . $job_id;
	
	$is_job			= is_dir($job_dir);
	
	$is_processed 	= file_exists($job_dir . '/info.json');
	$has_error 		= file_exists($job_dir . '/error.log');
	
	if ($is_processed)
	{
		$job_data = json_decode(file_get_contents($job_dir . '/info.json'));
	}
		
	$status 		= $job_data->status;
	
	if ($status == 'preprocessed' || $status == 'processing')
	{
		header('Location: /template/process?id=' . $job_id);
		die();
	}
	
	$zip_location = $_URL . '/process/' . $job_id . '/' . $job_data->zip_name;
	
	$exec_log_file_path = $job_dir . '/run.log';
		
	if (file_exists($exec_log_file_path))
	{
		// Open file with warning suppression (will check below if file was opened)
		$exec_log_file_buffer = @fopen($exec_log_file_path, 'r');	
			
		if ($exec_log_file_buffer)
		{
			// Parse each file of line, add markup, and concatenate to output string
			$exec_log_file_output = '';
			
			while(!feof($exec_log_file_buffer))
			{
				$exec_log_file_output .= '<span>' . trim($exec_log_file_line) . '</span>';
				$exec_log_file_line = fgets($exec_log_file_buffer);
			}
			
			fclose($exec_log_file_buffer);
		}
		else
		{
			$exec_log_file_output = 'Problem opening execution log file.';
		}
	}
	else
	{
		$exec_log_file_output = 'Job execution log file does not exist.';
	}
?>

<!DOCTYPE html>
<html>
	<head>
		
		<?php include $_INCLUDES . '/head.php'; ?>
		
	</head>
	<body>
		
		<?php include $_INCLUDES . '/nav.php'; ?>
		
		<div id="results-page-content" class="page-content">
			<div class="page-content-wrapper">
		
				<?php 
					if (isset($job_id)) 
					{
						if ($is_job)
						{
				?>			<header>
								<h1>Job Results</h1>
							</header>
							<hr>
							<div id="query-id">
								<h2>Job ID: </h2>
								<span><?php echo $job_id; ?></span>
							</div>
							
							<?php 
							
							if ($status == 'processed') 
							{
								
							?>
								<div id="results-note" class="panel callout">
									<h2>NOTE</h2>
									<span id="result-info">These results can be accessed on <a href="<?php $_URL; ?>/results">the results page</a> using the above Job ID, or directly accessed using <a href="<?php echo $_URL . '/results?id=' . $job_id; ?>">this link</a>.</span>
								</div>
								<a id="zip-download-button" class="button radius" href="<?php echo $zip_location; ?>">Download Results As .zip</a>
								<div id="exec-log-file-output">
								<?php
									
									echo($exec_log_file_output);
								
								?>
								</div>
															
							<?php
								
							}
							else if ($status == 'failed')
							{
							?>
							
								<span>The alignment of the networks failed. The contents of the execution log are:</span>
								<span><?php echo file_get_contents($job_dir . '/error.log'); ?></span>
								
							<?php
							}
						}
						else 
						{ 
						?>
						
							<header>
								<h1>Lookup Previous Job</h1>
							</header>
							<hr>
							<span>Sorry: no such (template)  result Job ID exists. Please try another Job ID.</span>
				
						<?php 
						} 
					} 
					else 
					{ 
					?>
						<header>
							<h1>Lookup Previous Job</h1>
						</header>
						<hr>
						<div id="results-search-form-wrapper">
							<label id="result-search-label">Job ID To Search For:</label>
							<form action="/results">
								<input type="text" id="results-search-input" name="id" placeholder="Previous Job ID"></input>
								<input type="submit" id="results-search-submit" class="button radius" value="Submit"></input>
							</form>
						</div>
				<?php 
					}
				?>
			</div>
		</div>
		
		<?php include $_INCLUDES . '/footer.php'; ?>
		
	</body>
</html>

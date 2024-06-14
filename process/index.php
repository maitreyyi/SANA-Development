<?php
	if ($_SERVER['REQUEST_METHOD'] != 'GET')
	{
		die();
	} 
	else if (!isset($_GET['id']))
	{
		header('Location: /template/');
		die();
	} 
	else if (!is_dir('runs/'.$_GET['id']))
	{
		header('Location: /template/results?id=' . $_GET['id']);
		die();
	}
	
	
	include $_SERVER["DOCUMENT_ROOT"] . "/template/inc/base.php";

	$_THIS_DIR = realpath(dirname(__FILE__));	
	$status = json_decode(file_get_contents('runs/'. $_GET['id'] . '/info.json'))->status;
	
	if ($status == 'processed')
	{
		header('Location: /template/results?id=' . $_GET['id']);
		die();
	}
	
	if ($status == 'processing')
	{
		$exec_log_file_path = 'runs/' . $_GET['id'] . '/run.log';
		
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
	}
	
?>

<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="refresh" content="10">
		
		<?php include $_INCLUDES . "/head.php"; ?>
		
		<script type="text/javascript">var queryID = '<?php echo $_GET["id"] ?>';</script>
		
	</head>
	<body>
		
		<?php include $_INCLUDES . "/nav.php"; ?>
		
		<div id="process-page-content" class="page-content">
			<div class="page-content-wrapper">
				<div id="content-container">
					<div class="content process active">
						<div id="query-id">
							<h2>Job ID: </h2>
							<span><?php echo $_GET["id"]?></span>
						</div>
						<div id="processing-animation-container">
							<?php
							/*
							<div>
								<img src="<?php echo $_IMAGES . '/processing-animation.png'; ?>" id="processing-animation" alt="" />
							</div>
							*/
							?>
							<div>
								<span>Aligning Networks...</span>
							</div>
						</div>
						<div id="processing-status-note" class="panel callout">
							<h2>NOTE</h2>
						<?php 
							if ($status == 'preprocessed') 
							{		
						?>
								<p>The networks selected for this job are currently being aligned. This could take a while depending on the selected options.</p>
						<?php 
							} 
							else 
							{
						?>
								<p>The networks selected for this job are still being aligned. This page will reload every ten seconds until the job is complete. Partial results of the network alignment are displayed below.</p>
						<?php 
							}
						?>
							<p>If you wish to close this window, take note of the Job ID: it can used to fetch the results when processing is finished by going to the <a href="/template/results">look up previous job</a> page.</p>
						</div>
						<div id="exec-log-file-output">
							<?php
								
							echo($exec_log_file_output);
							
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
			
		<?php include $_INCLUDES . "/footer.php"; ?>
			
	</body>
</html>

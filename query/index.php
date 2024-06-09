<?php
	include $_SERVER["DOCUMENT_ROOT"]. "/template/inc/base.php";
	$_THIS_DIR = realpath(dirname(__FILE__));

	$esim_count = 0;     //default esim count
	$version="SANA 2.0"; //default version of SANA is set
	
	$options_sections 	= array('standard', 'advanced');
	$default_options_info = version_options($version);	

	if($_SERVER["REQUEST_METHOD"] == "POST"){
		if(isset($_POST["version"])){
			//echo "inside version";
			$version = $_POST["version"];
		}
		if(isset($_POST["selectedOption"])){
			$esim_count = $_POST["selectedOption"];
		}
		echo $version;
	}

	
	/*
		Declare and initialize variables
		Based on the version that is selected, a different config.json is generated which then controls the options that are displayed.
	*/
	function version_options($version){
		$file = "./versions/config.json";
		switch($version){
			case "SANA 1.0":
				$file = "./versions/SANA1.json";
				break;
			case "SANA 1.1":
				$file = "./versions/SANA1_1.json";
				break;
			case "SANA 2.0":
				$file = "./versions/SANA2.json";
				break;
		}
		$default_options_info 	= json_decode(file_get_contents($file));

		return $default_options_info;
	}
	
	function outputFormGroup($name, $type, $value, $nameWrapper, $label, $tooltip='')
	{
		echo '<li>';
		echo '<div class="row options-form-group" title="' . ($tooltip != '' ? $tooltip : '') . '">';
		echo '<div class="columns small-7 medium-8">';
		echo '<label for="' . $name . '">' . $label . '</label>';
		echo '</div>';
		echo '<div id="'. $name . '" class="small-5 medium-4 columns">';
		
		$wrapname = "";
		foreach ($nameWrapper as $wrapper)
		{
			$wrapname += $wrapper . '[';
			$name .= "]";
		}
		
		$wrapname += $name;		

		if ($type == 'checkbox')
		{	
			echo '<input name="' .$wrapname. '" type="' . $type . '"'. ' value="';
			if ($value == 'true')
			{
				echo '1" checked="true"';
			}
			else
			{
				echo '0"';
			}
		}
		else {
			echo '<input name="' .$wrapname. '" type="' . $type . '"'. ' value="'.$value.'"';
		}

		/*
		elseif($type == "dbl_vec" or $type == "str_vec"){
			for($i = 0; $i < 3; $i++){
				echo '<input name="' .$wrapname. '" type="' . $type . '"'. ' value="';
			}
		}
		*/
		
		echo ' />';
		echo '</div>';
		echo '</div>';
		echo '</li>';
	}
	function returnFormGroup($name, $type, $value, $nameWrapper, $label, $tooltip='')
	{
		$htmlContent = 
		'<li>'.
		'<div class="row options-form-group" title="' . ($tooltip != '' ? $tooltip : '') . '">'.
		'<div class="columns small-7 medium-8">'.
		'<label for="' . $name . '">' . $label . '</label>'.
		'</div>'.
		'<div id="'. $name . '" class="small-5 medium-4 columns">';
		
		$wrapname = "";
		foreach ($nameWrapper as $wrapper)
		{
			$wrapname .= $wrapper . '[';
			$name .= "]";
		}
		
		$wrapname .= $name;		

		if ($type == 'checkbox')
		{	
			$htmlContent .= '<input name="' .$wrapname. '" type="' . $type . '"'. ' value="';
			if ($value == 'true')
			{
				$htmlContent .= '1" checked="true"';
			}
			else
			{
				$htmlContent .= '0"';
			}
		}
		else {
			$htmlContent .= '<input name="' .$wrapname. '" type="' . $type . '"'. ' value="'.$value.'"';
		}

		/*
		elseif($type == "dbl_vec" or $type == "str_vec"){
			for($i = 0; $i < 3; $i++){
				echo '<input name="' .$wrapname. '" type="' . $type . '"'. ' value="';
			}
		}
		*/
		
		$htmlContent .= ' />'.
		'</div>'.
		'</div>'.
		'</li>';
		return $htmlContent;
	}

	function createHelpTextGroup($version, $options_section)
	{
		$group = version_options($version)->$options_section;
		$htmlContent = '';
		foreach($group as $option)
		{
			$htmlContent .= '<div class="row option-help-listing">'.
			'<div class="columns small-12 medium-4">'
			.'<header class="option-help-name"><h5>' . $option[3] . '</h5></header>'
			.'</div>'
			.'<div class="columns small-12 medium-8 option-help-text">'
			.$option[4]
			.'</div></div>';
		}
		return $htmlContent;
	}

	function createHelpTextGroups($version)
	{
		$htmlContent = '';
		$options_sections = version_options($version);

		$htmlContent .= '<section>';									
		//if ($options_section == 'standard')
		$htmlContent .= '<header><h3>Standard Network Alignment Options</h3></header>';
		$htmlContent .= createHelpTextGroup($version, 'standard');
		#advanced
		$htmlContent .= '<header><h3>Advanced Options</h3></header>'
		. createHelpTextGroup($version, 'advanced');																					
		$htmlContent .= '</section>';

		return $htmlContent;
										
	}
	
	function createOptionValueGroup($group)
	{
		$htmlContent = '';
		foreach($group as $option)
		{
			$htmlContent .= '<li class="option-stats ' . $option[0] . '">'.
			'<div class="row">'.
			'<div class="option-name columns small-7 medium-8">' . $option[3]. '</div>' .
			'<div class="columns small-5 medium-4">'.
			'<div class="option-value">' . ($option[1] == "checkbox" ? ($option[2] == 1 ? 'Yes' : 'No') : $option[2]) . '</div>'.
			'<div class="option-original-value hidden">' .  ($option[1] == "checkbox" ? ($option[2] == 1 ? 'Yes' : 'No') : $option[2]) . '</div>'.
			'</div>'.
			'</div>'.
			'</li>';
		}

		return $htmlContent;
	}
	
	function displayStandardMenu($version)
	{	
		$default_options_info = version_options($version);
		$options_section = 'standard';
		$htmlContent =
		'<header><h2>'.
		'<span>Standard Network Alignment Options</span>'.
		'</h2></header>'.
		'<div class="options-section-wrapper">'.
		'<ul class="small-block-grid-1 medium-block-grid-2">';
											
		foreach($default_options_info->$options_section as $option)
		{
			$htmlContent .= returnFormGroup($option[0], $option[1], $option[2], array('options_inputs'), isset($option[3]) ? $option[3] : '',isset($option[4]) ? $option[4] : '');
		}									
		$htmlContent .= '</ul></div>';

		return $htmlContent;
	}									

	$standard_v1   = displayStandardMenu("SANA 1.0");
	$standard_v1_1 = displayStandardMenu("SANA 1.1");
	$standard_v2   = displayStandardMenu("SANA 2.0");

	$help_v1   = createHelpTextGroups("SANA 1.0");
	$help_v1_1 = createHelpTextGroups("SANA 1.1");
	$help_v2   = createHelpTextGroups("SANA 2.0");

	$options_standard_v1 	= createOptionValueGroup(version_options("SANA 1.0")->standard);
	$options_standard_v1_1  = createOptionValueGroup(version_options("SANA 1.1")->standard);
	$options_standard_v2    = createOptionValueGroup(version_options("SANA 2.0")->standard);
																				
	$options_advanced_v1    = createOptionValueGroup(version_options("SANA 1.0")->advanced);                                                                    
	$options_advanced_v1_1  = createOptionValueGroup(version_options("SANA 1.1")->advanced);
        $options_advanced_v2    = createOptionValueGroup(version_options("SANA 2.0")->advanced); 


?>

<!DOCTYPE html>
<html>
	<head>
		<?php include $_INCLUDES . "/head.php"; ?>
	</head>
	<body onload="ToggleFormDisplay()">
		
		<?php include $_INCLUDES . "/nav.php"; ?>
		
		<div id="js-disabled">
			<div class="page-content">
				<div class="page-content-wrapper">
					<div class="panel callout">
						<h1>WARNING</h1>
						<p>Our submission form uses JavaScript!</p>
						<p>Please enable JavaScript in your browser and refresh the page.</p>
					</div>
				</div>
			</div>
		</div>
		<div id="js-enabled" class="hidden">
			<div id="query-page-content" class="page-content">
				<div class="page-content-wrapper">
					<header>
						<h1>Submit New Job</h1>
					</header>
					<hr>
					<div id="steps-container">
						<ul id="steps">
							<li class = "step select-version active visited">
								<span class="arrow">&nbsp;</span>
								<span id = "select-version" >select version: SANA 2.0</span>
							</li>
							<li class="step select-networks">
								<span class="arrow">&nbsp;</span>
								<span id = "select-networks">select networks</span>
							</li>
							<li class="step options">
								<span class="arrow">&nbsp;</span>
								<span id ="select-options" >options</span>
							</li>
							<li class="step confirm">
								<span class="arrow">&nbsp;</span>
								<span>confirm</span>
							</li>
							<li class="step process">
								<span class="arrow">&nbsp;</span>
								<span>preprocessing</span>
							</li>
						</ul>
					</div>
					
					
					<div id="content-container">
						<div class = "content select-version active visited">
							<div id = "version-selection-prompt">
								<h3>Choose which version of SANA you would like to run</h3>
								<span>The default version of SANA that you can use is SANA 2.0</span>
							</div>
							<div id = "version-selection">
								<select id = "version" onchange = selectVersion(this.value)>
									<option value = "SANA 2.0">SANA 2.0</option>
									<option value = "SANA 1.1">SANA 1.1</option>
									<option value = "SANA 1.0">SANA 1.0.0</option>
								</select>	
							</div>
							<div class ="page-flipper">
								<div class = "next-page button radius">
									<span>next &rarr;</span>
								</div>
							</div>	
						</div>
						<form id="submit-new-job-form" method="POST" enctype="multipart/form-data" action="/template/process/index.php">
							<div class="content select-networks">
								<div id="network-selection-prompt">
									<p>Please select two networks to align. Allowed
file types are:</p> 
									<ul class="circle">
										<li>LEDA &rarr; .gw</li>
										<li>ELISP &rarr; .el</li>
									</ul>
								</div>
								<div id="network-selection-note" class="panel callout">
									<h2>NOTE</h2>
									<span>Please note the following:</span>
									<ul class="circle">
										<li>The networks must be of the same file type.</li>
										<li>If you would like to align a network against itself, select the same file twice.</li>
										<li>The first network must be smaller than or equal to the second network in terms of the number of nodes it contains (which can be found by looking at the 5th line of a given LEDA file).</li>
										<li>For the sake of conserving server space, network files must be less than or equal to 1MB in size.</li>
										<li>You can specify how much time SANA runs (up to 20 minutes, plus preprocessing time which adds about 10-20%).</li>
										<!-- <li>To see a demo, just click "NEXT", and default networks YEAST and HUMAN will
be aligned for a 3 minute run</li> -->
									</ul>
								</div>
								<div id="network-file-input-wrapper">
									<div class="row">
										<div class="columns small-12 medium-6">
											<span>If you aren't aligning a network to itself, select the <strong><em>smaller</em></strong> network (in terms of node count).</span>
										</div>
										<div class="columns small-12 medium-6">
											<!-- 
												Attempt to style input["type=file"] ala Foundation .button not working
												Source: http://zurb.com/building-blocks/file-upload-button
											-->
											<!-- <button class="file-upload radius"> -->
												<input type="file" id="network-file-1-input" class="file-input" name="network-files[]" disabled></input>
											<!-- </button> -->
										</div>
									</div>
									<div class="row">
										<div class="columns small-12 medium-6">
											<span>If you aren't aligning a network to itself, select the <strong><em>larger</em></strong> network (in terms of node count).</span>
										</div>
										<div class="columns small-12 medium-6">
											<!-- <button class="file-upload radius"> -->
												<input type="file" id="network-file-2-input" class="file-input" name="network-files[]" disabled></input>
											<!-- </button> -->
										</div>
									</div>
								</div>
								<!--Optional input for ESIM files-->
								<div id = "esim-selection-note" class="panel callout">
                                                                        <h2>OPTIONAL</h2>
                                                                        <span>External Similarity File Count</span>
                                                                        <ul class="circle">
                                                                        	<li>All similarity files must follow the 3-column format: protein from species 1, protein from species 2, similarity</li>
										<li>External Similarity weight: weight specifying objective function weights for external similarity files. Default will be zero.</>						                        </ul>
									<p>Choose number of files<p>
									<select id = "esim_count" name = "esim_count" onchange = "selectEsimCount(this.value)" >
										<option value = 0 selected = "selected"> 0 </option>
										<option value =1>  1 </option>
										<option value =2>  2 </option>
										<option value=3>   3 </option>
									</select>

								</div>
								<div id = "esim-file-input-wrapper"> </div>

								<div class="page-flipper">
									<div class = "prev-page button radius">
										<span>&larr; prev</span>
									</div>
									<div class="next-page button radius">
										<span>next &rarr;</span>
									</div>
								</div>
							</div>
							<div class="content options">
								<div id="options-help-menu" class="panel callout">
									<h2>Options Help</h2>
									<span>To see a description of a given option, simply hover over it.</span>
									<span>You can also view the entire help menu by clicking the button below.</span>
									
									<div id="options-help-menu-button" class="button radius">
										<span>+ Show Options Help Menu</span>
									</div>
									<div id="options-help-menu-sections" class="hidden">
										<?php echo $help_v2; ?>
									</div>
								</div>
								

								<section id = "standard-options" class = "options-section" >
										<?php echo $standard_v2; ?>
								</section>
								
								<section id = "advanced-options" class = "options-section">
								</section>
							

								<div class="page-flipper">
									<div class="prev-page button radius">
										<span>&larr; back</span>
									</div>
									<div class="next-page button radius">
										<span>next &rarr;</span>
									</div>
								</div>
							</div>
						</form>
						
		
	
						<div class="content confirm">
							<div class="panel callout">
								<h2>NOTE</h2>
								<span>Please note the following:</span>
								<ul class="circle">
									<li>The networks will be aligned with the following options, which cannot be changed after submission. To proceed, hit the submit button.</li>
									<li>Faded values are unchanged, default values. To make changes, click the back button.</li>

								</ul>
							</div>
							<div id="options-stats">
								<section>
									<header>
										<h3>Standard Network Alignment Options</h3>
									</header>
									<ul id = "options-stats-standard" class="small-block-grid-1 medium-block-grid-2">
										<?php echo $options_standard_v2; ?>
									</ul>
								</section>
								
								<?php
									
								if (sizeof($default_options_info->advanced) != 0)
								{
									
								?>
									<section>
										<header>
											<h4>Advanced Options</h4>
										</header>
										<ul id = "options-stats-advanced" class="small-block-grid-1 medium-block-grid-2">
											<?php echo $options_advanced_v2; ?>
										</ul>
									</section>
									
								<?php
									
								}
								
								?>
								<p>Check parameters and click submit when you are ready </p><br>
									
							</div>
							<div class="page-flipper">
								<div class="prev-page button radius">
									<span>&larr; back</span>
								</div>
								<div class="next-page button radius">
									<span>submit &rarr;</span>
								</div>
							</div>
						</div>
						
						
						
						<div class="content process">
							<div id="processing-animation-container">
								<?php
								/*
								<div>
									<img src="<?php echo $_IMAGES . '/processing-animation.png'; ?>" id="processing-animation" alt="" />
								</div>
								*/
								?>
								<div>
									<span>Preprocessing Networks...</span>
								</div>
							</div>
							<div class="panel callout">
								<h2>NOTE</h2>
								<span>Please note the following:</span>
								<ul class="circle">
									<li>We are aware of issues uploading files in certain browsers and/or on certain operating systems. If this page doesn't redirect within a few seconds, please close this window and try again in a different browser. Thank you for your patience while we squash this bug.</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<script type="text/javascript">
			// Script to hide form and show warning message if JavaScript is disabled 
			
			//$('div.content.options input').change(onchange);
			function ToggleFormDisplay()
			{
				$('#js-disabled').addClass('hidden');
				$('#js-enabled').removeClass('hidden');
				
				$('#network-file-1-input').prop('disabled', false);
				$('#network-file-2-input').prop('disabled', false);
			}
								
			function selectVersion(value){
				var xhr = new XMLHttpRequest();
            			xhr.onreadystatechange = function() {
					if (xhr.readyState == 4 && xhr.status == 200) {
						// Handle the response from the PHP script
						//console.log();
					}
				};

				xhr.open("POST", "index.php", true);
				xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xhr.send("version=" + value);
				
				//change menu, options help and esim display
				var optionsMenu = document.getElementById("standard-options");
				var optionsHelp = document.getElementById("options-help-menu-sections");
				var esimBlock   = document.getElementById("esim-selection-note");
				var panel       = document.getElementById("select-version");
				
				var optionsMenuStandard = document.getElementById("options-stats-standard");
				var optionsMenuAdvanced = document.getElementById("options-stats-advanced");

				var displayMenu = "<?php echo addslashes($standard_v2); ?>";
				var displayHelp = "<?php echo addslashes($help_v2); ?>";

				var optionStandardStats = "";
				var optionAdvancedStats = "";

				if (value == "SANA 1.0"){

					displayMenu = "<?php echo addslashes($standard_v1); ?>";
					displayHelp = "<?php echo addslashes($help_v1); ?>";

					optionStandardStats = "<?php echo addslashes($options_standard_v1); ?>";
					optionAdvancedStats = "<?php echo addslashes($options_advanced_v1); ?>";

					esimBlock.style.display = "none";
					
				} else if(value == "SANA 1.1"){

					displayMenu = "<?php echo addslashes($standard_v1_1); ?>";
					displayHelp = "<?php echo addslashes($help_v1_1); ?>";

					optionStandardStats = "<?php echo addslashes($options_standard_v1_1); ?>";
					optionAdvancedStats = "<?php echo addslashes($options_advanced_v1_1); ?>";

					esimBlock.style.display = "none";
					
				}else{
					optionStandardStats = "<?php echo addslashes($options_standard_v2); ?>";
					optionAdvancedStats = "<?php echo addslashes($options_advanced_v2); ?>";
		
					esimBlock.style.display = "";
				}

				panel.innerHTML = "select version: "+ value;
			    	optionsMenu.innerHTML = displayMenu;
				optionsHelp.innerHTML = displayHelp;
				
				optionsMenuStandard.innerHTML = optionStandardStats;
				optionsMenuAdvanced.innerHTML = optionAdvancedStats;

				console.log("adding event listener to options input");
			        $('div.content.options input').change(onchange);

			}
			
			function selectEsimCount(value){
				//var selectedOption = document.getElementById("esim_count").value
				var xhr = new XMLHttpRequest();
            			xhr.onreadystatechange = function() {
					if (xhr.readyState == 4 && xhr.status == 200) {
						// Handle the response from the PHP script
						//console.log(xhr.responseText);
					}
				};

				xhr.open("POST", "index.php", true);

				var fileInputs = document.getElementById("esim-file-input-wrapper");
				display = "";
				xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				
				for(var i =0; i < value; i++) {
					display += "<div class = 'row'>"+
						   "<div class = 'columns small-12 medium-6'>" +
						   "<span>External similarity file: " + i + " </span>" +
						   "<input type ='number' id='esim-file-weight' class='weight-input' name='esim-weights[]' style='width: 100px;'></input>"+
						   "</div>"+
						   "<div class ='columns small-12 medium-6'>" +
						   "<input type='file' id='esim-file-" + i + "-input' class='file-input' name= 'esim-files[]'></input>" +
						   "</div></div><br>";
				}

				fileInputs.innerHTML = display;
			}
		
		</script>
		
		<?php include $_INCLUDES . "/footer.php"; ?>
		
	</body>
</html>

<footer id="page-footer">
	<span class="page-footer-heading">SANA software</span>
	<span>Written by Nil Mamano under the supervision of <a href="mailto:whayes@uci.edu">Wayne B. Hayes</a> at U.C. Irvine.</span>
	<?php
		$software_location 		= '/home/sana/bin/sana2.0';
		$software_last_updated 	= date('F d, Y', filemtime($software_location));
		
		echo('<span>Last updated: ' . $software_last_updated . '</span>'); 
	?>
	<hr>
	<span class="page-footer-heading">SANA Web interface</span>
	<span>Adapted from SpArcFiRe Web Interface, developed by <a href="mailto:maitres@uci.edu">Wilmer R. Domingo </a></span>
	<span>Current developer: <a href="mailto:maitres@uci.edu">Maitreyi Sinha</a></span>
	<?php

		$web_interface_location 		= $_ROOT;
		$web_interface_last_updated 	= date('F d, Y', filemtime($web_interface_location));
		
		echo('<span>Last updated: ' . $web_interface_last_updated  . '</span>'); 
	?>
	<hr>
	<span>Please contact the current developer above and/or Wayne B. Hayes for questions, comments, or bugs.</span>
</footer>

<script src="<?php $_ROOT; ?>/bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php $_ROOT; ?>/bower_components/foundation/js/foundation.min.js"></script>
<script src="<?php $_ROOT; ?>/js/app.js"></script>

<?php
	if (file_exists($_THIS_DIR . '/js/min/script-min.js')) 
	{
		echo '<script src="./js/min/script-min.js"></script>';
	} 
	else if (file_exists($_THIS_DIR . '/js/script.js')) 
	{
		echo '<script src="./js/script.js"></script>';
	}
?>

<?php
	// GLOBAL VARIABLES
	$_ROOT 		= $_SERVER["DOCUMENT_ROOT"] . '/template';
	$_INCLUDES 	= $_ROOT . '/inc';
	
	$_URL 		= 'http://sana.ics.uci.edu/template';
	$_IMAGES	= $_URL . '/img';
	$_JOBS		= $_ROOT . '/process';
	
	// BASE INCLUDES
	include 'functions.php';

	// Start a session so that visior info can be logged
	session_start();

	include 'log.php';
?>

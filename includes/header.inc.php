<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<script type="text/javascript" src="js/prototype.js"></script>
<script type="text/javascript" src="js/effects.js"></script>
<script type="text/javascript" src="js/controls.js"></script>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>JibJab KudoBot</title><link rel="stylesheet" type="text/css" href="styles/styles.css">
<link rel="shortcut icon" type="image/ico" href="/images/favicon.ico" />

</head>
<? require_once("includes/kudo.class.php") ?>
<body>
<div id="content">

	<div id="nav">
		<? if (kudo::get_loggedin_user() != "") { ?>
		<a href="kudos.php">Send Kudos</a> &nbsp; <a href="stats.php?mode=given">Kudos Given</a> &nbsp; 
		<a href="stats.php?mode=received">Kudos Received</a> &nbsp; <a href="my_account.php">My Account</a>
		<?php
		if (kudo::is_admin())
		{
			echo '&nbsp; <a href="admin.php" target="_blank">Admin</a>';
		} ?> &nbsp; <a href="index.php?logout=1">Log Out</a><?
	}
		?>
		</div>
	<div id="selected"></div>
	<div id="panel">
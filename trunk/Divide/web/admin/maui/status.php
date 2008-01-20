<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<? include_once("const.inc.php"); ?>
<html>
<head>
<title></title>
</head>
<body>
	<?
	$username = "santiago";
	$host = "localhost";

	$selected=2;
	include_once("menu.inc.php");

	$status = `ssh -l $username $host "$status_cmd; exit" 2>&1`;
	print("<span style='font-size: 1.5em'>Maui status</span>&nbsp;".
		"<span style='font-size: 0.7em'>[<a href='config.php'>show configuration</a>]</span>");
	print("<pre>$status</pre>");

	$showstate = `ssh -l $username $host "$showstate_cmd; exit" 2>&1`;
	print("<span style='font-size: 1.5em'>System state</span>");
	print("<pre>$showstate</pre>");
	?>
</body>
</html>

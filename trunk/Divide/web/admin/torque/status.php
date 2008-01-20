<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<? include_once("const.inc.php"); ?>
<html>
<head>
<title></title>
</head>
<body>
	<?
	$selected=$menu_status;
	include_once("menu.inc.php");

	$username = "santiago";
	$host = "localhost";

	$qstatB = `ssh -l $username $host "$qstat_cmd -Bf; exit" 2>&1`;

	print("<span style='font-size: 2.0em'>Server Status</span>");
	print("<pre>$qstatB</pre>");
	?>
</body>
</html>

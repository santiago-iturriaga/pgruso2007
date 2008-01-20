<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<? 
	include_once("const.inc.php"); 
	include_once("lib.inc.php");
?>
<html>
<head>
<title></title>
</head>
<body>
	<?
	$selected=$menu_queues;
	include_once("menu.inc.php");

	$username = "santiago";
	$host = "localhost";

	$qstatQ = `ssh -l $username $host "$qstat_cmd -Qf; exit" 2>&1`;
	print("<span style='font-size: 2.0em'>Queue Configuration</span>");
	parsear_tabla_queues($qstatQ);
	?>
</body>
</html>

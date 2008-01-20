<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<? 
	include_once("lib.inc.php"); 
	include_once("const.inc.php"); 
?>
<html>
<head>
<title></title>
</head>
<body>
	<?
	$username = "santiago";
	$host = "localhost";

	$selected=1;
	include_once("menu.inc.php");

	$nodes = `ssh -l $username $host "$diagnose_cmd -n; exit" 2>&1`;
	print("<span style='font-size: 1.5em'>Nodes status</span>");
	print("<pre>$nodes</pre>");
	?>
</body>
</html>

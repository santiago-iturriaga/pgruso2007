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

	$config = `ssh -l $username $host "$config_cmd; exit" 2>&1`;
	print("<span style='font-size: 2.0em'>Maui configuration</span>&nbsp;".
		"<span style='font-size: 0.7em'>[<a href='status.php'>volver</a>]</span>");
	print("<pre>$config</pre>");
	?>
</body>
</html>

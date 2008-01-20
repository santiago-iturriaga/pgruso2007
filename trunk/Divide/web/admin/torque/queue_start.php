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

	print("<span style='font-size: 2.0em'>Queue Started</span>&nbsp;".
		"<span style='font-size: 0.7em'>[<a href='queues_status.php'>volver</a>]</span>");
	if (ISSET($_REQUEST["id"])) {
		$id = $_REQUEST["id"];
		$qstart_result = `ssh -l $username $host "$qstart_cmd $id; exit" 2>&1`;

		if ($qstart_result=="") {
			print("<pre>Ready.</pre>");
		} else {
			print("<pre>$qstart_result<pre>");
		}
	} else {
		print("Debe especificar un id de cola (?id=).");	
	}
	?>
</body>
</html>

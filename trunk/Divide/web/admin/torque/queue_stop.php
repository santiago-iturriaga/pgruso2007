<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<? include_once("const.inc.php"); ?>
<html>
<head>
<title></title>
</head>	
<body>
	<?
	print("<span style='font-size: 2.0em'>Queue Stoped</span>&nbsp;".
		"<span style='font-size: 0.7em'>[<a href='queues_status.php'>volver</a>]</span>");
	if (ISSET($_REQUEST["id"])) {
		$id = $_REQUEST["id"];
		$qstop_result = `ssh -l $username $host "$qstop_cmd $id; exit" 2>&1`;

		if ($qstop_result=="") {
			print("<pre>Ready.</pre>");
		} else {
			print("<pre>$qstop_result<pre>");
		}
	} else {
		print("Debe especificar un id de cola (?id=).");	
	}
	?>
</body>
</html>

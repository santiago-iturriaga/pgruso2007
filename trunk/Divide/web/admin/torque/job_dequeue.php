<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<? include_once("const.inc.php"); ?>
<html>
<head>
<title></title>
</head>	
<body>
	<?
	print("<span style='font-size: 2.0em'>Job dequeue</span>&nbsp;".
		"<span style='font-size: 0.7em'>[<a href='job_status.php'>volver</a>]</span>");
	if (ISSET($_REQUEST["id"])) {
		$id = $_REQUEST["id"];
		$qdel_result = `ssh -l $username $host "$qdel_cmd $id; exit" 2>&1`;

		if ($qdel_result=="") {
			print("<pre>Ready.</pre>");
		} else {
			print("<pre>$qdel_result<pre>");
		}
	} else {
		print("Debe especificar un id de trabajo (?id=).");	
	}
	?>
</body>
</html>

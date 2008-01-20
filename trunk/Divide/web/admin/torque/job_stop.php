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

	/*	u - USER
		o - OTHER
		s - SYSTEM
		n - None */
	$held_type = "u";

	print("<span style='font-size: 2.0em'>Job held</span>&nbsp;".
		"<span style='font-size: 0.7em'>[<a href='job_status.php'>volver</a>]</span>");
	if (ISSET($_REQUEST["id"])) {
		$id = $_REQUEST["id"];
		$qhold_result = `ssh -l $username $host "$qstop_cmd -h $held_type $id; exit" 2>&1`;

		if ($qhold_result=="") {
			print("<pre>Ready.</pre>");
		} else {
			print("<pre>$qhold_result<pre>");
		}
	} else {
		print("Debe especificar un id de trabajo (?id=).");	
	}
	?>
</body>
</html>

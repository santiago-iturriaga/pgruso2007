<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<? include_once("const.inc.php"); ?>
<html>
<head>
<title></title>
</head>
<body>
	<?
	if (ISSET($_REQUEST["id"])) {
		$id = $_REQUEST["id"];
		$accion = "";

		if (ISSET($_REQUEST["suspend"])) {
			$accion = "Suspend";
			$runjob = `ssh -l $username $host "$runjob_cmd -s $id; exit" 2>&1`;
		} else {
			$accion = "Run";
			$runjob = `ssh -l $username $host "$runjob_cmd $id; exit" 2>&1`;
		}

		print("<span style='font-size: 2.0em'>$accion job</span>&nbsp;".
			"<span style='font-size: 0.7em'>[<a href='jobs.php'>volver</a>]</span>");

		if ($runjob == "") {
			print("<pre>Ready.</pre>");
		} else {
			print("<pre>$runjob</pre>");
		}
		
	} else {
		print("<pre>Error!</pre>");
	}
	?>
</body>
</html>
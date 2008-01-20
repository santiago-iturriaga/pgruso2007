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

	if (ISSET($_REQUEST["id"])) {
		$id = $_REQUEST["id"];
		$canceljob = `ssh -l $username $host "$canceljob_cmd $id; exit" 2>&1`;
		print("<span style='font-size: 2.0em'>Cancel job</span>&nbsp;".
			"<span style='font-size: 0.7em'>[<a href='jobs.php'>volver</a>]</span>");

		if ($canceljob == "") {
			print("<pre>Ready.</pre>");
		} else {
			print("<pre>$canceljob</pre>");
		}
		
	} else {
		print("<pre>Error!</pre>");
	}
	?>
</body>
</html>
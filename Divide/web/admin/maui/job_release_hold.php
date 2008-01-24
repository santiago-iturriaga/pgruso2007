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
		$releasehold = `ssh -l $username $host "$releasehold_cmd $id; exit" 2>&1`;
		print("<span style='font-size: 2.0em'>Release hold job</span>&nbsp;".
			"<span style='font-size: 0.7em'>[<a href='jobs.php'>volver</a>]</span>");

		if ($releasehold == "") {
			print("<pre>Ready.</pre>");
		} else {
			print("<pre>$releasehold</pre>");
		}
		
	} else {
		print("<pre>Error!</pre>");
	}
	?>
</body>
</html>
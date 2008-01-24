<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<? include_once("const.inc.php"); ?>
<html>
<head>
<title></title>
</head>
<body>
	<?
	$selected=0;
	include_once("menu.inc.php");

	if (ISSET($_REQUEST["id"])) {
		$id = $_REQUEST["id"];
		$checkjob_job = `ssh -l $username $host "$checkjob_cmd -v $id; exit" 2>&1`;
		print("<span style='font-size: 2.0em'>Job status</span>&nbsp;".
			"<span style='font-size: 0.7em'>[<a href='jobs.php'>volver</a>]</span>");
		print("<pre>$checkjob_job</pre>");
		
	} else {
		print("<pre>Error!</pre>");
	}
	?>
</body>
</html>
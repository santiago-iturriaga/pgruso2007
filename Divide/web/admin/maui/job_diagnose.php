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
	if (ISSET($_REQUEST["id"])) {
		$id = $_REQUEST["id"];
		$diagnose = `ssh -l $username $host "$diagnose_cmd -j $id; exit" 2>&1`;
		print("<span style='font-size: 2.0em'>Diagnose job</span>&nbsp;".
			"<span style='font-size: 0.7em'>[<a href='jobs.php'>volver</a>]</span>");

		if ($diagnose == "") {
			print("<pre>Error! Empty?</pre>");
		} else {
			$cabezal=array("Name","State","Par","Proc","QOS","WCLimit","R","Min","User","Group","Account","QueuedTime","Network","Opsys","Arch","Mem","Disk","Procs","Class Features");
			parsear_tabla_una_fila($diagnose,$cabezal,false);
		}
		
	} else {
		print("<pre>Error!</pre>");
	}
	?>
</body>
</html>
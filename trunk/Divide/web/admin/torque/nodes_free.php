<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<? include_once("const.inc.php"); ?>
<html>
<head>
<title></title>
</head>
<body>
	<?
	print("<span style='font-size: 2.0em'>Node enabled</span>&nbsp;".
		"<span style='font-size: 0.7em'>[<a href='nodes.php'>volver</a>]</span>");
	if (ISSET($_REQUEST["id"])) {
		$id = $_REQUEST["id"];
		$qnodes_result = `ssh -l $username $host "$qnodes_cmd -c $id; exit" 2>&1`;

		if ($qnodes_result=="") {
			print("<pre>Ready.</pre>");
		} else {
			print("<pre>$qnodes_result<pre>");
		}
	} else {
		print("Debe especificar un id de nodo (?id=).");	
	}
	?>
</body>
</html>
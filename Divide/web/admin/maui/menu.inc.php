<?
	if (!ISSET($selected)) {
		$selected = -1;
	}

	$jobs=0;
	$nodes=1;
	$status=2;

	echo("<span style='font-size: 1.0em'><</span>");
	echo("<span style='font-size: 2.0em'>Maui</span>");
	echo("<span style='font-size: 1.0em'>>&nbsp&nbsp</span>");

	echo("<span style='font-size: 2.0em'>");
	if ($selected!=$jobs) {echo("<a href='jobs.php'>");}
	echo("Jobs");
	if ($selected!=$jobs) {echo("</a>");}
	echo("</span>");

	echo("<span style='font-size: 1.0em'>");
	echo("&nbsp;/&nbsp;");
	echo("</span>");

	echo("<span style='font-size: 2.0em'>");
	if ($selected!=$nodes) {echo("<a href='nodes.php'>");}
	echo("Nodes");
	if ($selected!=$nodes) {echo("</a>");}
	echo("</span>");

	echo("<span style='font-size: 1.0em'>");
	echo("&nbsp;/&nbsp;");
	echo("</span>");

	echo("<span style='font-size: 2.0em'>");
	if ($selected!=$status) {echo("<a href='status.php'>");}
	echo("Status");
	if ($selected!=$status) {echo("</a>");}
	echo("</span>");

	print("<hr />");
?>
<?
	include_once("const.inc.php");

	if (!ISSET($selected)) {
		$selected = -1;
	}

	echo("<span style='font-size: 1.0em'><</span>");
	echo("<span style='font-size: 2.0em'>Torque</span>");
	echo("<span style='font-size: 1.0em'>>&nbsp&nbsp</span>");

	echo("<span style='font-size: 2.0em'>");
	if ($selected!=$menu_jobs) {echo("<a href='job_status.php'>");}
	echo("Jobs");
	if ($selected!=$menu_jobs) {echo("</a>");}
	echo("</span>");

	echo("<span style='font-size: 1.0em'>");
	echo("&nbsp;/&nbsp;");
	echo("</span>");

	echo("<span style='font-size: 2.0em'>");
	if ($selected!=$menu_queues) {echo("<a href='queues_status.php'>");}
	echo("Queues");
	if ($selected!=$menu_queues) {echo("</a>");}
	echo("</span>");

	echo("<span style='font-size: 1.0em'>");
	echo("&nbsp;/&nbsp;");
	echo("</span>");

	echo("<span style='font-size: 2.0em'>");
	if ($selected!=$menu_nodes) {echo("<a href='nodes.php'>");}
	echo("Nodes");
	if ($selected!=$menu_nodes) {echo("</a>");}
	echo("</span>");

	echo("<span style='font-size: 1.0em'>");
	echo("&nbsp;/&nbsp;");
	echo("</span>");

	echo("<span style='font-size: 2.0em'>");
	if ($selected!=$menu_status) {echo("<a href='status.php'>");}
	echo("Status");
	if ($selected!=$menu_status) {echo("</a>");}
	echo("</span>");

	print("<hr />");
?>
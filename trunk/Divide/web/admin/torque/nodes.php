<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<? include_once("const.inc.php"); ?>
<html>
<head>
<title></title>
</head>
<body>
	<?
	$selected=$menu_nodes;
	include_once("menu.inc.php");

	$username = "santiago";
	$host = "localhost";

	$qnodes = `ssh -l $username $host "$qnodes_cmd -x; exit" 2>&1`;

	print("<span style='font-size: 2.0em'>Nodes Status</span>");

	$nodo=stristr($qnodes,"<Node>");
	while ($nodo != "") {
		print("<table border=1>");
	
		$name=stristr($nodo,"<name>");
		$name_fin=stripos($name,"</name>");
		$name_value=substr(substr($name,0,$name_fin),6);

		$state=stristr($nodo,"<state>");
		$state_fin=stripos($state,"</state>");
		$state_value=substr(substr($state,0,$state_fin),7);

		$np=stristr($nodo,"<np>");
		$np_fin=stripos($np,"</np>");
		$np_value=substr(substr($np,0,$np_fin),4);

		$ntype=stristr($nodo,"<ntype>");
		$ntype_fin=stripos($ntype,"</ntype>");
		$ntype_value=substr(substr($ntype,0,$ntype_fin),7);

		$status=stristr($nodo,"<status>");
		$status_fin=stripos($status,"</status>");
		$status_value=substr(substr($status,0,$status_fin),8);

		print("<tr><td style='font-weight:bold;'>Name</td><td>$name_value</td></tr>");

		echo("<tr><td style='font-weight:bold;'>State</td><td>$state_value");
		if (trim(strtolower($state_value))=="offline") {
			echo("&nbsp;<a href='nodes_free.php?id=$name_value'>[enable]</a>");
		} else if (trim(strtolower($state_value))=="down") {
			//esta down... no hay nada que hacer
		} else { 
			echo("&nbsp;<a href='nodes_offline.php?id=$name_value'>[disable]</a>");
		}
		echo("</td></tr>");	

		print("<tr><td style='font-weight:bold;'>Processors</td><td>$np_value</td></tr>");
		print("<tr><td style='font-weight:bold;'>Type</td><td>$ntype_value</td></tr>");
		print("<tr><td style='font-weight:bold;'>Status</td><td><pre>".implode("\n",explode(",",$status_value))."</pre></td></tr>");

		print("</table><br />");

		$nodo=stristr(stristr($nodo,"</Node>"),"<Node>");
	}
	?>
</body>
</html>
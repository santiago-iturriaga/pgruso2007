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
	/*
	-b // BLOCKED QUEUE
	-i // IDLE QUEUE
	-r // ACTIVE QUEUE
	*/
	//print("<div style='font-size: 2.0em'>Jobs</div>");
	$selected=0;
	include_once("menu.inc.php");

	$jobs_active = `ssh -l $username $host "$jobs_cmd -r; exit" 2>&1`;
	print("<div><span style='font-size: 1.5em'>Active</span>");
	$cabezal_active = array("JobName","S Par","Effic","XFactor","Q","User","Group","MHost","Procs","Remaining","StartTime");
	parsear_tabla($jobs_active,$cabezal_active,0);
	print("</div>");

	$jobs_idle = `ssh -l $username $host "$jobs_cmd -i; exit" 2>&1`;
	print("<br/><div><span style='font-size: 1.5em'>Idle</span>");
	$cabezal_idle = array("JobName","Priority","XFactor","Q","User","Group","Procs","WCLimit","Class","SystemQueueTime");
	parsear_tabla($jobs_idle,$cabezal_idle,1);
	print("</div>");

	$jobs_blocked = `ssh -l $username $host "$jobs_cmd -b; exit" 2>&1`;
	print("<br/><div><span style='font-size: 1.5em'>Blocked</span>");
	$cabezal_blocked = array("JobName","User","Reason");
	parsear_tabla($jobs_blocked,$cabezal_blocked,2);
	print("</div>");
	?>
</body>
</html>

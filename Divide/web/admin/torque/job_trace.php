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
		$qstat_job = `ssh -l $username $host "$qstat_cmd -f $id; exit" 2>&1`;
		print("<span style='font-size: 2.0em'>Job status</span>&nbsp;".
			"<span style='font-size: 0.7em'>[<a href='job_status.php'>volver</a>]</span>");
		print("<pre>$qstat_job</pre>");
		
	} else {
		// Listado de todos los trabajos
		$qstat = `ssh -l $username $host "$qstat_cmd; exit" 2>&1`;
		
		print("<span style='font-size: 2.0em'>Jobs</span>");
		print("<table border=1>");

		if ($qstat == "") {
			print("<pre>Empty.</pre>");
		} else {
			print("<br/><br/>");
			$qstat_lines = explode("\n", $qstat);
	
			// Arreglo los encabezados (hay problemas con los encabezados con espacios :S)
			$qstat_lines[0] = str_replace("Job id","Id",$qstat_lines[0]);
			$qstat_lines[0] = str_replace("Time Use","Use",$qstat_lines[0]);
			$qstat_lines[0] = str_replace("S", "Status",$qstat_lines[0]);

			// Columna que contiene el status del trabajo
			$status_column = 4;
	
			for ($linea = 0; $linea < sizeof($qstat_lines) - 1; $linea++) {
				// Salteo la linea con '---------------'
				if ($linea != 1) {
					if ($linea == 0) {
						print("<tr style='font-size: 1.0em;font-weight: bold'>");
					} else {
						print("<tr>");
					}
	
					// Elimino espacios sobrantes
					$qstat_lines_sin_espacios = ereg_replace('(  *)', " ", $qstat_lines[$linea]);
	
					// Separo segun espacios
					$qstat_line = explode(" ", $qstat_lines_sin_espacios);
					
					if ($linea >= 2) {
						list($jobID, $maquina) = split("\.",$qstat_line[0],2);
					}
	
					for ($columna = 0; $columna < sizeof($qstat_line); $columna++) {
						print("<td>");
						if ($columna == 0 && $linea >= 2) {
							print("<a href='job_status.php?id=$jobID'>$jobID</a></td>");
						} else {
							if ($columna == $status_column) {
								switch($qstat_line[$columna]) {
									case "H":
										print("Held</td>");
										break;
									case "Q":
										print("Queued</td>");
										break;
									case "R":
										print("Running</td>");
										break;
									default:
										print($qstat_line[$columna]."</td>");
										break;
								}
							} else {
								print($qstat_line[$columna]."</td>");
							}
						}
					}
	
					if ($linea >= 2) {
						print("<td><a href='job_dequeue.php?id=$jobID'>Delete</a></td>");

						if ($qstat_line[$status_column] == "H") {
							print("<td><a href='job_release_hold.php?id=$jobID'>Release hold</a></td>");
						} else {
							print("<td><a href='job_hold.php?id=$jobID'>Hold</a></td>");
						}
					}
	
					print("</tr>");
				}
			}
			print("</table>");
		}
	}
	?>
</body>
</html>

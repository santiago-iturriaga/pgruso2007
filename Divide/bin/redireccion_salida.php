#!/usr/bin/php5 -q
<?php
	if(!defined("STDIN")) {
        	define("STDIN", fopen('php://stdin','r'));
    	}

   	while (!feof(STDIN)) {
        	$bufer = fgets(STDIN);

		if ($argc == 2) {
        		$FILE = fopen($argv[1],'a');
        		fwrite($FILE,date("Y-m-d H:i.s")." ".$bufer);
        		fclose($FILE);
		}

        	echo $bufer;
    	}
?>

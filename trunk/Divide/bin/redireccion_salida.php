#!/usr/bin/php -q
<?php
	if ($argc != 2 ) {
		echo "Error: Cantidad de parametros incorrecta";
		exit;
	}
	if(!defined("STDIN")) {
        define("STDIN", fopen('php://stdin','r'));
    }
    while (!feof(STDIN)) {
        $bufer = fgets(STDIN);
        $FILE = fopen($argv[1],'a');
        fwrite($FILE,$bufer);
        fclose($FILE);
        echo $bufer;
    }

?>

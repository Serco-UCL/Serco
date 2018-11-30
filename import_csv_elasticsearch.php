<?php

if(count($argv) == 4)
{
	$csv = array_map('str_getcsv', file($argv[1]));

	$tabText = $csv[0];

	for ($i=1; $i < count($csv); $i++)
	{
		$tabFinal = [];
		$textFinal = '';
		for ($j=0; $j < count($tabText); $j++)
		{
			$tabFinal[$j] = '\"'.$tabText[$j].'\":\"'.$csv[$i][$j].'\"';
		}
		$textFinal = "{ ".implode(',', $tabFinal)." }";

		//file_put_contents("/var/www/html/serviceTest/testAyoub.txt", 'curl -XPOST "http://130.104.12.34:9200/'.$argv[2].'/symptome" -H "Content-Type: application/json" -u elastic:XXXX -d "'.$textFinal.'"'.PHP_EOL.PHP_EOL, FILE_APPEND);

		exec('curl -XPOST "http://130.104.12.34:9200/'.$argv[2].'/'.$argv[3].'" -H "Content-Type: application/json" -u elastic:XXXX -d "'.$textFinal.'"');
	}
}
else
{
	echo "Pour exécuté, ce fichier il faut lui passé deux arguments qui sont le fichier CSV et ensuite le nom de l'index.".PHP_EOL;
}

<?php


	$a=$_POST["edge"];
	$b=$_POST["kernel"];
	$c=$_POST["low"];
	$outputdir='/var/www/symfv2/Enterface16/UserResults/31/Current/Results';
	$myfile = fopen($outputdir ."/file.txt", "w");
    fwrite($myfile,"test");
    //fwrite($myfile, $a." ".$b." ".$c);
    fclose($myfile);
    echo $b;

?>
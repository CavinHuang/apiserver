<?php
	
	$fileurl = $_GET['fileurl'];
	$dfilename = $_GET['dfilename'];
	
	$filename=$fileurl;
	$file  =  fopen($filename, "rb"); 
	Header( "Content-type:  application/octet-stream "); 
	Header( "Accept-Ranges:  bytes "); 
	Header( "Content-Disposition:  attachment;  filename={$dfilename}"); 
	$contents = "";
	while (!feof($file)) {
	 $contents .= fread($file, 8192);
	}
	echo $contents;
	fclose($file); 	
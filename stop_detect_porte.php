<?php
include 'detect_porte.class.php';


//echo 'début </br>';

$odetect_porte = new detect_porte();

$odetect_porte->stop_detect();



/*
   //suppression fichier
   $fichier = 'running_flag.txt';
   $fichier2 = 'running_C.txt';

   if( file_exists ( $fichier)){
     unlink( $fichier ) ;
     echo 'running_flag supprimé </br>';
  }
   if( file_exists ( $fichier2)){
     unlink( $fichier2 ) ;
    echo 'running_C supprimé </br>';
  }
  
*/  
  
echo 'stop </br>';     
?>
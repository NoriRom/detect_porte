<?php
include 'detect_porte.class.php';


//echo 'd�but </br>';

$odetect_porte = new detect_porte();

$result=$odetect_porte->launch_detect();

echo $result;


//fwrite($fp,"1\n");
//echo 'fwrite ok ';

/*
//cr�ation du fichier de flag disant au programme de continuer tant qu'il existe  
$fp=fopen("running_flag.txt","c+");
if (fwrite($fp,"1\n")) { 
echo "Le fichier � �t� cr�� avec succ�s</br>"; 
} else { 
// Erreur 
echo "Impossible de cr�er le fichier</br>"; 
} 
fclose($fp);
//lancement du programme de d�tection



//on v�rifie que le fichier de flag du prog C n'existe pas sinon on va lancer 2 programmes en parall�le
$fichier = 'running_C.txt';
if( file_exists ( $fichier))
  echo "le programme est d�j� en train de tourner</br>";
else
  exec("sudo /var/www/detect_porte/detect_porte /var/www/detect_porte/detect_porte.php  1");
*/

/*
   //suppression fichier
   $fichier = 'running_flag.txt';

   if( file_exists ( $fichier))
     unlink( $fichier ) ;
     
*/
//system('sudo echo "test" > /var/www/detect_porte/running_flag.txt ') ;

?>
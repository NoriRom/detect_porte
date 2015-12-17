<?php
/*
@name detectporte
@author Nori
@link http://pasdelien.com 
@licence Nori
@version 1.0.0
@description Plugin pour la detection de porte sans fil
*/

include 'detect_porte.class.php';
$odetect_porte = new detect_porte();


if(isset($_GET['action'])){
switch($_GET['action']){
case 'status' :
      //statut de la detection
  $result=$odetect_porte->get_detect_status();
  echo $result;
  break;
case 'option' :
      //sauvegarde des options
  if(isset($_GET['video'])) 
    $video=$_GET['video'];
  else 
    $video="0" ;
  if(isset($_GET['tv'])) 
    $tv=$_GET['tv'];
  else 
    $tv="0"  ;
  if(isset($_GET['mail'])) 
    $mail=$_GET['mail'];
  else 
    $mail="0" ;
  if(isset($_GET['texto'])) 
    $texto=$_GET['texto'];
  else 
    $texto="0" ; 
      
  $result=$odetect_porte->manage_option($video,$tv,$mail,$texto);
  echo $result;
  break;
case 'showoption' :
   $result=$odetect_porte->get_option();
   break;
case 'showvideo' :
   $result=$odetect_porte->get_option("video");
   break;
case 'showtele' :
   $result=$odetect_porte->get_option("tele");
   break;
case 'showmail' :
   $result=$odetect_porte->get_option("mail");
   break;
case 'showsms' :
   $result=$odetect_porte->get_option("sms");  
   break;

  

}
}else{
echo "Error : no action value !";
}




/*
$csv=csv_to_array('detect_option.csv',',');
echo "video : ".$csv[0]['video'];
echo"</br>";
echo "tele : ".$csv[0]['tele'];
/*echo"</br>";     
$csv = array_map('str_getcsv', file('detect_option.csv'));
 var_dump($csv);
 echo "val : ".$csv[0][0];
 */

 //array_to_csv($csv,'test.csv',',');
 

 


?>

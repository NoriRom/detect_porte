<?php

/*
 @nom: detect_porte
 @auteur: Nori
 @description:  Classe pour la d�tection de fermeture de porte
 */


class detect_porte /*extends SQLiteEntity*/{

   public function __construct ()
    {
    }

	function launch_detect(){

    //on v�rifie que le fichier de flag du prog C n'existe pas sinon on va lancer 2 programmes en parall�le
    $fichier = '/var/www/yana-server/plugins/detect_porte/running_flag.txt';
    if( file_exists ( $fichier))
      //echo "le programme est d�j� en train de tourner</br>";
      return 0;
    else{
      popen("sudo /var/www/yana-server/plugins/detect_porte/detect_porte /var/www/yana-server/plugins/detect_porte/detect_porte.php  1 > /dev/null &","r");
      //echo "Le programme est lanc�</br>";
      return 1;
    }
    //var_dump(file_exists ( $fichier));
    //  return "Done";
	}

	
	function stop_detect(){
     //suppression fichier
     $fichier = '/var/www/yana-server/plugins/detect_porte/running_flag.txt';
     $fichier2 = '/var/www/yana-server/plugins/detect_porte/running_C.txt';
  
     if( file_exists ( $fichier)){
       unlink( $fichier ) ;
       //echo 'running_flag supprim� </br>';
       exec('sudo pkill -f detect_porte');
       return 1;
    }else{
    return 0;
    }
     if( file_exists ( $fichier2)){
       unlink( $fichier2 ) ;
      //echo 'running_C supprim� </br>';
      exec('sudo pkill -f detect_porte');
      return 1;
    }else{
    return 0;
    }
    
  } 
  
  function get_detect_status(){
  //on v�rifie que le fichier de flag du prog C n'existe pas sinon on va lancer 2 programmes en parall�le
    $fichier = '/var/www/yana-server/plugins/detect_porte/running_flag.txt';
    if( file_exists ( $fichier))
      //echo "le programme est d�j� en train de tourner</br>";
      return 1;
    else{
      //echo "Le programme est lanc�</br>";
      return 0;
    }
    //var_dump(file_exists ( $fichier));
    //  return "Done";
  }
  
  function manage_option($video,$tv,$mail,$texto){
  /*on v�rifie que le fichier csv existe et on initialise les variables
    $fichier = 'detect_option.csv';
    if( file_exists ( $fichier))
      //le fichier existe
      $csv=csv_to_array('detect_option.csv',',');
    else{
      //le fichier n'existe pas
      $csv= array(
      0=> array (
        "video" => "0",
        "tele" => "0",
        "mail"=> "0",
        "texto"=> "0"  )
      );
    }    */
    
    $csv= array(
      0=> array (
        "video" => $video,
        "tv" => $tv,
        "mail"=>$mail,
        "texto"=> $texto  )
      );
       //sauvegarde dans le fichier
      $this->array_to_csv($csv,'/var/www/yana-server/plugins/detect_porte/detect_option.csv',',');
      
    return 1;
    
  }
  
  function get_option($option='rien') {
      $data=$this->csv_to_array('/var/www/yana-server/plugins/detect_porte/detect_option.csv');
      if($option=='rien')var_dump($data);
      if($option=='video')echo $data[0]["video"];
      if($option=='tele')echo $data[0]["tv"];
      if($option=='sms')echo $data[0]["texto"];
      if($option=='mail')echo $data[0]["mail"];       
      
  }
  
  
  function csv_to_array($filename='', $delimiter=',')
{
    if(!file_exists($filename) || !is_readable($filename))
        return FALSE;

    $header = NULL;
    $data = array();
    if (($handle = fopen($filename, 'r')) !== FALSE)
    {

        while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
        {    
            if(!$header)
                $header = $row;

            else    
                $data[] = array_combine($header, $row);

        }
        fclose($handle);
    }

    return $data;
}

 function array_to_csv($data,$filename='', $delimiter=',')
 {

$file = fopen($filename,"w");

fputcsv($file,array_keys($data[0]));
foreach ($data as $line)
  {
  fputcsv($file,$line);
  }
  
  

fclose($file);
 }
 
 
//fonction pour lancer les diff�rentes options
function active_options(){
$csv=$this->csv_to_array('/var/www/yana-server/plugins/detect_porte/detect_option.csv',',');

if($csv[0]["tv"]=="1")
  $this->allume_tv();
if($csv[0]["texto"]=="1")
  $this->send_texto();
if($csv[0]["mail"]=="1")
  $this->send_mail();
if($csv[0]["video"]=="1")
  $this->enr_video();

 return 1;
}

function send_texto(){
//envoie d'un texto
$return_textopage=file_get_contents("https://smsapi.free-mobile.fr/sendmsg?user=14470979&pass=3JFhJ0emi65819&msg=ALERTE%20INTRUSION%20APPART.%20le%20".date('Y/m/d')."%20a%20".date('H:i:s'));
}



function send_mail(){
//Envoie de mail
$to = "romain.tomasoni@gmail.com";
$subject = "Urgent";

$message="<html><head></head><body>Intrusion dans l'appart</body></html>";

$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";

$flag=mail($to, $subject, $message, $headers);
//echo 'Mail envoy� ='.$flag;

}

function allume_tv(){
//allumage TV
 $return_tvpage=file_get_contents('http://192.168.0.117/yana-server/action.php?action=tv_start&token=de120821c802d57f3bd4c1bf264a25ae4a4c8bce');
 //echo "Tv allum�e!";
}
 
function enr_video(){
 $return_tvpage=file_get_contents('http://192.168.0.117/yana-server/action.php?action=video_record&time=15');
}


  

}

?>
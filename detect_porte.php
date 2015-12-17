<?php
/*
Cette page r�cupere les informations du signal radio recu par le raspberry PI et effectue une action
en fonction de ces derni�res.

NB : Cette page est appell�e en parametre du programme C 'radioReception', vous voupvez tout � fait
appeller une autre page en renseignant le parametre lors de l'execution du programme C.

@author : Valentin CARRUESCO (idleman@idleman.fr)
@licence : CC by sa (http://creativecommons.org/licenses/by-sa/3.0/fr/)
RadioPi de Valentin CARRUESCO (Idleman) est mis � disposition selon les termes de la 
licence Creative Commons Attribution - Partage dans les M�mes Conditions 3.0 France.
Les autorisations au-del� du champ de cette licence peuvent �tre obtenues � idleman@idleman.fr.
*/


//R�cuperation des parametres du signal sous forme de variables
//list($file,$step,$cpt) = $_SERVER['argv'];
//Affichages des valeurs dans la console a titre informatif
//echo "\nemetteur : $sender,\n Groupe :$group,\n on/off :$state,\n boutton :$interruptor";


//echo $argv[1];  //$step
//echo $argv[2];  //$cpt
echo "step=". $argv[1]." et cpt=".$argv[2]."\n";
//echo "step=".$_GET['step']." et cpt=".$_GET['cpt'];


include 'detect_porte.class.php';
$odetect_porte = new detect_porte();

$result=$odetect_porte->active_options();
echo $result;


/*************************************************************************/
//Envoie de mail DEBUG
$to = "romain.tomasoni@gmail.com";
$subject = "Urgent";

$message="<html><head></head><body>DEBUG DETECTION APPART : <\br> step=". $argv[1]." et cpt=".$argv[2]."<\br> </body></html>";

$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";

$flag=mail($to, $subject, $message, $headers);
//echo 'Mail envoy� ='.$flag;
/*************************************************************************/




?>
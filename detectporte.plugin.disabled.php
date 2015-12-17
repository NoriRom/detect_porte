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

function detectporte_vocal_command(&$response,$actionUrl){
	global $conf;

$response['commands'][] = array(
		'command'=>'je sors',
		'url'=>$actionUrl.'?action=detectporte_sors','confidence'=>'0.90'
    ); 
$response['commands'][] = array(
		'command'=>'je pars',
		'url'=>$actionUrl.'?action=detectporte_sors','confidence'=>'0.90'
    ); 	
$response['commands'][] = array(
		'command'=>$conf->get('VOCAL_ENTITY_NAME')." j'y vais",
		'url'=>$actionUrl.'?action=detectporte_sors','confidence'=>'0.90'
    ); 		
$response['commands'][] = array(
		'command'=>$conf->get('VOCAL_ENTITY_NAME')." j'y vais",
		'url'=>$actionUrl.'?action=detectporte_sors','confidence'=>'0.90'
    );
 	
$response['commands'][] = array(
		'command'=>$conf->get('VOCAL_ENTITY_NAME')." passe en mode sécurité",
		'url'=>$actionUrl.'?action=detectporte_securite','confidence'=>'0.90'
    );

$response['commands'][] = array(
		'command'=>$conf->get('VOCAL_ENTITY_NAME')." active la sécurité",
		'url'=>$actionUrl.'?action=detectporte_securite','confidence'=>'0.90'
    );	

$response['commands'][] = array(
		'command'=>$conf->get('VOCAL_ENTITY_NAME')." désactive la sécurité",
		'url'=>$actionUrl.'?action=detectporte_desactive','confidence'=>'0.90'
    );	
	
$response['commands'][] = array(
		'command'=>"c'est moi",
		'url'=>$actionUrl.'?action=detectporte_rentre','confidence'=>'0.90'
    );	

$response['commands'][] = array(
		'command'=>"je suis rentré",
		'url'=>$actionUrl.'?action=detectporte_rentre','confidence'=>'0.90'
    );	
	
}

function detectporte_action(){
	global $_,$conf;
	
	$odetect_porte = new detect_porte();
  
  	

	switch($_['action']){
		case 'detectporte_sors':
			global $_;
      //activation de la détection
  $result=$odetect_porte->launch_detect();
  
			if ($result==1)
      {	$possible_answers = array(
					'OK. Jactive la sécurité.'
					,'Jactive la sécurité. Passe une bonne journée'
					,'OK. Jactive la sécurité. A plus'
					,'OK. Je moccupe du reste'
					
				);
        }
        else  {
        $possible_answers = array(
					'OK. La sécurité tourne déjà.'
					,' La sécurité tourne déjà. Passe une bonne journée'
					,'OK.  La sécurité tourne déjà. A plus'
					,'OK. Je moccupe du reste'
				);
        }
				
				$affirmation = $possible_answers[rand(0,count($possible_answers)-1)];
				$response = array('responses'=>array(
										array('type'=>'talk','sentence'=>$affirmation)
													)
								);
				$json = json_encode($response);
				echo ($json=='[]'?'{}':$json);
				

		break;

case 'detectporte_securite':
			global $_;
      //activation de la détection
				$result=$odetect_porte->launch_detect();
        
					if ($result==1)
      {	$possible_answers = array(
					'Sécurité activée.',
					"OK. C'est fait"
					
				);
        }
        else  {
        $possible_answers = array(
					'Il tourne déjà'
				);
        }
				
				$affirmation = $possible_answers[rand(0,count($possible_answers)-1)];
				$response = array('responses'=>array(
										array('type'=>'talk','sentence'=>$affirmation)
													)
								);
				$json = json_encode($response);
				echo ($json=='[]'?'{}':$json);
				
				
				
		break;
		
       

	
	case 'detectporte_desactive':
			global $_;
      //activation de la détection
				$result=$odetect_porte->stop_detect();
        
				if ($result==1)
      {	$possible_answers = array(
					'Sécurité désactivée.'
					
				);
        }
        else  {
        $possible_answers = array(
					'Il est déjà stopé',
          'Il ne tournait pas'
				);
        }
				
				$affirmation = $possible_answers[rand(0,count($possible_answers)-1)];
				$response = array('responses'=>array(
										array('type'=>'talk','sentence'=>$affirmation)
													)
								);
				$json = json_encode($response);
				echo ($json=='[]'?'{}':$json);
				
				
				
		break;
		
	case 'detectporte_rentre':
			global $_;
      //activation de la détection
				$result=$odetect_porte->stop_detect();
        
				if ($result==1)
      {	$possible_answers = array(
					'Bienvenu. Je désactive la Sécurité.',
					"Coucou. j'ai coupé la sécurité"
					
				);
        }
        else  {
        $possible_answers = array(
					'Bienvenu. ',
					'Coucou.'
				);
        }
				
				$affirmation = $possible_answers[rand(0,count($possible_answers)-1)];
				$response = array('responses'=>array(
										array('type'=>'talk','sentence'=>$affirmation)
													)
								);
				$json = json_encode($response);
				echo ($json=='[]'?'{}':$json);
				
				
				
		break;
		
    } 	
}


Plugin::addHook("action_post_case", "detectporte_action");    
Plugin::addHook("vocal_command", "detectporte_vocal_command");
?>

/*
Cette page rÃ©cupere les informations du signal radio recu par le raspberry PI et execute une page PHP
en lui fournissant tout les paramÃªtres.

compiler ce source via la commande :
	g++ /var/www/yana-server/plugins/detect_porte/detect_porte.cpp -o /var/www/yana-server/plugins/detect_porte/detect_porte -lwiringPi
	
installer auparavant la librairie wiring pi

lancer le programme via la commande :
	sudo chmod 777 detect_porte
	./detect_porte /var/www/yana-server/plugins/detect_porte/detect_porte.php  0
 ou
 
   sudo /var/www/yana-server/plugins/detect_porte/detect_porte /var/www/yana-server/plugins/detect_porte/detect_porte.php  1


	Les deux parametres de fin Ã©tant le chemin vers le PHP a appeller, et le numÃ©ro wiringPi du PIN reliÃ© au rÃ©cepteur RF 433 mhz
	
@author : Nori
@contributors : Idleman
@webPage : http://nothing.fr
@references & Libraries: https://projects.drogon.net/raspberry-pi/wiringpi/, http://playground.arduino.cc/Code/HomeEasy
@licence : CC by sa 
*/


#include <wiringPi.h>
#include <iostream>
#include <stdio.h>
#include <sys/time.h>
#include <time.h>
#include <stdlib.h>
#include <sched.h>
#include <sstream>
#include<string.h>

using namespace std;

//initialisation du pin de reception
int pin;

//Fonction de log
void log(string a){
	//DÃ©commenter pour avoir les logs
	//cout << a << endl;
}

//Fonction de conversion long vers string
string longToString(long mylong){
    string mystring;
    stringstream mystream;
    mystream << mylong;
    return mystream.str();
}

//Fonction de passage du programme en temps rÃ©el (car la reception se joue a la micro seconde prÃ¨s)
void scheduler_realtime() {
	struct sched_param p;
	p.__sched_priority = sched_get_priority_max(SCHED_RR);
	if( sched_setscheduler( 0, SCHED_RR, &p ) == -1 ) {
	perror("Failed to switch to realtime scheduler.");
	}
}

//Fonction de remise du programme en temps standard
void scheduler_standard() {
	struct sched_param p;
	p.__sched_priority = 0;
	if( sched_setscheduler( 0, SCHED_OTHER, &p ) == -1 ) {
	perror("Failed to switch to normal scheduler.");
	}
}

//Recuperation du temp (en micro secondes) d'une pulsation
int pulseIn(int pin, int level, int timeout)
{
   struct timeval tn, t0, t1;
   long micros;
   gettimeofday(&t0, NULL);
   micros = 0;
   while (digitalRead(pin) != level)
   {
      gettimeofday(&tn, NULL);
      if (tn.tv_sec > t0.tv_sec) micros = 1000000L; else micros = 0;
      micros += (tn.tv_usec - t0.tv_usec);
      if (micros > timeout) return 0;
   }
   gettimeofday(&t1, NULL);
   while (digitalRead(pin) == level)
   {
      gettimeofday(&tn, NULL);
      if (tn.tv_sec > t0.tv_sec) micros = 1000000L; else micros = 0;
      micros = micros + (tn.tv_usec - t0.tv_usec);
      if (micros > timeout) return 0;
   }
   if (tn.tv_sec > t1.tv_sec) micros = 1000000L; else micros = 0;
   micros = micros + (tn.tv_usec - t1.tv_usec);
   return micros;
}

//Programme principal
int main (int argc, char** argv)
{
 char Sstep [50];
 char Scpt [50];
 
	//On passe en temps rÃ©el
	scheduler_realtime();
  FILE* fichier = NULL;
  FILE* fichier_C = NULL;
	string command;
  int flag;
	string path = "php ";
	//on rÃ©cupere l'argument 1, qui est le chemin vers le fichier php
	path.append(argv[1]);
	log("Demarrage du programme");
	//on rÃ©cupere l'argument 2, qui est le numÃ©ro de Pin GPIO auquel est connectÃ© le recepteur radio
	pin = atoi(argv[2]);
	//Si on ne trouve pas la librairie wiringPI, on arrÃªte l'execution
    if(wiringPiSetup() == -1)
    {
        log("Librairie Wiring PI introuvable, veuillez lier cette librairie...");
        return -1;
    }else{
    	log("Librairie WiringPI detectee");
    }
    pinMode(pin, INPUT);
	log("Pin GPIO configure en entree");
    log("Attente d'un signal du transmetteur ...");

     
    //check le flag de running dans le fichier
    fichier = fopen("running_flag.txt", "a+");
    if (fichier != NULL)
    {
        fputc('1', fichier);
        fclose(fichier);
		flag=1;
    }
    else
    {
        printf("Impossible d'ouvrir le fichier running_flag.txt");
    }
 

 //check le flag de running dans le fichier
 /*  fichier = fopen("/var/www/detect_porte/running_flag.txt", "r+");

    if (fichier != NULL)
    {
        flag=1;
        fclose(fichier);
    }
    else
    {
        flag=0;
        printf("Impossible d'ouvrir le fichier Flag");
    } */


	//On boucle pour ecouter les signaux
	while(flag==1)
    {
    	int i = 0;
      int cpt = 0;
      int error = 0;
      int step = 0;
		unsigned long t = 0;
	    //avant dernier byte reÃ§u
		int prevBit = 0;
	    //dernier byte reÃ§u
		int bit = 0;
    		
		//mise a zero de l'idenfiant tÃ©lÃ©commande
	    unsigned long sender = 0;
		//mise a zero du groupe
	    bool group=false;
		//mise a zero de l'etat on/off
	    bool on =false;
		//mise a zero de l'idenfiant de la rangÃ©e de bouton
	    unsigned long recipient = 0;
		
		command = path+" ";
		t = pulseIn(pin, LOW, 1000000);
		
		//Verrou 1
	//	while(t < 10900 || t > 10960){
  	while(t < 10900 || t > 13000){
			t = pulseIn(pin, LOW,1000000);
     /* if(t > 1000 &&
      t < 20000
       //(t < 30000 || t > 31999)
       ){printf("t=%d \n",t);//test
      }*/      
		}
		log("Verrou 1 detecte");
    printf("t bon=%d \n",t);//test
		// donnÃ©es
    
		while(i < 64)
		{
      t=0;
			while(t < 1000){
      t = pulseIn(pin, LOW, 1000000);
      }

			printf("t bit=%d \n",t);//test
			//compte pour flag
	    //if((t > 1040 && t < 1100) || (t > 1099 && t < 1200))
      if((t > 1040 && t < 1500))
			{
				++cpt;
				error=0;
			}
			
	    else if(t > 10500 && t < 15000)
			{
      if(cpt>((step*10)-5)){
				++step;
				error=0;
        }
      else
        {	error=1;}
			}
			else
			{
			   if(error > 0){
          printf("Break!!\n");//test
				  break;
         }else{
          error=1;
         }
				
       
			}
		
      printf("cpt=%d \n step=%d \n",cpt,step);//test
     printf("i vaut  %d\n",i);//test
			prevBit = bit;
			++i;
		}
   
  //Si les donnÃ©es ont bien Ã©tÃ© dÃ©tÃ©ctÃ©es
    if(step>1 || 
    cpt>17){
	 /*
		log("------------------------------");
		log("Donnees detectees");
		cout << "sender " << sender << endl;
		
		//on construit la commande qui vas envoyer les parametres au PHP
		command.append(longToString(sender));
		if(group)
		{
			command.append(" on");
			cout << "group command" << endl;
		}
		else
		{
			command.append(" off");
			cout << "no group" << endl;
		}

		if(on)
		{
			command.append(" on");
			cout << "on" << endl;
		}
		else
		{
			command.append(" off");
			cout << "off" << endl;
		}
		command.append(" "+longToString(recipient));
		cout << "recipient " << recipient << endl;
		log("Execution de la commande PHP");   */
    

    //sprintf(command, " %d %d", step,cpt);
//     std::stringstream ss;
//ss << command << " " <<  step;
//command = ss.str();
//  cout << command << step<<" "<<cpt; 
sprintf (Sstep, "%d", step);
sprintf (Scpt, "%d", cpt);
//command.append(" ");
command.append(Sstep);
command.append(" ");
command.append(Scpt);


		//Et hop, on envoie tout Ã§a au PHP
    printf("Command = %s",command.c_str());
		system(command.c_str()); 
	}else{
		log("Aucune donnee...");
	}
	
    	delay(700);
      
      
    //check le flag de running dans le fichier
    fichier = fopen("running_flag.txt", "a+");
     if (fichier != NULL)
    {
        flag=1;
        fclose(fichier);
    }
    else
    {
        flag=0;
        printf("Impossible d'ouvrir le fichier Flag");
    }
    
    
     
 }
	  printf("Fin du programme");
	scheduler_standard();
  
}


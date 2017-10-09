<?php
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

class mochad extends eqLogic {
	public static function BusMonitor() {
		$address=config::byKey('mochadHost', 'mochad');
		$port=config::byKey('mochadPort', 'mochad');
		$socket = stream_socket_client("tcp://$address:$port", $errno, $errstr, 100);
		if (!$socket) 
			throw new Exception(__("$errstr ($errno)", __FILE__));
		log::add('mochad', 'debug', 'Démarrage du démon');
		while (!feof($socket)) { 
			$Ligne=stream_get_line($socket, 1000000,"\n");
			log::add('mochad', 'debug', $Ligne);
			$monitor['TxRx']=substr($Ligne, 15,2);
			if (strrpos($Ligne,"RFSEC")>0){
				log::add('mochad', 'debug', 'Analyse de l\'équipement de sécurité');
				$monitor['RfPl']=substr($Ligne, 18,5);
				$monitor['Message']=substr($Ligne, 24);
				self::X10SecurityAnalyse($monitor['Message']);
			}elseif (strrpos($Ligne,"House: ")>0 )	{
				log::add('mochad', 'debug', 'Analyse de l\'équipement');
				$monitor['RfPl']=substr($Ligne, 18,2);
				$monitor['Message']=substr($Ligne, 21);
				$Lignedouble.='&'.$monitor['Message'];
				self::X10Analyse($Lignedouble);
			}elseif (strrpos($Ligne,"HouseUnit: ")>0){
				$monitor['RfPl']=substr($Ligne, 18,2);
				$monitor['Message']=substr($Ligne, 21);
				$Lignedouble=$monitor['Message'];
			}
			self::addCacheMonitor($monitor);
		}
		fclose($socket); 
	}
	public static function X10Analyse($Message) {
		$Ligne=explode('&',$Message);
		$Etat=substr($Ligne[1],strrpos($Ligne[1],"Func: ")+6);	
		if ($Etat=="On")
			$EtatModule= 1;
		else
			$EtatModule= 0;
		log::add('mochad', 'debug', 'Etat mémorisé '.$EtatModule);
		$Unite=substr(substr($Ligne[0],strrpos($Ligne[0],"HouseUnit: ")+11),0,1);
		$Code=substr(substr($Ligne[0],strrpos($Ligne[0],"HouseUnit: ")+11),1,1);
		log::add('mochad', 'debug', 'Recherche du module '.$Unite.$Code);
		$Equipement = mochad::byLogicalId($Unite.'-'.$Code, 'mochad');
		if (is_object($Equipement)) {
			$State=$Equipement->getCmd('info','Etat_'.$Equipement->getId());
			if (is_object($State)) {
				$State->setCollectDate('');
				$State->event($EtatModule);
				$State->save();
			}
		}
	}
	public static function X10SecurityAnalyse($Message) {
		$Adresse=substr($Message,strrpos($Message,"Addr: ")+6,8);
		$Fonction=explode('_',substr($Message,strrpos($Message,"Func: ")+6));
		if ($Fonction[1]=="alert")
			$Valeur= 1;
		elseif ($Fonction[1]=="normal")
			$Valeur= 0;
		log::add('mochad', 'debug', 'Nom: '.$Fonction[count($Fonction)-1]);
		log::add('mochad', 'debug', 'Adresse: '.$Adresse);
		$Equipement = self::byLogicalId($Adresse, 'mochad');
		if (!is_object($Equipement) && config::byKey('autoAddDevice', 'mochad')==1) {
			log::add('mochad', 'debug', 'Création de l\'équipement de sécurité');
			$Equipement = new mochad();
			$Equipement->setName($Fonction[count($Fonction)-1]);
			$Equipement->setLogicalId($Adresse);
			$Equipement->setObject_id(null);
			$Equipement->setEqType_name('mochad');
			$Equipement->setIsEnable(1);
			$Equipement->setIsVisible(1);
			$Equipement->save();
		}	
		if (is_object($Equipement)) {
			log::add('mochad', 'debug', 'L\'équipement de sécurité à été trouvé');
			$Security=$Equipement->getCmd('info','Etat_'.$Adresse);
			if (!is_object($Security) && config::byKey('autoAddDevice', 'mochad')==1) 
				$Security=self::CreateMochadModuleSecurity($Equipement, $Fonction[count($Fonction)-1], $Adresse);
			if (is_object($Security)) {
				log::add('mochad', 'debug', 'Mise a jours de la valeur de la comande: '.$Valeur);
				$Security->setCollectDate('');
				$Security->event($Valeur);
				$Security->save();
			}
		}
	}
	public static function addCacheMonitor($_monitor) {
		$cache = cache::byKey('mochad::Monitor');
		$value = json_decode($cache->getValue('[]'), true);
		$value[] = array('datetime' => date('d-m-Y H:i:s'), 'monitor' => $_monitor);
		cache::set('mochad::Monitor', json_encode(array_slice($value, -250, 250)), 0);
	}
	public static function newComInfoFromMochad($Equipement,$Commande) {
		$NewCommande = new mochadCmd();
		$NewCommande->setId(null);
		$NewCommande->setName("Etat ".$Commande->getName());
		$NewCommande->setLogicalId("Etat_".$Commande->getLogicalId());
		$NewCommande->setEqLogic_id($Equipement->getId());
		$NewCommande->setIsVisible(0);
		$NewCommande->setType('info');
		$NewCommande->setSubType('numeric');
		$NewCommande->setUnite($Commande->getUnite());
		$NewCommande->setConfiguration('MochadCommandeType',$Commande->getConfiguration('MochadCommandeType'));
		$NewCommande->setConfiguration('MochadCommande','st');
		$NewCommande->save();
		$Commande->setValue($NewCommande->getId());
		$Commande->save();
	}
	public static function CreateMochadModuleStat($Equipement) {
		$NewCommande = new mochadCmd();
		$NewCommande->setId(null);
		$NewCommande->setName("État du module");
		$NewCommande->setEqLogic_id($Equipement->getId());
		$NewCommande->setType('info');
		$NewCommande->setSubType('binary');
		$NewCommande->setLogicalId("Etat_".$Equipement->getId());
		$NewCommande->setConfiguration('MochadCommandeType','aucun');
		$NewCommande->setIsVisible(0);
		$NewCommande->save();
	}
	public static function CreateMochadModuleSecurity($Equipement, $Nom, $Addr) {
		$NewCommande = new mochadCmd();
		$NewCommande->setId(null);
		$NewCommande->setName($Nom);
		$NewCommande->setEqLogic_id($Equipement->getId());
		$NewCommande->setType('info');
		$NewCommande->setSubType('binary');
		$NewCommande->setLogicalId('Etat_'.$Addr);
		$NewCommande->setConfiguration('MochadCommandeType','rfsec');
		$NewCommande->setIsVisible(1);
		$NewCommande->save();
		return $NewCommande;
	}
	public static function deamonRunning() {
		$result = exec("ps aux | grep mochad | grep -v grep | awk '{print $2}'");
		$cron = cron::byClassAndFunction('mochad', 'BusMonitor');
		if(is_object($cron)){
			if($result != ""&& $cron->getState()=="run")
				return $result;
		}
		return false;
	}

	public static function UpdateInfoModule() {
		foreach(eqLogic::byType('mochad') as $Equipement){
			foreach($Equipement->getCmd('info') as $Commande){
				$Commande->event($Commande->execute());
				$Commande->save();
			}
		}
	}
	public static function dependancy_info() {
		$return = array();
		$return['log'] = 'mochad_update';
		$return['state'] = 'nok';
		$return['progress_file'] = '/tmp/compilation_Mochad_in_progress';
		if(file_exists('/etc/mochad/mochad_VERSION')){
			if(exec("cat /etc/mochad/mochad_VERSION")=="v0.1.17")
				$return['state'] = 'ok';
		}
		return $return;
	}
	public static function dependancy_install() {
		if (file_exists('/tmp/compilation_Mochad_in_progress')) {
			return;
		}
		log::remove('mochad_update');
		$cmd = 'sudo /bin/bash ' . dirname(__FILE__) . '/../../ressources/install-mochad.sh';
		$cmd .= ' >> ' . log::getPathToLog('mochad_update') . ' 2>&1 &';
		exec($cmd);
	}
	public static function deamon_info() {
		$return = array();
		$return['log'] = 'mochad';
		if(!self::deamonRunning())
			$return['state'] =  'nok';
		else
			$return['state'] =  'ok';
		$return['launchable'] = 'ok';
		return $return;
	}
	public static function deamon_start($_debug = false) {
		self::deamon_stop();
		log::remove('mochad');
		exec('sudo mochad');
		$deamon_info = self::deamon_info();
		if ($deamon_info['launchable'] != 'ok') 
			throw new Exception(__('Veuillez vérifier la configuration', __FILE__));
		$cron = cron::byClassAndFunction('mochad', 'BusMonitor');
		if (!is_object($cron)) {
			$cron = new cron();
			$cron->setClass('mochad');
			$cron->setFunction('BusMonitor');
			$cron->setEnable(1);
			$cron->setDeamon(1);
			$cron->setSchedule('* * * * *');
			$cron->setTimeout('999999');
			$cron->save();
		}
		$cron->start();
		$cron->run();
	}
	public static function deamon_stop() {
		$cron = cron::byClassAndFunction('mochad', 'BusMonitor');
		if (is_object($cron)) {
			$cron->stop();
			$cron->remove();
		}
		exec('sudo pkill mochad');
	}
}

class mochadCmd extends cmd {
	public function preSave() {
		if ($this->getConfiguration('MochadCommandeType') == '')
			throw new Exception(__('Le type de commande ne peut etre vide', __FILE__));
		if ($this->getConfiguration('MochadCommandeType') != 'rfsec'  && $this->getType() == 'info'){
			$Equipement = mochad::byId($this->getEqLogic_id());
			if (is_object($Equipement)) 
				$this->setLogicalId('Etat_'.$Equipement->getId());
		}
	}
	public function postSave() {
		if ($this->getConfiguration('MochadCommandeType') != 'rfsec'  && config::byKey('autoAddDevice', 'mochad')==1){
			$Equipement = mochad::byId($this->getEqLogic_id());
			if (is_object($Equipement)) {
				$State=$Equipement->getCmd('info','Etat_'.$Equipement->getId());
				if (!is_object($State)) 
					mochad::CreateMochadModuleStat($Equipement);
			}
		}
		if ($this->getConfiguration('MochadCommandeType') == '')
			throw new Exception(__('Le type de commande ne peut etre vide', __FILE__));
	} 
	public function execute($_options = null){     
		$address=config::byKey('mochadHost', 'mochad');
		$port=config::byKey('mochadPort', 'mochad');
		$Equipement = mochad::byId($this->getEqLogic_id(), 'mochad');
		$State=$Equipement->getCmd('info','Etat_'.$Equipement->getId());
		$codeMaison=explode('-',$Equipement->getLogicalId())[0];
		$codeEquipement=explode('-',$Equipement->getLogicalId())[1];
		//exemple de message : pl a all_lights_off
		switch ($this->getType()) {
			case 'action' :
				switch ($this->getSubType()) {
					case 'slider':    
						$ActionValue = $_options['slider'];
					break;
					case 'color':
						$ActionValue = $_options['color'];
					break;
					case 'message':
						$ActionValue = $_options['message'];
					break;
				}
				$message = $this->getConfiguration('MochadCommandeType').' '.$codeMaison;
				switch ($this->getConfiguration('MochadCommande')){
					case 'on-off':	
						$ActionValue=mochadCmd::ReadStatus($address,$port,$codeMaison,$codeEquipement);
						if ($ActionValue==1){
							$ActionValue=0;
							$message.= $codeEquipement.' off';	
						}else{
							$ActionValue=1;
							$message .= $codeEquipement.' on';	
						}
						if (is_object($State)){
							$State->setCollectDate('');
							$State->event($ActionValue);
							$State->save();
						}
					break;
					case 'on':	
						$ActionValue=1;
						$message.= $codeEquipement.' on';
						if (is_object($State)){
							$State->setCollectDate('');
							$State->event($ActionValue);
							$State->save();
						}
					break;
					case 'off':	
						$ActionValue=0;
						$message.= $codeEquipement.' off';	
						if (is_object($State)){
							$State->setCollectDate('');
							$State->event($ActionValue);
							$State->save();
						}
					break;
					case 'dim'://0-31
						$message.= $codeEquipement.' '.$this->getConfiguration('MochadCommande').' '.$ActionValue;	
					break;
					case 'bright'://0-31
						$message.= $codeEquipement.' '.$this->getConfiguration('MochadCommande').' '.$ActionValue;
					break;
					case 'xdim'://0-255	
						$message.= $codeEquipement.' '.$this->getConfiguration('MochadCommande').' '.round($ActionValue*255/100);
					break;
					case 'all_lights_on':	
						$message.= ' '.$this->getConfiguration('MochadCommande');	
					break;
					case 'all_lights_off':
						$message.= ' '.$this->getConfiguration('MochadCommande');		
					break;
					case 'all_units_off':
						$message.= ' '.$this->getConfiguration('MochadCommande');		
					break;
				}
				# On ouvre le FLUX sur le CM15
				$socket = stream_socket_client("tcp://$address:$port", $errno, $errstr, 100);

				if (!$socket) {
					throw new Exception(__("$errstr ($errno)", __FILE__));
				} else {
					# ACTION X10 à réaliser
					log::add('mochad', 'debug', 'Envoie : '.$message);
					fwrite($socket, $message."\n");
					$reponse='';
				}
				fclose($socket);
			break;
			case 'info':
				if ($this->getConfiguration('MochadCommandeType') == 'rfsec')
					$reponse=$this->execCmd();
				else
					$reponse=mochadCmd::ReadStatus($address,$port,$codeMaison,$codeEquipement);
				break;
		}
		return $reponse;
	}
	public function ReadStatus($address,$port,$codeMaison,$codeEquipement)	{    
		$socket = stream_socket_client("tcp://$address:$port", $errno, $errstr, 100);
		fwrite($socket, "ST\n");
		stream_set_timeout($socket, 1);
		$reponse = stream_get_line($socket, 1000000, "End status"); 
		fclose($socket); 
		log::add('mochad', 'debug', 'Recus : '.$reponse);
		$valeur=0;
		$reponse=explode("\n",$reponse);
		foreach ($reponse as $Line){
			if (strrpos($Line,"House ".$codeMaison)>0){
				log::add('mochad', 'debug', 'Table de valeur pour House '.$codeMaison.': '.$Line);
				$Values=substr($Line,strrpos($Line,"House ".$codeMaison)+9);
				log::add('mochad', 'debug', 'Table de valeur pour House '.$codeMaison.': '.$Values);
				$Valuetable=explode(',',$Values);
				foreach ($Valuetable as $ValueDevice){
					$ValueDevice=explode('=',$ValueDevice);
					if ($ValueDevice[0]==$codeEquipement){
						$valeur=$ValueDevice[1];
						log::add('mochad', 'debug', 'Valeur de l\'équipement '.$codeMaison.$codeEquipement.': '.$valeur);
					}
				}
			}
		}
		return $valeur;
	}
}

?>

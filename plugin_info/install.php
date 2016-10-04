<?php
require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';

function mochad_install() {
	foreach(eqLogic::byType('mochad') as $Equipement){
		foreach($Equipement->getCmd() as $Commande){
			$Commande->setEventOnly(1);
			$Commande->save();
		}
	}
}

function mochad_update() {
	foreach(eqLogic::byType('mochad') as $Equipement){
		foreach($Equipement->getCmd() as $Commande){
			$Commande->setEventOnly(1);
			$Commande->save();
		}
	}
  
}

function mochad_remove() {
}

?>
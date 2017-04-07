<?php

namespace BlockHorizons\BlockPets\listeners;

use BlockHorizons\BlockPets\pets\BasePet;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class EventListener implements Listener {

	public function onEntityDamage(EntityDamageEvent $event) {
		$petEntity = $event->getEntity();
		if($petEntity instanceof BasePet) {
			$event->setCancelled();
			if(!$petEntity->isRidden()) {
				$petOwner = $petEntity->getPetOwner();
				if($event instanceof EntityDamageByEntityEvent) {
					$attacker = $event->getDamager();
					if($attacker instanceof Player) {
						if(!$attacker->getName() === $petOwner->getName()) {
							return;
						}
						if(!$attacker->getInventory()->getItemInHand() === 329) {
							$petEntity->setRider($attacker);
						}
						$attacker->sendPopup(TextFormat::GRAY . "Tap the pet again to dismount...");
					}
				}
			} else {
				$petEntity->throwRiderOff();
			}
		}
	}
}
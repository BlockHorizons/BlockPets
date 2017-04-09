<?php

namespace BlockHorizons\BlockPets\listeners;

use BlockHorizons\BlockPets\pets\BasePet;
use pocketmine\event\entity\EntityDamageByBlockEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class EventListener implements Listener {

	public function onEntityDamage(EntityDamageEvent $event) {
		$petEntity = $event->getEntity();
		if($petEntity instanceof BasePet) {
			if(!$petEntity->isRidden()) {
				$petOwner = $petEntity->getPetOwner();
				if($event instanceof EntityDamageByEntityEvent) {
					$attacker = $event->getDamager();
					if($attacker instanceof Player) {
						if($attacker->getId() !== $petOwner->getId()) {
							return;
						}
						if($attacker->getInventory()->getItemInHand()->getId() === 329) {
							$petEntity->setRider($attacker);
							$attacker->sendTip(TextFormat::GRAY . "Crouch or jump to dismount...");
						}
					}
				}
			} else {
				$petEntity->throwRiderOff();
			}
			$event->setCancelled();
		}
	}
}
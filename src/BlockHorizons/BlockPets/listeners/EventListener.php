<?php

namespace BlockHorizons\BlockPets\listeners;

use BlockHorizons\BlockPets\pets\BasePet;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;

class EventListener implements Listener {

	public function onEntityDamage(EntityDamageEvent $event) {
		if($event->getEntity() instanceof BasePet) {
			$event->setCancelled();
		}
	}
}
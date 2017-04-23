<?php

namespace BlockHorizons\BlockPets\listeners;

use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\BasePet;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class EventListener implements Listener {

	private $loader;

	public function __construct(Loader $loader) {
		$this->loader = $loader;
	}

	public function onEntityDamage(EntityDamageEvent $event) {
		$petEntity = $event->getEntity();
		if($petEntity instanceof BasePet) {
			if(!$petEntity->isRidden()) {
				$petOwner = $petEntity->getPetOwner();
				if($event instanceof EntityDamageByEntityEvent) {
					$attacker = $event->getDamager();
					if($attacker instanceof Player) {
						if($attacker->getId() !== $petOwner->getId()) {
							$event->setCancelled();
							return;
						}
						if($attacker->getInventory()->getItemInHand()->getId() === 329) {
							$petEntity->setRider($attacker);
							$attacker->sendTip(TextFormat::GRAY . "Crouch or jump to dismount...");
						}
					}
				}
			}
			$event->setCancelled();
		}
	}

	public function onPlayerQuit(PlayerQuitEvent $event) {
		foreach($event->getPlayer()->getLevel()->getEntities() as $levelEntity) {
			if($levelEntity instanceof BasePet) {
				if($levelEntity->isRidden()) {
					$rider = $levelEntity->getRider();
					if($rider->getName() === $event->getPlayer()->getName()) {
						$levelEntity->throwRiderOff();
					}
				}
			}
		}
	}

	public function onCrouch(PlayerToggleSneakEvent $event) {
		if($event->isSneaking() === false) {
			if($this->getLoader()->isRidingAPet($event->getPlayer())) {
				$this->getLoader()->getRiddenPet($event->getPlayer())->throwRiderOff();
			}
		}
	}

	/**
	 * @return Loader
	 */
	public function getLoader(): Loader {
		return $this->loader;
	}
}
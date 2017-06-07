<?php

namespace BlockHorizons\BlockPets\listeners;

use BlockHorizons\BlockPets\events\PetRespawnEvent;
use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\BasePet;
use BlockHorizons\BlockPets\pets\IrasciblePet;
use BlockHorizons\BlockPets\tasks\PetRespawnTask;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;

class EventListener implements Listener {

	private $loader;

	public function __construct(Loader $loader) {
		$this->loader = $loader;
	}

	/**
	 * @param EntityDamageEvent $event
	 *
	 * @priority MONITOR
	 * @ignoreCancelled true
	 */
	public function onEntityDamage(EntityDamageEvent $event) {
		$petEntity = $event->getEntity();
		if($petEntity instanceof Player) {
			$player = $petEntity;
			if($event->getCause() === $event::CAUSE_FALL) {
				if($this->getLoader()->isRidingAPet($player)) {
					$event->setCancelled();
					return;
				}
			}
			if($event instanceof EntityDamageByEntityEvent) {
				if($event->isCancelled()) {
					return;
				}
				if(!empty($this->getLoader()->getPetsFrom($player))) {
					foreach($this->getLoader()->getPetsFrom($player) as $pet) {
						if(!$pet instanceof IrasciblePet) {
							continue;
						}
						$pet->setAngry($event->getDamager());
					}
				}
			}
		}
	}

	/**
	 * @return Loader
	 */
	public function getLoader(): Loader {
		return $this->loader;
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

	public function onPetDeath(EntityDeathEvent $event) {
		$pet = $event->getEntity();
		$delay = $this->getLoader()->getBlockPetsConfig()->getRespawnTime() * 20;
		if($pet instanceof BasePet) {
			$newPet = $this->getLoader()->createPet($pet->getEntityType(), $pet->getPetOwner(), $pet->getPetName(), $pet->getStartingScale(), $pet->namedtag["isBaby"], $pet->getPetLevel(), $pet->getPetLevelPoints());
			$this->getLoader()->getServer()->getPluginManager()->callEvent($ev = new PetRespawnEvent($this->getLoader(), $newPet, $delay));
			if($ev->isCancelled()) {
				return;
			}
			$delay = $ev->getDelay();

			$this->getLoader()->getServer()->getScheduler()->scheduleDelayedTask(new PetRespawnTask($this->getLoader(), $newPet), $delay * 20);
			$newPet->despawnFromAll();
		}
	}
}
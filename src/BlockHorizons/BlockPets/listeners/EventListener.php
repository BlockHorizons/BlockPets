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
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\Player;

class EventListener implements Listener {

	private $loader;

	public function __construct(Loader $loader) {
		$this->loader = $loader;
	}

	/**
	 * Used to ignore fall damage when ridden and anger pets when their owner has been attacked.
	 *
	 * @param EntityDamageEvent $event
	 *
	 * @priority        MONITOR
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

	/**
	 * Used to respawn a pet after being killed.
	 *
	 * @param EntityDeathEvent $event
	 */
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

			$this->getLoader()->getServer()->getScheduler()->scheduleDelayedTask(new PetRespawnTask($this->getLoader(), $newPet), $delay);
			$newPet->despawnFromAll();
			$newPet->setDormant();
		}
	}

	/**
	 * Used to respawn pets to the player, and fetch pets from the database if this has been configured.
	 *
	 * @param PlayerLoginEvent $event
	 */
	public function onPlayerLogin(PlayerLoginEvent $event) {
		$pets = $this->getLoader()->getPetsFrom($event->getPlayer());
		if($this->getLoader()->getBlockPetsConfig()->fetchFromDatabase()) {
			$petData = $this->getLoader()->getDatabase()->fetchAllPetData($event->getPlayer()->getName());
			foreach($petData as $data) {
				$this->getLoader()->createPet($data["EntityName"], $event->getPlayer(), $data["PetName"], $data["PetSize"], $data["IsBaby"], $data["PetLevel"], $data["LevelPoints"]);
			}
		}
		if(empty($pets)) {
			return;
		}
		foreach($pets as $pet) {
			$pet->spawnToAll();
			$pet->setDormant(false);
		}
	}
}
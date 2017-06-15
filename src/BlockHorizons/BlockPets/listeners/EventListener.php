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
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

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
	 * @priority HIGHEST
	 *
	 * Priority should be highest at all times to take care of plugins that use the entity before it gets closed.
	 */
	public function onPetDeath(EntityDeathEvent $event) {
		$pet = $event->getEntity();
		$delay = $this->getLoader()->getBlockPetsConfig()->getRespawnTime() * 20;
		if($pet instanceof BasePet) {
			$owner = $this->getLoader()->getServer()->getPlayer($pet->getPetOwnerName());
			$this->getLoader()->removePet($pet->getPetName(), $owner, false);

			$newPet = $this->getLoader()->createPet($pet->getEntityType(), $owner, $pet->getPetName(), $pet->getStartingScale(), 0, $pet->getPetLevel(), $pet->getPetLevelPoints());
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
	 * @param PlayerJoinEvent $event
	 */
	public function onPlayerJoin(PlayerJoinEvent $event) {
		$pets = $this->getLoader()->getPetsFrom($event->getPlayer());
		if($this->getLoader()->getBlockPetsConfig()->fetchFromDatabase()) {
			$petData = $this->getLoader()->getDatabase()->fetchAllPetData($event->getPlayer()->getName());
			foreach($petData as $data) {
				$pets[] = $this->getLoader()->createPet($data["EntityName"], $event->getPlayer(), $data["PetName"], $data["PetSize"], $data["IsBaby"], $data["PetLevel"], $data["LevelPoints"]);
			}
		}
		foreach($pets as $pet) {
			$pet->spawnToAll();
			$pet->setDormant(false);
			if($this->getLoader()->getBlockPetsConfig()->doHardReset()) {
				$pet->close();
			}
		}
	}

	/**
	 * Used to select a name through chat. Allows for names with spaces and players to choose themselves.
	 *
	 * @param PlayerChatEvent $event
	 */
	public function onChat(PlayerChatEvent $event) {
		if(isset($this->getLoader()->selectingName[$event->getPlayer()->getName()])) {
			$petName = $event->getMessage();
			$event->setCancelled();
			if($this->getLoader()->getPetByName($petName, $event->getPlayer()) !== null) {
				$event->getPlayer()->sendMessage(TextFormat::RED . "[Warning] You already own a pet with that name. Please choose a different name.");
				return;
			}
			$data = $this->getLoader()->selectingName[$event->getPlayer()->getName()];

			$this->getLoader()->createPet($data["petType"], $event->getPlayer(), $petName, $data["scale"], $data["isBaby"]);
			$event->getPlayer()->sendMessage(TextFormat::GREEN . "Successfully obtained a " . $data["petType"] . " with the name " . $event->getMessage());
			unset($this->getLoader()->selectingName[$event->getPlayer()->getName()]);
		}
	}
}
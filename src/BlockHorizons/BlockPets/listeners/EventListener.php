<?php

namespace BlockHorizons\BlockPets\listeners;

use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\BasePet;
use BlockHorizons\BlockPets\pets\IrasciblePet;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class EventListener implements Listener {

	private $loader;

	public function __construct(Loader $loader) {
		$this->loader = $loader;
	}

	public function onEntityDamage(EntityDamageEvent $event) {
		$petEntity = $event->getEntity();
		if($petEntity instanceof IrasciblePet) {
			$petOwner = $petEntity->getPetOwner();
			if($this->getLoader()->getBlockPetsConfig()->arePetsInvulnerable()) {
				$event->setCancelled();
			}
			if($petEntity->isRidden()) {
				$event->setCancelled();
			}
			if(!$event instanceof EntityDamageByEntityEvent) {
				return;
			}

			$attacker = $event->getDamager();
			if($attacker instanceof Player) {
				if($attacker->getId() === $petOwner->getId()) {
					if($attacker->getInventory()->getItemInHand()->getId() === Item::SADDLE) {
						$petEntity->setRider($attacker);
						$attacker->sendTip(TextFormat::GRAY . "Crouch or jump to dismount...");
						$event->setCancelled();
						return;
					}
				}
			}

			if(!$this->getLoader()->getBlockPetsConfig()->arePetsInvulnerable()) {
				if($attacker->getId() === $petOwner->getId()) {
					$event->setCancelled();
					return;
				}
				if($this->getLoader()->getBlockPetsConfig()->petsDoAttack()) {
					$petEntity->setAngry($attacker);
				}
			}
		} elseif($petEntity instanceof Player) {
			$player = $petEntity;
			if($event->getCause() === $event::CAUSE_FALL) {
				if($this->getLoader()->isRidingAPet($player)) {
					$event->setCancelled();
					return;
				}
			}
			if($event instanceof EntityDamageByEntityEvent) {
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
}
<?php

namespace BlockHorizons\BlockPets\pets;

use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

abstract class IrasciblePet extends Calculator {

	private $target = null;

	public function __construct(Level $level, CompoundTag $nbt) {
		parent::__construct($level, $nbt);
	}

	public function calmDown() {
		$this->target = null;
	}

	/**
	 * @param int  $amount
	 * @param bool $silent
	 *
	 * @return bool
	 */
	public function levelUp(int $amount = 1, bool $silent = false): bool {
		if(parent::levelUp($amount, $silent)) {
			$this->recalculateAll();
			if($this->getLoader()->getBlockPetsConfig()->storeToDatabase()) {
				if($this->getLoader()->getDatabase()->petExists($this->getName(), $this->getPetOwnerName())) {
					$this->getLoader()->getDatabase()->updatePetExperience($this->getName(), $this->getPetOwnerName(), $this->getPetLevel(), $this->getPetLevelPoints());
				} else {
					$this->getLoader()->getDatabase()->registerPet($this);
				}
			}
			return true;
		}
		return false;
	}

	/**
	 * @return Living
	 */
	public function getTarget(): Living {
		return $this->target;
	}

	/**
	 * @return bool
	 */
	public function isAngry(): bool {
		return $this->target !== null;
	}

	/**
	 * @param float             $damage
	 * @param EntityDamageEvent $source
	 */
	public function attack($damage, EntityDamageEvent $source) {
		if($this->getLoader()->getBlockPetsConfig()->arePetsInvulnerable()) {
			$source->setCancelled();
		}
		if($this->isRidden()) {
			$source->setCancelled();
		}
		if($source instanceof EntityDamageByEntityEvent) {
			$attacker = $source->getDamager();
			if(!$this->getLoader()->getBlockPetsConfig()->arePetsInvulnerable()) {
				if($attacker instanceof Player) {
					$nameTag = $attacker->getName();
				} else {
					$nameTag = $attacker->getNameTag();
				}
				if($nameTag === $this->getPetOwnerName()) {
					$source->setCancelled();
				}
				if($this->getLoader()->getBlockPetsConfig()->petsDoAttack() && !$source->isCancelled()) {
					$this->setAngry($attacker);
				}
			}
			if($attacker instanceof Player) {
				if($attacker->getInventory()->getItemInHand()->getId() === Item::SADDLE) {
					$this->setRider($attacker);
					$attacker->sendTip(TextFormat::GRAY . "Crouch or jump to dismount...");
					$source->setCancelled();
				}
			}
		}
		parent::attack($damage, $source);
	}

	/**
	 * @param Living $entity
	 */
	public function setAngry(Living $entity) {
		$this->target = $entity;
	}

	public abstract function doAttackingMovement();
}
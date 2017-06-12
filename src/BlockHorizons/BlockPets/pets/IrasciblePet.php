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

abstract class IrasciblePet extends BasePet {

	private $target = null;

	public function __construct(Level $level, CompoundTag $nbt) {
		parent::__construct($level, $nbt);
	}

	/**
	 * Calms down the pet, making it stop chasing it's target.
	 */
	public function calmDown() {
		$this->target = null;
	}

	/**
	 * Returns the current target of this pet.
	 *
	 * @return Living|null
	 */
	public function getTarget() {
		return $this->target;
	}

	/**
	 * Returns whether this pet is angry or not.
	 *
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
		if($this->closed || !$this->isAlive()) {
			return;
		}
		if($this->getLoader()->getBlockPetsConfig()->arePetsInvulnerable()) {
			$source->setCancelled();
		}
		if($this->isRidden() && $source->getCause() === $source::CAUSE_FALL) {
			$source->setCancelled();
		}
		if($this->getLoader()->getBlockPetsConfig()->arePetsInvulnerableIfOwnerIs() && $this->getPetOwner() !== null) {
			$this->getLoader()->getServer()->getPluginManager()->callEvent($ownerDamageEvent = new EntityDamageEvent($this->getPetOwner(), EntityDamageEvent::CAUSE_CUSTOM, 0));
			if($ownerDamageEvent->isCancelled()) {
				$source->setCancelled();
			}
			$ownerDamageEvent->setCancelled();
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
	 * Sets the pet angry and it's target to the given entity, making it chase the entity.
	 *
	 * @param Living $entity
	 */
	public function setAngry(Living $entity) {
		$this->target = $entity;
	}

	public abstract function doAttackingMovement();
}
<?php

namespace BlockHorizons\BlockPets\pets;

use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;

abstract class IrasciblePet extends Calculator {

	private $target = null;
	private $isAttacking = false;

	public function __construct(Level $level, CompoundTag $nbt) {
		parent::__construct($level, $nbt);
	}

	/**
	 * @param Entity $player
	 */
	public function setAngry(Entity $player) {
		$this->target = $player;
	}

	public function calmDown() {
		$this->target = null;
	}

	/**
	 * @param int $amount
	 */
	public function levelUp(int $amount = 1) {
		parent::levelUp($amount);
		$this->recalculateAll();
	}

	/**
	 * @return bool
	 */
	public function isAttacking(): bool {
		return $this->isAttacking;
	}

	/**
	 * @return Player
	 */
	public function getTarget(): Player {
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
	 *
	 * @return bool
	 */
	public function attack($damage, EntityDamageEvent $source): bool {
		if($this->getLoader()->getBlockPetsConfig()->arePetsInvulnerable()) {
			return false;
		}
		if($this->isRidden()) {
			return false;
		}
		if($source instanceof EntityDamageByEntityEvent) {
			$attacker = $source->getDamager();
			if(!$this->getLoader()->getBlockPetsConfig()->arePetsInvulnerable()) {
				if($attacker->getId() === $this->getPetOwner()->getId()) {
					return false;
				}
				if($this->getLoader()->getBlockPetsConfig()->petsDoAttack()) {
					$this->setAngry($attacker);
					parent::attack($damage, $source);
					return true;
				}
			}
		}
		return false;
	}

	public abstract function doAttackingMovement();
}
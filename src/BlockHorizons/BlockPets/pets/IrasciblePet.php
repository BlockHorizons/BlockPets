<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets;

use BlockHorizons\BlockPets\pets\creatures\ArrowPet;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

abstract class IrasciblePet extends BasePet {

	/** @var int */
	protected $waitingTime = 0;
	/** @var Living|null */
	private $target = null;

	public function __construct(Level $level, CompoundTag $nbt) {
		parent::__construct($level, $nbt);
	}

	/**
	 * @param EntityDamageEvent $source
	 */
	public function attack(EntityDamageEvent $source): void {
		if($this->closed || !$this->isAlive()) {
			return;
		}
		$bpConfig = $this->getLoader()->getBlockPetsConfig();
		if($bpConfig->arePetsInvulnerable()) {
			$source->setCancelled();
		}
		if($this->isRidden() && $source->getCause() === $source::CAUSE_FALL) {
			$source->setCancelled();
		}
		if($bpConfig->arePetsInvulnerableIfOwnerIs() && ($petOwner = $this->getPetOwner()) !== null) {
			$this->server->getPluginManager()->callEvent($ownerDamageEvent = new EntityDamageEvent($petOwner, EntityDamageEvent::CAUSE_CUSTOM, 0));
			if($ownerDamageEvent->isCancelled()) {
				$source->setCancelled();
			}
			$ownerDamageEvent->setCancelled();
		}
		if($source instanceof EntityDamageByEntityEvent) {
			$attacker = $source->getDamager();
			if(!$bpConfig->arePetsInvulnerable()) {
				if($attacker instanceof Player) {
					$nameTag = $attacker->getName();
				} else {
					$nameTag = $attacker->getNameTag();
				}
				if($nameTag === $this->getPetOwnerName()) {
					$source->setCancelled();
				}
				if($bpConfig->petsDoAttack() && $attacker instanceof Living && !$source->isCancelled()) {
					$this->setAngry($attacker);
				}
			}
			if($attacker instanceof Player && $this->canBeRidden && $attacker->getName() === $this->getPetOwnerName()) {
				if($attacker->getInventory()->getItemInHand()->getId() === Item::SADDLE) {
					$this->setRider($attacker);
					$attacker->sendTip(TextFormat::GRAY . "Crouch or jump to dismount...");
					$source->setCancelled();
				}
			}
		}
		parent::attack($source);
	}

	/**
	 * Sets the pet angry and it's target to the given entity, making it chase the entity.
	 *
	 * @param Living $entity
	 *
	 * @return bool
	 */
	public function setAngry(Living $entity): bool {
		if(!$this->canAttack) {
			return false;
		}
		$this->target = $entity;
		if($this instanceof ArrowPet) {
			$this->setCritical();
		}
		return true;
	}

	/**
	 * @return bool
	 */
	public abstract function doAttackingMovement(): bool;

	/**
	 * @return bool
	 */
	protected function checkAttackRequirements(): bool {
		if($this->closed || !($this->isAlive()) || !($this->isAngry())) {
			$this->calmDown();
			return false;
		}
		if(!$this->getTarget()->isAlive()) {
			$this->calmDown();
			return false;
		}
		return true;
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
	 * Calms down the pet, making it stop chasing it's target.
	 */
	public function calmDown(): void {
		$this->target = null;
		if($this instanceof ArrowPet) {
			$this->setCritical(false);
		}
	}

	/**
	 * Returns the current target of this pet.
	 *
	 * @return Living|null
	 */
	public function getTarget(): ?Living {
		return $this->target;
	}
}

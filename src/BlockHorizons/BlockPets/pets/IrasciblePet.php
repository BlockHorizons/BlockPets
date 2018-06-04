<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets;

use BlockHorizons\BlockPets\pets\creatures\ArrowPet;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

abstract class IrasciblePet extends BasePet {

	/** @var float */
	protected $follow_range_sq = 1.2;
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
				$item = $attacker->getInventory()->getItemInHand();
				if(($this->isSaddled() && $item->isNull()) || $item->getId() === Item::SADDLE) {
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

	public function doAttackingMovement(): void {
		if(!$this->checkAttackRequirements()) {
			return;
		}

		$target = $this->getTarget();
		$this->follow($target);

		if($this->distance($target) <= $this->scale + 0.5 && $this->waitingTime <= 0) {
			$event = new EntityDamageByEntityEvent($this, $target, EntityDamageEvent::CAUSE_ENTITY_ATTACK, $this->getAttackDamage());
			$target->attack($event);

			if(!$event->isCancelled() && !$target->isAlive()) {
				if($target instanceof Player) {
					$this->addPetLevelPoints($this->getLoader()->getBlockPetsConfig()->getPlayerExperiencePoints());
				} else {
					$this->addPetLevelPoints($this->getLoader()->getBlockPetsConfig()->getEntityExperiencePoints());
				}
				$this->calmDown();
			}

			$this->waitingTime = 12;
		} elseif($this->distance($this->getPetOwner()) > 25 || $this->distance($target) > 15) {
			$this->calmDown();
		}

		--$this->waitingTime;
	}

	public function follow(Entity $target, float $xOffset = 0.0, float $yOffset = 0.0, float $zOffset = 0.0): void {
		$x = $target->x + $xOffset - $this->x;
		$y = $target->y + $yOffset - $this->y;
		$z = $target->z + $zOffset - $this->z;

		$xz_sq = $x * $x + $z * $z;
		$xz_modulus = sqrt($xz_sq);

		if($xz_sq < $this->follow_range_sq) {
			$this->motion->x = 0;
			$this->motion->z = 0;
		} else {
			$speed_factor = $this->getSpeed() * 0.15;
			$this->motion->x = $speed_factor * ($x / $xz_modulus);
			$this->motion->z = $speed_factor * ($z / $xz_modulus);
		}
		$this->yaw = rad2deg(atan2(-$x, $z));
		$this->pitch = rad2deg(-atan2($y, $xz_modulus));

		$this->move($this->motion->x, $this->motion->y, $this->motion->z);
	}

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

<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets;

use BlockHorizons\BlockPets\items\Saddle;
use BlockHorizons\BlockPets\pets\creatures\ArrowPet;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use function atan2;
use function rad2deg;
use function sqrt;

abstract class IrasciblePet extends BasePet {

	protected float $followRangeSq = 1.2;
	protected int $waitingTime = 0;
	private ?Living $target = null;

	public function attack(EntityDamageEvent $source): void {
		if($this->closed || !$this->isAlive()) {
			return;
		}
		$bpConfig = $this->getLoader()->getBlockPetsConfig();
		if($bpConfig->arePetsInvulnerable()) {
			$source->cancel();
		}
		if($this->isRidden() && $source->getCause() === $source::CAUSE_FALL) {
			$source->cancel();
		}
		if($bpConfig->arePetsInvulnerableIfOwnerIs() && ($petOwner = $this->getPetOwner()) !== null) {
			$ownerDamageEvent = new EntityDamageEvent($petOwner, EntityDamageEvent::CAUSE_CUSTOM, 0);
			$ownerDamageEvent->call();
			if($ownerDamageEvent->isCancelled()) {
				$source->cancel();
			}
			$ownerDamageEvent->cancel();
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
					$source->cancel();
				}
				if($bpConfig->petsDoAttack() && $attacker instanceof Living && !$source->isCancelled()) {
					$this->setAngry($attacker);
				}
			}
			if($attacker instanceof Player && !$attacker->isSneaking() && $this->canBeRidden && $attacker->getName() === $this->getPetOwnerName()) {
				$item = $attacker->getInventory()->getItemInHand();
				if($item->getTypeId() === Saddle::SADDLE()->getTypeId()) {
					$this->setRider($attacker);
					$attacker->sendTip(TextFormat::GRAY . "Crouch or jump to dismount...");
					$source->cancel();
				}
			}
		}
		parent::attack($source);
	}

	/**
	 * Sets the pet angry and it's target to the given entity, making it chase the entity.
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

		if($this->location->distance($target->location) <= $this->scale + 0.5 && $this->waitingTime <= 0) {
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
		} elseif($this->location->distance($this->getPetOwner()->location) > 25 || $this->location->distance($target->location) > 15) {
			$this->calmDown();
		}

		--$this->waitingTime;
	}

	public function follow(Entity $target, float $xOffset = 0.0, float $yOffset = 0.0, float $zOffset = 0.0): void {
		$targetLoc = $target->getLocation();
		$currLoc = $this->getLocation();

		$x = $targetLoc->getX() + $xOffset - $currLoc->getX();
		$y = $targetLoc->getY() + $yOffset - $currLoc->getY();
		$z = $targetLoc->getZ() + $zOffset - $currLoc->getZ();

		$xz_sq = $x * $x + $z * $z;
		$xz_modulus = sqrt($xz_sq);

		if($xz_sq < $this->followRangeSq) {
			$this->motion->x = 0;
			$this->motion->z = 0;
		} else {
			$speed_factor = $this->getSpeed() * 0.15;
			$this->motion->x = $speed_factor * ($x / $xz_modulus);
			$this->motion->z = $speed_factor * ($z / $xz_modulus);
		}
		$this->location->yaw = rad2deg(atan2(-$x, $z));
		$this->location->pitch = rad2deg(-atan2($y, $xz_modulus));

		$this->move($this->motion->x, $this->motion->y, $this->motion->z);
	}

	protected function checkAttackRequirements(): bool {
		if($this->closed || !($this->isAlive()) || !($this->isAngry()) || $this->isFlaggedForDespawn()) {
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
	 */
	public function getTarget(): ?Living {
		return $this->target;
	}
}

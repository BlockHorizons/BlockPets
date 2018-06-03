<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\Player;

abstract class BouncingPet extends IrasciblePet {

	/** @var int */
	protected $jumpTicks = 0;
	/** @var float */
	protected $jumpHeight = 0.08;

	public function onUpdate(int $currentTick): bool {
		if(!$this->checkUpdateRequirements()) {
			return false;
		}

		$petOwner = $this->getPetOwner();
		if($this->isRiding()) {
			$this->yaw = $petOwner->yaw;
			$this->pitch = $petOwner->pitch;
			$this->updateMovement();
			return parent::onUpdate($currentTick);
		}

		if(!parent::onUpdate($currentTick)) {
			return false;
		}
		if(!$this->isAlive()) {
			return false;
		}
		if($this->isAngry()) {
			return $this->doAttackingMovement();
		}
		if($this->jumpTicks > 0) {
			$this->jumpTicks--;
		}

		if(!$this->isOnGround()) {
			if($this->motion->y > -$this->gravity * 2) {
				$this->motion->y = -$this->gravity * 2;
			} else {
				$this->motion->y -= $this->gravity;
			}
		} else {
			$this->motion->y -= $this->gravity;
		}

		$this->move($this->motion->x, $this->motion->y, $this->motion->z);

		$x = $petOwner->x + $this->xOffset - $this->x;
		$y = $petOwner->y - $this->y;
		$z = $petOwner->z + $this->zOffset - $this->z;

		if($x * $x + $z * $z < 9 + $this->getScale()) {
			$this->motion->x = 0;
			$this->motion->z = 0;
		} else {
			$this->motion->x = $this->getSpeed() * 0.15 * ($x / (abs($x) + abs($z)));
			$this->motion->z = $this->getSpeed() * 0.15 * ($z / (abs($x) + abs($z)));
			if($this->isOnGround() && $this->jumpTicks <= 0) {
				$this->jump();
			}
		}
		$this->yaw = rad2deg(atan2(-$x, $z));
		$this->pitch = rad2deg(-atan2($y, sqrt($x * $x + $z * $z)));

		$this->move($this->motion->x, $this->motion->y, $this->motion->z);
		$this->updateMovement();
		return $this->isAlive();
	}

	public function doAttackingMovement(): bool {
		$target = $this->getTarget();

		if(!$this->checkAttackRequirements()) {
			return false;
		}

		if($this->jumpTicks > 0) {
			$this->jumpTicks--;
		}

		if(!$this->isOnGround()) {
			if($this->motion->y > -$this->gravity * 2) {
				$this->motion->y = -$this->gravity * 2;
			} else {
				$this->motion->y -= $this->gravity;
			}
		} else {
			$this->motion->y -= $this->gravity;
		}
		if($this->isOnGround() && $this->jumpTicks <= 0) {
			$this->jump();
		}
		$this->move($this->motion->x, $this->motion->y, $this->motion->z);

		$x = $target->x - $this->x;
		$y = $target->y - $this->y;
		$z = $target->z - $this->z;

		if($x * $x + $z * $z < 1.2) {
			$this->motion->x = 0;
			$this->motion->z = 0;
		} else {
			$this->motion->x = $this->getSpeed() * 0.15 * ($x / (abs($x) + abs($z)));
			$this->motion->z = $this->getSpeed() * 0.15 * ($z / (abs($x) + abs($z)));

		}
		$this->yaw = rad2deg(atan2(-$x, $z));
		$this->pitch = rad2deg(-atan2($y, sqrt($x * $x + $z * $z)));

		$this->move($this->motion->x, $this->motion->y, $this->motion->z);
		if($this->distance($target) <= $this->scale + 1 && $this->waitingTime <= 0) {
			$event = new EntityDamageByEntityEvent($this, $target, EntityDamageEvent::CAUSE_ENTITY_ATTACK, $this->getAttackDamage());
			if($target->getHealth() - $event->getFinalDamage() <= 0) {
				if($target instanceof Player) {
					$this->addPetLevelPoints($this->getLoader()->getBlockPetsConfig()->getPlayerExperiencePoints());
				} else {
					$this->addPetLevelPoints($this->getLoader()->getBlockPetsConfig()->getEntityExperiencePoints());
				}
				$this->calmDown();
			}
			$target->attack($event);

			$this->waitingTime = 12;
		} elseif($this->distance($this->getPetOwner()) > 25 || $this->distance($this->getTarget()) > 15) {
			$this->calmDown();
		}
		$this->updateMovement();
		$this->waitingTime--;
		return $this->isAlive();
	}

	public function jump(): void {
		$this->motion->y = $this->jumpHeight * 12 * $this->getScale();
		$this->move($this->motion->x, $this->motion->y, $this->motion->z);
		$this->jumpTicks = 10;
	}

	public function doRidingMovement(float $motionX, float $motionZ): bool {
		$rider = $this->getPetOwner();

		$this->pitch = $rider->pitch;
		$this->yaw = $rider->yaw;

		$direction_vec = $this->getDirectionVector();
		$x = $direction_vec->x / 2 * $this->getSpeed();
		$z = $direction_vec->z / 2 * $this->getSpeed();

		if($this->jumpTicks > 0) {
			$this->jumpTicks--;
		}

		if(!$this->isOnGround()) {
			if($this->motion->y > -$this->gravity * 2) {
				$this->motion->y = -$this->gravity * 2;
			} else {
				$this->motion->y -= $this->gravity;
			}
		} else {
			$this->motion->y -= $this->gravity;
		}

		$finalMotionX = 0;
		$finalMotionZ = 0;
		switch($motionZ) {
			case 1:
				$finalMotionX = $x;
				$finalMotionZ = $z;
				if($this->isOnGround()) {
					$this->jump();
				}
				break;
			case 0:
				if($this->isOnGround()) {
					$this->jump();
				}
				break;
			case -1:
				$finalMotionX = -$x;
				$finalMotionZ = -$z;
				if($this->isOnGround()) {
					$this->jump();
				}
				break;
			default:
				$average = $x + $z / 2;
				$finalMotionX = $average / 1.414 * $motionZ;
				$finalMotionZ = $average / 1.414 * $motionX;
				if($this->isOnGround()) {
					$this->jump();
				}
				break;
		}
		switch($motionX) {
			case 1:
				$finalMotionX = $z;
				$finalMotionZ = -$x;
				if($this->isOnGround()) {
					$this->jump();
				}
				break;
			case 0:
				if($this->isOnGround()) {
					$this->jump();
				}
				break;
			case -1:
				$finalMotionX = -$z;
				$finalMotionZ = $x;
				if($this->isOnGround()) {
					$this->jump();
				}
				break;
		}

		$this->move($finalMotionX, $this->motion->y, $finalMotionZ);
		$this->updateMovement();
		return $this->isAlive();
	}

	/**
	 * @param EntityDamageEvent $source
	 */
	public function attack(EntityDamageEvent $source): void {
		if($source->getCause() === $source::CAUSE_FALL) {
			$source->setCancelled();
		}
		parent::attack($source);
	}

	/**
	 * @param array $properties
	 */
	public function useProperties(array $properties): void {
		parent::useProperties($properties);
		$this->jumpHeight = (float) $properties["Jumping-Height"];
	}
}

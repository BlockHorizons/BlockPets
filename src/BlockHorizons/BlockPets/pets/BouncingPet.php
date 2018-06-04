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

	protected function initEntity(): void {
		parent::initEntity();
		$this->follow_range_sq = 9 + $this->getScale();
		$this->jumpVelocity = $this->jumpHeight * 12 * $this->getScale();
	}

	public function doPetUpdates(int $currentTick): bool {
		if(!parent::doPetUpdates($currentTick)) {
			return false;
		}

		if($this->jumpTicks > 0) {
			--$this->jumpTicks;
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

		if($this->isRidden()) {
			return false;
		}

		if($this->isAngry()) {
			$this->doAttackingMovement();
		} else {
			$this->follow($this->getPetOwner(), $this->xOffset, 0.0, $this->zOffset);
		}

		$this->updateMovement();
		return true;
	}

	public function doAttackingMovement(): void {
		if(!$this->checkAttackRequirements()) {
			return;
		}

		$target = $this->getTarget();
		$this->follow($target);

		if($this->distance($target) <= $this->scale + 1 && $this->waitingTime <= 0) {
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
		} elseif($this->distance($this->getPetOwner()) > 25 || $this->distance($this->getTarget()) > 15) {
			$this->calmDown();
		}

		--$this->waitingTime;
	}

	public function jump(): void {
		parent::jump();
		$this->jumpTicks = 10;
	}

	public function doRidingMovement(float $motionX, float $motionZ): void {
		$rider = $this->getPetOwner();

		$this->pitch = $rider->pitch;
		$this->yaw = $rider->yaw;

		$speed_factor = 2 * $this->getSpeed();
		$direction_plane = $this->getDirectionPlane();
		$x = $direction_plane->x / $speed_factor;
		$z = $direction_plane->y / $speed_factor;

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

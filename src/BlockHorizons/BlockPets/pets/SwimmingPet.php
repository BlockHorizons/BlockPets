<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\Player;

abstract class SwimmingPet extends BouncingPet {

	/** @var float */
	protected $swimmingSpeed = 0.0;

	public function onUpdate(int $currentTick): bool {
		if(!$this->checkUpdateRequirements()) {
			return true;
		}
		$petOwner = $this->getPetOwner();
		if($this->isRiding()) {
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
		if($this->isUnderwater()) {
			$x = $petOwner->x + $this->xOffset - $this->x;
			$y = $petOwner->y + $this->yOffset - $this->y;
			$z = $petOwner->z + $this->zOffset - $this->z;

			if($x * $x + $z * $z < 6 + $this->getScale()) {
				$this->motion->x = 0;
				$this->motion->z = 0;
			} else {
				$this->motion->x = $this->getSwimmingSpeed() * 0.25 * ($x / (abs($x) + abs($z)));
				$this->motion->z = $this->getSwimmingSpeed() * 0.25 * ($z / (abs($x) + abs($z)));
			}
			$this->motion->y = $this->getSwimmingSpeed() * 0.25 * $y;
			$this->yaw = rad2deg(atan2(-$x, $z));
			$this->pitch = rad2deg(-atan2($y, sqrt($x * $x + $z * $z)));

			$this->move($this->motion->x, $this->motion->y, $this->motion->z);

			$this->updateMovement();
			return $this->isAlive();
		}
		return parent::onUpdate($currentTick);
	}

	public function doAttackingMovement(): bool {
		$target = $this->getTarget();

		if(!$this->checkAttackRequirements()) {
			return false;
		}
		if($this->isUnderwater()) {
			$x = $target->x - $this->x;
			$y = $target->y - $this->y;
			$z = $target->z - $this->z;

			if($x * $x + $z * $z < 1.2) {
				$this->motion->x = 0;
				$this->motion->z = 0;
			} else {
				$this->motion->x = $this->getSwimmingSpeed() * 0.15 * ($x / (abs($x) + abs($z)));
				$this->motion->z = $this->getSwimmingSpeed() * 0.15 * ($z / (abs($x) + abs($z)));
			}

			if($y !== 0.0) {
				$this->motion->y = $this->getSwimmingSpeed() * 0.15 * $y;
			}

			$this->yaw = rad2deg(atan2(-$x, $z));
			$this->pitch = rad2deg(-atan2($y, sqrt($x * $x + $z * $z)));
			$this->move($this->motion->x, $this->motion->y, $this->motion->z);

			if($this->distance($target) <= $this->scale + 0.5 && $this->waitingTime <= 0) {
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
			return true;
		}
		return parent::doAttackingMovement();
	}

	/**
	 * @return float
	 */
	public function getSwimmingSpeed(): float {
		return $this->swimmingSpeed;
	}

	public function doRidingMovement(float $motionX, float $motionZ): bool {
		if($this->isUnderwater()) {
			$rider = $this->getPetOwner();

			$this->pitch = $rider->pitch;
			$this->yaw = $rider->yaw;

			$rider_directionvec = $rider->getDirectionVector();
			$x = $rider_directionvec->x / 2 * $this->getSwimmingSpeed();
			$z = $rider_directionvec->z / 2 * $this->getSwimmingSpeed();
			$y = $rider_directionvec->y / 2 * $this->getSwimmingSpeed();

			$finalMotionX = 0;
			$finalMotionZ = 0;
			switch($motionZ) {
				case 1:
					$finalMotionX = $x;
					$finalMotionZ = $z;
					break;
				case 0:
					break;
				case -1:
					$finalMotionX = -$x;
					$finalMotionZ = -$z;
					break;
				default:
					$average = $x + $z / 2;
					$finalMotionX = $average / 1.414 * $motionZ;
					$finalMotionZ = $average / 1.414 * $motionX;
					break;
			}
			switch($motionX) {
				case 1:
					$finalMotionX = $z;
					$finalMotionZ = -$x;
					break;
				case 0:
					break;
				case -1:
					$finalMotionX = -$z;
					$finalMotionZ = $x;
					break;
			}

			if(((float) $y) !== 0.0) {
				$this->motion->y = $this->getSwimmingSpeed() * 0.25 * $y;
			}
			if(abs($y) < 0.1) {
				$this->motion->y = 0;
			}
			$this->move($finalMotionX, $this->motion->y, $finalMotionZ);

			$this->updateMovement();
			return $this->isAlive();
		}
		return parent::doRidingMovement($motionX, $motionZ);
	}

	/**
	 * @param array $properties
	 */
	public function useProperties(array $properties): void {
		parent::useProperties($properties);
		$this->swimmingSpeed = (float) $properties["Swimming-Speed"];
	}
}

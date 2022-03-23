<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets;

use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\player\Player;
use function abs;
use function atan2;
use function rad2deg;
use function sqrt;

abstract class SwimmingPet extends BouncingPet {

	protected float $swimmingSpeed = 0.0;
	protected float $followRangeSq = 1.2;

	public function doPetUpdates(int $currentTick): bool {
		if(!parent::doPetUpdates($currentTick)) {
			return false;
		}

		if(!$this->isAngry() && $this->isUnderwater()) {
			$petOwner = $this->getPetOwner();
			$ownerLoc = $petOwner->getLocation();
			$currLoc = $this->getLocation();

			$x = $ownerLoc->getX() + $this->xOffset - $currLoc->getX();
			$y = $ownerLoc->getY() + $this->yOffset - $currLoc->getY();
			$z = $ownerLoc->getZ() + $this->zOffset - $currLoc->getZ();

			$xz_sq = $x * $x + $z * $z;
			$xz_modulus = sqrt($xz_sq);
			$speed_factor = $this->getSwimmingSpeed() * 0.25;

			if($xz_sq < 6 + $this->getScale()) {
				$this->motion->x = 0;
				$this->motion->z = 0;
			} else {
				$this->motion->x = $speed_factor * ($x / $xz_modulus);
				$this->motion->z = $speed_factor * ($z / $xz_modulus);
			}
			$this->motion->y = $speed_factor * $y;
			$this->location->yaw = rad2deg(atan2(-$x, $z));
			$this->location->pitch = rad2deg(-atan2($y, $xz_modulus));

			$this->move($this->motion->x, $this->motion->y, $this->motion->z);

			$this->updateMovement();
			return true;
		}

		return true;
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
			$speed_factor = $this->getSwimmingSpeed() * 0.15;
			$this->motion->x = $speed_factor * ($x / $xz_modulus);
			$this->motion->z = $speed_factor * ($z / $xz_modulus);
		}

		if($y !== 0.0) {
			$this->motion->y = $this->getSwimmingSpeed() * 0.15 * $y;
		}

		$this->location->yaw = rad2deg(atan2(-$x, $z));
		$this->location->pitch = rad2deg(-atan2($y, $xz_modulus));

		$this->move($this->motion->x, $this->motion->y, $this->motion->z);
	}

	public function doAttackingMovement(): void {
		if(!$this->checkAttackRequirements()) {
			return;
		}

		if($this->isUnderwater()) {
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
			return;
		}

		parent::doAttackingMovement();
	}

	public function getSwimmingSpeed(): float {
		return $this->swimmingSpeed;
	}

	public function doRidingMovement(float $motionX, float $motionZ): void {
		if($this->isUnderwater()) {
			$rider = $this->getRider();

			$this->location->pitch = $rider->location->pitch;
			$this->location->yaw = $rider->location->yaw;

			$speed_factor = 2 * $this->getSwimmingSpeed();
			$rider_directionvec = $rider->getDirectionVector();
			$x = $rider_directionvec->x / $speed_factor;
			$z = $rider_directionvec->z / $speed_factor;
			$y = $rider_directionvec->y / $speed_factor;

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
			return;
		}

		parent::doRidingMovement($motionX, $motionZ);
	}

	public function useProperties(array $properties): void {
		parent::useProperties($properties);
		$this->swimmingSpeed = (float) $properties["Swimming-Speed"];
	}
}

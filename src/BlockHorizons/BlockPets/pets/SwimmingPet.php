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
		if($this->isInsideOfWater()) {
			$x = $petOwner->x + $this->xOffset - $this->x;
			$y = $petOwner->y + $this->yOffset - $this->y;
			$z = $petOwner->z + $this->zOffset - $this->z;

			if($x * $x + $z * $z < 6 + $this->getScale()) {
				$this->motionX = 0;
				$this->motionZ = 0;
			} else {
				$this->motionX = $this->getSwimmingSpeed() * 0.25 * ($x / (abs($x) + abs($z)));
				$this->motionZ = $this->getSwimmingSpeed() * 0.25 * ($z / (abs($x) + abs($z)));
			}
			$this->motionY = $this->getSwimmingSpeed() * 0.25 * $y;
			$this->yaw = rad2deg(atan2(-$x, $z));
			$this->pitch = rad2deg(-atan2($y, sqrt($x * $x + $z * $z)));

			$this->move($this->motionX, $this->motionY, $this->motionZ);

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
		if($this->isInsideOfWater()) {
			$x = $target->x - $this->x;
			$y = $target->y - $this->y;
			$z = $target->z - $this->z;

			if($x * $x + $z * $z < 1.2) {
				$this->motionX = 0;
				$this->motionZ = 0;
			} else {
				$this->motionX = $this->getSwimmingSpeed() * 0.15 * ($x / (abs($x) + abs($z)));
				$this->motionZ = $this->getSwimmingSpeed() * 0.15 * ($z / (abs($x) + abs($z)));
			}

			if($y !== 0.0) {
				$this->motionY = $this->getSwimmingSpeed() * 0.15 * $y;
			}

			$this->yaw = rad2deg(atan2(-$x, $z));
			$this->pitch = rad2deg(-atan2($y, sqrt($x * $x + $z * $z)));
			$this->move($this->motionX, $this->motionY, $this->motionZ);

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
		if($this->isInsideOfWater()) {
			$rider = $this->getPetOwner();

			$this->pitch = $rider->pitch;
			$this->yaw = $rider->yaw;

			$x = $rider->getDirectionVector()->x / 2 * $this->getSwimmingSpeed();
			$z = $rider->getDirectionVector()->z / 2 * $this->getSwimmingSpeed();
			$y = $rider->getDirectionVector()->y / 2 * $this->getSwimmingSpeed();

			$finalMotion = [0, 0];
			switch($motionZ) {
				case 1:
					$finalMotion = [$x, $z];
					break;
				case 0:
					break;
				case -1:
					$finalMotion = [-$x, -$z];
					break;
				default:
					$average = $x + $z / 2;
					$finalMotion = [$average / 1.414 * $motionZ, $average / 1.414 * $motionX];
					break;
			}
			switch($motionX) {
				case 1:
					$finalMotion = [$z, -$x];
					break;
				case 0:
					break;
				case -1:
					$finalMotion = [-$z, $x];
					break;
			}

			if(((float) $y) !== 0.0) {
				$this->motionY = $this->getSwimmingSpeed() * 0.25 * $y;
			}
			if(abs($y) < 0.1) {
				$this->motionY = 0;
			}
			$this->move($finalMotion[0], $this->motionY, $finalMotion[1]);

			$this->updateMovement();
			return $this->isAlive();
		}
		return parent::doRidingMovement($motionX, $motionZ);
	}

	/**
	 * @param array $properties
	 */
	public function useProperties(array $properties) {
		parent::useProperties($properties);
		$this->swimmingSpeed = (float) $properties["Swimming-Speed"];
	}
}

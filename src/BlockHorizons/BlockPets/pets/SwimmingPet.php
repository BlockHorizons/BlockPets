<?php

namespace BlockHorizons\BlockPets\pets;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\Player;

abstract class SwimmingPet extends BouncingPet {

	protected $swimmingSpeed = 0.0;

	public function onUpdate($currentTick) {
		if(!$this->checkUpdateRequirements()) {
			return true;
		}
		$petOwner = $this->getPetOwner();
		parent::onUpdate($currentTick);
		if(!$this->isAlive()) {
			return true;
		}
		if($this->isAngry()) {
			$this->doAttackingMovement();
			return true;
		}
		if($this->isInsideOfWater()) {
			$x = $petOwner->x - $this->x;
			$y = $petOwner->y - $this->y;
			$z = $petOwner->z - $this->z;

			if($x * $x + $z * $z < 6 + $this->getScale()) {
				$this->motionX = 0;
				$this->motionZ = 0;
			} else {
				$this->motionX = $this->getSwimmingSpeed() * 0.25 * ($x / (abs($x) + abs($z)));
				$this->motionZ = $this->getSwimmingSpeed() * 0.25 * ($z / (abs($x) + abs($z)));
				$this->motionY = $this->getSwimmingSpeed() * 0.25 * $y;
			}
			$this->yaw = rad2deg(atan2(-$x, $z));
			$this->pitch = rad2deg(-atan2($y, sqrt($x * $x + $z * $z)));

			$this->move($this->motionX, $this->motionY, $this->motionZ);

			$this->updateMovement();
			return true;
		} else {
			return parent::onUpdate($currentTick);
		}
	}

	/**
	 * @return float
	 */
	public function getSwimmingSpeed(): float {
		return $this->swimmingSpeed;
	}

	public function doRidingMovement($motionX, $motionZ) {
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
				case -1:
					$finalMotion = [-$x, -$z];
					break;
			}
			switch($motionX) {
				case 1:
					$finalMotion = [$z, -$x];
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
			return true;
		} else {
			return parent::doRidingMovement($motionX, $motionZ);
		}
	}

	public function doAttackingMovement() {
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

				$target->attack($event->getFinalDamage(), $event);

				$this->waitingTime = 15;
			} elseif($this->distance($this->getPetOwner()) > 25 || $this->distance($this->getTarget()) > 15) {
				$this->calmDown();
			}

			$this->updateMovement();
			$this->waitingTime--;
			return true;
		} else {
			return parent::doAttackingMovement();
		}
	}
}
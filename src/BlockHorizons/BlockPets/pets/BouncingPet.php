<?php

namespace BlockHorizons\BlockPets\pets;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\Player;

abstract class BouncingPet extends IrasciblePet {

	protected $jumpTicks = 0;
	protected $waitingTime = 15;
	protected $jumpHeight = 0.08;

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
		if($this->jumpTicks > 0) {
			$this->jumpTicks--;
		}

		if(!$this->isOnGround()) {
			if($this->motionY > -$this->gravity * 2) {
				$this->motionY = -$this->gravity * 2;
			} else {
				$this->motionY -= $this->gravity;
			}
		} else {
			$this->motionY -= $this->gravity;
		}

		$this->move($this->motionX, $this->motionY, $this->motionZ);

		$x = $petOwner->x + $this->xOffset - $this->x;
		$y = $petOwner->y - $this->y;
		$z = $petOwner->z + $this->zOffset - $this->z;

		if($x * $x + $z * $z < 9 + $this->getScale()) {
			$this->motionX = 0;
			$this->motionZ = 0;
		} else {
			$this->motionX = $this->getSpeed() * 0.15 * ($x / (abs($x) + abs($z)));
			$this->motionZ = $this->getSpeed() * 0.15 * ($z / (abs($x) + abs($z)));
			if($this->isOnGround() && $this->jumpTicks <= 0) {
				$this->jump();
			}
		}
		$this->yaw = rad2deg(atan2(-$x, $z));
		$this->pitch = rad2deg(-atan2($y, sqrt($x * $x + $z * $z)));

		$this->move($this->motionX, $this->motionY, $this->motionZ);
		$this->updateMovement();
		return true;
	}

	public function doAttackingMovement() {
		$target = $this->getTarget();

		if(!$this->checkAttackRequirements()) {
			return false;
		}

		if($this->jumpTicks > 0) {
			$this->jumpTicks--;
		}

		if(!$this->isOnGround()) {
			if($this->motionY > -$this->gravity * 2) {
				$this->motionY = -$this->gravity * 2;
			} else {
				$this->motionY -= $this->gravity;
			}
		} else {
			$this->motionY -= $this->gravity;
		}
		if($this->isOnGround() && $this->jumpTicks <= 0) {
			$this->jump();
		}
		$this->move($this->motionX, $this->motionY, $this->motionZ);

		$x = $target->x - $this->x;
		$y = $target->y - $this->y;
		$z = $target->z - $this->z;

		if($x * $x + $z * $z < 1.2) {
			$this->motionX = 0;
			$this->motionZ = 0;
		} else {
			$this->motionX = $this->getSpeed() * 0.15 * ($x / (abs($x) + abs($z)));
			$this->motionZ = $this->getSpeed() * 0.15 * ($z / (abs($x) + abs($z)));

		}
		$this->yaw = rad2deg(atan2(-$x, $z));
		$this->pitch = rad2deg(-atan2($y, sqrt($x * $x + $z * $z)));

		$this->move($this->motionX, $this->motionY, $this->motionZ);
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
			$target->attack($event->getFinalDamage(), $event);

			$this->waitingTime = 15;
		} elseif($this->distance($this->getPetOwner()) > 25 || $this->distance($this->getTarget()) > 15) {
			$this->calmDown();
		}
		$this->updateMovement();
		$this->waitingTime--;
		return true;
	}

	public function jump() {
		$this->motionY = $this->jumpHeight * 12 * $this->getScale();
		$this->move($this->motionX, $this->motionY, $this->motionZ);
		$this->jumpTicks = 10;
	}

	public function doRidingMovement($motionX, $motionZ) {
		$rider = $this->getPetOwner();

		$this->pitch = $rider->pitch;
		$this->yaw = $rider->yaw;

		$x = $this->getDirectionVector()->x / 2 * $this->getSpeed();
		$z = $this->getDirectionVector()->z / 2 * $this->getSpeed();

		if($this->jumpTicks > 0) {
			$this->jumpTicks--;
		}

		if(!$this->isOnGround()) {
			if($this->motionY > -$this->gravity * 2) {
				$this->motionY = -$this->gravity * 2;
			} else {
				$this->motionY -= $this->gravity;
			}
		} else {
			$this->motionY -= $this->gravity;
		}

		$finalMotion = [0, 0];
		switch($motionZ) {
			case 1:
				$finalMotion = [$x, $z];
				if($this->isOnGround()) {
					$this->jump();
				}
				break;
			case -1:
				$finalMotion = [-$x, -$z];
				if($this->isOnGround()) {
					$this->jump();
				}
				break;
		}
		switch($motionX) {
			case 1:
				$finalMotion = [$z, -$x];
				if($this->isOnGround()) {
					$this->jump();
				}
				break;
			case -1:
				$finalMotion = [-$z, $x];
				if($this->isOnGround()) {
					$this->jump();
				}
				break;
		}

		$this->move($finalMotion[0], $this->motionY, $finalMotion[1]);
		$this->updateMovement();
	}

	/**
	 * @param float             $damage
	 * @param EntityDamageEvent $source
	 */
	public function attack($damage, EntityDamageEvent $source) {
		if($source->getCause() === $source::CAUSE_FALL) {
			$source->setCancelled();
		}
		return parent::attack($damage, $source);
	}

	public function generateCustomPetData() {
		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_MOVING, true);
	}

	/**
	 * @param int $currentTick
	 *
	 * @return bool
	 */
	public function parentOnUpdate(int $currentTick) {
		return parent::onUpdate($currentTick);
	}

	/**
	 * @param array $properties
	 */
	public function useProperties(array $properties) {
		$this->speed = (float) $properties["Speed"];
		$this->jumpHeight = (float) $properties["Jumping-Height"];
		$this->canBeRidden = (bool) $properties["Can-Be-Ridden"];
	}
}
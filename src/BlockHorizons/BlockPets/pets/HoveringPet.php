<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets;

use BlockHorizons\BlockPets\pets\creatures\EnderDragonPet;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\Player;

abstract class HoveringPet extends IrasciblePet {

	/** @var float */
	public $gravity = 0;
	/** @var int */
	protected $flyHeight = 0;

	public function onUpdate(int $currentTick): bool {
		if(!$this->checkUpdateRequirements()) {
			return true;
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

		$x = $petOwner->x + $this->xOffset - $this->x;
		$y = $petOwner->y + abs($this->yOffset) + 1.5 - $this->y;
		$z = $petOwner->z + $this->zOffset - $this->z;

		if($x * $x + $z * $z < 8 + $this->getScale()) {
			$this->motion->x = 0;
			$this->motion->z = 0;
		} else {
			$this->motion->x = $this->getSpeed() * 0.25 * ($x / (abs($x) + abs($z)));
			$this->motion->z = $this->getSpeed() * 0.25 * ($z / (abs($x) + abs($z)));
		}

		if((float) $y !== 0.0) {
			$this->motion->y = $this->getSpeed() * 0.25 * $y;
		}

		$this->yaw = rad2deg(atan2(-$x, $z));
		if($this->getNetworkId() === self::ENDER_DRAGON) {
			$this->yaw += 180;
		}
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

		$x = $target->x - $this->x;
		$y = $target->y + 0.5 - $this->y;
		$z = $target->z - $this->z;

		if($x * $x + $z * $z < 0.8) {
			$this->motion->x = 0;
			$this->motion->z = 0;
		} else {
			$this->motion->x = $this->getSpeed() * 0.15 * ($x / (abs($x) + abs($z)));
			$this->motion->z = $this->getSpeed() * 0.15 * ($z / (abs($x) + abs($z)));
		}

		if((float) $y !== 0.0) {
			$this->motion->y = $this->getSpeed() * 0.15 * $y;
		}

		$this->yaw = rad2deg(atan2(-$x, $z));
		if($this->getNetworkId() === self::ENDER_DRAGON) {
			$this->yaw += 180;
		}
		$this->pitch = rad2deg(-atan2($y, sqrt($x * $x + $z * $z)));
		$this->move($this->motion->x, $this->motion->y, $this->motion->z);

		if($this->distance($target) <= $this->scale + 1.1 && $this->waitingTime <= 0 && $target->isAlive()) {
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

	public function doRidingMovement(float $motionX, float $motionZ): bool {
		$rider = $this->getPetOwner();

		$this->pitch = $rider->pitch;
		$this->yaw = $this instanceof EnderDragonPet ? $rider->yaw + 180 : $rider->yaw;

		$rider_directionvec = $rider->getDirectionVector();
		$x = $rider_directionvec->x / 2 * $this->getSpeed();
		$z = $rider_directionvec->z / 2 * $this->getSpeed();
		$y = $rider_directionvec->y / 2 * $this->getSpeed();

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
			if($y < 0) {
				$this->motion->y = $this->getSpeed() * 0.3 * $y;
			} elseif($this->y - $this->getLevel()->getHighestBlockAt((int) $this->x, (int) $this->z) < $this->flyHeight) {
				$this->motion->y = $this->getSpeed() * 0.3 * $y;
			}
		}
		if(abs($y) < 0.2) {
			$this->motion->y = 0;
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
		$this->flyHeight = (float) $properties["Flying-Height"];
	}
}

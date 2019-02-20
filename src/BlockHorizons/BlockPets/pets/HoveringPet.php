<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets;

use BlockHorizons\BlockPets\pets\creatures\EnderDragonPet;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\Player;

abstract class HoveringPet extends IrasciblePet {

	/** @var float */
	public $gravity = 0;
	/** @var int */
	protected $flyHeight = 0;

	protected function initEntity(): void {
		parent::initEntity();
		$this->follow_range_sq = 8 + $this->getScale();
	}

	public function doPetUpdates(int $tickDiff): bool {
		if(!parent::doPetUpdates($tickDiff)) {
			return false;
		}

		if($this->isRidden()) {
			return false;
		}

		if($this->isAngry()) {
			$this->doAttackingMovement();
		} else {
			$this->follow($this->getPetOwner(), $this->xOffset, abs($this->yOffset) + 1.5, $this->zOffset);
		}

		$this->updateMovement();
		return true;
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

		if((float) $y !== 0.0) {
			$this->motion->y = $this->getSpeed() * 0.25 * $y;
		}

		$this->yaw = rad2deg(atan2(-$x, $z));
		if($this->getNetworkId() === self::ENDER_DRAGON) {
			$this->yaw += 180;
		}
		$this->pitch = rad2deg(-atan2($y, $xz_modulus));

		$this->move($this->motion->x, $this->motion->y, $this->motion->z);
	}

	public function doAttackingMovement(): void {
		if(!$this->checkAttackRequirements()) {
			return;
		}

		$target = $this->getTarget();
		$this->follow($target, 0.0, 0.5, 0.0);

		if($this->distance($target) <= $this->getScale() + 1.1 && $this->waitingTime <= 0 && $target->isAlive()) {
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

	public function doRidingMovement(float $motionX, float $motionZ): void {
		$rider = $this->getPetOwner();

		$this->pitch = $rider->pitch;
		$this->yaw = $this instanceof EnderDragonPet ? $rider->yaw + 180 : $rider->yaw;

		$speed_factor = 2 * $this->getSpeed();
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

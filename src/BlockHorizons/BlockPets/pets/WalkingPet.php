<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\Player;

abstract class WalkingPet extends IrasciblePet {

	/** @var int */
	protected $jumpTicks = 0;

	protected function initEntity(): void {
		parent::initEntity();
		$this->jumpVelocity = $this->gravity * 10;
	}

	public function doPetUpdates(int $tickDiff): bool {
		if(!parent::doPetUpdates($tickDiff)) {
			return false;
		}

		if($this->jumpTicks > 0) {
			--$this->jumpTicks;
		}

		if(!$this->isOnGround()) {
			if($this->motion->y > -$this->gravity * 4) {
				$this->motion->y = -$this->gravity * 4;
			} else {
				$this->motion->y += $this->isUnderwater() ? $this->gravity : -$this->gravity;
			}
		} else {
			if($this->isCollidedHorizontally && $this->jumpTicks === 0) {
				$this->jump();
			} else {
				$this->motion->y -= $this->gravity;
			}
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

	public function jump(): void {
		parent::jump();
		$this->jumpTicks = 5;
	}

	public function doRidingMovement(float $motionX, float $motionZ): void {
		$rider = $this->getPetOwner();

		$this->pitch = $rider->pitch;
		$this->yaw = $rider->yaw;

		$speed_factor = 2.5 * $this->getSpeed();
		$direction_plane = $this->getDirectionPlane();
		$x = $direction_plane->x / $speed_factor;
		$z = $direction_plane->y / $speed_factor;

		$finalMotionX = 0;
		$finalMotionZ = 0;

		switch($motionZ) {
			case 1:
				$finalMotionX = $x;
				$finalMotionZ = $z;
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
			case -1:
				$finalMotionX = -$z;
				$finalMotionZ = $x;
				break;
		}

		$this->move($finalMotionX, $this->motion->y, $finalMotionZ);
		$this->updateMovement();
	}
}

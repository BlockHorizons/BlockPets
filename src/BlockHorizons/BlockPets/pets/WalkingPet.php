<?php

namespace BlockHorizons\BlockPets\pets;

use pocketmine\block\Slab;
use pocketmine\block\Stair;

abstract class WalkingPet extends BasePet {

	public function onUpdate($currentTick) {
		$petOwner = $this->getPetOwner();
		parent::onUpdate($currentTick);
		if($petOwner === null || $this->isRidden()) {
			return false;
		}
		if(!$this->isOnGround()) {
			if($this->motionY > -$this->gravity * 4) {
				$this->motionY = -$this->gravity * 4;
			} else {
				$this->motionY -= $this->gravity;
			}

		} elseif($this->isCollidedHorizontally) {
			if($this->getLevel()->getBlock($this->add($this->getDirectionVector()->x, 0, $this->getDirectionVector()->z))->isSolid()) {
				$this->motionY = 4;
			} elseif($this->level->getBlock($this->add($this->getDirectionVector()->x, 0, $this->getDirectionVector()->z)) instanceof Slab || $this->level->getBlock($this->add($this->getDirectionVector()->x, 0, $this->getDirectionVector()->z)) instanceof Stair) {
				$this->motionY = 2;
			}
		}

		$x = $petOwner->x - $this->x;
		$z = $petOwner->z - $this->z;

		if($x * $x + $z * $z < 5) {
			$this->motionX = 0;
			$this->motionZ = 0;
		} else {
			$this->motionX = $this->getSpeed() * 0.15 * ($x / (abs($x) + abs($z)));
			$this->motionZ = $this->getSpeed() * 0.15 * ($z / (abs($x) + abs($z)));
		}
		$this->yaw = rad2deg(atan2(-$x, $z));
		$this->pitch = rad2deg(-atan($petOwner->y - $this->y));

		$this->move($this->motionX, $this->motionY, $this->motionZ);
		$this->updateMovement();
		return true;
	}

	public function doRidingMovement() {
		$rider = $this->getPetOwner();
		$this->pitch = $rider->pitch;
		$this->yaw = $rider->yaw;
		if(!$this->isOnGround()) {
			if($this->motionY > -$this->gravity * 4) {
				$this->motionY = -$this->gravity * 4;
			} else {
				$this->motionY -= $this->gravity;
			}

		} elseif($this->isCollidedHorizontally) {
			if($this->getLevel()->getBlock($this->add($this->getDirectionVector()->x - 0.2, 0, $this->getDirectionVector()->z - 0.2))->isSolid()) {
				$this->motionY = 4;
			} elseif($this->level->getBlock($this->add($this->getDirectionVector()->x - 0.2, 0, $this->getDirectionVector()->z - 0.2)) instanceof Slab || $this->level->getBlock($this->add($this->getDirectionVector()->x - 0.2, 0, $this->getDirectionVector()->z - 0.2)) instanceof Stair) {
				$this->motionY = 2;
			}
		}

		$x = $rider->getDirectionVector()->x;
		$z = $rider->getDirectionVector()->z;

		if($x * $x + $z * $z < 5) {
			$this->motionX = 0;
			$this->motionZ = 0;
		} else {
			$this->motionX = $this->getSpeed() * 0.6 * ($x / (abs($x) + abs($z)));
			$this->motionZ = $this->getSpeed() * 0.6 * ($z / (abs($x) + abs($z)));
		}

		$this->move($this->motionX, $this->motionY, $this->motionZ);
		$this->updateMovement();
		return true;
	}
}
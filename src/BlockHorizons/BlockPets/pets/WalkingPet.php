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
			$this->jump();
			$this->motionY -= $this->gravity;
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

	public function doRidingMovement($currentTick) {
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
			$this->jump();
			$this->motionY -= $this->gravity;
		}

		$x = $rider->getDirectionVector()->x;
		$z = $rider->getDirectionVector()->z;

		$this->motionX = $this->getSpeed() * 0.4 * ($x / (abs($x) + abs($z)));
		$this->motionZ = $this->getSpeed() * 0.4 * ($z / (abs($x) + abs($z)));

		$this->move($this->motionX, $this->motionY, $this->motionZ);
		$this->checkBlockCollision();

		$this->updateMovement();
	}

	protected function jump() {
		var_dump($this->getDirectionVector());
		$x = $this->getDirectionVector()->x;
		$z = $this->getDirectionVector()->z;
		$positionsToCheck = [
			$this->add($x, 0, $z),
			$this->add($x * 0.8, 0, $z * 0.8),
			$this->add($x, 0.2, $z),
			$this->add($x, -0.2, $z),
			$this->add($x * 1.2, 0, $z * 1.2),
			$this->add($x + 1, 0, $z),
			$this->add($x - 1, 0, $z),
			$this->add($x, 0, $z + 1),
			$this->add($x, 0, $z - 1)
		];
		foreach($positionsToCheck as $position) {
			$blockAhead = $this->getLevel()->getBlock($position);
			if($blockAhead->isSolid()) {
				$this->motionY = 0.8;
				break;
			} elseif($blockAhead instanceof Slab || $blockAhead instanceof Stair) {
				$this->motionY = 0.4;
				break;
			}
		}
	}
}

<?php

namespace BlockHorizons\BlockPets\pets;

use pocketmine\block\Block;
use pocketmine\block\Slab;
use pocketmine\block\Stair;
use pocketmine\math\Vector3;

abstract class WalkingPet extends BasePet {

	public function onUpdate($currentTick) {
		$petOwner = $this->getPetOwner();
		parent::onUpdate($currentTick);
		if($petOwner === null || $this->isRidden()) {
			return false;
		}

		$this->motionY -= $this->gravity;
		if(!$this->isOnGround()) {
			if($this->motionY > -$this->gravity * 4) {
				$this->motionY = -$this->gravity * 4;
			} else {
				$this->motionY -= $this->gravity;
			}
		} elseif($this->isCollidedHorizontally) {
			$this->jump();
		} else {
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
		} else {
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
		$this->motionY = -1;
		$this->move($this->motionX, $this->motionY, $this->motionZ);
		$oldPitch = $this->pitch;
		$this->pitch = 90;
		if($this->getLevel()->getBlock(new Vector3($this->x, $this->y - 0.6, $this->z))->getId() === Block::AIR) {
			$this->y -= 0.6;
		}
		$posAhead = $this->getTargetBlock(3);
		$blockAhead = $this->getLevel()->getBlock($posAhead);
		if($blockAhead->isSolid()) {
			$this->motionY = $this->gravity * 6;
			$this->move($this->motionX, $this->motionY, $this->motionZ);
		} elseif($blockAhead instanceof Slab || $blockAhead instanceof Stair) {
			$this->motionY = $this->gravity * 3;
			$this->move($this->motionX, $this->motionY, $this->motionZ);
		}
		$this->pitch = $oldPitch;
		$this->y += 0.9;
	}
}

<?php

namespace BlockHorizons\BlockPets\pets;

use pocketmine\block\Slab;
use pocketmine\block\Stair;

abstract class WalkingPet extends BasePet {

	public function onUpdate($currentTick) {
		$petOwner = $this->getPetOwner();
		parent::onUpdate($currentTick);

		if(!$this->isOnGround()) {
			if($this->motionY > -$this->gravity * 4) {
				$this->motionY = -$this->gravity * 4;
			} else {
				$this->motionY -= $this->gravity;
			}

		} elseif($this->isCollidedHorizontally) {
			if($this->getLevel()->getBlock($this->add($this->getDirectionVector()->x, 0, $this->getDirectionVector()->z))->isSolid()) {
				$this->motionY = $this->gravity * 8;
			} elseif($this->level->getBlock($this->add($this->getDirectionVector()->x, 0, $this->getDirectionVector()->z)) instanceof Slab || $this->level->getBlock($this->add($this->getDirectionVector()->x, 0, $this->getDirectionVector()->z)) instanceof Stair) {
				$this->motionY = $this->gravity * 4;
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
		// TODO: Implement doRidingMovement() method.
	}
}
<?php

namespace BlockHorizons\BlockPets\pets;

use pocketmine\block\Block;
use pocketmine\block\Slab;
use pocketmine\block\Stair;
use pocketmine\math\Vector3;

abstract class WalkingPet extends BasePet {

	public function onUpdate($currentTick) {
		$petOwner = $this->getPetOwner();
		if($petOwner === null) {
			$this->despawnFromAll();
			return false;
		}
		if($this->distanceSquared($petOwner) >= 60) {
			$this->teleport($petOwner);
		}
		if(!$this->isOnGround()) {
			if($this->motionY > -$this->gravity * 4) {
				$this->motionY = -$this->gravity * 4;
			} else {
				$this->motionY -= $this->gravity;
			}
			$this->move($this->motionX, $this->motionY, $this->motionZ);
		}

		if($this->isCollidedHorizontally) {
			if($this->distance(new Vector3($this->x, $this->getLevel()->getHighestBlockAt($this->x, $this->z), $this->z)) <= 2) {
				if($this->getLevel()->getBlock(new Vector3($this->x, $this->y - 0.8, $this->z))->getId() !== Block::AIR) {
					if($this->level->getBlock($this->getDirectionVector())->isSolid()) {
						$this->motionY = $this->gravity * 8;
					} elseif($this->level->getBlock($this->getDirectionVector()) instanceof Slab || $this->level->getBlock($this->getDirectionVector()) instanceof Stair) {
						$this->motionY = $this->gravity * 4;
					}
				}
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
		$this->pitch = rad2deg(atan($petOwner->y - $this->y));
		$this->move($this->motionX, $this->motionY, $this->motionZ);

		$this->move($this->motionX, $this->motionY, $this->motionZ);
		$this->updateMovement();

		return true;
	}
}
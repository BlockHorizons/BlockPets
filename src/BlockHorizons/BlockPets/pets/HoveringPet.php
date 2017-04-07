<?php

namespace BlockHorizons\BlockPets\pets;

use pocketmine\block\Block;
use pocketmine\math\Vector3;

abstract class HoveringPet extends BasePet {

	public function onUpdate($currentTick) {
		$petOwner = $this->getPetOwner();
		if($petOwner !== null) {
			$this->spawnToAll();
		}
		if($petOwner === null) {
			$this->despawnFromAll();
			return false;
		}
		if($this->distanceSquared($petOwner) >= 20 || $this->getLevel()->getName() !== $petOwner->getLevel()->getName()) {
			$this->teleport($petOwner);
			$this->spawnToAll();
		}
		if(!$this->isOnGround()) {
			if($this->motionY > -$this->gravity * 4) {
				$this->motionY = -$this->gravity * 4;
			} else {
				$this->motionY -= $this->gravity;
			}
			$this->move($this->motionX, $this->motionY, $this->motionZ);
		}
		$x = $petOwner->x - $this->x;
		$z = $petOwner->z - $this->z;
		if($x * $x + $z * $z < 4) {
			$this->motionX = 0;
			$this->motionZ = 0;
		} else {
			$this->motionX = $this->getSpeed() * 0.15 * ($x / (abs($x) + abs($z)));
			$this->motionZ = $this->getSpeed() * 0.15 * ($z / (abs($x) + abs($z)));
		}
		$this->yaw = rad2deg(atan2(-$x, $z));
		if($this->getNetworkId() === 53) {
			$this->yaw += 180;
		}
		$this->pitch = rad2deg(atan($petOwner->y - $this->y));
		if($this->getLevel()->getBlock(new Vector3($this->x, $this->y - 0.6, $this->z))->getId() !== Block::AIR) {
			$this->motionY = $this->gravity * 1.8;
		}
		$this->checkBlockCollision();
		$this->move($this->motionX, $this->motionY, $this->motionZ);
		$this->updateMovement();

		parent::onUpdate($currentTick);
		return true;
	}
}

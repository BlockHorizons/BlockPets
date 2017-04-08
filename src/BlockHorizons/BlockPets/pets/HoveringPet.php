<?php

namespace BlockHorizons\BlockPets\pets;

use pocketmine\math\Vector3;

abstract class HoveringPet extends BasePet {

	public $gravity = 0;

	public function onUpdate($currentTick) {
		$petOwner = $this->getPetOwner();
		if($petOwner === null) {
			$this->ridden = false;
			$this->rider = null;
			$this->despawnFromAll();
			return false;
		}
		if($this->distance($petOwner) >= 50 || $this->getLevel()->getName() !== $petOwner->getLevel()->getName()) {
			$this->teleport($petOwner);
			$this->spawnToAll();
		}

		if($this->isRidden()) {
			$this->doRidingMovement();
			parent::onUpdate($currentTick);
			return true;
		}

		$x = $petOwner->x - $this->x;
		$y = $petOwner->y + 1 - $this->y;
		$z = $petOwner->z - $this->z;

		if($x * $x + $z * $z < 8) {
			$this->motionX = 0;
			$this->motionZ = 0;
		} else {
			$this->motionX = $this->getSpeed() * 0.15 * ($x / (abs($x) + abs($z)));
			$this->motionZ = $this->getSpeed() * 0.15 * ($z / (abs($x) + abs($z)));
		}

		$this->motionY = 0;
		if($y !== 0 && abs($y) >= 0.4) {
			$this->motionY = $this->getSpeed() * 0.15 * ($y / abs($y));
		}

		$this->yaw = rad2deg(atan2(-$x, $z));
		if($this->getNetworkId() === 53) {
			$this->yaw += 180;
		}
		$this->pitch = rad2deg(atan($petOwner->y - $this->y));
		$this->move($this->motionX, $this->motionY, $this->motionZ);

		$this->updateMovement();
		parent::onUpdate($currentTick);
		return true;
	}

	public function doRidingMovement() {
		$rider = $this->getPetOwner();

		$this->pitch = $rider->pitch;
		$this->yaw = $rider->yaw;
		if($this->getNetworkId() === 53) {
			$this->yaw += 180;
		}

		$x = $rider->getDirectionVector()->x;
		$y = $rider->getDirectionVector()->y;
		$z = $rider->getDirectionVector()->z;

		$this->motionX = $this->getSpeed() * 0.3 * ($x / (abs($x) + abs($z)));
		$this->motionZ = $this->getSpeed() * 0.3 * ($z / (abs($x) + abs($z)));

		$this->motionY = 0;
		if(abs($y) >= 0.3 || $y < 0.1) {
			$this->motionY = $this->getSpeed() * 0.15 * ($y / abs($y));
			$this->motionY -= $this->motionY * $this->distance(new Vector3($this->x, $this->level->getHighestBlockAt($this->x, $this->z), $this->z)) / 2;
		}

		$this->move($this->motionX, $this->motionY, $this->motionZ);
		$this->updateMovement();
	}
}

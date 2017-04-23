<?php

namespace BlockHorizons\BlockPets\pets;

use pocketmine\math\Vector3;

abstract class HoveringPet extends BasePet {

	public $gravity = 0;
	protected $flyHeight = 0;

	public function onUpdate($currentTick) {
		$petOwner = $this->getPetOwner();
		parent::onUpdate($currentTick);
		if($petOwner === null || $this->isRidden()) {
			return false;
		}

		$x = $petOwner->x - $this->x;
		$y = $petOwner->y + 2 - $this->y;
		$z = $petOwner->z - $this->z;

		if($x * $x + $z * $z < 8) {
			$this->motionX = 0;
			$this->motionZ = 0;
		} else {
			$this->motionX = $this->getSpeed() * 0.15 * ($x / (abs($x) + abs($z)));
			$this->motionZ = $this->getSpeed() * 0.15 * ($z / (abs($x) + abs($z)));
		}

		if($y !== 0 && $y >= 0.4) {
			$this->motionY = $this->getSpeed() * 0.15 * ($y / abs($y));
		}

		$this->yaw = rad2deg(atan2(-$x, $z));
		if($this->getNetworkId() === 53) {
			$this->yaw += 180;
		}
		$this->pitch = rad2deg(-atan2($y, sqrt($x * $x + $z * $z)));
		$this->move($this->motionX, $this->motionY, $this->motionZ);

		$this->updateMovement();
		parent::onUpdate($currentTick);
		return true;
	}

	public function doRidingMovement($motionX, $motionZ) {
		$rider = $this->getPetOwner();

		$this->pitch = $rider->pitch;
		$this->yaw = $rider->yaw;
		if($this->getNetworkId() === 53) {
			$this->yaw += 180;
		}
		$y = $rider->getDirectionVector()->y;

		if(($y !== 0.0 && $this->distance(new Vector3($this->x, $this->level->getHighestBlockAt($this->x, $this->z), $this->z)) <= $this->flyHeight) || $y < 0) {
			$this->motionY = $this->getSpeed() * 0.3 * ($y / abs($y * 2));
		}
		$this->move($motionX, $this->motionY, $motionZ);
		$this->checkBlockCollision();

		$this->updateMovement();
	}
}

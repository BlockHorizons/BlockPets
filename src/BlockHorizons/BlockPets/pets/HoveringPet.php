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

		$this->motionY = 0;
		if($y !== 0 && abs($y) >= 0.3) {
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

	public function doRidingMovement($currentTick) {
		$rider = $this->getPetOwner();

		$this->pitch = $rider->pitch;
		$this->yaw = $rider->yaw;
		if($this->getNetworkId() === 53) {
			$this->yaw += 180;
		}
		$x = $rider->getDirectionVector()->x;
		$y = $rider->getDirectionVector()->y;
		$z = $rider->getDirectionVector()->z;

		$this->motionX = $this->getSpeed() * 0.6 * ($x / (abs($x) + abs($z)));
		$this->motionZ = $this->getSpeed() * 0.6 * ($z / (abs($x) + abs($z)));

		if(($y !== 0 && $this->distance(new Vector3($this->x, $this->level->getHighestBlockAt($this->x, $this->z), $this->z)) <= $this->flyHeight) || $y < 0) {
			$this->motionY = $this->getSpeed() * 0.5 * ($y / abs($y));
		}
		$this->move($this->motionX, $this->motionY, $this->motionZ);
		$this->checkBlockCollision();

		$this->updateMovement();
	}
}

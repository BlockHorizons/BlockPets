<?php

namespace BlockHorizons\BlockPets\pets;

abstract class HoveringPet extends BasePet {

	public $gravity = 0;

	public function onUpdate($currentTick) {
		$petOwner = $this->getPetOwner();
		if($petOwner === null) {
			$this->despawnFromAll();
			return false;
		}
		if($this->distance($petOwner) >= 50 || $this->getLevel()->getName() !== $petOwner->getLevel()->getName()) {
			$this->teleport($petOwner);
			$this->spawnToAll();
		}
		foreach($this->getLevel()->getPlayers() as $player) {
			if(!isset($this->hasSpawned[$player->getLoaderId()])) {
				$this->spawnTo($player);
			}
		}
		if($this->getSpeed() === null) {
			$this->speed = 1;
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
		if(round($y, 1) !== 0) {
			$this->motionY = $this->getSpeed() * 0.15 * ($y / (abs($y) + abs($y)));
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
}

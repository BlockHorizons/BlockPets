<?php

namespace BlockHorizons\BlockPets\pets;

use BlockHorizons\BlockPets\pets\creatures\EnderDragonPet;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\Player;

abstract class HoveringPet extends IrasciblePet {

	public $gravity = 0;

	protected $flyHeight = 0;

	private $waitingTime = 15;

	public function onUpdate($currentTick) {
		if(!$this->checkUpdateRequirements()) {
			return true;
		}
		$petOwner = $this->getPetOwner();
		parent::onUpdate($currentTick);
		if(!$this->isAlive()) {
			return true;
		}
		if($this->isAngry()) {
			$this->doAttackingMovement();
			return true;
		}

		$x = $petOwner->x - $this->x;
		$y = $petOwner->y + 2 - $this->y;
		$z = $petOwner->z - $this->z;

		if($x * $x + $z * $z < 8 + $this->getScale()) {
			$this->motionX = 0;
			$this->motionZ = 0;
		} else {
			$this->motionX = $this->getSpeed() * 0.25 * ($x / (abs($x) + abs($z)));
			$this->motionZ = $this->getSpeed() * 0.25 * ($z / (abs($x) + abs($z)));
		}

		if((float) $y !== 0.0) {
			$this->motionY = $this->getSpeed() * 0.25 * $y;
		}

		$this->yaw = rad2deg(atan2(-$x, $z));
		if($this->getNetworkId() === 53) {
			$this->yaw += 180;
		}
		$this->pitch = rad2deg(-atan2($y, sqrt($x * $x + $z * $z)));
		$this->move($this->motionX, $this->motionY, $this->motionZ);

		$this->updateMovement();
		return true;
	}

	public function doAttackingMovement() {
		$target = $this->getTarget();

		if(!$this->checkAttackRequirements()) {
			return false;
		}

		$x = $target->x - $this->x;
		$y = $target->y + 0.5 - $this->y;
		$z = $target->z - $this->z;

		if($x * $x + $z * $z < 0.8) {
			$this->motionX = 0;
			$this->motionZ = 0;
		} else {
			$this->motionX = $this->getSpeed() * 0.15 * ($x / (abs($x) + abs($z)));
			$this->motionZ = $this->getSpeed() * 0.15 * ($z / (abs($x) + abs($z)));
		}

		if((float) $y !== 0.0) {
			$this->motionY = $this->getSpeed() * 0.15 * $y;
		}

		$this->yaw = rad2deg(atan2(-$x, $z));
		if($this->getNetworkId() === 53) {
			$this->yaw += 180;
		}
		$this->pitch = rad2deg(-atan2($y, sqrt($x * $x + $z * $z)));
		$this->move($this->motionX, $this->motionY, $this->motionZ);

		if($this->distance($target) <= $this->scale + 1.1 && $this->waitingTime <= 0 && $target->isAlive()) {
			$event = new EntityDamageByEntityEvent($this, $target, EntityDamageEvent::CAUSE_ENTITY_ATTACK, $this->getAttackDamage());
			if($target->getHealth() - $event->getFinalDamage() <= 0) {
				if($target instanceof Player) {
					$this->addPetLevelPoints($this->getLoader()->getBlockPetsConfig()->getPlayerExperiencePoints());
				} else {
					$this->addPetLevelPoints($this->getLoader()->getBlockPetsConfig()->getEntityExperiencePoints());
				}
				$this->calmDown();
			}

			$target->attack($event->getFinalDamage(), $event);

			$this->waitingTime = 15;
		} elseif($this->distance($this->getPetOwner()) > 25 || $this->distance($this->getTarget()) > 15) {
			$this->calmDown();
		}

		$this->updateMovement();
		$this->waitingTime--;
		return true;
	}

	public function doRidingMovement($motionX, $motionZ) {
		$rider = $this->getPetOwner();

		$this->pitch = $rider->pitch;
		$this->yaw = $this instanceof EnderDragonPet ? $rider->yaw + 180 : $rider->yaw;

		$x = $rider->getDirectionVector()->x / 2 * $this->getSpeed();
		$z = $rider->getDirectionVector()->z / 2 * $this->getSpeed();
		$y = $rider->getDirectionVector()->y / 2 * $this->getSpeed();

		$finalMotion = [0, 0];
		switch($motionZ) {
			case 1:
				$finalMotion = [$x, $z];
				break;
			case -1:
				$finalMotion = [-$x, -$z];
				break;
		}
		switch($motionX) {
			case 1:
				$finalMotion = [$z, -$x];
				break;
			case -1:
				$finalMotion = [-$z, $x];
				break;
		}

		if(((float) $y) !== 0.0) {
			if($y < 0) {
				$this->motionY = $this->getSpeed() * 0.3 * $y;
			} elseif($this->y - $this->getLevel()->getHighestBlockAt($this->x, $this->z) < $this->flyHeight) {
				$this->motionY = $this->getSpeed() * 0.3 * $y;
			}
		}
		if(abs($y) < 0.2) {
			$this->motionY = 0;
		}
		$this->move($finalMotion[0], $this->motionY, $finalMotion[1]);

		$this->updateMovement();
	}

	/**
	 * @param float             $damage
	 * @param EntityDamageEvent $source
	 */
	public function attack($damage, EntityDamageEvent $source) {
		if($source->getCause() === $source::CAUSE_FALL) {
			$source->setCancelled();
		}
		return parent::attack($damage, $source);
	}
}

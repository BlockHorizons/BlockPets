<?php

namespace BlockHorizons\BlockPets\listeners;

use BlockHorizons\BlockPets\Loader;
use pocketmine\event\Listener;
use pocketmine\event\player\cheat\PlayerIllegalMoveEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\InteractPacket;
use pocketmine\network\mcpe\protocol\PlayerInputPacket;

class RidingListener implements Listener {

	private $loader;

	public function __construct(Loader $loader) {
		$this->loader = $loader;
	}

	public function ridePet(DataPacketReceiveEvent $event) {
		if(($packet = $event->getPacket()) instanceof PlayerInputPacket) {
			if($this->getLoader()->isRidingAPet($event->getPlayer())) {
				$pet = $this->getLoader()->getRiddenPet($event->getPlayer());
				$pet->doRidingMovement($packet->motionX, $packet->motionY);
			}
		} elseif($packet instanceof InteractPacket) {
			if($packet->action === $packet::ACTION_LEAVE_VEHICLE) {
				if($this->getLoader()->isRidingAPet($event->getPlayer())) {
					$this->getLoader()->getRiddenPet($event->getPlayer())->throwRiderOff();
				}
			}
		}
	}

	/**
	 * @return Loader
	 */
	public function getLoader(): Loader {
		return $this->loader;
	}

	public function disableRidingMovementRevert(PlayerIllegalMoveEvent $event) {
		if($this->getLoader()->isRidingAPet($event->getPlayer())) {
			$event->setCancelled();
		}
	}
}
<?php

namespace BlockHorizons\BlockPets\listeners;

use BlockHorizons\BlockPets\Loader;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\cheat\PlayerIllegalMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\InteractPacket;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use pocketmine\network\mcpe\protocol\PlayerInputPacket;
use pocketmine\Player;

class RidingListener implements Listener {

	private $loader;

	public function __construct(Loader $loader) {
		$this->loader = $loader;
	}

	/**
	 * Used for getting values of arrows clicked during the riding of a pet, and when dismounted, as well as throwing the rider off the vehicle when leaving.
	 *
	 * @param DataPacketReceiveEvent $event
	 */
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
		} elseif($packet instanceof PlayerActionPacket) {
			if($packet->action === $packet::ACTION_JUMP) {
				foreach($this->getLoader()->getPetsFrom($event->getPlayer()) as $pet) {
					if($pet->isRiding()) {
						$pet->dismountFromOwner();
					}
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

	/**
	 * Used to dismount the player if it teleports, just like it does in vanilla.
	 *
	 * @param EntityTeleportEvent $event
	 */
	public function onTeleport(EntityTeleportEvent $event) {
		$player = $event->getEntity();
		if($player instanceof Player) {
			if($this->getLoader()->isRidingAPet($player)) {
				$this->getLoader()->getRiddenPet($player)->throwRiderOff();
				foreach($this->getLoader()->getPetsFrom($player) as $pet) {
					$pet->dismountFromOwner();
				}
			}
		}
	}

	/**
	 * Used to ignore any movement revert issues whilst riding a pet.
	 *
	 * @param PlayerIllegalMoveEvent $event
	 */
	public function disableRidingMovementRevert(PlayerIllegalMoveEvent $event) {
		if($this->getLoader()->isRidingAPet($event->getPlayer())) {
			$event->setCancelled();
		}
	}

	/**
	 * Used to dismount the player from the ridden pet. This does not matter for the player, but could have a significant effect on the pet's behaviour.
	 *
	 * @param PlayerQuitEvent $event
	 */
	public function onPlayerQuit(PlayerQuitEvent $event) {
		foreach($this->getLoader()->getPetsFrom($event->getPlayer()) as $pet) {
			if($pet->isRidden()) {
				$pet->throwRiderOff();
			}
			if($this->getLoader()->getBlockPetsConfig()->fetchFromDatabase()) {
				$pet->getCalculator()->storeToDatabase();
				$pet->close();
			}
		}
	}
}
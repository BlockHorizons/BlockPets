<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\listeners;

use BlockHorizons\BlockPets\sessions\PlayerSession;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\cheat\PlayerIllegalMoveEvent;
use pocketmine\event\player\PlayerJumpEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\InteractPacket;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use pocketmine\network\mcpe\protocol\PlayerInputPacket;
use pocketmine\Player;

class RidingListener extends BaseListener {

	/**
	 * Used for getting values of arrows clicked during the riding of a pet, and when dismounted, as well as throwing the rider off the vehicle when leaving.
	 *
	 * @param DataPacketReceiveEvent $event
	 */
	public function ridePet(DataPacketReceiveEvent $event): void {
		$packet = $event->getPacket();
		if($packet instanceof PlayerInputPacket) {
			if($packet->motionX !== 0 && $packet->motionY !== 0) {
				$session = PlayerSession::get($event->getPlayer());
				if($session !== null && ($pet = $session->getRidingPet()) !== null) {
					$pet->doRidingMovement($packet->motionX, $packet->motionY);
				}
			}
		} elseif($packet instanceof InteractPacket) {
			if($packet->action === InteractPacket::ACTION_LEAVE_VEHICLE) {
				$session = PlayerSession::get($event->getPlayer());
				if($session !== null && ($pet = $session->getRidingPet()) !== null) {
					$pet->throwRiderOff();
				}
			}
		}
	}

	/**
	 * Used to dismount the player if it warps, just like it does in vanilla.
	 *
	 * @param EntityTeleportEvent $event
	 */
	public function onTeleport(EntityTeleportEvent $event): void {
		$player = $event->getEntity();
		if($player instanceof Player) {
			$session = PlayerSession::get($player);
			if($session !== null && ($riding_pet = $session->getRidingPet()) !== null) {
				$riding_pet->throwRiderOff();
				foreach($session->getPets() as $pet) {
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
	public function disableRidingMovementRevert(PlayerIllegalMoveEvent $event): void {
		$session = PlayerSession::get($player);
		if($session !== null && $session->isRidingPet()) {
			$event->setCancelled();
		}
	}
}
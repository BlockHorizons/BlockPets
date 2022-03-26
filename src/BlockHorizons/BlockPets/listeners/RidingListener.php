<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\listeners;

use BlockHorizons\BlockPets\Loader;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\InteractPacket;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\player\Player;

class RidingListener implements Listener {

	public function __construct(private Loader $loader) {
	}

	/**
	 * Used for getting values of arrows clicked during the riding of a pet, and when dismounted, as well as throwing the rider off the vehicle when leaving.
	 */
	public function ridePet(DataPacketReceiveEvent $event): void {
		$packet = $event->getPacket();
		if($packet instanceof PlayerAuthInputPacket) {
			$loader = $this->getLoader();
			$player = $event->getOrigin()->getPlayer();
			if($loader->isRidingAPet($player)) {
				if(((int)$packet->getMoveVecX()) === 0 && ((int)$packet->getMoveVecZ()) === 0) {
					return;
				}
				$pet = $loader->getRiddenPet($player);
				if($pet->isClosed() || $pet->isFlaggedForDespawn()) {
					return;
				}
				$pet->doRidingMovement($packet->getMoveVecX(), $packet->getMoveVecZ());
			}
		} elseif($packet instanceof InteractPacket) {
			if($packet->action === InteractPacket::ACTION_LEAVE_VEHICLE) {
				$loader = $this->getLoader();
				$player = $event->getOrigin()->getPlayer();
				if($loader->isRidingAPet($player)) {
					$loader->getRiddenPet($player)->throwRiderOff();
				}
			}
		}
	}

	public function getLoader(): Loader {
		return $this->loader;
	}

	/**
	 * Used to dismount the player if it warps, just like it does in vanilla.
	 */
	public function onTeleport(EntityTeleportEvent $event): void {
		$player = $event->getEntity();
		if($player instanceof Player) {
			$loader = $this->getLoader();
			if($loader->isRidingAPet($player)) {
				$loader->getRiddenPet($player)->throwRiderOff();
				foreach($loader->getPetsFrom($player) as $pet) {
					$pet->dismountFromOwner();
				}
			}
		}
	}

	/**
	 * Used to dismount the player from the ridden pet. This does not matter for the player, but could have a significant effect on the pet's behaviour.
	 */
	public function onPlayerQuit(PlayerQuitEvent $event): void {
		$loader = $this->getLoader();
		foreach($loader->getPetsFrom($event->getPlayer()) as $pet) {
			if($pet->isRidden()) {
				$pet->throwRiderOff();
			}
			$pet->close();
		}
	}
}
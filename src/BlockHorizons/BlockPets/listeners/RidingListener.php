<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\listeners;

use BlockHorizons\BlockPets\Loader;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\cheat\PlayerIllegalMoveEvent;
use pocketmine\event\player\PlayerJumpEvent;
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
	public function ridePet(DataPacketReceiveEvent $event): void {
		$packet = $event->getPacket();
		if($packet instanceof PlayerInputPacket) {
			$loader = $this->getLoader();
			$player = $event->getPlayer();
			if($loader->isRidingAPet($player)) {
				if($packet->motionX === 0 && $packet->motionY === 0) {
					return;
				}
				$pet = $loader->getRiddenPet($player);
				$pet->doRidingMovement($packet->motionX, $packet->motionY);
			}
		} elseif($packet instanceof InteractPacket) {
			if($packet->action === InteractPacket::ACTION_LEAVE_VEHICLE) {
				$loader = $this->getLoader();
				$player = $event->getPlayer();
				if($loader->isRidingAPet($player)) {
					$loader->getRiddenPet($player)->throwRiderOff();
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
	 * Used to dismount the player if it warps, just like it does in vanilla.
	 *
	 * @param EntityTeleportEvent $event
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
	 * Used to ignore any movement revert issues whilst riding a pet.
	 *
	 * @param PlayerIllegalMoveEvent $event
	 */
	public function disableRidingMovementRevert(PlayerIllegalMoveEvent $event): void {
		if($this->getLoader()->isRidingAPet($event->getPlayer())) {
			$event->setCancelled();
		}
	}

	/**
	 * Used to dismount the player from the ridden pet. This does not matter for the player, but could have a significant effect on the pet's behaviour.
	 *
	 * @param PlayerQuitEvent $event
	 */
	public function onPlayerQuit(PlayerQuitEvent $event): void {
		$loader = $this->getLoader();
		$bpConfig = $loader->getBlockPetsConfig();
		foreach($loader->getPetsFrom($event->getPlayer()) as $pet) {
			if($pet->isRidden()) {
				$pet->throwRiderOff();
			}
			if($bpConfig->fetchFromDatabase()) {
				$pet->getCalculator()->storeToDatabase();
				$pet->close();
			}
		}
	}
}
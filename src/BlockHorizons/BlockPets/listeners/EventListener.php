<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\listeners;

use BlockHorizons\BlockPets\pets\BasePet;
use BlockHorizons\BlockPets\pets\IrasciblePet;
use BlockHorizons\BlockPets\sessions\PlayerSession;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class EventListener extends BaseListener {

	/**
	 * Used to ignore fall damage when ridden and anger pets when their owner has been attacked.
	 *
	 * @param EntityDamageEvent $event
	 *
	 * @priority        HIGHEST
	 * @ignoreCancelled true
	 */
	public function onEntityDamage(EntityDamageEvent $event): void {
		$player = $event->getEntity();
		if($player instanceof Player) {
			if($event->getCause() === EntityDamageEvent::CAUSE_FALL) {
				$player_session = PlayerSession::get($player);
				if($player_session !== null && $player_session->isRidingPet()) {
					$event->setCancelled();
					return;
				}
			}
			if($event instanceof EntityDamageByEntityEvent) {
				if($event->isCancelled()) {
					return;
				}

				$attacker = $event->getDamager();
				if($attacker instanceof Living && $attacker !== $player) {
					$session = PlayerSession::get($player);
					if($session !== null) {
						foreach($session->getPets() as $pet) {
							if($pet instanceof IrasciblePet) {
								$pet->setAngry($attacker);
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Used to respawn pets to the player, and fetch pets from the database if this has been configured.
	 *
	 * @param PlayerJoinEvent $event
	 */
	public function onPlayerJoin(PlayerJoinEvent $event): void {
		PlayerSession::create($this->getLoader(), $event->getPlayer());
	}

	public function onPlayerQuit(PlayerQuitEvent $event): void {
		PlayerSession::destroy($event->getPlayer());
	}

	/**
	 * Used to select a name through chat. Allows for names with spaces and players to choose themselves.
	 *
	 * @param PlayerChatEvent $event
	 */
	public function onChat(PlayerChatEvent $event): void {
		$player = $event->getPlayer();
		$session = PlayerSession::get($player);
		if($session !== null && ($data = $session->getSelectionData()) !== null) {
			$event->setCancelled();

			$pet_name = $event->getMessage();
			if($session->getPet($pet_name) !== null) {
				$player->sendMessage(TextFormat::RED . "[Warning] You already own a pet with that name. Please choose a different name.");
				return;
			}

			$pet = $session->addPet($data->toPetData($pet_name, $player->getName()));
			$player->sendMessage(TextFormat::GREEN . "Successfully obtained a " . $pet->getName() . " with the name " . $pet->getPetName());
			$session->setSelectionData(null);
		}
	}

	/**
	 * @param EntitySpawnEvent $event
	 */
	public function onEntitySpawn(EntitySpawnEvent $event): void {
		$entity = $event->getEntity();
		if($entity instanceof BasePet) {
			$clearLaggPlugin = $this->getLoader()->getServer()->getPluginManager()->getPlugin("ClearLagg");
			if($clearLaggPlugin !== null) {
				$clearLaggPlugin->exemptEntity($entity);
			}
		}
	}
}
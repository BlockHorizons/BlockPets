<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\listeners;

use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\BasePet;
use BlockHorizons\BlockPets\pets\IrasciblePet;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class EventListener implements Listener {

	public function __construct(private Loader $loader) {
	}

	/**
	 * Used to ignore fall damage when ridden and anger pets when their owner has been attacked.
	 *
	 * @priority        HIGHEST
	 * @ignoreCancelled true
	 */
	public function onEntityDamage(EntityDamageEvent $event): void {
		$player = $event->getEntity();
		if($player instanceof Player) {
			if($event->getCause() === EntityDamageEvent::CAUSE_FALL) {
				if($this->getLoader()->isRidingAPet($player)) {
					$event->cancel();
					return;
				}
			}
			if($event instanceof EntityDamageByEntityEvent) {
				if($event->isCancelled()) {
					return;
				}
				$pets = $this->getLoader()->getPetsFrom($player);
				if(!empty($pets)) {
					foreach($pets as $pet) {
						if(!($pet instanceof IrasciblePet)) {
							continue;
						}
						$attacker = $event->getDamager();
						if($attacker instanceof Living) {
							$pet->setAngry($attacker);
						}
					}
				}
			}
		}
	}

	public function getLoader(): Loader {
		return $this->loader;
	}

	/**
	 * Used to respawn pets to the player, and fetch pets from the database if this has been configured.
	 */
	public function onPlayerJoin(PlayerJoinEvent $event): void {
		$player = $event->getPlayer();
		$loader = $this->getLoader();

		if(!$loader->getBlockPetsConfig()->doHardReset()) {
			$loader->getDatabase()->load(
				$player->getName(),
				function(array $petData) use($player, $loader): void {
					$hard_reset = $loader->getBlockPetsConfig()->doHardReset();
					foreach($petData as $data) {
						$pet = $loader->createPet(
							$data["EntityName"],
							$player,
							$data["PetName"],
							$data["PetSize"],
							(bool) $data["IsBaby"],
							$data["PetLevel"],
							$data["LevelPoints"],
							(bool) $data["Chested"],
							(bool) $data["Visible"],
							$data["Inventory"]
						);
						$pet->spawnToAll();
						$pet->setDormant(false);
						if($hard_reset) {
							$pet->close();
						}
					}
				}
			);
		}
	}

	/**
	 * Used to select a name through chat. Allows for names with spaces and players to choose themselves.
	 */
	public function onChat(PlayerChatEvent $event): void {
		$player = $event->getPlayer();
		$loader = $this->getLoader();
		if(isset($loader->selectingName[$player->getName()])) {
			$petName = $event->getMessage();
			$event->cancel();
			if($loader->getPetByName($petName, $player->getName()) !== null) {
				$player->sendMessage(TextFormat::RED . "[Warning] You already own a pet with that name. Please choose a different name.");
				return;
			}
			$data = $loader->selectingName[$player->getName()];

			$loader->createPet($data["petType"], $player, $petName, $data["scale"], $data["isBaby"]);
			$player->sendMessage(TextFormat::GREEN . "Successfully obtained a " . $data["petType"] . " with the name " . $event->getMessage());
			unset($loader->selectingName[$player->getName()]);
		}
	}

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
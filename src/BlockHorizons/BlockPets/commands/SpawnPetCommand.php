<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\commands;

use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\PetFactory;
use BlockHorizons\BlockPets\pets\datastorage\types\PetData;
use BlockHorizons\BlockPets\sessions\PlayerSession;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class SpawnPetCommand extends BaseCommand {

	public function __construct(Loader $loader) {
		parent::__construct($loader, "spawnpet", "Spawn a pet for yourself or other players", "/spawnpet <petType> [name] [size] [baby] [player]", ["sp"]);
		$this->setPermission("blockpets.command.spawnpet.use");
	}

	public function onCommand(CommandSender $sender, string $commandLabel, array $args): bool {
		if(!($sender instanceof Player) && count($args) !== 5) {
			$this->sendConsoleError($sender);
			return false;
		}

		if($sender instanceof Player) {
			if(count($args) > 5 || count($args) < 1) {
				return false;
			}

			if(!$sender->hasPermission("blockpets.pet." . strtolower($args[0]))) {
				$this->sendPermissionMessage($sender);
				return true;
			}
		}

		$loader = $this->getLoader();

		$pet_type = PetFactory::getKnownPetId($args[0]);
		if($pet_type === null) {
			$this->sendWarning($sender, $loader->translate("commands.errors.pet.doesnt-exist", [$args[0]]));
			return true;
		}

		$player = $sender;

		if(isset($args[4])) {
			if(!$sender->hasPermission("blockpets.command.spawnpet.others")) {
				$this->sendWarning($sender, $loader->translate("commands.spawnpet.no-permission.others"));
				return true;
			}

			$session = $this->getPlayerSession($args[4], $player);
		} else {
			$session = PlayerSession::get($player);
			if($session === null) {
				$this->sendWarning($sender, $loader->translate("commands.errors.database.player-not-loaded"));
				return true;
			}
		}

		$pet_name = !isset($args[1]) || empty(trim($args[1])) ? $player->getDisplayName() : $args[1];

		if(isset($args[2])) {
			if(!is_numeric($args[2])) {
				$this->sendWarning($sender, $loader->translate("commands.errors.pet.numeric"));
				return true;
			}
		}

		$pet_size = (int) ($args[2] ?? 1.0);
		$pet_is_baby = isset($args[3]) && $args[3] !== "false" && $args !== "no";

		if(count($session->getPets()) >= $loader->getBlockPetsConfig()->getMaxPets() && !$player->hasPermission("blockpets.bypass-limit")) {
			$sender->sendMessage($sender, $loader->translate("commands.spawnpet.exceeded-limit", [
				$player === $sender ? "You have " : "Your target has "
			]));
			return true;
		}
		if(isset($args[1]) && strtolower($args[1]) === "select") {
			if($player !== $sender) {
				$sender->sendMessage(TF::GREEN . $loader->translate("commands.spawnpet.selecting-name", [$player->getName()]));
			}
			$player->sendMessage(TF::GREEN . $loader->translate("commands.spawnpet.name"));
			$session->setSelectionData(new PetSelectionData($pet_type, $pet_size, $pet_is_baby));
			return true;
		}
		if($session->getPet($pet_name) !== null) {
			$this->sendWarning($sender, $loader->translate("commands.errors.player.already-own-pet"));
			return true;
		}

		$pet_data = new PetData($pet_name, $pet_type, $player->getName());
		$pet_data->scale = $pet_size;
		$pet_data->is_baby = $pet_is_baby;

		$pet = $session->addPet($pet_data);
		if($pet === null) {
			$this->sendWarning($sender, $loader->translate("commands.errors.plugin-cancelled"));
			return true;
		}

		$sender->sendMessage(TF::GREEN . $loader->translate("commands.spawnpet.success", [$pet->getPetName()]));

		if($player->getName() !== $sender->getName()) {
			$player->sendMessage(TF::GREEN . $loader->translate("commands.spawnpet.success.other", [$pet->getPetName()]));
		}
		return true;
	}
}

<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\commands;

use BlockHorizons\BlockPets\Loader;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
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

		$player = $sender;
		$loader = $this->getLoader();

		if(isset($args[4])) {
			if(($player = $loader->getServer()->getPlayerByPrefix($args[4])) === null) {
				$this->sendWarning($sender, $loader->translate("commands.errors.player.not-found"));
				return true;
			}
			if(!$sender->hasPermission("blockpets.command.spawnpet.others")) {
				$this->sendWarning($sender, $loader->translate("commands.spawnpet.no-permission.others"));
				return true;
			}
		}

		if(!isset($args[1]) || empty(trim($args[1]))) {
			$args[1] = $player->getName();
		}

		if(isset($args[2])) {
			if(!is_numeric($args[2])) {
				$this->sendWarning($sender, $loader->translate("commands.errors.pet.numeric"));
				return true;
			}
		}

		if(isset($args[3])) {
			if($args[3] === "false" || $args[3] === "no") {
				$args[3] = false;
			} else {
				$args[3] = true;
			}
		} else {
			$args[3] = false;
		}
		$petName = $loader->getPet($args[0]);
		if($petName === null) {
			$this->sendWarning($sender, $loader->translate("commands.errors.pet.doesnt-exist", [$args[0]]));
			return true;
		}
		if(count($loader->getPetsFrom($player)) >= $loader->getBlockPetsConfig()->getMaxPets() && !$player->hasPermission("blockpets.bypass-limit")) {
			$sender->sendMessage($loader->translate("commands.spawnpet.exceeded-limit", [
				$player === $sender ? "You have " : "Your target has "
			]));
			return true;
		}
		if(strtolower($args[1]) === "select") {
			if($player !== $sender) {
				$sender->sendMessage(TF::GREEN . $loader->translate("commands.spawnpet.selecting-name", [$player->getName()]));
			}
			$player->sendMessage(TF::GREEN . $loader->translate("commands.spawnpet.name"));
			$loader->selectingName[$player->getName()] = [
				"petType" => $petName,
				"scale" => isset($args[2]) ? (float) $args[2] : 1.0,
				"isBaby" => $args[3] ?? false
			];
			return true;
		}
		if($loader->getPetByName($args[1], $player->getName()) !== null) {
			$this->sendWarning($sender, $loader->translate("commands.errors.player.already-own-pet"));
			return true;
		}

		$pet = $loader->createPet((string) $petName, $player, $args[1], isset($args[2]) ? (float) $args[2] : 1.0, $args[3]);
		if($pet === null) {
			$this->sendWarning($sender, $loader->translate("commands.errors.plugin-cancelled"));
			return true;
		}
		$sender->sendMessage(TF::GREEN . $loader->translate("commands.spawnpet.success", [$args[1]]));
		$pet->register();
		if($player->getName() !== $sender->getName()) {
			$player->sendMessage(TF::GREEN . $loader->translate("commands.spawnpet.success.other", [$args[1]]));
		}
		return true;
	}
}
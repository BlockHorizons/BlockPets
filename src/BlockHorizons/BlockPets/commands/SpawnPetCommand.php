<?php

namespace BlockHorizons\BlockPets\commands;

use BlockHorizons\BlockPets\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class SpawnPetCommand extends BaseCommand {

	public function __construct(Loader $loader) {
		parent::__construct($loader, "spawnpet", "Spawn a pet for yourself or other players", "/spawnpet <petType> [name] [size] [baby] [player]", ["sp"]);
		$this->setPermission("blockpets.command.spawnpet");
	}

	public function execute(CommandSender $sender, $commandLabel, array $args): bool {
		if(!$this->testPermission($sender)) {
			$this->sendNoPermission($sender);
			return true;
		}

		if(!$sender instanceof Player) {
			$this->sendConsoleError($sender);
			return true;
		}

		if(count($args) > 5 || count($args) < 1) {
			$sender->sendMessage(TF::RED . "[Usage] " . $this->getUsage());
			return true;
		}

		if(!$sender->hasPermission("blockpets.pet." . strtolower($args[0]))) {
			$sender->sendMessage(TF::RED . "[Warning] You don't have permission to spawn that pet.");
			return true;
		}

		$player = $sender;
		if(isset($args[4])) {
			if(($player = $this->getLoader()->getServer()->getPlayer($args[4])) === null) {
				$sender->sendMessage(TF::RED . "[Warning] That player isn't online.");
				return true;
			}
		}

		if(isset($args[2]) && !is_numeric($args[2])) {
			$sender->sendMessage(TF::RED . "[Warning] The pet scale should be numeric.");
			return true;
		}

		if(isset($args[3])) {
			if($args[3] === "false") {
				$args[3] = 0;
			} else {
				$args[3] = 1;
			}
		} else {
			$args[3] = 0;
		}
		$petName = $this->getLoader()->getPet($args[0]);
		if($petName === null){
			$sender->sendMessage(TF::RED . "[Warning] Pet type " . $args[0] . " does not exist!");
			return true;
		}
		if(count($this->getLoader()->getPetsFrom($player)) >= $this->getLoader()->getBlockPetsConfig()->getMaxPets() && !$player->hasPermission("blockpets.bypass-limit")) {
			$sender->sendMessage(TF::RED . "[Warning] " . $player === $sender ? "You have " : "Your target has " . " exceeded the pet limit.");
			return true;
		}
		if(!isset($args[1]) || strtolower($args[1]) === "select") {
			if($player !== $sender) {
				$sender->sendMessage(TF::GREEN . $player->getName() . " is now selecting a name for their pet.");
			}
			$player->sendMessage(TF::GREEN . "Please type a name for your pet in chat.");
			$this->getLoader()->selectingName[$player->getName()] = ["petType" => $petName];
			return true;
		}
		if($this->getLoader()->getPetByName($args[1], $sender) !== null) {
			$sender->sendMessage(TF::RED . "[Warning] You already own a pet with that name.");
			return true;
		}
		if($this->getLoader()->createPet((string) $petName, $player, (string) $args[1], isset($args[2]) ? (float) $args[2] : 1.0, $args[3]) === null) {
			$sender->sendMessage(TF::RED . "[Warning] A plugin has cancelled spawning this pet.");
			return true;
		}
		$sender->sendMessage(TF::GREEN . "Successfully spawned a pet with the name: " . TF::AQUA . $args[1]);
		if($player->getName() !== $sender->getName()) {
			$player->sendMessage(TF::GREEN . "You have received a pet with the name: " . TF::AQUA . $args[1]);
		}
		return true;
	}
}

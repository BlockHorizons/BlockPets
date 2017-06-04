<?php

namespace BlockHorizons\BlockPets\commands;

use BlockHorizons\BlockPets\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class SpawnPetCommand extends BaseCommand {

	public function __construct(Loader $loader) {
		parent::__construct($loader, "spawnpet", "Spawn a pet for yourself or other players", "/spawnpet <petType> <name> [size] [baby] [player]", ["sp"]);
		$this->setPermission("blockpets.command.spawnpet");
	}

	public function execute(CommandSender $sender, $commandLabel, array $args) {
		if(!$this->testPermission($sender)) {
			$this->sendNoPermission($sender);
			return true;
		}

		if(!$sender instanceof Player) {
			$this->sendConsoleError($sender);
			return true;
		}

		if(count($args) > 5 || count($args) < 2) {
			$sender->sendMessage(TF::RED . "[Usage] " . $this->getUsage());
			return true;
		}

		if(!$sender->hasPermission("blockpets.pet." . strtolower($args[0])) && !$sender->hasPermission("blockpets.pet.*")) {
		$sender->sendMessage(TF::RED . "[Warning] You don't have permission to spawn that pet.");
			return true;
		}

		$player = $sender;
		if(isset($args[4])) {
			if(($player = $this->getLoader()->getServer()->getPlayer($args[3])) === null) {
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
				$args[3] = false;
			} else {
				$args[3] = true;
			}
		} else {
			$args[3] = false;
		}
		$petName = $this->getLoader()->getPet($args[0]);
		$pet = $this->getLoader()->createPet($petName, $player, $args[1], isset($args[2]) ? $args[2] : 1.0, $args[3]);
		$pet->spawnToAll();
		$sender->sendMessage(TF::GREEN . "Successfully spawned a pet with the name: " . TF::AQUA . $args[1]);
		if($player->getName() !== $sender->getName()) {
			$player->sendMessage(TF::GREEN . "You have received a pet with the name: " . TF::AQUA . $args[1]);
		}
		return true;
	}

	public function generateCustomCommandData(Player $player) {
		$commandData = parent::generateCustomCommandData($player);

		$availablePets = Loader::PETS;
		foreach($availablePets as $key => $pet) {
			$availablePets[$key] = strtolower($pet);
		}
		$commandData["overloads"]["default"]["input"]["parameters"] = [
			0 => [
				"type" => "stringenum",
				"name" => "type",
				"optional" => false,
				"enum_values" => $availablePets
			],
			1 => [
				"type" => "rawtext",
				"name" => "name",
				"optional" => false,
			],
			2 => [
				"type" => "int",
				"name" => "size",
				"optional" => true
			],
			3 => [
				"type" => "stringenum",
				"name" => "baby",
				"optional" => true,
				"enum_values" => [
					"true",
					"false"
				]
			],
			4 => [
				"type" => "rawtext",
				"name" => "player",
				"optional" => true
			]
		];
		return $commandData;
	}
}

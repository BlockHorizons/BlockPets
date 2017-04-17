<?php

namespace BlockHorizons\BlockPets\commands;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;
use BlockHorizons\BlockPets\Loader;

class LevelUpPetCommand extends BaseCommand {

	public function __construct(Loader $loader) {
		parent::__construct($loader, "leveluppet", "Level up your pet", "/leveluppet <petName>", ["lup"]);
		$this->setPermission("blockpets.command.leveluppet");
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

		if(count($args) > 1 || count($args) < 1) {
			$sender->sendMessage(TF::RED . "[Usage] " . $this->getUsage());
			return true;
		}

		if(($pet = $this->getLoader()->getPetByName($args[0])) === null) {
			$sender->sendMessage(TF::RED . "[Warning] A pet with that name doesn't exist.");
			return true;
		}

		$this->getLoader()->getPetByName($args[0])->levelUp();
		$sender->sendMessage(TF::GREEN . "Successfully leveled up the pet: " . TF::AQUA . $pet->getNameTag());
		return true;
	}

	public function generateCustomCommandData(Player $player) {
		$commandData = parent::generateCustomCommandData($player);

		$commandData["default"]["input"]["parameters"] = [
			0 => [
				"type" => "string",
				"name" => "pet name",
				"optional" => false
			]
		];
		return $commandData;
	}
}

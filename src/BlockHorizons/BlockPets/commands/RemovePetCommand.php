<?php

namespace BlockHorizons\BlockPets\commands;

use BlockHorizons\BlockPets\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class RemovePetCommand extends BaseCommand {

	public function __construct(Loader $loader) {
		parent::__construct($loader, "removepet", "Remove a pet", "/removepet <petName>", ["rmp"]);
		$this->setPermission("blockpets.command.removepet");
	}

	public function execute(CommandSender $sender, $commandLabel, array $args) {
		if(!$this->testPermission($sender)) {
			$this->sendNoPermission($sender);
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

		if($this->getLoader()->removePet($args[0])) {
			$sender->sendMessage(TF::GREEN . "Successfully removed the pet: " . TF::AQUA . $pet->getNameTag());
		} else {
			$sender->sendMessage(TF::RED . "[Warning] A pet with that name doesn't exist.");
		}
		return true;
	}

	public function generateCustomCommandData(Player $player) {
		$commandData = parent::generateCustomCommandData($player);

		$commandData["overloads"]["default"]["input"]["parameters"] = [
			0 => [
				"type" => "rawtext",
				"name" => "pet name",
				"optional" => false
			]
		];
		return $commandData;
	}
}

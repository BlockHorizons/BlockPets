<?php

namespace BlockHorizons\BlockPets\commands;

use BlockHorizons\BlockPets\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

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

		if(($pet = $this->getLoader()->getPetByName($args[0])) === null) {
			$sender->sendMessage(TF::RED . "[Warning] A pet with that name doesn't exist.");
			return true;
		}

		$amount = 1;
		if(isset($args[1])) {
			if(is_numeric($args[1])) {
				$amount = $args[1];
			}
		}

		$pet->levelUp($amount);
		$sender->sendMessage(TF::GREEN . "Successfully leveled up the pet: " . TF::AQUA . $pet->getPetName() . ($amount === 1 ? " once!" : " " . $amount . " times!"));
		return true;
	}

	public function generateCustomCommandData(Player $player) {
		$commandData = parent::generateCustomCommandData($player);

		$commandData["overloads"]["default"]["input"]["parameters"] = [
			0 => [
				"type" => "rawtext",
				"name" => "pet name",
				"optional" => false
			],
			1 => [
				"type" => "int",
				"name" => "amount",
				"optional" => true
			]
		];
		return $commandData;
	}
}

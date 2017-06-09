<?php

namespace BlockHorizons\BlockPets\commands;

use BlockHorizons\BlockPets\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class LevelUpPetCommand extends BaseCommand {

	public function __construct(Loader $loader) {
		parent::__construct($loader, "leveluppet", "Level up your pet", "/leveluppet <petName> [player]", ["lup"]);
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

		if(isset($args[1])) {
			if(($player = $this->getLoader()->getServer()->getPlayer($args[1])) === null) {
				$sender->sendMessage(TF::RED . "[Warning] The given player could not be found.");
				return true;
			}
			if(($pet = $this->getLoader()->getPetByName($args[0], $player)) === null) {
				$sender->sendMessage(TF::RED . "[Warning] The given player does not own a pet with that name.");
				return true;
			}
			$pet->levelUp($amount);
			$sender->sendMessage(TF::GREEN . "Successfully leveled up the pet: " . TF::AQUA . $pet->getPetName() . TF::GREEN . ($amount === 1 ? " once!" : " " . $amount . " times!"));
			return true;
		}

		$pet->levelUp($amount);
		$sender->sendMessage(TF::GREEN . "Successfully leveled up the pet: " . TF::AQUA . $pet->getPetName() . TF::GREEN . ($amount === 1 ? " once!" : " " . $amount . " times!"));
		return true;
	}
}

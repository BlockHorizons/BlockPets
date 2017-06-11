<?php

namespace BlockHorizons\BlockPets\commands;

use BlockHorizons\BlockPets\Loader;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;

class RemovePetCommand extends BaseCommand {

	public function __construct(Loader $loader) {
		parent::__construct($loader, "removepet", "Remove a pet", "/removepet <petName> [player]", ["rmp"]);
		$this->setPermission("blockpets.command.removepet");
	}

	public function execute(CommandSender $sender, $commandLabel, array $args): bool {
		if(!$this->testPermission($sender)) {
			$this->sendNoPermission($sender);
			return true;
		}

		if(($pet = $this->getLoader()->getPetByName($args[0])) === null) {
			$sender->sendMessage(TF::RED . "[Warning] A pet with that name doesn't exist.");
			return true;
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
			if($this->getLoader()->removePet($pet->getPetName(), $player) === false) {
				$sender->sendMessage(TF::RED . "[Warning] A plugin has cancelled the removal of this pet.");
				return true;
			}
			$sender->sendMessage(TF::GREEN . "Successfully removed the pet: " . TF::AQUA . $pet->getPetName());
			return true;
		}

		if($this->getLoader()->removePet($args[0])) {
			$sender->sendMessage(TF::GREEN . "Successfully removed the pet: " . TF::AQUA . $pet->getPetName());
		} else {
			$sender->sendMessage(TF::RED . "[Warning] A plugin has cancelled the removal this pet.");
		}
		return true;
	}
}

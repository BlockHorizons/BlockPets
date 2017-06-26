<?php

namespace BlockHorizons\BlockPets\commands;

use BlockHorizons\BlockPets\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class ClearPetCommand extends BaseCommand {

	public function __construct(Loader $loader) {
		parent::__construct($loader, "clearpet", "Clear a pet", "/leveluppet <petName>", ["cp"]);
		$this->setPermission("blockpets.command.clearpet");
	}

	public function execute(CommandSender $sender, $commandLabel, array $args): bool {
		if(!$this->testPermission($sender)) {
			$this->sendPermissionMessage($sender);
			return true;
		}

		if(!$sender instanceof Player) {
			$this->sendConsoleError($sender);
			return true;
		}

		if(($pet = $this->getLoader()->getPetByName($args[0], $sender)) === null) {
			$sender->sendMessage(TF::RED . "[Warning] A pet with that name doesn't exist.");
			return true;
		}

		if($this->getLoader()->removePet($pet->getPetName(), $sender) === false) {
			$sender->sendMessage(TF::RED . "[Warning] A plugin has cancelled the removal of this pet.");
			return true;
		}
		$sender->sendMessage(TF::GREEN . "Successfully cleared the pet: " . TF::AQUA . $pet->getPetName());
		return true;
	}
}

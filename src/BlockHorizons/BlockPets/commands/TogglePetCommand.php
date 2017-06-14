<?php

namespace BlockHorizons\BlockPets\commands;

use BlockHorizons\BlockPets\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class TogglePetCommand extends BaseCommand {

	public function __construct(Loader $loader) {
		parent::__construct($loader, "togglepet", "Toggle pets on/off", "/togglepet [pet]", ["togglep"]);
		$this->setPermission("blockpets.command.togglepet");
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
		if(!isset($args[0])) {
			$sender->sendMessage(TF::RED . "[Warning] You did not specify a pet to toggle.");
			return true;
		}

		if(strtolower($args[0]) === "all") {
			$this->getLoader()->togglePets($sender);
			$sender->sendMessage(TF::GREEN . "Successfully toggled your pets " . ($this->getLoader()->arePetsToggledOn($sender) ? "on." : "off."));
		} else {
			$pet = $this->getLoader()->getPetByName($args[0], $sender);
			if($pet === null) {
				$sender->sendMessage(TF::RED . "[Warning] You do not own a pet with the given name.");
				return true;
			}
			$this->getLoader()->togglePet($pet, $sender);
			$sender->sendMessage(TF::GREEN . "Successfully toggled the pet " . TF::AQUA . $pet->getPetName() . TF::RESET . TF::GREEN . ($this->getLoader()->isPetToggledOn($pet, $sender) ? "off." : "on."));
		}
		return true;
	}
}
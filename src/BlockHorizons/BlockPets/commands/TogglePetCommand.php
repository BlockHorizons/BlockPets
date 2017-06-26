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
			$this->sendPermissionMessage($sender);
			return true;
		}
		if(!$sender instanceof Player) {
			$this->sendConsoleError($sender);
			return true;
		}
		if(!isset($args[0])) {
			$this->sendWarning($sender, "You did not specify a pet to toggle.");
			return true;
		}

		if(strtolower($args[0]) === "all") {
			$this->getLoader()->togglePets($sender);
			$sender->sendMessage(TF::GREEN . $this->getLoader()->translate("commands.togglepet.success", [
			    ($this->getLoader()->arePetsToggledOn($sender) ? "on." : "off.")
			]));
		} else {
			$pet = $this->getLoader()->getPetByName($args[0], $sender);
			if($pet === null) {
				$this->sendWarning($sender, $this->getLoader()->translate("commands.errors.player.no-pet"));
				return true;
			}
			$this->getLoader()->togglePet($pet, $sender);
			$sender->sendMessage(TF::GREEN . $this->getLoader()->translate("commands.togglepet.success.other", [
			    $pet->getPetName(),
			    ($this->getLoader()->isPetToggledOn($pet, $sender) ? " off." : " on.")
			]));
		}
		return true;
	}
}

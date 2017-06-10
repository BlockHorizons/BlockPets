<?php

namespace BlockHorizons\BlockPets\commands;

use BlockHorizons\BlockPets\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class TogglePetsCommand extends BaseCommand {

	public function __construct(Loader $loader) {
		parent::__construct($loader, "togglepets", "Toggle pets on/off", "/togglepets", ["togglepet", "togglep"]);
		$this->setPermission("blockpets.command.togglepets");
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

		$this->getLoader()->togglePets($sender);
		$sender->sendMessage(TF::GREEN . "Successfully toggled your pets " . $this->getLoader()->arePetsToggledOn($sender) ? "on." : "off.");
		return true;
	}
}
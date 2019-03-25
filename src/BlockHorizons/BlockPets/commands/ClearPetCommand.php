<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\commands;

use BlockHorizons\BlockPets\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class ClearPetCommand extends SessionDependentCommand {

	public function __construct(Loader $loader) {
		parent::__construct($loader, "clearpet", "Clear a pet", "/clearpet <petName>", ["cp"]);
		$this->setPermission("blockpets.command.clearpet");
	}

	public function onCommand(CommandSender $sender, string $commandLabel, array $args): bool {
		if(!($sender instanceof Player)) {
			$this->sendConsoleError($sender, true);
			return true;
		}

		if(!isset($args[0])) {
			return false;
		}

		$pet = $this->getPetByName($args[0], $sender, $session);
		$session->deletePet($pet);
		$sender->sendMessage(TF::GREEN . $this->getLoader()->translate("commands.removepet.success", [$pet->getPetName()]));
		return true;
	}
}

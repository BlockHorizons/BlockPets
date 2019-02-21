<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\commands;

use BlockHorizons\BlockPets\Loader;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;

class RemovePetCommand extends BaseCommand {

	public function __construct(Loader $loader) {
		parent::__construct($loader, "removepet", "Remove a pet", "/removepet <petName> [player]", ["rmp"]);
		$this->setPermission("blockpets.command.removepet");
	}

	public function onCommand(CommandSender $sender, string $commandLabel, array $args): bool {
		if(!isset($args[0])) {
			return false;
		}

		if(isset($args[1])) {
			$pet = $this->getPlayerPet($args[1], $args[0], $player, $session);
		} else {
			$pet = $this->getPetByName($args[0], $sender, $session);
		}

		$session->deletePet($pet);
		$sender->sendMessage(TF::GREEN . $this->getLoader()->translate("commands.removepet.success", [$pet->getPetName()]));
		return true;
	}
}

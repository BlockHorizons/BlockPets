<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\commands;

use BlockHorizons\BlockPets\Loader;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;

class ClearPetCommand extends BaseCommand {

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

		$loader = $this->getLoader();
		if(($pet = $loader->getPetByName($args[0], $sender->getName())) === null) {
			$this->sendWarning($sender, $loader->translate("commands.errors.pet.doesnt-exist"));
			return true;
		}

		$loader->removePet($pet);
		$loader->getDatabase()->unregisterPet($pet);
		$sender->sendMessage(TF::GREEN . $loader->translate("commands.removepet.success", [$pet->getPetName()]));
		return true;
	}
}

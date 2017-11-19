<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\commands;

use BlockHorizons\BlockPets\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class ClearPetCommand extends BaseCommand {

	public function __construct(Loader $loader) {
		parent::__construct($loader, "clearpet", "Clear a pet", "/clearpet <petName>", ["cp"]);
		$this->setPermission("blockpets.command.clearpet");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
		if(!$this->testPermission($sender)) {
			$this->sendPermissionMessage($sender);
			return true;
		}

		if(!$sender instanceof Player) {
			$this->sendConsoleError($sender, true);
			return true;
		}

		if(!isset($args[0])){
			$sender->sendMessage(TF::RED . "[Usage] " . $this->getUsage());
			return true;
		}

		if(($pet = $this->getLoader()->getPetByName($args[0], $sender)) === null) {
			$this->sendWarning($sender, $this->getLoader()->translate("commands.errors.pet.doesnt-exist"));
			return true;
		}

		if($this->getLoader()->removePet($pet->getPetName(), $sender) === false) {
			$this->sendWarning($sender, $this->getLoader()->translate("commands.errors.plugin-cancelled"));
			return true;
		}
		$sender->sendMessage(TF::GREEN . $this->getLoader()->translate("commands.removepet.success", [$pet->getPetName()]));
		return true;
	}
}

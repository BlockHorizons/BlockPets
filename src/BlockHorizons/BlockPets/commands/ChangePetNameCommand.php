<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\commands;

use BlockHorizons\BlockPets\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class ChangePetNameCommand extends BaseCommand {

	public function __construct(Loader $loader) {
		parent::__construct($loader, "changepetname", "Changes the name of a pet", "/changepetname <pet name> <new name> [player]", ["cpn, chpn"]);
		$this->setPermission("blockpets.command.changepetname.use");
	}

	public function onCommand(CommandSender $sender, string $commandLabel, array $args): bool {
		if(!($sender instanceof Player) && count($args) !== 3) {
			$this->sendConsoleError($sender);
			return false;
		}

		if(!isset($args[1]) || empty(trim($args[1]))) {
			$this->sendWarning($sender, "The name you entered is invalid.");
			return true;
		}

		$petName = $args[0];
		$newName = $args[1];

		if(isset($args[2])) {
			if($sender instanceof Player && $sender->hasPermission("blockpets.command.changepetname.others")) {
				$this->sendPermissionMessage($sender);
				return true;
			}
			$pet = $this->getPlayerPet($args[2], $petName);
		}else{
			$pet = $this->getPetByName($args[2], $sender);
		}

		$oldName = $pet->getPetName();
		$pet->changeName($newName);
		$sender->sendMessage(TF::GREEN . $this->getLoader()->translate("commands.changepetname.success", [$oldName, $newName]));
		return true;
	}
}

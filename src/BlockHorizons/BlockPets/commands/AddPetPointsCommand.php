<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\commands;

use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\sessions\PlayerSessionUtils;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TF;

class AddPetPointsCommand extends BaseCommand {

	public function __construct(Loader $loader) {
		parent::__construct($loader, "addpetpoints", "Add level points to a pet", "/addpetpoints <petName> [amount] [player]", ["app"]);
		$this->setPermission("blockpets.command.addpetpoints");
	}

	public function onCommand(CommandSender $sender, string $commandLabel, array $args): bool {
		if(!isset($args[0])) {
			return false;
		}

		$petName = $args[0];

		$amount = 1;
		if(isset($args[1])) {
			if(is_numeric($args[1])) {
				$amount = (int) $args[1];
			}
		}

		$loader = $this->getLoader();

		if(isset($args[2])) {
			$pet = $this->getPlayerPet($args[2], $petName);
		}else{
			$pet = $this->getPetByName($petName);
		}

		$pet->addPetLevelPoints($amount);
		$sender->sendMessage(TF::GREEN . $loader->translate("commands.addpetpoints.success", [$amount, $pet->getPetName()]));
		return true;
	}
}

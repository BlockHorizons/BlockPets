<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\commands;

use BlockHorizons\BlockPets\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class TogglePetCommand extends BaseCommand {

	public function __construct(Loader $loader) {
		parent::__construct($loader, "togglepet", "Toggle pets on/off", "/togglepet <all/pet name> [player]", ["togglep"]);
		$this->setPermission("blockpets.command.togglepet");
	}

	public function onCommand(CommandSender $sender, string $commandLabel, array $args): bool {
		if(!isset($args[0])) {
			$this->sendWarning($sender, $this->getLoader()->translate("commands.togglepet.no-pet-specified"));
			return false;
		}

		if(!($sender instanceof Player) && count($args) !== 2) {
			$this->sendConsoleError($sender);
			return false;
		}

		$player = $sender;
		$loader = $this->getLoader();
		if(isset($args[1])) {
			if(($player = $loader->getServer()->getPlayer($args[1])) === null) {
				$this->sendWarning($sender, $loader->translate("commands.errors.player.not-found"));
				return true;
			}
		}
		if(strtolower($args[0]) === "all") {
			$loader->togglePets($player);
			$sender->sendMessage(TF::GREEN . $loader->translate("commands.togglepet.success", [
					($loader->arePetsToggledOn($sender) ? "on" : "off")
				]));
		}
		$pet = $loader->getPetByName($args[0], $player);
		if($pet === null) {
			$this->sendWarning($sender, $loader->translate("commands.errors.player.no-pet"));
			return true;
		}
		$loader->togglePet($pet, $player);
		$sender->sendMessage(TF::GREEN . $loader->translate("commands.togglepet.success.other", [
				$pet->getPetName(),
				($loader->isPetToggledOn($pet, $sender) ? " off" : " on")
			]));
		return true;
	}
}

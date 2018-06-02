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

	public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
		if(!$this->testPermission($sender)) {
			$this->sendPermissionMessage($sender);
			return true;
		}

		if(!isset($args[0])) {
			$this->sendWarning($sender, $this->getLoader()->translate("commands.togglepet.no-pet-specified"));
			$sender->sendMessage(TF::RED . "[Usage] " . $this->getUsage());
			return true;
		}

		if(!($sender instanceof Player) && count($args) !== 2) {
			$this->sendConsoleError($sender);
			$sender->sendMessage(TF::RED . "[Usage] " . $this->getUsage());
			return true;
		}

		$player = $sender;
		if(isset($args[1])) {
			if(($player = $this->getLoader()->getServer()->getPlayer($args[1])) === null) {
				$this->sendWarning($sender, $this->getLoader()->translate("commands.errors.player.not-found"));
				return true;
			}
		}
		if(strtolower($args[0]) === "all") {
			$this->getLoader()->togglePets($player);
			$sender->sendMessage(TF::GREEN . $this->getLoader()->translate("commands.togglepet.success", [
					($this->getLoader()->arePetsToggledOn($sender) ? "on" : "off")
				]));
		}
		$pet = $this->getLoader()->getPetByName($args[0], $player);
		if($pet === null) {
			$this->sendWarning($sender, $this->getLoader()->translate("commands.errors.player.no-pet"));
			return true;
		}
		$this->getLoader()->togglePet($pet, $player);
		$sender->sendMessage(TF::GREEN . $this->getLoader()->translate("commands.togglepet.success.other", [
				$pet->getPetName(),
				($this->getLoader()->isPetToggledOn($pet, $sender) ? " off" : " on")
			]));
		return true;
	}
}

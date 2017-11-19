<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\commands;

use BlockHorizons\BlockPets\Loader;
use pocketmine\command\CommandSender;
use pocketmine\level\particle\HeartParticle;
use pocketmine\utils\TextFormat as TF;

class HealPetCommand extends BaseCommand {

	public function __construct(Loader $loader) {
		parent::__construct($loader, "healpet", "Heal a pet back to full health", "/healpet <petName> [player]", ["hp", "petheal"]);
		$this->setPermission("blockpets.command.healpet");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
		if(!$this->testPermission($sender)) {
			$this->sendPermissionMessage($sender);
			return true;
		}

		if(!isset($args[0])){
			$sender->sendMessage(TF::RED . "[Usage] " . $this->getUsage());
			return true;
		}

		if(($pet = $this->getLoader()->getPetByName($args[0])) === null) {
			$this->sendWarning($sender, $this->getLoader()->translate("commands.errors.pet.doesnt-exist"));
			return true;
		}

		if(isset($args[1])) {
			if(($player = $this->getLoader()->getServer()->getPlayer($args[1])) === null) {
				$this->sendWarning($sender, $this->getLoader()->translate("commands.errors.player.not-found"));
				return true;
			}
			if(($pet = $this->getLoader()->getPetByName($args[0], $player)) === null) {
				$this->sendWarning($sender, $this->getLoader()->translate("commands.errors.player.no-pet-other"));
				return true;
			}
		}

		$pet->fullHeal();
		$pet->getLevel()->addParticle(new HeartParticle($pet->add(0, 2), 4));
		$sender->sendMessage(TF::GREEN . $this->getLoader()->translate("commands.healpet.success", [$pet->getPetName()]));
		return true;
	}
}

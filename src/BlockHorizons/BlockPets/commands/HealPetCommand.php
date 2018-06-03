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

	public function onCommand(CommandSender $sender, string $commandLabel, array $args): bool {
		if(!isset($args[0])) {
			return false;
		}

		$loader = $this->getLoader();
		if(($pet = $loader->getPetByName($args[0])) === null) {
			$this->sendWarning($sender, $loader->translate("commands.errors.pet.doesnt-exist"));
			return true;
		}

		if(isset($args[1])) {
			if(($player = $loader->getServer()->getPlayer($args[1])) === null) {
				$this->sendWarning($sender, $loader->translate("commands.errors.player.not-found"));
				return true;
			}
			if(($pet = $loader->getPetByName($args[0], $player)) === null) {
				$this->sendWarning($sender, $loader->translate("commands.errors.player.no-pet-other"));
				return true;
			}
		}

		$pet->fullHeal();
		$pet->getLevel()->addParticle(new HeartParticle($pet->add(0, 2), 4));
		$sender->sendMessage(TF::GREEN . $loader->translate("commands.healpet.success", [$pet->getPetName()]));
		return true;
	}
}

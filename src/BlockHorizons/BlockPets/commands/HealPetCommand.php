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

		if(isset($args[1])) {
			$pet = $this->getPlayerPet($args[1], $args[0]);
		} else {
			$pet = $this->getPetByName($args[0], $sender);
		}

		$pet->fullHeal();
		$pet->getLevel()->addParticle(new HeartParticle($pet->add(0, 2), 4));
		$sender->sendMessage(TF::GREEN . $this->getLoader()->translate("commands.healpet.success", [$pet->getPetName()]));
		return true;
	}
}

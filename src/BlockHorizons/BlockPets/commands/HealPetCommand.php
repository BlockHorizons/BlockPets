<?php

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

	public function execute(CommandSender $sender, $commandLabel, array $args): bool {
		if(!$this->testPermission($sender)) {
			$this->sendNoPermission($sender);
			return true;
		}

		if(($pet = $this->getLoader()->getPetByName($args[0])) === null) {
			$sender->sendMessage(TF::RED . "[Warning] A pet with that name doesn't exist.");
			return true;
		}

		if(isset($args[1])) {
			if(($player = $this->getLoader()->getServer()->getPlayer($args[1])) === null) {
				$sender->sendMessage(TF::RED . "[Warning] The given player could not be found.");
				return true;
			}
			if(($pet = $this->getLoader()->getPetByName($args[0], $player)) === null) {
				$sender->sendMessage(TF::RED . "[Warning] The given player does not own a pet with that name.");
				return true;
			}
			$pet->fullHeal();
			$pet->getLevel()->addParticle(new HeartParticle($pet->add(0, 2), 4));
			$sender->sendMessage(TF::GREEN . "The pet " . $pet->getPetName() . TF::RESET . TF::GREEN . " has been healed successfully!");
			return true;
		}

		$pet->fullHeal();
		$pet->getLevel()->addParticle(new HeartParticle($pet->add(0, 2), 4));
		$sender->sendMessage(TF::GREEN . "The pet " . $pet->getPetName() . TF::RESET . TF::GREEN . " has been healed successfully!");
		return true;
	}
}
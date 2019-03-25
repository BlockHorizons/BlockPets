<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\commands;

use BlockHorizons\BlockPets\commands\utils\BlockPetsCommandException;
use BlockHorizons\BlockPets\pets\BasePet;
use BlockHorizons\BlockPets\sessions\PlayerSession;
use BlockHorizons\BlockPets\sessions\PlayerSessionUtils;
use pocketmine\command\CommandSender;
use pocketmine\Player;

abstract class SessionDependentCommand extends BaseCommand {

	/**
	 * Returns whether this command requires database connection
	 * to exist. If this command requires database connection and
	 * the plugin is configured to not use a database, this
	 * command will be disabled.
	 *
	 * @return bool
	 */
	public function requiresDatabaseConnection(): bool {
		return false;
	}

	public function testCommandSenderValidity(CommandSender $sender): void {
		if($sender instanceof Player && PlayerSession::get($sender) === null) {
			throw new BlockPetsCommandException($this->getLoader()->translate("commands.errors.database.player-not-loaded"));
		}
	}

	protected function getOnlinePlayerSession(string $playerName, &$player = null): PlayerSession {
		$player = $this->loader->getServer()->getPlayer($playerName);
		if($player === null) {
			throw new BlockPetsCommandException($this->getLoader()->translate("commands.errors.player.not-found"));
		}

		return $this->getPlayerSession($player);
	}

	protected function getPlayerSession(Player $player): PlayerSession {
		$session = PlayerSession::get($player);
		if($session === null) {
			throw new BlockPetsCommandException($this->getLoader()->translate("commands.errors.database.player-not-loaded-other", [$player->getName()]));
		}

		return $session;
	}

	protected function getPetByName(string $petName, CommandSender $sender, &$session = null): BasePet {
		if($sender instanceof Player) {
			$pet = ($session = $this->getPlayerSession($sender))->getPetByName($petName);
			if($pet !== null) {
				return $pet;
			}
		}

		$pet = PlayerSessionUtils::getPetByName($petName);
		if($pet === null) {
			throw new BlockPetsCommandException($this->getLoader()->translate("commands.errors.player.no-pet"));
		}

		return $pet;
	}

	protected function getPlayerPet(string $ownerName, string $petName, &$owner = null, &$session = null): BasePet {
		$pet = ($session = $this->getOnlinePlayerSession($ownerName, $owner))->getPetByName($petName);
		if($pet === null) {
			throw new BlockPetsCommandException($this->getLoader()->translate("commands.errors.player.no-pet-other"));
		}

		return $pet;
	}
}

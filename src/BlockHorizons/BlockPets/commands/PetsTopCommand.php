<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\commands;

use BlockHorizons\BlockPets\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class PetsTopCommand extends BaseCommand {

	const ENTRIES_PER_PAGE = 10;//No. of pets to list per page.

	public function __construct(Loader $loader) {
		parent::__construct($loader, "petstop", "Lists the pets leaderboard", "/petstop [page=1]", ["petsleaderboard", "toppets"]);
		$this->setPermission("blockpets.command.petstop");
	}

	public function onCommand(CommandSender $sender, string $commandLabel, array $args): bool {
		$page = isset($args[0]) ? max(1, (int) $args[0]) : 1;

		$this->getLoader()->getDatabase()->getPetsLeaderboard(
			($page - 1) * self::ENTRIES_PER_PAGE,
			self::ENTRIES_PER_PAGE,
			function(array $rows) use($sender, $page, $commandLabel): void {
				$pets = "";
				$index = PetsTopCommand::ENTRIES_PER_PAGE * ($page - 1);

				foreach($rows as [
					"Player" => $player,
					"PetName" => $petName,
					"EntityName" => $entityName,
					"PetLevel" => $petLevel,
					"LevelPoints" => $levelPoints
				]) {
					$pets .= TextFormat::YELLOW . ++$index . ". " . TextFormat::AQUA . $player . "'s Pet " . $entityName . ", " . TextFormat::YELLOW . $petName;
					$pets .= TextFormat::GRAY . "(" . "Lvl " . TextFormat::AQUA . $petLevel . TextFormat::GRAY . ", " . TextFormat::AQUA . $levelPoints . TextFormat::GRAY . " xp)" . TextFormat::EOL;
				}

				if($pets === "") {
					if($page === 1) {
						$sender->sendMessage(TextFormat::RED . $loader->translate("commands.errors.pets.none-on-server"));
					} else {
						$this->onCommand($sender, $commandLabel, [1]);//send first page.
					}
				} else {
					$message = TextFormat::GREEN . "--- Pets Leaderboard " . TextFormat::YELLOW . "#" . $page . TextFormat::GREEN . " ---" . TextFormat::EOL;
					$message .= $pets;
					$sender->sendMessage($message);
				}
			}
		);
		return true;
	}
}

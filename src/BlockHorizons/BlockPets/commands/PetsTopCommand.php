<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\commands;

use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\PetFactory;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class PetsTopCommand extends BaseCommand {

	public const ENTRIES_PER_PAGE = 10;//No. of pets to list per page.

	public function __construct(Loader $loader) {
		parent::__construct($loader, "petstop", "Lists the pets leaderboard", "/petstop [EntityName=ALL] [page=1]", ["petsleaderboard", "toppets"]);
		$this->setPermission("blockpets.command.petstop");
	}

	public function onCommand(CommandSender $sender, string $commandLabel, array $args): bool {
		$loader = $this->getLoader();

		if(isset($args[1]) || (isset($args[0]) && !is_numeric($args[0]))) {
			$entityName = $args[0];
			$page = max(1, (int) ($args[1] ?? 1));
		} else {
			$page = max(1, (int) ($args[0] ?? 1));
			$entityName = null;
		}

		if($entityName !== null) {
			$entityName = PetFactory::getKnownPetId($entityName);
			if($entityName === null) {
				$sender->sendMessage($loader->translate("commands.errors.pet.doesnt-exist"));
				return true;
			}
		}

		$this->sendPage($sender, $page, $entityName);
		return true;
	}

	public function sendPage(CommandSender $sender, int $page = 1, ?string $entityName = null): void {
		$loader->getDatabase()->getPetsLeaderboard(
			($page - 1) * self::ENTRIES_PER_PAGE,
			self::ENTRIES_PER_PAGE,
			$entityName,
			function(array $rows) use($sender, $page, $entityName): void {
				$pets = "";
				$index = PetsTopCommand::ENTRIES_PER_PAGE * ($page - 1);

				foreach($rows as [
					"Player" => $player,
					"PetName" => $petName,
					"EntityName" => $entityName,
					"PetLevel" => $petLevel,
					"LevelPoints" => $levelPoints
				]) {
					$pets .= TextFormat::YELLOW . ++$index . ". " . TextFormat::AQUA . $player . "'s Pet " . PetFactory::getReadableName($entityName) . ", " . TextFormat::YELLOW . $petName;
					$pets .= TextFormat::GRAY . "(" . "Lvl " . TextFormat::AQUA . $petLevel . TextFormat::GRAY . ", " . TextFormat::AQUA . $levelPoints . TextFormat::GRAY . " xp)" . TextFormat::EOL;
				}

				if($pets === "") {
					if($page === 1) {
						if($entityName === null) {
							$sender->sendMessage(TextFormat::RED . $loader->translate("commands.errors.pets.none-on-server"));
						} else {
							$sender->sendMessage(TextFormat::RED . $loader->translate("commands.errors.pets.none-on-server-type", [$entityName]));
						}
					} else {
						$this->sendPage($sender, 1, $entityName);//send first page.
					}
				} else {
					$message = TextFormat::GREEN . "--- Pets Leaderboard " . TextFormat::YELLOW . "#" . $page . TextFormat::GREEN . " ---" . TextFormat::EOL;
					$message .= $pets;
					$sender->sendMessage($message);
				}
			}
		);
	}
}

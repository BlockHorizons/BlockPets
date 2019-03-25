<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\commands;

use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\datastorage\types\MinimalPetData;
use BlockHorizons\BlockPets\pets\datastorage\types\PetsLeaderboardData;
use BlockHorizons\BlockPets\pets\PetFactory;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class PetsTopCommand extends SessionDependentCommand {

	public const ENTRIES_PER_PAGE = 10;//No. of pets to list per page.

	public function __construct(Loader $loader) {
		parent::__construct($loader, "petstop", "Lists the pets leaderboard", "/petstop [EntityName=ALL] [page=1]", ["petsleaderboard", "toppets"]);
		$this->setPermission("blockpets.command.petstop");
	}

	public function requiresDatabaseConnection(): bool {
		return true;
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

	public function sendPage(CommandSender $sender, int $page = 1, ?string $type = null): void {
		$loader = $this->getLoader();
		$loader->getDatabase()->getPetsLeaderboard(
			($page - 1) * self::ENTRIES_PER_PAGE,
			self::ENTRIES_PER_PAGE,
			$type,
			function(PetsLeaderboardData $data) use($type): void {
				$pets = "";
				$index = PetsTopCommand::ENTRIES_PER_PAGE * ($page - 1);

				foreach($data->getPets() as $pet) {
					$pets .= TextFormat::YELLOW . ++$index . ". " . TextFormat::AQUA . $pet->getOwner() . "'s Pet " . PetFactory::getReadableName($pet->getType()) . ", " . TextFormat::YELLOW . $pet->getName();
					$pets .= TextFormat::GRAY . "(" . "Lvl " . TextFormat::AQUA . $pet->getLevel() . TextFormat::GRAY . ", " . TextFormat::AQUA . $pet->getLevelPoints() . TextFormat::GRAY . " xp)" . TextFormat::EOL;
				}

				if($pets === "") {
					if($page === 1) {
						if($type === null) {
							$sender->sendMessage(TextFormat::RED . $loader->translate("commands.errors.pets.none-on-server"));
						} else {
							$sender->sendMessage(TextFormat::RED . $loader->translate("commands.errors.pets.none-on-server-type", [$type]));
						}
					} else {
						$this->sendPage($sender, 1, $type);//send first page.
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

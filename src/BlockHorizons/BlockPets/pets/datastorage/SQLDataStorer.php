<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\datastorage;

use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\BasePet;

use pocketmine\item\Item;
use pocketmine\nbt\BigEndianNBTStream;
use pocketmine\nbt\tag\ListTag;

use poggit\libasynql\libasynql;

class SQLDataStorer extends BaseDataStorer {

	const INITIALIZE_TABLES = "blockpets.init";
	const LOAD_PLAYER_PETS = "blockpets.loadplayer";
	const LIST_PLAYER_PETS = "blockpets.listpets";
	const RESET = "blockpets.reset";

	const REGISTER_PET = "blockpets.pet.register";
	const UNREGISTER_PET = "blockpets.pet.unregister";
	const PET_LEADERBOARD = "blockpets.pet.leaderboard";
	const PET_VISIBILITY = "blockpets.pet.visibility.select";
	const UPDATE_PET_CHESTED = "blockpets.pet.update.chested";
	const UPDATE_PET_EXPERIENCE = "blockpets.pet.update.exp";
	const UPDATE_PET_INVENTORY = "blockpets.pet.update.inv";
	const UPDATE_PET_VISIBILITY = "blockpets.pet.visibility.toggle";

	const VERSION_PATCH = "version.{VERSION}";

	/** @var libasynql */
	protected $database;
	/** @var string */
	protected $type;

	public function registerPet($pet): void {
		$this->database->executeChange(SQLDataStorer::REGISTER_PET, [
			"player" => $pet->getPetOwnerName(),
			"petname" => $pet->getPetName(),
			"entityname" => $pet->getEntityType() . "." . $pet->getPetCustomType(),
			"petsize" => $pet->getScale(),
			"isbaby" => (int) $pet->isBaby(),
			"chested" => (int) $pet->isChested(),
			"petlevel" => $pet->getPetLevel(),
			"levelpoints" => $pet->getPetLevelPoints()
		]);
	}

	public function unregisterPet($pet): void {
		$this->database->executeChange(SQLDataStorer::UNREGISTER_PET, [
			"player" => $pet->getPetOwnerName(),
			"petname" => $pet->getPetName()
		]);
	}

	public function load(string $player, callable $callable): void {
		$this->database->executeSelect(SQLDataStorer::LOAD_PLAYER_PETS, [
			"player" => $player
		], $callable);
	}

	public function getPlayerPets(string $player, ?string $entityName = null, callable $callable): void {
		$this->database->executeSelect(SQLDataStorer::LIST_PLAYER_PETS, [
			"player" => $player,
			"entityname" => $entityName ?? "%"
		], $callable);
	}

	public function getPetsLeaderboard(int $offset = 0, int $length = 1, ?string $entityName = null, callable $callable): void {
		$this->database->executeSelect(SQLDataStorer::PET_LEADERBOARD, [
			"offset" => $offset,
			"length" => $length,
			"entityname" => $entityName ?? "%"
		], $callable);
	}

	public function togglePets(string $ownerName, ?string $petName, callable $callable): void {
		$database = $this->database;

		$this->database->executeChange(SQLDataStorer::UPDATE_PET_VISIBILITY, [
			"player" => $ownerName,
			"petname" => $petName ?? "%"
		], function(int $changed) use($ownerName, $petName, $database, $callable): void {
			if($changed === 0) {
				$callable([]);
			} else {
				$database->executeSelect(SQLDataStorer::PET_VISIBILITY, [
					"player" => $ownerName,
					"petname" => $petName ?? "%"
				], $callable);
			}
		});
	}

	public function updateExperience($pet): void {
		$this->database->executeChange(SQLDataStorer::UPDATE_PET_EXPERIENCE, [
			"petlevel" => $pet->getPetLevel(),
			"levelpoints" => $pet->getPetLevelPoints(),
			"player" => $pet->getPetOwnerName(),
			"petname" => $pet->getPetName()
		]);
	}

	public function updateChested($pet): void {
		$this->database->executeChange(SQLDataStorer::UPDATE_PET_CHESTED, [
			"chested" => (int) $pet->isChested(),
			"player" => $pet->getPetOwnerName(),
			"petname" => $pet->getPetName()
		]);
	}

	public function updateInventory($pet): void {
		$this->database->executeChange(SQLDataStorer::UPDATE_PET_INVENTORY, [
			"inventory" => $pet->getInventoryManager()->compressContents(),
			"player" => $pet->getPetOwnerName(),
			"petname" => $pet->getPetName()
		]);
	}

	protected function prepare(): void {
		$loader = $this->getLoader();

		$config = $loader->getBlockPetsConfig();
		$this->type = strtolower($config->getDatabase());
		$mc = $config->getMySQLInfo();

		$libasynql_friendly_config = [
			"type" => $this->type,
			"sqlite" => [
				"file" => $loader->getDataFolder() . "pets.sqlite3"
			],
			"mysql" => array_combine(
				["host", "username", "password", "schema", "port"],
				[$mc["Host"], $mc["User"], $mc["Password"], $mc["Database"], $mc["Port"]]
			)
		];

		$this->database = libasynql::create($loader, $libasynql_friendly_config, [
			"mysql" => "database_stmts/mysql.sql",
			"sqlite" => "database_stmts/sqlite.sql"
		]);

		$this->database->executeGeneric(SQLDataStorer::INITIALIZE_TABLES);

		$resource = $this->getLoader()->getResource("patches/" . $this->type . ".sql");
		if($resource !== null) {
			$this->database->loadQueryFile($resource);//calls fclose($resource)
		}
	}

	public function patch(string $version): void {
		switch($version) {
			case "1.1.2":
				$this->database->executeGeneric(str_replace("{VERSION}", $version, SQLDataStorer::VERSION_PATCH));
				break;
		}
	}

	protected function close(): void {
		$this->database->close();
	}

	protected function reset(): void {
		$this->database->executeChange(SQLDataStorer::RESET);
	}
}
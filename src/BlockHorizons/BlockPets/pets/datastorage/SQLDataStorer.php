<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\datastorage;

use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\datastorage\types\PetData;
use BlockHorizons\BlockPets\pets\datastorage\types\PetOwnerData;

use pocketmine\item\Item;
use pocketmine\nbt\BigEndianNBTStream;
use pocketmine\nbt\tag\ListTag;

use poggit\libasynql\libasynql;

abstract class SQLDataStorer extends BaseDataStorer {

	protected const INITIALIZE_TABLES = "blockpets.init";
	protected const LOAD_PLAYER_PETS = "blockpets.loadplayer";
	protected const LIST_PLAYER_PETS = "blockpets.listpets";
	protected const RESET = "blockpets.reset";

	protected const REGISTER_PET = "blockpets.pet.register";
	protected const UNREGISTER_PET = "blockpets.pet.unregister";
	protected const PET_LEADERBOARD = "blockpets.pet.leaderboard";
	protected const PET_VISIBILITY = "blockpets.pet.visibility.select";
	protected const UPDATE_PET_VISIBILITY = "blockpets.pet.visibility.toggle";
	protected const UPDATE_PET_NAME = "blockpets.pet.update.name";

	protected const VERSION_PATCH = "version.{VERSION}";

	/** @var libasynql */
	protected $database;
	/** @var string */
	protected $type;

	public function registerPet(PetData $data): void {
		$this->database->executeChange(SQLDataStorer::REGISTER_PET, [
			"player" => $data->getOwner(),
			"petname" => $data->getName(),
			"entityname" => $data->getType(),
			"petsize" => $data->scale,
			"isbaby" => (int) $data->is_baby,
			"chested" => (int) $data->is_chested,
			"petlevel" => $data->level,
			"levelpoints" => $data->level_points,
			"visible" => $data->is_visible,
			"inventory" => $data->inventory_manager->write()
		]);
	}

	public function unregisterPet(string $ownerName, string $petName): void {
		$this->database->executeChange(SQLDataStorer::UNREGISTER_PET, [
			"player" => $ownerName,
			"petname" => $petName
		]);
	}

	public function load(string $owner, callable $callable): void {
		$this->database->executeSelect(SQLDataStorer::LOAD_PLAYER_PETS, [
			"player" => $owner
		], function(array $entries) use($owner, $callable): void {
			$player_data = new PetOwnerData($owner);

			foreach($entries as $entry) {
				$pet_data = new PetData($entry["PetName"], $entry["EntityName"], $owner);
				$pet_data->size = $entry["PetSize"];
				$pet_data->is_baby = (bool) $entry["IsBaby"];
				$pet_data->level = $entry["PetLevel"];
				$pet_data->level_points = $entry["LevelPoints"];
				$pet_data->is_chested = (bool) $entry["Chested"];
				$pet_data->is_visible = (bool) $entry["Visible"];
				$pet_data->inventory_manager->read($entry["Inventory"]);
				$player_data->addPet($pet_data);
			}

			$callable($player_data);
		});
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

	public function updatePetName(string $owner, string $oldName, string $newName): void {
		$this->database->executeChange(SQLDataStorer::UPDATE_PET_NAME, [
			"player" => $owner,
			"oldname" => $oldName,
			"newname" => $newName
		]);
	}

	public function updatePet(PetData $data): void {
		$this->registerPet($data);
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
			case "2.0.0":
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
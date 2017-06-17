<?php

namespace BlockHorizons\BlockPets\pets\datastorage;

use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\BasePet;

class SQLiteDataStorer extends BaseDataStorer {

	/** @var \SQLite3 */
	private $database;

	public function __construct(Loader $loader) {
		parent::__construct($loader);
	}

	public function registerPet(BasePet $pet): bool {
		$petName = $pet->getPetName();
		$playerName = $pet->getPetOwnerName();
		$entityName = $pet->getEntityType();
		$size = $pet->getScale();
		$baby = (int) $pet->namedtag["IsBaby"];
		$level = $pet->getPetLevel();
		$points = $pet->getPetLevelPoints();

		if($this->petExists($petName, $playerName)) {
			$this->unregisterPet($petName, $playerName);
		}
		$query = "INSERT INTO Pets(Player, PetName, EntityName, PetSize, IsBaby, PetLevel, LevelPoints) VALUES ('" . $this->escape($playerName) . "', '" . $this->escape($petName) . "', '" . $this->escape($entityName) . "', $size, $baby, $level, $points)";
		return $this->database->exec($query);
	}

	public function petExists(string $petName, string $ownerName): bool {
		$query = "SELECT * FROM Pets WHERE PetName = '" . $this->escape($petName) . "' AND Player = '" . $this->escape($ownerName) . "'";
		return !empty($this->database->query($query)->fetchArray(SQLITE3_ASSOC));
	}

	private function escape(string $string): string {
		return \SQLite3::escapeString($string);
	}

	public function unregisterPet(string $petName, string $ownerName): bool {
		if(!$this->petExists($petName, $ownerName)) {
			return false;
		}
		$query = "DELETE FROM Pets WHERE Player = '" . $this->escape($ownerName) . "' AND PetName = '" . $this->escape($petName) . "'";
		return $this->database->exec($query);
	}

	public function updatePetExperience(string $petName, string $ownerName, int $petLevel, int $levelPoints): bool {
		if(!$this->petExists($petName, $ownerName)) {
			return false;
		}
		$query = "
		UPDATE Pets SET
		PetLevel = $petLevel,
		LevelPoints = $levelPoints
		WHERE Player = '" . $this->escape($ownerName) . "' AND PetName = '" . $this->escape($petName) . "'";
		return $this->database->exec($query);
	}

	public function fetchAllPetData(string $ownerName): array {
		$data = [];
		$query = "SELECT * FROM Pets WHERE Player = '" . $this->escape($ownerName) . "'";
		$continue = true;
		$return = $this->database->query($query);
		while($continue) {
			$petData = $return->fetchArray(SQLITE3_ASSOC);
			if(!empty($petData["Player"])) {
				$data[] = $petData;
			} else {
				$continue = false;
			}
		}
		return $data;
	}

	protected function prepare(): bool {
		if(!file_exists($path = $this->getLoader()->getDataFolder() . "pets.sqlite3")) {
			file_put_contents($path, "");
		}
		$this->database = new \SQLite3($path);
		$query = "CREATE TABLE IF NOT EXISTS Pets(
			Player VARCHAR(16),
			PetName VARCHAR(48),
			EntityName VARCHAR(32),
			PetSize FLOAT,
			IsBaby INT,
			PetLevel INT,
			LevelPoints INT,
			PRIMARY KEY(Player, PetName)
		)";
		return $this->database->exec($query);
	}

	protected function close(): bool {
		if($this->database instanceof \mysqli) {
			$this->database->close();
			return true;
		}
		return false;
	}

	protected function reset(): bool {
		$query = "DROP TABLE IF EXISTS Pets";
		return $this->database->exec($query);
	}
}
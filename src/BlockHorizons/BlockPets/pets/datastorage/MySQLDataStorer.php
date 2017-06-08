<?php

namespace BlockHorizons\BlockPets\pets\datastorage;

use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\BasePet;

class MySQLDataStorer extends BaseDataStorer {

	/** @var \mysqli */
	private $database;

	public function __construct(Loader $loader) {
		parent::__construct($loader);
	}

	public function registerPet(BasePet $pet): bool {
		$petName = $pet->getPetName();
		$playerName = strtolower($pet->getPetOwnerName());
		$entityName = $pet->getEntityType();
		$size = $pet->getScale();
		$baby = (int) $pet->namedtag["IsBaby"];
		$level = $pet->getPetLevel();
		$points = $pet->getPetLevelPoints();
		if($this->petExists($petName, $playerName)) {
			return false;
		}

		$query = "INSERT INTO Pets(Player, PetName, EntityName, PetSize, IsBaby, PetLevel, LevelPoints) VALUES ('" . $this->escape($playerName) . "', '" . $this->escape($petName) . "', '" . $this->escape($entityName) . "', $size, $baby, $level, $points)";
		return $this->database->query($query);
	}

	public  function petExists(string $petName, string $ownerName): bool {
		$query = "SELECT * FROM Pets WHERE PetName = '" . $this->escape($petName) . "' AND Player = '" . $this->escape($ownerName) . "'";
		return !empty($this->database->query($query)->fetch_assoc());
	}

	private function escape(string $string): string {
		return $this->database->real_escape_string($string);
	}

	public function unregisterPet(string $petName, string $ownerName): bool {
		if(!$this->petExists($petName, $ownerName)) {
			return false;
		}
		$query = "DELETE FROM Pets WHERE Player = '" . $this->escape($ownerName) . "' AND PetName = '" . $this->escape($petName) . "'";
		return $this->database->query($query);
	}

	public function updatePetExperience(string $petName, string $ownerName, int $petLevel, int $levelPoints): bool {
		if(!$this->petExists($petName, $ownerName)) {
			return false;
		}
		$query = "
		UPDATE Pets SET
		PetLevel = $petLevel,
		LevelPoints = $levelPoints
		WHERE Player = '" . $this->escape($ownerName) . "' AND PetName = '" . $this->escape($ownerName) . "'";
		return $this->database->query($query);
	}

	public function fetchAllPetData(string $ownerName): array {
		$data = [];
		$query = "SELECT * FROM Pets WHERE Player = '" . $this->escape($ownerName) . "'";
		$continue = true;
		$return = $this->database->query($query);
		while($continue) {
			$petData = $return->fetch_assoc();
			if(!empty($petData["Player"])) {
				$data[] = $petData;
			} else {
				$continue = false;
			}
		}
		return $data;
	}

	protected function prepare(): bool {
		$s = $this->getLoader()->getBlockPetsConfig()->getMySQLInfo();
		$this->database = new \mysqli($s["Host"], $s["User"], $s["Password"], $s["Database"], $s["Port"]);
		if($this->database->connect_error !== null) {
			throw new \mysqli_sql_exception("No connection could be made to the MySQL database: " . $this->database->connect_error);
		}
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
		return $this->database->query($query);
	}

	protected function close(): bool {
		if($this->database instanceof \mysqli) {
			$this->database->close();
			return true;
		}
		return false;
	}
}
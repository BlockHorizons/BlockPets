<?php

namespace BlockHorizons\BlockPets\pets\datastorage;

use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\BasePet;
use pocketmine\item\Item;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\ListTag;

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
		$chested = (int) $pet->isChested();
		if($this->petExists($petName, $playerName)) {
			$this->unregisterPet($petName, $playerName);
		}

		$query = "INSERT INTO Pets(Player, PetName, EntityName, PetSize, IsBaby, Chested, PetLevel, LevelPoints) VALUES ('" . $this->escape($playerName) . "', '" . $this->escape($petName) . "', '" . $this->escape($entityName) . "', $size, $baby, $chested, $level, $points)";
		return $this->database->query($query);
	}

	public function petExists(string $petName, string $ownerName): bool {
		$ownerName = strtolower($ownerName);
		$query = "SELECT * FROM Pets WHERE PetName = '" . $this->escape($petName) . "' AND Player = '" . $this->escape($ownerName) . "'";
		return !empty($this->database->query($query)->fetch_assoc());
	}

	private function escape(string $string): string {
		return $this->database->real_escape_string($string);
	}

	public function unregisterPet(string $petName, string $ownerName): bool {
		$ownerName = strtolower($ownerName);
		if(!$this->petExists($petName, $ownerName)) {
			return false;
		}
		$query = "DELETE FROM Pets WHERE Player = '" . $this->escape($ownerName) . "' AND PetName = '" . $this->escape($petName) . "'";
		return $this->database->query($query);
	}

	public function updatePetExperience(string $petName, string $ownerName, int $petLevel, int $levelPoints): bool {
		$ownerName = strtolower($ownerName);
		if(!$this->petExists($petName, $ownerName)) {
			return false;
		}
		$query = "
		UPDATE Pets SET
		PetLevel = $petLevel,
		LevelPoints = $levelPoints
		WHERE Player = '" . $this->escape($ownerName) . "' AND PetName = '" . $this->escape($petName) . "'";
		return $this->database->query($query);
	}

	public function updateChested(string $petName, string $ownerName): bool {
		$ownerName = strtolower($ownerName);
		if(!$this->petExists($petName, $ownerName)) {
			return false;
		}
		$query = "UPDATE Pets SET Chested = 1 WHERE Player = '" . $this->escape($ownerName) . "' AND PetName = '" . $this->escape($petName) . "'";
		return $this->database->query($query);
	}

	public function fetchAllPetData(string $ownerName): array {
		$data = [];
		$ownerName = strtolower($ownerName);
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

	public function updateInventory(string $petName, string $ownerName, string $contents): bool {
		$ownerName = strtolower($ownerName);
		if(!$this->petExists($petName, $ownerName)) {
			return false;
		}
		$query = "UPDATE Pets SET Inventory = '" . $this->escape($contents) . "' WHERE PetName = '" . $this->escape($petName) . "' AND Player = '" . $this->escape($ownerName) . "'";
		return $this->database->query($query);
	}

	public function getInventory(string $petName, string $ownerName): string {
		$ownerName = strtolower($ownerName);
		if(!$this->petExists($petName, $ownerName)) {
			return "";
		}
		$query = "SELECT Inventory FROM Pets WHERE Player = '" . $this->escape($ownerName) . "' AND PetName = '" . $this->escape($petName) . "'";
		$return = $this->database->query($query)->fetch_assoc()["Inventory"];
		if(empty($return)) {
			return $return;
		}
		$compressedContents = base64_decode($return);
		$nbt = new NBT(NBT::BIG_ENDIAN);
		$nbt->readCompressed($compressedContents);
		$nbt = $nbt->getData();
		if(!isset($nbt->itemList)) {
			return [];
		}
		/** @var ListTag $items */
		$items = $nbt->ItemList;
		$contents = [];
		if(!empty($items)) {
			$items = $items->getValue();
			foreach($items as $slot => $compoundTag) {
				$contents[$slot] = Item::nbtDeserialize($compoundTag);
			}
		}
		return $contents;
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
			Chested INT,
			PetLevel INT,
			LevelPoints INT,
			Inventory VARCHAR,
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

	protected function reset(): bool {
		$query = "DROP TABLE IF EXISTS Pets";
		return $this->database->query($query);
	}
}
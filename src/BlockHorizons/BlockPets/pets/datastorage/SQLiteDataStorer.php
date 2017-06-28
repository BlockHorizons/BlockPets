<?php

namespace BlockHorizons\BlockPets\pets\datastorage;

use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\BasePet;
use pocketmine\item\Item;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\ListTag;

class SQLiteDataStorer extends BaseDataStorer {

	/** @var \SQLite3 */
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
		return $this->database->exec($query);
	}

	public function petExists(string $petName, string $ownerName): bool {
		$ownerName = strtolower($ownerName);
		$query = "SELECT * FROM Pets WHERE PetName = '" . $this->escape($petName) . "' AND Player = '" . $this->escape($ownerName) . "'";
		return !empty($this->database->query($query)->fetchArray(SQLITE3_ASSOC));
	}

	private function escape(string $string): string {
		return \SQLite3::escapeString($string);
	}

	public function unregisterPet(string $petName, string $ownerName): bool {
		$ownerName = strtolower($ownerName);
		if(!$this->petExists($petName, $ownerName)) {
			return false;
		}
		$query = "DELETE FROM Pets WHERE Player = '" . $this->escape($ownerName) . "' AND PetName = '" . $this->escape($petName) . "'";
		return $this->database->exec($query);
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
		return $this->database->exec($query);
	}

	public function updateChested(string $petName, string $ownerName): bool {
		$ownerName = strtolower($ownerName);
		if(!$this->petExists($petName, $ownerName)) {
			return false;
		}
		$chested = (int) $this->loader->getPetByName($petName, $this->loader->getServer()->getPlayer($ownerName))->isChested();
		$query = "UPDATE Pets SET Chested = $chested WHERE Player = '" . $this->escape($ownerName) . "' AND PetName = '" . $this->escape($petName) . "'";
		return $this->database->exec($query);
	}

	public function fetchAllPetData(string $ownerName): array {
		$data = [];
		$ownerName = strtolower($ownerName);
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

	public function updateInventory(string $petName, string $ownerName, string $contents): bool {
		$ownerName = strtolower($ownerName);
		if(!$this->petExists($petName, $ownerName)) {
			return false;
		}
		$query = "UPDATE Pets SET Inventory = '" . $this->escape($contents) . "' WHERE PetName = '" . $this->escape($petName) . "' AND Player = '" . $this->escape($ownerName) . "'";
		return $this->database->exec($query);
	}

	public function getInventory(string $petName, string $ownerName): array {
		$ownerName = strtolower($ownerName);
		if(!$this->petExists($petName, $ownerName)) {
			return [];
		}
		$query = "SELECT Inventory FROM Pets WHERE Player = '" . $this->escape($ownerName) . "' AND PetName = '" . $this->escape($petName) . "'";
		$return = $this->database->query($query)->fetchArray(SQLITE3_ASSOC)["Inventory"];
		if(empty($return)) {
			return [];
		}
		$compressedContents = base64_decode($return);

		$nbt = new NBT(NBT::BIG_ENDIAN);
		$nbt->readCompressed($compressedContents);
		$nbt = $nbt->getData();
		/** @var ListTag $items */
		$items = $nbt->ItemList ?? [];
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
			Chested INT,
			PetLevel INT,
			LevelPoints INT,
			Inventory VARCHAR,
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
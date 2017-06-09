<?php

namespace BlockHorizons\BlockPets;

use BlockHorizons\BlockPets\commands\CommandOverloads;
use BlockHorizons\BlockPets\commands\LevelUpPetCommand;
use BlockHorizons\BlockPets\commands\RemovePetCommand;
use BlockHorizons\BlockPets\commands\SpawnPetCommand;
use BlockHorizons\BlockPets\configurable\BlockPetsConfig;
use BlockHorizons\BlockPets\listeners\EventListener;
use BlockHorizons\BlockPets\listeners\RidingListener;
use BlockHorizons\BlockPets\pets\BasePet;
use BlockHorizons\BlockPets\pets\creatures\BatPet;
use BlockHorizons\BlockPets\pets\creatures\BlazePet;
use BlockHorizons\BlockPets\pets\creatures\CaveSpiderPet;
use BlockHorizons\BlockPets\pets\creatures\ChickenPet;
use BlockHorizons\BlockPets\pets\creatures\CowPet;
use BlockHorizons\BlockPets\pets\creatures\CreeperPet;
use BlockHorizons\BlockPets\pets\creatures\EnderCrystalPet;
use BlockHorizons\BlockPets\pets\creatures\EnderDragonPet;
use BlockHorizons\BlockPets\pets\creatures\EndermanPet;
use BlockHorizons\BlockPets\pets\creatures\EndermitePet;
use BlockHorizons\BlockPets\pets\creatures\GhastPet;
use BlockHorizons\BlockPets\pets\creatures\HorsePet;
use BlockHorizons\BlockPets\pets\creatures\HuskPet;
use BlockHorizons\BlockPets\pets\creatures\IronGolemPet;
use BlockHorizons\BlockPets\pets\creatures\LlamaPet;
use BlockHorizons\BlockPets\pets\creatures\MooshroomPet;
use BlockHorizons\BlockPets\pets\creatures\OcelotPet;
use BlockHorizons\BlockPets\pets\creatures\PigPet;
use BlockHorizons\BlockPets\pets\creatures\PolarBearPet;
use BlockHorizons\BlockPets\pets\creatures\SheepPet;
use BlockHorizons\BlockPets\pets\creatures\SilverFishPet;
use BlockHorizons\BlockPets\pets\creatures\SkeletonPet;
use BlockHorizons\BlockPets\pets\creatures\SnowGolemPet;
use BlockHorizons\BlockPets\pets\creatures\SpiderPet;
use BlockHorizons\BlockPets\pets\creatures\StrayPet;
use BlockHorizons\BlockPets\pets\creatures\VillagerPet;
use BlockHorizons\BlockPets\pets\creatures\WitchPet;
use BlockHorizons\BlockPets\pets\creatures\WitherPet;
use BlockHorizons\BlockPets\pets\creatures\WitherSkeletonPet;
use BlockHorizons\BlockPets\pets\creatures\WitherSkullPet;
use BlockHorizons\BlockPets\pets\creatures\WolfPet;
use BlockHorizons\BlockPets\pets\creatures\ZombiePet;
use BlockHorizons\BlockPets\pets\creatures\ZombiePigmanPet;
use BlockHorizons\BlockPets\pets\creatures\ZombieVillagerPet;
use BlockHorizons\BlockPets\pets\datastorage\BaseDataStorer;
use BlockHorizons\BlockPets\pets\datastorage\MySQLDataStorer;
use BlockHorizons\BlockPets\pets\datastorage\SQLiteDataStorer;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class Loader extends PluginBase {

	const PETS = [
		"Ghast",
		"Blaze",
		"Chicken",
		"Bat",
		"EnderDragon",
		"Horse",
		"Ocelot",
		"Skeleton",
		"Wolf",
		"Zombie",
		"EnderCrystal",
		"WitherSkull",
		"CaveSpider",
		"Cow",
		"Creeper",
		"Enderman",
		"Endermite",
		"Husk",
		"IronGolem",
		"Mooshroom",
		"Pig",
		"PolarBear",
		"Sheep",
		"SilverFish",
		"SnowGolem",
		"Spider",
		"Stray",
		"Villager",
		"Witch",
		"Wither",
		"WitherSkeleton",
		"WitherSkull",
		"ZombiePigman",
		"ZombieVillager",
		"Llama"
	];

	const PET_CLASSES = [
		BlazePet::class,
		ChickenPet::class,
		GhastPet::class,
		BatPet::class,
		EnderDragonPet::class,
		HorsePet::class,
		OcelotPet::class,
		SkeletonPet::class,
		WolfPet::class,
		ZombiePet::class,
		EnderCrystalPet::class,
		CaveSpiderPet::class,
		CowPet::class,
		CreeperPet::class,
		EndermanPet::class,
		EndermitePet::class,
		HuskPet::class,
		IronGolemPet::class,
		MooshroomPet::class,
		PigPet::class,
		PolarBearPet::class,
		SheepPet::class,
		SilverFishPet::class,
		SnowGolemPet::class,
		SpiderPet::class,
		StrayPet::class,
		VillagerPet::class,
		WitchPet::class,
		WitherPet::class,
		WitherSkeletonPet::class,
		WitherSkullPet::class,
		ZombiePigmanPet::class,
		ZombieVillagerPet::class,
		LlamaPet::class
	];

	private $bpConfig;
	private $database;

	public function onEnable() {
		CommandOverloads::initialize();
		foreach(self::PET_CLASSES as $petClass) {
			Entity::registerEntity($petClass, true);
		}
		$this->registerCommands();
		$this->registerListeners();

		$this->bpConfig = new BlockPetsConfig($this);
		$this->selectDatabase();
	}

	public function registerCommands() {
		$petCommands = [
			"spawnpet" => new SpawnPetCommand($this),
			"leveluppet" => new LevelUpPetCommand($this),
			"removepet" => new RemovePetCommand($this)
		];
		foreach($petCommands as $fallBack => $command) {
			$this->getServer()->getCommandMap()->register($fallBack, $command);
		}
	}

	public function registerListeners() {
		$listeners = [
			new EventListener($this),
			new RidingListener($this)
		];
		foreach($listeners as $listener) {
			$this->getServer()->getPluginManager()->registerEvents($listener, $this);
		}
	}

	/**
	 * @return bool
	 */
	private function selectDatabase(): bool {
		if(!$this->getBlockPetsConfig()->storeToDatabase()) {
			return false;
		}
		switch(strtolower($this->getBlockPetsConfig()->getDatabase())) {
			default:
			case "mysql":
				$this->database = new MySQLDataStorer($this);
				break;
			case "sqlite3":
			case "sqlite":
				$this->database = new SQLiteDataStorer($this);
				break;
		}
		return true;
	}

	/**
	 * @return BlockPetsConfig
	 */
	public function getBlockPetsConfig(): BlockPetsConfig {
		return $this->bpConfig;
	}

	/**
	 * @param EntitySpawnEvent $event
	 */
	public function onEntitySpawn(EntitySpawnEvent $event) {
		if($event->getEntity() instanceof BasePet) {
			$clearLaggPlugin = $this->getServer()->getPluginManager()->getPlugin("ClearLagg");
			if($clearLaggPlugin !== null) {
				$clearLaggPlugin->exemptEntity($event->getEntity());
			}
		}
	}

	/**
	 * Checks if a pet type of that name exists.
	 *
	 * @param string $entityName
	 *
	 * @return bool
	 */
	public function petExists(string $entityName) {
		foreach(self::PETS as $pet) {
			if(strtolower($pet) === strtolower($entityName)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Tries to match a pet type with the pet type list, and returns the fully qualified name if this could be found. Null if no valid result was found.
	 *
	 * @param string $entityName
	 *
	 * @return string|null
	 */
	public function getPet(string $entityName): string {
		foreach(self::PETS as $pet) {
			if(strtolower($pet) === strtolower($entityName)) {
				return $pet;
			}
		}
		return null;
	}

	/**
	 * Creates a new pet to the given player.
	 *
	 * @param string $entityName
	 * @param Player $position
	 * @param string $name
	 * @param float  $scale
	 * @param bool   $isBaby
	 * @param int    $level
	 * @param int    $levelPoints
	 *
	 * @return null|BasePet
	 */
	public function createPet(string $entityName, Player $position, string $name, float $scale = 1.0, bool $isBaby = false, int $level = 1, int $levelPoints = 0) {
		$nbt = new CompoundTag("", [
			"Pos" => new ListTag("Pos", [
				new DoubleTag("", $position->x),
				new DoubleTag("", $position->y),
				new DoubleTag("", $position->z)
			]),
			"Motion" => new ListTag("Motion", [
				new DoubleTag("", 0),
				new DoubleTag("", 0),
				new DoubleTag("", 0)
			]),
			"Rotation" => new ListTag("Rotation", [
				new FloatTag("", $position->yaw),
				new FloatTag("", $position->pitch)
			]),
			"petOwner" => new StringTag("petOwner", $position->getName()),
			"scale" => new FloatTag("scale", $scale),
			"petName" => new StringTag("petName", $name),
			"petLevel" => new IntTag("petLevel", $level),
			"petLevelPoints" => new IntTag("petLevelPoints", $levelPoints),
			"isBaby" => new ByteTag("isBaby", (int) $isBaby)
		]);

		$entity = Entity::createEntity($entityName . "Pet", $position->getLevel(), $nbt);
		if($entity instanceof BasePet) {
			return $entity;
		}
		return null;
	}

	/**
	 * Removes the first pet found with the given name, or the pet with the given name if playerName has been specified.
	 *
	 * @param string $name
	 * @param Player $player
	 *
	 * @return bool
	 */
	public function removePet(string $name, Player $player = null): bool {
		$pet = $this->getPetByName($name, $player);
		if($pet === null) {
			return false;
		}
		if(!empty($playerName)) {
			foreach($this->getPetsFrom($player) as $pet) {
				if(strpos(strtolower($pet->getPetName()), strtolower($name)) !== false) {
					$pet->close();
					return true;
				}
			}
			return false;
		}
		$pet->close();
		$this->getDatabase()->unregisterPet($pet->getPetName(), $pet->getPetOwnerName());
		return true;
	}

	/**
	 * Returns the first pet found with the given name.
	 *
	 * @param string $name
	 * @param Player $player
	 *
	 * @return BasePet|null
	 */
	public function getPetByName(string $name, Player $player = null) {
		if($player !== null) {
			foreach($this->getPetsFrom($player) as $pet) {
				if(strpos(strtolower($pet->getPetName()), strtolower($name)) !== false) {
					return $pet;
				}
			}
			return null;
		}
		foreach($this->getServer()->getLevels() as $level) {
			foreach($level->getEntities() as $entity) {
				if(!$entity instanceof BasePet) {
					continue;
				}
				if(strpos(strtolower($entity->getPetName()), strtolower($name)) !== false) {
					return $entity;
				}
			}
		}
		return null;
	}

	/**
	 * Gets all currently available pets from the given player.
	 *
	 * @param Player $player
	 *
	 * @return BasePet[]
	 */
	public function getPetsFrom(Player $player): array {
		$playerPets = [];
		foreach($player->getLevel()->getEntities() as $entity) {
			if($entity instanceof BasePet) {
				if($entity->getPetOwner() === null) {
					continue;
				}
				if($entity->getPetOwner()->getName() === $player->getName()) {
					$playerPets[] = $entity;
				}
			}
		}
		return $playerPets;
	}

	/**
	 * Returns the database to store and fetch data from.
	 *
	 * @return BaseDataStorer
	 */
	public function getDatabase(): BaseDataStorer {
		if($this->database === null) {
			throw new \RuntimeException("Attempted to retrieve the database while database storing was disabled.");
		}
		return $this->database;
	}

	/**
	 * Gets the pet the given player is currently riding.
	 *
	 * @param Player $player
	 *
	 * @return BasePet
	 */
	public function getRiddenPet(Player $player): BasePet {
		foreach($this->getPetsFrom($player) as $pet) {
			if($pet->isRidden()) {
				return $pet;
			}
		}
		return null;
	}

	/**
	 * Checks if the given player is currently riding a pet.
	 *
	 * @param Player $player
	 *
	 * @return bool
	 */
	public function isRidingAPet(Player $player): bool {
		foreach($this->getPetsFrom($player) as $pet) {
			if($pet->isRidden()) {
				return true;
			}
		}
		return false;
	}
}
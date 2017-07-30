<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets;

use BlockHorizons\BlockPets\commands\BaseCommand;
use BlockHorizons\BlockPets\commands\ChangePetNameCommand;
use BlockHorizons\BlockPets\commands\ClearPetCommand;
use BlockHorizons\BlockPets\commands\CommandOverloads;
use BlockHorizons\BlockPets\commands\HealPetCommand;
use BlockHorizons\BlockPets\commands\LevelUpPetCommand;
use BlockHorizons\BlockPets\commands\PetCommand;
use BlockHorizons\BlockPets\commands\RemovePetCommand;
use BlockHorizons\BlockPets\commands\SpawnPetCommand;
use BlockHorizons\BlockPets\commands\TogglePetCommand;
use BlockHorizons\BlockPets\configurable\BlockPetsConfig;
use BlockHorizons\BlockPets\configurable\LanguageConfig;
use BlockHorizons\BlockPets\configurable\PetProperties;
use BlockHorizons\BlockPets\events\PetRemoveEvent;
use BlockHorizons\BlockPets\events\PetSpawnEvent;
use BlockHorizons\BlockPets\listeners\EventListener;
use BlockHorizons\BlockPets\listeners\RidingListener;
use BlockHorizons\BlockPets\pets\BasePet;
use BlockHorizons\BlockPets\pets\creatures\ArrowPet;
use BlockHorizons\BlockPets\pets\creatures\BatPet;
use BlockHorizons\BlockPets\pets\creatures\BlazePet;
use BlockHorizons\BlockPets\pets\creatures\CaveSpiderPet;
use BlockHorizons\BlockPets\pets\creatures\ChickenPet;
use BlockHorizons\BlockPets\pets\creatures\CowPet;
use BlockHorizons\BlockPets\pets\creatures\CreeperPet;
use BlockHorizons\BlockPets\pets\creatures\DonkeyPet;
use BlockHorizons\BlockPets\pets\creatures\ElderGuardianPet;
use BlockHorizons\BlockPets\pets\creatures\EnderCrystalPet;
use BlockHorizons\BlockPets\pets\creatures\EnderDragonPet;
use BlockHorizons\BlockPets\pets\creatures\EndermanPet;
use BlockHorizons\BlockPets\pets\creatures\EndermitePet;
use BlockHorizons\BlockPets\pets\creatures\EvokerPet;
use BlockHorizons\BlockPets\pets\creatures\GhastPet;
use BlockHorizons\BlockPets\pets\creatures\GuardianPet;
use BlockHorizons\BlockPets\pets\creatures\HorsePet;
use BlockHorizons\BlockPets\pets\creatures\HuskPet;
use BlockHorizons\BlockPets\pets\creatures\IronGolemPet;
use BlockHorizons\BlockPets\pets\creatures\LlamaPet;
use BlockHorizons\BlockPets\pets\creatures\MagmaCubePet;
use BlockHorizons\BlockPets\pets\creatures\MooshroomPet;
use BlockHorizons\BlockPets\pets\creatures\MulePet;
use BlockHorizons\BlockPets\pets\creatures\OcelotPet;
use BlockHorizons\BlockPets\pets\creatures\PigPet;
use BlockHorizons\BlockPets\pets\creatures\PolarBearPet;
use BlockHorizons\BlockPets\pets\creatures\RabbitPet;
use BlockHorizons\BlockPets\pets\creatures\SheepPet;
use BlockHorizons\BlockPets\pets\creatures\SilverFishPet;
use BlockHorizons\BlockPets\pets\creatures\SkeletonHorsePet;
use BlockHorizons\BlockPets\pets\creatures\SkeletonPet;
use BlockHorizons\BlockPets\pets\creatures\SlimePet;
use BlockHorizons\BlockPets\pets\creatures\SnowGolemPet;
use BlockHorizons\BlockPets\pets\creatures\SpiderPet;
use BlockHorizons\BlockPets\pets\creatures\SquidPet;
use BlockHorizons\BlockPets\pets\creatures\StrayPet;
use BlockHorizons\BlockPets\pets\creatures\VexPet;
use BlockHorizons\BlockPets\pets\creatures\VillagerPet;
use BlockHorizons\BlockPets\pets\creatures\VindicatorPet;
use BlockHorizons\BlockPets\pets\creatures\WitchPet;
use BlockHorizons\BlockPets\pets\creatures\WitherPet;
use BlockHorizons\BlockPets\pets\creatures\WitherSkeletonPet;
use BlockHorizons\BlockPets\pets\creatures\WitherSkullPet;
use BlockHorizons\BlockPets\pets\creatures\WolfPet;
use BlockHorizons\BlockPets\pets\creatures\ZombieHorsePet;
use BlockHorizons\BlockPets\pets\creatures\ZombiePet;
use BlockHorizons\BlockPets\pets\creatures\ZombiePigmanPet;
use BlockHorizons\BlockPets\pets\creatures\ZombieVillagerPet;
use BlockHorizons\BlockPets\pets\datastorage\BaseDataStorer;
use BlockHorizons\BlockPets\pets\datastorage\MySQLDataStorer;
use BlockHorizons\BlockPets\pets\datastorage\SQLiteDataStorer;
use pocketmine\entity\Entity;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use spoondetector\SpoonDetector;

class Loader extends PluginBase {

	const VERSION = "1.1.0";
	const API_TARGET = "3.0.0-ALPHA7";

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
		"Llama",
		"Rabbit",
		"Slime",
		"MagmaCube",
		"Evoker",
		"Vindicator",
		"Vex",
		"Mule",
		"Donkey",
		"SkeletonHorse",
		"ZombieHorse",
		"Squid",
		"Guardian",
		"ElderGuardian",
		"Arrow"
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
		LlamaPet::class,
		MagmaCubePet::class,
		SlimePet::class,
		RabbitPet::class,
		EvokerPet::class,
		VindicatorPet::class,
		VexPet::class,
		MulePet::class,
		DonkeyPet::class,
		SkeletonHorsePet::class,
		ZombieHorsePet::class,
		SquidPet::class,
		ElderGuardianPet::class,
		GuardianPet::class,
		ArrowPet::class
	];

	private $availableLanguages = [
		"en",
		"nl",
		"vi",
		"gr"
	];

	/** @var array */
	public $selectingName = [];

	/** @var BlockPetsConfig */
	private $bpConfig;
	/** @var PetProperties */
	private $pProperties;
	/** @var LanguageConfig */
	private $language;

	/** @var BaseDataStorer */
	private $database;
	/** @var array */
	private $toggledOff = [];
	/** @var array */
	private $toggledPets = [];

	public function onEnable() {
		if(!is_dir($this->getDataFolder())) {
			mkdir($this->getDataFolder());
		}
		SpoonDetector::printSpoon($this);
		CommandOverloads::initialize();
		foreach(self::PET_CLASSES as $petClass) {
			Entity::registerEntity($petClass, true);
		}
		$this->registerCommands();
		$this->registerListeners();

		$this->bpConfig = new BlockPetsConfig($this);
		$this->pProperties = new PetProperties($this);
		$this->language = new LanguageConfig($this);
		$this->selectDatabase();
	}

	public function registerCommands() {
		/** @var BaseCommand[] $petCommands */
		$petCommands = [
			new SpawnPetCommand($this),
			new LevelUpPetCommand($this),
			new RemovePetCommand($this),
			new HealPetCommand($this),
			new ClearPetCommand($this),
			new TogglePetCommand($this),
			new ChangePetNameCommand($this),
			new PetCommand($this)
		];
		foreach($petCommands as $command) {
			$this->getServer()->getCommandMap()->register($command->getName(), $command);
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
	 * @return string[]
	 */
	public function getAvailableLanguages(): array {
		return $this->availableLanguages;
	}

	/**
	 * @return bool
	 */
	private function selectDatabase(): bool {
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
	 * @param string $key
	 * @param array  $params
	 *
	 * @return string
	 */
	public function translate(string $key, array $params = []) {
		if(!empty($params)) {
			return vsprintf($this->getLanguage()->get($key), $params);
		}
		return $this->getLanguage()->get($key);
	}

	/**
	 * @return LanguageConfig
	 */
	public function getLanguage(): LanguageConfig {
		return $this->language;
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
	public function getPet(string $entityName) {
		foreach(self::PETS as $pet) {
			if(strtolower($pet) === strtolower($entityName)) {
				return $pet;
			}
		}
		return null;
	}

	/**
	 * Creates a new pet for the given player.
	 *
	 * @param string $entityName
	 * @param Player $player
	 * @param string $name
	 * @param float  $scale
	 * @param bool   $isBaby
	 * @param int    $level
	 * @param int    $levelPoints
	 * @param bool   $chested
	 *
	 * @return null|BasePet
	 */
	public function createPet(string $entityName, Player $player, string $name, float $scale = 1.0, bool $isBaby = false, int $level = 1, int $levelPoints = 0, bool $chested = false) {
		foreach($this->getPetsFrom($player) as $pet) {
			if($pet->getPetName() === $name) {
				$this->removePet($pet->getPetName(), $player);
			}
		}
		$nbt = new CompoundTag("", [
			"Pos" => new ListTag("Pos", [
				new DoubleTag("", $player->x),
				new DoubleTag("", $player->y),
				new DoubleTag("", $player->z)
			]),
			"Motion" => new ListTag("Motion", [
				new DoubleTag("", 0),
				new DoubleTag("", 0),
				new DoubleTag("", 0)
			]),
			"Rotation" => new ListTag("Rotation", [
				new FloatTag("", $player->yaw),
				new FloatTag("", $player->pitch)
			]),
			"petOwner" => new StringTag("petOwner", $player->getName()),
			"scale" => new FloatTag("scale", $scale),
			"petName" => new StringTag("petName", $name),
			"petLevel" => new IntTag("petLevel", $level),
			"petLevelPoints" => new IntTag("petLevelPoints", $levelPoints),
			"isBaby" => new ByteTag("isBaby", (int) $isBaby),
			"chested" => new ByteTag("chested", (int) $chested)
		]);

		$entity = Entity::createEntity($entityName . "Pet", $player->getLevel(), $nbt);
		if($entity instanceof BasePet) {
			$this->getServer()->getPluginManager()->callEvent($ev = new PetSpawnEvent($this, $entity));
			if($ev->isCancelled()) {
				$this->removePet($entity->getPetName(), $player);
				return null;
			}
			return $entity;
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
				if($entity->getPetOwner() === null || $entity->closed || !($entity->isAlive())) {
					continue;
				}
				if($entity->getPetOwnerName() === $player->getName()) {
					$playerPets[] = $entity;
				}
			}
		}
		return $playerPets;
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
	 * Removes the first pet found with the given name, or the pet with the given name if playerName has been specified.
	 *
	 * @param string $name
	 * @param Player $player
	 *
	 * @return bool
	 */
	public function removePet(string $name, Player $player = null): bool {
		$foundPet = $this->getPetByName($name);
		if($foundPet === null) {
			return false;
		}
		if($player !== null) {
			foreach($this->getPetsFrom($player) as $pet) {
				if(strpos(strtolower($pet->getPetName()), strtolower($name)) !== false) {
					$this->getServer()->getPluginManager()->callEvent($ev = new PetRemoveEvent($this, $pet));
					if($ev->isCancelled()) {
						return false;
					}
					if($pet->isRidden()) {
						$pet->throwRiderOff();
					}
					$pet->kill(true);
					return true;
				}
			}
			return false;
		}
		$this->getServer()->getPluginManager()->callEvent($ev = new PetRemoveEvent($this, $foundPet));
		if($ev->isCancelled()) {
			return false;
		}
		if($foundPet->isRidden()) {
			$foundPet->throwRiderOff();
		}
		$foundPet->kill(true);
		return true;
	}

	/**
	 * Returns the database to store and fetch data from.
	 *
	 * @return BaseDataStorer
	 */
	public function getDatabase(): BaseDataStorer {
		if($this->database === null) {
			throw new \RuntimeException("Attempted to retrieve the database while database storing was unavailable.");
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

	/**
	 * Toggles pets of the given player on/off.
	 *
	 * @param Player $player
	 *
	 * @return bool
	 */
	public function togglePets(Player $player): bool {
		if($this->arePetsToggledOn($player)) {
			$this->toggledOff[$player->getName()] = true;
			foreach($this->getPetsFrom($player) as $pet) {
				$pet->despawnFromAll();
				$pet->setDormant();
			}
			return false;
		} else {
			unset($this->toggledOff[$player->getName()]);
			foreach($this->getPetsFrom($player) as $pet) {
				$pet->spawnToAll();
				$pet->setDormant(false);
			}
			return true;
		}
	}

	/**
	 * Checks if pets are toggled on.
	 *
	 * @param Player $player
	 *
	 * @return bool
	 */
	public function arePetsToggledOn(Player $player): bool {
		return !isset($this->toggledOff[$player->getName()]);
	}

	/**
	 * Toggles the given pet of the given player on or off.
	 *
	 * @param BasePet $pet
	 * @param Player  $owner
	 *
	 * @return bool
	 */
	public function togglePet(BasePet $pet, Player $owner): bool {
		if(isset($this->toggledPets[$pet->getPetName()])) {
			if($this->toggledPets[$pet->getPetName()] === $owner->getName()) {
				$pet->spawnToAll();
				$pet->setDormant(false);
				unset($this->toggledPets[$pet->getPetName()]);
				return true;
			}
		}
		$pet->despawnFromAll();
		$pet->setDormant();
		$this->toggledPets[$pet->getPetName()] = $owner->getName();
		return false;
	}

	/**
	 * @param BasePet $pet
	 * @param Player  $owner
	 *
	 * @return bool
	 */
	public function isPetToggledOn(BasePet $pet, Player $owner): bool {
		if(isset($this->toggledPets[$pet->getPetName()])) {
			return $this->toggledPets[$pet->getPetName()] === $owner->getName();
		}
		return false;
	}

	/**
	 * @return PetProperties
	 */
	public function getPetProperties(): PetProperties {
		return $this->pProperties;
	}
}

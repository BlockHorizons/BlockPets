<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets;

use BlockHorizons\BlockPets\commands\AddPetPointsCommand;
use BlockHorizons\BlockPets\commands\BaseCommand;
use BlockHorizons\BlockPets\commands\ChangePetNameCommand;
use BlockHorizons\BlockPets\commands\ClearPetCommand;
use BlockHorizons\BlockPets\commands\CommandOverloads;
use BlockHorizons\BlockPets\commands\HealPetCommand;
use BlockHorizons\BlockPets\commands\LevelUpPetCommand;
use BlockHorizons\BlockPets\commands\ListPetsCommand;
use BlockHorizons\BlockPets\commands\PetCommand;
use BlockHorizons\BlockPets\commands\PetsTopCommand;
use BlockHorizons\BlockPets\commands\RemovePetCommand;
use BlockHorizons\BlockPets\commands\SpawnPetCommand;
use BlockHorizons\BlockPets\commands\TogglePetCommand;
use BlockHorizons\BlockPets\configurable\BlockPetsConfig;
use BlockHorizons\BlockPets\configurable\LanguageConfig;
use BlockHorizons\BlockPets\configurable\PetProperties;
use BlockHorizons\BlockPets\events\PetRemoveEvent;
use BlockHorizons\BlockPets\events\PetSpawnEvent;
use BlockHorizons\BlockPets\items\Saddle;
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
use BlockHorizons\BlockPets\pets\inventory\PetInventoryManager;
use pocketmine\entity\Attribute;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\lang\BaseLang;
use spoondetector\SpoonDetector;

class Loader extends PluginBase {

	const PETS = [
		"Arrow",
		"Bat",
		"Blaze",
		"CaveSpider",
		"Chicken",
		"Cow",
		"Creeper",
		"Donkey",
		"ElderGuardian",
		"EnderCrystal",
		"EnderDragon",
		"Enderman",
		"Endermite",
		"Evoker",
		"Ghast",
		"Guardian",
		"Horse",
		"Husk",
		"IronGolem",
		"Llama",
		"MagmaCube",
		"Mooshroom",
		"Mule",
		"Ocelot",
		"Pig",
		"PolarBear",
		"Rabbit",
		"Sheep",
		"SilverFish",
		"Skeleton",
		"SkeletonHorse",
		"Slime",
		"SnowGolem",
		"Spider",
		"Squid",
		"Stray",
		"Vex",
		"Villager",
		"Vindicator",
		"Witch",
		"Wither",
		"WitherSkeleton",
		"WitherSkull",
		"WitherSkull",
		"Wolf",
		"Zombie",
		"ZombieHorse",
		"ZombiePigman",
		"ZombieVillager"
	];

	const PET_CLASSES = [
		ArrowPet::class,
		BatPet::class,
		BlazePet::class,
		CaveSpiderPet::class,
		ChickenPet::class,
		CowPet::class,
		CreeperPet::class,
		DonkeyPet::class,
		ElderGuardianPet::class,
		EnderCrystalPet::class,
		EnderDragonPet::class,
		EndermanPet::class,
		EndermitePet::class,
		EvokerPet::class,
		GhastPet::class,
		GuardianPet::class,
		HorsePet::class,
		HuskPet::class,
		IronGolemPet::class,
		LlamaPet::class,
		MagmaCubePet::class,
		MooshroomPet::class,
		MulePet::class,
		OcelotPet::class,
		PigPet::class,
		PolarBearPet::class,
		RabbitPet::class,
		SheepPet::class,
		SilverFishPet::class,
		SkeletonHorsePet::class,
		SkeletonPet::class,
		SlimePet::class,
		SnowGolemPet::class,
		SpiderPet::class,
		SquidPet::class,
		StrayPet::class,
		VexPet::class,
		VillagerPet::class,
		VindicatorPet::class,
		WitchPet::class,
		WitherPet::class,
		WitherSkeletonPet::class,
		WitherSkullPet::class,
		WolfPet::class,
		ZombieHorsePet::class,
		ZombiePet::class,
		ZombiePigmanPet::class,
		ZombieVillagerPet::class
	];

	private $availableLanguages = [
		"en",
		"nl",
		"vi",
		"gr",
		"ko",
		"de"
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
	/** @var int[][] */
	private $playerPets = [];

	public function onEnable() {
		if(!is_dir($this->getDataFolder())) {
			mkdir($this->getDataFolder());
			$this->saveResource(".version_file");
		}

		$database_stmts = $this->getDataFolder() . "database_stmts/";
		if(!is_dir($database_stmts)) {
			mkdir($database_stmts);
		}

		$this->saveResource("database_stmts/mysql.sql", true);
		$this->saveResource("database_stmts/sqlite.sql", true);

		SpoonDetector::printSpoon($this);

		$this->registerEntities();
		$this->registerItems();
		$this->registerCommands();
		$this->registerListeners();
		PetInventoryManager::init($this);

		$this->bpConfig = new BlockPetsConfig($this);
		$this->pProperties = new PetProperties($this);
		$this->language = new LanguageConfig($this);
		$this->selectDatabase();
		Attribute::addAttribute(20, "minecraft:horse.jump_strength", 0, 3, 0.6679779);

		$this->checkVersionChange();
	}

	private function checkVersionChange(): void {
		$version_file = $this->getDataFolder() . ".version_file";
		if(!is_file($version_file)) {
			$current_version = "1.1.0";
		} else {
			$current_version = yaml_parse_file($version_file)["version"];
		}

		if(version_compare($this->getDescription()->getVersion(), $current_version, '>')) {
			$this->updateVersion($current_version);
		}
	}

	private function updateVersion(string $current_version): void {
		$current = (int) str_replace(".", "", $current_version);
		$newest = (int) str_replace(".", "", $this->getDescription()->getVersion());
		while($current < $newest) {
			++$current;
			$version = implode(".", str_split((string) $current));
			$this->onVersionUpdate($version);
		}

		$this->saveResource(".version_file", true);
	}

	private function onVersionUpdate(string $version): void {
		$this->getDatabase()->patch($version);
	}

	public function registerCommands(): void {
		$this->getServer()->getCommandMap()->registerAll($this->getName(), [
			new AddPetPointsCommand($this),
			new SpawnPetCommand($this),
			new LevelUpPetCommand($this),
			new RemovePetCommand($this),
			new HealPetCommand($this),
			new ClearPetCommand($this),
			new TogglePetCommand($this),
			new ChangePetNameCommand($this),
			new ListPetsCommand($this),
			new PetsTopCommand($this),
			new PetCommand($this)
		]);
	}

	public function registerEntities(): void {
		foreach(self::PET_CLASSES as $petClass) {
			Entity::registerEntity($petClass, true);
		}
	}

	public function registerItems(): void {
		ItemFactory::registerItem(new Saddle(), true);
		Item::addCreativeItem(Item::get(Item::SADDLE));
	}

	public function registerListeners(): void {
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
			case "sqlite":
			case "sqlite3":
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
	public function translate(string $key, array $params = []): string {
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
	public function petExists(string $entityName): bool {
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
	public function getPet(string $entityName): ?string {
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
	 * @param string|null $inventory
	 *
	 * @return null|BasePet
	 */
	public function createPet(string $entityName, Player $player, string $name, float $scale = 1.0, bool $isBaby = false, int $level = 1, int $levelPoints = 0, bool $chested = false, bool $isVisible = true, ?string $inventory = null): ?BasePet {
		$pet = $this->getPetByName($name, $player->getName());
		if($pet !== null) {
			$this->removePet($pet);
		}

		$nbt = Entity::createBaseNBT($player, null, $player->yaw, $player->pitch);
		$nbt->setString("petOwner", $player->getName());
		$nbt->setFloat("scale", $scale);
		$nbt->setString("petName", $name);
		$nbt->setInt("petLevel", $level);
		$nbt->setInt("petLevelPoints", $levelPoints);
		$nbt->setByte("isBaby", (int) $isBaby);
		$nbt->setByte("chested", (int) $chested);

		$entity = Entity::createEntity($entityName . "Pet", $player->getLevel(), $nbt);
		if($entity instanceof BasePet) {
			if(!empty($inventory)) {
				$entity->getInventoryManager()->load($inventory);
			}

			$this->getServer()->getPluginManager()->callEvent($ev = new PetSpawnEvent($this, $entity));
			if($ev->isCancelled()) {
				$this->removePet($entity);
				return null;
			}

			if(!$isVisible) {
				$entity->updateVisibility(false);
			}
			$this->playerPets[$player->getLowerCaseName()][strtolower($entity->getPetName())] = $entity;
			return $entity;
		}
		return null;
	}

	/**
	 * Creates a copy of the given pet and returns it.
	 *
	 * @param BasePet $pet
	 *
	 * @return BasePet
	 */
	public function clonePet(BasePet $pet): BasePet {
		$clone = $this->createPet($pet->getEntityType(), $pet->getPetOwner(), $pet->getPetName(), $pet->getStartingScale(), $pet->isBaby(), $pet->getPetLevel(), $pet->getPetLevelPoints(), $pet->isChested(), $pet->getVisibility());
		$clone->getInventory()->setContents($pet->getInventory()->getContents());
		return $clone;
	}

	/**
	 * Gets all currently available pets from the given player.
	 *
	 * @param Player $player
	 *
	 * @return BasePet[]
	 */
	public function getPetsFrom(Player $player): array {
		return $this->playerPets[$player->getLowerCaseName()] ?? [];
	}

	/**
	 * Returns the first pet found with the given name.
	 *
	 * @param string $name
	 * @param string|null $player
	 *
	 * @return BasePet|null
	 */
	public function getPetByName(string $name, ?string $player = null): ?BasePet {
		$name = strtolower($name);
		if($player !== null) {
			return $this->playerPets[strtolower($player)][$name] ?? null;
		}
		foreach($this->getServer()->getOnlinePlayers() as $player) {
			if(isset($this->playerPets[$k = $player->getLowerCaseName()][$name])) {
				return $this->playerPets[$k][$name];
			}
		}
		return null;
	}

	/**
	 * Closes and removes the specified pet from cache
	 * and calls PetRemoveEvent events.
	 *
	 * @param BasePet $pet
	 */
	public function removePet(BasePet $pet, bool $close = true): void {
		$this->getServer()->getPluginManager()->callEvent(new PetRemoveEvent($this, $pet));//TODO: Call a cancellable event if this method isn't called when pet owner quits
		if($pet->isRidden()) {
			$pet->throwRiderOff();
		}
		if($close && !$pet->isClosed()) {
			$pet->close();
		}
		unset($this->playerPets[strtolower($pet->getPetOwnerName())][strtolower($pet->getPetName())]);
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
	 * @return PetProperties
	 */
	public function getPetProperties(): PetProperties {
		return $this->pProperties;
	}
}

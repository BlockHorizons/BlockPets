<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets;

use BlockHorizons\BlockPets\commands\AddPetPointsCommand;
use BlockHorizons\BlockPets\commands\ChangePetNameCommand;
use BlockHorizons\BlockPets\commands\ClearPetCommand;
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
use BlockHorizons\BlockPets\pets\creatures\AllayPet;
use BlockHorizons\BlockPets\pets\creatures\ArrowPet;
use BlockHorizons\BlockPets\pets\creatures\AxolotlPet;
use BlockHorizons\BlockPets\pets\creatures\BatPet;
use BlockHorizons\BlockPets\pets\creatures\BeePet;
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
use BlockHorizons\BlockPets\pets\creatures\FoxPet;
use BlockHorizons\BlockPets\pets\creatures\GhastPet;
use BlockHorizons\BlockPets\pets\creatures\GoatPet;
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
use BlockHorizons\BlockPets\pets\creatures\StriderPet;
use BlockHorizons\BlockPets\pets\creatures\VexPet;
use BlockHorizons\BlockPets\pets\creatures\VillagerPet;
use BlockHorizons\BlockPets\pets\creatures\VindicatorPet;
use BlockHorizons\BlockPets\pets\creatures\WardenPet;
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
use BlockHorizons\BlockPets\pets\PetData;
use pocketmine\entity\Attribute;
use pocketmine\entity\AttributeFactory;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\item\ItemFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\world\World;
use Webmozart\PathUtil\Path;
use function strtolower;

class Loader extends PluginBase {

	/** @var string[] */
	const PETS = [
		"Allay"          => AllayPet::class,
		"Arrow"          => ArrowPet::class,
		"Axolotl"        => AxolotlPet::class,
		"Bat"            => BatPet::class,
		"Bee"	         => BeePet::class,
		"Blaze"          => BlazePet::class,
		"CaveSpider"     => CaveSpiderPet::class,
		"Chicken"        => ChickenPet::class,
		"Cow"            => CowPet::class,
		"Creeper"        => CreeperPet::class,
		"Donkey"         => DonkeyPet::class,
		"ElderGuardian"  => ElderGuardianPet::class,
		"EnderCrystal"   => EnderCrystalPet::class,
		"EnderDragon"    => EnderDragonPet::class,
		"Enderman"       => EndermanPet::class,
		"Endermite"      => EndermitePet::class,
		"Evoker"         => EvokerPet::class,
		"Fox"	         => FoxPet::class,
		"Ghast"          => GhastPet::class,
		"Goat"	         => GoatPet::class,
		"Guardian"       => GuardianPet::class,
		"Horse"          => HorsePet::class,
		"Husk"           => HuskPet::class,
		"IronGolem"      => IronGolemPet::class,
		"Llama"          => LlamaPet::class,
		"MagmaCube"      => MagmaCubePet::class,
		"Mooshroom"      => MooshroomPet::class,
		"Mule"           => MulePet::class,
		"Ocelot"         => OcelotPet::class,
		"Pig"            => PigPet::class,
		"PolarBear"      => PolarBearPet::class,
		"Rabbit"         => RabbitPet::class,
		"Sheep"          => SheepPet::class,
		"SilverFish"     => SilverFishPet::class,
		"Skeleton"       => SkeletonPet::class,
		"SkeletonHorse"  => SkeletonHorsePet::class,
		"Slime"          => SlimePet::class,
		"SnowGolem"      => SnowGolemPet::class,
		"Spider"         => SpiderPet::class,
		"Squid"          => SquidPet::class,
		"Stray"          => StrayPet::class,
		"Strider"        => StriderPet::class,
		"Vex"            => VexPet::class,
		"Villager"       => VillagerPet::class,
		"Vindicator"     => VindicatorPet::class,
		"Warden"         => WardenPet::class,
		"Witch"          => WitchPet::class,
		"Wither"         => WitherPet::class,
		"WitherSkeleton" => WitherSkeletonPet::class,
		"WitherSkull"    => WitherSkullPet::class,
		"Wolf"           => WolfPet::class,
		"Zombie"         => ZombiePet::class,
		"ZombieHorse"    => ZombieHorsePet::class,
		"ZombiePigman"   => ZombiePigmanPet::class,
		"ZombieVillager" => ZombieVillagerPet::class
	];

	/** @var string[] */
	private array $availableLanguages = [
		"en",
		"nl",
		"vi",
		"gr",
		"ko",
		"de"
	];

	public array $selectingName = [];

	private BlockPetsConfig $bpConfig;
	private PetProperties $pProperties;
	private LanguageConfig $language;
	private ?BaseDataStorer $database = null;

	/** @var BasePet[][] */
	private array $playerPets = [];

	protected function onEnable(): void {
		if(!is_dir($this->getDataFolder())) {
			mkdir($this->getDataFolder());
		}

		$this->saveResource(".version_file");

		$database_stmts = Path::join($this->getDataFolder(), "database_stmts");
		if(!is_dir($database_stmts)) {
			mkdir($database_stmts);
		}

		$this->saveResource("database_stmts/mysql.sql", true);
		$this->saveResource("database_stmts/sqlite.sql", true);

		$this->registerEntities();
		$this->registerItems();
		$this->registerCommands();
		$this->registerListeners();
		PetInventoryManager::init($this);

		$this->bpConfig = new BlockPetsConfig($this);
		$this->pProperties = new PetProperties($this);
		$this->language = new LanguageConfig($this);
		$this->selectDatabase();

		/** @var AttributeFactory $factory */
		$factory = AttributeFactory::getInstance();
		$factory->register(Attribute::HORSE_JUMP_STRENGTH, 0.0, 3.0, 0.6679779);

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
		/** @var EntityFactory $entityFactory */
		$entityFactory = EntityFactory::getInstance();
		/**
		 * @var string  $name
		 * @var BasePet $petClass
		 */
		foreach(self::PETS as $name => $petClass) {
			$entityFactory->register($petClass, function(World $world, CompoundTag $nbt) use ($petClass): Entity {
				return new $petClass(EntityDataHelper::parseLocation($nbt, $world), $nbt);
			}, [$name, $petClass::NETWORK_NAME]);
		}
	}

	public function registerItems(): void {
		/** @var ItemFactory $factory */
		$factory = ItemFactory::getInstance();
		$factory->register(new Saddle(), true);
		// Item::addCreativeItem(Item::get(Item::SADDLE));
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

	public function getBlockPetsConfig(): BlockPetsConfig {
		return $this->bpConfig;
	}

	public function translate(string $key, array $params = []): string {
		if(!empty($params)) {
			return vsprintf($this->getLanguage()->get($key), $params);
		}
		return $this->getLanguage()->get($key);
	}

	public function getLanguage(): LanguageConfig {
		return $this->language;
	}

	/**
	 * Checks if a pet type of that name exists.
	 */
	public function petExists(string $entityName): bool {
		return $this->getPet($entityName) !== null;
	}

	/**
	 * Tries to match a pet type with the pet type list, and returns the fully qualified name if this could be found. Null if no valid result was found.
	 */
	public function getPet(string $entityName): ?string {
		foreach(self::PETS as $pet => $petClass) {
			if(strtolower($pet) === strtolower($entityName)) {
				return $pet;
			}
		}
		return null;
	}

	/**
	 * Get the class of the relevant pet.
	 */
	public function getPetClass(string $entityName): ?string {
		foreach(self::PETS as $pet => $petClass) {
			if(strtolower($pet) === strtolower($entityName)) {
				return $petClass;
			}
		}
		return null;
	}

	/**
	 * Creates a new pet for the given player.
	 */
	public function createPet(string $entityName, Player $player, string $name, float $scale = 1.0, bool $isBaby = false, int $level = 1, int $levelPoints = 0, bool $chested = false, bool $isVisible = true, ?string $inventory = null): ?BasePet {
		$pet = $this->getPetByName($name, $player->getName());
		if($pet !== null) {
			$this->removePet($pet);
		}

		$nbt = CompoundTag::create();
		$nbt->setString("petOwner", $player->getName());
		$nbt->setFloat("scale", $scale);
		$nbt->setString("petName", $name);
		$nbt->setInt("petLevel", $level);
		$nbt->setInt("petLevelPoints", $levelPoints);
		$nbt->setByte("isBaby", (int) $isBaby);
		$nbt->setByte("chested", (int) $chested);

		$class = $this->getPetClass($entityName);
		if($class === null) {
			return null;
		}

		$entity = new $class($player->getLocation(), $nbt);
		if($entity instanceof BasePet) {
			if(!empty($inventory)) {
				$entity->getInventoryManager()->load($inventory);
			}

			$ev = new PetSpawnEvent($this, $entity);
			$ev->call();

			if($ev->isCancelled()) {
				$this->removePet($entity);
				return null;
			}

			if(!$isVisible) {
				$entity->updateVisibility(false);
			} else {
				$entity->spawnToAll();
			}

			$this->playerPets[strtolower($player->getName())][strtolower($entity->getPetName())] = $entity;
			return $entity;
		}

		return null;
	}

	/**
	 * Creates a copy of the given pet and returns it.
	 */
	public function clonePet(PetData $data): ?BasePet {
		$owner = $this->getServer()->getPlayerExact($data->getOwnerName());
		if($owner === null) {
			return null;
		}
		return $this->createPet(
			$data->getPetId(),
			$owner,
			$data->getPetName(),
			$data->getScale(),
			$data->isBaby(),
			$data->getLevel(),
			$data->getLevelPoints(),
			$data->isChested(),
			$data->isVisible(),
			$data->getInventory()
		);
	}

	/**
	 * Gets all currently available pets from the given player.
	 *
	 * @return BasePet[]
	 */
	public function getPetsFrom(Player $player): array {
		return $this->playerPets[strtolower($player->getName())] ?? [];
	}

	/**
	 * Returns the first pet found with the given name.
	 */
	public function getPetByName(string $name, ?string $player = null): ?BasePet {
		$name = strtolower($name);
		if($player !== null) {
			return $this->playerPets[strtolower($player)][$name] ?? null;
		}
		foreach($this->getServer()->getOnlinePlayers() as $player) {
			if(isset($this->playerPets[$k = strtolower($player->getName())][$name])) {
				return $this->playerPets[$k][$name];
			}
		}
		return null;
	}

	/**
	 * Closes and removes the specified pet from cache and calls PetRemoveEvent events.
	 */
	public function removePet(BasePet $pet, bool $close = true): void {
		// TODO: Call a cancellable event if this method isn't called when pet owner quits
		(new PetRemoveEvent($this, $pet))->call();
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
	 */
	public function getDatabase(): BaseDataStorer {
		if($this->database === null) {
			throw new \RuntimeException("Attempted to retrieve the database while database storing was unavailable.");
		}
		return $this->database;
	}

	/**
	 * Gets the pet the given player is currently riding.
	 */
	public function getRiddenPet(Player $player): ?BasePet {
		foreach($this->getPetsFrom($player) as $pet) {
			if($pet->isRidden()) {
				return $pet;
			}
		}
		return null;
	}

	/**
	 * Checks if the given player is currently riding a pet.
	 */
	public function isRidingAPet(Player $player): bool {
		foreach($this->getPetsFrom($player) as $pet) {
			if($pet->isRidden()) {
				return true;
			}
		}
		return false;
	}

	public function getPetProperties(): PetProperties {
		return $this->pProperties;
	}
}

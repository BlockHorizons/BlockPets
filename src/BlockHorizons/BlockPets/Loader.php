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
use pocketmine\data\bedrock\item\ItemTypeNames;
use pocketmine\data\bedrock\item\SavedItemData;
use pocketmine\entity\Attribute;
use pocketmine\entity\AttributeFactory;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\item\StringToItemParser;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\AsyncTask;
use pocketmine\world\format\io\GlobalItemDataHandlers;
use pocketmine\world\World;
use Symfony\Component\Filesystem\Path;

use function strtolower;

class Loader extends PluginBase {

	/** @var string[] */
	protected const PETS = [
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

		$this->registerPermissions();
		$this->registerEntities();
		$this->registerItems();
		$this->registerCommands();
		$this->registerListeners();
		PetInventoryManager::init($this);

		$this->bpConfig = new BlockPetsConfig($this);
		$this->pProperties = new PetProperties($this);
		$this->language = new LanguageConfig($this);
		$this->selectDatabase();

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
		$entityFactory = EntityFactory::getInstance();
		foreach(self::PETS as $name => $petClass) {
			$entityFactory->register($petClass, function(World $world, CompoundTag $nbt) use ($petClass): Entity {
				return new $petClass(EntityDataHelper::parseLocation($nbt, $world), $nbt);
			}, [$name, $petClass::NETWORK_NAME]);
		}
	}

	public function registerItems(): void {
		self::registerOnCurrentThread();
		$this->getServer()->getAsyncPool()->addWorkerStartHook(function(int $worker) : void{
			$this->getServer()->getAsyncPool()->submitTaskToWorker(new class extends AsyncTask{
				public function onRun() : void{
					Loader::registerOnCurrentThread();
				}
			}, $worker);
		});
	}

	public static function registerOnCurrentThread() : void{
		GlobalItemDataHandlers::getDeserializer()->map(ItemTypeNames::SADDLE, fn() => clone Saddle::SADDLE());
		GlobalItemDataHandlers::getSerializer()->map(Saddle::SADDLE(), fn() => new SavedItemData(ItemTypeNames::SADDLE));
		StringToItemParser::getInstance()->register("saddle", fn() => clone Saddle::SADDLE());
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
	 * Registers all BlockPets permissions with the server's permission manager.
	 * This method creates and adds permissions for various plugin features and pet types.
	 * Permissions are used to control access to commands and pet spawning functionality.
	 * We use PermissionManager::addPermission() instead of PluginManager::addPermission()
	 * because it directly registers permissions with the global permission system,
	 * ensuring they are available for all players and plugins to check against.
	 */
	public function registerPermissions(): void {
		$permissions = [
			"blockpets" => new Permission("blockpets", "Allows access to all BlockPets features"),
			"blockpets.bypass-limit" => new Permission("blockpets.bypass-limit", "Allows bypassing the pet limit set in the config.yml"),
			"blockpets.bypass-size-limit" => new Permission("blockpets.bypass-size-limit", "Allows bypassing the maximum size of pets"),
			"blockpets.command" => new Permission("blockpets.command", "Allows access to all BlockPets command features"),
			"blockpets.command.spawnpet" => new Permission("blockpets.command.spawnpet", "Allows access to use the spawnpet command"),
			"blockpets.command.spawnpet.use" => new Permission("blockpets.command.spawnpet.use", "Allows access to use the spawnpet command for yourself"),
			"blockpets.command.spawnpet.others" => new Permission("blockpets.command.spawnpet.others", "Allows access to use the spawnpet command for other players"),
			"blockpets.command.changepetname" => new Permission("blockpets.command.changepetname", "Allows access to use the changepetname command"),
			"blockpets.command.changepetname.use" => new Permission("blockpets.command.changepetname.use", "Allows access to use the changepetname command for yourself"),
			"blockpets.command.changepetname.others" => new Permission("blockpets.command.changepetname.others", "Allows access to use the changepetname command for others"),
			"blockpets.command.removepet" => new Permission("blockpets.command.removepet", "Allows access to use the removepet command"),
			"blockpets.command.leveluppet" => new Permission("blockpets.command.leveluppet", "Allows access to use the leveluppet command"),
			"blockpets.command.addpetpoints" => new Permission("blockpets.command.addpetpoints", "Allows access to use the addpetpoints command"),
			"blockpets.command.healpet" => new Permission("blockpets.command.healpet", "Allows access to use the healpet command"),
			"blockpets.command.clearpet" => new Permission("blockpets.command.clearpet", "Allows access to use the clearpet command"),
			"blockpets.command.togglepet" => new Permission("blockpets.command.togglepet", "Allows access to use the togglepets command"),
			"blockpets.command.pet" => new Permission("blockpets.command.pet", "Allows access to use the pets command"),
			"blockpets.command.listpets" => new Permission("blockpets.command.listpets", "Allows access to use the listpets command"),
			"blockpets.command.petstop" => new Permission("blockpets.command.petstop", "Allows access to use the petstop command"),
			"blockpets.pet" => new Permission("blockpets.pet", "Allows access to all BlockPets pets"),
			"blockpets.pet.allay" => new Permission("blockpets.pet.allay", "Allows access to use the allay pet"),
			"blockpets.pet.arrow" => new Permission("blockpets.pet.arrow", "Allows access to use the arrow pet"),
			"blockpets.pet.axolotl" => new Permission("blockpets.pet.axolotl", "Allows access to use the axolotl pet"),
			"blockpets.pet.bat" => new Permission("blockpets.pet.bat", "Allows access to use the bat pet"),
			"blockpets.pet.bee" => new Permission("blockpets.pet.bee", "Allows access to use the bee pet"),
			"blockpets.pet.blaze" => new Permission("blockpets.pet.blaze", "Allows access to use the blaze pet"),
			"blockpets.pet.cavespider" => new Permission("blockpets.pet.cavespider", "Allows access to use the cave spider pet"),
			"blockpets.pet.chicken" => new Permission("blockpets.pet.chicken", "Allows access to use the chicken pet"),
			"blockpets.pet.cow" => new Permission("blockpets.pet.cow", "Allows access to use the cow pet"),
			"blockpets.pet.creeper" => new Permission("blockpets.pet.creeper", "Allows access to use the creeper pet"),
			"blockpets.pet.donkey" => new Permission("blockpets.pet.donkey", "Allows access to use the donkey pet"),
			"blockpets.pet.elderguardian" => new Permission("blockpets.pet.elderguardian", "Allows access to use the elder guardian pet"),
			"blockpets.pet.endercrystal" => new Permission("blockpets.pet.endercrystal", "Allows access to use the ender crystal pet"),
			"blockpets.pet.enderdragon" => new Permission("blockpets.pet.enderdragon", "Allows access to use the ender dragon pet"),
			"blockpets.pet.enderman" => new Permission("blockpets.pet.enderman", "Allows access to use the enderman pet"),
			"blockpets.pet.endermite" => new Permission("blockpets.pet.endermite", "Allows access to use the endermite pet"),
			"blockpets.pet.evoker" => new Permission("blockpets.pet.evoker", "Allows access to use the evoker pet"),
			"blockpets.pet.fox" => new Permission("blockpets.pet.fox", "Allows access to use the fox pet"),
			"blockpets.pet.ghast" => new Permission("blockpets.pet.ghast", "Allows access to use the ghast pet"),
			"blockpets.pet.goat" => new Permission("blockpets.pet.goat", "Allows access to use the goat pet"),
			"blockpets.pet.guardian" => new Permission("blockpets.pet.guardian", "Allows access to use the guardian pet"),
			"blockpets.pet.horse" => new Permission("blockpets.pet.horse", "Allows access to use the horse pet"),
			"blockpets.pet.husk" => new Permission("blockpets.pet.husk", "Allows access to use the husk pet"),
			"blockpets.pet.irongolem" => new Permission("blockpets.pet.irongolem", "Allows access to use the iron golem pet"),
			"blockpets.pet.llama" => new Permission("blockpets.pet.llama", "Allows access to use the llama pet"),
			"blockpets.pet.magmacube" => new Permission("blockpets.pet.magmacube", "Allows access to use the magma cube pet"),
			"blockpets.pet.mooshroom" => new Permission("blockpets.pet.mooshroom", "Allows access to use the mooshroom pet"),
			"blockpets.pet.mule" => new Permission("blockpets.pet.mule", "Allows access to use the mule pet"),
			"blockpets.pet.ocelot" => new Permission("blockpets.pet.ocelot", "Allows access to use the ocelot pet"),
			"blockpets.pet.pig" => new Permission("blockpets.pet.pig", "Allows access to use the pig pet"),
			"blockpets.pet.polarbear" => new Permission("blockpets.pet.polarbear", "Allows access to use the polar bear pet"),
			"blockpets.pet.rabbit" => new Permission("blockpets.pet.rabbit", "Allows access to use the rabbit pet"),
			"blockpets.pet.sheep" => new Permission("blockpets.pet.sheep", "Allows access to use the sheep pet"),
			"blockpets.pet.silverfish" => new Permission("blockpets.pet.silverfish", "Allows access to use the silverfish pet"),
			"blockpets.pet.skeleton" => new Permission("blockpets.pet.skeleton", "Allows access to use the skeleton pet"),
			"blockpets.pet.skeletonhorse" => new Permission("blockpets.pet.skeletonhorse", "Allows access to use the skeleton horse pet"),
			"blockpets.pet.slime" => new Permission("blockpets.pet.slime", "Allows access to use the slime pet"),
			"blockpets.pet.snowgolem" => new Permission("blockpets.pet.snowgolem", "Allows access to use the snow golem pet"),
			"blockpets.pet.spider" => new Permission("blockpets.pet.spider", "Allows access to use the spider pet"),
			"blockpets.pet.squid" => new Permission("blockpets.pet.squid", "Allows access to use the squid pet"),
			"blockpets.pet.stray" => new Permission("blockpets.pet.stray", "Allows access to use the stray pet"),
			"blockpets.pet.strider" => new Permission("blockpets.pet.strider", "Allows access to use the strider pet"),
			"blockpets.pet.vex" => new Permission("blockpets.pet.vex", "Allows access to use the vex pet"),
			"blockpets.pet.villager" => new Permission("blockpets.pet.villager", "Allows access to use the village pet"),
			"blockpets.pet.vindicator" => new Permission("blockpets.pet.vindicator", "Allows access to use the vindicator pet"),
			"blockpets.pet.warden" => new Permission("blockpets.pet.warden", "Allows access to use the warden pet"),
			"blockpets.pet.witch" => new Permission("blockpets.pet.witch", "Allows access to use the witch pet"),
			"blockpets.pet.wither" => new Permission("blockpets.pet.wither", "Allows access to use the wither pet"),
			"blockpets.pet.witherskeleton" => new Permission("blockpets.pet.witherskeleton", "Allows access to use the wither skeleton pet"),
			"blockpets.pet.witherskull" => new Permission("blockpets.pet.witherskull", "Allows access to use the wither skull pet"),
			"blockpets.pet.wolf" => new Permission("blockpets.pet.wolf", "Allows access to use the wolf pet"),
			"blockpets.pet.zombie" => new Permission("blockpets.pet.zombie", "Allows access to use the zombie pet"),
			"blockpets.pet.zombiehorse" => new Permission("blockpets.pet.zombiehorse", "Allows access to use the zombie horse pet"),
			"blockpets.pet.zombiepigman" => new Permission("blockpets.pet.zombiepigman", "Allows access to use the zombie pigman pet"),
			"blockpets.pet.zombievillager" => new Permission("blockpets.pet.zombievillager", "Allows access to use the zombie villager pet")
		];

		$permissionManager = PermissionManager::getInstance();
		foreach($permissions as $permission) {
			$permissionManager->addPermission($permission);
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

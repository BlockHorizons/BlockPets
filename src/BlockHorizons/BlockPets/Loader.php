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

	public function onEnable() {
		CommandOverloads::initialize();
		foreach(self::PET_CLASSES as $petClass) {
			Entity::registerEntity($petClass, true);
		}
		$this->registerCommands();
		$this->registerListeners();

		$this->bpConfig = new BlockPetsConfig($this);
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

	public function onEntitySpawn(EntitySpawnEvent $event) {
		if($event->getEntity() instanceof BasePet) {
			$clearLaggPlugin = $this->getServer()->getPluginManager()->getPlugin("ClearLagg");
			if($clearLaggPlugin !== null) {
				$clearLaggPlugin->exemptEntity($event->getEntity());
			}
		}
	}

	/**
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
			"isBaby" => new ByteTag("isBaby", $isBaby)
		]);

		$entity = Entity::createEntity($entityName . "Pet", $position->getLevel(), $nbt);
		if($entity instanceof BasePet) {
			return $entity;
		}
		return null;
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function removePet(string $name): bool {
		$pet = $this->getPetByName($name);
		if($pet === null) {
			return false;
		}
		$pet->close();
		return true;
	}

	/**
	 * @param string $name
	 *
	 * @return BasePet|null
	 */
	public function getPetByName(string $name) {
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
	 * @return BlockPetsConfig
	 */
	public function getBlockPetsConfig(): BlockPetsConfig {
		return $this->bpConfig;
	}
}
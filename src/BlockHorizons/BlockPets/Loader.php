<?php

namespace BlockHorizons\BlockPets;

use BlockHorizons\BlockPets\commands\LevelUpPetCommand;
use BlockHorizons\BlockPets\commands\RemovePetCommand;
use BlockHorizons\BlockPets\listeners\EventListener;
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
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use BlockHorizons\BlockPets\commands\SpawnPetCommand;

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
		"ZombieVillager"
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
		ZombieVillagerPet::class
	];

	public function onEnable() {
		foreach(self::PET_CLASSES as $petClass) {
			Entity::registerEntity($petClass, true);
		}
		$this->registerCommands();
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
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

	public function onDisable() {

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
		if(!$this->petExists($entityName)) {
			return null;
		}
		foreach(self::PETS as $pet) {
			if(strtolower($pet) === strtolower($entityName)) {
				return $pet;
			}
		}
		return null;
	}

	public function createPet(string $entityName, Player $position, float $scale = 1.0) {
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
			"networkId" => new IntTag("networkId", 10),
		]);
		$chunk = $position->level->getChunk($position->x >> 4, $position->z >> 4, true);

		return Entity::createEntity($entityName . "Pet", $chunk, $nbt);
	}

	/**
	 * @param string $name
	 *
	 * @return BasePet|null
	 */
	public function getPetByName(string $name) {
		foreach($this->getServer()->getLevels() as $level) {
			foreach($level->getEntities() as $entity) {
				if(strpos(strtolower($entity->getNameTag()), strtolower($name)) !== false) {
					if($entity instanceof BasePet) {
						return $entity;
					}
				}
			}
		}
		return null;
	}

	/**
	 * @param Player $player
	 *
	 * @return array
	 */
	public function getPetsFrom(Player $player): array {
		$playerPets = [];
		foreach($player->getLevel()->getEntities() as $entity) {
			if($entity instanceof BasePet) {
				if($entity->getPetOwner()->getName() === $player->getName()) {
					$playerPets[] = $entity;
				}
			}
		}
		return $playerPets;
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
		$pet->kill();
		return true;
	}
}
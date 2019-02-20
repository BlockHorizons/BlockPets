<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets;

use BlockHorizons\BlockPets\Loader;
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
use BlockHorizons\BlockPets\pets\datastorage\types\PetData;
use BlockHorizons\BlockPets\pets\datastorage\types\PetInventoryManager;
use pocketmine\entity\Attribute;
use pocketmine\entity\Entity;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\Utils;

final class PetFactory {

	public const SAVE_ID_PREFIX = "blockpets:";

	/** @var string[] */
	private static $knownPets = [];

	public static function init(Loader $loader): void {
		Attribute::addAttribute(20, "minecraft:horse.jump_strength", 0, 3, 0.6679779);
		PetInventoryManager::init($loader);

		self::registerPet(ArrowPet::class);
		self::registerPet(BatPet::class);
		self::registerPet(BlazePet::class);
		self::registerPet(CaveSpiderPet::class);
		self::registerPet(ChickenPet::class);
		self::registerPet(CowPet::class);
		self::registerPet(CreeperPet::class);
		self::registerPet(DonkeyPet::class);
		self::registerPet(ElderGuardianPet::class);
		self::registerPet(EnderCrystalPet::class);
		self::registerPet(EnderDragonPet::class);
		self::registerPet(EndermanPet::class);
		self::registerPet(EndermitePet::class);
		self::registerPet(EvokerPet::class);
		self::registerPet(GhastPet::class);
		self::registerPet(GuardianPet::class);
		self::registerPet(HorsePet::class);
		self::registerPet(HuskPet::class);
		self::registerPet(IronGolemPet::class);
		self::registerPet(LlamaPet::class);
		self::registerPet(MagmaCubePet::class);
		self::registerPet(MooshroomPet::class);
		self::registerPet(MulePet::class);
		self::registerPet(OcelotPet::class);
		self::registerPet(PigPet::class);
		self::registerPet(PolarBearPet::class);
		self::registerPet(RabbitPet::class);
		self::registerPet(SheepPet::class);
		self::registerPet(SilverFishPet::class);
		self::registerPet(SkeletonHorsePet::class);
		self::registerPet(SkeletonPet::class);
		self::registerPet(SlimePet::class);
		self::registerPet(SnowGolemPet::class);
		self::registerPet(SpiderPet::class);
		self::registerPet(SquidPet::class);
		self::registerPet(StrayPet::class);
		self::registerPet(VexPet::class);
		self::registerPet(VillagerPet::class);
		self::registerPet(VindicatorPet::class);
		self::registerPet(WitchPet::class);
		self::registerPet(WitherPet::class);
		self::registerPet(WitherSkeletonPet::class);
		self::registerPet(WitherSkullPet::class);
		self::registerPet(WolfPet::class);
		self::registerPet(ZombieHorsePet::class);
		self::registerPet(ZombiePet::class);
		self::registerPet(ZombiePigmanPet::class);
		self::registerPet(ZombieVillagerPet::class);
	}

	public static function registerPet(string $className, bool $override = false): void {
		Utils::testValidInstance($className, BasePet::class);

		$className::getPetNetworkId(); // throws error if pet's network id is null

		if($className::NETWORK_ID !== -1) {
			throw new \LogicException("NETWORK_ID of " . $className . " should not be overridden.");
		}

		$readable_name = self::getReadableName($className::getPetSaveId());
		if(isset(self::$knownPets[$readable_name]) && !$override) {
			throw new \InvalidArgumentException("Tried to override pet " . $readable_name . ".");
		}

		Entity::registerEntity($className, true, [$className::getPetSaveId()]);
		self::$knownPets[$readable_name] = $className::getPetSaveId();
	}

	public static function create(PetData $data, Level $level, CompoundTag $nbt): BasePet {
		return Entity::createEntity($data->getType(), $level, $nbt, $data);
	}

	public static function getKnownPetId(string $pet): ?string {
		// "snowgolem" => self::SAVE_ID_PREFIX . "snow_golem"
		// "spider" => self::SAVE_ID_PREFIX . "spider"
		// ...etc
		return self::$knownPets[strtolower($pet)] ?? null;
	}

	public static function getReadableName(string $saveId): string {
		$offset = strpos($saveId, ":");
		if($offset === false){
			$offset = 0;
		} else {
			$offset++;
		}

		return strtolower(str_replace("_", "", substr($saveId, $offset)));
	}
}
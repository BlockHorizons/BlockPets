<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\commands;

use BlockHorizons\BlockPets\Loader;

final class CommandFactory {

	public static function init(Loader $loader): void {
		$loader->getServer()->getCommandMap()->registerAll($loader->getName(), [
			new AddPetPointsCommand($loader),
			new ChangePetNameCommand($loader),
			new ClearPetCommand($loader),
			new HealPetCommand($loader),
			new LevelUpPetCommand($loader),
			new ListPetsCommand($loader),
			new PetCommand($loader),
			new PetsTopCommand($loader),
			new RemovePetCommand($loader),
			new SpawnPetCommand($loader),
			new TogglePetCommand($loader)
		]);
	}
}
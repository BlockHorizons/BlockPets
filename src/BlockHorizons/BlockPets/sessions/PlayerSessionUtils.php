<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\sessions;

use BlockHorizons\BlockPets\pets\BasePet;

class PlayerSessionUtils {

	/**
	 * Finds pets by the given name.
	 *
	 * @param string $pet_name
	 *
	 * @return \Generator<BasePet>
	 */
	public static function getPetsByName(string $pet_name): \Generator {
		foreach(PlayerSession::getAll() as $session) {
			$pet = $session->getPet($pet_name);
			if($pet !== null) {
				yield $pet;
			}
		}
	}

	public static function getPetByName(string $pet_name): ?BasePet {
		foreach(self::getPetsByName($pet_name) as $pet) {
			return $pet;
		}

		return null;
	}
}
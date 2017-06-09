<?php

namespace BlockHorizons\BlockPets\commands;


use BlockHorizons\BlockPets\Loader;

class CommandOverloads {

	private static $commandOverloads = [];

	/**
	 * @param string $command
	 *
	 * @return array
	 */
	public static function getOverloads(string $command): array {
		return self::$commandOverloads[$command];
	}

	public static function initialize() {
		$pets = Loader::PETS;
		foreach($pets as $key => $pet) {
			$pets[$key] = strtolower($pet);
		}
		self::$commandOverloads = [
			"spawnpet" => [
				0 => [
					"type" => "stringenum",
					"name" => "type",
					"optional" => false,
					"enum_values" => $pets
				],
				1 => [
					"type" => "rawtext",
					"name" => "name",
					"optional" => false
				],
				2 => [
					"type" => "int",
					"name" => "size",
					"optional" => true
				],
				3 => [
					"type" => "stringenum",
					"name" => "isBaby",
					"optional" => true,
					"enum_values" => [
						"true",
						"false"
					]
				],
				4 => [
					"type" => "rawtext",
					"name" => "player",
					"optional" => true
				]
			],

			"removepet" => [
				0 => [
					"type" => "rawtext",
					"name" => "pet name",
					"optional" => false
				],
				1 => [
					"type" => "rawtext",
					"name" => "player",
					"optional" => true
				]
			],

			"leveluppet" => [
				0 => [
					"type" => "rawtext",
					"name" => "pet name",
					"optional" => false
				],
				1 => [
					"type" => "int",
					"name" => "amount",
					"optional" => true
				],
				2 => [
					"type" => "rawtext",
					"name" => "player",
					"optional" => true
				]
			]
		];
	}
}
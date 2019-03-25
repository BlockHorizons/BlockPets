<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\datastorage;

use BlockHorizons\BlockPets\Loader;

class SQLiteDataStorer extends SQLDataStorer {

	protected static function readBinaryString(string $string): string {
		return base64_decode($string);
	}

	protected static function writeBinaryString(string $string): string {
		return base64_encode($string);
	}

	protected function getLibasynqlFriendlyConfig(Loader $loader): array {
		return [
			"file" => $loader->getDataFolder() . "blockpets.sqlite3"
		];
	}

	protected function getName(): string {
		return "sqlite";
	}
}
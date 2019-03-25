<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\datastorage;

use BlockHorizons\BlockPets\Loader;

class MySQLDataStorer extends SQLDataStorer {

	protected function getLibasynqlFriendlyConfig(Loader $loader): array {
		$mysql_config = $loader->getBlockPetsConfig()->getMySQLInfo();
		return [
			"host" => $mysql_config["Host"],
			"username" => $mysql_config["User"],
			"password" => $mysql_config["Password"],
			"schema" => $mysql_config["Database"],
			"port" => $mysql_config["Port"]
		];
	}

	protected function getName(): string {
		return "mysql";
	}
}
-- #!mysql
-- #{ blockpets

-- #  { init
-- #    { pets
CREATE TABLE IF NOT EXISTS pets(
  uuid BINARY(16) NOT NULL,
  owner VARCHAR(16) NOT NULL,
  type VARCHAR(32) NOT NULL,
  PRIMARY KEY(uuid)
);
-- #    }
-- #    { pets_property
CREATE TABLE IF NOT EXISTS pets_property(
  uuid BINARY(16) NOT NULL,
  name VARCHAR(48) NOT NULL,
  xp INT UNSIGNED NOT NULL DEFAULT 0,
  nbt BLOB,

  PRIMARY KEY(uuid, name),

  FOREIGN KEY(uuid) REFERENCES pets(uuid)
    ON UPDATE CASCADE
    ON DELETE CASCADE
);
-- #    }
-- #  }

-- #  { player
-- #    { load
-- #      :owner string
SELECT
  pet.uuid,
  pet.type,
  property.name,
  property.xp,
  property.nbt
FROM pets pet
INNER JOIN pets_property property
  ON property.uuid=pet.uuid
WHERE pet.owner=:owner
-- #    }
-- #  }

-- #  { pet
-- #    { create
-- #      :uuid string
-- #      :owner string
-- #      :type string
INSERT INTO pets(uuid, owner, type)
VALUES(:uuid, :owner, :type);
-- #    }
-- #    { delete
-- #      :uuid string
DELETE FROM pets WHERE uuid=:uuid;
-- #    }
-- #    { init_properties
-- #      :uuid string
-- #      :name string
INSERT INTO pets_property(uuid, name)
VALUES(:uuid, :name);
-- #    }
-- #    { update
-- #      { name
-- #        :uuid string
-- #        :name string
UPDATE pets_property SET name=:name WHERE uuid=:uuid;
-- #      }
-- #      { xp
-- #        :uuid string
-- #        :xp int
UPDATE pets_property SET xp=:xp WHERE uuid=:uuid;
-- #      }
-- #      { nbt
-- #        :uuid string
-- #        :nbt string
UPDATE pets_property SET nbt=:nbt WHERE uuid=:uuid;
-- #      }
-- #    }
-- #    { leaderboards
-- #      :offset int
-- #      :length int
-- #      :type string
SELECT
  pet.owner,
  pet.type
  property.name,
  property.xp
FROM pets pet
INNER JOIN pets_property property
  ON property.uuid=pet.uuid
WHERE pet.type LIKE :type
ORDER BY xp DESC
LIMIT :offset, :length;
-- #    }
-- #  }
-- #}

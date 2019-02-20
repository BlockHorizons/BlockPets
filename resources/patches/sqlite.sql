-- #!sqlite
-- #{ version
-- #  { 1.1.2
ALTER TABLE Pets ADD COLUMN Visible BOOL NOT NULL DEFAULT TRUE;
-- #  }
-- #  { 2.0.0
UPDATE Pets
SET EntityName=
CASE
  WHEN EntityName='Arrow' THEN 'blockpets:arrow'
  WHEN EntityName='Bat' THEN 'blockpets:bat'
  WHEN EntityName='Blaze' THEN 'blockpets:blaze'
  WHEN EntityName='CaveSpider' THEN 'blockpets:cave_spider'
  WHEN EntityName='Chicken' THEN 'blockpets:chicken'
  WHEN EntityName='Cow' THEN 'blockpets:cow'
  WHEN EntityName='Creeper' THEN 'blockpets:creeper'
  WHEN EntityName='Donkey' THEN 'blockpets:donkey'
  WHEN EntityName='ElderGuardian' THEN 'blockpets:elder_guardian'
  WHEN EntityName='EnderCrystal' THEN 'blockpets:ender_crystal'
  WHEN EntityName='EnderDragon' THEN 'blockpets:ender_dragon'
  WHEN EntityName='Enderman' THEN 'blockpets:enderman'
  WHEN EntityName='Endermite' THEN 'blockpets:endermite'
  WHEN EntityName='Evoker' THEN 'blockpets:evoker'
  WHEN EntityName='Ghast' THEN 'blockpets:ghast'
  WHEN EntityName='Guardian' THEN 'blockpets:guardian'
  WHEN EntityName='Horse' THEN 'blockpets:horse'
  WHEN EntityName='Husk' THEN 'blockpets:husk'
  WHEN EntityName='IronGolem' THEN 'blockpets:iron_golem'
  WHEN EntityName='Llama' THEN 'blockpets:llama'
  WHEN EntityName='MagmaCube' THEN 'blockpets:magma_cube'
  WHEN EntityName='Mooshroom' THEN 'blockpets:mooshroom'
  WHEN EntityName='Mule' THEN 'blockpets:mule'
  WHEN EntityName='Ocelot' THEN 'blockpets:ocelot'
  WHEN EntityName='Pig' THEN 'blockpets:pig'
  WHEN EntityName='PolarBear' THEN 'blockpets:polar_bear'
  WHEN EntityName='Rabbit' THEN 'blockpets:rabbit'
  WHEN EntityName='Sheep' THEN 'blockpets:sheep'
  WHEN EntityName='SilverFish' THEN 'blockpets:silver_fish'
  WHEN EntityName='Skeleton' THEN 'blockpets:skeleton'
  WHEN EntityName='SkeletonHorse' THEN 'blockpets:skeleton_horse'
  WHEN EntityName='Slime' THEN 'blockpets:slime'
  WHEN EntityName='SnowGolem' THEN 'blockpets:snow_golem'
  WHEN EntityName='Spider' THEN 'blockpets:spider'
  WHEN EntityName='Squid' THEN 'blockpets:squid'
  WHEN EntityName='Stray' THEN 'blockpets:stray'
  WHEN EntityName='Vex' THEN 'blockpets:vex'
  WHEN EntityName='Villager' THEN 'blockpets:villager'
  WHEN EntityName='Vindicator' THEN 'blockpets:vindicator'
  WHEN EntityName='Witch' THEN 'blockpets:witch'
  WHEN EntityName='Wither' THEN 'blockpets:wither'
  WHEN EntityName='WitherSkeleton' THEN 'blockpets:wither_skeleton'
  WHEN EntityName='WitherSkull' THEN 'blockpets:wither_skull'
  WHEN EntityName='Wolf' THEN 'blockpets:wolf'
  WHEN EntityName='Zombie' THEN 'blockpets:zombie'
  WHEN EntityName='ZombieHorse' THEN 'blockpets:zombie_horse'
  WHEN EntityName='ZombiePigman' THEN 'blockpets:zombie_pigman'
  WHEN EntityName='ZombieVillager' THEN 'blockpets:zombie_villager'
END;
-- #  }
-- #}
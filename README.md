# BlockPets

[![Join the chat at https://gitter.im/BlockHorizons/BlockPets](https://badges.gitter.im/BlockHorizons/BlockPets.svg)](https://gitter.im/BlockHorizons/BlockPets?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)<br><br>
An advanced pets plugin for PocketMine-MP, a server software for Minecraft PE servers in PHP. Implements highly customizable pets with interesting features.<br>
> Third party versions, forks or spoons of PocketMine-MP are **not** supported.
>
> Issues caused by other server softwares than PocketMine-MP will be closed immediately.

### Installation
Stable releases of BlockPets will be drafted at the Release tab with a pre-built phar file, and can be found on the Poggit website once released. Alternatively, you can grab a development build from the Poggit button below.
To install:
- Download one of the pre-built phar files from either releases or the Poggit button below.
- Drop the phar file in the plugins folder of your server.
- Restart the server, and enjoy.
[![Poggit-CI](https://poggit.pmmp.io/ci.shield/BlockHorizons/BlockPets/BlockPets)](https://poggit.pmmp.io/ci/BlockHorizons/BlockPets/BlockPets)<br><br>

### Translation
BlockPets has a language system so everybody can use the plugin in the language they prefer. You can change language to one of the available languages in the plugin.yml. Of course, not every language file exists. If you want to help translate, don't hesitate to add a new file and copy the english translations in it, then translate them. Any help is greatly appreciated.

### Features
- Pet spawning of most monster types and being able to remove them.
- Adjustable size, name, and player can be specified when spawning.
- Smooth pet movement.
- Pet defending system, attacking them or the owner makes the pet enraged.
- Angry pets that kill enemies get experience which they use to level up.
- Scaling health, size and attack damage per level.
- Pets have tiers, with speeds connected to those.
- Pets can be ridden by tapping them with a saddle.
- Multi-world pets, following you through worlds.
- Pets have inventories. Give pets an inventory by tapping them with a chest and open it with an empty hand.
- All pets are highly configurable in the [pet_properties](https://github.com/BlockHorizons/BlockPets/blob/master/resources/pet_properties.yml) file located in the plugin folder.
- Pets can sit on your head by tapping them while sneaking.

### Commands and Permissions
BlockPets has permissions for every pet and command, to wall off the permissions for players but still allow them to have at least some way to spawn pets. The most up to date list of permissions can be found in the [plugin.yml file](https://github.com/BlockHorizons/BlockPets/blob/master/plugin.yml)<br><br>

BlockPets contains a hand full of commands to modify, create and remove pets. These commands can be found using the /pet \<help\> command. This command will list all BlockPets commands, and can show other information.

### Feedback
We'd like to keep our issue tracker as clean as possible, so please take the following in consideration when opening a new issue in the issue tracker:
- If you have an issue with the plugin, check if the issue exists in the Issue tracker, and report it if not.
- If you'd like support or have a question about the plugin, please click the Gitter button under the title.
- If you have a great idea for a new feature or enhancement, please create a new issue in the Issue tracker.
- If you just want to have a talk, please click the Gitter button below the title.
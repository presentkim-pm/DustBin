<?php

declare(strict_types=1);

namespace blugin\dustbin;

use pocketmine\Player;
use pocketmine\command\{
  Command, PluginCommand, CommandExecutor, CommandSender
};
use pocketmine\plugin\PluginBase;
use blugin\dustbin\inventory\DustBinInventory;
use blugin\dustbin\lang\PluginLang;

class DustBin extends PluginBase implements CommandExecutor{

    /** @var DustBin */
    private static $instance = null;

    /** @return DustBin */
    public static function getInstance() : DustBin{
        return self::$instance;
    }

    /** @var PluginCommand */
    private $command = null;

    /** @var PluginLang */
    private $language;

    public function onLoad() : void{
        self::$instance = $this;
    }

    public function onEnable() : void{
        $dataFolder = $this->getDataFolder();
        if (!file_exists($dataFolder)) {
            mkdir($dataFolder, 0777, true);
        }
        $this->language = new PluginLang($this);

        if ($this->command !== null) {
            $this->getServer()->getCommandMap()->unregister($this->command);
        }
        $this->command = new PluginCommand($this->language->translate('commands.dustbin'), $this);
        $this->command->setPermission('dustbin.cmd');
        $this->command->setDescription($this->language->translate('commands.dustbin.description'));
        $this->command->setUsage($this->language->translate('commands.dustbin.usage'));
        if (is_array($aliases = $this->language->getArray('commands.dustbin.aliases'))) {
            $this->command->setAliases($aliases);
        }
        $this->getServer()->getCommandMap()->register('dustbin', $this->command);
    }

    /**
     * @param CommandSender $sender
     * @param Command       $command
     * @param string        $label
     * @param string[]      $args
     *
     * @return bool
     */
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if ($sender instanceof Player) {
            $sender->addWindow(new DustBinInventory());
        } else {
            $sender->sendMessage($this->language->translate('commands.generic.onlyPlayer'));
        }
        return true;
    }

    /**
     * @param string $name = ''
     *
     * @return PluginCommand
     */
    public function getCommand(string $name = '') : PluginCommand{
        return $this->command;
    }

    /**
     * @return PluginLang
     */
    public function getLanguage() : PluginLang{
        return $this->language;
    }

    /**
     * @return string
     */
    public function getSourceFolder() : string{
        $pharPath = \Phar::running();
        if (empty($pharPath)) {
            return dirname(__FILE__, 4) . DIRECTORY_SEPARATOR;
        } else {
            return $pharPath . DIRECTORY_SEPARATOR;
        }
    }
}
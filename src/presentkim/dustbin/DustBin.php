<?php

namespace presentkim\dustbin;

use pocketmine\command\PluginCommand;
use pocketmine\plugin\PluginBase;
use presentkim\dustbin\command\CommandListener;
use presentkim\dustbin\lang\PluginLang;

class DustBin extends PluginBase{

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
        if (self::$instance === null) {
            self::$instance = $this;
        }
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
        $this->command->setExecutor(new CommandListener($this));
        $this->command->setPermission('dustbin.cmd');
        $this->command->setDescription($this->language->translate('commands.dustbin.description'));
        $this->command->setUsage($this->language->translate('commands.dustbin.usage'));
        if (is_array($aliases = $this->language->getArray('commands.dustbin.aliases'))) {
            $this->command->setAliases($aliases);
        }
        $this->getServer()->getCommandMap()->register('dustbin', $this->command);
    }

    /**
     * @param string $name = ''
     *
     * @return PluginCommand
     */
    public function getCommand(string $name = '') : PluginCommand{
        return $this->command;
    }

    /** @param PluginCommand $command */
    public function setCommand(PluginCommand $command) : void{
        $this->command = $command;
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
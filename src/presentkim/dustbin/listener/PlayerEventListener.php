<?php

namespace presentkim\dustbin\listener;

use pocketmine\event\{
  Listener
};
use presentkim\dustbin\DustBinMain as Plugin;

class PlayerEventListener implements Listener{

    /** @var Plugin */
    private $owner = null;

    public function __construct(){
        $this->owner = Plugin::getInstance();
    }
}
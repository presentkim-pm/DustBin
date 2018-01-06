<?php

namespace presentkim\dustbin\inventory;

use pocketmine\block\Block;
use pocketmine\inventory\{
  BaseInventory, CustomInventory
};
use pocketmine\nbt\NetworkLittleEndianNBTStream;
use pocketmine\nbt\tag\{
  CompoundTag, IntTag, StringTag
};
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\Player;
use pocketmine\tile\Spawnable;
use function presentkim\dustbin\util\{
  createUpdateBlockPacket, createContainerOpenPacket, createBlockEntityDataPacket
};

class DustBinInventory extends CustomInventory{

    /** @var NetworkLittleEndianNBTStream|null */
    private static $nbtWriter = null;

    /** Vector3 */
    private $vec;

    /** @return string */
    public function getName() : string{
        return "DustBin";
    }

    /** @return int */
    public function getDefaultSize() : int{
        return 27;
    }

    /** @return int */
    public function getNetworkType() : int{
        return WindowTypes::CONTAINER;
    }

    /**
     * @param Player $who
     */
    public function onOpen(Player $who) : void{
        $this->vec = $who->floor()->add(0, 5, 0);

        if (self::$nbtWriter === null) {
            self::$nbtWriter = new NetworkLittleEndianNBTStream();
        }
        self::$nbtWriter->setData(new CompoundTag("", [
          new StringTag("id", "Chest"),
          new IntTag("x", $this->vec->x),
          new IntTag("y", $this->vec->y),
          new IntTag("z", $this->vec->z),
          new StringTag("CustomName", "DustBin"),
        ]));

        $who->sendDataPacket(createUpdateBlockPacket($this->vec, Block::CHEST));
        $who->sendDataPacket(createBlockEntityDataPacket($this->vec, self::$nbtWriter->write()));
        $who->sendDataPacket(createContainerOpenPacket($this->vec, $who->getWindowId($this), $this->getNetworkType()));
        $this->sendContents($who);

        BaseInventory::onOpen($who);
    }

    public function onClose(Player $who) : void{
        BaseInventory::onClose($who);
        $block = $who->getLevel()->getBlock($this->vec);
        $who->sendDataPacket(createUpdateBlockPacket($this->vec, $block->getId(), $block->getDamage()));
        $tile = $who->getLevel()->getTile($this->vec);
        if ($tile instanceof Spawnable) {
            $who->sendDataPacket($tile->createSpawnPacket());
        }
        $this->clearAll();
    }
}
<?php

/*
 *
 *  ____                           _   _  ___
 * |  _ \ _ __ ___  ___  ___ _ __ | |_| |/ (_)_ __ ___
 * | |_) | '__/ _ \/ __|/ _ \ '_ \| __| ' /| | '_ ` _ \
 * |  __/| | |  __/\__ \  __/ | | | |_| . \| | | | | | |
 * |_|   |_|  \___||___/\___|_| |_|\__|_|\_\_|_| |_| |_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the MIT License. see <https://opensource.org/licenses/MIT>.
 *
 * @author  PresentKim (debe3721@gmail.com)
 * @link    https://github.com/PresentKim
 * @license https://opensource.org/licenses/MIT MIT License
 *
 *   (\ /)
 *  ( . .) ♥
 *  c(")(")
 */

declare(strict_types=1);

namespace kim\present\dustbin\inventory;

use kim\present\dustbin\DustBin;
use pocketmine\block\{
	Block, BlockFactory
};
use pocketmine\inventory\{
	CustomInventory
};
use pocketmine\math\Vector3;
use pocketmine\nbt\NetworkLittleEndianNBTStream;
use pocketmine\nbt\tag\{
	CompoundTag, IntTag, StringTag
};
use pocketmine\network\mcpe\protocol\{
	BlockEntityDataPacket, UpdateBlockPacket
};
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\Player;
use pocketmine\tile\{
	Chest, Spawnable, Tile
};

class DustBinInventory extends CustomInventory{
	public function __construct(){
		parent::__construct(new Vector3(), [], $this->getDefaultSize(), null);
	}

	/**
	 * @param Player $who
	 */
	public function onOpen(Player $who) : void{
		$this->holder = $who->subtract(0, 3, 0)->floor();
		if($this->holder->y < 0){
			$this->holder->y = 0;
		}

		$pk = new UpdateBlockPacket();
		$pk->blockRuntimeId = BlockFactory::toStaticRuntimeId(Block::CHEST);
		$pk->x = $this->holder->x;
		$pk->y = $this->holder->y;
		$pk->z = $this->holder->z;
		$pk->flags = UpdateBlockPacket::FLAG_NONE;
		$who->sendDataPacket($pk);


		$pk = new BlockEntityDataPacket();
		$pk->x = $this->holder->x;
		$pk->y = $this->holder->y;
		$pk->z = $this->holder->z;
		$pk->namedtag = (new NetworkLittleEndianNBTStream())->write(new CompoundTag("", [
			new StringTag(Tile::TAG_ID, Tile::CHEST),
			new StringTag(Chest::TAG_CUSTOM_NAME, DustBin::getInstance()->getLanguage()->translate("dustbin.name")),
			new IntTag(Tile::TAG_X, $this->holder->x),
			new IntTag(Tile::TAG_Y, $this->holder->y),
			new IntTag(Tile::TAG_Z, $this->holder->z)
		]));
		$who->sendDataPacket($pk);

		parent::onOpen($who);
	}

	/**
	 * @param Player $who
	 */
	public function onClose(Player $who) : void{
		$block = $who->getLevel()->getBlock($this->holder);

		$pk = new UpdateBlockPacket();
		$pk->x = $this->holder->x;
		$pk->y = $this->holder->y;
		$pk->z = $this->holder->z;
		$pk->blockRuntimeId = BlockFactory::toStaticRuntimeId($block->getId(), $block->getDamage());
		$pk->flags = UpdateBlockPacket::FLAG_NONE;
		$who->sendDataPacket($pk);

		$tile = $who->getLevel()->getTile($this->holder);
		if($tile instanceof Spawnable){
			$who->sendDataPacket($tile->createSpawnPacket());
		}
		$this->clearAll();

		parent::onClose($who);
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "DustBin";
	}

	/**
	 * @return int
	 */
	public function getDefaultSize() : int{
		return 27;
	}

	/**
	 * @return int
	 */
	public function getNetworkType() : int{
		return WindowTypes::CONTAINER;
	}
}

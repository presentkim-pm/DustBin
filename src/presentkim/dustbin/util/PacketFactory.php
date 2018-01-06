<?php

namespace presentkim\dustbin\util;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\{
  UpdateBlockPacket, ContainerOpenPacket, BlockEntityDataPacket
};

/**
 * @param Vector3 $vec
 * @param int     $blockId
 * @param int     $blockData
 *
 * @return UpdateBlockPacket
 */
function createUpdateBlockPacket(Vector3 $vec, int $blockId = 0, int $blockData = 0) : UpdateBlockPacket{
    $pk = new UpdateBlockPacket();
    $pk->x = $vec->x;
    $pk->y = $vec->y;
    $pk->z = $vec->z;
    $pk->blockId = $blockId;
    $pk->blockData = $blockData;
    return $pk;
}

/**
 * @param Vector3 $vec
 * @param string  $namedtag
 *
 * @return BlockEntityDataPacket
 */
function createBlockEntityDataPacket(Vector3 $vec, string $namedtag) : BlockEntityDataPacket{
    $pk = new BlockEntityDataPacket();
    $pk->x = $vec->x;
    $pk->y = $vec->y;
    $pk->z = $vec->z;
    $pk->namedtag = $namedtag;
    return $pk;
}

/**
 * @param Vector3 $vec
 * @param int     $windowId
 * @param int     $type
 * @param int     $entityUniqueId
 *
 * @return ContainerOpenPacket
 */
function createContainerOpenPacket(Vector3 $vec, int $windowId, int $type, int $entityUniqueId = -1) : ContainerOpenPacket{
    $pk = new ContainerOpenPacket();
    $pk->x = $vec->x;
    $pk->y = $vec->y;
    $pk->z = $vec->z;
    $pk->windowId = $windowId;
    $pk->type = $type;
    $pk->entityUniqueId = $entityUniqueId;
    return $pk;
}
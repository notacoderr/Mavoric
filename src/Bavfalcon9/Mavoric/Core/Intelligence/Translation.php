<?php

/***
 *      __  __                       _      
 *     |  \/  |                     (_)     
 *     | \  / | __ ___   _____  _ __ _  ___ 
 *     | |\/| |/ _` \ \ / / _ \| '__| |/ __|
 *     | |  | | (_| |\ V / (_) | |  | | (__ 
 *     |_|  |_|\__,_| \_/ \___/|_|  |_|\___|
 *                                          
 *   THIS CODE IS TO NOT BE REDISTRUBUTED
 *   @author MavoricAC
 *   @copyright Everything is copyrighted to their respective owners.
 *   @link https://github.com/Olybear9/Mavoric                                  
 */

namespace Bavfalcon9\Mavoric\Core\Intelligence;

use pocketmine\event\block\{
    BlockBreakEvent, BlockBurnEvent, BlockEvent, BlockFormEvent, BlockGrowEvent, BlockPlaceEvent, BlockSpreadEvent,
    BlockUpdateEvent, LeavesDecayEvent, SignChangeEvent
};
use pocketmine\event\entity\{
    EntityArmorChangeEvent, EntityBlockChangeEvent, EntityCombustByBlockEvent, EntityCombustByEntityEvent, EntityCombustEvent,
    EntityDamageByBlockEvent, EntityDamageByChildEntityEvent, EntityDamageByEntityEvent, EntityDamageEvent, EntityDeathEvent,
    EntityDespawnEvent, EntityEffectAddEvent, EntityEffectEvent, EntityEffectRemoveEvent, EntityEvent, EntityExplodeEvent,
    EntityInventoryChangeEvent, EntityLevelChangeEvent, EntityMotionEvent, EntityRegainHealthEvent, EntityShootBowEvent, 
    EntitySpawnEvent, EntityTeleportEvent, ExplosionPrimeEvent, ItemDespawnEvent, ItemSpawnEvent, ProjectileHitBlockEvent,
    ProjectileHitEntityEvent, ProjectileHitEvent, ProjectileLaunchEvent
};
use pocketmine\event\inventory\{
    CraftItemEvent, FurnaceBurnEvent, FurnaceSmeltEvent, InventoryCloseEvent, InventoryEvent, InventoryOpenEvent,
    InventoryPickupArrowEvent, InventoryPickupItemEvent, InventoryTransactionEvent
};
use pocketmine\event\level\{
    ChunkEvent, ChunkLoadEvent, ChunkPopulateEvent, ChunkUnloadEvent, LevelEvent, LevelInitEvent,
    LevelLoadEvent, LevelSaveEvent, LevelUnloadEvent, SpawnChangeEvent
};
use pocketmine\event\player\{
    PlayerAchievementAwardedEvent, PlayerAnimationEvent, PlayerBedEnterEvent,
    PlayerBedLeaveEvent, PlayerBlockPickEvent, PlayerBucketEmptyEvent, PlayerBucketEvent,
    PlayerBucketFillEvent, PlayerChangeSkinEvent, PlayerChatEvent, PlayerCommandPreprocessEvent,
    PlayerCreationEvent, PlayerDataSaveEvent, PlayerDeathEvent, PlayerDropItemEvent, PlayerEditBookEvent,
    PlayerEvent, PlayerExhaustEvent, PlayerExperienceChangeEvent, PlayerExhauseChangeEvent, PlayerGameModeChangeEvent,
    PlayerInteractEvent, PlayerItemConsumeEvent, PlayerItemHeldEvent, PlayerJoinEvent, PlayerJumpEvent, PlayerKickEvent,
    PlayerLoginEvent, PlayerPreLoginEvent, PlayerQuitEvent, PlayerRespawnEvent, PlayerToggleFlightEvent, PlayerToggleSneakEvent,
    PlayerToggleSprintEvent, PlayerTransferEvent
};
use pocketmine\event\plugin\{
    PluginDisableEvent, PluginEnableEvent, PluginEvent
};
use pocketmine\event\server\{
    CommandEvent, DataPacketRecieveEvent, DataPacketSendEvent,
    LowMemoryEvent, NetworkInterfaceCrashEvent, NetworkInterfaceEvent,
    NetworkInterfaceRegisterEvent, NetworkInterfaceUnregisterEvent,
    QueryRegenerateEvent, RemoteServerCommandEvent, ServerCommandEvent,
    ServerEvent, UpdateNotifyEvent
};

class Translation {
    public function __construct() {

    }
 
    public function eventToJSON($event) : ?String {
        
    }

}
CREATE TABLE %dbpre%weapons (
  wp_num smallint(5) unsigned NOT NULL auto_increment,
  wp_type varchar(60) NOT NULL default '',
  wp_desc varchar(40) NOT NULL default '',
  wp_weaptype tinyint(3) unsigned NOT NULL default 0,
  wp_secondary tinyint(3) unsigned NOT NULL default 0,
  wp_frags int(11) NOT NULL default 0,
  wp_kills int(10) unsigned NOT NULL default 0,
  wp_deaths int(10) unsigned NOT NULL default 0,
  wp_suicides int(10) unsigned NOT NULL default 0,
  wp_nwsuicides int(10) unsigned NOT NULL default 0,
  wp_fired int(10) unsigned NOT NULL default 0,
  wp_hits int(10) unsigned NOT NULL default 0,
  wp_damage bigint(19) unsigned NOT NULL default 0,
  wp_chkills mediumint(8) unsigned NOT NULL default 0,
  wp_chkills_plr mediumint(8) unsigned NOT NULL default 0,
  wp_chkills_gms mediumint(8) unsigned NOT NULL default 0,
  wp_chkills_tm bigint(19) unsigned NOT NULL default 0,
  wp_chdeaths mediumint(8) unsigned NOT NULL default 0,
  wp_chdeaths_plr mediumint(8) unsigned NOT NULL default 0,
  wp_chdeaths_gms mediumint(8) unsigned NOT NULL default 0,
  wp_chdeaths_tm bigint(19) unsigned NOT NULL default 0,
  wp_chdeathshld mediumint(8) unsigned NOT NULL default 0,
  wp_chdeathshld_plr mediumint(8) unsigned NOT NULL default 0,
  wp_chdeathshld_gms mediumint(8) unsigned NOT NULL default 0,
  wp_chdeathshld_tm bigint(19) unsigned NOT NULL default 0,
  wp_chsuicides mediumint(8) unsigned NOT NULL default 0,
  wp_chsuicides_plr mediumint(8) unsigned NOT NULL default 0,
  wp_chsuicides_gms mediumint(8) unsigned NOT NULL default 0,
  wp_chsuicides_tm bigint(19) unsigned NOT NULL default 0,
  wp_chkillssg mediumint(8) unsigned NOT NULL default 0,
  wp_chkillssg_plr mediumint(8) unsigned NOT NULL default 0,
  wp_chkillssg_tm bigint(19) unsigned NOT NULL default 0,
  wp_chkillssg_map mediumint(8) unsigned NOT NULL default 0,
  wp_chkillssg_dt datetime NOT NULL default '0000-00-00 00:00:00',
  wp_chdeathssg mediumint(8) unsigned NOT NULL default 0,
  wp_chdeathssg_plr mediumint(8) unsigned NOT NULL default 0,
  wp_chdeathssg_tm bigint(19) unsigned NOT NULL default 0,
  wp_chdeathssg_map mediumint(8) unsigned NOT NULL default 0,
  wp_chdeathssg_dt datetime NOT NULL default '0000-00-00 00:00:00',
  wp_chdeathshldsg mediumint(8) unsigned NOT NULL default 0,
  wp_chdeathshldsg_plr mediumint(8) unsigned NOT NULL default 0,
  wp_chdeathshldsg_tm bigint(19) unsigned NOT NULL default 0,
  wp_chdeathshldsg_map mediumint(8) unsigned NOT NULL default 0,
  wp_chdeathshldsg_dt datetime NOT NULL default '0000-00-00 00:00:00',
  wp_chsuicidessg mediumint(8) unsigned NOT NULL default 0,
  wp_chsuicidessg_plr mediumint(8) unsigned NOT NULL default 0,
  wp_chsuicidessg_tm bigint(19) unsigned NOT NULL default 0,
  wp_chsuicidessg_map mediumint(8) unsigned NOT NULL default 0,
  wp_chsuicidessg_dt datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY wp_num (wp_num),
  UNIQUE KEY wp_type (wp_type),
  KEY wp_weaptype (wp_weaptype)
) Engine=MyISAM;

INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('None','None');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('TransLauncher','Translocator');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeTeleFrag','Translocator');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeTelefragged','Telefragged');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('ShieldGun','Shield Gun');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeShieldImpact','Shield Gun');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('AssaultRifle','Assault Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeAssaultBullet','Assault Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('DamTypeAssaultGrenade','Assault Rifle',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('LinkGun','Link Gun');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeLinkPlasma','Link Gun');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('DamTypeLinkShaft','Link Gun',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('ShockRifle','Shock Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeShockBeam','Shock Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('DamTypeShockBall','Shock Rifle',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('DamTypeShockCombo','Shock Rifle',2);
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('BioRifle','Bio Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeBioGlob','Bio Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('Minigun','Minigun');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeMinigunBullet','Minigun');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('DamTypeMinigunAlt','Minigun',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('FlakCannon','Flak Cannon');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeFlakChunk','Flak Cannon');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('DamTypeFlakShell','Flak Cannon',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('FlakDeath','Flak Cannon',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('RocketLauncher','Rocket Launcher');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeRocket','Rocket Launcher');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('DamTypeRocketHoming','Rocket Launcher',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('RocketDeath','Rocket Launcher',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('LightningGun','Lightning Gun');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('SniperRifle','Lightning Gun');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeSniperShot','Lightning Gun');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('DamTypeSniperHeadShot','Lightning Gun',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('Redeemer','Redeemer');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeRedeemer','Redeemer');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('RedeemerDeath','Redeemer');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeIonBlast','Ion Cannon');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('SuperShockRifle','Super Shock Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamSuperShockRifle','Super Shock Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeSuperShockBeam','Super Shock Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('ZoomSuperShockRifle','Super Shock Rifle',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamZoomSuperShockRifle','Super Shock Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('BallLauncher','Ball Launcher');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamBallLauncher','Ball Launcher');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('fell','Fell');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('Crushed','Crushed');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('FellLava','Fell Into Lava');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('Suicided','Suicided');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('Gibbed','Gibbed');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('Drowned','Drowned');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('Corroded','Corroded');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('SwamTooFar','Swam Too Far');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('Depressurized','Depressurized');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('shredded','Shredded');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('jolted','Jolted');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('impact','Impact');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('exploded','Exploded');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('shot','Shot');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('ClassicDamTypeEnforcer','Classic Enforcer');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('ClassicDamTypeSniperShot','Classic Sniper Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('ClassicDamTypeSniperHeadShot','Classic Sniper Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('DamageType','Unknown Weapon',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('TeamChange','Team Change');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('DamTypeInstaVape','Super Shock Rifle',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('Painter','Ion Cannon');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('Colt','Colt Peacemaker');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DualColt','Colt Peacemaker');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeColt','Colt Peacemaker');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('DamTypeColtHeadshot','Colt Peacemaker',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('ArenaColt','Colt Peacemaker');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('ONSAVRiL','AVRiL');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeONSAVRiLRocket','AVRiL');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('ONSMineLayer','Mine Layer');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeONSMine','Mine Layer');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('ONSGrenadeLauncher','Grenade Launcher');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeONSGrenade','Grenade Launcher');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('ClassicSniperRifle','Sniper Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeClassicSniper','Sniper Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('DamTypeClassicHeadshot','Sniper Rifle',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('ZoomSuperShockBeamDamage','Super Shock Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('ConvoyGibbed','Gibbed by Convoy');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('Burned','Burned');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('ONSPainter','Target Painter');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeExploBarrel','Exploding Barrel');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeIonVolume','Orbital Ion Satellite');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DB_Hammer','DeathBall Hammer');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('Ripper','Ripper');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeRipper','Ripper');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeRipperHeadshot','Ripper');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('RipperAltDeath','Ripper',1);
#
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('Impact Hammer','Impact Hammer');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('Enforcer','Enforcer');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('Double Enforcer','Double Enforcer');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('Double Enforcers','Double Enforcer');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('Chainsaw','Chainsaw');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('GES Bio Rifle','GES Bio Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('Pulse Gun','Pulse Gun');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('Shock Rifle','ASMD Shock Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('Enhanced Shock Rifle','Enhanced Shock Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('Flak Cannon','Flak Cannon');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('Rocket Launcher','Rocket Launcher');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('Sniper Rifle','Sniper Rifle');
#
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UT99 Translocator','UT99 Translocator');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UT99 Impact Hammer','UT99 Impact Hammer');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UT99 Enforcer','UT99 Enforcer');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UT99 Double Enforcer','UT99 Double Enforcer');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UT99 Double Enforcers','UT99 Double Enforcer');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UT99 Chainsaw','UT99 Chainsaw');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UT99 GES Bio Rifle','UT99 GES Bio Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UT99 Pulse Gun','UT99 Pulse Gun');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UT99 ASMD Shock Rifle','UT99 ASMD Shock Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UT99 Enhanced Shock Rifle','UT99 Enhanced Shock Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UT99 Ripper','UT99 Ripper');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UT99 Minigun','UT99 Minigun');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UT99 Flak Cannon','UT99 Flak Cannon');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UT99 Rocket Launcher','UT99 Rocket Launcher');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UT99 Sniper Rifle','UT99 Sniper Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UT99 Redeemer','UT99 Redeemer');
#
#
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('DamTypeONSVehicle','Reckless Driving',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('DamTypeDestroyedVehicleRoadKill','Destroyed Vehicle',1,4);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('DamTypeONSVehicleExplosion','Vehicle Explosion',1);
#
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('ONSAttackCraft','Raptor',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('DamTypeAttackCraftPlasma','Raptor',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('DamTypeAttackCraftMissle','Raptor',1,1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('DamTypeAttackCraftRoadkill','Raptor',1,4);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('DamTypeAttackCraftPancake','Raptor',1,4);
#
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('ONSHoverBike','Manta',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('DamTypeHoverBikePlasma','Manta',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('DamTypeHoverBikeHeadshot','Manta',1,4);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('DamTypeHoverBikePancake','Manta',1,4);
#
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('ONSRV','Scorpion',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('DamTypeONSWeb','Scorpion',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('DamTypeONSRVBlade','Scorpion',1,1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('DamTypeRVRoadkill','Scorpion',1,4);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('DamTypeRVPancake','Scorpion',1,4);
#
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('ONSPRV','HellBender',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('DamTypePRVRoadkill','HellBender',1,4);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('DamTypePRVPancake','HellBender',1,4);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('ONSPRVSideGunPawn','HellBender Side Turret',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('DamTypeSkyMine','HellBender Side Turret',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('DamTypePRVLaser','HellBender Side Turret',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('DamTypePRVCombo','HellBender Side Turret',1,1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('ONSPRVRearGunPawn','HellBender Rear Turret',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('DamTypeChargingBeam','HellBender Rear Turret',1);
#
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('ONSHoverTank','Goliath',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('DamTypeTankShell','Goliath',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('DamTypeTankRoadkill','Goliath',1,4);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('DamTypeTankPancake','Goliath',1,4);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('ONSTankSecondaryTurretPawn','Goliath Minigun Turret',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('DamTypeONSChainGun','Goliath Minigun Turret',1);
#
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('DamTypeIonTankBlast','Ion Plasma Tank',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('DamTypeIonTankRoadkill','Ion Plasma Tank',1,4);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('DamTypeIonTankPancake','Ion Plasma Tank',1,4);
#
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('ONSMobileAssaultStation','Leviathan',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('DamTypeMASRocket','Leviathan',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('DamTypeMASCannon','Leviathan',1,1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('DamTypeMASPancake','Leviathan',1,4);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('DamTypeMASRoadkill','Leviathan',1,4);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('DamTypeMASPlasma','Leviathan Side Gunner',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('ONSMASSideGunPawn','Leviathan Side Gunner',1);
#
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('Weapon_SpaceFighter','Space Fighter',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('ASVehicle_SpaceFighter','Space Fighter',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('Weapon_SpaceFighter_Skaarj','Skaarj Space Fighter',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('DamTypeSpaceFighterLaser','Space Fighter',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('DamTypeSpaceFighterLaser_Skaarj','Skaarj Space Fighter',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('DamTypeSpaceFighterMissile','Space Fighter',1,1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('DamTypeSpaceFighterMissileSkaarj','Skaarj Space Fighter',1,1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('ONSBomber','DragonFly',1);
#
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('ONSShockTank','Paladin',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('DamTypeShockTankShockBall','Paladin',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('DamTypeShockTankProximityExplosion','Paladin',1,1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('DamTypeShockTankRoadkill','Paladin',1,4);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('DamTypeShockTankPancake','Paladin',1,4);
#
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('ONSArtillery','SPMA',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('DamTypeArtilleryShell','SPMA',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('DamTypeArtilleryLaser','SPMA',1,1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('DamTypeArtilleryCombo','SPMA',1,2);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('ONSArtillerySideGunPawn','SPMA Turret',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('DamTypeArtilleryRoadkill','SPMA',1,4);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('DamTypeArtilleryPancake','SPMA',1,4);
#
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('ONSDualAttackCraft','Cicada',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('DamTypeONSCicadaRocket','Cicada',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('ONSDualACGatlingGunPawn','Cicada Turret',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('DamTypeONSCicadaLaser','Cicada Turret',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('ONSDualAttackCraftRoadkill','Cicada',1,4);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('DamTypeDualAttackCraftRoadkill','Cicada',1,4);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('ONSDualAttackCraftPancake','Cicada',1,4);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('DamTypeDualAttackCraftPancake','Cicada',1,4);
#
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('DamTypeAtakapa50Cal','Atakapa',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('DamTypeAtakapaMissle','Atakapa',1,1);
#INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('DamTypeAttackCraftRoadkill','Atakapa',1,4);
#INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('DamTypeAttackCraftPancake','Atakapa',1,4);
#
#
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('ASTurret','Turret',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('DamTypeTurretBeam','Energy Turret',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('ONSManualGunPawn','Energy Turret',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('Weapon_Turret_Minigun','Minigun Turret',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('ASTurret_Minigun','Minigun Turret',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('DamTypeMinigunTurretBullet','Minigun Turret',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('DamTypeLinkTurretPlasma','Link Turret',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('DamTypeLinkTurretBeam','Link Turret',1,1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('ASTurret_IonCannon','Ion Cannon',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('DamTypeIonCannonBlast','RobotFactory Ion Cannon',2);
#
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('VCTFManualGunPawn','Energy Turret',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('OLVCTFManualGunPawn','Energy Turret',1);
#
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('Weapon_Sentinel','Sentinel',2);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('ASVehicle_Sentinel','Sentinel',2);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('ASVehicle_Sentinel_Ceiling','Sentinel',2);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('ASVehicle_Sentinel_Floor','Sentinel',2);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('DamTypeBallTurretPlasma','Mothership Plasma Turret',2);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('DamTypeSentinelLaser','Sentinel Laser',2);
#
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('Behemoth','Behemoth',3);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('Brute','Brute',3);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('DamTypeBruteRocket','Brute',3);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('EliteKrall','Elite Krall',3);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('FireSkaarj','Fire Skaarj',3);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('GasBag','Gasbag',3);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('DamTypeBelch','Gasbag',3);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('IceSkaarj','Ice Skaarj',3);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('Krall','Krall',3);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('DamTypeKrallBolt','Krall',3);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('Manta','Manta Ray',3);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('RazorFly','Razorfly',3);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('Skaarj','Skaarj',3);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('DamTypeSkaarjProj','Skaarj',3);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('SkaarjPupae','Skaarj Pupae',3);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('Warlord','Warlord',3);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('DamTypeWarlordRocket','Warlord',3);
#
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('RW_Damage','RPG Damage');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('RW_Protection','Protection Weapon');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('RW_Force','Force Weapon');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('RW_Piercing','Piercing Weapon');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('RW_Penetrating','Penetrating Weapon');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('RW_Infinity','Weapon of Infinity');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('RW_NoMomentum','Sturdy Weapon');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('RW_Luck','Luck Weapon');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('RW_Poison','Poison');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('RW_Energy','Energy/Draining Weapon');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('RPGWeapon','RPG Weapon');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('RPGLinkGun','Link Gun');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeRetaliation','Retaliation');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypePoison','Poison');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeUltima','Ultima');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeLightningRod','Lightning Rod');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('MeleeDamage','Melee Damage');
#
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('Soar','Soar');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeSoar','Soar');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('DamTypeSoarGrenade','Soar',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('Parasite','Parasite');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeParasite','Parasite');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('DamTypeParasiteAlt','Parasite',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('Crispe','Crispe');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeCrispe','Crispe');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('DamTypeCrispeFire','Crispe',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('CFX','CFX');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeCFX','CFX');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeCFXHeadShot','CFX');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('Fyrian','Fyrian Cannon');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeFyrian','Fyrian Cannon');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('FireChucker','FireChucker');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeFireChucker','FireChucker');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('DamTypeFireChuckerFireBall','FireChucker',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('PepperPot','PepperPot');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypePepperPot','PepperPot');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('Helios','Helios');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeHelios','Helios');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeHeliosHeadShot','Helios');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('PIC','Personal Ion Cannon');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypePIC','Personal Ion Cannon');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('Disturber','Transdimensional Disturber');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('DamTypeDisturberZap','Transdimensional Disturber',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeDisturberImplosion','Transdimensional Disturber');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UT99SASniperRifle','UT99SASniperRifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamSA2K4BodyShot','UT99SASniperRifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('DamSA2K4HeadShot','UT99SASniperRifle',1);
#
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPSniperRifle','XMP Sniper Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPSniperDam','XMP Sniper Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPPistol','XMP Pistol');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPPistolDam','XMP Pistol');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPEnergyRifle','XMP Shock Lance');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPGrenadeLauncherLight','XMP Grenade Launcher (Light)');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPDamPhysical','XMP Grenade Launcher (Light)');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPHealthPacks','XMP Health Pack');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPAssaultRifle','XMP Assault Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPAssaultDam','XMP Assault Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPShotgun','XMP Shotgun');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPShotgunDam','XMP Shotgun');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('XMPDamThermalExplosiveRound','XMP Shotgun',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPGrenadeLauncherMedium','XMP Grenade Launcher (Medium)');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPDamEMP','XMP Grenade Launcher (Medium)');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('XMPDamBiologicalGas','XMP Grenade Launcher (Medium)',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPAutoTurretDeploy','XMP Auto Turret');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPAutoTurretDam','XMP Auto Turret');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPRocketTurretDeploy','XMP Rocket Turret');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPRocketTurretDam','XMP Rocket Turret');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPFieldGeneratorDeploy','XMP Field Generator');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPEnergyPacks','XMP Energy Pack');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPRocketLauncher','XMP Rocket Launcher');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPRocketDam','XMP Rocket Launcher');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPFlameThrower','XMP Flamethrower');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPDamThermalFlaming','XMP Flamethrower');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPGrenadeLauncherHeavy','XMP Grenade Launcher (Heavy)');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPDamThermal','XMP Grenade Launcher (Heavy)');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPLandMineDeploy','XMP Flamethrower');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPSmartLandMineDeploy','XMP Land Mine');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPDamLandMine','XMP Land Mine');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPSmartLaserTripMineDeploy','XMP Laser Trip Mine');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPLaserTripMineDeploy','XMP Laser Trip Mine');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPAmmoPacks','XMP Ammo Pack');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPTransLocator','Translocator');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPBioRifle','Bio Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPSuperShockRifle','Super Shock Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPMinigun','Minigun');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPMineLayer','Mine Layer');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMP2k4GrenadeLauncher','Grenade Launcher');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPLinkGun','Link Gun');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPShockRifle','Shock Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPFlakCannon','Flak Cannon');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPAVRiL','AVRiL');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMP2k4RocketLauncher','Rocket Launcher');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPClassicSniperRifle','Sniper Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPLightningGun','Lightning Gun');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPJetPainter','XMP Jet Painter');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPDamImpact','XMP Impact');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPMortarDam','XMP Mortar');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPRoundhouseTurretGun','XMP Roundhouse Turret');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPRoundhouseTurretDam','XMP Roundhouse Turret');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('XMPGardenTurretGun','XMP Garden Turret');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('XMPRaptorTurretDam','XMP Raptor Turret',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('XMPWingedTurretDam','XMP Winged Turret',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('XMPJuggernautCannonDam','XMP Juggernaut Cannon',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('XMPVehicularManslaughterDam','XMP Vehicular Manslaughter',1,4);
#
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('beretta','Beretta');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('damtypeberetta','Beretta');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('damtypeberretahs','Beretta',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('bubblegun','Bubble Gun');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('damtypebubble','Bubble Gun');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('damtypebubblealt','Bubble Gun',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('reaper','Reaper');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('damtypereaper','Reaper');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('DamTypereaperhs','Reaper',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('Bfg','BFG');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeBfg','BFG');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('DamTypeBfgFire','BFG',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('FleshBombRifle','FleshBomb Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeFleshbombrifle','FleshBomb Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('MoverRifle','I.A.M. (Mover)');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('damMoverRifle','I.A.M. (Mover)');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('Python','.357 Magnum Python');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('damtypepython','.357 Magnum Python');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('damtypepythonhs','.357 Magnum Python',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('QSG','QSG');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamQSG','QSG');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('AirBlast','Airblast');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('damAirblast','Airblast');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('Phasor','Phasor');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypePhasorBeam','Phasor');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('Freezer','Freezer');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('damFreezer','Freezer');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('railgun','Railgun');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeRailgun','Railgun');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('DamTypeRailgunHS','Railgun',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DollBomb','Dollbomb');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeNuke','Dollbomb');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('katana','Katana');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('damtypekatana','Katana');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('Grenadelauncher','Grenade Launcher');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('damtypegrenade','Grenade Launcher');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('damtypegrenadeflying','Grenade Launcher',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('damtypegrenadegas','Grenade Launcher',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('damtypegrenadehive','Grenade Launcher',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('damtypegrenadesticky','Grenade Launcher',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('Howitzer','Howitzer Lite');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('quicksilver','Quick Silver');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeTomahawk','Tomahawk');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeHellsaw','Hellsaw');
#
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('OMFG','OMFG');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DamTypeOMFG','OMFG');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('DamRanOver','Ran Over',1,4);
#
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('nWeapon_Priest','Neotokyo Priest');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('nDamTypePriestBullet','Neotokyo Priest');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('nDamTypeHeadshot','Neotokyo Priest',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('nWeapon_FragGrenade','Neotokyo Thermite Grenade');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('nWeapon_FragGrenadeFour','Neotokyo Thermite Grenade');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('nDamTypeFragGrenade','Neotokyo Thermite Grenade');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('nWeapon_SD13G','Neotokyo SD13G');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('nDamTypeSD13GBullet','Neotokyo SD13G');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('nDamTypeSD13Grenade','Neotokyo SD13G',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('nWeapon_BP22','Neotokyo BP22');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('nDamTypeBP22Bullet','Neotokyo BP22');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('nWeapon_ID3','Neotokyo ID3');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('nDamTypeID3Bullet','Neotokyo ID3');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('nWeapon_ID3Secondary','Neotokyo ID3',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('nWeapon_Tachi','Neotokyo Tachi');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('nDamTypeTachiBullet','Neotokyo Tachi');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('nWeapon_UBR','Neotokyo UBR-50');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('nDamTypeUBRBullet','Neotokyo UBR-50');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('nWeapon_CX','Neotokyo CX');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('nDamTypeCXBullet','Neotokyo CX');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('nWeapon_Jitte','Neotokyo Jitte');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('nDamTypeJitteBullet','Neotokyo Jitte');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('nWeapon_PZ252MG','Neotokyo PZ252MG');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('nDamTypePZ252MGBullet','Neotokyo PZ252MG');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('nWeapon_SD11','Neotokyo SD11');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('nDamTypeSD11Bullet','Neotokyo SD11');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('nWeapon_Shotgun','Neotokyo Shotgun');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('nDamTypeShotgunShell','Neotokyo Shotgun');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('Mine Layer','Neotokyo Smoke/Mines');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('nWeapon_SmokeGrenade','Neotokyo Smoke/Mines');
#
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTWeap_ImpactHammer','Impact Hammer');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTDmgType_ImpactHammer','Impact Hammer');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTWeap_Enforcer','Enforcer');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTDmgType_Enforcer','Enforcer');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTDmgType_DualEnforcer','Dual Enforcers');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTDmgType_Grenade','Grenade');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTWeap_BioRifle','Bio Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTDmgType_BioGoo','Bio Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTWeap_BioRifle_Content','Bio Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('UTDmgType_BioGoo_Charged','Bio Rifle',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('UTDmgType_BioGooGib','Bio Rifle',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTWeap_LinkGun','Link Gun');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTDmgType_LinkPlasma','Link Gun');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('UTDmgType_LinkBeam','Link Gun',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTWeap_ShockRifle','Shock Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTDmgType_ShockPrimary','Shock Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTWeap_InstagibRifle','InstaGib Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('UTDmgType_ShockBall','Shock Rifle',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('UTDmgType_ShockCombo','Shock Rifle',2);
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTWeap_Stinger','Stinger Minigun');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTDmgType_StingerBullet','Stinger Minigun');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('UTDmgType_StingerShard','Stinger Minigun',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTWeap_FlakCannon','Flak Cannon');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTDmgType_FlakShard','Flak Cannon');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('UTDmgType_FlakShell','Flak Cannon',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTWeap_RocketLauncher','Rocket Launcher');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTDmgType_Rocket','Rocket Launcher');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('UTDmgType_SeekingRocket','Rocket Launcher',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTWeap_SniperRifle','Sniper Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTDmgType_SniperPrimary','Sniper Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('UTDmgType_SniperHeadShot','Sniper Rifle',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTWeap_Redeemer','Redeemer');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTDmgType_Redeemer','Redeemer');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTWeap_Redeemer_Content','Redeemer');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTDmgType_Telefrag','Telefrag');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTWeap_Translocator','Translocator');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTDmgType_FailedTranslocation','Translocator');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTWeap_Avril','AVRiL');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTWeap_Avril_Content','Avril');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTDmgType_AvrilRocket','Avril');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTDmgType_SpiderMine','Spider Mine');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('UTDmgType_SpiderMineDirectHit','Spider Mine',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTDmgType_ShapedCharge','Shaped Charge');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTDeployableShapedCharge','Shaped Charge');
#--------------------------------------------------------------------------------------------------
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DmgType_Suicided','Suicided');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTDmgType_Instagib','InstaGibbed');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTDmgType_Burning','Burned');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTDmgType_Drowned','Drowned');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTDmgType_Slime','Slimed');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTDmgType_Lava','Fell Into Lava');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DmgType_Fell','Fell');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTDmgType_Fire','Fire');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('DmgType_Crushed','Crushed');
#--------------------------------------------------------------------------------------------------
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTVWeap_ScorpionTurret','Scorpion',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_ScorpionBlade','Scorpion',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_ScorpionGlob','Scorpion',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_ScorpionGlobRed','Scorpion',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_ScorpionSelfDestruct','Scorpion',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTVWeap_MantaGun','Manta',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_MantaBolt','Manta',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_HellBenderPrimary','Hell Bender',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTVWeap_GoliathTurret','Goliath',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_TankShell','Goliath',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_GoliathMachineGun','Goliath Machine Gun',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTVWeap_GoliathMachineGun','Goliath Machine Gun',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_LeviathanBeam','Leviathan',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_LeviathanBolt','Leviathan',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('UTDmgType_LeviathanCollision','Leviathan',1,1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('UTDmgType_LeviathanExplosion','Leviathan',1,1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_LeviathanRocket','Leviathan',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_LeviathanShard','Leviathan',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_LeviathanShockBall','Leviathan',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTVWeap_PaladinGun','Paladin',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_PaladinEnergyBolt','Paladin',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_PaladinProximityExplosion','Paladin',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTVWeap_RaptorGun','Raptor',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_RaptorBolt','Raptor',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_RaptorRocket','Raptor',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTVWeap_CicadaMissileLauncher','Cicada',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_CicadaLaser','Cicada',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_CicadaRocket','Cicada',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_FuryBeam','Fury',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_FuryBolt','Fury',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTVWeap_ScavengerGun','Scavenger',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_ScavengerBolt','Scavenger',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_ScavengerStabbed','Scavenger',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_ScavengerBallCollision','Scavenger',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTVWeap_DarkWalkerPassGun','Dark Walker',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_DarkWalkerBolt','Dark Walker',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_DarkWalkerTurretBeam','Dark Walker',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTVWeap_DarkWalkerTurret','Dark Walker',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_NemesisBeam','Nemisis',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTVWeap_NemesisTurret','Nemisis',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_NightshadeBeam','Nightshade',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_StealthbenderBeam','Stealthbender',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_SPMAShell','SPMA',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_SPMAShockBall','SPMA',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_SPMAShockBeam','SPMA',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('UTDmgType_SPMAShockChain','SPMA',1,1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_SPMASmallShell','SPMA',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('UTDmgType_SPMACameraCrush','SPMA',1,1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTVWeap_ViperGun','Viper',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_ViperBolt','Viper',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('UTDmgType_ViperSelfDestruct','Viper',1,1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('UTDmgType_Pancake','Pancaked',1,4);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('UTDmgType_RanOver','Ran Over',1,4);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_VehicleCollision','Vehicle Collision',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_VehicleExplosion','Vehicle Explosion',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_VehicleShockBall','Vehicle Shock Turret',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_VehicleShockBeam','Vehicle Shock Turret',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype,wp_secondary) VALUES('UTDmgType_VehicleShockChain','Vehicle Shock Turret',1,1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTVWeap_TurretPrimary','Laser Turret',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_TurretPrimary','Laser Turret',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTVWeap_RocketTurret','Rocket Turret',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_TurretRocket','Rocket Turret',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTVWeap_TurretStinger','Machine Gun Turret',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_TurretShard','Machine Gun Turret',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTVWeap_TurretShock','Shock Turret',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_weaptype) VALUES('UTDmgType_TurretShockBall','Shock Turret',1);
#--------------------------------------------------------------------------------------------------
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTDmgType_NodeDestruction','Node Destruction');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTDmgType_EMP','EMP');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTDmgType_Encroached','Encroached');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTDmgType_OrbReturn','Orb Return');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTDmgType_SpaceDeath','Space Death');
#--------------------------------------------------------------------------------------------------
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTWeap_RipperLite','Ripper');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTDmgType_RipperLite','Ripper');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('UTDmgType_RipperLiteHeadshot','Ripper');
INSERT INTO %dbpre%weapons (wp_type,wp_desc,wp_secondary) VALUES('UTDmgType_RipperLiteAlt','Ripper',1);
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('KillZDamageType','Fell');
#--------------------------------------------------------------------------------------------------
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('BattleWeapon_ImpactHammer','Impact Hammer');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('BattleWeapon_Enforcer','Enforcer');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('BattleWeapon_BioRifle','Bio Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('BattleWeapon_ShockRifle','Shock Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('BattleWeapon_LinkGun','Link Gun');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('BattleWeapon_Stinger','Stinger Minigun');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('BattleWeapon_FlakCannon','Flak Cannon');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('BattleWeapon_RocketLauncher','Rocket Launcher');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('BattleWeapon_SniperRifle','Sniper Rifle');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('BattleDamageType_LateEntry','Late Entry');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('BattleDamageType_ReverseFriendlyFire','Reverse Fire');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('BattleDamageType_RoundOvertime','Round Overtime');
INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('BattleDamageType_Camping','Camping');
#
#INSERT INTO %dbpre%weapons (wp_type,wp_desc) VALUES('','');

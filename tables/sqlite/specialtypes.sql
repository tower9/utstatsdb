CREATE TABLE %dbpre%specialtypes (
  st_type varchar(40) NOT NULL default '',
  st_snum smallint(5) NOT NULL
);

CREATE INDEX st_type ON %dbpre%specialtypes (st_type);

INSERT INTO %dbpre%specialtypes (st_type,st_snum) SELECT 'UTDmgType_ImpactHammer',se_num FROM %dbpre%special WHERE se_title='Jackhammer';
INSERT INTO %dbpre%specialtypes (st_type,st_snum) SELECT 'DamTypeShieldImpact',se_num FROM %dbpre%special WHERE se_title='Jackhammer';
INSERT INTO %dbpre%specialtypes (st_type,st_snum) SELECT 'UTDmgType_Enforcer',se_num FROM %dbpre%special WHERE se_title='Gunslinger';
INSERT INTO %dbpre%specialtypes (st_type,st_snum) SELECT 'UTDmgType_DualEnforcer',se_num FROM %dbpre%special WHERE se_title='Gunslinger';
INSERT INTO %dbpre%specialtypes (st_type,st_snum) SELECT 'ClassicDamTypeEnforcer',se_num FROM %dbpre%special WHERE se_title='Gunslinger';
INSERT INTO %dbpre%specialtypes (st_type,st_snum) SELECT 'UTDmgType_BioGoo',se_num FROM %dbpre%special WHERE se_title='Bio Hazard';
INSERT INTO %dbpre%specialtypes (st_type,st_snum) SELECT 'DamTypeBio',se_num FROM %dbpre%special WHERE se_title='Bio Hazard';
INSERT INTO %dbpre%specialtypes (st_type,st_snum) SELECT 'UTDmgType_ShockCombo',se_num FROM %dbpre%special WHERE se_title='Combo King';
INSERT INTO %dbpre%specialtypes (st_type,st_snum) SELECT 'DamTypeShockCombo',se_num FROM %dbpre%special WHERE se_title='Combo King';
INSERT INTO %dbpre%specialtypes (st_type,st_snum) SELECT 'UTDmgType_LinkBeam',se_num FROM %dbpre%special WHERE se_title='Shaftmaster';
INSERT INTO %dbpre%specialtypes (st_type,st_snum) SELECT 'DamTypeLinkShaft',se_num FROM %dbpre%special WHERE se_title='Shaftmaster';
INSERT INTO %dbpre%specialtypes (st_type,st_snum) SELECT 'UTDmgType_Stinger',se_num FROM %dbpre%special WHERE se_title='Blue Streak';
INSERT INTO %dbpre%specialtypes (st_type,st_snum) SELECT 'DamTypeMinigun',se_num FROM %dbpre%special WHERE se_title='Blue Streak';
INSERT INTO %dbpre%specialtypes (st_type,st_snum) SELECT 'UTDmgType_Flak',se_num FROM %dbpre%special WHERE se_title='Flak Master';
INSERT INTO %dbpre%specialtypes (st_type,st_snum) SELECT 'DamTypeFlak',se_num FROM %dbpre%special WHERE se_title='Flak Master';
INSERT INTO %dbpre%specialtypes (st_type,st_snum) SELECT 'UTDmgType_Rocket',se_num FROM %dbpre%special WHERE se_title='Rocket Scientist';
INSERT INTO %dbpre%specialtypes (st_type,st_snum) SELECT 'UTDmgType_SeekingRocket',se_num FROM %dbpre%special WHERE se_title='Rocket Scientist';
INSERT INTO %dbpre%specialtypes (st_type,st_snum) SELECT 'DamTypeRocket',se_num FROM %dbpre%special WHERE se_title='Rocket Scientist';
INSERT INTO %dbpre%specialtypes (st_type,st_snum) SELECT 'HeadShot',se_num FROM %dbpre%special WHERE se_title='Headhunter';
INSERT INTO %dbpre%specialtypes (st_type,st_snum) SELECT 'UTDmgType_AvrilRocket',se_num FROM %dbpre%special WHERE se_title='Big Game Hunter';
INSERT INTO %dbpre%specialtypes (st_type,st_snum) SELECT 'DamTypeONSAVRiLRocket',se_num FROM %dbpre%special WHERE se_title='Big Game Hunter';
INSERT INTO %dbpre%specialtypes (st_type,st_snum) SELECT 'Pancake',se_num FROM %dbpre%special WHERE se_title='Pancake';
INSERT INTO %dbpre%specialtypes (st_type,st_snum) SELECT 'RanOver',se_num FROM %dbpre%special WHERE se_title='Road Rampage';

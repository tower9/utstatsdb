CREATE TABLE %dbpre%playersgt
(
   gt_num int NOT NULL identity(1, 1),
   gt_pnum int NOT NULL,
   gt_tnum smallint NOT NULL default 0,
   gt_type tinyint NOT NULL default 0,
   gt_score int NOT NULL default 0,
   gt_frags int NOT NULL default 0,
   gt_kills int NOT NULL default 0,
   gt_deaths int NOT NULL default 0,
   gt_suicides int NOT NULL default 0,
   gt_teamkills int NOT NULL default 0,
   gt_teamdeaths int NOT NULL default 0,
   gt_sph float NOT NULL default 0,
   gt_eff float NOT NULL default 0,
   gt_wins int NOT NULL default 0,
   gt_losses int NOT NULL default 0,
   gt_matches int NOT NULL default 0,
   gt_time bigint NOT NULL default 0,
   gt_rank decimal(14,8) NOT NULL default 0,
   gt_capcarry int NOT NULL default 0,
   gt_tossed int NOT NULL default 0,
   gt_drop int NOT NULL default 0,
   gt_pickup int NOT NULL default 0,
   gt_return int NOT NULL default 0,
   gt_taken int NOT NULL default 0,
   gt_typekill int NOT NULL default 0,
   gt_assist int NOT NULL default 0,
   gt_holdtime bigint NOT NULL default 0,
   gt_extraa int NOT NULL default 0,
   gt_extrab int NOT NULL default 0,
   gt_extrac int NOT NULL default 0,
   CONSTRAINT gt_num primary key (gt_num)
);

CREATE INDEX gt_pnumt ON %dbpre%playersgt (gt_pnum, gt_type);
CREATE INDEX gt_rank  ON %dbpre%playersgt (gt_rank);

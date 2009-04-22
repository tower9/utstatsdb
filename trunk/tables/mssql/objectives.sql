CREATE TABLE %dbpre%objectives
(
   obj_num int NOT NULL identity(1, 1),
   obj_map int NOT NULL default 0,
   obj_priority tinyint NOT NULL default 0,
   obj_secondary tinyint NOT NULL default 0,
   obj_desc varchar(121) NOT NULL default '',
   obj_times int NOT NULL default 0,
   obj_besttime int NOT NULL default 0,
   obj_avgtime float NOT NULL default 0,
   CONSTRAINT obj_num primary key (obj_num)
);

CREATE INDEX obj_desc ON %dbpre%objectives (obj_desc);
CREATE INDEX obj_map ON %dbpre%objectives (obj_map);

#INSERT INTO %dbpre%objectives (obj_map,obj_priority,obj_secondary,obj_desc) VALUES(4,0,1,'Open the panel');
#INSERT INTO %dbpre%objectives (obj_map,obj_priority,obj_desc) VALUES(4,0,'Extend the boarding platform');
#INSERT INTO %dbpre%objectives (obj_map,obj_priority,obj_desc) VALUES(4,1,'Place explosives ON the door');
#INSERT INTO %dbpre%objectives (obj_map,obj_priority,obj_desc) VALUES(4,2,'Open rear door');
#INSERT INTO %dbpre%objectives (obj_map,obj_priority,obj_desc) VALUES(4,3,'Open the side doors');
#INSERT INTO %dbpre%objectives (obj_map,obj_priority,obj_desc) VALUES(4,4,'Reach the N.E.X.U.S. missile trailer');
#INSERT INTO %dbpre%objectives (obj_map,obj_priority,obj_desc) VALUES(4,5,'Retrieve the N.E.X.U.S. missiles');
#
#INSERT INTO %dbpre%objectives (obj_map,obj_priority,obj_desc) VALUES(6,0,'Destroy barricade');
#INSERT INTO %dbpre%objectives (obj_map,obj_priority,obj_desc) VALUES(6,1,'Secure forward outpost');
#INSERT INTO %dbpre%objectives (obj_map,obj_priority,obj_desc) VALUES(6,2,'Destroy Gate Lock');
#INSERT INTO %dbpre%objectives (obj_map,obj_priority,obj_desc) VALUES(6,3,'Destroy Command Center');
#
#INSERT INTO %dbpre%objectives (obj_map,obj_priority,obj_desc) VALUES(7,0,'Infiltrate the Base');
#INSERT INTO %dbpre%objectives (obj_map,obj_priority,obj_desc) VALUES(7,1,'Activate the Ion Core');
#INSERT INTO %dbpre%objectives (obj_map,obj_priority,obj_desc) VALUES(7,2,'Capture the Tank');
#INSERT INTO %dbpre%objectives (obj_map,obj_priority,obj_desc) VALUES(7,3,'Destroy Access Doors');
#INSERT INTO %dbpre%objectives (obj_map,obj_priority,obj_desc) VALUES(7,4,'Open Security Gate');
#INSERT INTO %dbpre%objectives (obj_map,obj_priority,obj_desc) VALUES(7,5,'Shutdown Primary Dam');
#INSERT INTO %dbpre%objectives (obj_map,obj_priority,obj_desc) VALUES(7,6,'Destroy the Depot Door');
#INSERT INTO %dbpre%objectives (obj_map,obj_priority,obj_desc) VALUES(7,7,'Shutdown Secondary Dam');
#INSERT INTO %dbpre%objectives (obj_map,obj_priority,obj_desc) VALUES(7,8,'Destroy the Blast Door');
#INSERT INTO %dbpre%objectives (obj_map,obj_priority,obj_desc) VALUES(7,9,'Escape the Base');
#
#INSERT INTO %dbpre%objectives (obj_map,obj_priority,obj_desc) VALUES(8,0,'Find the Energy Core');
#INSERT INTO %dbpre%objectives (obj_map,obj_priority,obj_desc) VALUES(8,1,'Return the Core to the vehicle');
#INSERT INTO %dbpre%objectives (obj_map,obj_priority,obj_desc) VALUES(8,2,'Lower the bridge');
#INSERT INTO %dbpre%objectives (obj_map,obj_priority,obj_desc) VALUES(8,3,'Cross the bridge');
#INSERT INTO %dbpre%objectives (obj_map,obj_priority,obj_desc) VALUES(8,4,'Open the checkpoint gate');
#INSERT INTO %dbpre%objectives (obj_map,obj_priority,obj_desc) VALUES(8,5,'Secure the checkpoint');
#INSERT INTO %dbpre%objectives (obj_map,obj_priority,obj_desc) VALUES(8,6,'Proceed to the main gates');
#
#INSERT INTO %dbpre%objectives (obj_map,obj_priority,obj_desc) VALUES(9,0,'Destroy lower shield generator');
#INSERT INTO %dbpre%objectives (obj_map,obj_priority,obj_desc) VALUES(9,1,'Destroy upper shield generator');
#INSERT INTO %dbpre%objectives (obj_map,obj_priority,obj_desc) VALUES(9,2,'Proceed to landing bay');
#INSERT INTO %dbpre%objectives (obj_map,obj_priority,obj_desc) VALUES(9,3,'Destroy energy bypass');
#INSERT INTO %dbpre%objectives (obj_map,obj_priority,obj_desc) VALUES(9,4,'Destroy sentinel');
#INSERT INTO %dbpre%objectives (obj_map,obj_priority,obj_desc) VALUES(9,5,'Use panel to unlock core hatch');
#INSERT INTO %dbpre%objectives (obj_map,obj_priority,obj_desc) VALUES(9,6,'Destroy the conductor');
#
#INSERT INTO %dbpre%objectives (obj_map,obj_priority,obj_desc) VALUES(10,0,'Align the satellite dish');
#INSERT INTO %dbpre%objectives (obj_map,obj_priority,obj_desc) VALUES(10,1,'Destroy the gate');
#INSERT INTO %dbpre%objectives (obj_map,obj_priority,obj_desc) VALUES(10,2,'Cut the Data Cables');
#INSERT INTO %dbpre%objectives (obj_map,obj_priority,obj_desc) VALUES(10,3,'Destroy shield component');
#INSERT INTO %dbpre%objectives (obj_map,obj_priority,obj_desc) VALUES(10,4,'Destroy the AI generator');

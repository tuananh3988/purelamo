CREATE TABLE `search_sumary` (
`id`  int NOT NULL AUTO_INCREMENT ,
`keyword`  varchar(255) NOT NULL ,
`count`  int NOT NULL DEFAULT 0 ,
`created_date`  datetime NULL ,
`updated_date`  datetime NULL ,
PRIMARY KEY (`id`)
);
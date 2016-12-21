ALTER TABLE `devices`
ADD COLUMN `type_time_recieve_notify`  tinyint(4) NOT NULL DEFAULT 1 COMMENT '1: all time, 2: morning, 3: afternoon' AFTER `device_id`;
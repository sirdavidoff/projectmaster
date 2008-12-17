ALTER TABLE `sectors` ADD `ordering` INT NOT NULL DEFAULT '10';
UPDATE `sectors` SET `ordering` = '1' WHERE `sectors`.`id` =1 LIMIT 1 ;
UPDATE `sectors` SET `ordering` = '2' WHERE `sectors`.`id` =16 LIMIT 1 ;
UPDATE `sectors` SET `ordering` = '1000' WHERE `sectors`.`id` =29 LIMIT 1 ;
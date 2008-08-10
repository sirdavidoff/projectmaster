CREATE TABLE `contact_import_fields`
(
`id` INTEGER(10) NOT NULL AUTO_INCREMENT ,
`name` VARCHAR(255),
`reference` VARCHAR(255) NOT NULL,
`validate` VARCHAR(255),
PRIMARY KEY (`id`)
);

insert into contact_import_fields values ('', 'Contact.name', 'Contact Name', '');
insert into contact_import_fields values ('', 'Contact.contacttype_id', 'Contact Type', 'Contacttype.name');
insert into contact_import_fields values ('', 'Contact.sector_id', 'Contact Sector', 'Sector.name');
insert into contact_import_fields values ('', 'Contact.market_id', 'Contact Market', 'Market.name');
insert into contact_import_fields values ('', 'Contact.status_id', 'Contact Status', 'Status.name');
insert into contact_import_fields values ('', 'Contact.address', 'Contact Address', '');
insert into contact_import_fields values ('', 'Contact.tel', 'Contact Tel', '');
insert into contact_import_fields values ('', 'Contact.fax', 'Contact Fax', '');
insert into contact_import_fields values ('', 'Contact.website', 'Contact Website', '');
insert into contact_import_fields values ('', 'Note.n.text', 'Contact Note', '');
insert into contact_import_fields values ('', 'Person.0.name', 'Person 1 Name', '');
insert into contact_import_fields values ('', 'Person.0.position', 'Person 1 Position', '');
insert into contact_import_fields values ('', 'Person.0.address', 'Person 1 Address', '');
insert into contact_import_fields values ('', 'Person.0.tel', 'Person 1 Tel', '');
insert into contact_import_fields values ('', 'Person.0.mobile', 'Person 1 Mobile', '');
insert into contact_import_fields values ('', 'Person.0.fax', 'Person 1 Fax', '');
insert into contact_import_fields values ('', 'Person.0.email', 'Person 1 Email', '');
insert into contact_import_fields values ('', 'Person.0.notes', 'Person 1 Note', '');
insert into contact_import_fields values ('', 'Person.1.name', 'Person 2 Name', '');
insert into contact_import_fields values ('', 'Person.1.position', 'Person 2 Position', '');
insert into contact_import_fields values ('', 'Person.1.address', 'Person 2 Address', '');
insert into contact_import_fields values ('', 'Person.1.tel', 'Person 2 Tel', '');
insert into contact_import_fields values ('', 'Person.1.mobile', 'Person 2 Mobile', '');
insert into contact_import_fields values ('', 'Person.1.fax', 'Person 2 Fax', '');
insert into contact_import_fields values ('', 'Person.1.email', 'Person 2 Email', '');
insert into contact_import_fields values ('', 'Person.1.notes', 'Person 2 Note', '');
insert into contact_import_fields values ('', 'Person.2.name', 'Person 3 Name', '');
insert into contact_import_fields values ('', 'Person.2.position', 'Person 3 Position', '');
insert into contact_import_fields values ('', 'Person.2.address', 'Person 3 Address', '');
insert into contact_import_fields values ('', 'Person.2.tel', 'Person 3 Tel', '');
insert into contact_import_fields values ('', 'Person.2.mobile', 'Person 3 Mobile', '');
insert into contact_import_fields values ('', 'Person.2.fax', 'Person 3 Fax', '');
insert into contact_import_fields values ('', 'Person.2.email', 'Person 3 Email', '');
insert into contact_import_fields values ('', 'Person.2.notes', 'Person 3 Note', '');
insert into contact_import_fields values ('', 'Note.n.text', 'Other (Add as note)', '');
insert into contact_import_fields values ('', '', '*Ignore this column*', '');

ALTER TABLE `contacts` CHANGE `contacttype_id` `contacttype_id` INT( 10 ) NOT NULL DEFAULT '2';
UPDATE `sectors` SET `id` = 0 WHERE `name` = "Other";
UPDATE contacts SET sector_id = 0 WHERE sector_id = 13;
ALTER TABLE `contacts` CHANGE `sector_id` `sector_id` INT( 10 ) NOT NULL DEFAULT '0';
ALTER TABLE `contacts` CHANGE `market_id` `market_id` INT( 10 ) NULL DEFAULT '4';

CREATE TABLE `contact_imports`
(
`id` INTEGER(10) NOT NULL AUTO_INCREMENT ,
`created` DATETIME NOT NULL,
`created_by` INTEGER(10) NOT NULL,
PRIMARY KEY (`id`)
);

ALTER TABLE `contacts` ADD `contact_import_id` INT( 10 ) NULL;

ALTER TABLE `people` CHANGE `position` `position` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '?';

UPDATE `sectors` SET `name` = 'Energy & Utilities' WHERE `sectors`.`id` =2 LIMIT 1 ;
UPDATE `sectors` SET `name` = 'IT & Electronics' WHERE `sectors`.`id` =4 LIMIT 1 ;
UPDATE `sectors` SET `name` = 'Banking & Finance' WHERE `sectors`.`id` =8 LIMIT 1 ;
INSERT INTO `sectors` (`id`, `name`) VALUES (NULL, 'Manufacturing');
INSERT INTO `sectors` (`id`, `name`) VALUES (NULL, 'Agriculture');
INSERT INTO `sectors` (`id`, `name`) VALUES (NULL, 'Institutions');
INSERT INTO `sectors` (`id`, `name`) VALUES (NULL, 'Utilities');
INSERT INTO `sectors` (`id`, `name`) VALUES (NULL, 'Education');
INSERT INTO `sectors` (`id`, `name`) VALUES (NULL, 'Various');
INSERT INTO `sectors` (`id`, `name`) VALUES (NULL, 'Holding');
INSERT INTO `sectors` (`id`, `name`) VALUES (NULL, 'Natural Resources');
INSERT INTO `sectors` (`id`, `name`) VALUES (NULL, 'Defence');
INSERT INTO `sectors` (`id`, `name`) VALUES (NULL, 'Pharmaceuticals');
INSERT INTO `sectors` (`id`, `name`) VALUES (NULL, 'Leisure');
INSERT INTO `sectors` (`id`, `name`) VALUES (NULL, 'Trading');
INSERT INTO `sectors` (`id`, `name`) VALUES (NULL, 'Services');
INSERT INTO `sectors` (`id`, `name`) VALUES (NULL, 'Business');
INSERT INTO `sectors` (`id`, `name`) VALUES (NULL, 'Healthcare');
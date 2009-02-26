/* SQLEditor export export plugin = MySQL*/

DROP TABLE IF EXISTS `roles`;

DROP TABLE IF EXISTS `contacttypes`;

DROP TABLE IF EXISTS `sectors`;

DROP TABLE IF EXISTS `project_statuses`;

DROP TABLE IF EXISTS `users`;

DROP TABLE IF EXISTS `events`;

DROP TABLE IF EXISTS `contact_imports`;

DROP TABLE IF EXISTS `contact_import_fields`;

DROP TABLE IF EXISTS `media`;

DROP TABLE IF EXISTS `projects`;

DROP TABLE IF EXISTS `team_members`;

DROP TABLE IF EXISTS `markets`;

DROP TABLE IF EXISTS `statuses`;

DROP TABLE IF EXISTS `contacts`;

DROP TABLE IF EXISTS `people`;

DROP TABLE IF EXISTS `contracts`;

DROP TABLE IF EXISTS `users_contracts`;

DROP TABLE IF EXISTS `actions`;

DROP TABLE IF EXISTS `meetings`;

DROP TABLE IF EXISTS `notes`;

DROP TABLE IF EXISTS `contact_opens_contact`;

DROP TABLE IF EXISTS `contact_status_changes`;


CREATE TABLE `roles`
(
`id` INTEGER(10) NOT NULL AUTO_INCREMENT ,
`name` VARCHAR(255),
PRIMARY KEY (`id`)
);



CREATE TABLE `contacttypes`
(
`id` INTEGER(10) NOT NULL AUTO_INCREMENT ,
`name` VARCHAR(255) NOT NULL,
PRIMARY KEY (`id`)
);



CREATE TABLE `sectors`
(
`id` INTEGER(10) NOT NULL AUTO_INCREMENT ,
`name` VARCHAR(255) NOT NULL,
`ordering` INTEGER DEFAULT 10 NOT NULL,
PRIMARY KEY (`id`)
);



CREATE TABLE `project_statuses`
(
`id` INTEGER(10) NOT NULL AUTO_INCREMENT ,
`name` VARCHAR(50),
PRIMARY KEY (`id`)
);



CREATE TABLE `users`
(
`id` INTEGER(10) NOT NULL AUTO_INCREMENT ,
`first_name` VARCHAR(50) NOT NULL,
`last_name` VARCHAR(255) NOT NULL,
`email` VARCHAR(255) NOT NULL,
`username` VARCHAR(100) NOT NULL,
`passwd` VARCHAR(255) NOT NULL,
`is_active` INTEGER(1) DEFAULT 1 NOT NULL,
`last_login` DATETIME,
PRIMARY KEY (`id`)
);



CREATE TABLE `events`
(
`id` INTEGER(10) NOT NULL AUTO_INCREMENT ,
`name` TEXT NOT NULL,
`notes` TEXT,
`type` INTEGER(2),
`start_date` DATE NOT NULL,
`start_time` VARCHAR(5),
`end_date` DATE,
`end_time` VARCHAR(5),
`created` DATETIME,
`created_by` INTEGER(10),
`updated` DATETIME,
`updated_by` INTEGER(10),
PRIMARY KEY (`id`)
);



CREATE TABLE `contact_imports`
(
`id` INTEGER(10) NOT NULL AUTO_INCREMENT ,
`created` DATETIME NOT NULL,
`created_by` INTEGER(10) NOT NULL,
PRIMARY KEY (`id`)
);



CREATE TABLE `contact_import_fields`
(
`id` INTEGER(10) NOT NULL AUTO_INCREMENT ,
`name` VARCHAR(255),
`reference` VARCHAR(255) NOT NULL,
`validate` VARCHAR(255),
PRIMARY KEY (`id`)
);



CREATE TABLE `media`
(
`id` INTEGER(10) NOT NULL AUTO_INCREMENT ,
`name` VARCHAR(255) NOT NULL,
`agency_name` VARCHAR(255) NOT NULL,
PRIMARY KEY (`id`)
);



CREATE TABLE `projects`
(
`id` INTEGER(10) NOT NULL AUTO_INCREMENT ,
`media_id` INTEGER(10) NOT NULL,
`subject` VARCHAR(255) NOT NULL,
`started_on` DATE NOT NULL,
`finished_on` DATE,
`project_status_id` INTEGER(10) DEFAULT 1 NOT NULL,
PRIMARY KEY (`id`)
);



CREATE TABLE `team_members`
(
`id` INTEGER(10) NOT NULL AUTO_INCREMENT ,
`project_id` INTEGER(10) NOT NULL,
`user_id` INTEGER(10) NOT NULL,
`role_id` INTEGER(10) NOT NULL,
`unique_name` VARCHAR(255),
`tel` VARCHAR(255),
`email` VARCHAR(255),
`started_on` DATE,
`finished_on` DATE,
`status` TINYINT(1),
PRIMARY KEY (`id`)
);



CREATE TABLE `markets`
(
`id` INTEGER(10) NOT NULL AUTO_INCREMENT ,
`name` VARCHAR(255),
PRIMARY KEY (`id`)
);



CREATE TABLE `statuses`
(
`id` INTEGER(10) NOT NULL AUTO_INCREMENT ,
`name` VARCHAR(50),
PRIMARY KEY (`id`)
);



CREATE TABLE `contacts`
(
`id` INTEGER(10) NOT NULL AUTO_INCREMENT ,
`project_id` INTEGER(10) NOT NULL,
`contacttype_id` INTEGER(10) DEFAULT 2 NOT NULL,
`sector_id` INTEGER(10) DEFAULT 0 NOT NULL,
`name` VARCHAR(255) NOT NULL,
`address` TEXT,
`tel` VARCHAR(255),
`fax` VARCHAR(255),
`email` VARCHAR(255),
`website` VARCHAR(255),
`market_id` INTEGER(10) DEFAULT 4,
`revenue` VARCHAR(255),
`growth` VARCHAR(255),
`status_id` INTEGER(10) DEFAULT 2 NOT NULL,
`created` DATETIME,
`created_by` INTEGER(10),
`updated` DATETIME,
`updated_by` INTEGER(10),
`assigned_to` INTEGER(10) DEFAULT 0,
`contact_import_id` INTEGER(10),
PRIMARY KEY (`id`)
);



CREATE TABLE `people`
(
`id` INTEGER(10) NOT NULL AUTO_INCREMENT ,
`contact_id` INTEGER(10) NOT NULL,
`position` VARCHAR(255) DEFAULT '?' NOT NULL,
`name` VARCHAR(255),
`address` VARCHAR(255),
`tel` VARCHAR(255),
`mobile` VARCHAR(255),
`fax` VARCHAR(255),
`email` VARCHAR(255),
`notes` TEXT,
`created` DATETIME,
`created_by` INTEGER(10),
`updated` DATETIME,
`updated_by` INTEGER(10),
`ordering` INTEGER(10),
PRIMARY KEY (`id`)
);



CREATE TABLE `contracts`
(
`id` INTEGER(10) NOT NULL AUTO_INCREMENT ,
`contact_id` INTEGER(10) NOT NULL,
`space` TEXT NOT NULL,
`cost` FLOAT NOT NULL,
`notes` TEXT,
`signed_on` DATE NOT NULL,
`payment_by` DATE,
`paid_on` DATE,
`created` DATETIME,
`created_by` INTEGER(10),
`updated` DATETIME,
`updated_by` INTEGER(10),
PRIMARY KEY (`id`)
);



CREATE TABLE `users_contracts`
(
`user_id` INTEGER(10) NOT NULL,
`contract_id` INTEGER(10) NOT NULL,
PRIMARY KEY (`user_id`,`contract_id`)
);



CREATE TABLE `actions`
(
`id` INTEGER(10) NOT NULL AUTO_INCREMENT ,
`contact_id` INTEGER(10) NOT NULL,
`user_id` INTEGER(10),
`text` TEXT NOT NULL,
`deadline_date` DATE,
`deadline_time` VARCHAR(5),
`completed` TINYINT(1) DEFAULT 0 NOT NULL,
`completed_at` DATETIME,
`completed_by` INTEGER(10),
`created` DATETIME,
`created_by` INTEGER(10),
`updated` DATETIME,
`updated_by` INTEGER(10),
PRIMARY KEY (`id`)
);



CREATE TABLE `meetings`
(
`id` INTEGER(10) NOT NULL AUTO_INCREMENT ,
`contact_id` INTEGER(10),
`with` TEXT,
`text` TEXT,
`date` DATE,
`time` VARCHAR(5),
`created` DATETIME,
`created_by` INTEGER(10),
`updated` DATETIME,
`updated_by` INTEGER(10),
PRIMARY KEY (`id`)
);



CREATE TABLE `notes`
(
`id` INTEGER(10) NOT NULL AUTO_INCREMENT ,
`contact_id` INTEGER(10) NOT NULL,
`text` TEXT NOT NULL,
`ordering` INTEGER(10),
`created` DATETIME,
`created_by` INTEGER(10),
`updated` DATETIME,
`updated_by` INTEGER(10),
PRIMARY KEY (`id`)
);



CREATE TABLE `contact_opens_contact`
(
`opener_id` INTEGER(10) NOT NULL,
`openee_id` INTEGER(10) NOT NULL
);



CREATE TABLE `contact_status_changes`
(
`id` INTEGER(10) NOT NULL AUTO_INCREMENT ,
`contact_id` INTEGER(10) NOT NULL,
`status_id` INTEGER(10) NOT NULL,
`changed_at` DATETIME,
PRIMARY KEY (`id`)
);


ALTER TABLE `events` ADD FOREIGN KEY (`created_by`) REFERENCES `users`(`id`);
ALTER TABLE `events` ADD FOREIGN KEY (`updated_by`) REFERENCES `users`(`id`);
ALTER TABLE `contact_imports` ADD FOREIGN KEY (`created_by`) REFERENCES `users`(`id`);
ALTER TABLE `projects` ADD FOREIGN KEY (`media_id`) REFERENCES `media`(`id`);
ALTER TABLE `projects` ADD FOREIGN KEY (`project_status_id`) REFERENCES `project_statuses`(`id`);
ALTER TABLE `team_members` ADD FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`);
ALTER TABLE `team_members` ADD FOREIGN KEY (`user_id`) REFERENCES `users`(`id`);
ALTER TABLE `team_members` ADD FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`);
ALTER TABLE `contacts` ADD FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`);
ALTER TABLE `contacts` ADD FOREIGN KEY (`contacttype_id`) REFERENCES `contacttypes`(`id`);
ALTER TABLE `contacts` ADD FOREIGN KEY (`sector_id`) REFERENCES `sectors`(`id`);
ALTER TABLE `contacts` ADD FOREIGN KEY (`market_id`) REFERENCES `markets`(`id`);
ALTER TABLE `contacts` ADD FOREIGN KEY (`status_id`) REFERENCES `statuses`(`id`);
ALTER TABLE `contacts` ADD FOREIGN KEY (`created_by`) REFERENCES `users`(`id`);
ALTER TABLE `contacts` ADD FOREIGN KEY (`updated_by`) REFERENCES `users`(`id`);
ALTER TABLE `contacts` ADD FOREIGN KEY (`assigned_to`) REFERENCES `team_members`(`id`);
ALTER TABLE `contacts` ADD FOREIGN KEY (`contact_import_id`) REFERENCES `contact_imports`(`id`);
ALTER TABLE `people` ADD FOREIGN KEY (`contact_id`) REFERENCES `contacts`(`id`);
ALTER TABLE `people` ADD FOREIGN KEY (`created_by`) REFERENCES `users`(`id`);
ALTER TABLE `people` ADD FOREIGN KEY (`updated_by`) REFERENCES `users`(`id`);
ALTER TABLE `contracts` ADD FOREIGN KEY (`contact_id`) REFERENCES `contacts`(`id`);
ALTER TABLE `contracts` ADD FOREIGN KEY (`created_by`) REFERENCES `users`(`id`);
ALTER TABLE `contracts` ADD FOREIGN KEY (`updated_by`) REFERENCES `users`(`id`);
CREATE INDEX `users_contracts_user_id_idxfk` ON `users_contracts`(user_id);
ALTER TABLE `users_contracts` ADD FOREIGN KEY (`user_id`) REFERENCES `users`(`id`);
CREATE INDEX `users_contracts_contract_id_idxfk` ON `users_contracts`(contract_id);
ALTER TABLE `users_contracts` ADD FOREIGN KEY (`contract_id`) REFERENCES `contracts`(`id`);
ALTER TABLE `actions` ADD FOREIGN KEY (`contact_id`) REFERENCES `contacts`(`id`);
ALTER TABLE `actions` ADD FOREIGN KEY (`user_id`) REFERENCES `users`(`id`);
ALTER TABLE `actions` ADD FOREIGN KEY (`completed_by`) REFERENCES `users`(`id`);
ALTER TABLE `actions` ADD FOREIGN KEY (`created_by`) REFERENCES `users`(`id`);
ALTER TABLE `actions` ADD FOREIGN KEY (`updated_by`) REFERENCES `users`(`id`);
ALTER TABLE `meetings` ADD FOREIGN KEY (`contact_id`) REFERENCES `contacts`(`id`);
ALTER TABLE `meetings` ADD FOREIGN KEY (`created_by`) REFERENCES `users`(`id`);
ALTER TABLE `meetings` ADD FOREIGN KEY (`updated_by`) REFERENCES `users`(`id`);
ALTER TABLE `notes` ADD FOREIGN KEY (`contact_id`) REFERENCES `contacts`(`id`);
ALTER TABLE `notes` ADD FOREIGN KEY (`created_by`) REFERENCES `users`(`id`);
ALTER TABLE `notes` ADD FOREIGN KEY (`updated_by`) REFERENCES `users`(`id`);
ALTER TABLE `contact_opens_contact` ADD FOREIGN KEY (`opener_id`) REFERENCES `contacts`(`id`);
ALTER TABLE `contact_opens_contact` ADD FOREIGN KEY (`openee_id`) REFERENCES `contacts`(`id`);
ALTER TABLE `contact_status_changes` ADD FOREIGN KEY (`contact_id`) REFERENCES `contacts`(`id`);
ALTER TABLE `contact_status_changes` ADD FOREIGN KEY (`status_id`) REFERENCES `statuses`(`id`);

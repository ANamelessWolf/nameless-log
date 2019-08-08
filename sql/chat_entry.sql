CREATE TABLE `nameless_log`.`chat_entry` (
`entryId` INT NOT NULL AUTO_INCREMENT, 
`memberId` INT NOT NULL, 
`entry` TEXT CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL, 
`creation_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
PRIMARY KEY (`entryId`)) ENGINE = InnoDB;

ALTER TABLE `chat_entry`
  ADD KEY `member_entry_id_fk` (`memberId`);

ALTER TABLE `chat_entry`
  ADD CONSTRAINT `member_entry_id_fk` FOREIGN KEY (`memberId`) REFERENCES `chat_members` (`memberId`) ON DELETE CASCADE ON UPDATE NO ACTION;
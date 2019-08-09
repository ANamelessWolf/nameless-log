CREATE TABLE `nameless_log`.`chat_members` (
  `memberId` int(11) NOT NULL,
  `chatId` int(11) NOT NULL,
  `userId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

ALTER TABLE `nameless_log`.`chat_members`
  ADD PRIMARY KEY (`memberId`),
  ADD UNIQUE KEY `chatId` (`chatId`,`userId`),
  ADD KEY `chat_member_id_fk` (`chatId`),
  ADD KEY `user_member_id_fk` (`userId`);

ALTER TABLE `nameless_log`.`chat_members`
  MODIFY `memberId` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `nameless_log`.`chat_members`
  ADD CONSTRAINT `chat_member_id_fk` FOREIGN KEY (`chatId`) REFERENCES `nameless_log`.`chat` (`chatId`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `user_member_id_fk` FOREIGN KEY (`userId`) REFERENCES `nameless_log`.`users` (`userId`) ON DELETE CASCADE;
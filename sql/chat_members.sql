CREATE TABLE `chat_members` (
  `chatId` int(11) NOT NULL,
  `userId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

ALTER TABLE `chat_members`
  ADD PRIMARY KEY (`memberId`),
  ADD KEY `chat_member_id_fk` (`chatId`),
  ADD KEY `user_member_id_fk` (`userId`);

ALTER TABLE `chat_members`
  ADD CONSTRAINT `chat_member_id_fk` FOREIGN KEY (`chatId`) REFERENCES `chat` (`chatId`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `user_member_id_fk` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE;
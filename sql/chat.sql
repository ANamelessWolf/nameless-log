CREATE TABLE `nameless_log`.`chat` (
  `chatId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `name` varchar(25) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

ALTER TABLE `nameless_log`.`chat`
  ADD PRIMARY KEY (`chatId`),
  ADD KEY `chat_user_id_fk` (`userId`);
  
ALTER TABLE `nameless_log`.`chat`
  MODIFY `chatId` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `nameless_log`.`chat`
  ADD CONSTRAINT `chat_user_id_fk` FOREIGN KEY (`userId`) REFERENCES `nameless_log`.`users` (`userId`) ON DELETE CASCADE;  
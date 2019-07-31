CREATE TABLE `contacts` (
  `userId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

ALTER TABLE `contacts`
  ADD UNIQUE KEY `userId` (`userId`);

ALTER TABLE `contacts`
  ADD CONSTRAINT `contacts_user_id_fk` FOREIGN KEY (`userId`) REFERENCES `nameless_log`.`users` (`userId`) ON DELETE CASCADE;


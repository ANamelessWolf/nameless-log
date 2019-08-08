CREATE TABLE `chat_entry` (
  `entryId` int(11) NOT NULL,
  `memberId` int(11) NOT NULL,
  `entry` text COLLATE utf8_spanish_ci NOT NULL,
  `creation_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Indexes for table `chat_entry`
--
ALTER TABLE `chat_entry`
  ADD PRIMARY KEY (`entryId`);

ALTER TABLE `chat_entry`
  MODIFY `entryId` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `chat_entry`
  ADD KEY `member_entry_id_fk` (`memberId`);

ALTER TABLE `chat_entry`
  ADD CONSTRAINT `member_entry_id_fk` FOREIGN KEY (`memberId`) REFERENCES `chat_members` (`memberId`) ON DELETE CASCADE ON UPDATE NO ACTION;
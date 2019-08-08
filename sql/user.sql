CREATE TABLE `nameless_log`.`users`
( 
  `userId` INT NOT NULL AUTO_INCREMENT , 
  `username` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL , 
  `pass` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL , 
  PRIMARY KEY (`userId`), UNIQUE (`username`)
  ) ENGINE = InnoDB;
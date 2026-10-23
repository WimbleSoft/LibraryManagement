-- ------------------------------------------------------------------
-- Upgrades a version 1 Library Records database to version 2.
--
-- BACK UP YOUR DATABASE FIRST, then run this file once against it:
--     mysql -u root -p libraryrecords < database/upgrade-v1-to-v2.sql
--
-- Existing MD5 passwords keep working: each one is upgraded to a
-- secure hash automatically the next time that person signs in.
-- ------------------------------------------------------------------

SET NAMES utf8mb4;
-- Version 1 stored "0000-00-00" dates, which strict SQL modes reject.
SET SESSION sql_mode = '';

-- Personnel: clearer English column names, room for modern password hashes.
ALTER TABLE `personnel`
    CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    CHANGE `userName`    `username` VARCHAR(50)  NOT NULL,
    CHANGE `passWord`    `password` VARCHAR(255) NOT NULL,
    CHANGE `nameSurname` `fullName` VARCHAR(100) NOT NULL,
    CHANGE `eMail`       `email`    VARCHAR(100) NOT NULL DEFAULT '',
    MODIFY `phone`                  VARCHAR(30)  NOT NULL DEFAULT '',
    CHANGE `auth`        `isAdmin`  TINYINT(1)   NOT NULL DEFAULT 0,
    ADD UNIQUE KEY `uq_personnel_username` (`username`);

-- Items: availability is now worked out from the loans table,
-- so the separate status columns are no longer needed.
ALTER TABLE `books`
    CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    MODIFY `bookName`                  VARCHAR(100) NOT NULL,
    CHANGE `bookWriter` `bookAuthor`   VARCHAR(100) NOT NULL,
    DROP COLUMN `bookStatus`;

ALTER TABLE `computers`
    CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    MODIFY `manufacturer`                VARCHAR(50) NOT NULL,
    MODIFY `model`                       VARCHAR(50) NOT NULL,
    CHANGE `serialNo` `serialNumber`     VARCHAR(50) NOT NULL,
    DROP COLUMN `computerStatus`;

ALTER TABLE `roomkeys`
    CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    CHANGE `keyContent` `keyDescription` VARCHAR(255) NOT NULL,
    DROP COLUMN `keyStatus`;

-- Loans: version 1 stored the item type as text in some rows.
UPDATE `loans` SET `type` = CASE
    WHEN `type` IN ('0', 'Kitap', 'Book')          THEN '0'
    WHEN `type` IN ('1', 'Bilgisayar', 'Computer') THEN '1'
    WHEN `type` IN ('2', 'Anahtar', 'Key')         THEN '2'
    ELSE `type`
END;

ALTER TABLE `loans`
    CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    MODIFY `returnDate`       DATE NULL DEFAULT NULL,
    MODIFY `returnAccepterId` INT NULL DEFAULT NULL;

-- "Not returned" used to be stored as a zero date / zero id.
UPDATE `loans` SET `returnDate` = NULL, `returnAccepterId` = NULL
 WHERE `returnDate` IS NULL OR `returnDate` < '1000-01-01' OR `returnAccepterId` = 0;

ALTER TABLE `loans`
    CHANGE `type`             `itemType`     TINYINT UNSIGNED NOT NULL,
    CHANGE `productId`        `itemId`       INT UNSIGNED NOT NULL,
    MODIFY `lenderId`                        INT UNSIGNED NOT NULL,
    CHANGE `loanerId`         `borrowerId`   INT UNSIGNED NOT NULL,
    CHANGE `returnAccepterId` `receivedById` INT UNSIGNED NULL DEFAULT NULL,
    ADD KEY `ix_loans_item` (`itemType`, `itemId`, `returnDate`),
    ADD KEY `ix_loans_borrower` (`borrowerId`, `returnDate`);

CREATE TABLE IF NOT EXISTS `login_attempts` (
    `attemptId`   INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username`    VARCHAR(50) NOT NULL,
    `ipAddress`   VARCHAR(45) NOT NULL,
    `attemptedAt` DATETIME NOT NULL,
    PRIMARY KEY (`attemptId`),
    KEY `ix_login_attempts_username` (`username`, `attemptedAt`),
    KEY `ix_login_attempts_ip` (`ipAddress`, `attemptedAt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

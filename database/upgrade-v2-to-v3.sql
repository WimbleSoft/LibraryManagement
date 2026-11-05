-- ------------------------------------------------------------------
-- Upgrades a version 2 Library Records database to version 3.
--
-- BACK UP YOUR DATABASE FIRST, then run this file once against it:
--     mysql -u root -p libraryrecords < database/upgrade-v2-to-v3.sql
--
-- Version 3 adds due dates, item status, staff deactivation, return
-- condition and lending rules. Existing loans keep no due date, so
-- they are never shown as overdue; set one from the Loans page if
-- needed.
-- ------------------------------------------------------------------

SET NAMES utf8mb4;

ALTER TABLE `personnel`
    ADD COLUMN `isActive` TINYINT(1) NOT NULL DEFAULT 1 AFTER `isAdmin`;

-- 0 = active, 1 = in repair, 2 = retired, 3 = lost.
ALTER TABLE `books`
    ADD COLUMN `status` TINYINT UNSIGNED NOT NULL DEFAULT 0;
ALTER TABLE `computers`
    ADD COLUMN `status` TINYINT UNSIGNED NOT NULL DEFAULT 0;
ALTER TABLE `roomkeys`
    ADD COLUMN `status` TINYINT UNSIGNED NOT NULL DEFAULT 0;

ALTER TABLE `loans`
    ADD COLUMN `dueDate`         DATE NULL DEFAULT NULL AFTER `returnDate`,
    ADD COLUMN `returnCondition` TINYINT UNSIGNED NULL DEFAULT NULL AFTER `receivedById`,
    ADD COLUMN `returnNote`      VARCHAR(255) NOT NULL DEFAULT '' AFTER `returnCondition`,
    ADD COLUMN `replacementCost` DECIMAL(10,2) NULL DEFAULT NULL AFTER `returnNote`,
    ADD KEY `ix_loans_due` (`returnDate`, `dueDate`);

-- Earlier returns had no condition recorded; treat them as good.
UPDATE `loans` SET `returnCondition` = 0 WHERE `returnDate` IS NOT NULL;

CREATE TABLE IF NOT EXISTS `settings` (
    `name`  VARCHAR(50)  NOT NULL,
    `value` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `settings` (`name`, `value`) VALUES
('loanDaysBook', '21'),
('loanDaysComputer', '14'),
('loanDaysKey', '1'),
('maxOpenLoans', '5'),
('blockWhenOverdue', '1');

-- ------------------------------------------------------------------
-- Library Records database schema (version 3).
--
-- For a NEW installation. To upgrade an existing version 1 database
-- without losing data, run upgrade-v1-to-v2.sql and then
-- upgrade-v2-to-v3.sql instead.
--
-- Default sign-in: username "admin", password "ChangeMe123!".
-- Change this password from the Settings page right after signing in.
-- ------------------------------------------------------------------

SET NAMES utf8mb4;
SET time_zone = '+00:00';

CREATE DATABASE IF NOT EXISTS `libraryrecords`
    DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `libraryrecords`;

-- Staff members. Only active administrators (isAdmin = 1) can sign in.
-- Inactive staff (isActive = 0) keep their loan history but cannot
-- borrow or sign in.
CREATE TABLE `personnel` (
    `personnelId` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username`    VARCHAR(50)  NOT NULL,
    `password`    VARCHAR(255) NOT NULL COMMENT 'password_hash() output',
    `fullName`    VARCHAR(100) NOT NULL,
    `email`       VARCHAR(100) NOT NULL DEFAULT '',
    `phone`       VARCHAR(30)  NOT NULL DEFAULT '',
    `isAdmin`     TINYINT(1)   NOT NULL DEFAULT 0,
    `isActive`    TINYINT(1)   NOT NULL DEFAULT 1,
    PRIMARY KEY (`personnelId`),
    UNIQUE KEY `uq_personnel_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Item status, used by books, computers and roomkeys:
-- 0 = active, 1 = in repair, 2 = retired, 3 = lost.
-- Only active items can be lent.
CREATE TABLE `books` (
    `bookId`     INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `bookName`   VARCHAR(100) NOT NULL,
    `bookAuthor` VARCHAR(100) NOT NULL,
    `status` TINYINT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (`bookId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `computers` (
    `computerId`   INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `manufacturer` VARCHAR(50) NOT NULL,
    `model`        VARCHAR(50) NOT NULL,
    `serialNumber` VARCHAR(50) NOT NULL,
    `status` TINYINT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (`computerId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `roomkeys` (
    `keyId`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `keyNumber`      VARCHAR(20)  NOT NULL,
    `keyDescription` VARCHAR(255) NOT NULL,
    `status` TINYINT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (`keyId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- An item is on loan while its loan row has no returnDate.
-- itemType: 0 = book, 1 = computer, 2 = key.
-- dueDate may be NULL for loans made before version 3.
-- returnCondition: 0 = good, 1 = damaged, 2 = incomplete, 3 = lost.
-- Loans are never deleted, so the history stays complete.
CREATE TABLE `loans` (
    `loanId`       INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `itemType`     TINYINT UNSIGNED NOT NULL,
    `itemId`       INT UNSIGNED NOT NULL,
    `loanDate`     DATE NOT NULL,
    `lenderId`     INT UNSIGNED NOT NULL,
    `borrowerId`   INT UNSIGNED NOT NULL,
    `returnDate`   DATE NULL DEFAULT NULL,
    `dueDate`      DATE NULL DEFAULT NULL,
    `receivedById` INT UNSIGNED NULL DEFAULT NULL,
    `returnCondition` TINYINT UNSIGNED NULL DEFAULT NULL,
    `returnNote`   VARCHAR(255) NOT NULL DEFAULT '',
    `replacementCost` DECIMAL(10,2) NULL DEFAULT NULL,
    PRIMARY KEY (`loanId`),
    KEY `ix_loans_item` (`itemType`, `itemId`, `returnDate`),
    KEY `ix_loans_borrower` (`borrowerId`, `returnDate`),
    KEY `ix_loans_due` (`returnDate`, `dueDate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Lending rules, editable on the Settings page.
CREATE TABLE `settings` (
    `name`  VARCHAR(50)  NOT NULL,
    `value` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `settings` (`name`, `value`) VALUES
('loanDaysBook', '21'),
('loanDaysComputer', '14'),
('loanDaysKey', '1'),
('maxOpenLoans', '5'),
('blockWhenOverdue', '1');

-- Failed sign-in attempts, used to slow down password guessing.
CREATE TABLE `login_attempts` (
    `attemptId`   INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username`    VARCHAR(50) NOT NULL,
    `ipAddress`   VARCHAR(45) NOT NULL,
    `attemptedAt` DATETIME NOT NULL,
    PRIMARY KEY (`attemptId`),
    KEY `ix_login_attempts_username` (`username`, `attemptedAt`),
    KEY `ix_login_attempts_ip` (`ipAddress`, `attemptedAt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `personnel` (`username`, `password`, `fullName`, `email`, `phone`, `isAdmin`) VALUES
('admin', '$2y$10$K0gzoKtC5jft2Tef2lOWg.WsliXfC5lWN7bY9mqRvHyTuwDBxnp6W', 'Administrator', '', '', 1);

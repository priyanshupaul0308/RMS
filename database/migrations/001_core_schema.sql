-- =============================================================================
-- RMS: Enterprise Restaurant Management & POS System
-- Phase 1 - Core Database Schema
-- MySQL 5.7+ / MySQL 8.x Compatible | UTF8MB4 | InnoDB | Foreign Keys
-- =============================================================================

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET collation_connection = "utf8mb4_unicode_ci";

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE;
SET SQL_MODE="TRADITIONAL,ALLOW_INVALID_DATES";

-- =============================================================================
-- DATABASE
-- =============================================================================
CREATE DATABASE IF NOT EXISTS `rms_db`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `rms_db`;

-- =============================================================================
-- 1. RESTAURANT MASTER
-- =============================================================================
CREATE TABLE IF NOT EXISTS `restaurants` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`            VARCHAR(150) NOT NULL,
  `legal_name`      VARCHAR(150)          DEFAULT NULL,
  `logo`            VARCHAR(255)          DEFAULT NULL,
  `address`         TEXT                  DEFAULT NULL,
  `city`            VARCHAR(100)          DEFAULT NULL,
  `state`           VARCHAR(100)          DEFAULT NULL,
  `country`         VARCHAR(100)          DEFAULT 'India',
  `postal_code`     VARCHAR(20)           DEFAULT NULL,
  `phone`           VARCHAR(20)           DEFAULT NULL,
  `email`           VARCHAR(150)          DEFAULT NULL,
  `website`         VARCHAR(255)          DEFAULT NULL,
  `gstin`           VARCHAR(20)           DEFAULT NULL,
  `pan`             VARCHAR(20)           DEFAULT NULL,
  `fssai_license`   VARCHAR(30)           DEFAULT NULL,
  `currency_code`   CHAR(3)      NOT NULL DEFAULT 'INR',
  `currency_symbol` VARCHAR(5)   NOT NULL DEFAULT '₹',
  `timezone`        VARCHAR(60)  NOT NULL DEFAULT 'Asia/Kolkata',
  `date_format`     VARCHAR(20)  NOT NULL DEFAULT 'd/m/Y',
  `time_format`     VARCHAR(5)   NOT NULL DEFAULT '12',
  `is_active`       TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- 2. BRANCHES
-- =============================================================================
CREATE TABLE IF NOT EXISTS `branches` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `restaurant_id`   INT UNSIGNED NOT NULL,
  `name`            VARCHAR(150) NOT NULL,
  `code`            VARCHAR(20)           DEFAULT NULL,
  `address`         TEXT                  DEFAULT NULL,
  `city`            VARCHAR(100)          DEFAULT NULL,
  `state`           VARCHAR(100)          DEFAULT NULL,
  `country`         VARCHAR(100)          DEFAULT 'India',
  `postal_code`     VARCHAR(20)           DEFAULT NULL,
  `phone`           VARCHAR(20)           DEFAULT NULL,
  `email`           VARCHAR(150)          DEFAULT NULL,
  `gstin`           VARCHAR(20)           DEFAULT NULL,
  `fssai_license`   VARCHAR(30)           DEFAULT NULL,
  `opening_time`    TIME                  DEFAULT '08:00:00',
  `closing_time`    TIME                  DEFAULT '23:00:00',
  `is_active`       TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_branches_restaurant` (`restaurant_id`),
  CONSTRAINT `fk_branches_restaurant`
    FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- 3. RBAC - ROLES
-- =============================================================================
CREATE TABLE IF NOT EXISTS `roles` (
  `id`          TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(60)      NOT NULL,
  `slug`        VARCHAR(60)      NOT NULL,
  `description` VARCHAR(255)              DEFAULT NULL,
  `is_active`   TINYINT(1)       NOT NULL DEFAULT 1,
  `created_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_roles_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- 4. RBAC - PERMISSIONS
-- =============================================================================
CREATE TABLE IF NOT EXISTS `permissions` (
  `id`          SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `module`      VARCHAR(80)       NOT NULL,
  `action`      VARCHAR(60)       NOT NULL,
  `slug`        VARCHAR(120)      NOT NULL,
  `description` VARCHAR(255)               DEFAULT NULL,
  `created_at`  DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_permissions_slug` (`slug`),
  KEY `idx_permissions_module` (`module`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- 5. RBAC - ROLE PERMISSIONS (pivot)
-- =============================================================================
CREATE TABLE IF NOT EXISTS `role_permissions` (
  `role_id`       TINYINT UNSIGNED  NOT NULL,
  `permission_id` SMALLINT UNSIGNED NOT NULL,
  `granted_at`    DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`role_id`, `permission_id`),
  KEY `idx_rp_permission` (`permission_id`),
  CONSTRAINT `fk_rp_role`
    FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_rp_permission`
    FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- 6. USERS
-- =============================================================================
CREATE TABLE IF NOT EXISTS `users` (
  `id`                   INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `restaurant_id`        INT UNSIGNED     NOT NULL,
  `branch_id`            INT UNSIGNED              DEFAULT NULL,
  `role_id`              TINYINT UNSIGNED NOT NULL,
  `employee_id`          VARCHAR(30)               DEFAULT NULL,
  `first_name`           VARCHAR(80)      NOT NULL,
  `last_name`            VARCHAR(80)               DEFAULT NULL,
  `email`                VARCHAR(150)     NOT NULL,
  `phone`                VARCHAR(20)               DEFAULT NULL,
  `password_hash`        VARCHAR(255)     NOT NULL,
  `avatar`               VARCHAR(255)              DEFAULT NULL,
  `gender`               ENUM('male','female','other','prefer_not_to_say') DEFAULT NULL,
  `date_of_birth`        DATE                      DEFAULT NULL,
  `address`              TEXT                      DEFAULT NULL,
  `emergency_contact`    VARCHAR(100)              DEFAULT NULL,
  `emergency_phone`      VARCHAR(20)               DEFAULT NULL,
  `joining_date`         DATE                      DEFAULT NULL,
  `last_login_at`        DATETIME                  DEFAULT NULL,
  `last_login_ip`        VARCHAR(45)               DEFAULT NULL,
  `failed_login_count`   TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `locked_until`         DATETIME                  DEFAULT NULL,
  `password_changed_at`  DATETIME                  DEFAULT NULL,
  `remember_token`       VARCHAR(100)              DEFAULT NULL,
  `email_verified_at`    DATETIME                  DEFAULT NULL,
  `is_active`            TINYINT(1)       NOT NULL DEFAULT 1,
  `deactivated_at`       DATETIME                  DEFAULT NULL,
  `deactivated_by`       INT UNSIGNED              DEFAULT NULL,
  `created_at`           DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`           DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email` (`email`),
  KEY `idx_users_restaurant`  (`restaurant_id`),
  KEY `idx_users_branch`      (`branch_id`),
  KEY `idx_users_role`        (`role_id`),
  KEY `idx_users_active`      (`is_active`),
  CONSTRAINT `fk_users_restaurant`
    FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_users_branch`
    FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_users_role`
    FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- 7. USER - EXTRA PERMISSIONS (individual overrides)
-- =============================================================================
CREATE TABLE IF NOT EXISTS `user_permissions` (
  `user_id`       INT UNSIGNED      NOT NULL,
  `permission_id` SMALLINT UNSIGNED NOT NULL,
  `granted`       TINYINT(1)        NOT NULL DEFAULT 1,
  `granted_by`    INT UNSIGNED               DEFAULT NULL,
  `granted_at`    DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`, `permission_id`),
  KEY `idx_up_permission` (`permission_id`),
  CONSTRAINT `fk_up_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_up_permission`
    FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- 8. USER SESSIONS
-- =============================================================================
CREATE TABLE IF NOT EXISTS `user_sessions` (
  `id`            VARCHAR(128)     NOT NULL,
  `user_id`       INT UNSIGNED              DEFAULT NULL,
  `ip_address`    VARCHAR(45)      NOT NULL,
  `user_agent`    VARCHAR(512)              DEFAULT NULL,
  `data`          BLOB                      DEFAULT NULL,
  `last_activity` INT UNSIGNED     NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_sessions_user`     (`user_id`),
  KEY `idx_sessions_activity` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- 9. PASSWORD RESET TOKENS
-- =============================================================================
CREATE TABLE IF NOT EXISTS `password_resets` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    INT UNSIGNED NOT NULL,
  `token`      VARCHAR(100) NOT NULL,
  `expires_at` DATETIME     NOT NULL,
  `used_at`    DATETIME              DEFAULT NULL,
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pr_token` (`token`),
  KEY `idx_pr_user`  (`user_id`),
  CONSTRAINT `fk_pr_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- 10. GLOBAL SYSTEM SETTINGS
-- =============================================================================
CREATE TABLE IF NOT EXISTS `settings` (
  `id`            SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `restaurant_id` INT UNSIGNED      NOT NULL,
  `branch_id`     INT UNSIGNED               DEFAULT NULL,
  `group`         VARCHAR(60)       NOT NULL  DEFAULT 'general',
  `key`           VARCHAR(120)      NOT NULL,
  `value`         TEXT                        DEFAULT NULL,
  `value_type`    ENUM('string','integer','decimal','boolean','json') NOT NULL DEFAULT 'string',
  `label`         VARCHAR(150)               DEFAULT NULL,
  `description`   VARCHAR(255)               DEFAULT NULL,
  `is_public`     TINYINT(1)        NOT NULL  DEFAULT 0,
  `updated_by`    INT UNSIGNED               DEFAULT NULL,
  `updated_at`    DATETIME          NOT NULL  DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_settings_scope_key` (`restaurant_id`, `branch_id`, `key`),
  KEY `idx_settings_branch` (`branch_id`),
  KEY `idx_settings_group`  (`group`),
  CONSTRAINT `fk_settings_restaurant`
    FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_settings_branch`
    FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- 11. TAX RATES
-- =============================================================================
CREATE TABLE IF NOT EXISTS `tax_rates` (
  `id`            SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `restaurant_id` INT UNSIGNED      NOT NULL,
  `branch_id`     INT UNSIGNED               DEFAULT NULL,
  `name`          VARCHAR(80)       NOT NULL,
  `rate`          DECIMAL(5,2)      NOT NULL  DEFAULT 0.00,
  `type`          ENUM('inclusive','exclusive') NOT NULL DEFAULT 'exclusive',
  `applies_to`    ENUM('food','beverage','service','all') NOT NULL DEFAULT 'all',
  `is_compound`   TINYINT(1)        NOT NULL  DEFAULT 0,
  `is_active`     TINYINT(1)        NOT NULL  DEFAULT 1,
  `created_at`    DATETIME          NOT NULL  DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    DATETIME          NOT NULL  DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_tax_restaurant` (`restaurant_id`),
  KEY `idx_tax_branch`     (`branch_id`),
  CONSTRAINT `fk_tax_restaurant`
    FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_tax_branch`
    FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- 12. PAYMENT METHODS
-- =============================================================================
CREATE TABLE IF NOT EXISTS `payment_methods` (
  `id`             TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `restaurant_id`  INT UNSIGNED     NOT NULL,
  `name`           VARCHAR(60)      NOT NULL,
  `slug`           VARCHAR(40)      NOT NULL,
  `icon`           VARCHAR(100)              DEFAULT NULL,
  `gateway`        VARCHAR(60)               DEFAULT NULL,
  `gateway_config` JSON                      DEFAULT NULL,
  `surcharge_pct`  DECIMAL(4,2)     NOT NULL DEFAULT 0.00,
  `is_active`      TINYINT(1)       NOT NULL DEFAULT 1,
  `sort_order`     TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `created_at`     DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pm_restaurant_slug` (`restaurant_id`, `slug`),
  CONSTRAINT `fk_pm_restaurant`
    FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- 13. AUDIT TRAIL
-- =============================================================================
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id`            BIGINT UNSIGNED   NOT NULL AUTO_INCREMENT,
  `restaurant_id` INT UNSIGNED               DEFAULT NULL,
  `branch_id`     INT UNSIGNED               DEFAULT NULL,
  `user_id`       INT UNSIGNED               DEFAULT NULL,
  `module`        VARCHAR(80)       NOT NULL,
  `action`        VARCHAR(80)       NOT NULL,
  `record_type`   VARCHAR(80)                DEFAULT NULL,
  `record_id`     BIGINT UNSIGNED            DEFAULT NULL,
  `old_values`    JSON                       DEFAULT NULL,
  `new_values`    JSON                       DEFAULT NULL,
  `description`   TEXT                       DEFAULT NULL,
  `ip_address`    VARCHAR(45)                DEFAULT NULL,
  `user_agent`    VARCHAR(512)               DEFAULT NULL,
  `created_at`    DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_audit_restaurant` (`restaurant_id`),
  KEY `idx_audit_branch`     (`branch_id`),
  KEY `idx_audit_user`       (`user_id`),
  KEY `idx_audit_module`     (`module`),
  KEY `idx_audit_action`     (`action`),
  KEY `idx_audit_record`     (`record_type`, `record_id`),
  KEY `idx_audit_created`    (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- 14. NOTIFICATIONS
-- =============================================================================
CREATE TABLE IF NOT EXISTS `notifications` (
  `id`            BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `restaurant_id` INT UNSIGNED     NOT NULL,
  `branch_id`     INT UNSIGNED              DEFAULT NULL,
  `user_id`       INT UNSIGNED              DEFAULT NULL,
  `type`          VARCHAR(80)      NOT NULL,
  `title`         VARCHAR(200)     NOT NULL,
  `body`          TEXT                      DEFAULT NULL,
  `data`          JSON                      DEFAULT NULL,
  `read_at`       DATETIME                  DEFAULT NULL,
  `created_at`    DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_notif_user`   (`user_id`),
  KEY `idx_notif_branch` (`branch_id`),
  KEY `idx_notif_type`   (`type`),
  KEY `idx_notif_read`   (`read_at`),
  CONSTRAINT `fk_notif_restaurant`
    FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- RESTORE ORIGINAL MODES
-- =============================================================================
SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

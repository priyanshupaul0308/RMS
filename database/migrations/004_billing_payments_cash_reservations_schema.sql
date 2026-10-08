-- ============================================================================
-- RMS - PHASE 5 SCHEMA: BILLING, MULTI-TENDER PAYMENTS, CASH DRAWER & RESERVATIONS
-- Compatible with MySQL 5.7+ and MySQL 8.x
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------------------------------------------------------
-- 1. TABLE RESERVATIONS
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `reservations` (
  `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `restaurant_id`     INT UNSIGNED NOT NULL DEFAULT 1,
  `branch_id`         INT UNSIGNED NULL,
  `table_id`          INT UNSIGNED NULL,
  `customer_name`     VARCHAR(100) NOT NULL,
  `customer_phone`    VARCHAR(20) NOT NULL,
  `customer_email`    VARCHAR(150) NULL,
  `guest_count`       TINYINT UNSIGNED NOT NULL DEFAULT 2,
  `reservation_date`  DATE NOT NULL,
  `reservation_time`  TIME NOT NULL,
  `status`            ENUM('confirmed','seated','cancelled','no_show','completed') NOT NULL DEFAULT 'confirmed',
  `special_requests`  TEXT NULL,
  `created_by`        INT UNSIGNED NULL,
  `created_at`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_res_branch` (`branch_id`),
  KEY `idx_res_date` (`reservation_date`),
  KEY `idx_res_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 2. MULTI-TENDER ORDER PAYMENTS (SPLIT BILLING)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `order_payments` (
  `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id`          INT UNSIGNED NOT NULL,
  `payment_method_id` INT UNSIGNED NOT NULL,
  `amount`            DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `reference_number`  VARCHAR(100) NULL,
  `received_by`       INT UNSIGNED NULL,
  `created_at`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_op_order` (`order_id`),
  KEY `idx_op_method` (`payment_method_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 3. CASH REGISTERS / SHIFTS
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cash_registers` (
  `id`                    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `restaurant_id`         INT UNSIGNED NOT NULL DEFAULT 1,
  `branch_id`             INT UNSIGNED NULL,
  `user_id`               INT UNSIGNED NOT NULL,
  `opened_at`             DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `closed_at`             DATETIME NULL,
  `opening_float`         DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `closing_cash_counted`  DECIMAL(10,2) NULL,
  `expected_cash`         DECIMAL(10,2) NULL,
  `discrepancy`           DECIMAL(10,2) NULL,
  `status`                ENUM('open','closed') NOT NULL DEFAULT 'open',
  `notes`                 TEXT NULL,
  `created_at`            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cr_branch` (`branch_id`),
  KEY `idx_cr_user` (`user_id`),
  KEY `idx_cr_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 4. CASH TRANSACTIONS (CASH-IN / CASH-OUT / DROPS)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cash_transactions` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `register_id`   INT UNSIGNED NOT NULL,
  `type`          ENUM('cash_in','cash_out') NOT NULL,
  `amount`        DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `reason`        VARCHAR(255) NOT NULL,
  `user_id`       INT UNSIGNED NOT NULL,
  `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ct_register` (`register_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- SEED SAMPLE RESERVATIONS
-- ----------------------------------------------------------------------------
INSERT INTO `reservations` (`id`, `restaurant_id`, `branch_id`, `table_id`, `customer_name`, `customer_phone`, `customer_email`, `guest_count`, `reservation_date`, `reservation_time`, `status`, `special_requests`, `created_by`) VALUES
(1, 1, 1, 5, 'Rohan Kapoor', '+91 9820011223', 'rohan.k@example.com', 4, CURRENT_DATE, '19:30:00', 'confirmed', 'Anniversary celebration. Window table preferred.', 1),
(2, 1, 1, 11, 'Ananya Sen',   '+91 9910044556', 'ananya.s@example.com', 8, CURRENT_DATE, '20:00:00', 'confirmed', 'VIP lounge seating. Chef specials menu.', 1)
ON DUPLICATE KEY UPDATE `customer_name`=VALUES(`customer_name`);

SET FOREIGN_KEY_CHECKS = 1;

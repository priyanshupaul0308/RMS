-- ============================================================================
-- RMS - PHASE 4 SCHEMA: KITCHEN WORKFLOW, STATIONS, KOT & REAL-TIME KDS
-- Compatible with MySQL 5.7+ and MySQL 8.x
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------------------------------------------------------
-- 1. KITCHEN STATIONS
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `kitchen_stations` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `restaurant_id`   INT UNSIGNED NOT NULL DEFAULT 1,
  `branch_id`       INT UNSIGNED NULL,
  `name`            VARCHAR(100) NOT NULL,
  `code`            VARCHAR(30) NOT NULL,
  `color_hex`       VARCHAR(10) NOT NULL DEFAULT '#3b82f6',
  `sort_order`      SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `is_active`       TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ks_branch` (`branch_id`),
  KEY `idx_ks_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 2. SEED DEFAULT KITCHEN STATIONS
-- ----------------------------------------------------------------------------
INSERT INTO `kitchen_stations` (`id`, `restaurant_id`, `branch_id`, `name`, `code`, `color_hex`, `sort_order`) VALUES
(1, 1, 1, 'Main Hot Kitchen', 'HOT_KITCHEN', '#ef4444', 1),
(2, 1, 1, 'Tandoor & Charcoal Grill', 'TANDOOR', '#f97316', 2),
(3, 1, 1, 'Bar & Mocktails', 'BAR', '#06b6d4', 3),
(4, 1, 1, 'Desserts & Bakery', 'BAKERY', '#ec4899', 4)
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

SET FOREIGN_KEY_CHECKS = 1;

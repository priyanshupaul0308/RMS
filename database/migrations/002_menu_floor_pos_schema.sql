-- ============================================================================
-- RMS - PHASE 2 & 3 SCHEMA: MENU, FLOORS, TABLES, ORDERS, ITEMS & KOT
-- Compatible with MySQL 5.7+ and MySQL 8.x (InnoDB, UTF8mb4)
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------------------------------------------------------
-- 1. FLOORS
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `floors` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `restaurant_id`   INT UNSIGNED NOT NULL DEFAULT 1,
  `branch_id`       INT UNSIGNED NULL,
  `name`            VARCHAR(100) NOT NULL,
  `floor_number`    INT NOT NULL DEFAULT 1,
  `sort_order`      SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `is_active`       TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_floors_branch` (`branch_id`),
  KEY `idx_floors_restaurant` (`restaurant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 2. RESTAURANT TABLES
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `restaurant_tables` (
  `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `restaurant_id`     INT UNSIGNED NOT NULL DEFAULT 1,
  `branch_id`         INT UNSIGNED NULL,
  `floor_id`          INT UNSIGNED NOT NULL,
  `table_number`      VARCHAR(20) NOT NULL,
  `seating_capacity`  TINYINT UNSIGNED NOT NULL DEFAULT 4,
  `shape`             ENUM('square','rectangle','round') NOT NULL DEFAULT 'square',
  `status`            ENUM('available','occupied','reserved','dirty') NOT NULL DEFAULT 'available',
  `current_order_id`  INT UNSIGNED NULL,
  `is_active`         TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_branch_table` (`branch_id`, `table_number`),
  KEY `idx_tables_floor` (`floor_id`),
  KEY `idx_tables_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 3. MENU CATEGORIES
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `menu_categories` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `restaurant_id`   INT UNSIGNED NOT NULL DEFAULT 1,
  `branch_id`       INT UNSIGNED NULL,
  `name`            VARCHAR(100) NOT NULL,
  `slug`            VARCHAR(120) NOT NULL,
  `icon`            VARCHAR(50) NULL DEFAULT '🍽️',
  `description`     VARCHAR(255) NULL,
  `sort_order`      SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `is_active`       TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cats_branch` (`branch_id`),
  KEY `idx_cats_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 4. MENU ITEMS
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `menu_items` (
  `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `restaurant_id`     INT UNSIGNED NOT NULL DEFAULT 1,
  `branch_id`         INT UNSIGNED NULL,
  `category_id`       INT UNSIGNED NOT NULL,
  `name`              VARCHAR(150) NOT NULL,
  `code`              VARCHAR(30) NULL,
  `description`       TEXT NULL,
  `price`             DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `tax_rate_id`       INT UNSIGNED NULL,
  `is_veg`            TINYINT(1) NOT NULL DEFAULT 1,
  `preparation_time`  SMALLINT UNSIGNED NOT NULL DEFAULT 15,
  `is_available`      TINYINT(1) NOT NULL DEFAULT 1,
  `sort_order`        SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `created_at`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_items_cat` (`category_id`),
  KEY `idx_items_code` (`code`),
  KEY `idx_items_available` (`is_available`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 5. ORDERS
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `orders` (
  `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `restaurant_id`     INT UNSIGNED NOT NULL DEFAULT 1,
  `branch_id`         INT UNSIGNED NULL,
  `order_number`      VARCHAR(40) NOT NULL,
  `order_type`        ENUM('dine_in','takeaway','delivery') NOT NULL DEFAULT 'dine_in',
  `table_id`          INT UNSIGNED NULL,
  `customer_name`     VARCHAR(100) NULL,
  `customer_phone`    VARCHAR(20) NULL,
  `customer_address`  TEXT NULL,
  `waiter_id`         INT UNSIGNED NULL,
  `cashier_id`        INT UNSIGNED NULL,
  `status`            ENUM('pending','confirmed','preparing','ready','served','completed','cancelled') NOT NULL DEFAULT 'confirmed',
  `subtotal`          DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `tax_amount`        DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `discount_amount`   DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `service_charge`    DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `final_total`       DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `payment_status`    ENUM('unpaid','partially_paid','paid') NOT NULL DEFAULT 'unpaid',
  `payment_method_id` INT UNSIGNED NULL,
  `notes`             TEXT NULL,
  `created_at`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_order_number` (`order_number`),
  KEY `idx_orders_branch` (`branch_id`),
  KEY `idx_orders_table` (`table_id`),
  KEY `idx_orders_status` (`status`),
  KEY `idx_orders_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 6. ORDER ITEMS
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `order_items` (
  `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id`          INT UNSIGNED NOT NULL,
  `menu_item_id`      INT UNSIGNED NOT NULL,
  `item_name`         VARCHAR(150) NOT NULL,
  `unit_price`        DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `quantity`          DECIMAL(6,2) NOT NULL DEFAULT 1.00,
  `subtotal`          DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `tax_amount`        DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total`             DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `special_notes`     VARCHAR(255) NULL,
  `status`            ENUM('pending','sent','preparing','ready','served','cancelled') NOT NULL DEFAULT 'pending',
  `kot_id`            INT UNSIGNED NULL,
  `created_at`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_oi_order` (`order_id`),
  KEY `idx_oi_menu_item` (`menu_item_id`),
  KEY `idx_oi_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 7. KITCHEN ORDER TICKETS (KOT)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `kots` (
  `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id`          INT UNSIGNED NOT NULL,
  `branch_id`         INT UNSIGNED NULL,
  `kot_number`        VARCHAR(40) NOT NULL,
  `status`            ENUM('sent','preparing','completed') NOT NULL DEFAULT 'sent',
  `printed_at`        DATETIME NULL,
  `created_at`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_kots_order` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- SEED DATA: FLOORS & TABLES
-- ----------------------------------------------------------------------------
INSERT INTO `floors` (`id`, `restaurant_id`, `branch_id`, `name`, `floor_number`, `sort_order`) VALUES
(1, 1, 1, 'Ground Floor Dining', 1, 1),
(2, 1, 1, 'Rooftop Terrace',     2, 2),
(3, 1, 1, 'VIP Private Lounge',   3, 3)
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

INSERT INTO `restaurant_tables` (`id`, `restaurant_id`, `branch_id`, `floor_id`, `table_number`, `seating_capacity`, `shape`, `status`) VALUES
(1,  1, 1, 1, 'T-01', 2, 'square',    'available'),
(2,  1, 1, 1, 'T-02', 2, 'square',    'available'),
(3,  1, 1, 1, 'T-03', 4, 'square',    'available'),
(4,  1, 1, 1, 'T-04', 4, 'square',    'available'),
(5,  1, 1, 1, 'T-05', 6, 'rectangle', 'available'),
(6,  1, 1, 1, 'T-06', 8, 'rectangle', 'available'),
(7,  1, 1, 2, 'R-01', 2, 'round',     'available'),
(8,  1, 1, 2, 'R-02', 4, 'round',     'available'),
(9,  1, 1, 2, 'R-03', 4, 'round',     'available'),
(10, 1, 1, 2, 'R-04', 6, 'rectangle', 'available'),
(11, 1, 1, 3, 'VIP-1', 10, 'rectangle', 'available'),
(12, 1, 1, 3, 'VIP-2', 12, 'rectangle', 'available')
ON DUPLICATE KEY UPDATE `table_number`=VALUES(`table_number`);

-- ----------------------------------------------------------------------------
-- SEED DATA: MENU CATEGORIES & ITEMS
-- ----------------------------------------------------------------------------
INSERT INTO `menu_categories` (`id`, `restaurant_id`, `branch_id`, `name`, `slug`, `icon`, `description`, `sort_order`) VALUES
(1, 1, NULL, 'Starters & Appetizers', 'starters',     '🥗', 'Crispy appetizers, kebabs, and soups', 1),
(2, 1, NULL, 'Main Course',          'main-course',  '🍛', 'Rich curries, biryanis, and chef specials', 2),
(3, 1, NULL, 'Tandoor & Breads',     'breads',       '🫓', 'Freshly baked naan, roti, and parathas', 3),
(4, 1, NULL, 'Beverages & Mocktails','beverages',    '🍹', 'Artisanal coolers, shakes, and mocktails', 4),
(5, 1, NULL, 'Desserts & Sweets',    'desserts',     '🍨', 'Decadent sweets and gourmet ice creams', 5)
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

INSERT INTO `menu_items` (`id`, `restaurant_id`, `branch_id`, `category_id`, `name`, `code`, `description`, `price`, `tax_rate_id`, `is_veg`, `preparation_time`, `is_available`) VALUES
(1,  1, NULL, 1, 'Paneer Tikka Angaare',     'APP-01', 'Cottage cheese marinated in spices, roasted in clay oven', 320.00, 1, 1, 15, 1),
(2,  1, NULL, 1, 'Crispy Corn & Water Chestnut', 'APP-02', 'Golden fried corn tossed with peppers and scallions', 280.00, 1, 1, 12, 1),
(3,  1, NULL, 1, 'Chicken Malai Tikka',      'APP-03', 'Tender chicken morsels in creamy cardamom marinade', 380.00, 1, 0, 18, 1),
(4,  1, NULL, 1, 'Tandoori Prawns Zaffrani', 'APP-04', 'Jumbo prawns infused with saffron and mustard', 520.00, 1, 0, 20, 1),
(5,  1, NULL, 2, 'Dal Makhani Signature',    'MC-01',  'Slow cooked black lentils simmered overnight with butter', 340.00, 1, 1, 10, 1),
(6,  1, NULL, 2, 'Paneer Butter Masala',     'MC-02',  'Cottage cheese cubes in rich tomato cashew gravy', 360.00, 1, 1, 15, 1),
(7,  1, NULL, 2, 'Butter Chicken Dhabawala', 'MC-03',  'Classic shredded roasted chicken in silky velvety gravy', 440.00, 1, 0, 15, 1),
(8,  1, NULL, 2, 'Dum Gosht Biryani',        'MC-04',  'Fragrant basmati rice layered with spiced mutton dum cooked', 540.00, 1, 0, 20, 1),
(9,  1, NULL, 2, 'Hyderabadi Veg Biryani',   'MC-05',  'Spiced garden vegetables layered with saffron rice', 360.00, 1, 1, 15, 1),
(10, 1, NULL, 3, 'Butter Garlic Naan',       'BRD-01', 'Clay oven leavened bread brushed with garlic & butter', 85.00, 1, 1, 5, 1),
(11, 1, NULL, 3, 'Laccha Paratha',           'BRD-02', 'Crisp layered whole wheat flatbread', 75.00, 1, 1, 5, 1),
(12, 1, NULL, 3, 'Tandoori Roti',            'BRD-03', 'Traditional whole wheat flatbread from the tandoor', 45.00, 1, 1, 5, 1),
(13, 1, NULL, 4, 'Virgin Mojito Blue Lagoon', 'BEV-01', 'Fresh mint, lime, curacao syrup topped with fizz', 180.00, 3, 1, 5, 1),
(14, 1, NULL, 4, 'Mango Kesar Lassi',        'BEV-02', 'Rich churned yogurt with Alphonso mango pulp', 150.00, 3, 1, 5, 1),
(15, 1, NULL, 4, 'Masala Chai Artisan',      'BEV-03', 'Slow brewed tea with ginger, cardamom, and whole spices', 90.00, 3, 1, 5, 1),
(16, 1, NULL, 5, 'Gulab Jamun Flambé',       'DES-01', 'Warm cottage cheese dumplings infused with rose syrup', 180.00, 1, 1, 5, 1),
(17, 1, NULL, 5, 'Kesar Pista Kulfi',        'DES-02', 'Traditional slow reduced milk ice cream on sticks', 160.00, 1, 1, 5, 1),
(18, 1, NULL, 5, 'Sizzling Brownie with Ice Cream', 'DES-03', 'Hot walnut brownie with vanilla ice cream & hot fudge', 240.00, 1, 1, 10, 1)
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

SET FOREIGN_KEY_CHECKS = 1;

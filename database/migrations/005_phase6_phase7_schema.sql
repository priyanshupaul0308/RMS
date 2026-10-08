-- =============================================================================
-- Phase 6 & Phase 7 Schema: Inventory, Procurement, Wastage, CRM, Loyalty & Feedback
-- Compatible with MySQL 5.7+ and MySQL 8.x (InnoDB, utf8mb4)
-- =============================================================================

USE `rms_db`;

-- 1. INVENTORY UNITS
CREATE TABLE IF NOT EXISTS `inventory_units` (
  `id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `restaurant_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `name` VARCHAR(50) NOT NULL,
  `short_code` VARCHAR(15) NOT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_inv_unit_rest` (`restaurant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. INVENTORY CATEGORIES
CREATE TABLE IF NOT EXISTS `inventory_categories` (
  `id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `restaurant_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `name` VARCHAR(80) NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_inv_cat_rest` (`restaurant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. SUPPLIERS / VENDORS
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `restaurant_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `name` VARCHAR(120) NOT NULL,
  `contact_person` VARCHAR(100) DEFAULT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `phone` VARCHAR(25) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `tax_number` VARCHAR(50) DEFAULT NULL,
  `payment_terms` VARCHAR(100) DEFAULT 'Net 30',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_suppliers_rest` (`restaurant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. INVENTORY ITEMS (RAW MATERIALS)
CREATE TABLE IF NOT EXISTS `inventory_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `restaurant_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `branch_id` INT UNSIGNED DEFAULT 1,
  `category_id` SMALLINT UNSIGNED DEFAULT NULL,
  `unit_id` SMALLINT UNSIGNED NOT NULL,
  `supplier_id` INT UNSIGNED DEFAULT NULL,
  `name` VARCHAR(120) NOT NULL,
  `sku` VARCHAR(50) DEFAULT NULL,
  `current_stock` DECIMAL(12,3) NOT NULL DEFAULT 0.000,
  `min_stock_level` DECIMAL(12,3) NOT NULL DEFAULT 5.000,
  `ideal_stock_level` DECIMAL(12,3) NOT NULL DEFAULT 25.000,
  `unit_cost` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_inv_item_sku` (`branch_id`, `sku`),
  KEY `idx_inv_cat` (`category_id`),
  KEY `idx_inv_unit` (`unit_id`),
  KEY `idx_inv_supp` (`supplier_id`),
  CONSTRAINT `fk_inv_unit` FOREIGN KEY (`unit_id`) REFERENCES `inventory_units` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_inv_cat` FOREIGN KEY (`category_id`) REFERENCES `inventory_categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_inv_supp` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. PURCHASE ORDERS
CREATE TABLE IF NOT EXISTS `purchase_orders` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `po_number` VARCHAR(30) NOT NULL,
  `restaurant_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `branch_id` INT UNSIGNED DEFAULT 1,
  `supplier_id` INT UNSIGNED NOT NULL,
  `order_date` DATE NOT NULL,
  `expected_date` DATE DEFAULT NULL,
  `status` ENUM('draft','approved','sent','received','cancelled') NOT NULL DEFAULT 'draft',
  `total_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `notes` TEXT DEFAULT NULL,
  `created_by` INT UNSIGNED DEFAULT NULL,
  `approved_by` INT UNSIGNED DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_po_num` (`po_number`),
  KEY `idx_po_supplier` (`supplier_id`),
  KEY `idx_po_status` (`status`),
  CONSTRAINT `fk_po_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. PURCHASE ORDER ITEMS
CREATE TABLE IF NOT EXISTS `purchase_order_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `purchase_order_id` INT UNSIGNED NOT NULL,
  `inventory_item_id` INT UNSIGNED NOT NULL,
  `quantity_ordered` DECIMAL(12,3) NOT NULL,
  `quantity_received` DECIMAL(12,3) NOT NULL DEFAULT 0.000,
  `unit_price` DECIMAL(10,2) NOT NULL,
  `total_price` DECIMAL(12,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_poi_po` (`purchase_order_id`),
  KEY `idx_poi_item` (`inventory_item_id`),
  CONSTRAINT `fk_poi_po` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_poi_item` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. GOODS RECEIPT NOTES (GRN)
CREATE TABLE IF NOT EXISTS `goods_receipt_notes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `grn_number` VARCHAR(30) NOT NULL,
  `purchase_order_id` INT UNSIGNED DEFAULT NULL,
  `branch_id` INT UNSIGNED DEFAULT 1,
  `supplier_id` INT UNSIGNED NOT NULL,
  `invoice_no` VARCHAR(60) DEFAULT NULL,
  `received_date` DATE NOT NULL,
  `total_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `status` ENUM('received','verified','rejected') NOT NULL DEFAULT 'received',
  `received_by` INT UNSIGNED DEFAULT NULL,
  `notes` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_grn_num` (`grn_number`),
  KEY `idx_grn_po` (`purchase_order_id`),
  KEY `idx_grn_supp` (`supplier_id`),
  CONSTRAINT `fk_grn_supp` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. GRN ITEMS
CREATE TABLE IF NOT EXISTS `goods_receipt_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `grn_id` INT UNSIGNED NOT NULL,
  `inventory_item_id` INT UNSIGNED NOT NULL,
  `quantity_received` DECIMAL(12,3) NOT NULL,
  `unit_price` DECIMAL(10,2) NOT NULL,
  `total_price` DECIMAL(12,2) NOT NULL,
  `expiry_date` DATE DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_gri_grn` (`grn_id`),
  KEY `idx_gri_item` (`inventory_item_id`),
  CONSTRAINT `fk_gri_grn` FOREIGN KEY (`grn_id`) REFERENCES `goods_receipt_notes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_gri_item` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. STOCK ADJUSTMENTS
CREATE TABLE IF NOT EXISTS `stock_adjustments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `branch_id` INT UNSIGNED DEFAULT 1,
  `inventory_item_id` INT UNSIGNED NOT NULL,
  `adjustment_type` ENUM('in','out','reconciliation') NOT NULL,
  `quantity` DECIMAL(12,3) NOT NULL,
  `previous_stock` DECIMAL(12,3) NOT NULL,
  `new_stock` DECIMAL(12,3) NOT NULL,
  `reason` VARCHAR(255) NOT NULL,
  `adjusted_by` INT UNSIGNED DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sa_item` (`inventory_item_id`),
  CONSTRAINT `fk_sa_item` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. WASTE LOGS
CREATE TABLE IF NOT EXISTS `waste_logs` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `branch_id` INT UNSIGNED DEFAULT 1,
  `inventory_item_id` INT UNSIGNED NOT NULL,
  `quantity` DECIMAL(12,3) NOT NULL,
  `cost_impact` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `waste_reason` ENUM('spoilage','expired','damaged','burnt','prep_error','other') NOT NULL DEFAULT 'spoilage',
  `logged_by` INT UNSIGNED DEFAULT NULL,
  `notes` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_wl_item` (`inventory_item_id`),
  KEY `idx_wl_reason` (`waste_reason`),
  CONSTRAINT `fk_wl_item` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. ITEM RECIPES (BOM - Bill of Materials)
CREATE TABLE IF NOT EXISTS `item_recipes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `menu_item_id` INT UNSIGNED NOT NULL,
  `inventory_item_id` INT UNSIGNED NOT NULL,
  `quantity_required` DECIMAL(10,3) NOT NULL,
  `unit_id` SMALLINT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_recipe_link` (`menu_item_id`, `inventory_item_id`),
  KEY `idx_recipe_menu` (`menu_item_id`),
  KEY `idx_recipe_inv` (`inventory_item_id`),
  CONSTRAINT `fk_rec_menu` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_rec_inv` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_rec_unit` FOREIGN KEY (`unit_id`) REFERENCES `inventory_units` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. CRM: CUSTOMERS
CREATE TABLE IF NOT EXISTS `customers` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `restaurant_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `name` VARCHAR(120) NOT NULL,
  `phone` VARCHAR(25) NOT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `loyalty_points` INT NOT NULL DEFAULT 0,
  `total_visits` INT UNSIGNED NOT NULL DEFAULT 0,
  `lifetime_spend` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `vip_status` TINYINT(1) NOT NULL DEFAULT 0,
  `notes` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_customer_phone` (`restaurant_id`, `phone`),
  KEY `idx_crm_spend` (`lifetime_spend`),
  KEY `idx_crm_vip` (`vip_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. LOYALTY TRANSACTIONS
CREATE TABLE IF NOT EXISTS `loyalty_transactions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_id` INT UNSIGNED NOT NULL,
  `order_id` INT UNSIGNED DEFAULT NULL,
  `points_earned` INT NOT NULL DEFAULT 0,
  `points_redeemed` INT NOT NULL DEFAULT 0,
  `balance_after` INT NOT NULL,
  `description` VARCHAR(255) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_lt_customer` (`customer_id`),
  KEY `idx_lt_order` (`order_id`),
  CONSTRAINT `fk_lt_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. CUSTOMER FEEDBACK
CREATE TABLE IF NOT EXISTS `customer_feedback` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_id` INT UNSIGNED DEFAULT NULL,
  `order_id` INT UNSIGNED DEFAULT NULL,
  `customer_name` VARCHAR(120) DEFAULT NULL,
  `customer_phone` VARCHAR(25) DEFAULT NULL,
  `rating` TINYINT UNSIGNED NOT NULL DEFAULT 5,
  `food_rating` TINYINT UNSIGNED NOT NULL DEFAULT 5,
  `service_rating` TINYINT UNSIGNED NOT NULL DEFAULT 5,
  `ambience_rating` TINYINT UNSIGNED NOT NULL DEFAULT 5,
  `comments` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_fb_customer` (`customer_id`),
  KEY `idx_fb_rating` (`rating`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- SEED INITIAL DATA FOR PHASE 6 & 7
-- =============================================================================

-- Units
INSERT INTO `inventory_units` (`id`, `restaurant_id`, `name`, `short_code`) VALUES
(1, 1, 'Kilograms', 'kg'),
(2, 1, 'Grams', 'g'),
(3, 1, 'Liters', 'L'),
(4, 1, 'Milliliters', 'ml'),
(5, 1, 'Pieces', 'pcs'),
(6, 1, 'Packets', 'pkt'),
(7, 1, 'Cans / Bottles', 'can')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- Categories
INSERT INTO `inventory_categories` (`id`, `restaurant_id`, `name`, `description`) VALUES
(1, 1, 'Dairy & Cheese', 'Milk, butter, mozzarella, paneer, cream'),
(2, 1, 'Produce & Vegetables', 'Fresh tomatoes, onions, lettuce, herbs'),
(3, 1, 'Meat & Poultry', 'Chicken, beef patties, bacon, pepperoni'),
(4, 1, 'Bakery & Grains', 'Burger buns, pizza flour, rice, pasta'),
(5, 1, 'Sauces & Spices', 'Olive oil, tomato puree, seasonings, dips'),
(6, 1, 'Beverages & Syrups', 'Soda syrups, coffee beans, tea leaves')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- Suppliers
INSERT INTO `suppliers` (`id`, `restaurant_id`, `name`, `contact_person`, `email`, `phone`, `address`, `tax_number`, `payment_terms`) VALUES
(1, 1, 'Golden Valley Farm Products', 'Mark Spencer', 'orders@goldenvalley.local', '+1 555-0144', '42 Farm Road, Suburbia', 'TAX-GV-98421', 'Net 15'),
(2, 1, 'Oceanic Fresh & Meats Supply', 'Sarah Jenkins', 'sales@oceanicmeats.local', '+1 555-0188', '10 Harbour Bay Blvd', 'TAX-OC-54129', 'Net 30'),
(3, 1, 'Artisan Bakehouse & Mills', 'David Miller', 'david@artisanmills.local', '+1 555-0199', '88 Grain District Way', 'TAX-AB-77123', 'Cash on Delivery')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- Raw Materials (Stock Items)
INSERT INTO `inventory_items` (`id`, `restaurant_id`, `branch_id`, `category_id`, `unit_id`, `supplier_id`, `name`, `sku`, `current_stock`, `min_stock_level`, `ideal_stock_level`, `unit_cost`) VALUES
(1, 1, 1, 1, 1, 1, 'Mozzarella Cheese Block', 'RAW-MOZZ-01', 18.500, 5.000, 25.000, 8.50),
(2, 1, 1, 2, 1, 1, 'Roma Tomatoes (Fresh)', 'RAW-TOM-02', 24.000, 10.000, 40.000, 2.20),
(3, 1, 1, 4, 1, 3, 'Italian 00 Pizza Flour', 'RAW-FLR-03', 45.000, 15.000, 60.000, 1.80),
(4, 1, 1, 3, 1, 2, 'Chicken Breast Fillets', 'RAW-CHK-04', 14.000, 8.000, 30.000, 6.50),
(5, 1, 1, 4, 5, 3, 'Brioche Burger Buns', 'RAW-BUN-05', 80.000, 20.000, 100.000, 0.45),
(6, 1, 1, 3, 1, 2, 'Angus Beef Patties (180g)', 'RAW-BEEF-06', 4.500, 10.000, 35.000, 9.00), -- Low Stock Trigger!
(7, 1, 1, 5, 3, 1, 'Extra Virgin Olive Oil', 'RAW-OIL-07', 12.000, 4.000, 20.000, 11.00),
(8, 1, 1, 6, 1, 1, 'Espresso Whole Beans', 'RAW-COF-08', 9.500, 3.000, 15.000, 14.50)
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- Seed sample Purchase Order
INSERT INTO `purchase_orders` (`id`, `po_number`, `restaurant_id`, `branch_id`, `supplier_id`, `order_date`, `expected_date`, `status`, `total_amount`, `notes`, `created_by`) VALUES
(1, 'PO-2026-001', 1, 1, 2, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 2 DAY), 'sent', 240.00, 'Urgent restocking for Angus beef patties and chicken fillets', 1)
ON DUPLICATE KEY UPDATE `po_number`=VALUES(`po_number`);

INSERT INTO `purchase_order_items` (`id`, `purchase_order_id`, `inventory_item_id`, `quantity_ordered`, `quantity_received`, `unit_price`, `total_price`) VALUES
(1, 1, 6, 20.000, 0.000, 9.00, 180.00),
(2, 1, 4, 10.000, 0.000, 6.00, 60.00)
ON DUPLICATE KEY UPDATE `quantity_ordered`=VALUES(`quantity_ordered`);

-- Seed sample Recipe Links for Menu Items
-- Link Margherita Pizza (Item 1) -> Flour (0.25kg), Mozzarella (0.15kg), Tomatoes (0.20kg)
INSERT INTO `item_recipes` (`menu_item_id`, `inventory_item_id`, `quantity_required`, `unit_id`) VALUES
(1, 3, 0.250, 1),
(1, 1, 0.150, 1),
(1, 2, 0.200, 1)
ON DUPLICATE KEY UPDATE `quantity_required`=VALUES(`quantity_required`);

-- Seed Sample Customers & Loyalty
INSERT INTO `customers` (`id`, `restaurant_id`, `name`, `phone`, `email`, `address`, `loyalty_points`, `total_visits`, `lifetime_spend`, `vip_status`, `notes`) VALUES
(1, 1, 'Arthur Pendelton', '+1 555-0921', 'arthur.p@example.com', '742 Evergreen Terrace', 350, 12, 1420.50, 1, 'VIP regular. Prefers Booth Table 2. Sparkling water with lime.'),
(2, 1, 'Elena Rostova', '+1 555-0814', 'elena.r@example.com', '19 High St, Apt 4B', 120, 5, 485.00, 0, 'Vegetarian preferences. Loves extra cheese on Margherita.'),
(3, 1, 'Marcus Vance', '+1 555-0763', 'marcus.v@example.com', '124 Oakwood Avenue', 50, 2, 195.00, 0, 'First registered via online reservation.')
ON DUPLICATE KEY UPDATE `phone`=VALUES(`phone`);

-- Seed Loyalty History
INSERT INTO `loyalty_transactions` (`customer_id`, `order_id`, `points_earned`, `points_redeemed`, `balance_after`, `description`) VALUES
(1, NULL, 150, 0, 150, 'Welcome bonus & Dine-in bill ORD-000001'),
(1, NULL, 200, 0, 350, 'Celebration dinner order settlement ORD-000005'),
(2, NULL, 120, 0, 120, 'Weekend family lunch order ORD-000008')
ON DUPLICATE KEY UPDATE `description`=VALUES(`description`);

-- Seed Customer Feedback
INSERT INTO `customer_feedback` (`customer_id`, `order_id`, `customer_name`, `customer_phone`, `rating`, `food_rating`, `service_rating`, `ambience_rating`, `comments`) VALUES
(1, NULL, 'Arthur Pendelton', '+1 555-0921', 5, 5, 5, 5, 'Exceptional experience as always. The pizza crust was cooked to perfection and staff attentive!'),
(2, NULL, 'Elena Rostova', '+1 555-0814', 4, 5, 4, 4, 'Great pizza, fast service. Will definitely visit again with friends.')
ON DUPLICATE KEY UPDATE `customer_name`=VALUES(`customer_name`);

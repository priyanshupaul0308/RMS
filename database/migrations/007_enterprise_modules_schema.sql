-- ============================================================================
-- RMS Migration 007: Enterprise Modules
-- 1. Staff Attendance & Shifts
-- 2. Discount & Coupon Management
-- 3. Delivery Partner Management
-- 4. Expense Management
-- 5. Staff Performance Reviews
-- 6. Equipment Maintenance Management
-- ============================================================================

-- ----------------------------------------------------------------------------
-- 1. Staff Attendance & Shifts
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `shifts` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `restaurant_id` INT UNSIGNED NOT NULL DEFAULT 1,
    `branch_id` INT UNSIGNED NOT NULL DEFAULT 1,
    `name` VARCHAR(80) NOT NULL,
    `start_time` TIME NOT NULL,
    `end_time` TIME NOT NULL,
    `break_duration_mins` SMALLINT UNSIGNED NOT NULL DEFAULT 30,
    `color_code` VARCHAR(10) NOT NULL DEFAULT '#3b82f6',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_shifts_branch` (`branch_id`, `is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `staff_shifts` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `shift_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `branch_id` INT UNSIGNED NOT NULL DEFAULT 1,
    `shift_date` DATE NOT NULL,
    `status` ENUM('scheduled', 'completed', 'swapped', 'cancelled') NOT NULL DEFAULT 'scheduled',
    `notes` VARCHAR(255) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_ss_date_user` (`shift_date`, `user_id`),
    INDEX `idx_ss_branch` (`branch_id`, `shift_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `staff_attendance` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `branch_id` INT UNSIGNED NOT NULL DEFAULT 1,
    `shift_id` INT UNSIGNED NULL,
    `date` DATE NOT NULL,
    `check_in` DATETIME NOT NULL,
    `check_out` DATETIME NULL,
    `total_hours` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    `overtime_hours` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    `status` ENUM('present', 'late', 'half_day', 'absent', 'on_leave') NOT NULL DEFAULT 'present',
    `check_in_ip` VARCHAR(45) NULL,
    `check_out_ip` VARCHAR(45) NULL,
    `notes` VARCHAR(255) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_att_user_date` (`user_id`, `date`),
    INDEX `idx_att_branch_date` (`branch_id`, `date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 2. Discount & Coupon Management
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `coupons` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `restaurant_id` INT UNSIGNED NOT NULL DEFAULT 1,
    `branch_id` INT UNSIGNED NULL,
    `code` VARCHAR(40) NOT NULL UNIQUE,
    `name` VARCHAR(120) NOT NULL,
    `type` ENUM('percentage', 'fixed') NOT NULL DEFAULT 'percentage',
    `value` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `min_order_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `max_discount_amount` DECIMAL(10,2) NULL,
    `usage_limit_total` INT UNSIGNED NOT NULL DEFAULT 100,
    `usage_limit_per_user` INT UNSIGNED NOT NULL DEFAULT 1,
    `times_used` INT UNSIGNED NOT NULL DEFAULT 0,
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_coupon_code_active` (`code`, `is_active`),
    INDEX `idx_coupon_dates` (`start_date`, `end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `coupon_usages` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `coupon_id` INT UNSIGNED NOT NULL,
    `order_id` INT UNSIGNED NOT NULL,
    `customer_id` INT UNSIGNED NULL,
    `discount_amount` DECIMAL(10,2) NOT NULL,
    `used_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_cu_coupon` (`coupon_id`),
    INDEX `idx_cu_order` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 3. Delivery Partner Management
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `delivery_partners` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `restaurant_id` INT UNSIGNED NOT NULL DEFAULT 1,
    `branch_id` INT UNSIGNED NOT NULL DEFAULT 1,
    `name` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(25) NOT NULL,
    `email` VARCHAR(100) NULL,
    `vehicle_type` ENUM('bike', 'scooter', 'car', 'van') NOT NULL DEFAULT 'bike',
    `vehicle_number` VARCHAR(30) NOT NULL,
    `commission_rate` DECIMAL(5,2) NOT NULL DEFAULT 15.00,
    `availability_status` ENUM('available', 'on_delivery', 'offline', 'on_break') NOT NULL DEFAULT 'available',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `rating` DECIMAL(3,2) NOT NULL DEFAULT 5.00,
    `total_deliveries` INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_dp_branch_avail` (`branch_id`, `availability_status`, `is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `delivery_orders` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT UNSIGNED NOT NULL,
    `partner_id` INT UNSIGNED NOT NULL,
    `branch_id` INT UNSIGNED NOT NULL DEFAULT 1,
    `assigned_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `picked_up_at` DATETIME NULL,
    `delivered_at` DATETIME NULL,
    `status` ENUM('assigned', 'picked_up', 'in_transit', 'delivered', 'failed') NOT NULL DEFAULT 'assigned',
    `delivery_fee` DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    `tip_amount` DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    `customer_rating` TINYINT UNSIGNED NULL,
    `delivery_notes` VARCHAR(255) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_do_partner` (`partner_id`, `status`),
    INDEX `idx_do_order` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 4. Expense Management
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `expense_categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `restaurant_id` INT UNSIGNED NOT NULL DEFAULT 1,
    `name` VARCHAR(80) NOT NULL,
    `description` VARCHAR(255) NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `expenses` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `restaurant_id` INT UNSIGNED NOT NULL DEFAULT 1,
    `branch_id` INT UNSIGNED NOT NULL DEFAULT 1,
    `category_id` INT UNSIGNED NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `expense_date` DATE NOT NULL,
    `payment_method` ENUM('cash', 'bank_transfer', 'company_card', 'cheque') NOT NULL DEFAULT 'cash',
    `vendor_name` VARCHAR(120) NULL,
    `invoice_receipt_no` VARCHAR(60) NULL,
    `receipt_file` VARCHAR(255) NULL,
    `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'approved',
    `recorded_by` INT UNSIGNED NOT NULL,
    `approved_by` INT UNSIGNED NULL,
    `approved_at` DATETIME NULL,
    `notes` TEXT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_exp_branch_date` (`branch_id`, `expense_date`),
    INDEX `idx_exp_category` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 5. Staff Performance Reviews
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `staff_performance_reviews` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `branch_id` INT UNSIGNED NOT NULL DEFAULT 1,
    `reviewer_id` INT UNSIGNED NOT NULL,
    `review_period` VARCHAR(40) NOT NULL,
    `rating_attendance` TINYINT UNSIGNED NOT NULL DEFAULT 5,
    `rating_punctuality` TINYINT UNSIGNED NOT NULL DEFAULT 5,
    `rating_order_accuracy` TINYINT UNSIGNED NOT NULL DEFAULT 5,
    `rating_hospitality` TINYINT UNSIGNED NOT NULL DEFAULT 5,
    `overall_score` DECIMAL(3,2) NOT NULL DEFAULT 5.00,
    `strengths` TEXT NULL,
    `areas_for_improvement` TEXT NULL,
    `goals` TEXT NULL,
    `review_date` DATE NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_spr_user` (`user_id`),
    INDEX `idx_spr_branch_date` (`branch_id`, `review_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 6. Equipment Maintenance Management
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `equipment` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `restaurant_id` INT UNSIGNED NOT NULL DEFAULT 1,
    `branch_id` INT UNSIGNED NOT NULL DEFAULT 1,
    `name` VARCHAR(120) NOT NULL,
    `model_number` VARCHAR(60) NULL,
    `serial_number` VARCHAR(60) NULL,
    `location` VARCHAR(80) NOT NULL DEFAULT 'Main Kitchen',
    `purchase_date` DATE NULL,
    `warranty_expiry` DATE NULL,
    `cost` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `status` ENUM('operational', 'under_maintenance', 'broken', 'retired') NOT NULL DEFAULT 'operational',
    `last_serviced_date` DATE NULL,
    `next_service_due` DATE NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_eq_branch_status` (`branch_id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `maintenance_requests` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `equipment_id` INT UNSIGNED NOT NULL,
    `branch_id` INT UNSIGNED NOT NULL DEFAULT 1,
    `title` VARCHAR(150) NOT NULL,
    `priority` ENUM('low', 'medium', 'high', 'emergency') NOT NULL DEFAULT 'medium',
    `description` TEXT NOT NULL,
    `reported_by` INT UNSIGNED NOT NULL,
    `assigned_to_vendor` VARCHAR(120) NULL,
    `cost` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `status` ENUM('reported', 'in_progress', 'completed', 'cancelled') NOT NULL DEFAULT 'reported',
    `reported_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `completed_at` DATETIME NULL,
    `resolution_notes` TEXT NULL,
    `invoice_number` VARCHAR(60) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_mr_equipment` (`equipment_id`),
    INDEX `idx_mr_branch_status` (`branch_id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Seed Initial Sample Data & Categories
-- ----------------------------------------------------------------------------
-- Shifts
INSERT INTO `shifts` (`id`, `restaurant_id`, `branch_id`, `name`, `start_time`, `end_time`, `break_duration_mins`, `color_code`, `is_active`)
VALUES 
(1, 1, 1, 'Morning Prep & Lunch', '08:00:00', '16:00:00', 45, '#3b82f6', 1),
(2, 1, 1, 'Evening Dinner Rush', '16:00:00', '00:00:00', 45, '#f59e0b', 1),
(3, 1, 1, 'Late Night Cleaning', '22:00:00', '04:00:00', 30, '#8b5cf6', 1)
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- Coupons
INSERT INTO `coupons` (`id`, `restaurant_id`, `branch_id`, `code`, `name`, `type`, `value`, `min_order_amount`, `max_discount_amount`, `usage_limit_total`, `usage_limit_per_user`, `times_used`, `start_date`, `end_date`, `is_active`)
VALUES
(1, 1, 1, 'WELCOME20', 'New Guest Welcome 20% Off', 'percentage', 20.00, 300.00, 200.00, 500, 1, 14, '2026-01-01', '2026-12-31', 1),
(2, 1, 1, 'FLAT100', 'Flat ₹100 Off on Feast', 'fixed', 100.00, 600.00, 100.00, 200, 2, 8, '2026-01-01', '2026-12-31', 1),
(3, 1, 1, 'VIPDINER', 'VIP Diner 15% Privilege', 'percentage', 15.00, 500.00, 350.00, 1000, 5, 23, '2026-01-01', '2026-12-31', 1)
ON DUPLICATE KEY UPDATE `code`=VALUES(`code`);

-- Delivery Partners
INSERT INTO `delivery_partners` (`id`, `restaurant_id`, `branch_id`, `name`, `phone`, `email`, `vehicle_type`, `vehicle_number`, `commission_rate`, `availability_status`, `is_active`, `rating`, `total_deliveries`)
VALUES
(1, 1, 1, 'Rajesh Kumar', '+91 9811223344', 'rajesh.delivery@rms.local', 'bike', 'DL-01-AB-1234', 15.00, 'available', 1, 4.90, 84),
(2, 1, 1, 'Amit Sharma', '+91 9822334455', 'amit.rider@rms.local', 'scooter', 'DL-02-CD-5678', 15.00, 'on_delivery', 1, 4.75, 62),
(3, 1, 1, 'Sunil Yadav', '+91 9833445566', 'sunil.yadav@rms.local', 'bike', 'DL-03-EF-9012', 15.00, 'available', 1, 4.85, 41)
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- Expense Categories
INSERT INTO `expense_categories` (`id`, `restaurant_id`, `name`, `description`, `is_active`)
VALUES
(1, 1, 'Kitchen & Cooking Gas', 'Commercial LPG cylinders and fuel', 1),
(2, 1, 'Cleaning & Sanitation Supplies', 'Detergents, sanitizers, mop pads, pest control', 1),
(3, 1, 'Repairs & Equipment Maintenance', 'Oven servicing, HVAC repair, plumbing, fridge gas', 1),
(4, 1, 'Staff Meals & Welfare', 'Staff duty food, water supply, tea/coffee', 1),
(5, 1, 'Printing & Packaging Materials', 'Takeaway containers, thermal paper rolls, bags', 1),
(6, 1, 'Utilities & Electricity', 'Municipal water, high-load commercial power', 1)
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- Equipment
INSERT INTO `equipment` (`id`, `restaurant_id`, `branch_id`, `name`, `model_number`, `serial_number`, `location`, `purchase_date`, `warranty_expiry`, `cost`, `status`, `last_serviced_date`, `next_service_due`)
VALUES
(1, 1, 1, 'Commercial Deck Pizza Oven', 'MKN-PO900', 'SN-OVEN-9921', 'Baking & Pizza Station', '2024-03-15', '2027-03-15', 185000.00, 'operational', '2026-08-10', '2026-11-10'),
(2, 1, 1, 'Blast Chiller & Deep Freezer 4-Door', 'FRIG-4D-PRO', 'SN-FRZ-4412', 'Prep & Cold Storage', '2024-01-20', '2027-01-20', 145000.00, 'operational', '2026-07-05', '2026-10-05'),
(3, 1, 1, 'La Marzocco Espresso Machine 2-Group', 'LM-LINEA-PB', 'SN-ESP-8819', 'Bar & Beverage Counter', '2024-05-10', '2026-05-10', 320000.00, 'operational', '2026-09-01', '2026-12-01'),
(4, 1, 1, 'Commercial 2-Tank Electric Deep Fryer', 'FRY-TANK-20L', 'SN-FRY-1102', 'Hot Kitchen Line', '2024-04-12', '2026-04-12', 48000.00, 'under_maintenance', '2026-06-20', '2026-09-20')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- Maintenance Requests
INSERT INTO `maintenance_requests` (`id`, `equipment_id`, `branch_id`, `title`, `priority`, `description`, `reported_by`, `assigned_to_vendor`, `cost`, `status`, `reported_at`, `completed_at`, `resolution_notes`, `invoice_number`)
VALUES
(1, 4, 1, 'Thermostat sensor tripping on Tank 2', 'high', 'Right tank overheating past 190C and shutting off safety relay.', 1, 'CoolTech Commercial Kitchen Repairs', 3500.00, 'in_progress', '2026-10-01 10:30:00', NULL, 'Technician dispatched with replacement thermostat probe.', 'INV-CT-9012')
ON DUPLICATE KEY UPDATE `title`=VALUES(`title`);

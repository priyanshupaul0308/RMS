-- ============================================================================
-- RMS Migration 008: Accounting & Finance and Notification & Communication
-- Module 32: Accounting & Finance
-- Module 35: Notification & Communication
-- ============================================================================

-- ----------------------------------------------------------------------------
-- Module 32: Accounting & Finance Tables
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `chart_of_accounts` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `restaurant_id` INT UNSIGNED NOT NULL DEFAULT 1,
    `code` VARCHAR(20) NOT NULL UNIQUE,
    `name` VARCHAR(100) NOT NULL,
    `type` ENUM('asset', 'liability', 'equity', 'revenue', 'expense') NOT NULL,
    `category` VARCHAR(50) NOT NULL,
    `current_balance` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `journal_entries` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `restaurant_id` INT UNSIGNED NOT NULL DEFAULT 1,
    `branch_id` INT UNSIGNED NOT NULL DEFAULT 1,
    `account_id` INT UNSIGNED NOT NULL,
    `entry_date` DATE NOT NULL,
    `reference_type` ENUM('order_sale', 'purchase_order', 'operating_expense', 'tax_payment', 'manual') NOT NULL DEFAULT 'manual',
    `reference_id` INT UNSIGNED NULL,
    `description` VARCHAR(255) NOT NULL,
    `debit` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `credit` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `created_by` INT UNSIGNED NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_je_branch_date` (`branch_id`, `entry_date`),
    INDEX `idx_je_account` (`account_id`),
    INDEX `idx_je_ref` (`reference_type`, `reference_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Module 35: Notification & Communication Tables
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `communication_logs` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `restaurant_id` INT UNSIGNED NOT NULL DEFAULT 1,
    `branch_id` INT UNSIGNED NOT NULL DEFAULT 1,
    `recipient_type` ENUM('customer', 'staff', 'broadcast') NOT NULL DEFAULT 'customer',
    `recipient_id` INT UNSIGNED NULL,
    `recipient_name` VARCHAR(100) NOT NULL,
    `recipient_contact` VARCHAR(100) NOT NULL,
    `channel` ENUM('in_app', 'sms', 'email', 'whatsapp') NOT NULL DEFAULT 'in_app',
    `category` ENUM('order', 'reservation', 'payment', 'low_stock', 'announcement', 'promo') NOT NULL DEFAULT 'order',
    `subject` VARCHAR(150) NOT NULL,
    `message` TEXT NOT NULL,
    `status` ENUM('queued', 'sent', 'delivered', 'failed') NOT NULL DEFAULT 'sent',
    `sent_by` INT UNSIGNED NOT NULL,
    `sent_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_cl_branch_cat` (`branch_id`, `category`),
    INDEX `idx_cl_channel_status` (`channel`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Seed Chart of Accounts
-- ----------------------------------------------------------------------------
INSERT INTO `chart_of_accounts` (`id`, `restaurant_id`, `code`, `name`, `type`, `category`, `current_balance`, `is_active`)
VALUES 
(1, 1, '1010', 'Main Cash Register', 'asset', 'Current Assets', 25000.00, 1),
(2, 1, '1020', 'Bank Operating Account', 'asset', 'Current Assets', 185000.00, 1),
(3, 1, '1030', 'Accounts Receivable (Credit)', 'asset', 'Current Assets', 0.00, 1),
(4, 1, '1040', 'Food & Beverage Inventory', 'asset', 'Inventory', 84000.00, 1),
(5, 1, '2010', 'Accounts Payable (Vendors)', 'liability', 'Current Liabilities', 32000.00, 1),
(6, 1, '2020', 'GST / VAT Tax Payable', 'liability', 'Current Liabilities', 14500.00, 1),
(7, 1, '3010', 'Owner Capital & Equity', 'equity', 'Equity', 200000.00, 1),
(8, 1, '4010', 'Dine-In Food & Beverage Revenue', 'revenue', 'Sales Revenue', 0.00, 1),
(9, 1, '4020', 'Takeaway & Delivery Revenue', 'revenue', 'Sales Revenue', 0.00, 1),
(10, 1, '4030', 'Delivery Fee Income', 'revenue', 'Service Revenue', 0.00, 1),
(11, 1, '5010', 'Cost of Goods Sold (Raw Ingredients)', 'expense', 'COGS', 0.00, 1),
(12, 1, '6010', 'Staff Wages & Overtime', 'expense', 'Operating Expenses', 0.00, 1),
(13, 1, '6020', 'Kitchen LPG & Utilities', 'expense', 'Operating Expenses', 0.00, 1),
(14, 1, '6030', 'Equipment Repairs & Maintenance', 'expense', 'Operating Expenses', 0.00, 1),
(15, 1, '6040', 'Promotional Discounts Given', 'expense', 'Marketing & Sales', 0.00, 1)
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- ----------------------------------------------------------------------------
-- Seed Initial Communication Announcements & Alerts
-- ----------------------------------------------------------------------------
INSERT INTO `communication_logs` (`id`, `restaurant_id`, `branch_id`, `recipient_type`, `recipient_name`, `recipient_contact`, `channel`, `category`, `subject`, `message`, `status`, `sent_by`, `sent_at`)
VALUES
(1, 1, 1, 'staff', 'All Kitchen Staff', 'team-kitchen@rms.local', 'in_app', 'announcement', 'Weekend Special Menu Briefing', 'All station leads please note the updated plating guidelines for the Chef Special Truffle Risotto.', 'delivered', 1, '2026-10-01 09:00:00'),
(2, 1, 1, 'customer', 'Vikram Malhotra', '+91 9876543210', 'whatsapp', 'reservation', 'Table Reservation Confirmed', 'Dear Vikram, your reservation for Table #4 (4 guests) at 7:30 PM is confirmed. We look forward to serving you!', 'delivered', 1, '2026-10-02 14:15:00'),
(3, 1, 1, 'staff', 'Inventory Manager', 'inventory@rms.local', 'in_app', 'low_stock', 'Low Stock Alert: Basmati Rice', 'Warning: Basmati Royal Rice has dropped below safety threshold (8.5 kg remaining). Please review PO.', 'delivered', 1, '2026-10-02 16:30:00'),
(4, 1, 1, 'customer', 'Ananya Roy', '+91 9811223344', 'sms', 'order', 'Delivery Order #ORD-20261002-0014 Picked Up', 'Your delicious food order has been picked up by our delivery rider Rajesh Kumar and is on the way!', 'delivered', 1, '2026-10-02 19:45:00')
ON DUPLICATE KEY UPDATE `subject`=VALUES(`subject`);

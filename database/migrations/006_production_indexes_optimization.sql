-- =============================================================================
-- Production Database Performance Optimization & Index Hardening
-- Ensures zero full-table-scans on high-throughput POS, KDS & Analytics tables
-- =============================================================================

USE `rms_db`;

-- Indexes on orders
CREATE INDEX IF NOT EXISTS `idx_orders_branch_status` ON `orders` (`branch_id`, `status`);
CREATE INDEX IF NOT EXISTS `idx_orders_created` ON `orders` (`created_at`);
CREATE INDEX IF NOT EXISTS `idx_orders_table` ON `orders` (`table_id`);
CREATE INDEX IF NOT EXISTS `idx_orders_payment` ON `orders` (`payment_status`);

-- Indexes on order_items
CREATE INDEX IF NOT EXISTS `idx_oi_order` ON `order_items` (`order_id`);
CREATE INDEX IF NOT EXISTS `idx_oi_menu_item` ON `order_items` (`menu_item_id`);
CREATE INDEX IF NOT EXISTS `idx_oi_kot` ON `order_items` (`kot_id`);

-- Indexes on kots
CREATE INDEX IF NOT EXISTS `idx_kots_branch_status` ON `kots` (`branch_id`, `status`);
CREATE INDEX IF NOT EXISTS `idx_kots_created` ON `kots` (`created_at`);

-- Indexes on inventory_items
CREATE INDEX IF NOT EXISTS `idx_inv_branch_active` ON `inventory_items` (`branch_id`, `is_active`);

-- Indexes on customers
CREATE INDEX IF NOT EXISTS `idx_cust_phone` ON `customers` (`phone`);

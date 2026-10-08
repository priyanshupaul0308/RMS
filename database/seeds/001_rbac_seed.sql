-- =============================================================================
-- RMS Seed Data – RBAC Roles, Permissions & Default Admin
-- =============================================================================
USE `rms_db`;

-- ----------------------------------------------------------------------------
-- ROLES
-- ----------------------------------------------------------------------------
INSERT INTO `roles` (`id`, `name`, `slug`, `description`) VALUES
(1, 'Administrator', 'admin',            'Full system access across all branches'),
(2, 'Manager',       'manager',          'Branch management and reporting access'),
(3, 'Cashier',       'cashier',          'Billing, payments and POS access'),
(4, 'Waiter',        'waiter',           'Table service, order taking and KOT'),
(5, 'Chef',          'chef',             'Kitchen display, KOT management and recipes'),
(6, 'Inventory Staff','inventory_staff', 'Inventory, purchases and GRN management')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- ----------------------------------------------------------------------------
-- PERMISSIONS  (module.action pattern)
-- ----------------------------------------------------------------------------
INSERT INTO `permissions` (`module`, `action`, `slug`, `description`) VALUES
-- Dashboard
('dashboard',       'view',   'dashboard.view',         'View main dashboard'),
-- Restaurants
('restaurants',     'view',   'restaurants.view',       'View restaurant settings'),
('restaurants',     'edit',   'restaurants.edit',       'Edit restaurant settings'),
-- Branches
('branches',        'view',   'branches.view',          'View branches'),
('branches',        'create', 'branches.create',        'Create branches'),
('branches',        'edit',   'branches.edit',          'Edit branches'),
('branches',        'delete', 'branches.delete',        'Delete branches'),
-- Users
('users',           'view',   'users.view',             'View users'),
('users',           'create', 'users.create',           'Create users'),
('users',           'edit',   'users.edit',             'Edit users'),
('users',           'delete', 'users.delete',           'Delete/deactivate users'),
-- Roles & Permissions
('roles',           'view',   'roles.view',             'View roles'),
('roles',           'edit',   'roles.edit',             'Assign role permissions'),
-- Menu Categories
('menu_categories', 'view',   'menu_categories.view',   'View menu categories'),
('menu_categories', 'create', 'menu_categories.create', 'Create menu categories'),
('menu_categories', 'edit',   'menu_categories.edit',   'Edit menu categories'),
('menu_categories', 'delete', 'menu_categories.delete', 'Delete menu categories'),
-- Menu Items
('menu_items',      'view',   'menu_items.view',        'View menu items'),
('menu_items',      'create', 'menu_items.create',      'Create menu items'),
('menu_items',      'edit',   'menu_items.edit',        'Edit menu items'),
('menu_items',      'delete', 'menu_items.delete',      'Delete menu items'),
-- Tables & Floors
('floors',          'view',   'floors.view',            'View floor layouts'),
('floors',          'manage', 'floors.manage',          'Create/edit floor layouts and tables'),
-- Reservations
('reservations',    'view',   'reservations.view',      'View reservations'),
('reservations',    'create', 'reservations.create',    'Create reservations'),
('reservations',    'edit',   'reservations.edit',      'Edit reservations'),
('reservations',    'delete', 'reservations.delete',    'Cancel reservations'),
-- Orders
('orders',          'view',   'orders.view',            'View orders'),
('orders',          'create', 'orders.create',          'Create new orders'),
('orders',          'edit',   'orders.edit',            'Modify open orders'),
('orders',          'cancel', 'orders.cancel',          'Cancel orders'),
('orders',          'void',   'orders.void',            'Void finalized orders'),
-- KOT / Kitchen
('kot',             'view',   'kot.view',               'View KOT queue'),
('kot',             'manage', 'kot.manage',             'Update KOT status'),
('kds',             'view',   'kds.view',               'Access Kitchen Display System'),
-- Billing
('billing',         'view',   'billing.view',           'View bills'),
('billing',         'create', 'billing.create',         'Generate bills'),
('billing',         'discount','billing.discount',      'Apply discounts on bills'),
('billing',         'void',   'billing.void',           'Void/cancel bills'),
-- Payments
('payments',        'view',   'payments.view',          'View payments'),
('payments',        'process','payments.process',       'Process payments'),
('payments',        'refund', 'payments.refund',        'Process refunds'),
-- POS
('pos',             'access', 'pos.access',             'Access POS terminal'),
-- Inventory
('inventory',       'view',   'inventory.view',         'View inventory'),
('inventory',       'adjust', 'inventory.adjust',       'Manual stock adjustments'),
('inventory',       'export', 'inventory.export',       'Export inventory reports'),
-- Purchases
('purchases',       'view',   'purchases.view',         'View purchase orders'),
('purchases',       'create', 'purchases.create',       'Create purchase orders'),
('purchases',       'approve','purchases.approve',      'Approve purchase orders'),
-- GRN
('grn',             'view',   'grn.view',               'View goods received notes'),
('grn',             'create', 'grn.create',             'Create GRN entries'),
-- Waste
('waste',           'view',   'waste.view',             'View waste logs'),
('waste',           'create', 'waste.create',           'Log waste entries'),
-- Customers
('customers',       'view',   'customers.view',         'View customer profiles'),
('customers',       'create', 'customers.create',       'Create customer profiles'),
('customers',       'edit',   'customers.edit',         'Edit customer profiles'),
-- Reports
('reports',         'view',   'reports.view',           'Access all reports'),
('reports',         'export', 'reports.export',         'Export reports'),
-- Staff
('staff',           'view',   'staff.view',             'View staff records'),
('staff',           'manage', 'staff.manage',           'Manage staff records'),
-- Attendance
('attendance',      'view',   'attendance.view',        'View attendance records'),
('attendance',      'manage', 'attendance.manage',      'Manage attendance and shifts'),
-- Expenses
('expenses',        'view',   'expenses.view',          'View expense records'),
('expenses',        'create', 'expenses.create',        'Create expense entries'),
('expenses',        'approve','expenses.approve',       'Approve expenses'),
-- Settings
('settings',        'view',   'settings.view',          'View system settings'),
('settings',        'edit',   'settings.edit',          'Edit system settings'),
-- Audit
('audit',           'view',   'audit.view',             'View audit logs'),
-- Backup
('backup',          'manage', 'backup.manage',          'Create and restore backups')
ON DUPLICATE KEY UPDATE `description`=VALUES(`description`);

-- ----------------------------------------------------------------------------
-- ROLE → PERMISSION ASSIGNMENTS
-- ----------------------------------------------------------------------------

-- Admin (1): ALL permissions
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 1, `id` FROM `permissions`
ON DUPLICATE KEY UPDATE `granted_at`=`granted_at`;

-- Manager (2): most permissions except system-admin level
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 2, `id` FROM `permissions`
WHERE `slug` NOT IN (
  'restaurants.edit','roles.edit','settings.edit','backup.manage','audit.view',
  'users.delete','billing.void','orders.void','payments.refund'
)
ON DUPLICATE KEY UPDATE `granted_at`=`granted_at`;

-- Cashier (3)
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 3, `id` FROM `permissions`
WHERE `slug` IN (
  'dashboard.view','pos.access','orders.view','orders.create','orders.edit',
  'orders.cancel','billing.view','billing.create','billing.discount',
  'payments.view','payments.process','customers.view','customers.create',
  'reservations.view','reservations.create','kot.view'
)
ON DUPLICATE KEY UPDATE `granted_at`=`granted_at`;

-- Waiter (4)
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 4, `id` FROM `permissions`
WHERE `slug` IN (
  'dashboard.view','orders.view','orders.create','orders.edit','orders.cancel',
  'floors.view','reservations.view','reservations.create','customers.view',
  'customers.create','kot.view','menu_items.view','menu_categories.view'
)
ON DUPLICATE KEY UPDATE `granted_at`=`granted_at`;

-- Chef (5)
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 5, `id` FROM `permissions`
WHERE `slug` IN (
  'dashboard.view','kds.view','kot.view','kot.manage',
  'menu_items.view','menu_categories.view','inventory.view','waste.view','waste.create'
)
ON DUPLICATE KEY UPDATE `granted_at`=`granted_at`;

-- Inventory Staff (6)
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 6, `id` FROM `permissions`
WHERE `slug` IN (
  'dashboard.view','inventory.view','inventory.adjust','inventory.export',
  'purchases.view','purchases.create','grn.view','grn.create',
  'waste.view','waste.create','menu_items.view','reports.view'
)
ON DUPLICATE KEY UPDATE `granted_at`=`granted_at`;

-- ----------------------------------------------------------------------------
-- DEFAULT RESTAURANT
-- ----------------------------------------------------------------------------
INSERT INTO `restaurants` (
  `id`, `name`, `legal_name`, `currency_code`, `currency_symbol`, `timezone`
) VALUES (
  1, 'My Restaurant', 'My Restaurant Pvt. Ltd.', 'INR', '₹', 'Asia/Kolkata'
) ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- ----------------------------------------------------------------------------
-- DEFAULT BRANCH
-- ----------------------------------------------------------------------------
INSERT INTO `branches` (`id`, `restaurant_id`, `name`, `code`) VALUES
(1, 1, 'Main Branch', 'BR001')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- ----------------------------------------------------------------------------
-- DEFAULT ADMIN USER  (password: Admin@123)
-- bcrypt hash for "Admin@123"
-- ----------------------------------------------------------------------------
INSERT INTO `users` (
  `id`, `restaurant_id`, `branch_id`, `role_id`,
  `first_name`, `last_name`, `email`, `password_hash`, `is_active`
) VALUES (
  1, 1, NULL, 1,
  'System', 'Admin', 'admin@rms.local',
  '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
  1
) ON DUPLICATE KEY UPDATE `email`=VALUES(`email`);

-- ----------------------------------------------------------------------------
-- DEFAULT TAX RATES
-- ----------------------------------------------------------------------------
INSERT INTO `tax_rates` (`restaurant_id`, `branch_id`, `name`, `rate`, `type`, `applies_to`) VALUES
(1, NULL, 'GST 5%',   5.00,  'exclusive', 'food'),
(1, NULL, 'GST 12%',  12.00, 'exclusive', 'food'),
(1, NULL, 'GST 18%',  18.00, 'exclusive', 'beverage'),
(1, NULL, 'Service Charge 5%', 5.00, 'exclusive', 'service')
ON DUPLICATE KEY UPDATE `rate`=VALUES(`rate`);

-- ----------------------------------------------------------------------------
-- DEFAULT PAYMENT METHODS
-- ----------------------------------------------------------------------------
INSERT INTO `payment_methods` (`restaurant_id`, `name`, `slug`, `sort_order`) VALUES
(1, 'Cash',          'cash',   1),
(1, 'Credit Card',   'card',   2),
(1, 'Debit Card',    'debit',  3),
(1, 'UPI',           'upi',    4),
(1, 'Digital Wallet','wallet', 5)
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- ----------------------------------------------------------------------------
-- DEFAULT SETTINGS
-- ----------------------------------------------------------------------------
INSERT INTO `settings` (`restaurant_id`, `branch_id`, `group`, `key`, `value`, `value_type`, `label`) VALUES
(1, NULL, 'general',  'restaurant_name',      'My Restaurant',   'string',  'Restaurant Name'),
(1, NULL, 'general',  'currency_code',        'INR',             'string',  'Currency Code'),
(1, NULL, 'general',  'currency_symbol',      '₹',               'string',  'Currency Symbol'),
(1, NULL, 'billing',  'service_charge_pct',   '5.00',            'decimal', 'Service Charge %'),
(1, NULL, 'billing',  'default_tax_inclusive','0',               'boolean', 'Prices Include Tax'),
(1, NULL, 'billing',  'bill_prefix',          'INV',             'string',  'Invoice Prefix'),
(1, NULL, 'billing',  'print_footer_note',    'Thank you for dining with us!','string','Receipt Footer'),
(1, NULL, 'order',    'kot_auto_print',        '1',              'boolean', 'Auto-print KOT'),
(1, NULL, 'order',    'delivery_charge_flat',  '40.00',          'decimal', 'Default Delivery Charge'),
(1, NULL, 'notification','low_stock_threshold','10',             'integer', 'Low Stock Alert Threshold'),
(1, NULL, 'security', 'max_login_attempts',   '5',               'integer', 'Max Failed Login Attempts'),
(1, NULL, 'security', 'lockout_minutes',      '15',              'integer', 'Account Lockout Duration (min)'),
(1, NULL, 'security', 'session_timeout_min',  '120',             'integer', 'Session Timeout (min)')
ON DUPLICATE KEY UPDATE `value`=VALUES(`value`);

-- MySQL dump 10.13  Distrib 8.0.46, for Win64 (x86_64)
--
-- Host: localhost    Database: rms_db
-- ------------------------------------------------------
-- Server version	8.0.46

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` int unsigned DEFAULT NULL,
  `branch_id` int unsigned DEFAULT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `module` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `record_type` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `record_id` bigint unsigned DEFAULT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_audit_restaurant` (`restaurant_id`),
  KEY `idx_audit_branch` (`branch_id`),
  KEY `idx_audit_user` (`user_id`),
  KEY `idx_audit_module` (`module`),
  KEY `idx_audit_action` (`action`),
  KEY `idx_audit_record` (`record_type`,`record_id`),
  KEY `idx_audit_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=622 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES (1,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','','2026-10-01 17:46:41'),(2,1,NULL,1,'branches','create','branches',2,NULL,'{\"city\": \"New Delhi\", \"code\": \"CP-01\", \"name\": \"Connaught Place Flagship\", \"email\": \"cp@rms.local\", \"phone\": \"+91 11 2345 6789\", \"state\": \"Delhi\", \"address\": \"Block B, Inner Circle, Connaught Place\", \"postal_code\": \"110001\", \"restaurant_id\": 1}',NULL,'::1','','2026-10-01 17:51:02'),(3,1,NULL,1,'branches','create','branches',3,NULL,'{\"city\": \"New Delhi\", \"code\": \"CP-01\", \"name\": \"Connaught Place Flagship\", \"email\": \"cp@rms.local\", \"phone\": \"+91 11 2345 6789\", \"state\": \"Delhi\", \"address\": \"Block B, Inner Circle, Connaught Place\", \"postal_code\": \"110001\", \"restaurant_id\": 1}',NULL,'::1','','2026-10-01 17:51:36'),(4,1,NULL,1,'users','create','users',2,NULL,'{\"email\": \"manager.cp@rms.local\", \"phone\": \"+91 9876543210\", \"role_id\": 2, \"password\": \"Manager@123\", \"branch_id\": \"1\", \"is_active\": 1, \"last_name\": \"Sharma\", \"first_name\": \"Rahul\", \"employee_id\": \"EMP-101\", \"joining_date\": \"2026-10-01\", \"restaurant_id\": 1}','New user created: manager.cp@rms.local','::1','','2026-10-01 17:51:37'),(5,NULL,NULL,NULL,'auth','login','users',2,NULL,NULL,'Successful login from ::1','::1','','2026-10-01 17:51:45'),(6,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-01 17:53:56'),(7,1,NULL,1,'auth','logout','users',1,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-01 17:58:37'),(8,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','','2026-10-01 18:08:30'),(9,1,NULL,1,'pos','create_order','orders',1,NULL,'{\"type\": \"dine_in\", \"total\": 1659, \"table_id\": 3, \"order_number\": \"ORD-20261001-0001\"}','Order placed: ORD-20261001-0001 (KOT: KOT-180831-001)','::1','','2026-10-01 18:08:31'),(10,1,NULL,1,'orders','update_status','orders',1,'{\"old_status\": \"confirmed\"}','{\"new_status\": \"preparing\"}','Order ORD-20261001-0001 status changed to preparing','::1','','2026-10-01 18:09:15'),(11,1,NULL,1,'orders','update_status','orders',1,'{\"old_status\": \"preparing\"}','{\"new_status\": \"preparing\"}','Order ORD-20261001-0001 status changed to preparing','::1','','2026-10-01 18:09:29'),(12,1,NULL,1,'pos','settle_order','orders',1,NULL,'{\"amount\": \"1659.00\", \"order_number\": \"ORD-20261001-0001\"}','Order ORD-20261001-0001 settled & completed.','::1','','2026-10-01 18:09:30'),(13,1,NULL,1,'orders','update_status','orders',1,'{\"old_status\": \"completed\"}','{\"new_status\": \"preparing\"}','Order ORD-20261001-0001 status changed to preparing','::1','','2026-10-01 18:09:58'),(14,1,NULL,1,'orders','update_status','orders',1,'{\"old_status\": \"preparing\"}','{\"new_status\": \"ready\"}','Order ORD-20261001-0001 status changed to ready','::1','','2026-10-01 18:09:58'),(15,1,NULL,1,'pos','settle_order','orders',1,NULL,'{\"amount\": \"1659.00\", \"order_number\": \"ORD-20261001-0001\"}','Order ORD-20261001-0001 settled & completed.','::1','','2026-10-01 18:09:59'),(16,1,NULL,1,'floors','table_status_change','restaurant_tables',3,'{\"old_status\": \"dirty\"}','{\"new_status\": \"available\"}','Table T-03 set to available','::1','','2026-10-01 18:09:59'),(17,NULL,NULL,NULL,'auth','login_failed','users',1,NULL,NULL,'Failed login attempt from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-01 18:11:17'),(18,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-01 18:11:25'),(19,1,NULL,1,'auth','logout','users',1,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-01 18:12:23'),(20,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-01 18:12:50'),(21,1,NULL,1,'kds','start_cooking','kots',1,NULL,'{\"time\": \"2026-10-01 18:19:28\", \"kot_number\": \"KOT-180831-001\"}','Cooking started for KOT-180831-001','::1','','2026-10-01 18:19:28'),(22,1,NULL,1,'kds','bump_ticket','kots',1,NULL,'{\"time\": \"2026-10-01 18:19:29\", \"kot_number\": \"KOT-180831-001\"}','Ticket KOT-180831-001 bumped (marked ready)','::1','','2026-10-01 18:19:29'),(23,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 11:27:03'),(24,1,NULL,1,'auth','logout','users',1,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 11:27:29'),(25,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 11:27:46'),(26,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','','2026-10-03 11:36:07'),(27,1,1,1,'backup','export_sql',NULL,NULL,NULL,NULL,'Generated database backup: rms_db_backup_2026_10_03_113609.sql','::1','','2026-10-03 11:36:09'),(28,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','','2026-10-03 11:37:54'),(29,1,1,1,'backup','export_sql',NULL,NULL,NULL,NULL,'Generated database backup: rms_db_backup_2026_10_03_113756.sql','::1','','2026-10-03 11:37:56'),(30,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','','2026-10-03 11:38:58'),(31,1,1,1,'backup','export_sql',NULL,NULL,NULL,NULL,'Generated database backup: rms_db_backup_2026_10_03_113900.sql','::1','','2026-10-03 11:39:00'),(32,1,1,1,'analytics','export_csv',NULL,NULL,NULL,NULL,'Exported sales ledger CSV: rms_sales_export_2026_10_03.csv','::1','','2026-10-03 11:39:00'),(33,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','','2026-10-03 11:39:37'),(34,1,1,1,'backup','export_sql',NULL,NULL,NULL,NULL,'Generated database backup: rms_db_backup_2026_10_03_113938.sql','::1','','2026-10-03 11:39:38'),(35,1,1,1,'analytics','export_csv',NULL,NULL,NULL,NULL,'Exported sales ledger CSV: rms_sales_export_2026_10_03.csv','::1','','2026-10-03 11:39:39'),(36,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','','2026-10-03 11:40:06'),(37,1,1,1,'backup','export_sql',NULL,NULL,NULL,NULL,'Generated database backup: rms_db_backup_2026_10_03_114008.sql','::1','','2026-10-03 11:40:08'),(38,1,1,1,'analytics','export_csv',NULL,NULL,NULL,NULL,'Exported sales ledger CSV: rms_sales_export_2026_10_03.csv','::1','','2026-10-03 11:40:08'),(39,1,1,1,'inventory','create_item',NULL,NULL,NULL,NULL,'Added raw material item: Sweet Whipping Cream (35%) (SKU: RAW-CRM-99)','::1','','2026-10-03 11:40:31'),(40,1,1,1,'inventory','adjust_stock',NULL,NULL,NULL,NULL,'Stock adjustment (in 5) for Item #9: Received bonus tasting sample carton','::1','','2026-10-03 11:40:32'),(41,1,1,1,'procurement','create_po',NULL,NULL,NULL,NULL,'Generated Purchase Order #PO-2026-002 for $90','::1','','2026-10-03 11:40:32'),(42,1,1,1,'procurement','receive_po',NULL,NULL,NULL,NULL,'Received PO #2 and credited stock levels with verified GRN','::1','','2026-10-03 11:40:32'),(43,1,1,1,'inventory','log_waste',NULL,NULL,NULL,NULL,'Logged 2 units waste (damaged) on Item #9','::1','','2026-10-03 11:40:33'),(44,1,1,1,'crm','create_customer',NULL,NULL,NULL,NULL,'Registered new customer Victoria Sterling (+1 555-0433)','::1','','2026-10-03 11:40:33'),(45,1,1,1,'loyalty','adjust_points',NULL,NULL,NULL,NULL,'Awarded +50 points for Customer #4: Anniversary celebration bonus dining visit','::1','','2026-10-03 11:40:34'),(46,1,NULL,1,'settings','updated','settings',NULL,NULL,'{\"bill_prefix\": \"INV\", \"currency_code\": \"INR\", \"kot_auto_print\": \"1\", \"currency_symbol\": \"₹\", \"lockout_minutes\": \"15\", \"restaurant_name\": \"My Restaurant\", \"print_footer_note\": \"Thank you for dining with us!\", \"max_login_attempts\": \"5\", \"service_charge_pct\": \"5\", \"low_stock_threshold\": \"10\", \"session_timeout_min\": \"120\", \"delivery_charge_flat\": \"40\"}',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 11:52:51'),(47,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','','2026-10-03 11:53:32'),(48,1,1,1,'orders','create_order','orders',2,NULL,NULL,'Order #ORD-20261003-0001 placed (dine_in) for $27.5 (KOT: KOT-115333-001)','::1','','2026-10-03 11:53:33'),(49,1,1,1,'billing','settle_order','orders',2,NULL,NULL,'Settled Order #ORD-20261003-0001 for ₹27.5 across 2 tender(s)','::1','','2026-10-03 11:53:33'),(50,1,NULL,1,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 11:56:40'),(51,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','','2026-10-03 12:01:04'),(52,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','','2026-10-03 12:07:00'),(53,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','','2026-10-03 12:11:08'),(54,1,1,1,'orders','create_order','orders',3,NULL,NULL,'Order #ORD-20261003-0002 placed (dine_in) for $539 (KOT: KOT-122003-002)','::1','','2026-10-03 12:20:03'),(55,1,1,1,'billing','settle_order','orders',3,NULL,NULL,'Settled Order #ORD-20261003-0002 for ₹539 across 1 tender(s)','::1','','2026-10-03 12:20:03'),(56,1,NULL,1,'auth','logout','users',1,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 12:20:38'),(57,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 12:20:48'),(58,1,1,1,'orders','create_order','orders',4,NULL,NULL,'Order #ORD-20261003-0003 placed (dine_in) for $577.5 (KOT: KOT-122130-003)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 12:21:30'),(59,1,1,1,'orders','create_order','orders',4,NULL,NULL,'Order #ORD-20261003-0003 placed (dine_in) for $577.5 (KOT: KOT-122135-004)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 12:21:35'),(60,1,1,1,'orders','create_order','orders',4,NULL,NULL,'Order #ORD-20261003-0003 placed (dine_in) for $577.5 (KOT: KOT-122144-005)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 12:21:44'),(61,1,1,1,'orders','create_order','orders',4,NULL,NULL,'Order #ORD-20261003-0003 placed (dine_in) for $418 (KOT: KOT-122216-006)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 12:22:16'),(62,NULL,NULL,NULL,'auth','login_failed','users',1,NULL,NULL,'Failed login attempt from ::1','::1','RMS-Integration-Test/1.0','2026-10-03 12:46:47'),(63,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','','2026-10-03 12:47:19'),(64,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','','2026-10-03 12:50:08'),(65,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','','2026-10-03 12:51:33'),(66,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','','2026-10-03 12:52:12'),(67,1,NULL,1,'kds','start_cooking','kots',2,NULL,'{\"time\": \"2026-10-03 13:05:24\", \"kot_number\": \"KOT-115333-001\"}','Cooking started for KOT-115333-001','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 13:05:24'),(68,1,NULL,1,'kds','start_cooking','kots',2,NULL,'{\"time\": \"2026-10-03 13:05:46\", \"kot_number\": \"KOT-115333-001\"}','Cooking started for KOT-115333-001','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 13:05:46'),(69,1,NULL,1,'floors','table_status_change','restaurant_tables',3,'{\"old_status\": \"dirty\"}','{\"new_status\": \"available\"}','Table T-03 set to available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 13:06:00'),(70,1,NULL,1,'floors','table_status_change','restaurant_tables',3,'{\"old_status\": \"available\"}','{\"new_status\": \"available\"}','Table T-03 set to available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 13:06:04'),(71,1,NULL,1,'billing','open_shift','cash_registers',1,NULL,'{\"opening_float\": 2000}','Cash register shift opened with float ₹2000','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 13:06:25'),(72,1,NULL,1,'orders','update_status','orders',4,'{\"old_status\": \"confirmed\"}','{\"new_status\": \"preparing\"}','Order ORD-20261003-0003 status changed to preparing','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 13:06:51'),(73,1,1,1,'orders','create_order','orders',5,NULL,NULL,'Order #ORD-20261003-0004 placed (dine_in) for $577.5 (KOT: KOT-130755-007)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 13:07:55'),(74,1,NULL,1,'kds','start_cooking','kots',8,NULL,'{\"time\": \"2026-10-03 13:08:06\", \"kot_number\": \"KOT-130755-007\"}','Cooking started for KOT-130755-007','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 13:08:06'),(75,1,NULL,1,'kds','bump_ticket','kots',2,NULL,'{\"time\": \"2026-10-03 13:08:12\", \"kot_number\": \"KOT-115333-001\"}','Ticket KOT-115333-001 bumped (marked ready)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 13:08:12'),(76,1,NULL,1,'kds','bump_ticket','kots',2,NULL,'{\"time\": \"2026-10-03 13:08:31\", \"kot_number\": \"KOT-115333-001\"}','Ticket KOT-115333-001 bumped (marked ready)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 13:08:31'),(77,1,NULL,1,'kds','bump_ticket','kots',8,NULL,'{\"time\": \"2026-10-03 13:08:53\", \"kot_number\": \"KOT-130755-007\"}','Ticket KOT-130755-007 bumped (marked ready)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 13:08:53'),(78,1,NULL,1,'orders','update_status','orders',5,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261003-0004 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 13:10:05'),(79,1,NULL,1,'orders','update_status','orders',4,'{\"old_status\": \"preparing\"}','{\"new_status\": \"ready\"}','Order ORD-20261003-0003 status changed to ready','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 13:10:12'),(80,1,NULL,1,'orders','update_status','orders',5,'{\"old_status\": \"served\"}','{\"new_status\": \"completed\"}','Order ORD-20261003-0004 status changed to completed','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 13:10:48'),(81,1,NULL,1,'orders','update_status','orders',4,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261003-0003 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 13:11:02'),(82,1,NULL,1,'auth','logout','users',1,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 13:12:06'),(83,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','','2026-10-03 13:14:43'),(84,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','','2026-10-03 13:16:24'),(85,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','','2026-10-03 13:16:44'),(86,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 13:17:26'),(87,1,NULL,1,'orders','update_status','orders',4,'{\"old_status\": \"served\"}','{\"new_status\": \"completed\"}','Order ORD-20261003-0003 status changed to completed','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 13:17:43'),(88,1,NULL,1,'users','create','users',3,NULL,'{\"email\": \"sudesh@gmail.com\", \"phone\": \"1478523698\", \"role_id\": 3, \"password\": \"12345678\", \"branch_id\": \"1\", \"is_active\": 1, \"last_name\": \"kumar\", \"first_name\": \"sudesh\", \"employee_id\": \"EMP-01\", \"joining_date\": \"2026-10-15\", \"restaurant_id\": 1}','New user created: sudesh@gmail.com','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 13:22:31'),(89,1,NULL,1,'auth','logout','users',1,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 13:22:45'),(90,NULL,NULL,NULL,'auth','login','users',3,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 13:22:52'),(91,1,1,3,'auth','logout','users',3,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 13:23:47'),(92,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 13:23:58'),(93,1,1,1,'analytics','export_csv',NULL,NULL,NULL,NULL,'Exported sales ledger CSV: rms_sales_export_2026_10_03.csv','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 13:24:54'),(94,1,1,1,'procurement','receive_po',NULL,NULL,NULL,NULL,'Received PO #1 and credited stock levels with verified GRN','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 13:25:40'),(95,1,NULL,1,'users','create','users',4,NULL,'{\"email\": \"subh@gmail.com\", \"phone\": \"1236547896\", \"role_id\": 4, \"password\": \"123456789\", \"branch_id\": \"2\", \"is_active\": 1, \"last_name\": \"singh\", \"first_name\": \"subh\", \"employee_id\": \"EMP-03\", \"joining_date\": \"2026-10-20\", \"restaurant_id\": 1}','New user created: subh@gmail.com','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 14:09:08'),(96,1,NULL,1,'auth','logout','users',1,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 14:09:25'),(97,NULL,NULL,NULL,'auth','login','users',4,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 14:09:34'),(98,1,2,4,'auth','logout','users',4,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 14:10:43'),(99,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 14:10:54'),(100,1,NULL,1,'roles','permissions_updated','roles',4,NULL,'[]',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 14:12:32'),(101,1,NULL,1,'roles','permissions_updated','roles',4,NULL,'[]',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 14:12:36'),(102,1,NULL,1,'auth','logout','users',1,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 14:12:46'),(103,NULL,NULL,NULL,'auth','login','users',4,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 14:13:01'),(104,1,2,4,'auth','logout','users',4,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 14:13:12'),(105,NULL,NULL,NULL,'auth','login','users',4,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 14:13:29'),(106,NULL,NULL,NULL,'auth','login','users',4,NULL,NULL,'Successful login from ::1','::1','','2026-10-03 14:18:13'),(107,1,2,4,'auth','logout','users',4,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 14:21:23'),(108,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 14:21:33'),(109,1,NULL,1,'roles','permissions_updated','roles',4,NULL,'[]',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 14:24:08'),(110,1,NULL,1,'auth','logout','users',1,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 14:24:13'),(111,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 14:24:21'),(112,1,NULL,1,'auth','logout','users',1,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 14:24:27'),(113,NULL,NULL,NULL,'auth','login','users',4,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 14:24:40'),(114,1,2,4,'auth','logout','users',4,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 14:26:42'),(115,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 14:26:53'),(116,1,1,1,'orders','create_order','orders',6,NULL,NULL,'Order #ORD-20261003-0005 placed (delivery) for $771.75 (KOT: KOT-142803-008)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 14:28:03'),(117,1,NULL,1,'kds','start_cooking','kots',7,NULL,'{\"time\": \"2026-10-03 14:28:34\", \"kot_number\": \"KOT-122216-006\"}','Cooking started for KOT-122216-006','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 14:28:34'),(118,1,NULL,1,'kds','start_cooking','kots',5,NULL,'{\"time\": \"2026-10-03 14:28:40\", \"kot_number\": \"KOT-122135-004\"}','Cooking started for KOT-122135-004','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 14:28:40'),(119,1,NULL,1,'users','user_deactivated','users',3,'{\"is_active\": \"1\"}','{\"is_active\": 0}',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 14:29:09'),(120,1,NULL,1,'users','user_activated','users',3,'{\"is_active\": \"0\"}','{\"is_active\": 1}',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 14:29:16'),(121,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','','2026-10-03 14:37:47'),(122,1,NULL,1,'roles','permissions_updated','roles',4,NULL,'[61, 37, 36, 84, 55, 54, 1, 22, 34, 33, 14, 18, 31, 29, 30, 28, 43, 25, 26, 24]',NULL,'::1','','2026-10-03 14:37:47'),(123,NULL,NULL,NULL,'auth','login','users',4,NULL,NULL,'Successful login from ::1','::1','','2026-10-03 14:37:48'),(124,1,NULL,1,'roles','permissions_updated','roles',4,NULL,'[61, 37, 36, 84, 55, 54, 1, 22, 34, 33, 14, 18, 31, 29, 30, 28, 43, 25, 26, 24]',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 14:40:41'),(125,1,NULL,1,'auth','logout','users',1,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 14:40:46'),(126,NULL,NULL,NULL,'auth','login_failed','users',1,NULL,NULL,'Failed login attempt from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 14:41:01'),(127,NULL,NULL,NULL,'auth','login_failed','users',1,NULL,NULL,'Failed login attempt from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 14:41:06'),(128,NULL,NULL,NULL,'auth','login','users',4,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 14:41:17'),(129,1,2,4,'auth','logout','users',4,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 14:42:45'),(130,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 14:43:00'),(131,1,1,1,'inventory','create_item',NULL,NULL,NULL,NULL,'Added raw material item: frozen chicken (SKU: RAW-FROZ-91)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 14:46:44'),(132,1,1,1,'inventory','adjust_stock',NULL,NULL,NULL,NULL,'Stock adjustment (out 5) for Item #10: used','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 14:49:02'),(133,1,1,1,'backup','export_sql',NULL,NULL,NULL,NULL,'Generated database backup: rms_db_backup_2026_10_03_145135.sql','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 14:51:35'),(134,1,1,1,'orders','create_order','orders',7,NULL,NULL,'Order #ORD-20261003-0006 placed (dine_in) for $577.5 (KOT: KOT-145325-009)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 14:53:25'),(135,1,NULL,1,'orders','update_status','orders',7,'{\"old_status\": \"confirmed\"}','{\"new_status\": \"preparing\"}','Order ORD-20261003-0006 status changed to preparing','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 14:53:39'),(136,1,NULL,1,'orders','update_status','orders',6,'{\"old_status\": \"confirmed\"}','{\"new_status\": \"preparing\"}','Order ORD-20261003-0005 status changed to preparing','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 14:54:38'),(137,1,NULL,1,'settings','updated','settings',NULL,NULL,'{\"bill_prefix\": \"INV\", \"currency_code\": \"INR\", \"kot_auto_print\": \"1\", \"currency_symbol\": \"₹\", \"lockout_minutes\": \"15\", \"restaurant_name\": \"Kichu Khon 🍱\", \"print_footer_note\": \"Thank you for dining with us!\", \"max_login_attempts\": \"5\", \"service_charge_pct\": \"5\", \"low_stock_threshold\": \"10\", \"session_timeout_min\": \"120\", \"delivery_charge_flat\": \"40\"}',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 15:04:13'),(138,1,NULL,1,'branches','update','branches',2,'{\"id\": \"2\", \"city\": \"New Delhi\", \"code\": \"CP-01\", \"name\": \"Connaught Place Flagship\", \"email\": \"cp@rms.local\", \"gstin\": null, \"phone\": \"+91 11 2345 6789\", \"state\": \"Delhi\", \"address\": \"Block B, Inner Circle, Connaught Place\", \"country\": \"India\", \"is_active\": \"1\", \"created_at\": \"2026-10-01 17:51:02\", \"updated_at\": \"2026-10-01 17:51:02\", \"postal_code\": \"110001\", \"closing_time\": \"23:00:00\", \"opening_time\": \"08:00:00\", \"fssai_license\": null, \"restaurant_id\": \"1\"}','{\"city\": \"New Delhi\", \"code\": \"CP-01\", \"name\": \"Connaught Place \", \"email\": \"cp@rms.local\", \"phone\": \"+91 11 2345 6789\", \"state\": \"Delhi\", \"address\": \"Block B, Inner Circle, Connaught Place\", \"postal_code\": \"110001\"}',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 15:05:37'),(139,1,NULL,1,'branches','update','branches',3,'{\"id\": \"3\", \"city\": \"New Delhi\", \"code\": \"CP-01\", \"name\": \"Connaught Place Flagship\", \"email\": \"cp@rms.local\", \"gstin\": null, \"phone\": \"+91 11 2345 6789\", \"state\": \"Delhi\", \"address\": \"Block B, Inner Circle, Connaught Place\", \"country\": \"India\", \"is_active\": \"1\", \"created_at\": \"2026-10-01 17:51:36\", \"updated_at\": \"2026-10-01 17:51:36\", \"postal_code\": \"110001\", \"closing_time\": \"23:00:00\", \"opening_time\": \"08:00:00\", \"fssai_license\": null, \"restaurant_id\": \"1\"}','{\"city\": \"Kolkata\", \"code\": \"CP-01\", \"name\": \"Sector 5 Kolkata\", \"email\": \"cp@rms.local\", \"phone\": \"+91 11 2345 6789\", \"state\": \"West Bengal\", \"address\": \"Block B, Inner Circle, Connaught Place\", \"postal_code\": \"110001\"}',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 15:06:08'),(140,1,NULL,1,'branches','update','branches',1,'{\"id\": \"1\", \"city\": null, \"code\": \"BR001\", \"name\": \"Main Branch\", \"email\": null, \"gstin\": null, \"phone\": null, \"state\": null, \"address\": null, \"country\": \"India\", \"is_active\": \"1\", \"created_at\": \"2026-10-01 17:44:43\", \"updated_at\": \"2026-10-01 17:44:43\", \"postal_code\": null, \"closing_time\": \"23:00:00\", \"opening_time\": \"08:00:00\", \"fssai_license\": null, \"restaurant_id\": \"1\"}','{\"city\": \"Howrah\", \"code\": \"BR001\", \"name\": \"Main Branch Howrah\", \"email\": \"MB@gmail.com\", \"phone\": \"+91980235944\", \"state\": \"West Bengal\", \"address\": \"Adamas university\", \"postal_code\": \"700126\"}',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 15:06:53'),(141,1,NULL,1,'branches','create','branches',4,NULL,'{\"city\": \"Ahmedabad\", \"code\": \"AH-001\", \"name\": \"KK Ahmd\", \"email\": \"KK@gmail.com\", \"phone\": \"+919800236587\", \"state\": \"Gujrat\", \"address\": \"St xyz lane\", \"postal_code\": \"700236\", \"restaurant_id\": 1}',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 15:08:36'),(142,1,NULL,1,'orders','update_status','orders',7,'{\"old_status\": \"preparing\"}','{\"new_status\": \"ready\"}','Order ORD-20261003-0006 status changed to ready','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 15:09:11'),(143,1,NULL,1,'orders','update_status','orders',6,'{\"old_status\": \"preparing\"}','{\"new_status\": \"ready\"}','Order ORD-20261003-0005 status changed to ready','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 15:09:14'),(144,1,NULL,1,'orders','update_status','orders',7,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261003-0006 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 15:09:20'),(145,1,NULL,1,'orders','update_status','orders',2,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261003-0001 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 15:09:27'),(146,1,NULL,1,'kds','start_cooking','kots',3,NULL,'{\"time\": \"2026-10-03 15:10:58\", \"kot_number\": \"KOT-122003-002\"}','Cooking started for KOT-122003-002','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 15:10:58'),(147,1,NULL,1,'kds','bump_ticket','kots',3,NULL,'{\"time\": \"2026-10-03 15:11:00\", \"kot_number\": \"KOT-122003-002\"}','Ticket KOT-122003-002 bumped (marked ready)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 15:11:00'),(148,1,NULL,1,'floors','table_status_change','restaurant_tables',1,'{\"old_status\": \"dirty\"}','{\"new_status\": \"available\"}','Table T-01 set to available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 15:11:21'),(149,1,NULL,1,'reservations','create','reservations',3,NULL,'{\"status\": \"confirmed\", \"table_id\": 11, \"branch_id\": 1, \"created_by\": 1, \"guest_count\": 2, \"customer_name\": \"Vikram paul\", \"restaurant_id\": 1, \"customer_email\": \"VP@gmail.com\", \"customer_phone\": \"+919800047856\", \"reservation_date\": \"2026-10-07\", \"reservation_time\": \"19:30\", \"special_requests\": \"Birthday \"}','Reservation for Vikram paul on 2026-10-07','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 15:13:35'),(150,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','','2026-10-03 15:20:08'),(151,1,NULL,1,'billing','close_shift','cash_registers',1,NULL,'{\"counted\": 850, \"expected\": 2850, \"discrepancy\": -2000}','Shift closed. Counted: ₹850, Expected: ₹2850, Diff: ₹-2000','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 15:21:41'),(152,1,NULL,1,'billing','open_shift','cash_registers',2,NULL,'{\"opening_float\": 2000}','Cash register shift opened with float ₹2000','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 15:21:58'),(153,1,NULL,1,'billing','close_shift','cash_registers',2,NULL,'{\"counted\": 2000, \"expected\": 2000, \"discrepancy\": 0}','Shift closed. Counted: ₹2000, Expected: ₹2000, Diff: ₹0','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 15:22:37'),(154,1,NULL,1,'billing','open_shift','cash_registers',3,NULL,'{\"opening_float\": 2000}','Cash register shift opened with float ₹2000','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 15:22:45'),(155,1,NULL,1,'billing','close_shift','cash_registers',3,NULL,'{\"counted\": 50, \"expected\": 2000, \"discrepancy\": -1950}','Shift closed. Counted: ₹50, Expected: ₹2000, Diff: ₹-1950','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 15:23:00'),(156,1,NULL,1,'billing','open_shift','cash_registers',4,NULL,'{\"opening_float\": 2000}','Cash register shift opened with float ₹2000','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-03 15:23:28'),(157,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 10:47:15'),(158,1,NULL,1,'floors','table_status_change','restaurant_tables',2,'{\"old_status\": \"dirty\"}','{\"new_status\": \"available\"}','Table T-02 set to available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 12:10:15'),(159,1,NULL,1,'roles','permissions_updated','roles',2,NULL,'[83, 82, 62, 61, 37, 38, 36, 5, 7, 6, 4, 85, 84, 73, 72, 55, 56, 54, 1, 75, 74, 65, 64, 77, 63, 23, 22, 51, 50, 45, 46, 44, 35, 34, 33, 81, 80, 15, 17, 16, 14, 19, 21, 20, 18, 31, 29, 30, 28, 41, 40, 79, 78, 43, 49, 48, 47, 58, 57, 25, 27, 26, 24, 2, 12, 66, 60, 59, 9, 10, 8, 53, 52]',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 12:10:43'),(160,1,NULL,1,'users','update','users',2,'{\"id\": \"2\", \"email\": \"manager.cp@rms.local\", \"phone\": \"+91 9876543210\", \"avatar\": null, \"gender\": null, \"address\": null, \"role_id\": \"2\", \"branch_id\": \"1\", \"is_active\": \"1\", \"last_name\": \"Sharma\", \"created_at\": \"2026-10-01 17:51:37\", \"first_name\": \"Rahul\", \"updated_at\": \"2026-10-01 17:51:45\", \"employee_id\": \"EMP-101\", \"joining_date\": \"2026-10-01\", \"locked_until\": null, \"date_of_birth\": null, \"last_login_at\": \"2026-10-01 17:51:45\", \"last_login_ip\": \"::1\", \"password_hash\": \"$2y$12$cHTC1KT3uUcEJEL4xufnNutB6CaKh2POZ1vssxzQJ6mahorodnWaG\", \"restaurant_id\": \"1\", \"deactivated_at\": null, \"deactivated_by\": null, \"remember_token\": null, \"emergency_phone\": null, \"email_verified_at\": null, \"emergency_contact\": null, \"failed_login_count\": \"0\", \"password_changed_at\": null}','{\"email\": \"manager.cp@rms.local\", \"phone\": \"+91 9876543210\", \"role_id\": 2, \"password\": \"12345678\", \"branch_id\": \"1\", \"last_name\": \"Sharma\", \"first_name\": \"Rahul\", \"employee_id\": \"EMP-101\"}',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 12:11:21'),(161,1,NULL,1,'auth','logout','users',1,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 12:11:32'),(162,NULL,NULL,NULL,'auth','login_failed','users',2,NULL,NULL,'Failed login attempt from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 12:11:39'),(163,NULL,NULL,NULL,'auth','login_failed','users',2,NULL,NULL,'Failed login attempt from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 12:11:44'),(164,NULL,NULL,NULL,'auth','login_failed','users',2,NULL,NULL,'Failed login attempt from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 12:11:52'),(165,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 12:12:21'),(166,1,NULL,1,'users','user_deactivated','users',4,'{\"is_active\": \"1\"}','{\"is_active\": 0}',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 12:12:45'),(167,1,NULL,1,'users','user_activated','users',4,'{\"is_active\": \"0\"}','{\"is_active\": 1}',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 12:12:52'),(168,1,NULL,1,'users','update','users',4,'{\"id\": \"4\", \"email\": \"subh@gmail.com\", \"phone\": \"1236547896\", \"avatar\": null, \"gender\": null, \"address\": null, \"role_id\": \"4\", \"branch_id\": \"2\", \"is_active\": \"1\", \"last_name\": \"singh\", \"created_at\": \"2026-10-03 14:09:08\", \"first_name\": \"subh\", \"updated_at\": \"2026-10-05 12:12:52\", \"employee_id\": \"EMP-03\", \"joining_date\": \"2026-10-20\", \"locked_until\": null, \"date_of_birth\": null, \"last_login_at\": \"2026-10-03 14:41:17\", \"last_login_ip\": \"::1\", \"password_hash\": \"$2y$12$Fst5W1Rah2yZt0HY5MpKEu9MAWJOyIAQoaJe6z5q/y0O6fqU.zbea\", \"restaurant_id\": \"1\", \"deactivated_at\": null, \"deactivated_by\": null, \"remember_token\": null, \"emergency_phone\": null, \"email_verified_at\": null, \"emergency_contact\": null, \"failed_login_count\": \"0\", \"password_changed_at\": null}','{\"email\": \"subh@gmail.com\", \"phone\": \"1236547896\", \"role_id\": 4, \"password\": \"147852369\", \"branch_id\": \"2\", \"last_name\": \"singh\", \"first_name\": \"subh\", \"employee_id\": \"EMP-03\"}',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 12:13:10'),(169,1,NULL,1,'auth','logout','users',1,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 12:13:15'),(170,NULL,NULL,NULL,'auth','login_failed','users',4,NULL,NULL,'Failed login attempt from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 12:13:23'),(171,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','','2026-10-05 12:22:14'),(172,1,NULL,1,'users','update','users',4,'{\"id\": \"4\", \"email\": \"subh@gmail.com\", \"phone\": \"1236547896\", \"avatar\": null, \"gender\": null, \"address\": null, \"role_id\": \"4\", \"branch_id\": \"2\", \"is_active\": \"1\", \"last_name\": \"singh\", \"created_at\": \"2026-10-03 14:09:08\", \"first_name\": \"subh\", \"updated_at\": \"2026-10-05 12:13:23\", \"employee_id\": \"EMP-03\", \"joining_date\": \"2026-10-20\", \"locked_until\": null, \"date_of_birth\": null, \"last_login_at\": \"2026-10-03 14:41:17\", \"last_login_ip\": \"::1\", \"password_hash\": \"$2y$12$Fst5W1Rah2yZt0HY5MpKEu9MAWJOyIAQoaJe6z5q/y0O6fqU.zbea\", \"restaurant_id\": \"1\", \"deactivated_at\": null, \"deactivated_by\": null, \"remember_token\": null, \"emergency_phone\": null, \"email_verified_at\": null, \"emergency_contact\": null, \"failed_login_count\": \"1\", \"password_changed_at\": null}','{\"email\": \"subh@gmail.com\", \"phone\": \"1236547896\", \"role_id\": 4, \"password\": \"147852369\", \"branch_id\": \"1\", \"last_name\": \"singh\", \"first_name\": \"subh\", \"employee_id\": \"EMP-03\"}',NULL,'::1','','2026-10-05 12:22:14'),(173,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','','2026-10-05 12:23:00'),(174,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','','2026-10-05 12:23:42'),(175,1,NULL,1,'users','update','users',4,'{\"id\": \"4\", \"email\": \"subh@gmail.com\", \"phone\": \"1236547896\", \"avatar\": null, \"gender\": null, \"address\": null, \"role_id\": \"4\", \"branch_id\": \"2\", \"is_active\": \"1\", \"last_name\": \"singh\", \"created_at\": \"2026-10-03 14:09:08\", \"first_name\": \"subh\", \"updated_at\": \"2026-10-05 12:13:23\", \"employee_id\": \"EMP-03\", \"joining_date\": \"2026-10-20\", \"locked_until\": null, \"date_of_birth\": null, \"last_login_at\": \"2026-10-03 14:41:17\", \"last_login_ip\": \"::1\", \"password_hash\": \"$2y$12$Fst5W1Rah2yZt0HY5MpKEu9MAWJOyIAQoaJe6z5q/y0O6fqU.zbea\", \"restaurant_id\": \"1\", \"deactivated_at\": null, \"deactivated_by\": null, \"remember_token\": null, \"emergency_phone\": null, \"email_verified_at\": null, \"emergency_contact\": null, \"failed_login_count\": \"1\", \"password_changed_at\": null}','{\"id\": 4, \"email\": \"subh@gmail.com\", \"phone\": \"1236547896\", \"role_id\": 4, \"password\": \"147852369\", \"branch_id\": \"1\", \"last_name\": \"singh\", \"first_name\": \"subh\", \"employee_id\": \"EMP-03\", \"restaurant_id\": 1}',NULL,'::1','','2026-10-05 12:23:43'),(176,NULL,NULL,NULL,'auth','login','users',4,NULL,NULL,'Successful login from ::1','::1','','2026-10-05 12:24:04'),(177,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','','2026-10-05 12:26:13'),(178,NULL,NULL,NULL,'auth','login','users',2,NULL,NULL,'Successful login from ::1','::1','','2026-10-05 12:26:14'),(179,NULL,NULL,NULL,'auth','login','users',3,NULL,NULL,'Successful login from ::1','::1','','2026-10-05 12:26:14'),(180,NULL,NULL,NULL,'auth','login','users',4,NULL,NULL,'Successful login from ::1','::1','','2026-10-05 12:26:15'),(181,NULL,NULL,NULL,'auth','login','users',5,NULL,NULL,'Successful login from ::1','::1','','2026-10-05 12:26:16'),(182,NULL,NULL,NULL,'auth','login','users',5,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 12:27:42'),(183,1,1,5,'kds','start_cooking','kots',4,NULL,'{\"time\": \"2026-10-05 12:27:55\", \"kot_number\": \"KOT-122130-003\"}','Cooking started for KOT-122130-003','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 12:27:55'),(184,1,1,5,'kds','start_cooking','kots',6,NULL,'{\"time\": \"2026-10-05 12:28:04\", \"kot_number\": \"KOT-122144-005\"}','Cooking started for KOT-122144-005','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 12:28:04'),(185,1,1,5,'kds','bump_ticket','kots',7,NULL,'{\"time\": \"2026-10-05 12:28:15\", \"kot_number\": \"KOT-122216-006\"}','Ticket KOT-122216-006 bumped (marked ready)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 12:28:15'),(186,1,1,5,'kds','start_cooking','kots',9,NULL,'{\"time\": \"2026-10-05 12:28:17\", \"kot_number\": \"KOT-142803-008\"}','Cooking started for KOT-142803-008','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 12:28:17'),(187,1,1,5,'kds','start_cooking','kots',10,NULL,'{\"time\": \"2026-10-05 12:28:23\", \"kot_number\": \"KOT-145325-009\"}','Cooking started for KOT-145325-009','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 12:28:23'),(188,1,1,5,'auth','logout','users',5,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 12:30:01'),(189,NULL,NULL,NULL,'auth','login','users',4,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 12:30:26'),(190,1,1,4,'orders','create_order','orders',8,NULL,NULL,'Order #ORD-20261005-0001 placed (dine_in) for $1012 (KOT: KOT-123051-001)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 12:30:51'),(191,1,1,4,'auth','logout','users',4,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 12:31:32'),(192,NULL,NULL,NULL,'auth','login','users',3,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 12:32:01'),(193,1,1,3,'orders','update_status','orders',4,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261003-0003 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 12:33:44'),(194,1,1,3,'orders','update_status','orders',4,'{\"old_status\": \"served\"}','{\"new_status\": \"completed\"}','Order ORD-20261003-0003 status changed to completed','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 12:33:49'),(195,1,1,3,'orders','update_status','orders',7,'{\"old_status\": \"preparing\"}','{\"new_status\": \"ready\"}','Order ORD-20261003-0006 status changed to ready','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 12:34:13'),(196,1,1,3,'orders','update_status','orders',2,'{\"old_status\": \"served\"}','{\"new_status\": \"completed\"}','Order ORD-20261003-0001 status changed to completed','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 12:34:18'),(197,1,1,3,'orders','update_status','orders',8,'{\"old_status\": \"confirmed\"}','{\"new_status\": \"preparing\"}','Order ORD-20261005-0001 status changed to preparing','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 12:34:57'),(198,1,1,3,'orders','update_status','orders',8,'{\"old_status\": \"preparing\"}','{\"new_status\": \"ready\"}','Order ORD-20261005-0001 status changed to ready','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 12:35:03'),(199,1,1,3,'orders','update_status','orders',8,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261005-0001 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 13:01:50'),(200,1,1,3,'orders','update_status','orders',8,'{\"old_status\": \"served\"}','{\"new_status\": \"completed\"}','Order ORD-20261005-0001 status changed to completed','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 13:01:54'),(201,1,1,3,'orders','update_status','orders',7,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261003-0006 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 13:01:58'),(202,1,1,3,'auth','logout','users',3,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 13:02:33'),(203,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 13:02:49'),(204,NULL,NULL,NULL,'auth','login','users',3,NULL,NULL,'Successful login from ::1','::1','','2026-10-05 13:08:06'),(205,1,1,3,'billing','settle_order','orders',9,NULL,NULL,'Settled Order #TEST-CASH-1791185957 for ₹350 across 1 tender(s)','::1','','2026-10-05 13:09:17'),(206,1,1,3,'billing','settle_order','orders',10,NULL,NULL,'Settled Order #TEST-UPI-1791185957 for ₹420 across 1 tender(s)','::1','','2026-10-05 13:09:17'),(207,NULL,NULL,NULL,'auth','login','users',3,NULL,NULL,'Successful login from ::1','::1','','2026-10-05 13:09:38'),(208,NULL,NULL,NULL,'auth','login','users',5,NULL,NULL,'Successful login from ::1','::1','','2026-10-05 13:09:40'),(209,NULL,NULL,NULL,'auth','login','users',4,NULL,NULL,'Successful login from ::1','::1','','2026-10-05 13:09:41'),(210,NULL,NULL,NULL,'auth','login','users',2,NULL,NULL,'Successful login from ::1','::1','','2026-10-05 13:09:43'),(211,NULL,NULL,NULL,'auth','login','users',3,NULL,NULL,'Successful login from ::1','::1','','2026-10-05 13:10:41'),(212,NULL,NULL,NULL,'auth','login','users',5,NULL,NULL,'Successful login from ::1','::1','','2026-10-05 13:10:43'),(213,NULL,NULL,NULL,'auth','login','users',4,NULL,NULL,'Successful login from ::1','::1','','2026-10-05 13:10:44'),(214,NULL,NULL,NULL,'auth','login','users',2,NULL,NULL,'Successful login from ::1','::1','','2026-10-05 13:10:45'),(215,1,NULL,1,'orders','update_status','orders',7,'{\"old_status\": \"served\"}','{\"new_status\": \"completed\"}','Order ORD-20261003-0006 status changed to completed','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 13:17:57'),(216,1,NULL,1,'floors','table_status_change','restaurant_tables',1,'{\"old_status\": \"dirty\"}','{\"new_status\": \"available\"}','Table T-01 set to available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 13:23:49'),(217,1,NULL,1,'floors','table_status_change','restaurant_tables',2,'{\"old_status\": \"dirty\"}','{\"new_status\": \"available\"}','Table T-02 set to available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 13:23:50'),(218,1,NULL,1,'floors','table_status_change','restaurant_tables',3,'{\"old_status\": \"dirty\"}','{\"new_status\": \"available\"}','Table T-03 set to available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 13:23:51'),(219,1,NULL,1,'floors','table_status_change','restaurant_tables',7,'{\"old_status\": \"dirty\"}','{\"new_status\": \"available\"}','Table R-01 set to available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 13:23:53'),(220,1,NULL,1,'orders','update_status','orders',3,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261003-0002 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 14:06:12'),(221,1,1,1,'orders','create_order','orders',11,NULL,NULL,'Order #ORD-20261005-0004 placed (dine_in) for $654.5 (KOT: KOT-140648-002)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 14:06:48'),(222,1,1,1,'billing','settle_order','orders',11,NULL,NULL,'Settled Order #ORD-20261005-0004 for ₹654.5 across 1 tender(s)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 14:06:48'),(223,1,NULL,1,'kds','start_cooking','kots',12,NULL,'{\"time\": \"2026-10-05 14:07:08\", \"kot_number\": \"KOT-140648-002\"}','Cooking started for KOT-140648-002','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 14:07:08'),(224,1,NULL,1,'kds','bump_ticket','kots',12,NULL,'{\"time\": \"2026-10-05 14:07:09\", \"kot_number\": \"KOT-140648-002\"}','Ticket KOT-140648-002 bumped (marked ready)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 14:07:09'),(225,1,NULL,1,'orders','update_status','orders',11,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261005-0004 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 14:07:32'),(226,1,NULL,1,'orders','update_status','orders',11,'{\"old_status\": \"served\"}','{\"new_status\": \"completed\"}','Order ORD-20261005-0004 status changed to completed','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 14:07:35'),(227,1,1,1,'crm','create_customer',NULL,NULL,NULL,NULL,'Registered new customer Subbham (+91980067389)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 14:11:55'),(228,1,1,1,'loyalty','adjust_points',NULL,NULL,NULL,NULL,'Awarded +50 points for Customer #5: good ambience','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 14:12:27'),(229,NULL,NULL,NULL,'auth','login','users',3,NULL,NULL,'Successful login from ::1','::1','','2026-10-05 14:35:12'),(230,1,1,3,'billing','settle_order','orders',12,NULL,NULL,'Settled Order #DISC-TEST-1791191113 for ₹800 across 1 tender(s)','::1','','2026-10-05 14:35:13'),(231,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','','2026-10-05 14:46:10'),(232,1,NULL,1,'users','create','users',6,NULL,'{\"email\": \"staff1791191770@rms.local\", \"phone\": null, \"role_id\": 3, \"password\": \"StaffPass@123\", \"branch_id\": null, \"is_active\": 1, \"last_name\": \"Staff\", \"first_name\": \"Test\", \"employee_id\": null, \"joining_date\": null, \"restaurant_id\": 1}','New user created: staff1791191770@rms.local','::1','','2026-10-05 14:46:10'),(233,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','','2026-10-05 14:47:32'),(234,1,NULL,1,'users','create','users',7,NULL,'{\"email\": \"staff1791191853@rms.local\", \"phone\": \"9876543210\", \"role_id\": 3, \"password\": \"StaffPass@123\", \"branch_id\": null, \"is_active\": 1, \"last_name\": \"Kumar\", \"first_name\": \"Anand\", \"employee_id\": null, \"joining_date\": null, \"restaurant_id\": 1}','New user created: staff1791191853@rms.local','::1','','2026-10-05 14:47:33'),(235,1,NULL,1,'users','create','users',8,NULL,'{\"email\": \"priyanshupaul.mng2003@gmail.com\", \"phone\": \"+919345671944\", \"role_id\": 4, \"password\": \"Rishav@123\", \"branch_id\": \"3\", \"is_active\": 1, \"last_name\": \"dalla\", \"first_name\": \"Rishav\", \"employee_id\": \"EMP-09\", \"joining_date\": \"2026-10-05\", \"restaurant_id\": 1}','New user created: priyanshupaul.mng2003@gmail.com','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 14:49:28'),(236,1,NULL,1,'auth','logout','users',1,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 14:49:55'),(237,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 14:50:23'),(238,1,NULL,1,'users','update','users',8,'{\"id\": \"8\", \"email\": \"priyanshupaul.mng2003@gmail.com\", \"phone\": \"+919345671944\", \"avatar\": null, \"gender\": null, \"address\": null, \"role_id\": \"4\", \"branch_id\": \"3\", \"is_active\": \"1\", \"last_name\": \"dalla\", \"created_at\": \"2026-10-05 14:49:28\", \"first_name\": \"Rishav\", \"updated_at\": \"2026-10-05 14:49:28\", \"employee_id\": \"EMP-09\", \"joining_date\": \"2026-10-05\", \"locked_until\": null, \"date_of_birth\": null, \"last_login_at\": null, \"last_login_ip\": null, \"password_hash\": \"$2y$12$GWPwx9acUi3s0NDc2kNjmOtj7m9XU61RVn8AzIrXMRmEf5XGfEPbW\", \"restaurant_id\": \"1\", \"deactivated_at\": null, \"deactivated_by\": null, \"remember_token\": null, \"emergency_phone\": null, \"email_verified_at\": null, \"emergency_contact\": null, \"failed_login_count\": \"0\", \"password_changed_at\": null}','{\"id\": 8, \"email\": \"rishav@gmail.com\", \"phone\": \"+919345671944\", \"role_id\": 4, \"branch_id\": \"3\", \"last_name\": \"dalla\", \"first_name\": \"Rishav\", \"employee_id\": \"EMP-09\", \"restaurant_id\": 1}',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 14:50:41'),(239,1,NULL,1,'auth','logout','users',1,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 14:50:47'),(240,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 14:51:38'),(241,1,NULL,1,'users','update','users',8,'{\"id\": \"8\", \"email\": \"rishav@gmail.com\", \"phone\": \"+919345671944\", \"avatar\": null, \"gender\": null, \"address\": null, \"role_id\": \"4\", \"branch_id\": \"3\", \"is_active\": \"1\", \"last_name\": \"dalla\", \"created_at\": \"2026-10-05 14:49:28\", \"first_name\": \"Rishav\", \"updated_at\": \"2026-10-05 14:50:41\", \"employee_id\": \"EMP-09\", \"joining_date\": \"2026-10-05\", \"locked_until\": null, \"date_of_birth\": null, \"last_login_at\": null, \"last_login_ip\": null, \"password_hash\": \"$2y$12$GWPwx9acUi3s0NDc2kNjmOtj7m9XU61RVn8AzIrXMRmEf5XGfEPbW\", \"restaurant_id\": \"1\", \"deactivated_at\": null, \"deactivated_by\": null, \"remember_token\": null, \"emergency_phone\": null, \"email_verified_at\": null, \"emergency_contact\": null, \"failed_login_count\": \"0\", \"password_changed_at\": null}','{\"id\": 8, \"email\": \"rishav@gmail.com\", \"phone\": \"+919345671944\", \"role_id\": 4, \"password\": \"Rishav@123\", \"branch_id\": \"3\", \"last_name\": \"dalla\", \"first_name\": \"Rishav\", \"employee_id\": \"EMP-09\", \"restaurant_id\": 1}',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 14:51:55'),(242,1,NULL,1,'auth','logout','users',1,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 14:51:59'),(243,NULL,NULL,NULL,'auth','login','users',8,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 14:52:16'),(244,1,3,8,'auth','logout','users',8,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 14:53:34'),(245,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 14:53:50'),(246,1,NULL,1,'performance','update','staff_performance_reviews',3,'{\"id\": \"3\", \"goals\": \"\", \"user_id\": \"5\", \"branch_id\": \"1\", \"strengths\": \"\", \"created_at\": \"2026-10-05 15:04:35\", \"updated_at\": \"2026-10-05 15:04:35\", \"review_date\": \"2026-10-05\", \"reviewer_id\": \"1\", \"overall_score\": \"2.25\", \"review_period\": \"October 2026 Appraisal\", \"rating_attendance\": \"3\", \"rating_hospitality\": \"2\", \"rating_punctuality\": \"2\", \"areas_for_improvement\": \"\", \"rating_order_accuracy\": \"2\"}','{\"goals\": \"\", \"user_id\": 5, \"branch_id\": 1, \"strengths\": \"\", \"review_date\": \"2026-10-05\", \"reviewer_id\": 1, \"overall_score\": 2.25, \"review_period\": \"October 2026 Appraisal\", \"rating_attendance\": 3, \"rating_hospitality\": 2, \"rating_punctuality\": 2, \"areas_for_improvement\": \"\", \"rating_order_accuracy\": 2}','Appraisal review saved for Vikram Chef (Score: 2.25/5.0)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 15:20:32'),(247,1,NULL,1,'users','update','users',4,'{\"id\": \"4\", \"email\": \"subh@gmail.com\", \"phone\": \"1236547896\", \"avatar\": null, \"gender\": null, \"address\": null, \"role_id\": \"4\", \"branch_id\": \"1\", \"is_active\": \"1\", \"last_name\": \"singh\", \"created_at\": \"2026-10-03 14:09:08\", \"first_name\": \"subh\", \"updated_at\": \"2026-10-05 13:10:44\", \"employee_id\": \"EMP-03\", \"joining_date\": \"2026-10-20\", \"locked_until\": null, \"date_of_birth\": null, \"last_login_at\": \"2026-10-05 13:10:44\", \"last_login_ip\": \"::1\", \"password_hash\": \"$2y$12$2l2Lt/RxiKIomPjWjRPYiO/moFbIRxsmXuBEYfEtW8DxEIlVAO/6a\", \"restaurant_id\": \"1\", \"deactivated_at\": null, \"deactivated_by\": null, \"remember_token\": null, \"emergency_phone\": null, \"email_verified_at\": null, \"emergency_contact\": null, \"failed_login_count\": \"0\", \"password_changed_at\": null}','{\"id\": 4, \"email\": \"subh@gmail.com\", \"phone\": \"1236547896\", \"role_id\": 4, \"password\": \"12345678\", \"branch_id\": \"1\", \"last_name\": \"singh\", \"first_name\": \"subh\", \"employee_id\": \"EMP-03\", \"restaurant_id\": 1}',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 15:33:48'),(248,1,NULL,1,'auth','logout','users',1,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 15:33:58'),(249,NULL,NULL,NULL,'auth','login','users',4,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 15:34:08'),(250,1,1,4,'crm','create_customer',NULL,NULL,NULL,NULL,'Registered new customer stephen petro (7412589632)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:09:19'),(251,1,1,4,'orders','create_order','orders',13,NULL,NULL,'Order #ORD-20261005-0006 placed (dine_in) for $407 (KOT: KOT-172148-003)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:21:48'),(252,1,1,4,'billing','settle_order','orders',13,NULL,NULL,'Settled Order #ORD-20261005-0006 for ₹329.3 across 1 tender(s)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:21:48'),(253,1,1,4,'auth','logout','users',4,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:22:18'),(254,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:22:43'),(255,1,NULL,1,'orders','update_status','orders',6,'{\"old_status\": \"preparing\"}','{\"new_status\": \"ready\"}','Order ORD-20261003-0005 status changed to ready','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:22:58'),(256,1,NULL,1,'kds','bump_ticket','kots',4,NULL,'{\"time\": \"2026-10-05 17:23:14\", \"kot_number\": \"KOT-122130-003\"}','Ticket KOT-122130-003 bumped (marked ready)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:23:14'),(257,1,NULL,1,'kds','bump_ticket','kots',5,NULL,'{\"time\": \"2026-10-05 17:23:16\", \"kot_number\": \"KOT-122135-004\"}','Ticket KOT-122135-004 bumped (marked ready)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:23:16'),(258,1,NULL,1,'kds','bump_ticket','kots',9,NULL,'{\"time\": \"2026-10-05 17:23:17\", \"kot_number\": \"KOT-142803-008\"}','Ticket KOT-142803-008 bumped (marked ready)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:23:17'),(259,1,NULL,1,'kds','bump_ticket','kots',10,NULL,'{\"time\": \"2026-10-05 17:23:18\", \"kot_number\": \"KOT-145325-009\"}','Ticket KOT-145325-009 bumped (marked ready)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:23:18'),(260,1,NULL,1,'kds','bump_ticket','kots',6,NULL,'{\"time\": \"2026-10-05 17:23:19\", \"kot_number\": \"KOT-122144-005\"}','Ticket KOT-122144-005 bumped (marked ready)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:23:19'),(261,1,NULL,1,'kds','start_cooking','kots',13,NULL,'{\"time\": \"2026-10-05 17:23:23\", \"kot_number\": \"KOT-172148-003\"}','Cooking started for KOT-172148-003','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:23:23'),(262,1,NULL,1,'kds','start_cooking','kots',11,NULL,'{\"time\": \"2026-10-05 17:23:28\", \"kot_number\": \"KOT-123051-001\"}','Cooking started for KOT-123051-001','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:23:28'),(263,1,NULL,1,'kds','bump_ticket','kots',13,NULL,'{\"time\": \"2026-10-05 17:23:29\", \"kot_number\": \"KOT-172148-003\"}','Ticket KOT-172148-003 bumped (marked ready)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:23:29'),(264,1,NULL,1,'kds','bump_ticket','kots',11,NULL,'{\"time\": \"2026-10-05 17:23:30\", \"kot_number\": \"KOT-123051-001\"}','Ticket KOT-123051-001 bumped (marked ready)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:23:30'),(265,1,NULL,1,'floors','table_status_change','restaurant_tables',1,'{\"old_status\": \"dirty\"}','{\"new_status\": \"available\"}','Table T-01 set to available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:23:35'),(266,1,NULL,1,'floors','table_status_change','restaurant_tables',2,'{\"old_status\": \"dirty\"}','{\"new_status\": \"available\"}','Table T-02 set to available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:23:36'),(267,1,NULL,1,'orders','update_status','orders',3,'{\"old_status\": \"served\"}','{\"new_status\": \"completed\"}','Order ORD-20261003-0002 status changed to completed','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:23:57'),(268,1,NULL,1,'orders','update_status','orders',8,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261005-0001 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:24:00'),(269,1,NULL,1,'orders','update_status','orders',6,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261003-0005 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:24:05'),(270,1,NULL,1,'orders','update_status','orders',6,'{\"old_status\": \"served\"}','{\"new_status\": \"completed\"}','Order ORD-20261003-0005 status changed to completed','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:24:18'),(271,1,1,1,'inventory','adjust_stock',NULL,NULL,NULL,NULL,'Stock adjustment (in 10) for Item #10: ready to use','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:24:43'),(272,1,1,1,'procurement','create_po',NULL,NULL,NULL,NULL,'Generated Purchase Order #PO-2026-003 for $100','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:25:14'),(273,1,1,1,'procurement','receive_po',NULL,NULL,NULL,NULL,'Received PO #3 and credited stock levels with verified GRN','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:25:19'),(274,1,1,1,'inventory','log_waste',NULL,NULL,NULL,NULL,'Logged 1 units waste (expired) on Item #6','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:25:55'),(275,1,1,1,'orders','create_order','orders',14,NULL,NULL,'Order #ORD-20261005-0007 placed (delivery) for $819 (KOT: KOT-172947-004)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:29:47'),(276,1,1,1,'billing','settle_order','orders',14,NULL,NULL,'Settled Order #ORD-20261005-0007 for ₹819 across 1 tender(s)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:29:47'),(277,1,NULL,1,'kds','start_cooking','kots',14,NULL,'{\"time\": \"2026-10-05 17:30:17\", \"kot_number\": \"KOT-172947-004\"}','Cooking started for KOT-172947-004','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:30:17'),(278,1,NULL,1,'kds','bump_ticket','kots',14,NULL,'{\"time\": \"2026-10-05 17:30:18\", \"kot_number\": \"KOT-172947-004\"}','Ticket KOT-172947-004 bumped (marked ready)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:30:18'),(279,1,NULL,1,'menu_items','toggle_availability','menu_items',7,'{\"is_available\": \"1\"}','{\"is_available\": 0}','Item Butter Chicken Dhabawala marked as Out of Stock (86ed)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:36:49'),(280,1,NULL,1,'menu_items','create','menu_items',19,NULL,'{\"code\": \"MC-01\", \"name\": \"Mushroom curry\", \"price\": 250, \"category_id\": 2}','Created dish Mushroom curry (MC-01)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:46:34'),(281,1,NULL,1,'menu_items','toggle_availability','menu_items',19,'{\"is_available\": \"1\"}','{\"is_available\": 0}','Item Mushroom curry marked as Out of Stock (86ed)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:46:56'),(282,1,NULL,1,'menu_items','toggle_availability','menu_items',19,'{\"is_available\": \"0\"}','{\"is_available\": 1}','Item Mushroom curry marked as Available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:47:02'),(283,1,NULL,1,'menu_items','toggle_availability','menu_items',19,'{\"is_available\": \"1\"}','{\"is_available\": 0}','Item Mushroom curry marked as Out of Stock (86ed)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:47:07'),(284,1,NULL,1,'menu_items','toggle_availability','menu_items',19,'{\"is_available\": \"0\"}','{\"is_available\": 1}','Item Mushroom curry marked as Available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:47:11'),(285,1,NULL,1,'menu_items','update','menu_items',19,'{\"id\": \"19\", \"code\": \"MC-01\", \"name\": \"Mushroom curry\", \"price\": \"250.00\", \"is_veg\": \"1\", \"branch_id\": \"1\", \"is_active\": \"1\", \"created_at\": \"2026-10-05 17:46:34\", \"sort_order\": \"0\", \"updated_at\": \"2026-10-05 17:47:11\", \"category_id\": \"2\", \"description\": null, \"tax_rate_id\": null, \"is_available\": \"1\", \"restaurant_id\": \"1\", \"preparation_time\": \"15\"}','{\"code\": \"MC-01\", \"name\": \"Mushroom curry\", \"price\": 250, \"is_veg\": 1, \"sort_order\": 0, \"category_id\": 2, \"description\": \"Rich creamy gravy cooked slow with desi masalas.\", \"tax_rate_id\": null, \"is_available\": 1, \"preparation_time\": 15}','Updated dish Mushroom curry','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:47:41'),(286,1,NULL,1,'menu_items','deactivate','menu_items',4,NULL,NULL,'Deactivated dish Tandoori Prawns Zaffrani','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:48:25'),(287,1,NULL,1,'auth','logout','users',1,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:49:52'),(288,NULL,NULL,NULL,'auth','login','users',2,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:50:47'),(289,1,1,2,'auth','logout','users',2,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:50:57'),(290,NULL,NULL,NULL,'auth','login_failed','users',4,NULL,NULL,'Failed login attempt from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:51:26'),(291,NULL,NULL,NULL,'auth','login_failed','users',4,NULL,NULL,'Failed login attempt from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:51:42'),(292,NULL,NULL,NULL,'auth','login','users',5,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:52:06'),(293,1,1,5,'auth','logout','users',5,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:52:56'),(294,NULL,NULL,NULL,'auth','login','users',2,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:53:17'),(295,1,1,2,'users','update','users',4,'{\"id\": \"4\", \"email\": \"subh@gmail.com\", \"phone\": \"1236547896\", \"avatar\": null, \"gender\": null, \"address\": null, \"role_id\": \"4\", \"branch_id\": \"1\", \"is_active\": \"1\", \"last_name\": \"singh\", \"created_at\": \"2026-10-03 14:09:08\", \"first_name\": \"subh\", \"updated_at\": \"2026-10-05 17:51:42\", \"employee_id\": \"EMP-03\", \"joining_date\": \"2026-10-20\", \"locked_until\": null, \"date_of_birth\": null, \"last_login_at\": \"2026-10-05 15:34:08\", \"last_login_ip\": \"::1\", \"password_hash\": \"$2y$12$E.dnd/5Q52UoIEdPz7ewXOD1xpsLBzwRhPy8FbVVlGWwQBjNvUdgi\", \"restaurant_id\": \"1\", \"deactivated_at\": null, \"deactivated_by\": null, \"remember_token\": null, \"emergency_phone\": null, \"email_verified_at\": null, \"emergency_contact\": null, \"failed_login_count\": \"2\", \"password_changed_at\": null}','{\"id\": 4, \"email\": \"subh@gmail.com\", \"phone\": \"1236547896\", \"role_id\": 4, \"password\": \"Waiter@123\", \"branch_id\": \"1\", \"last_name\": \"singh\", \"first_name\": \"subh\", \"employee_id\": \"EMP-03\", \"restaurant_id\": 1}',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:54:19'),(296,1,1,2,'auth','logout','users',2,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:54:26'),(297,NULL,NULL,NULL,'auth','login_failed','users',4,NULL,NULL,'Failed login attempt from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:54:45'),(298,NULL,NULL,NULL,'auth','login','users',4,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:54:54'),(299,1,1,4,'auth','logout','users',4,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:55:22'),(300,NULL,NULL,NULL,'auth','login','users',3,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 17:55:50'),(301,1,1,3,'billing','close_shift','cash_registers',4,NULL,'{\"counted\": 1000, \"expected\": 9876.55, \"discrepancy\": -8876.55}','Shift closed. Counted: ₹1000, Expected: ₹9876.55, Diff: ₹-8876.55','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 18:00:32'),(302,1,1,3,'billing','open_shift','cash_registers',5,NULL,'{\"opening_float\": 2000}','Cash register shift opened with float ₹2000','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 18:00:36'),(303,1,1,3,'orders','update_status','orders',8,'{\"old_status\": \"served\"}','{\"new_status\": \"completed\"}','Order ORD-20261005-0001 status changed to completed','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 18:15:38'),(304,1,1,3,'orders','update_status','orders',7,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261003-0006 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 18:15:41'),(305,1,1,3,'orders','update_status','orders',14,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261005-0007 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 18:15:45'),(306,1,1,3,'orders','update_status','orders',13,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261005-0006 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 18:15:48'),(307,1,1,3,'orders','update_status','orders',7,'{\"old_status\": \"served\"}','{\"new_status\": \"completed\"}','Order ORD-20261003-0006 status changed to completed','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-05 18:15:53'),(308,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 10:40:03'),(309,1,NULL,1,'auth','logout','users',1,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 10:42:52'),(310,NULL,NULL,NULL,'auth','login','users',2,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 10:43:22'),(311,1,1,2,'floors','table_status_change','restaurant_tables',1,'{\"old_status\": \"dirty\"}','{\"new_status\": \"available\"}','Table T-01 set to available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 10:47:46'),(312,1,1,2,'floors','table_status_change','restaurant_tables',1,'{\"old_status\": \"available\"}','{\"new_status\": \"available\"}','Table T-01 set to available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 10:47:47'),(313,1,1,2,'floors','table_status_change','restaurant_tables',1,'{\"old_status\": \"available\"}','{\"new_status\": \"available\"}','Table T-01 set to available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 10:47:48'),(314,1,1,2,'floors','table_status_change','restaurant_tables',7,'{\"old_status\": \"dirty\"}','{\"new_status\": \"available\"}','Table R-01 set to available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 10:47:50'),(315,1,1,2,'floors','table_status_change','restaurant_tables',11,'{\"old_status\": \"dirty\"}','{\"new_status\": \"available\"}','Table VIP-1 set to available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 10:47:53'),(316,1,1,2,'auth','logout','users',2,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 10:48:50'),(317,NULL,NULL,NULL,'auth','login','users',5,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 10:49:12'),(318,1,1,5,'auth','logout','users',5,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 10:54:26'),(319,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 10:54:38'),(320,1,NULL,1,'floors','table_status_change','restaurant_tables',6,'{\"old_status\": \"available\"}','{\"new_status\": \"reserved\"}','Table T-06 set to reserved','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 10:57:41'),(321,1,NULL,1,'floors','table_status_change','restaurant_tables',6,'{\"old_status\": \"reserved\"}','{\"new_status\": \"available\"}','Table T-06 set to available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 10:57:43'),(322,1,NULL,1,'billing','close_shift','cash_registers',5,NULL,'{\"counted\": 6000, \"expected\": 6737.799999999999, \"discrepancy\": -737.7999999999993}','Shift closed. Counted: ₹6000, Expected: ₹6737.8, Diff: ₹-737.8','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 11:10:45'),(323,1,NULL,1,'billing','open_shift','cash_registers',6,NULL,'{\"opening_float\": 1000}','Cash register shift opened with float ₹1000','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 11:11:01'),(324,1,1,1,'orders','create_order','orders',15,NULL,NULL,'Order #ORD-20261006-0001 placed (dine_in) for $396 (KOT: KOT-111305-001)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 11:13:05'),(325,1,NULL,1,'kds','start_cooking','kots',15,NULL,'{\"time\": \"2026-10-06 11:13:14\", \"kot_number\": \"KOT-111305-001\"}','Cooking started for KOT-111305-001','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 11:13:14'),(326,1,NULL,1,'kds','bump_ticket','kots',15,NULL,'{\"time\": \"2026-10-06 11:13:23\", \"kot_number\": \"KOT-111305-001\"}','Ticket KOT-111305-001 bumped (marked ready)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 11:13:23'),(327,1,NULL,1,'orders','update_status','orders',15,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261006-0001 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 11:13:48'),(328,1,NULL,1,'orders','update_status','orders',15,'{\"old_status\": \"served\"}','{\"new_status\": \"completed\"}','Order ORD-20261006-0001 status changed to completed','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 11:13:55'),(329,1,NULL,1,'billing','close_shift','cash_registers',6,NULL,'{\"counted\": 1596, \"expected\": 1596, \"discrepancy\": 0}','Shift closed. Counted: ₹1596, Expected: ₹1596, Diff: ₹0','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 11:14:28'),(330,1,NULL,1,'billing','open_shift','cash_registers',7,NULL,'{\"opening_float\": 1000}','Cash register shift opened with float ₹1000','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 11:15:08'),(331,1,NULL,1,'billing','close_shift','cash_registers',7,NULL,'{\"counted\": 1200, \"expected\": 1000, \"discrepancy\": 200}','Shift closed. Counted: ₹1200, Expected: ₹1000, Diff: ₹200','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 11:15:20'),(332,1,NULL,1,'billing','delete_shift_audit','cash_registers',1,'{\"id\": \"1\", \"notes\": null, \"status\": \"closed\", \"user_id\": \"1\", \"branch_id\": \"1\", \"closed_at\": \"2026-10-03 15:21:41\", \"opened_at\": \"2026-10-03 13:06:25\", \"created_at\": \"2026-10-03 13:06:25\", \"updated_at\": \"2026-10-03 15:21:41\", \"discrepancy\": \"-2000.00\", \"expected_cash\": \"2850.00\", \"opening_float\": \"2000.00\", \"restaurant_id\": \"1\", \"closing_cash_counted\": \"850.00\"}',NULL,'Deleted shift #1 audit record','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 11:22:33'),(333,1,NULL,1,'billing','update_shift_audit','cash_registers',4,'{\"id\": \"4\", \"notes\": null, \"status\": \"closed\", \"user_id\": \"1\", \"branch_id\": \"1\", \"closed_at\": \"2026-10-05 18:00:32\", \"opened_at\": \"2026-10-03 15:23:28\", \"created_at\": \"2026-10-03 15:23:28\", \"updated_at\": \"2026-10-05 18:00:32\", \"discrepancy\": \"-8876.55\", \"expected_cash\": \"9876.55\", \"opening_float\": \"2000.00\", \"restaurant_id\": \"1\", \"closing_cash_counted\": \"1000.00\"}','{\"notes\": null, \"discrepancy\": -1876.5499999999993, \"opening_float\": 2000, \"closing_cash_counted\": 8000}','Updated shift #4 audit details and notes','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 11:22:58'),(334,1,NULL,1,'billing','update_shift_audit','cash_registers',4,'{\"id\": \"4\", \"notes\": null, \"status\": \"closed\", \"user_id\": \"1\", \"branch_id\": \"1\", \"closed_at\": \"2026-10-05 18:00:32\", \"opened_at\": \"2026-10-03 15:23:28\", \"created_at\": \"2026-10-03 15:23:28\", \"updated_at\": \"2026-10-06 11:22:58\", \"discrepancy\": \"-1876.55\", \"expected_cash\": \"9876.55\", \"opening_float\": \"2000.00\", \"restaurant_id\": \"1\", \"closing_cash_counted\": \"8000.00\"}','{\"notes\": null, \"discrepancy\": -76.54999999999927, \"opening_float\": 2000, \"closing_cash_counted\": 9800}','Updated shift #4 audit details and notes','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 11:23:23'),(335,1,NULL,1,'users','create','users',9,NULL,'{\"email\": \"riya@gmail.com\", \"phone\": \"+919887654321\", \"role_id\": 6, \"password\": \"riya@123\", \"branch_id\": \"1\", \"is_active\": 1, \"last_name\": \"paul\", \"first_name\": \"Riya\", \"employee_id\": \"EMP-11\", \"joining_date\": \"2026-10-06\", \"restaurant_id\": 1}','New user created: riya@gmail.com','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 11:28:16'),(336,1,NULL,1,'auth','logout','users',1,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 11:28:42'),(337,NULL,NULL,NULL,'auth','login','users',9,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 11:29:03'),(338,1,1,9,'auth','logout','users',9,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 11:30:33'),(339,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 11:30:43'),(340,1,NULL,1,'floors','table_status_change','restaurant_tables',2,'{\"old_status\": \"dirty\"}','{\"new_status\": \"available\"}','Table T-02 set to available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 12:06:54'),(341,1,NULL,1,'orders','update_status','orders',4,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261003-0003 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 12:26:11'),(342,1,NULL,1,'menu_items','create','menu_items',20,NULL,'{\"code\": \"MAI-07\", \"name\": \"chicken bharta\", \"price\": 250, \"category_id\": 2}','Created dish chicken bharta (MAI-07)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 12:35:00'),(343,1,NULL,1,'menu_items','create','menu_items',21,NULL,'{\"code\": \"TAN-04\", \"name\": \"Tawa roti\", \"price\": 7, \"category_id\": 3}','Created dish Tawa roti (TAN-04)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 12:35:24'),(344,1,NULL,1,'menu_items','toggle_availability','menu_items',7,'{\"is_available\": \"0\"}','{\"is_available\": 1}','Item Butter Chicken Dhabawala marked as Available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 12:35:27'),(345,1,NULL,1,'menu_items','create','menu_items',22,NULL,'{\"code\": \"TAN-05\", \"name\": \"rumali roti\", \"price\": 10, \"category_id\": 3}','Created dish rumali roti (TAN-05)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 12:36:28'),(346,1,NULL,1,'menu_items','create','menu_items',23,NULL,'{\"code\": \"MAI-08\", \"name\": \"Mutton kasha\", \"price\": 350, \"category_id\": 2}','Created dish Mutton kasha (MAI-08)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 12:37:30'),(347,1,NULL,1,'menu_items','update','menu_items',23,'{\"id\": \"23\", \"code\": \"MAI-08\", \"name\": \"Mutton kasha\", \"price\": \"350.00\", \"is_veg\": \"1\", \"branch_id\": \"1\", \"is_active\": \"1\", \"created_at\": \"2026-10-06 12:37:30\", \"sort_order\": \"0\", \"updated_at\": \"2026-10-06 12:37:30\", \"category_id\": \"2\", \"description\": \"the most loved dish in bengal\", \"tax_rate_id\": null, \"is_available\": \"1\", \"restaurant_id\": \"1\", \"preparation_time\": \"35\"}','{\"code\": \"MAI-08\", \"name\": \"Mutton kasha\", \"price\": 350, \"is_veg\": 0, \"sort_order\": 0, \"category_id\": 2, \"description\": \"the most loved dish in bengal\", \"tax_rate_id\": null, \"is_available\": 1, \"preparation_time\": 35}','Updated dish Mutton kasha','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 12:37:55'),(348,1,1,1,'orders','create_order','orders',16,NULL,NULL,'Order #ORD-20261006-0002 placed (dine_in) for $308 (KOT: KOT-123912-002)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 12:39:12'),(349,1,NULL,1,'kds','start_cooking','kots',16,NULL,'{\"time\": \"2026-10-06 12:39:38\", \"kot_number\": \"KOT-123912-002\"}','Cooking started for KOT-123912-002','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 12:39:38'),(350,1,NULL,1,'kds','bump_ticket','kots',16,NULL,'{\"time\": \"2026-10-06 12:39:49\", \"kot_number\": \"KOT-123912-002\"}','Ticket KOT-123912-002 bumped (marked ready)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 12:39:49'),(351,1,NULL,1,'orders','update_status','orders',16,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261006-0002 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 12:40:01'),(352,1,NULL,1,'orders','update_status','orders',16,'{\"old_status\": \"served\"}','{\"new_status\": \"completed\"}','Order ORD-20261006-0002 status changed to completed','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 12:40:10'),(353,1,NULL,1,'billing','open_shift','cash_registers',8,NULL,'{\"opening_float\": 2000}','Cash register shift opened with float ₹2000','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 12:41:43'),(354,1,1,1,'analytics','export_csv',NULL,NULL,NULL,NULL,'Exported sales ledger CSV: rms_sales_export_2026_10_06.csv','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 12:43:50'),(355,1,NULL,1,'menu_categories','create','menu_categories',6,NULL,'{\"name\": \"Biriyani\", \"slug\": \"biriyani\"}','Created category Biriyani','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 12:49:41'),(356,1,NULL,1,'menu_items','create','menu_items',24,NULL,'{\"code\": \"BIR-01\", \"name\": \"Chicken biryani\", \"price\": 179, \"category_id\": 6}','Created dish Chicken biryani (BIR-01)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 12:50:20'),(357,1,NULL,1,'menu_items','create','menu_items',25,NULL,'{\"code\": \"BIR-02\", \"name\": \"Mutton biriyani\", \"price\": 300, \"category_id\": 6}','Created dish Mutton biriyani (BIR-02)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 12:51:00'),(358,1,NULL,1,'auth','logout','users',1,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 12:51:06'),(359,NULL,NULL,NULL,'auth','login','users',4,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 12:51:34'),(360,1,1,4,'orders','create_order','orders',17,NULL,NULL,'Order #ORD-20261006-0003 placed (dine_in) for $495 (KOT: KOT-125206-003)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 12:52:06'),(361,1,1,4,'auth','logout','users',4,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 12:52:43'),(362,NULL,NULL,NULL,'auth','login','users',5,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 12:53:05'),(363,1,1,5,'kds','start_cooking','kots',17,NULL,'{\"time\": \"2026-10-06 12:53:16\", \"kot_number\": \"KOT-125206-003\"}','Cooking started for KOT-125206-003','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 12:53:16'),(364,1,1,5,'kds','bump_ticket','kots',17,NULL,'{\"time\": \"2026-10-06 12:53:20\", \"kot_number\": \"KOT-125206-003\"}','Ticket KOT-125206-003 bumped (marked ready)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 12:53:20'),(365,1,1,5,'auth','logout','users',5,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 12:53:36'),(366,NULL,NULL,NULL,'auth','login','users',4,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 12:53:54'),(367,1,1,4,'orders','update_status','orders',17,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261006-0003 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 12:54:15'),(368,1,1,4,'orders','update_status','orders',17,'{\"old_status\": \"served\"}','{\"new_status\": \"completed\"}','Order ORD-20261006-0003 status changed to completed','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 12:54:33'),(369,1,1,4,'auth','logout','users',4,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 13:13:33'),(370,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 13:13:43'),(371,1,NULL,1,'auth','logout','users',1,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 13:15:03'),(372,NULL,NULL,NULL,'auth','login','users',3,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 13:16:06'),(373,1,1,3,'orders','update_status','orders',1,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261001-0001 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 13:16:48'),(374,1,1,3,'auth','logout','users',3,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 13:22:02'),(375,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 13:22:15'),(376,1,1,1,'inventory','log_waste',NULL,NULL,NULL,NULL,'Logged 0.5 units waste (burnt) on Item #4','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 13:23:39'),(377,1,NULL,1,'menu_items','toggle_availability','menu_items',7,'{\"is_available\": \"1\"}','{\"is_available\": 0}','Item Butter Chicken Dhabawala marked as Out of Stock (86ed)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 14:08:09'),(378,1,NULL,1,'menu_items','toggle_availability','menu_items',7,'{\"is_available\": \"0\"}','{\"is_available\": 1}','Item Butter Chicken Dhabawala marked as Available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 14:08:14'),(379,1,NULL,1,'menu_items','toggle_availability','menu_items',7,'{\"is_available\": \"1\"}','{\"is_available\": 0}','Item Butter Chicken Dhabawala marked as Out of Stock (86ed)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 14:09:32'),(380,1,NULL,1,'menu_items','toggle_availability','menu_items',7,'{\"is_available\": \"0\"}','{\"is_available\": 1}','Item Butter Chicken Dhabawala marked as Available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 14:09:33'),(381,1,1,1,'orders','create_order','orders',18,NULL,NULL,'Order #ORD-20261006-0004 placed (takeaway) for $325.5 (KOT: KOT-141733-004)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 14:17:33'),(382,1,NULL,1,'kds','start_cooking','kots',18,NULL,'{\"time\": \"2026-10-06 14:17:51\", \"kot_number\": \"KOT-141733-004\"}','Cooking started for KOT-141733-004','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 14:17:51'),(383,1,NULL,1,'kds','bump_ticket','kots',18,NULL,'{\"time\": \"2026-10-06 14:17:56\", \"kot_number\": \"KOT-141733-004\"}','Ticket KOT-141733-004 bumped (marked ready)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 14:17:56'),(384,1,NULL,1,'orders','update_status','orders',18,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261006-0004 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 14:18:07'),(385,1,NULL,1,'branches','update','branches',1,'{\"id\": \"1\", \"city\": \"Howrah\", \"code\": \"BR001\", \"name\": \"Main Branch Howrah\", \"email\": \"MB@gmail.com\", \"gstin\": null, \"phone\": \"+91980235944\", \"state\": \"West Bengal\", \"address\": \"Adamas university\", \"country\": \"India\", \"is_active\": \"1\", \"created_at\": \"2026-10-01 17:44:43\", \"updated_at\": \"2026-10-03 15:06:53\", \"postal_code\": \"700126\", \"closing_time\": \"23:00:00\", \"opening_time\": \"08:00:00\", \"fssai_license\": null, \"restaurant_id\": \"1\"}','{\"city\": \"Howrah\", \"code\": \"BR001\", \"name\": \"Main Branch Howrah\", \"email\": \"MB@gmail.com\", \"phone\": \"+91980235944\", \"state\": \"West Bengal\", \"address\": \"Dada boudi\", \"postal_code\": \"700126\"}',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 14:19:49'),(386,1,NULL,1,'settings','updated','settings',NULL,NULL,'{\"bill_prefix\": \"INV\", \"currency_code\": \"INR\", \"kot_auto_print\": \"1\", \"currency_symbol\": \"₹\", \"lockout_minutes\": \"15\", \"restaurant_name\": \"Kichu Khon 🍱\", \"print_footer_note\": \"Thank you for dining with us!\", \"max_login_attempts\": \"5\", \"service_charge_pct\": \"5\", \"low_stock_threshold\": \"10\", \"session_timeout_min\": \"120\", \"delivery_charge_flat\": \"40\"}',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 14:22:37'),(387,1,NULL,1,'floors','table_status_change','restaurant_tables',2,'{\"old_status\": \"dirty\"}','{\"new_status\": \"available\"}','Table T-02 set to available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 14:24:43'),(388,1,NULL,1,'floors','table_status_change','restaurant_tables',3,'{\"old_status\": \"dirty\"}','{\"new_status\": \"available\"}','Table T-03 set to available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 14:24:46'),(389,1,1,1,'orders','create_order','orders',19,NULL,NULL,'Order #ORD-20261006-0005 placed (dine_in) for $196.9 (KOT: KOT-142504-005)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 14:25:04'),(390,1,NULL,1,'orders','update_status','orders',19,'{\"old_status\": \"confirmed\"}','{\"new_status\": \"preparing\"}','Order ORD-20261006-0005 status changed to preparing','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 14:25:13'),(391,1,NULL,1,'auth','logout','users',1,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 14:55:19'),(392,NULL,NULL,NULL,'auth','login','users',4,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 14:56:11'),(393,1,1,4,'orders','create_order','orders',20,NULL,NULL,'Order #ORD-20261006-0006 placed (dine_in) for $396 (KOT: KOT-145644-006)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 14:56:44'),(394,1,1,4,'billing','settle_order','orders',20,NULL,NULL,'Settled Order #ORD-20261006-0006 for ₹316.8 across 1 tender(s)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 14:58:06'),(395,1,1,4,'orders','update_status','orders',19,'{\"old_status\": \"preparing\"}','{\"new_status\": \"ready\"}','Order ORD-20261006-0005 status changed to ready','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 14:58:22'),(396,1,1,4,'orders','update_status','orders',19,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261006-0005 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 14:58:24'),(397,1,1,4,'orders','update_status','orders',19,'{\"old_status\": \"served\"}','{\"new_status\": \"completed\"}','Order ORD-20261006-0005 status changed to completed','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 14:58:42'),(398,1,1,4,'orders','update_status','orders',18,'{\"old_status\": \"served\"}','{\"new_status\": \"completed\"}','Order ORD-20261006-0004 status changed to completed','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 14:58:49'),(399,1,1,4,'floors','table_status_change','restaurant_tables',2,'{\"old_status\": \"dirty\"}','{\"new_status\": \"available\"}','Table T-02 set to available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 15:11:14'),(400,1,1,4,'floors','table_status_change','restaurant_tables',1,'{\"old_status\": \"dirty\"}','{\"new_status\": \"available\"}','Table T-01 set to available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 15:11:16'),(401,1,1,4,'auth','logout','users',4,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 15:13:45'),(402,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 15:13:55'),(403,1,NULL,1,'branches','update','branches',3,'{\"id\": \"3\", \"city\": \"Kolkata\", \"code\": \"CP-01\", \"name\": \"Sector 5 Kolkata\", \"email\": \"cp@rms.local\", \"gstin\": null, \"phone\": \"+91 11 2345 6789\", \"state\": \"West Bengal\", \"address\": \"Block B, Inner Circle, Connaught Place\", \"country\": \"India\", \"is_active\": \"1\", \"created_at\": \"2026-10-01 17:51:36\", \"updated_at\": \"2026-10-03 15:06:08\", \"postal_code\": \"110001\", \"closing_time\": \"23:00:00\", \"opening_time\": \"08:00:00\", \"fssai_license\": null, \"restaurant_id\": \"1\"}','{\"city\": \"Kolkata\", \"code\": \"CP-01\", \"name\": \"Sector 5 Kolkata\", \"email\": \"cp@rms.local\", \"phone\": \"+91 11 2345 6789\", \"state\": \"West Bengal\", \"address\": \"Block B, Inner Circle, Connaught Place\", \"postal_code\": \"110001\"}',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 15:15:06'),(404,1,NULL,1,'kds','start_cooking','kots',20,NULL,'{\"time\": \"2026-10-06 15:16:14\", \"kot_number\": \"KOT-145644-006\"}','Cooking started for KOT-145644-006','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 15:16:14'),(405,1,NULL,1,'kds','start_cooking','kots',19,NULL,'{\"time\": \"2026-10-06 15:16:17\", \"kot_number\": \"KOT-142504-005\"}','Cooking started for KOT-142504-005','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 15:16:17'),(406,1,NULL,1,'kds','bump_ticket','kots',20,NULL,'{\"time\": \"2026-10-06 15:16:26\", \"kot_number\": \"KOT-145644-006\"}','Ticket KOT-145644-006 bumped (marked ready)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 15:16:26'),(407,1,NULL,1,'kds','bump_ticket','kots',19,NULL,'{\"time\": \"2026-10-06 15:16:27\", \"kot_number\": \"KOT-142504-005\"}','Ticket KOT-142504-005 bumped (marked ready)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 15:16:27'),(408,1,NULL,1,'orders','update_status','orders',20,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261006-0006 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 15:16:37'),(409,1,NULL,1,'orders','update_status','orders',19,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261006-0005 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 15:18:37'),(410,1,NULL,1,'reservations','create','reservations',4,NULL,'{\"status\": \"confirmed\", \"table_id\": 10, \"branch_id\": 1, \"created_by\": 1, \"guest_count\": 5, \"customer_name\": \"subhman gill\", \"restaurant_id\": 1, \"customer_email\": \"subhman@gmail.com\", \"customer_phone\": \"7418529636\", \"reservation_date\": \"2026-10-06\", \"reservation_time\": \"19:30\", \"special_requests\": \"high on life\\r\\n\"}','Reservation for subhman gill on 2026-10-06','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 15:28:45'),(411,1,1,1,'orders','create_order','orders',21,NULL,NULL,'Order #ORD-20261006-0007 placed (dine_in) for $6263.4 (KOT: KOT-153008-007)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 15:30:08'),(412,1,NULL,1,'kds','start_cooking','kots',21,NULL,'{\"time\": \"2026-10-06 15:30:25\", \"kot_number\": \"KOT-153008-007\"}','Cooking started for KOT-153008-007','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 15:30:25'),(413,1,NULL,1,'kds','bump_ticket','kots',21,NULL,'{\"time\": \"2026-10-06 15:30:28\", \"kot_number\": \"KOT-153008-007\"}','Ticket KOT-153008-007 bumped (marked ready)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 15:30:28'),(414,1,NULL,1,'orders','update_status','orders',21,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261006-0007 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 15:30:43'),(415,1,NULL,1,'orders','update_status','orders',21,'{\"old_status\": \"served\"}','{\"new_status\": \"completed\"}','Order ORD-20261006-0007 status changed to completed','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 15:30:48'),(416,1,NULL,1,'floors','table_status_change','restaurant_tables',10,'{\"old_status\": \"dirty\"}','{\"new_status\": \"available\"}','Table R-04 set to available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 17:36:35'),(417,1,NULL,1,'auth','logout','users',1,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 17:37:45'),(418,NULL,NULL,NULL,'auth','login','users',9,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 17:37:57'),(419,1,1,9,'auth','logout','users',9,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 17:55:24'),(420,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-06 17:55:38'),(421,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 10:30:54'),(422,1,1,1,'orders','create_order','orders',22,NULL,NULL,'Order #ORD-20261007-0001 placed (dine_in) for $279.4 (KOT: KOT-105227-001)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 10:52:27'),(423,1,NULL,1,'orders','update_status','orders',22,'{\"old_status\": \"confirmed\"}','{\"new_status\": \"preparing\"}','Order ORD-20261007-0001 status changed to preparing','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 10:52:36'),(424,1,NULL,1,'orders','update_status','orders',22,'{\"old_status\": \"preparing\"}','{\"new_status\": \"ready\"}','Order ORD-20261007-0001 status changed to ready','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 10:52:45'),(425,1,NULL,1,'orders','update_status','orders',22,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261007-0001 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 10:52:49'),(426,1,NULL,1,'orders','update_status','orders',22,'{\"old_status\": \"served\"}','{\"new_status\": \"completed\"}','Order ORD-20261007-0001 status changed to completed','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 10:55:37'),(427,1,NULL,1,'auth','logout','users',1,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 11:01:48'),(428,NULL,NULL,NULL,'auth','login','users',4,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 11:02:14'),(429,1,1,4,'orders','create_order','orders',23,NULL,NULL,'Order #ORD-20261007-0002 placed (dine_in) for $550 (KOT: KOT-110335-002)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 11:03:35'),(430,1,1,4,'orders','update_status','orders',23,'{\"old_status\": \"confirmed\"}','{\"new_status\": \"preparing\"}','Order ORD-20261007-0002 status changed to preparing','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 11:07:04'),(431,1,1,4,'orders','update_status','orders',23,'{\"old_status\": \"preparing\"}','{\"new_status\": \"ready\"}','Order ORD-20261007-0002 status changed to ready','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 11:18:32'),(432,1,1,4,'auth','logout','users',4,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 11:23:17'),(433,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 11:23:28'),(434,1,NULL,1,'orders','update_status','orders',23,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261007-0002 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 11:26:14'),(435,1,NULL,1,'orders','update_status','orders',23,'{\"old_status\": \"served\"}','{\"new_status\": \"completed\"}','Order ORD-20261007-0002 status changed to completed','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 11:26:21'),(436,1,NULL,1,'auth','logout','users',1,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 11:28:06'),(437,NULL,NULL,NULL,'auth','login','users',2,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 11:28:28'),(438,1,1,2,'kds','start_cooking','kots',22,NULL,'{\"time\": \"2026-10-07 11:29:32\", \"kot_number\": \"KOT-105227-001\"}','Cooking started for KOT-105227-001','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 11:29:32'),(439,1,1,2,'kds','start_cooking','kots',23,NULL,'{\"time\": \"2026-10-07 11:29:37\", \"kot_number\": \"KOT-110335-002\"}','Cooking started for KOT-110335-002','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 11:29:37'),(440,1,1,2,'kds','bump_ticket','kots',22,NULL,'{\"time\": \"2026-10-07 11:29:38\", \"kot_number\": \"KOT-105227-001\"}','Ticket KOT-105227-001 bumped (marked ready)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 11:29:38'),(441,1,1,2,'kds','bump_ticket','kots',23,NULL,'{\"time\": \"2026-10-07 11:29:41\", \"kot_number\": \"KOT-110335-002\"}','Ticket KOT-110335-002 bumped (marked ready)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 11:29:41'),(442,1,1,2,'orders','update_status','orders',23,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261007-0002 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 11:29:51'),(443,1,1,2,'orders','update_status','orders',22,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261007-0001 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 11:37:36'),(444,1,1,2,'orders','create_order','orders',24,NULL,NULL,'Order #ORD-20261007-0003 placed (dine_in) for $682 (KOT: KOT-113749-003)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 11:37:49'),(445,1,1,2,'orders','update_status','orders',24,'{\"old_status\": \"confirmed\"}','{\"new_status\": \"completed\"}','Order ORD-20261007-0003 status changed to completed','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 11:38:00'),(446,1,1,2,'auth','logout','users',2,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 11:41:36'),(447,NULL,NULL,NULL,'auth','login','users',3,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 11:44:24'),(448,1,1,3,'orders','update_status','orders',21,'{\"old_status\": \"completed\"}','{\"new_status\": \"ready\"}','Order ORD-20261006-0007 status changed to ready','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 11:49:07'),(449,1,1,3,'orders','update_status','orders',21,'{\"old_status\": \"ready\"}','{\"new_status\": \"preparing\"}','Order ORD-20261006-0007 status changed to preparing','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 11:49:09'),(450,1,1,3,'orders','update_status','orders',21,'{\"old_status\": \"preparing\"}','{\"new_status\": \"served\"}','Order ORD-20261006-0007 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 11:49:10'),(451,1,1,3,'orders','update_status','orders',21,'{\"old_status\": \"served\"}','{\"new_status\": \"cancelled\"}','Order ORD-20261006-0007 status changed to cancelled','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 11:49:12'),(452,1,1,3,'orders','update_status','orders',24,'{\"old_status\": \"completed\"}','{\"new_status\": \"cancelled\"}','Order ORD-20261007-0003 status changed to cancelled','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 11:54:13'),(453,1,1,3,'orders','update_status','orders',24,'{\"old_status\": \"cancelled\"}','{\"new_status\": \"completed\"}','Order ORD-20261007-0003 status changed to completed','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 12:02:07'),(454,1,1,3,'orders','update_status','orders',23,'{\"old_status\": \"served\"}','{\"new_status\": \"completed\"}','Order ORD-20261007-0002 status changed to completed','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 12:02:32'),(455,1,1,3,'orders','update_status','orders',24,'{\"old_status\": \"completed\"}','{\"new_status\": \"cancelled\"}','Order ORD-20261007-0003 status changed to cancelled','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 12:05:38'),(456,1,1,3,'orders','update_status','orders',24,'{\"old_status\": \"cancelled\"}','{\"new_status\": \"completed\"}','Order ORD-20261007-0003 status changed to completed','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 12:06:04'),(457,1,1,3,'auth','logout','users',3,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 12:12:07'),(458,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 12:12:48'),(459,1,NULL,1,'kds','start_cooking','kots',24,NULL,'{\"time\": \"2026-10-07 12:14:37\", \"kot_number\": \"KOT-113749-003\"}','Cooking started for KOT-113749-003','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 12:14:37'),(460,1,NULL,1,'kds','bump_ticket','kots',24,NULL,'{\"time\": \"2026-10-07 12:14:41\", \"kot_number\": \"KOT-113749-003\"}','Ticket KOT-113749-003 bumped (marked ready)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 12:14:41'),(461,1,NULL,1,'floors','table_status_change','restaurant_tables',2,'{\"old_status\": \"dirty\"}','{\"new_status\": \"available\"}','Table T-02 set to available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 12:15:10'),(462,1,NULL,1,'floors','table_status_change','restaurant_tables',1,'{\"old_status\": \"dirty\"}','{\"new_status\": \"available\"}','Table T-01 set to available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 12:15:11'),(463,1,NULL,1,'floors','create_table','restaurant_tables',13,'[]','{\"floor_id\": 3, \"table_number\": \"VP-03\"}','Created table VP-03','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 12:22:12'),(464,1,1,1,'orders','create_order','orders',25,NULL,NULL,'Order #ORD-20261007-0004 placed (dine_in) for $440 (KOT: KOT-122349-004)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 12:23:49'),(465,1,1,1,'billing','settle_order','orders',25,NULL,NULL,'Settled Order #ORD-20261007-0004 for ₹356 across 1 tender(s)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 12:23:49'),(466,1,NULL,1,'kds','start_cooking','kots',25,NULL,'{\"time\": \"2026-10-07 12:23:57\", \"kot_number\": \"KOT-122349-004\"}','Cooking started for KOT-122349-004','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 12:23:57'),(467,1,NULL,1,'kds','bump_ticket','kots',25,NULL,'{\"time\": \"2026-10-07 12:24:01\", \"kot_number\": \"KOT-122349-004\"}','Ticket KOT-122349-004 bumped (marked ready)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 12:24:01'),(468,1,NULL,1,'orders','update_status','orders',25,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261007-0004 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 12:24:09'),(469,1,1,1,'loyalty','adjust_points',NULL,NULL,NULL,NULL,'Awarded +150 points for Customer #2: the birthday gift was really good at your side soo that we are really happy visit again and again ','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 12:44:47'),(470,1,1,1,'loyalty','adjust_points',NULL,NULL,NULL,NULL,'Awarded +250 points for Customer #1: thank you ','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 12:45:19'),(471,1,1,1,'loyalty','adjust_points',NULL,NULL,NULL,NULL,'Redeemed -150 points for Customer #1: lll','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 12:46:57'),(472,1,NULL,1,'users','create','users',10,NULL,'{\"email\": \"karan@gmail.com\", \"phone\": \"7679199805\", \"role_id\": 2, \"password\": \"123456789\", \"branch_id\": \"3\", \"is_active\": 1, \"last_name\": \"upadhyay\", \"first_name\": \"karan \", \"employee_id\": \"EMP-10\", \"joining_date\": null, \"restaurant_id\": 1}','New user created: karan@gmail.com','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 12:49:38'),(473,1,NULL,1,'auth','logout','users',1,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 12:49:48'),(474,NULL,NULL,NULL,'auth','login','users',10,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 12:49:59'),(475,1,3,10,'menu_items','create','menu_items',26,NULL,'{\"code\": \"STA-05\", \"name\": \"chicken kabiraji\", \"price\": 150, \"category_id\": 1}','Created dish chicken kabiraji (STA-05)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 12:52:06'),(476,1,1,10,'inventory','create_item',NULL,NULL,NULL,NULL,'Added raw material item: chicken  (SKU: RAW-CHIC-20)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 12:53:25'),(477,1,1,10,'inventory','create_item',NULL,NULL,NULL,NULL,'Added raw material item: Mutton (SKU: RAW-MUTT-94)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 12:54:01'),(478,1,3,10,'auth','logout','users',10,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 12:57:48'),(479,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 12:58:03'),(480,1,NULL,1,'users','update','users',10,'{\"id\": \"10\", \"email\": \"karan@gmail.com\", \"phone\": \"7679199805\", \"avatar\": null, \"gender\": null, \"address\": null, \"role_id\": \"2\", \"branch_id\": \"3\", \"is_active\": \"1\", \"last_name\": \"upadhyay\", \"created_at\": \"2026-10-07 12:49:38\", \"first_name\": \"karan \", \"updated_at\": \"2026-10-07 12:49:59\", \"employee_id\": \"EMP-10\", \"joining_date\": null, \"locked_until\": null, \"date_of_birth\": null, \"last_login_at\": \"2026-10-07 12:49:59\", \"last_login_ip\": \"::1\", \"password_hash\": \"$2y$12$R.9TosSlKoaOZMUqPA0q1uXUBjEJig3x22pqKn0HBJTYd8UQF9ovC\", \"restaurant_id\": \"1\", \"deactivated_at\": null, \"deactivated_by\": null, \"remember_token\": null, \"emergency_phone\": null, \"email_verified_at\": null, \"emergency_contact\": null, \"failed_login_count\": \"0\", \"password_changed_at\": null}','{\"id\": 10, \"email\": \"karan@gmail.com\", \"phone\": \"7679199805\", \"role_id\": 2, \"branch_id\": null, \"last_name\": \"upadhyay\", \"first_name\": \"karan \", \"employee_id\": \"EMP-10\", \"restaurant_id\": 1}',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 12:58:32'),(481,1,NULL,1,'auth','logout','users',1,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 12:58:42'),(482,NULL,NULL,NULL,'auth','login','users',10,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 12:58:55'),(483,1,NULL,10,'orders','update_status','orders',24,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261007-0003 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 12:59:12'),(484,1,1,10,'orders','create_order','orders',26,NULL,NULL,'Order #ORD-20261007-0005 placed (dine_in) for $1006.5 (KOT: KOT-130819-005)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 13:08:20'),(485,1,1,10,'billing','settle_order','orders',26,NULL,NULL,'Settled Order #ORD-20261007-0005 for ₹756.5 across 1 tender(s)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 13:08:20'),(486,1,NULL,10,'floors','table_status_change','restaurant_tables',2,'{\"old_status\": \"dirty\"}','{\"new_status\": \"available\"}','Table T-02 set to available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 13:09:40'),(487,1,NULL,10,'floors','table_status_change','restaurant_tables',3,'{\"old_status\": \"dirty\"}','{\"new_status\": \"available\"}','Table T-03 set to available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 13:09:41'),(488,1,NULL,10,'kds','start_cooking','kots',26,NULL,'{\"time\": \"2026-10-07 14:49:15\", \"kot_number\": \"KOT-130819-005\"}','Cooking started for KOT-130819-005','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 14:49:15'),(489,1,NULL,10,'kds','bump_ticket','kots',26,NULL,'{\"time\": \"2026-10-07 14:49:16\", \"kot_number\": \"KOT-130819-005\"}','Ticket KOT-130819-005 bumped (marked ready)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 14:49:16'),(490,1,1,10,'loyalty','adjust_points',NULL,NULL,NULL,NULL,'Redeemed -150 points for Customer #1: thank you ','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 14:53:06'),(491,1,1,10,'loyalty','adjust_points',NULL,NULL,NULL,NULL,'Awarded +34 points for Customer #1: thank you','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 14:53:51'),(492,1,1,10,'orders','create_order','orders',27,NULL,NULL,'Order #ORD-20261007-0006 placed (dine_in) for $247.5 (KOT: KOT-151938-006)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 15:19:38'),(493,1,NULL,10,'kds','start_cooking','kots',27,NULL,'{\"time\": \"2026-10-07 15:19:46\", \"kot_number\": \"KOT-151938-006\"}','Cooking started for KOT-151938-006','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 15:19:46'),(494,1,NULL,10,'kds','bump_ticket','kots',27,NULL,'{\"time\": \"2026-10-07 15:19:47\", \"kot_number\": \"KOT-151938-006\"}','Ticket KOT-151938-006 bumped (marked ready)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 15:19:47'),(495,1,1,10,'crm','create_customer',NULL,NULL,NULL,NULL,'Registered new customer suresh rathor (7679199207)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 15:20:31'),(496,1,NULL,10,'orders','update_status','orders',27,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261007-0006 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 15:24:28'),(497,1,1,10,'orders','create_order','orders',28,NULL,NULL,'Order #ORD-20261007-0007 placed (dine_in) for $583 (KOT: KOT-152745-007)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 15:27:45'),(498,1,NULL,10,'orders','update_status','orders',28,'{\"old_status\": \"confirmed\"}','{\"new_status\": \"preparing\"}','Order ORD-20261007-0007 status changed to preparing','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 15:28:39'),(499,1,NULL,10,'orders','update_status','orders',28,'{\"old_status\": \"preparing\"}','{\"new_status\": \"completed\"}','Order ORD-20261007-0007 status changed to completed','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 15:28:51'),(500,1,NULL,10,'orders','update_status','orders',26,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261007-0005 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 15:29:00'),(501,1,NULL,10,'auth','logout','users',10,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 15:30:33'),(502,NULL,NULL,NULL,'auth','login','users',4,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 15:31:08'),(503,1,1,4,'orders','update_status','orders',27,'{\"old_status\": \"served\"}','{\"new_status\": \"completed\"}','Order ORD-20261007-0006 status changed to completed','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 15:31:21'),(504,1,1,4,'floors','table_status_change','restaurant_tables',2,'{\"old_status\": \"dirty\"}','{\"new_status\": \"available\"}','Table T-02 set to available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 15:31:30'),(505,1,1,4,'floors','table_status_change','restaurant_tables',1,'{\"old_status\": \"dirty\"}','{\"new_status\": \"available\"}','Table T-01 set to available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 15:31:31'),(506,1,1,4,'orders','update_status','orders',26,'{\"old_status\": \"served\"}','{\"new_status\": \"completed\"}','Order ORD-20261007-0005 status changed to completed','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 15:33:33'),(507,1,1,4,'floors','table_status_change','restaurant_tables',2,'{\"old_status\": \"dirty\"}','{\"new_status\": \"available\"}','Table T-02 set to available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 15:39:32'),(508,1,1,4,'auth','logout','users',4,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 15:40:27'),(509,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 15:40:49'),(510,1,NULL,1,'menu_categories','create','menu_categories',7,NULL,'{\"name\": \"Chinese\", \"slug\": \"chinese\"}','Created category Chinese','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 15:41:18'),(511,1,NULL,1,'menu_categories','update','menu_categories',7,'{\"id\": \"7\", \"icon\": \"🍜\", \"name\": \"Chinese\", \"slug\": \"chinese\", \"branch_id\": \"1\", \"is_active\": \"1\", \"created_at\": \"2026-10-07 15:41:18\", \"sort_order\": \"0\", \"updated_at\": \"2026-10-07 15:41:18\", \"description\": \"\", \"restaurant_id\": \"1\"}','{\"icon\": \"🍜\", \"name\": \"Chinese\", \"slug\": \"chinese\", \"sort_order\": 0, \"description\": \"the best chinese food make here \\r\\n\"}','Updated category Chinese','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 15:43:02'),(512,1,NULL,1,'menu_items','create','menu_items',27,NULL,'{\"code\": \"STA-06\", \"name\": \"reshmi kabab\", \"price\": 125, \"category_id\": 1}','Created dish reshmi kabab (STA-06)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 15:44:33'),(513,1,NULL,1,'menu_items','update','menu_items',27,'{\"id\": \"27\", \"code\": \"STA-06\", \"name\": \"reshmi kabab\", \"price\": \"125.00\", \"is_veg\": \"1\", \"branch_id\": \"1\", \"is_active\": \"1\", \"created_at\": \"2026-10-07 15:44:33\", \"sort_order\": \"0\", \"updated_at\": \"2026-10-07 15:44:33\", \"category_id\": \"1\", \"description\": null, \"tax_rate_id\": null, \"is_available\": \"1\", \"restaurant_id\": \"1\", \"preparation_time\": \"10\"}','{\"code\": \"STA-06\", \"name\": \"reshmi kabab\", \"price\": 125, \"is_veg\": 0, \"sort_order\": 0, \"category_id\": 1, \"description\": null, \"tax_rate_id\": null, \"is_available\": 1, \"preparation_time\": 10}','Updated dish reshmi kabab','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 15:44:54'),(514,1,NULL,1,'kds','start_cooking','kots',28,NULL,'{\"time\": \"2026-10-07 15:56:58\", \"kot_number\": \"KOT-152745-007\"}','Cooking started for KOT-152745-007','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 15:56:58'),(515,1,NULL,1,'kds','bump_ticket','kots',28,NULL,'{\"time\": \"2026-10-07 15:57:24\", \"kot_number\": \"KOT-152745-007\"}','Ticket KOT-152745-007 bumped (marked ready)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 15:57:24'),(516,1,NULL,1,'orders','update_status','orders',28,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261007-0007 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 16:12:25'),(517,1,1,1,'orders','create_order','orders',29,NULL,NULL,'Order #ORD-20261007-0008 placed (dine_in) for $929.5 (KOT: KOT-162727-008)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 16:27:27'),(518,1,NULL,1,'kds','start_cooking','kots',29,NULL,'{\"time\": \"2026-10-07 16:28:08\", \"kot_number\": \"KOT-162727-008\"}','Cooking started for KOT-162727-008','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 16:28:08'),(519,1,NULL,1,'kds','bump_ticket','kots',29,NULL,'{\"time\": \"2026-10-07 16:28:10\", \"kot_number\": \"KOT-162727-008\"}','Ticket KOT-162727-008 bumped (marked ready)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 16:28:10'),(520,1,NULL,1,'orders','update_status','orders',29,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261007-0008 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 16:28:35'),(521,1,NULL,1,'orders','update_status','orders',29,'{\"old_status\": \"served\"}','{\"new_status\": \"completed\"}','Order ORD-20261007-0008 status changed to completed','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 16:28:42'),(522,1,NULL,1,'floors','table_status_change','restaurant_tables',1,'{\"old_status\": \"dirty\"}','{\"new_status\": \"available\"}','Table T-01 set to available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 16:44:46'),(523,1,NULL,1,'floors','table_status_change','restaurant_tables',7,'{\"old_status\": \"available\"}','{\"new_status\": \"reserved\"}','Table R-01 set to reserved','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 16:49:46'),(524,1,NULL,1,'floors','table_status_change','restaurant_tables',7,'{\"old_status\": \"reserved\"}','{\"new_status\": \"available\"}','Table R-01 set to available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 16:49:48'),(525,1,NULL,1,'menu_items','create','menu_items',28,NULL,'{\"code\": \"CHI-01\", \"name\": \"Chili chicken\", \"price\": 200, \"category_id\": 7}','Created dish Chili chicken (CHI-01)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 16:52:09'),(526,1,NULL,1,'menu_items','create','menu_items',29,NULL,'{\"code\": \"CHI-02\", \"name\": \"kum pao chicken\", \"price\": 250, \"category_id\": 7}','Created dish kum pao chicken (CHI-02)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 16:52:44'),(527,1,1,1,'orders','create_order','orders',30,NULL,NULL,'Order #ORD-20261007-0009 placed (dine_in) for $715 (KOT: KOT-165655-009)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 16:56:55'),(528,1,NULL,1,'orders','update_status','orders',30,'{\"old_status\": \"confirmed\"}','{\"new_status\": \"preparing\"}','Order ORD-20261007-0009 status changed to preparing','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 16:57:06'),(529,1,NULL,1,'orders','update_status','orders',30,'{\"old_status\": \"preparing\"}','{\"new_status\": \"ready\"}','Order ORD-20261007-0009 status changed to ready','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 16:57:14'),(530,1,NULL,1,'orders','update_status','orders',30,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261007-0009 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 16:57:20'),(531,1,NULL,1,'orders','update_status','orders',30,'{\"old_status\": \"served\"}','{\"new_status\": \"completed\"}','Order ORD-20261007-0009 status changed to completed','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 16:57:35'),(532,1,NULL,1,'kds','start_cooking','kots',30,NULL,'{\"time\": \"2026-10-07 16:58:54\", \"kot_number\": \"KOT-165655-009\"}','Cooking started for KOT-165655-009','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 16:58:54'),(533,1,NULL,1,'kds','bump_ticket','kots',30,NULL,'{\"time\": \"2026-10-07 16:59:02\", \"kot_number\": \"KOT-165655-009\"}','Ticket KOT-165655-009 bumped (marked ready)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 16:59:02'),(534,1,NULL,1,'orders','update_status','orders',30,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261007-0009 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 16:59:12'),(535,1,NULL,1,'floors','table_status_change','restaurant_tables',5,'{\"old_status\": \"dirty\"}','{\"new_status\": \"available\"}','Table T-05 set to available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 17:07:38'),(536,1,NULL,1,'floors','create_table','restaurant_tables',14,'[]','{\"floor_id\": 2, \"table_number\": \"R-05\"}','Created table R-05','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 17:08:13'),(537,1,NULL,1,'roles','permissions_updated','roles',4,NULL,'[61, 37, 38, 36, 84, 87, 55, 54, 1, 89, 22, 34, 33, 14, 18, 31, 29, 30, 28, 43, 25, 26, 24]',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 17:30:59'),(538,1,NULL,1,'menu_items','toggle_availability','menu_items',1,'{\"is_available\": \"1\"}','{\"is_available\": 0}','Item Paneer Tikka Angaare marked as Out of Stock (86ed)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 17:32:26'),(539,1,NULL,1,'menu_items','toggle_availability','menu_items',1,'{\"is_available\": \"0\"}','{\"is_available\": 1}','Item Paneer Tikka Angaare marked as Available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 17:32:32'),(540,1,NULL,1,'menu_items','toggle_availability','menu_items',1,'{\"is_available\": \"1\"}','{\"is_available\": 0}','Item Paneer Tikka Angaare marked as Out of Stock (86ed)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 17:32:34'),(541,1,NULL,1,'menu_items','toggle_availability','menu_items',1,'{\"is_available\": \"0\"}','{\"is_available\": 1}','Item Paneer Tikka Angaare marked as Available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 17:34:01'),(542,1,NULL,1,'menu_items','toggle_availability','menu_items',1,'{\"is_available\": \"1\"}','{\"is_available\": 0}','Item Paneer Tikka Angaare marked as Out of Stock (86ed)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 17:34:02'),(543,1,NULL,1,'menu_items','toggle_availability','menu_items',1,'{\"is_available\": \"0\"}','{\"is_available\": 1}','Item Paneer Tikka Angaare marked as Available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 17:34:04'),(544,1,NULL,1,'menu_items','update','menu_items',1,'{\"id\": \"1\", \"code\": \"APP-01\", \"name\": \"Paneer Tikka Angaare\", \"price\": \"320.00\", \"is_veg\": \"1\", \"branch_id\": null, \"is_active\": \"1\", \"created_at\": \"2026-10-01 18:00:47\", \"sort_order\": \"0\", \"updated_at\": \"2026-10-07 17:34:04\", \"category_id\": \"1\", \"description\": \"Cottage cheese marinated in spices, roasted in clay oven\", \"tax_rate_id\": \"1\", \"is_available\": \"1\", \"restaurant_id\": \"1\", \"preparation_time\": \"15\"}','{\"code\": \"APP-01\", \"name\": \"Paneer Tikka Angaare\", \"price\": 320, \"is_veg\": 1, \"sort_order\": 0, \"category_id\": 1, \"description\": \"Cottage cheese marinated in spices, roasted in clay oven\", \"tax_rate_id\": 1, \"is_available\": 1, \"preparation_time\": 15}','Updated dish Paneer Tikka Angaare','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 17:34:11'),(545,1,NULL,1,'performance','update','staff_performance_reviews',1,'{\"id\": \"1\", \"goals\": \"Complete advanced POS inventory auditing certification.\", \"user_id\": \"2\", \"branch_id\": \"1\", \"strengths\": \"Exceptional punctuality, great team leadership, zero order void errors.\", \"created_at\": \"2026-10-03 12:52:03\", \"updated_at\": \"2026-10-03 12:52:03\", \"review_date\": \"2026-10-03\", \"reviewer_id\": \"1\", \"overall_score\": \"4.80\", \"review_period\": \"October 2026 Monthly Appraisal\", \"rating_attendance\": \"5\", \"rating_hospitality\": \"5\", \"rating_punctuality\": \"4\", \"areas_for_improvement\": \"Continue cross-training kitchen station expediting.\", \"rating_order_accuracy\": \"5\"}','{\"goals\": \"Complete advanced POS inventory auditing certification.\", \"user_id\": 2, \"branch_id\": 1, \"strengths\": \"Exceptional punctuality, great team leadership, zero order void errors.\", \"review_date\": \"2026-10-07\", \"reviewer_id\": 1, \"overall_score\": 4.8, \"review_period\": \"October 2026 Monthly Appraisal\", \"rating_attendance\": 5, \"rating_hospitality\": 5, \"rating_punctuality\": 4, \"areas_for_improvement\": \"Continue cross-training kitchen station expediting.\", \"rating_order_accuracy\": 5}','Appraisal review saved for Rahul Sharma (Score: 4.8/5.0)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 17:35:07'),(546,1,NULL,1,'performance','update','staff_performance_reviews',4,'{\"id\": \"4\", \"goals\": \"\", \"user_id\": \"4\", \"branch_id\": \"1\", \"strengths\": \"\", \"created_at\": \"2026-10-05 15:05:18\", \"updated_at\": \"2026-10-05 15:05:18\", \"review_date\": \"2026-10-05\", \"reviewer_id\": \"1\", \"overall_score\": \"1.00\", \"review_period\": \"October 2026 Appraisal\", \"rating_attendance\": \"1\", \"rating_hospitality\": \"1\", \"rating_punctuality\": \"1\", \"areas_for_improvement\": \"\", \"rating_order_accuracy\": \"1\"}','{\"goals\": \"\", \"user_id\": 4, \"branch_id\": 1, \"strengths\": \"\", \"review_date\": \"2026-10-07\", \"reviewer_id\": 1, \"overall_score\": 1, \"review_period\": \"October 2026 Appraisal\", \"rating_attendance\": 1, \"rating_hospitality\": 1, \"rating_punctuality\": 1, \"areas_for_improvement\": \"\", \"rating_order_accuracy\": 1}','Appraisal review saved for subh singh (Score: 1/5.0)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 17:36:00'),(547,1,NULL,1,'performance','update','staff_performance_reviews',4,'{\"id\": \"4\", \"goals\": \"\", \"user_id\": \"4\", \"branch_id\": \"1\", \"strengths\": \"\", \"created_at\": \"2026-10-05 15:05:18\", \"updated_at\": \"2026-10-07 17:36:00\", \"review_date\": \"2026-10-07\", \"reviewer_id\": \"1\", \"overall_score\": \"1.00\", \"review_period\": \"October 2026 Appraisal\", \"rating_attendance\": \"1\", \"rating_hospitality\": \"1\", \"rating_punctuality\": \"1\", \"areas_for_improvement\": \"\", \"rating_order_accuracy\": \"1\"}','{\"goals\": \"\", \"user_id\": 4, \"branch_id\": 1, \"strengths\": \"very good hospitality and very good customer handeling\", \"review_date\": \"2026-10-07\", \"reviewer_id\": 1, \"overall_score\": 3.95, \"review_period\": \"October 2026 Appraisal\", \"rating_attendance\": 4, \"rating_hospitality\": 5, \"rating_punctuality\": 4, \"areas_for_improvement\": \"you have to maximize your accuracy and make sure what is goal\", \"rating_order_accuracy\": 3}','Appraisal review saved for subh singh (Score: 3.95/5.0)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-07 17:52:18'),(548,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 10:36:09'),(549,1,1,1,'orders','create_order','orders',31,NULL,NULL,'Order #ORD-20261008-0001 placed (dine_in) for $462 (KOT: KOT-103802-001)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 10:38:02'),(550,1,NULL,1,'auth','logout','users',1,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 10:38:06'),(551,NULL,NULL,NULL,'auth','login','users',5,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 10:39:24'),(552,1,1,5,'kds','start_cooking','kots',31,NULL,'{\"time\": \"2026-10-08 10:39:33\", \"kot_number\": \"KOT-103802-001\"}','Cooking started for KOT-103802-001','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 10:39:33'),(553,1,1,5,'kds','bump_ticket','kots',31,NULL,'{\"time\": \"2026-10-08 10:39:34\", \"kot_number\": \"KOT-103802-001\"}','Ticket KOT-103802-001 bumped (marked ready)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 10:39:34'),(554,1,1,5,'auth','logout','users',5,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 10:39:47'),(555,NULL,NULL,NULL,'auth','login','users',1,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 10:40:02'),(556,1,NULL,1,'orders','update_status','orders',31,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261008-0001 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 10:40:15'),(557,1,NULL,1,'orders','update_status','orders',31,'{\"old_status\": \"served\"}','{\"new_status\": \"completed\"}','Order ORD-20261008-0001 status changed to completed','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 10:40:24'),(558,1,1,1,'orders','create_order','orders',32,NULL,NULL,'Order #ORD-20261008-0002 placed (dine_in) for $792 (KOT: KOT-104535-002)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 10:45:35'),(559,1,NULL,1,'kds','start_cooking','kots',32,NULL,'{\"time\": \"2026-10-08 10:45:42\", \"kot_number\": \"KOT-104535-002\"}','Cooking started for KOT-104535-002','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 10:45:42'),(560,1,NULL,1,'kds','bump_ticket','kots',32,NULL,'{\"time\": \"2026-10-08 10:46:17\", \"kot_number\": \"KOT-104535-002\"}','Ticket KOT-104535-002 bumped (marked ready)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 10:46:17'),(561,1,NULL,1,'orders','update_status','orders',32,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261008-0002 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 10:46:26'),(562,1,NULL,1,'orders','update_status','orders',32,'{\"old_status\": \"served\"}','{\"new_status\": \"completed\"}','Order ORD-20261008-0002 status changed to completed','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 10:57:56'),(563,1,1,1,'orders','create_order','orders',33,NULL,NULL,'Order #ORD-20261008-0003 placed (dine_in) for $374 (KOT: KOT-110241-003)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 11:02:41'),(564,1,NULL,1,'orders','update_status','orders',33,'{\"old_status\": \"confirmed\"}','{\"new_status\": \"preparing\"}','Order ORD-20261008-0003 status changed to preparing','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 11:03:22'),(565,1,NULL,1,'orders','update_status','orders',33,'{\"old_status\": \"preparing\"}','{\"new_status\": \"completed\"}','Order ORD-20261008-0003 status changed to completed','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 11:03:57'),(566,1,NULL,1,'kds','start_cooking','kots',33,NULL,'{\"time\": \"2026-10-08 11:15:41\", \"kot_number\": \"KOT-110241-003\"}','Cooking started for KOT-110241-003','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 11:15:41'),(567,1,NULL,1,'kds','bump_ticket','kots',33,NULL,'{\"time\": \"2026-10-08 11:15:55\", \"kot_number\": \"KOT-110241-003\"}','Ticket KOT-110241-003 bumped (marked ready)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 11:15:55'),(568,1,NULL,1,'orders','update_status','orders',33,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261008-0003 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 11:16:01'),(569,1,NULL,1,'orders','update_status','orders',33,'{\"old_status\": \"served\"}','{\"new_status\": \"completed\"}','Order ORD-20261008-0003 status changed to completed','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 11:19:37'),(570,1,NULL,1,'orders','update_status','orders',33,'{\"old_status\": \"completed\"}','{\"new_status\": \"served\"}','Order ORD-20261008-0003 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 11:19:40'),(571,1,NULL,1,'orders','update_status','orders',33,'{\"old_status\": \"served\"}','{\"new_status\": \"preparing\"}','Order ORD-20261008-0003 status changed to preparing','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 11:19:42'),(572,1,NULL,1,'orders','update_status','orders',33,'{\"old_status\": \"preparing\"}','{\"new_status\": \"confirmed\"}','Order ORD-20261008-0003 status changed to confirmed','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 11:19:43'),(573,1,NULL,1,'orders','update_status','orders',33,'{\"old_status\": \"confirmed\"}','{\"new_status\": \"ready\"}','Order ORD-20261008-0003 status changed to ready','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 11:19:44'),(574,1,NULL,1,'orders','update_status','orders',33,'{\"old_status\": \"ready\"}','{\"new_status\": \"completed\"}','Order ORD-20261008-0003 status changed to completed','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 11:19:46'),(575,1,1,1,'orders','create_order','orders',34,NULL,NULL,'Order #ORD-20261008-0004 placed (dine_in) for $1639 (KOT: KOT-112121-004)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 11:21:21'),(576,1,1,1,'orders','create_order','orders',35,NULL,NULL,'Order #ORD-20261008-0005 placed (dine_in) for $616 (KOT: KOT-113620-005)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 11:36:20'),(577,1,NULL,1,'orders','update_status','orders',35,'{\"old_status\": \"confirmed\"}','{\"new_status\": \"preparing\"}','Order ORD-20261008-0005 status changed to preparing','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 11:36:40'),(578,1,NULL,1,'orders','update_status','orders',35,'{\"old_status\": \"preparing\"}','{\"new_status\": \"ready\"}','Order ORD-20261008-0005 status changed to ready','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 11:36:57'),(579,1,NULL,1,'orders','update_status','orders',34,'{\"old_status\": \"confirmed\"}','{\"new_status\": \"preparing\"}','Order ORD-20261008-0004 status changed to preparing','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 11:38:18'),(580,1,NULL,1,'orders','update_status','orders',34,'{\"old_status\": \"preparing\"}','{\"new_status\": \"ready\"}','Order ORD-20261008-0004 status changed to ready','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 11:38:25'),(581,1,NULL,1,'orders','update_status','orders',35,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261008-0005 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 11:38:29'),(582,1,NULL,1,'orders','update_status','orders',35,'{\"old_status\": \"served\"}','{\"new_status\": \"completed\"}','Order ORD-20261008-0005 status changed to completed','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 11:38:54'),(583,1,NULL,1,'kds','start_cooking','kots',34,NULL,'{\"time\": \"2026-10-08 11:41:19\", \"kot_number\": \"KOT-112121-004\"}','Cooking started for KOT-112121-004','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 11:41:19'),(584,1,NULL,1,'kds','start_cooking','kots',35,NULL,'{\"time\": \"2026-10-08 11:41:20\", \"kot_number\": \"KOT-113620-005\"}','Cooking started for KOT-113620-005','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 11:41:20'),(585,1,NULL,1,'kds','bump_ticket','kots',34,NULL,'{\"time\": \"2026-10-08 11:41:22\", \"kot_number\": \"KOT-112121-004\"}','Ticket KOT-112121-004 bumped (marked ready)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 11:41:22'),(586,1,NULL,1,'kds','bump_ticket','kots',35,NULL,'{\"time\": \"2026-10-08 11:41:23\", \"kot_number\": \"KOT-113620-005\"}','Ticket KOT-113620-005 bumped (marked ready)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 11:41:23'),(587,1,NULL,1,'orders','update_status','orders',35,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261008-0005 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 11:41:49'),(588,1,NULL,1,'orders','update_status','orders',34,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261008-0004 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 11:41:53'),(589,1,NULL,1,'orders','update_status','orders',34,'{\"old_status\": \"served\"}','{\"new_status\": \"completed\"}','Order ORD-20261008-0004 status changed to completed','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 11:44:09'),(590,1,1,1,'orders','create_order','orders',36,NULL,NULL,'Order #ORD-20261008-0006 placed (dine_in) for $742.5 (KOT: KOT-115608-006)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 11:56:08'),(591,1,NULL,1,'auth','logout','users',1,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 11:56:33'),(592,NULL,NULL,NULL,'auth','login','users',4,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 11:57:38'),(593,1,1,4,'orders','update_status','orders',36,'{\"old_status\": \"confirmed\"}','{\"new_status\": \"preparing\"}','Order ORD-20261008-0006 status changed to preparing','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 11:57:48'),(594,1,1,4,'orders','update_status','orders',36,'{\"old_status\": \"preparing\"}','{\"new_status\": \"ready\"}','Order ORD-20261008-0006 status changed to ready','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 11:57:54'),(595,1,1,4,'orders','update_status','orders',36,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261008-0006 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 12:03:28'),(596,1,1,4,'floors','table_status_change','restaurant_tables',4,'{\"old_status\": \"dirty\"}','{\"new_status\": \"available\"}','Table T-04 set to available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 12:03:38'),(597,1,1,4,'floors','table_status_change','restaurant_tables',5,'{\"old_status\": \"dirty\"}','{\"new_status\": \"available\"}','Table T-05 set to available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 12:03:40'),(598,1,1,4,'floors','table_status_change','restaurant_tables',6,'{\"old_status\": \"dirty\"}','{\"new_status\": \"available\"}','Table T-06 set to available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 12:03:41'),(599,1,1,4,'floors','table_status_change','restaurant_tables',2,'{\"old_status\": \"dirty\"}','{\"new_status\": \"available\"}','Table T-02 set to available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 12:03:43'),(600,1,1,4,'floors','table_status_change','restaurant_tables',1,'{\"old_status\": \"dirty\"}','{\"new_status\": \"available\"}','Table T-01 set to available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 12:03:44'),(601,1,1,4,'orders','update_status','orders',36,'{\"old_status\": \"served\"}','{\"new_status\": \"completed\"}','Order ORD-20261008-0006 status changed to completed','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 12:19:46'),(602,1,1,4,'orders','update_status','orders',35,'{\"old_status\": \"served\"}','{\"new_status\": \"completed\"}','Order ORD-20261008-0005 status changed to completed','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 12:19:55'),(603,1,1,4,'orders','create_order','orders',37,NULL,NULL,'Order #ORD-20261008-0007 placed (delivery) for $544.95 (KOT: KOT-122123-007)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 12:21:23'),(604,1,1,4,'auth','logout','users',4,NULL,NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 12:21:40'),(605,NULL,NULL,NULL,'auth','login','users',2,NULL,NULL,'Successful login from ::1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 12:22:25'),(606,1,1,2,'orders','update_status','orders',37,'{\"old_status\": \"confirmed\"}','{\"new_status\": \"preparing\"}','Order ORD-20261008-0007 status changed to preparing','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 12:27:10'),(607,1,1,2,'orders','update_status','orders',37,'{\"old_status\": \"preparing\"}','{\"new_status\": \"ready\"}','Order ORD-20261008-0007 status changed to ready','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 12:27:14'),(608,1,1,2,'orders','update_status','orders',37,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261008-0007 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 12:27:20'),(609,1,1,2,'orders','update_status','orders',37,'{\"old_status\": \"served\"}','{\"new_status\": \"completed\"}','Order ORD-20261008-0007 status changed to completed','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 12:27:26'),(610,1,1,2,'menu_items','create','menu_items',30,NULL,'{\"code\": \"BIR-03\", \"name\": \"Special chicken biryani\", \"price\": 450, \"category_id\": 6}','Created dish Special chicken biryani (BIR-03)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 12:58:55'),(611,1,1,2,'menu_categories','create','menu_categories',8,NULL,'{\"name\": \"Pasta\", \"slug\": \"pasta\"}','Created category Pasta','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 13:00:17'),(612,1,1,2,'menu_items','create','menu_items',31,NULL,'{\"code\": \"PAS-01\", \"name\": \"white sauce pasta\", \"price\": 200, \"category_id\": 8}','Created dish white sauce pasta (PAS-01)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 13:01:03'),(613,1,1,2,'kds','start_cooking','kots',36,NULL,'{\"time\": \"2026-10-08 14:23:21\", \"kot_number\": \"KOT-115608-006\"}','Cooking started for KOT-115608-006','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 14:23:21'),(614,1,1,2,'kds','start_cooking','kots',37,NULL,'{\"time\": \"2026-10-08 14:23:25\", \"kot_number\": \"KOT-122123-007\"}','Cooking started for KOT-122123-007','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 14:23:25'),(615,1,1,2,'kds','bump_ticket','kots',36,NULL,'{\"time\": \"2026-10-08 14:23:33\", \"kot_number\": \"KOT-115608-006\"}','Ticket KOT-115608-006 bumped (marked ready)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 14:23:33'),(616,1,1,2,'kds','bump_ticket','kots',37,NULL,'{\"time\": \"2026-10-08 14:23:35\", \"kot_number\": \"KOT-122123-007\"}','Ticket KOT-122123-007 bumped (marked ready)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 14:23:35'),(617,1,1,2,'orders','update_status','orders',37,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261008-0007 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 14:23:59'),(618,1,1,2,'orders','update_status','orders',36,'{\"old_status\": \"ready\"}','{\"new_status\": \"served\"}','Order ORD-20261008-0006 status changed to served','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 14:24:02'),(619,1,1,2,'floors','table_status_change','restaurant_tables',2,'{\"old_status\": \"dirty\"}','{\"new_status\": \"available\"}','Table T-02 set to available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 14:30:50'),(620,1,1,2,'floors','table_status_change','restaurant_tables',3,'{\"old_status\": \"dirty\"}','{\"new_status\": \"available\"}','Table T-03 set to available','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 14:30:52'),(621,1,1,2,'orders','update_status','orders',37,'{\"old_status\": \"served\"}','{\"new_status\": \"completed\"}','Order ORD-20261008-0007 status changed to completed','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-08 14:32:40');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branches`
--

DROP TABLE IF EXISTS `branches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `branches` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` int unsigned NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'India',
  `postal_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gstin` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fssai_license` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `opening_time` time DEFAULT '08:00:00',
  `closing_time` time DEFAULT '23:00:00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_branches_restaurant` (`restaurant_id`),
  CONSTRAINT `fk_branches_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branches`
--

LOCK TABLES `branches` WRITE;
/*!40000 ALTER TABLE `branches` DISABLE KEYS */;
INSERT INTO `branches` VALUES (1,1,'Main Branch Howrah','BR001','Dada boudi','Howrah','West Bengal','India','700126','+91980235944','MB@gmail.com',NULL,NULL,'08:00:00','23:00:00',1,'2026-10-01 17:44:43','2026-10-06 14:19:49'),(2,1,'Connaught Place ','CP-01','Block B, Inner Circle, Connaught Place','New Delhi','Delhi','India','110001','+91 11 2345 6789','cp@rms.local',NULL,NULL,'08:00:00','23:00:00',1,'2026-10-01 17:51:02','2026-10-03 15:05:37'),(3,1,'Sector 5 Kolkata','CP-01','Block B, Inner Circle, Connaught Place','Kolkata','West Bengal','India','110001','+91 11 2345 6789','cp@rms.local',NULL,NULL,'08:00:00','23:00:00',1,'2026-10-01 17:51:36','2026-10-06 15:15:06'),(4,1,'KK Ahmd','AH-001','St xyz lane','Ahmedabad','Gujrat','India','700236','+919800236587','KK@gmail.com',NULL,NULL,'08:00:00','23:00:00',1,'2026-10-03 15:08:36','2026-10-03 15:08:36');
/*!40000 ALTER TABLE `branches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cash_registers`
--

DROP TABLE IF EXISTS `cash_registers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cash_registers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` int unsigned NOT NULL DEFAULT '1',
  `branch_id` int unsigned DEFAULT NULL,
  `user_id` int unsigned NOT NULL,
  `opened_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `closed_at` datetime DEFAULT NULL,
  `opening_float` decimal(10,2) NOT NULL DEFAULT '0.00',
  `closing_cash_counted` decimal(10,2) DEFAULT NULL,
  `expected_cash` decimal(10,2) DEFAULT NULL,
  `discrepancy` decimal(10,2) DEFAULT NULL,
  `status` enum('open','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cr_branch` (`branch_id`),
  KEY `idx_cr_user` (`user_id`),
  KEY `idx_cr_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cash_registers`
--

LOCK TABLES `cash_registers` WRITE;
/*!40000 ALTER TABLE `cash_registers` DISABLE KEYS */;
INSERT INTO `cash_registers` VALUES (2,1,1,1,'2026-10-03 15:21:58','2026-10-03 15:22:37',2000.00,2000.00,2000.00,0.00,'closed',NULL,'2026-10-03 15:21:58','2026-10-03 15:22:37'),(3,1,1,1,'2026-10-03 15:22:45','2026-10-03 15:23:00',2000.00,50.00,2000.00,-1950.00,'closed',NULL,'2026-10-03 15:22:45','2026-10-03 15:23:00'),(4,1,1,1,'2026-10-03 15:23:28','2026-10-05 18:00:32',2000.00,9800.00,9876.55,-76.55,'closed',NULL,'2026-10-03 15:23:28','2026-10-06 11:23:23'),(5,1,1,3,'2026-10-05 18:00:36','2026-10-06 11:10:45',2000.00,6000.00,6737.80,-737.80,'closed',NULL,'2026-10-05 18:00:36','2026-10-06 11:10:45'),(6,1,1,1,'2026-10-06 11:11:01','2026-10-06 11:14:28',1000.00,1596.00,1596.00,0.00,'closed',NULL,'2026-10-06 11:11:01','2026-10-06 11:14:28'),(7,1,1,1,'2026-10-06 11:15:08','2026-10-06 11:15:20',1000.00,1200.00,1000.00,200.00,'closed',NULL,'2026-10-06 11:15:08','2026-10-06 11:15:20'),(8,1,1,1,'2026-10-06 12:41:43',NULL,2000.00,NULL,17566.16,NULL,'open',NULL,'2026-10-06 12:41:43','2026-10-08 14:30:59');
/*!40000 ALTER TABLE `cash_registers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cash_transactions`
--

DROP TABLE IF EXISTS `cash_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cash_transactions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `register_id` int unsigned NOT NULL,
  `type` enum('cash_in','cash_out') COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ct_register` (`register_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cash_transactions`
--

LOCK TABLES `cash_transactions` WRITE;
/*!40000 ALTER TABLE `cash_transactions` DISABLE KEYS */;
INSERT INTO `cash_transactions` VALUES (4,4,'cash_in',500.00,'friends come here',3,'2026-10-05 17:59:49','2026-10-05 17:59:49'),(5,5,'cash_in',5000.00,'paid',1,'2026-10-06 11:06:25','2026-10-06 11:06:25'),(6,5,'cash_out',3000.00,'paid grocery',1,'2026-10-06 11:07:54','2026-10-06 11:07:54'),(7,6,'cash_in',500.00,'tip',1,'2026-10-06 11:11:54','2026-10-06 11:11:54'),(8,6,'cash_out',300.00,'bill payment',1,'2026-10-06 11:12:23','2026-10-06 11:12:23');
/*!40000 ALTER TABLE `cash_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chart_of_accounts`
--

DROP TABLE IF EXISTS `chart_of_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chart_of_accounts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` int unsigned NOT NULL DEFAULT '1',
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('asset','liability','equity','revenue','expense') COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `current_balance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chart_of_accounts`
--

LOCK TABLES `chart_of_accounts` WRITE;
/*!40000 ALTER TABLE `chart_of_accounts` DISABLE KEYS */;
INSERT INTO `chart_of_accounts` VALUES (1,1,'1010','Main Cash Register','asset','Current Assets',25000.00,1,'2026-10-03 13:11:09'),(2,1,'1020','Bank Operating Account','asset','Current Assets',185000.00,1,'2026-10-03 13:11:09'),(3,1,'1030','Accounts Receivable (Credit)','asset','Current Assets',0.00,1,'2026-10-03 13:11:09'),(4,1,'1040','Food & Beverage Inventory','asset','Inventory',84000.00,1,'2026-10-03 13:11:09'),(5,1,'2010','Accounts Payable (Vendors)','liability','Current Liabilities',32000.00,1,'2026-10-03 13:11:09'),(6,1,'2020','GST / VAT Tax Payable','liability','Current Liabilities',14500.00,1,'2026-10-03 13:11:09'),(7,1,'3010','Owner Capital & Equity','equity','Equity',200000.00,1,'2026-10-03 13:11:09'),(8,1,'4010','Dine-In Food & Beverage Revenue','revenue','Sales Revenue',0.00,1,'2026-10-03 13:11:09'),(9,1,'4020','Takeaway & Delivery Revenue','revenue','Sales Revenue',0.00,1,'2026-10-03 13:11:09'),(10,1,'4030','Delivery Fee Income','revenue','Service Revenue',0.00,1,'2026-10-03 13:11:09'),(11,1,'5010','Cost of Goods Sold (Raw Ingredients)','expense','COGS',0.00,1,'2026-10-03 13:11:09'),(12,1,'6010','Staff Wages & Overtime','expense','Operating Expenses',0.00,1,'2026-10-03 13:11:09'),(13,1,'6020','Kitchen LPG & Utilities','expense','Operating Expenses',0.00,1,'2026-10-03 13:11:09'),(14,1,'6030','Equipment Repairs & Maintenance','expense','Operating Expenses',0.00,1,'2026-10-03 13:11:09'),(15,1,'6040','Promotional Discounts Given','expense','Marketing & Sales',0.00,1,'2026-10-03 13:11:09');
/*!40000 ALTER TABLE `chart_of_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `communication_logs`
--

DROP TABLE IF EXISTS `communication_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `communication_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` int unsigned NOT NULL DEFAULT '1',
  `branch_id` int unsigned NOT NULL DEFAULT '1',
  `recipient_type` enum('customer','staff','broadcast') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer',
  `recipient_id` int unsigned DEFAULT NULL,
  `recipient_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `recipient_contact` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `channel` enum('in_app','sms','email','whatsapp') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'in_app',
  `category` enum('order','reservation','payment','low_stock','announcement','promo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'order',
  `subject` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('queued','sent','delivered','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sent',
  `sent_by` int unsigned NOT NULL,
  `sent_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cl_branch_cat` (`branch_id`,`category`),
  KEY `idx_cl_channel_status` (`channel`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `communication_logs`
--

LOCK TABLES `communication_logs` WRITE;
/*!40000 ALTER TABLE `communication_logs` DISABLE KEYS */;
INSERT INTO `communication_logs` VALUES (1,1,1,'staff',NULL,'All Kitchen Staff','team-kitchen@rms.local','in_app','announcement','Weekend Special Menu Briefing','All station leads please note the updated plating guidelines for the Chef Special Truffle Risotto.','delivered',1,'2026-10-01 09:00:00'),(2,1,1,'customer',NULL,'Vikram Malhotra','+91 9876543210','whatsapp','reservation','Table Reservation Confirmed','Dear Vikram, your reservation for Table #4 (4 guests) at 7:30 PM is confirmed. We look forward to serving you!','delivered',1,'2026-10-02 14:15:00'),(3,1,1,'staff',NULL,'Inventory Manager','inventory@rms.local','in_app','low_stock','Low Stock Alert: Basmati Rice','Warning: Basmati Royal Rice has dropped below safety threshold (8.5 kg remaining). Please review PO.','delivered',1,'2026-10-02 16:30:00'),(4,1,1,'customer',NULL,'Ananya Roy','+91 9811223344','sms','order','Delivery Order #ORD-20261002-0014 Picked Up','Your delicious food order has been picked up by our delivery rider Rajesh Kumar and is on the way!','delivered',1,'2026-10-02 19:45:00'),(5,1,1,'customer',NULL,'Priya Sharma','+91 9911223344','whatsapp','order','Your Takeaway Order is Ready!','Hello Priya, your order #ORD-20261003-0005 is fresh and packed ready for pickup at our counter.','delivered',1,'2026-10-03 13:14:45'),(6,1,1,'broadcast',NULL,'All Service Staff','service-floor@rms.local','in_app','announcement','Evening Shift Floor Briefing at 4:45 PM','All floor captains and servers please assemble near Counter 1 for briefing on VIP banquet booking.','delivered',1,'2026-10-03 13:14:45'),(7,1,1,'customer',NULL,'Priya Sharma','+91 9911223344','whatsapp','order','Your Takeaway Order is Ready!','Hello Priya, your order #ORD-20261003-0005 is fresh and packed ready for pickup at our counter.','delivered',1,'2026-10-03 13:16:25'),(8,1,1,'broadcast',NULL,'All Service Staff','service-floor@rms.local','in_app','announcement','Evening Shift Floor Briefing at 4:45 PM','All floor captains and servers please assemble near Counter 1 for briefing on VIP banquet booking.','delivered',1,'2026-10-03 13:16:25'),(9,1,1,'broadcast',NULL,'priyanshu paul','priyanshupaul.mng2003@gmail.com','email','promo','ord-345','thank you for ordering us and please wlcome again','delivered',1,'2026-10-05 12:08:31'),(10,1,1,'customer',NULL,'priyanshu paul','9800011944','whatsapp','order','order-1','you oordeer is ready please be ready to get it','delivered',1,'2026-10-05 13:19:29'),(11,1,1,'staff',NULL,'all-stafff','7418529631','in_app','promo','alll stafff','today is big event soo be prepare all please ready for all shift','delivered',1,'2026-10-06 10:42:27');
/*!40000 ALTER TABLE `communication_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coupon_usages`
--

DROP TABLE IF EXISTS `coupon_usages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupon_usages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `coupon_id` int unsigned NOT NULL,
  `order_id` int unsigned NOT NULL,
  `customer_id` int unsigned DEFAULT NULL,
  `discount_amount` decimal(10,2) NOT NULL,
  `used_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cu_coupon` (`coupon_id`),
  KEY `idx_cu_order` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coupon_usages`
--

LOCK TABLES `coupon_usages` WRITE;
/*!40000 ALTER TABLE `coupon_usages` DISABLE KEYS */;
INSERT INTO `coupon_usages` VALUES (1,1,12,NULL,200.00,'2026-10-05 14:35:13'),(2,1,13,NULL,77.70,'2026-10-05 17:21:48'),(3,1,20,NULL,79.20,'2026-10-06 14:58:06'),(4,1,25,NULL,84.00,'2026-10-07 12:23:49'),(5,4,26,NULL,250.00,'2026-10-07 13:08:20'),(6,1,33,NULL,74.80,'2026-10-08 11:03:57'),(7,5,35,NULL,135.52,'2026-10-08 11:38:54'),(8,1,36,NULL,148.50,'2026-10-08 12:19:46'),(9,1,37,NULL,108.99,'2026-10-08 12:27:26');
/*!40000 ALTER TABLE `coupon_usages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coupons`
--

DROP TABLE IF EXISTS `coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupons` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` int unsigned NOT NULL DEFAULT '1',
  `branch_id` int unsigned DEFAULT NULL,
  `code` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('percentage','fixed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percentage',
  `value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `min_order_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `max_discount_amount` decimal(10,2) DEFAULT NULL,
  `usage_limit_total` int unsigned NOT NULL DEFAULT '100',
  `usage_limit_per_user` int unsigned NOT NULL DEFAULT '1',
  `times_used` int unsigned NOT NULL DEFAULT '0',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `idx_coupon_code_active` (`code`,`is_active`),
  KEY `idx_coupon_dates` (`start_date`,`end_date`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coupons`
--

LOCK TABLES `coupons` WRITE;
/*!40000 ALTER TABLE `coupons` DISABLE KEYS */;
INSERT INTO `coupons` VALUES (1,1,1,'WELCOME20','New Guest Welcome 20% Off','percentage',20.00,300.00,200.00,500,1,21,'2026-01-01','2026-12-31',1,'2026-10-03 12:38:44','2026-10-08 12:27:26'),(2,1,1,'FLAT100','Flat ₹100 Off on Feast','fixed',100.00,600.00,100.00,200,2,8,'2026-01-01','2026-12-31',1,'2026-10-03 12:38:44','2026-10-03 12:38:44'),(3,1,1,'VIPDINER','VIP Diner 15% Privilege','percentage',15.00,500.00,350.00,1000,5,23,'2026-01-01','2026-12-31',1,'2026-10-03 12:38:44','2026-10-03 12:38:44'),(4,1,1,'FESTIVE50','Diwali Festive 50% Off','percentage',50.00,500.00,250.00,200,1,1,'2026-10-03','2026-12-31',1,'2026-10-03 12:52:01','2026-10-07 13:08:20'),(5,1,1,'KAR2026','BDAY','percentage',22.00,0.00,999.00,100,1,1,'2026-10-05','2026-12-31',1,'2026-10-05 14:14:37','2026-10-08 11:38:54'),(6,1,1,'WIN26','winter sale','percentage',20.00,5000.00,5000.00,100,1,0,'2026-10-07','2026-12-31',0,'2026-10-07 16:31:16','2026-10-07 16:31:36');
/*!40000 ALTER TABLE `coupons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_feedback`
--

DROP TABLE IF EXISTS `customer_feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_feedback` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int unsigned DEFAULT NULL,
  `order_id` int unsigned DEFAULT NULL,
  `customer_name` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_phone` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rating` tinyint unsigned NOT NULL DEFAULT '5',
  `food_rating` tinyint unsigned NOT NULL DEFAULT '5',
  `service_rating` tinyint unsigned NOT NULL DEFAULT '5',
  `ambience_rating` tinyint unsigned NOT NULL DEFAULT '5',
  `comments` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_fb_customer` (`customer_id`),
  KEY `idx_fb_rating` (`rating`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_feedback`
--

LOCK TABLES `customer_feedback` WRITE;
/*!40000 ALTER TABLE `customer_feedback` DISABLE KEYS */;
INSERT INTO `customer_feedback` VALUES (1,1,NULL,'Arthur Pendelton','+1 555-0921',5,5,5,5,'Exceptional experience as always. The pizza crust was cooked to perfection and staff attentive!','2026-10-03 11:29:44'),(2,2,NULL,'Elena Rostova','+1 555-0814',4,5,4,4,'Great pizza, fast service. Will definitely visit again with friends.','2026-10-03 11:29:44'),(3,NULL,NULL,'Victoria Sterling','+1 555-0433',5,5,5,5,'First-class fine dining ambiance and courteous waitstaff!','2026-10-03 11:40:34'),(4,NULL,NULL,'Subham','123458796',4,5,5,5,'It was a great experience','2026-10-03 14:50:54'),(5,NULL,NULL,'cristopher nolan','',4,5,5,5,'','2026-10-06 15:00:07'),(6,NULL,NULL,'dipender goyal','7679199253',5,5,5,5,'the resturant  was really good and the food was extremly good recomended to everyone\r\n','2026-10-07 11:05:25');
/*!40000 ALTER TABLE `customer_feedback` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` int unsigned NOT NULL DEFAULT '1',
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `loyalty_points` int NOT NULL DEFAULT '0',
  `total_visits` int unsigned NOT NULL DEFAULT '0',
  `lifetime_spend` decimal(12,2) NOT NULL DEFAULT '0.00',
  `vip_status` tinyint(1) NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_customer_phone` (`restaurant_id`,`phone`),
  KEY `idx_crm_spend` (`lifetime_spend`),
  KEY `idx_crm_vip` (`vip_status`),
  KEY `idx_cust_phone` (`phone`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES (1,1,'Arthur Pendelton','+1 555-0921','arthur.p@example.com','742 Evergreen Terrace',336,13,1448.00,1,'VIP regular. Prefers Booth Table 2. Sparkling water with lime.','2026-10-03 11:29:44','2026-10-07 14:53:51'),(2,1,'Elena Rostova','+1 555-0814','elena.r@example.com','19 High St, Apt 4B',270,5,485.00,0,'Vegetarian preferences. Loves extra cheese on Margherita.','2026-10-03 11:29:44','2026-10-07 12:44:47'),(3,1,'Marcus Vance','+1 555-0763','marcus.v@example.com','124 Oakwood Avenue',50,2,195.00,0,'First registered via online reservation.','2026-10-03 11:29:44','2026-10-03 11:29:44'),(4,1,'Victoria Sterling','+1 555-0433','victoria.s@example.com','',150,0,0.00,1,'Prefers private booth table, sparkling water','2026-10-03 11:40:33','2026-10-03 11:40:34'),(5,1,'Subbham','+91980067389','Subh@gmail.com','',100,0,0.00,0,'','2026-10-05 14:11:55','2026-10-05 14:12:27'),(6,1,'stephen petro','7412589632','fghysf@gmail.com','',50,0,0.00,0,'','2026-10-05 17:09:19','2026-10-05 17:09:19'),(7,1,'suresh rathor','7679199207','','',74,1,247.50,0,'','2026-10-07 15:20:31','2026-10-07 15:31:21'),(8,1,'sugar prasad','8637046587',NULL,NULL,58,1,583.00,0,NULL,'2026-10-07 15:27:45','2026-10-07 15:28:51'),(9,1,'sayan biswas','8523697412',NULL,NULL,92,1,929.50,0,NULL,'2026-10-07 16:27:27','2026-10-07 16:28:42'),(10,1,'rupam saha','7412536987',NULL,NULL,46,1,462.00,0,NULL,'2026-10-08 10:38:02','2026-10-08 10:40:24'),(11,1,'Customer 9633','7418529633',NULL,NULL,87,3,897.60,0,NULL,'2026-10-08 11:02:41','2026-10-08 11:19:46'),(12,1,'Customer 6341','78596526341',NULL,NULL,163,1,1639.00,0,NULL,'2026-10-08 11:21:21','2026-10-08 11:44:09');
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_orders`
--

DROP TABLE IF EXISTS `delivery_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivery_orders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `partner_id` int unsigned NOT NULL,
  `branch_id` int unsigned NOT NULL DEFAULT '1',
  `assigned_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `picked_up_at` datetime DEFAULT NULL,
  `delivered_at` datetime DEFAULT NULL,
  `status` enum('assigned','picked_up','in_transit','delivered','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'assigned',
  `delivery_fee` decimal(8,2) NOT NULL DEFAULT '0.00',
  `tip_amount` decimal(8,2) NOT NULL DEFAULT '0.00',
  `customer_rating` tinyint unsigned DEFAULT NULL,
  `delivery_notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_do_partner` (`partner_id`,`status`),
  KEY `idx_do_order` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_orders`
--

LOCK TABLES `delivery_orders` WRITE;
/*!40000 ALTER TABLE `delivery_orders` DISABLE KEYS */;
INSERT INTO `delivery_orders` VALUES (1,6,5,1,'2026-10-05 15:26:30','2026-10-05 15:26:35','2026-10-05 15:26:40','delivered',40.00,0.00,NULL,'','2026-10-05 15:26:30','2026-10-05 15:26:40'),(2,14,5,1,'2026-10-05 17:31:10','2026-10-05 17:31:17','2026-10-05 17:31:26','delivered',400.00,0.00,NULL,'haapy food','2026-10-05 17:31:10','2026-10-05 17:31:26'),(3,37,5,1,'2026-10-08 12:24:12','2026-10-08 12:24:17','2026-10-08 12:25:07','delivered',40.00,0.00,NULL,'','2026-10-08 12:24:12','2026-10-08 12:25:07');
/*!40000 ALTER TABLE `delivery_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_partners`
--

DROP TABLE IF EXISTS `delivery_partners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivery_partners` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` int unsigned NOT NULL DEFAULT '1',
  `branch_id` int unsigned NOT NULL DEFAULT '1',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_type` enum('bike','scooter','car','van') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bike',
  `vehicle_number` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `commission_rate` decimal(5,2) NOT NULL DEFAULT '15.00',
  `availability_status` enum('available','on_delivery','offline','on_break') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `rating` decimal(3,2) NOT NULL DEFAULT '5.00',
  `total_deliveries` int unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_dp_branch_avail` (`branch_id`,`availability_status`,`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_partners`
--

LOCK TABLES `delivery_partners` WRITE;
/*!40000 ALTER TABLE `delivery_partners` DISABLE KEYS */;
INSERT INTO `delivery_partners` VALUES (1,1,1,'Rajesh Kumar','+91 9811223344','rajesh.delivery@rms.local','bike','DL-01-AB-1234',15.00,'available',1,4.90,84,'2026-10-03 12:38:44','2026-10-03 12:38:44'),(2,1,1,'Amit Sharma','+91 9822334455','amit.rider@rms.local','scooter','DL-02-CD-5678',15.00,'on_delivery',1,4.75,62,'2026-10-03 12:38:44','2026-10-03 12:38:44'),(3,1,1,'Sunil Yadav','+91 9833445566','sunil.yadav@rms.local','bike','DL-03-EF-9012',15.00,'available',1,4.85,41,'2026-10-03 12:38:44','2026-10-03 12:38:44'),(4,1,1,'Rohan Verma','+91 9988776655','rohan.rider@rms.local','bike','DL-09-XY-4321',12.50,'available',1,5.00,0,'2026-10-03 12:52:02','2026-10-03 12:52:02'),(5,1,1,'subh','+91989876543','subha@gmail.com','bike','KL-70-23',15.00,'available',1,5.00,3,'2026-10-05 15:26:06','2026-10-08 12:25:07');
/*!40000 ALTER TABLE `delivery_partners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `equipment`
--

DROP TABLE IF EXISTS `equipment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `equipment` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` int unsigned NOT NULL DEFAULT '1',
  `branch_id` int unsigned NOT NULL DEFAULT '1',
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_number` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serial_number` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Main Kitchen',
  `purchase_date` date DEFAULT NULL,
  `warranty_expiry` date DEFAULT NULL,
  `cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` enum('operational','under_maintenance','broken','retired') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'operational',
  `last_serviced_date` date DEFAULT NULL,
  `next_service_due` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_eq_branch_status` (`branch_id`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `equipment`
--

LOCK TABLES `equipment` WRITE;
/*!40000 ALTER TABLE `equipment` DISABLE KEYS */;
INSERT INTO `equipment` VALUES (1,1,1,'Commercial Deck Pizza Oven','MKN-PO900','SN-OVEN-9921','Baking & Pizza Station','2024-03-15','2027-03-15',185000.00,'operational','2026-08-10','2026-11-10','2026-10-03 12:38:44','2026-10-03 12:38:44'),(2,1,1,'Blast Chiller & Deep Freezer 4-Door','FRIG-4D-PRO','SN-FRZ-4412','Prep & Cold Storage','2024-01-20','2027-01-20',145000.00,'operational','2026-07-05','2026-10-05','2026-10-03 12:38:44','2026-10-03 12:38:44'),(3,1,1,'La Marzocco Espresso Machine 2-Group','LM-LINEA-PB','SN-ESP-8819','Bar & Beverage Counter','2024-05-10','2026-05-10',320000.00,'operational','2026-09-01','2026-12-01','2026-10-03 12:38:44','2026-10-03 12:38:44'),(4,1,1,'Commercial 2-Tank Electric Deep Fryer','FRY-TANK-20L','SN-FRY-1102','Hot Kitchen Line','2024-04-12','2026-04-12',48000.00,'operational','2026-10-06','2027-01-06','2026-10-03 12:38:44','2026-10-06 18:20:12'),(5,1,1,'Undercounter 2-Door Beverage Chiller','CHILL-PRO-200','SN-BAR-8831','Front Bar & Beverage Station',NULL,NULL,62000.00,'operational','2026-10-03','2027-01-03','2026-10-03 12:52:03','2026-10-03 12:52:04');
/*!40000 ALTER TABLE `equipment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expense_categories`
--

DROP TABLE IF EXISTS `expense_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expense_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` int unsigned NOT NULL DEFAULT '1',
  `name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expense_categories`
--

LOCK TABLES `expense_categories` WRITE;
/*!40000 ALTER TABLE `expense_categories` DISABLE KEYS */;
INSERT INTO `expense_categories` VALUES (1,1,'Kitchen & Cooking Gas','Commercial LPG cylinders and fuel',1,'2026-10-03 12:38:44'),(2,1,'Cleaning & Sanitation Supplies','Detergents, sanitizers, mop pads, pest control',1,'2026-10-03 12:38:44'),(3,1,'Repairs & Equipment Maintenance','Oven servicing, HVAC repair, plumbing, fridge gas',1,'2026-10-03 12:38:44'),(4,1,'Staff Meals & Welfare','Staff duty food, water supply, tea/coffee',1,'2026-10-03 12:38:44'),(5,1,'Printing & Packaging Materials','Takeaway containers, thermal paper rolls, bags',1,'2026-10-03 12:38:44'),(6,1,'Utilities & Electricity','Municipal water, high-load commercial power',1,'2026-10-03 12:38:44');
/*!40000 ALTER TABLE `expense_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expenses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` int unsigned NOT NULL DEFAULT '1',
  `branch_id` int unsigned NOT NULL DEFAULT '1',
  `category_id` int unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `expense_date` date NOT NULL,
  `payment_method` enum('cash','bank_transfer','company_card','cheque') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `vendor_name` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_receipt_no` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approved',
  `recorded_by` int unsigned NOT NULL,
  `approved_by` int unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_exp_branch_date` (`branch_id`,`expense_date`),
  KEY `idx_exp_category` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expenses`
--

LOCK TABLES `expenses` WRITE;
/*!40000 ALTER TABLE `expenses` DISABLE KEYS */;
INSERT INTO `expenses` VALUES (1,1,1,1,2450.00,'2026-10-03','cash','Indane Commercial LPG Cylinders','GAS-OCT-2026-01',NULL,'approved',1,1,'2026-10-03 12:52:03','Two 19kg commercial cylinders refilled for main cooking range','2026-10-03 12:52:02','2026-10-03 12:52:03'),(2,1,1,2,5000.00,'2026-10-05','cash','','',NULL,'approved',1,1,'2026-10-05 15:27:37','','2026-10-05 15:27:37','2026-10-05 15:27:37'),(3,1,1,2,200.00,'2026-10-07','cash','mukesh','INV-2525',NULL,'approved',1,1,'2026-10-07 16:34:27','the expense regarding cleaning the whole kitchen','2026-10-07 16:34:27','2026-10-07 16:34:27');
/*!40000 ALTER TABLE `expenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `floors`
--

DROP TABLE IF EXISTS `floors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `floors` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` int unsigned NOT NULL DEFAULT '1',
  `branch_id` int unsigned DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `floor_number` int NOT NULL DEFAULT '1',
  `sort_order` smallint unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_floors_branch` (`branch_id`),
  KEY `idx_floors_restaurant` (`restaurant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `floors`
--

LOCK TABLES `floors` WRITE;
/*!40000 ALTER TABLE `floors` DISABLE KEYS */;
INSERT INTO `floors` VALUES (1,1,1,'Ground Floor Dining',1,1,1,'2026-10-01 18:00:47','2026-10-01 18:00:47'),(2,1,1,'Rooftop Terrace',2,2,1,'2026-10-01 18:00:47','2026-10-01 18:00:47'),(3,1,1,'VIP Private Lounge',3,3,1,'2026-10-01 18:00:47','2026-10-01 18:00:47');
/*!40000 ALTER TABLE `floors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `goods_receipt_items`
--

DROP TABLE IF EXISTS `goods_receipt_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `goods_receipt_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `grn_id` int unsigned NOT NULL,
  `inventory_item_id` int unsigned NOT NULL,
  `quantity_received` decimal(12,3) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(12,2) NOT NULL,
  `expiry_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_gri_grn` (`grn_id`),
  KEY `idx_gri_item` (`inventory_item_id`),
  CONSTRAINT `fk_gri_grn` FOREIGN KEY (`grn_id`) REFERENCES `goods_receipt_notes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_gri_item` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `goods_receipt_items`
--

LOCK TABLES `goods_receipt_items` WRITE;
/*!40000 ALTER TABLE `goods_receipt_items` DISABLE KEYS */;
INSERT INTO `goods_receipt_items` VALUES (1,1,9,20.000,4.50,90.00,NULL),(2,2,6,20.000,9.00,180.00,NULL),(3,2,4,10.000,6.00,60.00,NULL),(4,3,3,10.000,10.00,100.00,NULL);
/*!40000 ALTER TABLE `goods_receipt_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `goods_receipt_notes`
--

DROP TABLE IF EXISTS `goods_receipt_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `goods_receipt_notes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `grn_number` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `purchase_order_id` int unsigned DEFAULT NULL,
  `branch_id` int unsigned DEFAULT '1',
  `supplier_id` int unsigned NOT NULL,
  `invoice_no` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `received_date` date NOT NULL,
  `total_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `status` enum('received','verified','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'received',
  `received_by` int unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_grn_num` (`grn_number`),
  KEY `idx_grn_po` (`purchase_order_id`),
  KEY `idx_grn_supp` (`supplier_id`),
  CONSTRAINT `fk_grn_supp` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `goods_receipt_notes`
--

LOCK TABLES `goods_receipt_notes` WRITE;
/*!40000 ALTER TABLE `goods_receipt_notes` DISABLE KEYS */;
INSERT INTO `goods_receipt_notes` VALUES (1,'GRN-2026-0002',2,1,4,'INV-608434','2026-10-03',90.00,'verified',1,'Auto-verified from PO receipt','2026-10-03 11:40:32'),(2,'GRN-2026-0001',1,1,2,'INV-BF4361','2026-10-03',240.00,'verified',1,'Auto-verified from PO receipt','2026-10-03 13:25:40'),(3,'GRN-2026-0003',3,1,2,'INV-F99B24','2026-10-05',100.00,'verified',1,'Auto-verified from PO receipt','2026-10-05 17:25:19');
/*!40000 ALTER TABLE `goods_receipt_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_categories`
--

DROP TABLE IF EXISTS `inventory_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_categories` (
  `id` smallint unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` int unsigned NOT NULL DEFAULT '1',
  `name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_inv_cat_rest` (`restaurant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_categories`
--

LOCK TABLES `inventory_categories` WRITE;
/*!40000 ALTER TABLE `inventory_categories` DISABLE KEYS */;
INSERT INTO `inventory_categories` VALUES (1,1,'Dairy & Cheese','Milk, butter, mozzarella, paneer, cream',1),(2,1,'Produce & Vegetables','Fresh tomatoes, onions, lettuce, herbs',1),(3,1,'Meat & Poultry','Chicken, beef patties, bacon, pepperoni',1),(4,1,'Bakery & Grains','Burger buns, pizza flour, rice, pasta',1),(5,1,'Sauces & Spices','Olive oil, tomato puree, seasonings, dips',1),(6,1,'Beverages & Syrups','Soda syrups, coffee beans, tea leaves',1);
/*!40000 ALTER TABLE `inventory_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_items`
--

DROP TABLE IF EXISTS `inventory_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` int unsigned NOT NULL DEFAULT '1',
  `branch_id` int unsigned DEFAULT '1',
  `category_id` smallint unsigned DEFAULT NULL,
  `unit_id` smallint unsigned NOT NULL,
  `supplier_id` int unsigned DEFAULT NULL,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_stock` decimal(12,3) NOT NULL DEFAULT '0.000',
  `min_stock_level` decimal(12,3) NOT NULL DEFAULT '5.000',
  `ideal_stock_level` decimal(12,3) NOT NULL DEFAULT '25.000',
  `unit_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_inv_item_sku` (`branch_id`,`sku`),
  KEY `idx_inv_cat` (`category_id`),
  KEY `idx_inv_unit` (`unit_id`),
  KEY `idx_inv_supp` (`supplier_id`),
  KEY `idx_inv_branch_active` (`branch_id`,`is_active`),
  CONSTRAINT `fk_inv_cat` FOREIGN KEY (`category_id`) REFERENCES `inventory_categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_inv_supp` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_inv_unit` FOREIGN KEY (`unit_id`) REFERENCES `inventory_units` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_items`
--

LOCK TABLES `inventory_items` WRITE;
/*!40000 ALTER TABLE `inventory_items` DISABLE KEYS */;
INSERT INTO `inventory_items` VALUES (1,1,1,1,1,1,'Mozzarella Cheese Block','RAW-MOZZ-01',17.750,5.000,25.000,8.50,1,'2026-10-03 11:29:44','2026-10-07 13:08:19'),(2,1,1,2,1,1,'Roma Tomatoes (Fresh)','RAW-TOM-02',23.000,10.000,40.000,2.20,1,'2026-10-03 11:29:44','2026-10-07 13:08:19'),(3,1,1,4,1,3,'Italian 00 Pizza Flour','RAW-FLR-03',53.750,15.000,60.000,1.80,1,'2026-10-03 11:29:44','2026-10-07 13:08:19'),(4,1,1,3,1,2,'Chicken Breast Fillets','RAW-CHK-04',23.500,8.000,30.000,6.50,1,'2026-10-03 11:29:44','2026-10-06 13:23:39'),(5,1,1,4,5,3,'Brioche Burger Buns','RAW-BUN-05',80.000,20.000,100.000,0.45,1,'2026-10-03 11:29:44','2026-10-03 11:29:44'),(6,1,1,3,1,2,'Angus Beef Patties (180g)','RAW-BEEF-06',23.500,10.000,35.000,9.00,1,'2026-10-03 11:29:44','2026-10-05 17:25:55'),(7,1,1,5,3,1,'Extra Virgin Olive Oil','RAW-OIL-07',12.000,4.000,20.000,11.00,1,'2026-10-03 11:29:44','2026-10-03 11:29:44'),(8,1,1,6,1,1,'Espresso Whole Beans','RAW-COF-08',9.500,3.000,15.000,14.50,1,'2026-10-03 11:29:44','2026-10-03 11:29:44'),(9,1,1,1,3,4,'Sweet Whipping Cream (35%)','RAW-CRM-99',0.000,5.000,25.000,4.50,1,'2026-10-03 11:40:31','2026-10-08 11:36:20'),(10,1,1,3,6,NULL,'frozen chicken','RAW-FROZ-91',15.000,5.000,25.000,200.00,1,'2026-10-03 14:46:44','2026-10-05 17:24:43'),(11,1,3,3,1,2,'chicken ','RAW-CHIC-20',10.000,5.000,25.000,150.00,1,'2026-10-07 12:53:25','2026-10-07 12:53:25'),(12,1,3,3,1,2,'Mutton','RAW-MUTT-94',10.000,5.000,25.000,700.00,1,'2026-10-07 12:54:01','2026-10-07 12:54:01');
/*!40000 ALTER TABLE `inventory_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_units`
--

DROP TABLE IF EXISTS `inventory_units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_units` (
  `id` smallint unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` int unsigned NOT NULL DEFAULT '1',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_code` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_inv_unit_rest` (`restaurant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_units`
--

LOCK TABLES `inventory_units` WRITE;
/*!40000 ALTER TABLE `inventory_units` DISABLE KEYS */;
INSERT INTO `inventory_units` VALUES (1,1,'Kilograms','kg',1),(2,1,'Grams','g',1),(3,1,'Liters','L',1),(4,1,'Milliliters','ml',1),(5,1,'Pieces','pcs',1),(6,1,'Packets','pkt',1),(7,1,'Cans / Bottles','can',1);
/*!40000 ALTER TABLE `inventory_units` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item_recipes`
--

DROP TABLE IF EXISTS `item_recipes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `item_recipes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `menu_item_id` int unsigned NOT NULL,
  `inventory_item_id` int unsigned NOT NULL,
  `quantity_required` decimal(10,3) NOT NULL,
  `unit_id` smallint unsigned NOT NULL,
  `unit_cost` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_recipe_link` (`menu_item_id`,`inventory_item_id`),
  KEY `idx_recipe_menu` (`menu_item_id`),
  KEY `idx_recipe_inv` (`inventory_item_id`),
  KEY `fk_rec_unit` (`unit_id`),
  CONSTRAINT `fk_rec_inv` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_rec_menu` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_rec_unit` FOREIGN KEY (`unit_id`) REFERENCES `inventory_units` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_recipes`
--

LOCK TABLES `item_recipes` WRITE;
/*!40000 ALTER TABLE `item_recipes` DISABLE KEYS */;
INSERT INTO `item_recipes` VALUES (1,1,3,0.250,1,NULL),(2,1,1,0.150,1,NULL),(3,1,2,0.200,1,NULL),(4,1,9,0.050,3,NULL),(5,16,9,10.000,1,NULL);
/*!40000 ALTER TABLE `item_recipes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `journal_entries`
--

DROP TABLE IF EXISTS `journal_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `journal_entries` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` int unsigned NOT NULL DEFAULT '1',
  `branch_id` int unsigned NOT NULL DEFAULT '1',
  `account_id` int unsigned NOT NULL,
  `entry_date` date NOT NULL,
  `reference_type` enum('order_sale','purchase_order','operating_expense','tax_payment','manual') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `reference_id` int unsigned DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `debit` decimal(12,2) NOT NULL DEFAULT '0.00',
  `credit` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_by` int unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_je_branch_date` (`branch_id`,`entry_date`),
  KEY `idx_je_account` (`account_id`),
  KEY `idx_je_ref` (`reference_type`,`reference_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `journal_entries`
--

LOCK TABLES `journal_entries` WRITE;
/*!40000 ALTER TABLE `journal_entries` DISABLE KEYS */;
/*!40000 ALTER TABLE `journal_entries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kitchen_stations`
--

DROP TABLE IF EXISTS `kitchen_stations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kitchen_stations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` int unsigned NOT NULL DEFAULT '1',
  `branch_id` int unsigned DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color_hex` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#3b82f6',
  `sort_order` smallint unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ks_branch` (`branch_id`),
  KEY `idx_ks_code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kitchen_stations`
--

LOCK TABLES `kitchen_stations` WRITE;
/*!40000 ALTER TABLE `kitchen_stations` DISABLE KEYS */;
INSERT INTO `kitchen_stations` VALUES (1,1,1,'Main Hot Kitchen','HOT_KITCHEN','#ef4444',1,1,'2026-10-01 18:16:50','2026-10-01 18:16:50'),(2,1,1,'Tandoor & Charcoal Grill','TANDOOR','#f97316',2,1,'2026-10-01 18:16:50','2026-10-01 18:16:50'),(3,1,1,'Bar & Mocktails','BAR','#06b6d4',3,1,'2026-10-01 18:16:50','2026-10-01 18:16:50'),(4,1,1,'Desserts & Bakery','BAKERY','#ec4899',4,1,'2026-10-01 18:16:50','2026-10-01 18:16:50');
/*!40000 ALTER TABLE `kitchen_stations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kots`
--

DROP TABLE IF EXISTS `kots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kots` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `branch_id` int unsigned DEFAULT NULL,
  `kot_number` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('sent','preparing','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sent',
  `station` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'kitchen',
  `printed_at` datetime DEFAULT NULL,
  `cooking_started_at` datetime DEFAULT NULL,
  `ready_at` datetime DEFAULT NULL,
  `bumped_by` int unsigned DEFAULT NULL,
  `reprint_count` tinyint unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_kots_order` (`order_id`),
  KEY `idx_kots_branch_status` (`branch_id`,`status`),
  KEY `idx_kots_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kots`
--

LOCK TABLES `kots` WRITE;
/*!40000 ALTER TABLE `kots` DISABLE KEYS */;
INSERT INTO `kots` VALUES (1,1,1,'KOT-180831-001','completed','kitchen','2026-10-01 18:18:43','2026-10-01 18:19:28','2026-10-01 18:19:29',1,1,'2026-10-01 18:08:31','2026-10-01 18:19:29'),(2,2,1,'KOT-115333-001','completed','kitchen','2026-10-03 11:53:33','2026-10-03 13:05:46','2026-10-03 13:08:31',1,0,'2026-10-03 11:53:33','2026-10-03 13:08:31'),(3,3,1,'KOT-122003-002','completed','kitchen','2026-10-03 12:20:03','2026-10-03 15:10:58','2026-10-03 15:11:00',1,0,'2026-10-03 12:20:03','2026-10-03 15:11:00'),(4,4,1,'KOT-122130-003','completed','kitchen','2026-10-03 12:21:30','2026-10-05 12:27:55','2026-10-05 17:23:14',1,0,'2026-10-03 12:21:30','2026-10-05 17:23:14'),(5,4,1,'KOT-122135-004','completed','kitchen','2026-10-03 12:21:35','2026-10-03 14:28:40','2026-10-05 17:23:16',1,0,'2026-10-03 12:21:35','2026-10-05 17:23:16'),(6,4,1,'KOT-122144-005','completed','kitchen','2026-10-03 12:21:44','2026-10-05 12:28:04','2026-10-05 17:23:19',1,0,'2026-10-03 12:21:44','2026-10-05 17:23:19'),(7,4,1,'KOT-122216-006','completed','kitchen','2026-10-03 12:22:16','2026-10-03 14:28:34','2026-10-05 12:28:15',5,0,'2026-10-03 12:22:16','2026-10-05 12:28:15'),(8,5,1,'KOT-130755-007','completed','kitchen','2026-10-03 13:07:55','2026-10-03 13:08:06','2026-10-03 13:08:53',1,0,'2026-10-03 13:07:55','2026-10-03 13:08:53'),(9,6,1,'KOT-142803-008','completed','kitchen','2026-10-03 14:28:03','2026-10-05 12:28:17','2026-10-05 17:23:17',1,0,'2026-10-03 14:28:03','2026-10-05 17:23:17'),(10,7,1,'KOT-145325-009','completed','kitchen','2026-10-03 14:53:25','2026-10-05 12:28:23','2026-10-05 17:23:18',1,0,'2026-10-03 14:53:25','2026-10-05 17:23:18'),(11,8,1,'KOT-123051-001','completed','kitchen','2026-10-05 12:30:51','2026-10-05 17:23:28','2026-10-05 17:23:30',1,0,'2026-10-05 12:30:51','2026-10-05 17:23:30'),(12,11,1,'KOT-140648-002','completed','kitchen','2026-10-05 14:06:48','2026-10-05 14:07:07','2026-10-05 14:07:09',1,0,'2026-10-05 14:06:48','2026-10-05 14:07:09'),(13,13,1,'KOT-172148-003','completed','kitchen','2026-10-05 17:21:48','2026-10-05 17:23:23','2026-10-05 17:23:29',1,0,'2026-10-05 17:21:48','2026-10-05 17:23:29'),(14,14,1,'KOT-172947-004','completed','kitchen','2026-10-05 17:29:47','2026-10-05 17:30:17','2026-10-05 17:30:18',1,0,'2026-10-05 17:29:47','2026-10-05 17:30:18'),(15,15,1,'KOT-111305-001','completed','kitchen','2026-10-06 11:13:05','2026-10-06 11:13:14','2026-10-06 11:13:23',1,0,'2026-10-06 11:13:05','2026-10-06 11:13:23'),(16,16,1,'KOT-123912-002','completed','kitchen','2026-10-06 12:39:12','2026-10-06 12:39:38','2026-10-06 12:39:49',1,0,'2026-10-06 12:39:12','2026-10-06 12:39:49'),(17,17,1,'KOT-125206-003','completed','kitchen','2026-10-06 12:52:06','2026-10-06 12:53:16','2026-10-06 12:53:20',5,0,'2026-10-06 12:52:06','2026-10-06 12:53:20'),(18,18,1,'KOT-141733-004','completed','kitchen','2026-10-06 14:17:33','2026-10-06 14:17:51','2026-10-06 14:17:56',1,0,'2026-10-06 14:17:33','2026-10-06 14:17:56'),(19,19,1,'KOT-142504-005','completed','kitchen','2026-10-06 14:25:04','2026-10-06 15:16:17','2026-10-06 15:16:27',1,0,'2026-10-06 14:25:04','2026-10-06 15:16:27'),(20,20,1,'KOT-145644-006','completed','kitchen','2026-10-06 14:56:44','2026-10-06 15:16:14','2026-10-06 15:16:26',1,0,'2026-10-06 14:56:44','2026-10-06 15:16:26'),(21,21,1,'KOT-153008-007','completed','kitchen','2026-10-06 15:30:08','2026-10-06 15:30:25','2026-10-06 15:30:28',1,0,'2026-10-06 15:30:08','2026-10-06 15:30:28'),(22,22,1,'KOT-105227-001','completed','kitchen','2026-10-07 10:52:27','2026-10-07 11:29:32','2026-10-07 11:29:38',2,0,'2026-10-07 10:52:27','2026-10-07 11:29:38'),(23,23,1,'KOT-110335-002','completed','kitchen','2026-10-07 11:03:35','2026-10-07 11:29:37','2026-10-07 11:29:41',2,0,'2026-10-07 11:03:35','2026-10-07 11:29:41'),(24,24,1,'KOT-113749-003','completed','kitchen','2026-10-07 11:37:49','2026-10-07 12:14:37','2026-10-07 12:14:41',1,0,'2026-10-07 11:37:49','2026-10-07 12:14:41'),(25,25,1,'KOT-122349-004','completed','kitchen','2026-10-07 12:23:49','2026-10-07 12:23:57','2026-10-07 12:24:01',1,0,'2026-10-07 12:23:49','2026-10-07 12:24:01'),(26,26,1,'KOT-130819-005','completed','kitchen','2026-10-07 13:08:19','2026-10-07 14:49:15','2026-10-07 14:49:16',10,0,'2026-10-07 13:08:19','2026-10-07 14:49:16'),(27,27,1,'KOT-151938-006','completed','kitchen','2026-10-07 15:19:38','2026-10-07 15:19:46','2026-10-07 15:19:47',10,0,'2026-10-07 15:19:38','2026-10-07 15:19:47'),(28,28,1,'KOT-152745-007','completed','kitchen','2026-10-07 15:27:45','2026-10-07 15:56:58','2026-10-07 15:57:24',1,0,'2026-10-07 15:27:45','2026-10-07 15:57:24'),(29,29,1,'KOT-162727-008','completed','kitchen','2026-10-07 16:27:27','2026-10-07 16:28:08','2026-10-07 16:28:10',1,0,'2026-10-07 16:27:27','2026-10-07 16:28:10'),(30,30,1,'KOT-165655-009','completed','kitchen','2026-10-07 16:56:55','2026-10-07 16:58:54','2026-10-07 16:59:02',1,0,'2026-10-07 16:56:55','2026-10-07 16:59:02'),(31,31,1,'KOT-103802-001','completed','kitchen','2026-10-08 10:38:02','2026-10-08 10:39:33','2026-10-08 10:39:34',5,0,'2026-10-08 10:38:02','2026-10-08 10:39:34'),(32,32,1,'KOT-104535-002','completed','kitchen','2026-10-08 10:45:35','2026-10-08 10:45:42','2026-10-08 10:46:17',1,0,'2026-10-08 10:45:35','2026-10-08 10:46:17'),(33,33,1,'KOT-110241-003','completed','kitchen','2026-10-08 11:02:41','2026-10-08 11:15:41','2026-10-08 11:15:55',1,0,'2026-10-08 11:02:41','2026-10-08 11:15:55'),(34,34,1,'KOT-112121-004','completed','kitchen','2026-10-08 11:21:21','2026-10-08 11:41:19','2026-10-08 11:41:22',1,0,'2026-10-08 11:21:21','2026-10-08 11:41:22'),(35,35,1,'KOT-113620-005','completed','kitchen','2026-10-08 11:36:20','2026-10-08 11:41:20','2026-10-08 11:41:23',1,0,'2026-10-08 11:36:20','2026-10-08 11:41:23'),(36,36,1,'KOT-115608-006','completed','kitchen','2026-10-08 11:56:08','2026-10-08 14:23:21','2026-10-08 14:23:33',2,0,'2026-10-08 11:56:08','2026-10-08 14:23:33'),(37,37,1,'KOT-122123-007','completed','kitchen','2026-10-08 12:21:23','2026-10-08 14:23:25','2026-10-08 14:23:35',2,0,'2026-10-08 12:21:23','2026-10-08 14:23:35');
/*!40000 ALTER TABLE `kots` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `loyalty_transactions`
--

DROP TABLE IF EXISTS `loyalty_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `loyalty_transactions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int unsigned NOT NULL,
  `order_id` int unsigned DEFAULT NULL,
  `points_earned` int NOT NULL DEFAULT '0',
  `points_redeemed` int NOT NULL DEFAULT '0',
  `balance_after` int NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_lt_customer` (`customer_id`),
  KEY `idx_lt_order` (`order_id`),
  CONSTRAINT `fk_lt_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loyalty_transactions`
--

LOCK TABLES `loyalty_transactions` WRITE;
/*!40000 ALTER TABLE `loyalty_transactions` DISABLE KEYS */;
INSERT INTO `loyalty_transactions` VALUES (1,1,NULL,150,0,150,'Welcome bonus & Dine-in bill ORD-000001','2026-10-03 11:29:44'),(2,1,NULL,200,0,350,'Celebration dinner order settlement ORD-000005','2026-10-03 11:29:44'),(3,2,NULL,120,0,120,'Weekend family lunch order ORD-000008','2026-10-03 11:29:44'),(4,4,NULL,50,0,150,'Anniversary celebration bonus dining visit','2026-10-03 11:40:34'),(5,1,2,2,0,352,'Dining reward for Order #ORD-20261003-0001','2026-10-03 11:53:33'),(6,5,NULL,50,0,100,'good ambience','2026-10-05 14:12:27'),(7,2,NULL,150,0,270,'the birthday gift was really good at your side soo that we are really happy visit again and again ','2026-10-07 12:44:47'),(8,1,NULL,250,0,602,'thank you ','2026-10-07 12:45:19'),(9,1,NULL,0,150,452,'lll','2026-10-07 12:46:57'),(10,1,NULL,0,150,302,'thank you ','2026-10-07 14:53:06'),(11,1,NULL,34,0,336,'thank you','2026-10-07 14:53:51'),(12,8,28,58,0,58,'Dining reward for Order #ORD-20261007-0007','2026-10-07 15:28:51'),(13,7,27,24,0,74,'Dining reward for Order #ORD-20261007-0006','2026-10-07 15:31:21'),(14,9,29,92,0,92,'Dining reward for Order #ORD-20261007-0008','2026-10-07 16:28:42'),(15,10,31,46,0,46,'Dining reward for Order #ORD-20261008-0001','2026-10-08 10:40:24'),(16,11,33,29,0,29,'Dining reward for Order #ORD-20261008-0003','2026-10-08 11:03:57'),(17,11,33,29,0,58,'Dining reward for Order #ORD-20261008-0003','2026-10-08 11:19:37'),(18,11,33,29,0,87,'Dining reward for Order #ORD-20261008-0003','2026-10-08 11:19:46'),(19,12,34,163,0,163,'Dining reward for Order #ORD-20261008-0004','2026-10-08 11:44:09');
/*!40000 ALTER TABLE `loyalty_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `maintenance_requests`
--

DROP TABLE IF EXISTS `maintenance_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `maintenance_requests` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `equipment_id` int unsigned NOT NULL,
  `branch_id` int unsigned NOT NULL DEFAULT '1',
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority` enum('low','medium','high','emergency') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `reported_by` int unsigned NOT NULL,
  `assigned_to_vendor` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` enum('reported','in_progress','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'reported',
  `reported_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `completed_at` datetime DEFAULT NULL,
  `resolution_notes` text COLLATE utf8mb4_unicode_ci,
  `invoice_number` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_mr_equipment` (`equipment_id`),
  KEY `idx_mr_branch_status` (`branch_id`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `maintenance_requests`
--

LOCK TABLES `maintenance_requests` WRITE;
/*!40000 ALTER TABLE `maintenance_requests` DISABLE KEYS */;
INSERT INTO `maintenance_requests` VALUES (1,4,1,'Thermostat sensor tripping on Tank 2','high','Right tank overheating past 190C and shutting off safety relay.',1,'CoolTech Commercial Kitchen Repairs',3000.00,'completed','2026-10-01 10:30:00','2026-10-06 18:20:12','fix it now it is working properly \r\n','','2026-10-03 12:38:44','2026-10-06 18:20:12'),(2,5,1,'Condenser fan motor making vibration noise','medium','Noticeable hum from lower compressor compartment during peak cooling cycles.',1,'Apex Cool Refrigeration Services',1850.00,'completed','2026-10-03 12:52:04','2026-10-03 12:52:04','Lubricated fan bearing mount and re-torqued vibration dampers. Sound levels normalized.','INV-ACR-5541','2026-10-03 12:52:04','2026-10-03 12:52:04');
/*!40000 ALTER TABLE `maintenance_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_categories`
--

DROP TABLE IF EXISTS `menu_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` int unsigned NOT NULL DEFAULT '1',
  `branch_id` int unsigned DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 0xF09F8DBDEFB88F,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` smallint unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cats_branch` (`branch_id`),
  KEY `idx_cats_slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_categories`
--

LOCK TABLES `menu_categories` WRITE;
/*!40000 ALTER TABLE `menu_categories` DISABLE KEYS */;
INSERT INTO `menu_categories` VALUES (1,1,NULL,'Starters & Appetizers','starters','🥗','Crispy appetizers, kebabs, and soups',1,1,'2026-10-01 18:00:47','2026-10-01 18:00:47'),(2,1,NULL,'Main Course','main-course','🍛','Rich curries, biryanis, and chef specials',2,1,'2026-10-01 18:00:47','2026-10-01 18:00:47'),(3,1,NULL,'Tandoor & Breads','breads','🫓','Freshly baked naan, roti, and parathas',3,1,'2026-10-01 18:00:47','2026-10-01 18:00:47'),(4,1,NULL,'Beverages & Mocktails','beverages','🍹','Artisanal coolers, shakes, and mocktails',4,1,'2026-10-01 18:00:47','2026-10-01 18:00:47'),(5,1,NULL,'Desserts & Sweets','desserts','🍨','Decadent sweets and gourmet ice creams',5,1,'2026-10-01 18:00:47','2026-10-01 18:00:47'),(6,1,1,'Biriyani','biriyani','🍲','the best dish of all time ',0,1,'2026-10-06 12:49:41','2026-10-06 12:49:41'),(7,1,1,'Chinese','chinese','🍜','the best chinese food make here \r\n',0,1,'2026-10-07 15:41:18','2026-10-07 15:43:02'),(8,1,1,'Pasta','pasta','🍝','',0,1,'2026-10-08 13:00:17','2026-10-08 13:00:17');
/*!40000 ALTER TABLE `menu_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_items`
--

DROP TABLE IF EXISTS `menu_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` int unsigned NOT NULL DEFAULT '1',
  `branch_id` int unsigned DEFAULT NULL,
  `category_id` int unsigned NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax_rate_id` int unsigned DEFAULT NULL,
  `is_veg` tinyint(1) NOT NULL DEFAULT '1',
  `preparation_time` smallint unsigned NOT NULL DEFAULT '15',
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` smallint unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_items_cat` (`category_id`),
  KEY `idx_items_code` (`code`),
  KEY `idx_items_available` (`is_available`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_items`
--

LOCK TABLES `menu_items` WRITE;
/*!40000 ALTER TABLE `menu_items` DISABLE KEYS */;
INSERT INTO `menu_items` VALUES (1,1,NULL,1,'Paneer Tikka Angaare','APP-01','Cottage cheese marinated in spices, roasted in clay oven',320.00,1,1,15,1,1,0,'2026-10-01 18:00:47','2026-10-07 17:34:11'),(2,1,NULL,1,'Crispy Corn & Water Chestnut','APP-02','Golden fried corn tossed with peppers and scallions',280.00,1,1,12,1,1,0,'2026-10-01 18:00:47','2026-10-01 18:00:47'),(3,1,NULL,1,'Chicken Malai Tikka','APP-03','Tender chicken morsels in creamy cardamom marinade',380.00,1,0,18,1,1,0,'2026-10-01 18:00:47','2026-10-01 18:00:47'),(4,1,NULL,1,'Tandoori Prawns Zaffrani','APP-04','Jumbo prawns infused with saffron and mustard',520.00,1,0,20,1,0,0,'2026-10-01 18:00:47','2026-10-05 17:48:25'),(5,1,NULL,2,'Dal Makhani Signature','MC-01','Slow cooked black lentils simmered overnight with butter',340.00,1,1,10,1,1,0,'2026-10-01 18:00:47','2026-10-01 18:00:47'),(6,1,NULL,2,'Paneer Butter Masala','MC-02','Cottage cheese cubes in rich tomato cashew gravy',360.00,1,1,15,1,1,0,'2026-10-01 18:00:47','2026-10-01 18:00:47'),(7,1,NULL,2,'Butter Chicken Dhabawala','MC-03','Classic shredded roasted chicken in silky velvety gravy',440.00,1,0,15,1,1,0,'2026-10-01 18:00:47','2026-10-06 14:09:33'),(8,1,NULL,2,'Dum Gosht Biryani','MC-04','Fragrant basmati rice layered with spiced mutton dum cooked',540.00,1,0,20,1,1,0,'2026-10-01 18:00:47','2026-10-01 18:00:47'),(9,1,NULL,2,'Hyderabadi Veg Biryani','MC-05','Spiced garden vegetables layered with saffron rice',360.00,1,1,15,1,1,0,'2026-10-01 18:00:47','2026-10-01 18:00:47'),(10,1,NULL,3,'Butter Garlic Naan','BRD-01','Clay oven leavened bread brushed with garlic & butter',85.00,1,1,5,1,1,0,'2026-10-01 18:00:47','2026-10-01 18:00:47'),(11,1,NULL,3,'Laccha Paratha','BRD-02','Crisp layered whole wheat flatbread',75.00,1,1,5,1,1,0,'2026-10-01 18:00:47','2026-10-01 18:00:47'),(12,1,NULL,3,'Tandoori Roti','BRD-03','Traditional whole wheat flatbread from the tandoor',45.00,1,1,5,1,1,0,'2026-10-01 18:00:47','2026-10-01 18:00:47'),(13,1,NULL,4,'Virgin Mojito Blue Lagoon','BEV-01','Fresh mint, lime, curacao syrup topped with fizz',180.00,3,1,5,1,1,0,'2026-10-01 18:00:47','2026-10-01 18:00:47'),(14,1,NULL,4,'Mango Kesar Lassi','BEV-02','Rich churned yogurt with Alphonso mango pulp',150.00,3,1,5,1,1,0,'2026-10-01 18:00:47','2026-10-01 18:00:47'),(15,1,NULL,4,'Masala Chai Artisan','BEV-03','Slow brewed tea with ginger, cardamom, and whole spices',90.00,3,1,5,1,1,0,'2026-10-01 18:00:47','2026-10-01 18:00:47'),(16,1,NULL,5,'Gulab Jamun Flambé','DES-01','Warm cottage cheese dumplings infused with rose syrup',180.00,1,1,5,1,1,0,'2026-10-01 18:00:47','2026-10-01 18:00:47'),(17,1,NULL,5,'Kesar Pista Kulfi','DES-02','Traditional slow reduced milk ice cream on sticks',160.00,1,1,5,1,1,0,'2026-10-01 18:00:47','2026-10-01 18:00:47'),(18,1,NULL,5,'Sizzling Brownie with Ice Cream','DES-03','Hot walnut brownie with vanilla ice cream & hot fudge',240.00,1,1,10,1,1,0,'2026-10-01 18:00:47','2026-10-01 18:00:47'),(19,1,1,2,'Mushroom curry','MC-01','Rich creamy gravy cooked slow with desi masalas.',250.00,NULL,1,15,1,1,0,'2026-10-05 17:46:34','2026-10-05 17:47:41'),(20,1,1,2,'chicken bharta','MAI-07','chicken bharta is one the best dish with rich taste and cream and everything is good',250.00,NULL,0,25,1,1,0,'2026-10-06 12:35:00','2026-10-06 12:35:00'),(21,1,1,3,'Tawa roti','TAN-04',NULL,7.00,NULL,1,15,1,1,0,'2026-10-06 12:35:24','2026-10-06 12:35:24'),(22,1,1,3,'rumali roti','TAN-05','soft, taste ,thin roti',10.00,NULL,1,5,1,1,0,'2026-10-06 12:36:28','2026-10-06 12:36:28'),(23,1,1,2,'Mutton kasha','MAI-08','the most loved dish in bengal',350.00,NULL,0,35,1,1,0,'2026-10-06 12:37:30','2026-10-06 12:37:55'),(24,1,1,6,'Chicken biryani','BIR-01',NULL,179.00,NULL,0,30,1,1,0,'2026-10-06 12:50:20','2026-10-06 12:50:20'),(25,1,1,6,'Mutton biriyani','BIR-02',NULL,300.00,NULL,0,45,1,1,0,'2026-10-06 12:51:00','2026-10-06 12:51:00'),(26,1,3,1,'chicken kabiraji','STA-05','the delicious chicken fried',150.00,NULL,0,24,1,1,0,'2026-10-07 12:52:06','2026-10-07 12:52:06'),(27,1,1,1,'reshmi kabab','STA-06',NULL,125.00,NULL,0,10,1,1,0,'2026-10-07 15:44:33','2026-10-07 15:44:54'),(28,1,1,7,'Chili chicken','CHI-01',NULL,200.00,NULL,0,15,1,1,0,'2026-10-07 16:52:09','2026-10-07 16:52:09'),(29,1,1,7,'kum pao chicken','CHI-02',NULL,250.00,NULL,0,15,1,1,0,'2026-10-07 16:52:44','2026-10-07 16:52:44'),(30,1,1,6,'Special chicken biryani','BIR-03',NULL,450.00,NULL,0,35,1,1,0,'2026-10-08 12:58:55','2026-10-08 12:58:55'),(31,1,1,8,'white sauce pasta','PAS-01',NULL,200.00,NULL,0,20,1,1,0,'2026-10-08 13:01:03','2026-10-08 13:01:03');
/*!40000 ALTER TABLE `menu_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` int unsigned NOT NULL,
  `branch_id` int unsigned DEFAULT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `type` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci,
  `data` json DEFAULT NULL,
  `read_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_notif_user` (`user_id`),
  KEY `idx_notif_branch` (`branch_id`),
  KEY `idx_notif_type` (`type`),
  KEY `idx_notif_read` (`read_at`),
  KEY `fk_notif_restaurant` (`restaurant_id`),
  CONSTRAINT `fk_notif_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=149 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,1,1,NULL,'order','Your Takeaway Order is Ready!','Hello Priya, your order #ORD-20261003-0005 is fresh and packed ready for pickup at our counter.','{\"log_id\": 5}','2026-10-03 13:14:45','2026-10-03 13:14:45'),(2,1,1,NULL,'announcement','Evening Shift Floor Briefing at 4:45 PM','All floor captains and servers please assemble near Counter 1 for briefing on VIP banquet booking.','{\"log_id\": 6}','2026-10-03 13:14:45','2026-10-03 13:14:45'),(3,1,1,NULL,'order','Your Takeaway Order is Ready!','Hello Priya, your order #ORD-20261003-0005 is fresh and packed ready for pickup at our counter.','{\"log_id\": 7}','2026-10-03 13:16:25','2026-10-03 13:16:25'),(4,1,1,NULL,'announcement','Evening Shift Floor Briefing at 4:45 PM','All floor captains and servers please assemble near Counter 1 for briefing on VIP banquet booking.','{\"log_id\": 8}','2026-10-03 13:16:25','2026-10-03 13:16:25'),(5,1,1,NULL,'order','order-1','you oordeer is ready please be ready to get it','{\"log_id\": 10}','2026-10-05 18:38:52','2026-10-05 13:19:29'),(6,1,1,NULL,'promo','alll stafff','today is big event soo be prepare all please ready for all shift','{\"log_id\": 11}','2026-10-06 10:51:18','2026-10-06 10:42:27'),(7,1,1,NULL,'order','New Order #ORD-20261006-0001','New order placed (KOT: KOT-111305-001)','{\"order_id\": 15, \"order_number\": \"ORD-20261006-0001\"}','2026-10-06 12:46:14','2026-10-06 11:13:05'),(8,1,1,NULL,'kitchen','Kitchen: KOT-111305-001 Ready!','Order items are cooked and ready to be served.','{\"kot_id\": 15, \"kot_number\": \"KOT-111305-001\"}','2026-10-06 11:44:26','2026-10-06 11:13:23'),(9,1,1,NULL,'order_status','Order #ORD-20261006-0001 Served','Order status changed from ready to served.','{\"status\": \"served\", \"order_id\": 15, \"order_number\": \"ORD-20261006-0001\"}','2026-10-06 11:44:25','2026-10-06 11:13:48'),(10,1,1,NULL,'order_status','Order #ORD-20261006-0001 Completed','Order status changed from served to completed.','{\"status\": \"completed\", \"order_id\": 15, \"order_number\": \"ORD-20261006-0001\"}','2026-10-06 11:44:23','2026-10-06 11:13:55'),(11,1,1,NULL,'order_status','Order #ORD-20261003-0003 Served','Order status changed from ready to served.','{\"status\": \"served\", \"order_id\": 4, \"order_number\": \"ORD-20261003-0003\"}','2026-10-06 12:46:14','2026-10-06 12:26:11'),(12,1,1,NULL,'order','New Order #ORD-20261006-0002','New order placed (KOT: KOT-123912-002)','{\"order_id\": 16, \"order_number\": \"ORD-20261006-0002\"}','2026-10-06 12:46:14','2026-10-06 12:39:12'),(13,1,1,NULL,'kitchen','Kitchen: KOT-123912-002 Ready!','Order items are cooked and ready to be served.','{\"kot_id\": 16, \"kot_number\": \"KOT-123912-002\"}','2026-10-06 12:46:14','2026-10-06 12:39:49'),(14,1,1,NULL,'order_status','Order #ORD-20261006-0002 Served','Order status changed from ready to served.','{\"status\": \"served\", \"order_id\": 16, \"order_number\": \"ORD-20261006-0002\"}','2026-10-06 12:46:14','2026-10-06 12:40:01'),(15,1,1,NULL,'order_status','Order #ORD-20261006-0002 Completed','Order status changed from served to completed.','{\"status\": \"completed\", \"order_id\": 16, \"order_number\": \"ORD-20261006-0002\"}','2026-10-06 12:46:14','2026-10-06 12:40:10'),(16,1,1,NULL,'order','New Order #ORD-20261006-0003','New order placed (KOT: KOT-125206-003)','{\"order_id\": 17, \"order_number\": \"ORD-20261006-0003\"}','2026-10-06 12:55:15','2026-10-06 12:52:06'),(17,1,1,NULL,'kitchen','Kitchen: KOT-125206-003 Ready!','Order items are cooked and ready to be served.','{\"kot_id\": 17, \"kot_number\": \"KOT-125206-003\"}','2026-10-06 12:55:15','2026-10-06 12:53:20'),(18,1,1,NULL,'order_status','Order #ORD-20261006-0003 Served','Order status changed from ready to served.','{\"status\": \"served\", \"order_id\": 17, \"order_number\": \"ORD-20261006-0003\"}','2026-10-06 12:55:15','2026-10-06 12:54:15'),(19,1,1,NULL,'order_status','Order #ORD-20261006-0003 Completed','Order status changed from served to completed.','{\"status\": \"completed\", \"order_id\": 17, \"order_number\": \"ORD-20261006-0003\"}','2026-10-06 12:55:15','2026-10-06 12:54:33'),(20,1,1,NULL,'order_status','Order #ORD-20261001-0001 Served','Order status changed from ready to served.','{\"status\": \"served\", \"order_id\": 1, \"order_number\": \"ORD-20261001-0001\"}','2026-10-06 13:16:53','2026-10-06 13:16:48'),(21,1,1,NULL,'order','New Order #ORD-20261006-0004','New order placed (KOT: KOT-141733-004)','{\"order_id\": 18, \"order_number\": \"ORD-20261006-0004\"}','2026-10-06 14:53:01','2026-10-06 14:17:33'),(22,1,1,NULL,'kitchen','Kitchen: KOT-141733-004 Ready!','Order items are cooked and ready to be served.','{\"kot_id\": 18, \"kot_number\": \"KOT-141733-004\"}','2026-10-06 14:53:01','2026-10-06 14:17:56'),(23,1,1,NULL,'order_status','Order #ORD-20261006-0004 Served','Order status changed from ready to served.','{\"status\": \"served\", \"order_id\": 18, \"order_number\": \"ORD-20261006-0004\"}','2026-10-06 14:53:01','2026-10-06 14:18:07'),(24,1,1,NULL,'order','New Order #ORD-20261006-0005','New order placed (KOT: KOT-142504-005)','{\"order_id\": 19, \"order_number\": \"ORD-20261006-0005\"}','2026-10-06 14:53:01','2026-10-06 14:25:04'),(25,1,1,NULL,'order_status','Order #ORD-20261006-0005 Preparing','Order status changed from confirmed to preparing.','{\"status\": \"preparing\", \"order_id\": 19, \"order_number\": \"ORD-20261006-0005\"}','2026-10-06 14:53:01','2026-10-06 14:25:13'),(26,1,1,NULL,'order','New Order #ORD-20261006-0006','New order placed (KOT: KOT-145644-006)','{\"order_id\": 20, \"order_number\": \"ORD-20261006-0006\"}','2026-10-06 15:11:03','2026-10-06 14:56:44'),(27,1,1,NULL,'payment','Order #ORD-20261006-0006 Settled','Payment of ₹316.80 completed via POS. (Discount: ₹79.20)','{\"amount\": 316.8, \"order_id\": 20, \"order_number\": \"ORD-20261006-0006\"}','2026-10-06 15:11:03','2026-10-06 14:58:06'),(28,1,1,NULL,'order_status','Order #ORD-20261006-0005 Ready','Order status changed from preparing to ready.','{\"status\": \"ready\", \"order_id\": 19, \"order_number\": \"ORD-20261006-0005\"}','2026-10-06 15:11:03','2026-10-06 14:58:22'),(29,1,1,NULL,'order_status','Order #ORD-20261006-0005 Served','Order status changed from ready to served.','{\"status\": \"served\", \"order_id\": 19, \"order_number\": \"ORD-20261006-0005\"}','2026-10-06 15:11:03','2026-10-06 14:58:24'),(30,1,1,NULL,'order_status','Order #ORD-20261006-0005 Completed','Order status changed from served to completed.','{\"status\": \"completed\", \"order_id\": 19, \"order_number\": \"ORD-20261006-0005\"}','2026-10-06 15:11:03','2026-10-06 14:58:42'),(31,1,1,NULL,'order_status','Order #ORD-20261006-0004 Completed','Order status changed from served to completed.','{\"status\": \"completed\", \"order_id\": 18, \"order_number\": \"ORD-20261006-0004\"}','2026-10-06 15:11:03','2026-10-06 14:58:49'),(32,1,1,NULL,'kitchen','Kitchen: KOT-145644-006 Ready!','Order items are cooked and ready to be served.','{\"kot_id\": 20, \"kot_number\": \"KOT-145644-006\"}','2026-10-06 15:18:45','2026-10-06 15:16:26'),(33,1,1,NULL,'kitchen','Kitchen: KOT-142504-005 Ready!','Order items are cooked and ready to be served.','{\"kot_id\": 19, \"kot_number\": \"KOT-142504-005\"}','2026-10-06 15:18:45','2026-10-06 15:16:27'),(34,1,1,NULL,'order_status','Order #ORD-20261006-0006 Served','Order status changed from ready to served.','{\"status\": \"served\", \"order_id\": 20, \"order_number\": \"ORD-20261006-0006\"}','2026-10-06 15:18:45','2026-10-06 15:16:37'),(35,1,1,NULL,'order_status','Order #ORD-20261006-0005 Served','Order status changed from ready to served.','{\"status\": \"served\", \"order_id\": 19, \"order_number\": \"ORD-20261006-0005\"}','2026-10-06 15:18:42','2026-10-06 15:18:37'),(36,1,1,NULL,'order','New Order #ORD-20261006-0007','New order placed (KOT: KOT-153008-007)','{\"order_id\": 21, \"order_number\": \"ORD-20261006-0007\"}','2026-10-06 17:00:51','2026-10-06 15:30:08'),(37,1,1,NULL,'kitchen','Kitchen: KOT-153008-007 Ready!','Order items are cooked and ready to be served.','{\"kot_id\": 21, \"kot_number\": \"KOT-153008-007\"}','2026-10-06 17:00:51','2026-10-06 15:30:28'),(38,1,1,NULL,'order_status','Order #ORD-20261006-0007 Served','Order status changed from ready to served.','{\"status\": \"served\", \"order_id\": 21, \"order_number\": \"ORD-20261006-0007\"}','2026-10-06 17:00:51','2026-10-06 15:30:43'),(39,1,1,NULL,'order_status','Order #ORD-20261006-0007 Completed','Order status changed from served to completed.','{\"status\": \"completed\", \"order_id\": 21, \"order_number\": \"ORD-20261006-0007\"}','2026-10-06 17:00:48','2026-10-06 15:30:48'),(40,1,1,NULL,'order','New Order #ORD-20261007-0001','New order placed (KOT: KOT-105227-001)','{\"order_id\": 22, \"order_number\": \"ORD-20261007-0001\"}','2026-10-07 10:57:32','2026-10-07 10:52:27'),(41,1,1,NULL,'order_status','Order #ORD-20261007-0001 Preparing','Order status changed from confirmed to preparing.','{\"status\": \"preparing\", \"order_id\": 22, \"order_number\": \"ORD-20261007-0001\"}','2026-10-07 10:57:32','2026-10-07 10:52:36'),(42,1,1,NULL,'order_status','Order #ORD-20261007-0001 Ready','Order status changed from preparing to ready.','{\"status\": \"ready\", \"order_id\": 22, \"order_number\": \"ORD-20261007-0001\"}','2026-10-07 10:57:32','2026-10-07 10:52:45'),(43,1,1,NULL,'order_status','Order #ORD-20261007-0001 Served','Order status changed from ready to served.','{\"status\": \"served\", \"order_id\": 22, \"order_number\": \"ORD-20261007-0001\"}','2026-10-07 10:57:32','2026-10-07 10:52:49'),(44,1,1,NULL,'order_status','Order #ORD-20261007-0001 Completed','Order status changed from served to completed.','{\"status\": \"completed\", \"order_id\": 22, \"order_number\": \"ORD-20261007-0001\"}','2026-10-07 10:57:32','2026-10-07 10:55:37'),(45,1,1,NULL,'order','New Order #ORD-20261007-0002','New order placed (KOT: KOT-110335-002)','{\"order_id\": 23, \"order_number\": \"ORD-20261007-0002\"}','2026-10-07 11:06:57','2026-10-07 11:03:35'),(46,1,1,NULL,'order_status','Order #ORD-20261007-0002 Preparing','Order status changed from confirmed to preparing.','{\"status\": \"preparing\", \"order_id\": 23, \"order_number\": \"ORD-20261007-0002\"}','2026-10-07 11:23:45','2026-10-07 11:07:04'),(47,1,1,NULL,'order_status','Order #ORD-20261007-0002 Ready','Order status changed from preparing to ready.','{\"status\": \"ready\", \"order_id\": 23, \"order_number\": \"ORD-20261007-0002\"}','2026-10-07 11:38:50','2026-10-07 11:18:32'),(48,1,1,NULL,'order_status','Order #ORD-20261007-0002 Served','Order status changed from ready to served.','{\"status\": \"served\", \"order_id\": 23, \"order_number\": \"ORD-20261007-0002\"}','2026-10-07 11:38:48','2026-10-07 11:26:14'),(49,1,1,NULL,'order_status','Order #ORD-20261007-0002 Completed','Order status changed from served to completed.','{\"status\": \"completed\", \"order_id\": 23, \"order_number\": \"ORD-20261007-0002\"}','2026-10-07 11:38:47','2026-10-07 11:26:21'),(50,1,1,NULL,'kitchen','Kitchen: KOT-105227-001 Ready!','Order items are cooked and ready to be served.','{\"kot_id\": 22, \"kot_number\": \"KOT-105227-001\"}','2026-10-07 11:38:46','2026-10-07 11:29:38'),(51,1,1,NULL,'kitchen','Kitchen: KOT-110335-002 Ready!','Order items are cooked and ready to be served.','{\"kot_id\": 23, \"kot_number\": \"KOT-110335-002\"}','2026-10-07 11:38:45','2026-10-07 11:29:41'),(52,1,1,NULL,'order_status','Order #ORD-20261007-0002 Served','Order status changed from ready to served.','{\"status\": \"served\", \"order_id\": 23, \"order_number\": \"ORD-20261007-0002\"}','2026-10-07 11:38:44','2026-10-07 11:29:51'),(53,1,1,NULL,'order_status','Order #ORD-20261007-0001 Served','Order status changed from ready to served.','{\"status\": \"served\", \"order_id\": 22, \"order_number\": \"ORD-20261007-0001\"}','2026-10-07 11:38:43','2026-10-07 11:37:36'),(54,1,1,NULL,'order','New Order #ORD-20261007-0003','New order placed (KOT: KOT-113749-003)','{\"order_id\": 24, \"order_number\": \"ORD-20261007-0003\"}','2026-10-07 11:38:42','2026-10-07 11:37:49'),(55,1,1,NULL,'order_status','Order #ORD-20261007-0003 Completed','Order status changed from confirmed to completed.','{\"status\": \"completed\", \"order_id\": 24, \"order_number\": \"ORD-20261007-0003\"}','2026-10-07 11:38:40','2026-10-07 11:38:00'),(56,1,1,NULL,'order_status','Order #ORD-20261006-0007 Ready','Order status changed from completed to ready.','{\"status\": \"ready\", \"order_id\": 21, \"order_number\": \"ORD-20261006-0007\"}','2026-10-07 12:02:43','2026-10-07 11:49:07'),(57,1,1,NULL,'order_status','Order #ORD-20261006-0007 Preparing','Order status changed from ready to preparing.','{\"status\": \"preparing\", \"order_id\": 21, \"order_number\": \"ORD-20261006-0007\"}','2026-10-07 12:02:42','2026-10-07 11:49:09'),(58,1,1,NULL,'order_status','Order #ORD-20261006-0007 Served','Order status changed from preparing to served.','{\"status\": \"served\", \"order_id\": 21, \"order_number\": \"ORD-20261006-0007\"}','2026-10-07 12:02:42','2026-10-07 11:49:10'),(59,1,1,NULL,'order_status','Order #ORD-20261006-0007 Cancelled','Order status changed from served to cancelled.','{\"status\": \"cancelled\", \"order_id\": 21, \"order_number\": \"ORD-20261006-0007\"}','2026-10-07 12:02:41','2026-10-07 11:49:12'),(60,1,1,NULL,'order_status','Order #ORD-20261007-0003 Cancelled','Order status changed from completed to cancelled.','{\"status\": \"cancelled\", \"order_id\": 24, \"order_number\": \"ORD-20261007-0003\"}','2026-10-07 12:02:40','2026-10-07 11:54:13'),(61,1,1,NULL,'order_status','Order #ORD-20261007-0003 Completed','Order status changed from cancelled to completed.','{\"status\": \"completed\", \"order_id\": 24, \"order_number\": \"ORD-20261007-0003\"}','2026-10-07 12:02:39','2026-10-07 12:02:07'),(62,1,1,NULL,'order_status','Order #ORD-20261007-0002 Completed','Order status changed from served to completed.','{\"status\": \"completed\", \"order_id\": 23, \"order_number\": \"ORD-20261007-0002\"}','2026-10-07 12:02:38','2026-10-07 12:02:32'),(63,1,1,NULL,'order_status','Order #ORD-20261007-0003 Cancelled','Order status changed from completed to cancelled.','{\"status\": \"cancelled\", \"order_id\": 24, \"order_number\": \"ORD-20261007-0003\"}','2026-10-07 12:07:53','2026-10-07 12:05:38'),(64,1,1,NULL,'order_status','Order #ORD-20261007-0003 Completed','Order status changed from cancelled to completed.','{\"status\": \"completed\", \"order_id\": 24, \"order_number\": \"ORD-20261007-0003\"}','2026-10-07 12:07:51','2026-10-07 12:06:04'),(65,1,1,NULL,'kitchen','Kitchen: KOT-113749-003 Ready!','Order items are cooked and ready to be served.','{\"kot_id\": 24, \"kot_number\": \"KOT-113749-003\"}','2026-10-07 12:39:47','2026-10-07 12:14:41'),(66,1,1,NULL,'order','New Order #ORD-20261007-0004','New order placed (KOT: KOT-122349-004)','{\"order_id\": 25, \"order_number\": \"ORD-20261007-0004\"}','2026-10-07 12:39:45','2026-10-07 12:23:49'),(67,1,1,NULL,'payment','Order #ORD-20261007-0004 Settled','Payment of ₹356.00 completed via POS. (Discount: ₹84.00)','{\"amount\": 356, \"order_id\": 25, \"order_number\": \"ORD-20261007-0004\"}','2026-10-07 12:39:44','2026-10-07 12:23:49'),(68,1,1,NULL,'kitchen','Kitchen: KOT-122349-004 Ready!','Order items are cooked and ready to be served.','{\"kot_id\": 25, \"kot_number\": \"KOT-122349-004\"}','2026-10-07 12:39:41','2026-10-07 12:24:01'),(69,1,1,NULL,'order_status','Order #ORD-20261007-0004 Served','Order status changed from ready to served.','{\"status\": \"served\", \"order_id\": 25, \"order_number\": \"ORD-20261007-0004\"}','2026-10-07 12:39:43','2026-10-07 12:24:09'),(70,1,1,NULL,'order_status','Order #ORD-20261007-0003 Served','Order status changed from ready to served.','{\"status\": \"served\", \"order_id\": 24, \"order_number\": \"ORD-20261007-0003\"}','2026-10-07 15:23:45','2026-10-07 12:59:12'),(71,1,1,NULL,'order','New Order #ORD-20261007-0005','New order placed (KOT: KOT-130819-005)','{\"order_id\": 26, \"order_number\": \"ORD-20261007-0005\"}','2026-10-07 15:23:44','2026-10-07 13:08:20'),(72,1,1,NULL,'payment','Order #ORD-20261007-0005 Settled','Payment of ₹756.50 completed via POS. (Discount: ₹250.00)','{\"amount\": 756.5, \"order_id\": 26, \"order_number\": \"ORD-20261007-0005\"}','2026-10-07 15:23:42','2026-10-07 13:08:20'),(73,1,1,NULL,'kitchen','Kitchen: KOT-130819-005 Ready!','Order items are cooked and ready to be served.','{\"kot_id\": 26, \"kot_number\": \"KOT-130819-005\"}','2026-10-07 15:23:41','2026-10-07 14:49:16'),(74,1,1,NULL,'order','New Order #ORD-20261007-0006','New order placed (KOT: KOT-151938-006)','{\"order_id\": 27, \"order_number\": \"ORD-20261007-0006\"}','2026-10-07 15:23:39','2026-10-07 15:19:38'),(75,1,1,NULL,'kitchen','Kitchen: KOT-151938-006 Ready!','Order items are cooked and ready to be served.','{\"kot_id\": 27, \"kot_number\": \"KOT-151938-006\"}','2026-10-07 15:23:47','2026-10-07 15:19:47'),(76,1,1,NULL,'order_status','Order #ORD-20261007-0006 Served','Order status changed from ready to served.','{\"status\": \"served\", \"order_id\": 27, \"order_number\": \"ORD-20261007-0006\"}','2026-10-07 15:30:28','2026-10-07 15:24:28'),(77,1,1,NULL,'order','New Order #ORD-20261007-0007','New order placed (KOT: KOT-152745-007)','{\"order_id\": 28, \"order_number\": \"ORD-20261007-0007\"}','2026-10-07 15:30:28','2026-10-07 15:27:45'),(78,1,1,NULL,'order_status','Order #ORD-20261007-0007 Preparing','Order status changed from confirmed to preparing.','{\"status\": \"preparing\", \"order_id\": 28, \"order_number\": \"ORD-20261007-0007\"}','2026-10-07 15:30:28','2026-10-07 15:28:39'),(79,1,1,NULL,'order_status','Order #ORD-20261007-0007 Completed','Order status changed from preparing to completed.','{\"status\": \"completed\", \"order_id\": 28, \"order_number\": \"ORD-20261007-0007\"}','2026-10-07 15:30:28','2026-10-07 15:28:51'),(80,1,1,NULL,'order_status','Order #ORD-20261007-0005 Served','Order status changed from ready to served.','{\"status\": \"served\", \"order_id\": 26, \"order_number\": \"ORD-20261007-0005\"}','2026-10-07 15:30:28','2026-10-07 15:29:00'),(81,1,1,NULL,'order_status','Order #ORD-20261007-0006 Completed','Order status changed from served to completed.','{\"status\": \"completed\", \"order_id\": 27, \"order_number\": \"ORD-20261007-0006\"}','2026-10-07 15:43:41','2026-10-07 15:31:21'),(82,1,1,NULL,'order_status','Order #ORD-20261007-0005 Completed','Order status changed from served to completed.','{\"status\": \"completed\", \"order_id\": 26, \"order_number\": \"ORD-20261007-0005\"}','2026-10-07 15:43:41','2026-10-07 15:33:33'),(83,1,1,NULL,'kitchen','Kitchen: KOT-152745-007 Ready!','Order items are cooked and ready to be served.','{\"kot_id\": 28, \"kot_number\": \"KOT-152745-007\"}','2026-10-07 15:59:21','2026-10-07 15:57:24'),(84,1,1,NULL,'order_status','Order #ORD-20261007-0007 Served','Order status changed from ready to served.','{\"status\": \"served\", \"order_id\": 28, \"order_number\": \"ORD-20261007-0007\"}','2026-10-07 16:23:38','2026-10-07 16:12:25'),(85,1,1,NULL,'order','New Order #ORD-20261007-0008','New order placed (KOT: KOT-162727-008)','{\"order_id\": 29, \"order_number\": \"ORD-20261007-0008\"}','2026-10-08 10:59:43','2026-10-07 16:27:27'),(86,1,1,NULL,'kitchen','Kitchen: KOT-162727-008 Ready!','Order items are cooked and ready to be served.','{\"kot_id\": 29, \"kot_number\": \"KOT-162727-008\"}','2026-10-08 10:59:43','2026-10-07 16:28:10'),(87,1,1,NULL,'order_status','Order #ORD-20261007-0008 Served','Order status changed from ready to served.','{\"status\": \"served\", \"order_id\": 29, \"order_number\": \"ORD-20261007-0008\"}','2026-10-08 10:59:43','2026-10-07 16:28:35'),(88,1,1,NULL,'order_status','Order #ORD-20261007-0008 Completed','Order status changed from served to completed.','{\"status\": \"completed\", \"order_id\": 29, \"order_number\": \"ORD-20261007-0008\"}','2026-10-07 17:27:07','2026-10-07 16:28:42'),(89,1,1,NULL,'expense','New Expense: ₹200.00','Vendor: mukesh - Recorded by System','{\"amount\": 200, \"expense_id\": 3}','2026-10-07 17:27:09','2026-10-07 16:34:27'),(90,1,1,NULL,'order','New Order #ORD-20261007-0009','New order placed (KOT: KOT-165655-009)','{\"order_id\": 30, \"order_number\": \"ORD-20261007-0009\"}','2026-10-07 17:27:11','2026-10-07 16:56:55'),(91,1,1,NULL,'order_status','Order #ORD-20261007-0009 Preparing','Order status changed from confirmed to preparing.','{\"status\": \"preparing\", \"order_id\": 30, \"order_number\": \"ORD-20261007-0009\"}','2026-10-07 17:27:12','2026-10-07 16:57:06'),(92,1,1,NULL,'order_status','Order #ORD-20261007-0009 Ready','Order status changed from preparing to ready.','{\"status\": \"ready\", \"order_id\": 30, \"order_number\": \"ORD-20261007-0009\"}','2026-10-07 17:27:13','2026-10-07 16:57:14'),(93,1,1,NULL,'order_status','Order #ORD-20261007-0009 Served','Order status changed from ready to served.','{\"status\": \"served\", \"order_id\": 30, \"order_number\": \"ORD-20261007-0009\"}','2026-10-08 10:59:43','2026-10-07 16:57:20'),(94,1,1,NULL,'order_status','Order #ORD-20261007-0009 Completed','Order status changed from served to completed.','{\"status\": \"completed\", \"order_id\": 30, \"order_number\": \"ORD-20261007-0009\"}','2026-10-07 17:00:28','2026-10-07 16:57:35'),(95,1,1,NULL,'kitchen','Kitchen: KOT-165655-009 Ready!','Order items are cooked and ready to be served.','{\"kot_id\": 30, \"kot_number\": \"KOT-165655-009\"}','2026-10-07 16:59:20','2026-10-07 16:59:02'),(96,1,1,NULL,'order_status','Order #ORD-20261007-0009 Served','Order status changed from ready to served.','{\"status\": \"served\", \"order_id\": 30, \"order_number\": \"ORD-20261007-0009\"}','2026-10-07 16:59:18','2026-10-07 16:59:12'),(97,1,1,NULL,'order','New Order #ORD-20261008-0001','New order placed (KOT: KOT-103802-001)','{\"order_id\": 31, \"order_number\": \"ORD-20261008-0001\"}','2026-10-08 10:59:43','2026-10-08 10:38:02'),(98,1,1,NULL,'kitchen','Kitchen: KOT-103802-001 Ready!','Order items are cooked and ready to be served.','{\"kot_id\": 31, \"kot_number\": \"KOT-103802-001\"}','2026-10-08 10:59:43','2026-10-08 10:39:34'),(99,1,1,NULL,'order_status','Order #ORD-20261008-0001 Served','Order status changed from ready to served.','{\"status\": \"served\", \"order_id\": 31, \"order_number\": \"ORD-20261008-0001\"}','2026-10-08 10:59:43','2026-10-08 10:40:15'),(100,1,1,NULL,'order_status','Order #ORD-20261008-0001 Completed','Order status changed from served to completed.','{\"status\": \"completed\", \"order_id\": 31, \"order_number\": \"ORD-20261008-0001\"}','2026-10-08 10:59:43','2026-10-08 10:40:24'),(101,1,1,NULL,'order','New Order #ORD-20261008-0002','New order placed (KOT: KOT-104535-002)','{\"order_id\": 32, \"order_number\": \"ORD-20261008-0002\"}','2026-10-08 10:59:43','2026-10-08 10:45:35'),(102,1,1,NULL,'kitchen','Kitchen: KOT-104535-002 Ready!','Order items are cooked and ready to be served.','{\"kot_id\": 32, \"kot_number\": \"KOT-104535-002\"}','2026-10-08 10:59:43','2026-10-08 10:46:17'),(103,1,1,NULL,'order_status','Order #ORD-20261008-0002 Served','Order status changed from ready to served.','{\"status\": \"served\", \"order_id\": 32, \"order_number\": \"ORD-20261008-0002\"}','2026-10-08 10:59:43','2026-10-08 10:46:26'),(104,1,1,NULL,'order_status','Order #ORD-20261008-0002 Completed','Order status changed from served to completed.','{\"status\": \"completed\", \"order_id\": 32, \"order_number\": \"ORD-20261008-0002\"}','2026-10-08 10:59:43','2026-10-08 10:57:56'),(105,1,1,NULL,'order','New Order #ORD-20261008-0003','New order placed (KOT: KOT-110241-003)','{\"order_id\": 33, \"order_number\": \"ORD-20261008-0003\"}','2026-10-08 11:56:27','2026-10-08 11:02:41'),(106,1,1,NULL,'order_status','Order #ORD-20261008-0003 Preparing','Order status changed from confirmed to preparing.','{\"status\": \"preparing\", \"order_id\": 33, \"order_number\": \"ORD-20261008-0003\"}','2026-10-08 11:56:27','2026-10-08 11:03:22'),(107,1,1,NULL,'order_status','Order #ORD-20261008-0003 Completed','Order status changed from preparing to completed.','{\"status\": \"completed\", \"order_id\": 33, \"order_number\": \"ORD-20261008-0003\"}','2026-10-08 11:56:27','2026-10-08 11:03:57'),(108,1,1,NULL,'kitchen','Kitchen: KOT-110241-003 Ready!','Order items are cooked and ready to be served.','{\"kot_id\": 33, \"kot_number\": \"KOT-110241-003\"}','2026-10-08 11:56:27','2026-10-08 11:15:55'),(109,1,1,NULL,'order_status','Order #ORD-20261008-0003 Served','Order status changed from ready to served.','{\"status\": \"served\", \"order_id\": 33, \"order_number\": \"ORD-20261008-0003\"}','2026-10-08 11:56:27','2026-10-08 11:16:01'),(110,1,1,NULL,'order_status','Order #ORD-20261008-0003 Completed','Order status changed from served to completed.','{\"status\": \"completed\", \"order_id\": 33, \"order_number\": \"ORD-20261008-0003\"}','2026-10-08 11:56:27','2026-10-08 11:19:37'),(111,1,1,NULL,'order_status','Order #ORD-20261008-0003 Served','Order status changed from completed to served.','{\"status\": \"served\", \"order_id\": 33, \"order_number\": \"ORD-20261008-0003\"}','2026-10-08 11:56:27','2026-10-08 11:19:40'),(112,1,1,NULL,'order_status','Order #ORD-20261008-0003 Preparing','Order status changed from served to preparing.','{\"status\": \"preparing\", \"order_id\": 33, \"order_number\": \"ORD-20261008-0003\"}','2026-10-08 11:56:27','2026-10-08 11:19:42'),(113,1,1,NULL,'order_status','Order #ORD-20261008-0003 Confirmed','Order status changed from preparing to confirmed.','{\"status\": \"confirmed\", \"order_id\": 33, \"order_number\": \"ORD-20261008-0003\"}','2026-10-08 11:56:27','2026-10-08 11:19:43'),(114,1,1,NULL,'order_status','Order #ORD-20261008-0003 Ready','Order status changed from confirmed to ready.','{\"status\": \"ready\", \"order_id\": 33, \"order_number\": \"ORD-20261008-0003\"}','2026-10-08 11:56:27','2026-10-08 11:19:44'),(115,1,1,NULL,'order_status','Order #ORD-20261008-0003 Completed','Order status changed from ready to completed.','{\"status\": \"completed\", \"order_id\": 33, \"order_number\": \"ORD-20261008-0003\"}','2026-10-08 11:56:27','2026-10-08 11:19:46'),(116,1,1,NULL,'order','New Order #ORD-20261008-0004','New order placed (KOT: KOT-112121-004)','{\"order_id\": 34, \"order_number\": \"ORD-20261008-0004\"}','2026-10-08 11:56:27','2026-10-08 11:21:21'),(117,1,1,NULL,'order','New Order #ORD-20261008-0005','New order placed (KOT: KOT-113620-005)','{\"order_id\": 35, \"order_number\": \"ORD-20261008-0005\"}','2026-10-08 11:56:27','2026-10-08 11:36:20'),(118,1,1,NULL,'order_status','Order #ORD-20261008-0005 Preparing','Order status changed from confirmed to preparing.','{\"status\": \"preparing\", \"order_id\": 35, \"order_number\": \"ORD-20261008-0005\"}','2026-10-08 11:42:24','2026-10-08 11:36:40'),(119,1,1,NULL,'order_status','Order #ORD-20261008-0005 Ready','Order status changed from preparing to ready.','{\"status\": \"ready\", \"order_id\": 35, \"order_number\": \"ORD-20261008-0005\"}','2026-10-08 11:42:23','2026-10-08 11:36:57'),(120,1,1,NULL,'order_status','Order #ORD-20261008-0004 Preparing','Order status changed from confirmed to preparing.','{\"status\": \"preparing\", \"order_id\": 34, \"order_number\": \"ORD-20261008-0004\"}','2026-10-08 11:42:21','2026-10-08 11:38:18'),(121,1,1,NULL,'order_status','Order #ORD-20261008-0004 Ready','Order status changed from preparing to ready.','{\"status\": \"ready\", \"order_id\": 34, \"order_number\": \"ORD-20261008-0004\"}','2026-10-08 11:42:20','2026-10-08 11:38:25'),(122,1,1,NULL,'order_status','Order #ORD-20261008-0005 Served','Order status changed from ready to served.','{\"status\": \"served\", \"order_id\": 35, \"order_number\": \"ORD-20261008-0005\"}','2026-10-08 11:42:19','2026-10-08 11:38:29'),(123,1,1,NULL,'order_status','Order #ORD-20261008-0005 Completed','Order status changed from served to completed.','{\"status\": \"completed\", \"order_id\": 35, \"order_number\": \"ORD-20261008-0005\"}','2026-10-08 11:42:18','2026-10-08 11:38:54'),(124,1,1,NULL,'kitchen','Kitchen: KOT-112121-004 Ready!','Order items are cooked and ready to be served.','{\"kot_id\": 34, \"kot_number\": \"KOT-112121-004\"}','2026-10-08 11:42:17','2026-10-08 11:41:22'),(125,1,1,NULL,'kitchen','Kitchen: KOT-113620-005 Ready!','Order items are cooked and ready to be served.','{\"kot_id\": 35, \"kot_number\": \"KOT-113620-005\"}','2026-10-08 11:42:16','2026-10-08 11:41:23'),(126,1,1,NULL,'order_status','Order #ORD-20261008-0005 Served','Order status changed from ready to served.','{\"status\": \"served\", \"order_id\": 35, \"order_number\": \"ORD-20261008-0005\"}','2026-10-08 11:42:14','2026-10-08 11:41:49'),(127,1,1,NULL,'order_status','Order #ORD-20261008-0004 Served','Order status changed from ready to served.','{\"status\": \"served\", \"order_id\": 34, \"order_number\": \"ORD-20261008-0004\"}','2026-10-08 11:42:12','2026-10-08 11:41:53'),(128,1,1,NULL,'order_status','Order #ORD-20261008-0004 Completed','Order status changed from served to completed.','{\"status\": \"completed\", \"order_id\": 34, \"order_number\": \"ORD-20261008-0004\"}','2026-10-08 11:56:21','2026-10-08 11:44:09'),(129,1,1,NULL,'order','New Order #ORD-20261008-0006','New order placed (KOT: KOT-115608-006)','{\"order_id\": 36, \"order_number\": \"ORD-20261008-0006\"}','2026-10-08 11:56:20','2026-10-08 11:56:08'),(130,1,1,NULL,'order_status','Order #ORD-20261008-0006 Preparing','Order status changed from confirmed to preparing.','{\"status\": \"preparing\", \"order_id\": 36, \"order_number\": \"ORD-20261008-0006\"}','2026-10-08 12:16:20','2026-10-08 11:57:48'),(131,1,1,NULL,'order_status','Order #ORD-20261008-0006 Ready','Order status changed from preparing to ready.','{\"status\": \"ready\", \"order_id\": 36, \"order_number\": \"ORD-20261008-0006\"}','2026-10-08 12:16:19','2026-10-08 11:57:54'),(132,1,1,NULL,'order_status','Order #ORD-20261008-0006 Served','Order status changed from ready to served.','{\"status\": \"served\", \"order_id\": 36, \"order_number\": \"ORD-20261008-0006\"}','2026-10-08 12:16:18','2026-10-08 12:03:28'),(133,1,1,NULL,'order_status','Order #ORD-20261008-0006 Completed','Order status changed from served to completed.','{\"status\": \"completed\", \"order_id\": 36, \"order_number\": \"ORD-20261008-0006\"}',NULL,'2026-10-08 12:19:46'),(134,1,1,NULL,'order_status','Order #ORD-20261008-0005 Completed','Order status changed from served to completed.','{\"status\": \"completed\", \"order_id\": 35, \"order_number\": \"ORD-20261008-0005\"}','2026-10-08 12:54:49','2026-10-08 12:19:55'),(135,1,1,NULL,'order','New Order #ORD-20261008-0007','New order placed (KOT: KOT-122123-007)','{\"order_id\": 37, \"order_number\": \"ORD-20261008-0007\"}','2026-10-08 12:54:48','2026-10-08 12:21:23'),(136,1,1,NULL,'delivery','Delivery Dispatched','Order #37 assigned to delivery partner.','{\"order_id\": 37, \"assignment_id\": 3}','2026-10-08 12:47:18','2026-10-08 12:24:12'),(137,1,1,NULL,'delivery','Delivery: Picked up','Delivery assignment #3 updated to picked_up.','{\"status\": \"picked_up\", \"assignment_id\": 3}','2026-10-08 12:47:17','2026-10-08 12:24:17'),(138,1,1,NULL,'delivery','Delivery: In transit','Delivery assignment #3 updated to in_transit.','{\"status\": \"in_transit\", \"assignment_id\": 3}','2026-10-08 12:47:16','2026-10-08 12:24:57'),(139,1,1,NULL,'delivery','Delivery: Delivered','Delivery assignment #3 updated to delivered.','{\"status\": \"delivered\", \"assignment_id\": 3}','2026-10-08 12:54:08','2026-10-08 12:25:07'),(140,1,1,NULL,'order_status','Order #ORD-20261008-0007 Preparing','Order status changed from confirmed to preparing.','{\"status\": \"preparing\", \"order_id\": 37, \"order_number\": \"ORD-20261008-0007\"}','2026-10-08 12:54:09','2026-10-08 12:27:10'),(141,1,1,NULL,'order_status','Order #ORD-20261008-0007 Ready','Order status changed from preparing to ready.','{\"status\": \"ready\", \"order_id\": 37, \"order_number\": \"ORD-20261008-0007\"}','2026-10-08 12:54:07','2026-10-08 12:27:14'),(142,1,1,NULL,'order_status','Order #ORD-20261008-0007 Served','Order status changed from ready to served.','{\"status\": \"served\", \"order_id\": 37, \"order_number\": \"ORD-20261008-0007\"}','2026-10-08 12:54:06','2026-10-08 12:27:20'),(143,1,1,NULL,'order_status','Order #ORD-20261008-0007 Completed','Order status changed from served to completed.','{\"status\": \"completed\", \"order_id\": 37, \"order_number\": \"ORD-20261008-0007\"}','2026-10-08 12:54:05','2026-10-08 12:27:26'),(144,1,1,NULL,'kitchen','Kitchen: KOT-115608-006 Ready!','Order items are cooked and ready to be served.','{\"kot_id\": 36, \"kot_number\": \"KOT-115608-006\"}','2026-10-08 14:31:13','2026-10-08 14:23:33'),(145,1,1,NULL,'kitchen','Kitchen: KOT-122123-007 Ready!','Order items are cooked and ready to be served.','{\"kot_id\": 37, \"kot_number\": \"KOT-122123-007\"}','2026-10-08 14:31:12','2026-10-08 14:23:35'),(146,1,1,NULL,'order_status','Order #ORD-20261008-0007 Served','Order status changed from ready to served.','{\"status\": \"served\", \"order_id\": 37, \"order_number\": \"ORD-20261008-0007\"}','2026-10-08 14:31:10','2026-10-08 14:23:59'),(147,1,1,NULL,'order_status','Order #ORD-20261008-0006 Served','Order status changed from ready to served.','{\"status\": \"served\", \"order_id\": 36, \"order_number\": \"ORD-20261008-0006\"}','2026-10-08 14:31:11','2026-10-08 14:24:02'),(148,1,1,NULL,'order_status','Order #ORD-20261008-0007 Completed','Order status changed from served to completed.','{\"status\": \"completed\", \"order_id\": 37, \"order_number\": \"ORD-20261008-0007\"}',NULL,'2026-10-08 14:32:40');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `menu_item_id` int unsigned NOT NULL,
  `item_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `quantity` decimal(6,2) NOT NULL DEFAULT '1.00',
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `special_notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','sent','preparing','ready','served','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `kot_id` int unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_oi_order` (`order_id`),
  KEY `idx_oi_menu_item` (`menu_item_id`),
  KEY `idx_oi_status` (`status`),
  KEY `idx_oi_kot` (`kot_id`)
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (1,1,7,'Butter Chicken Dhabawala',440.00,2.00,880.00,44.00,924.00,'Boneless & spicy','ready',1,'2026-10-01 18:08:31','2026-10-01 18:19:29'),(2,1,10,'Butter Garlic Naan',85.00,4.00,340.00,17.00,357.00,'Crisp','sent',1,'2026-10-01 18:08:31','2026-10-01 18:08:31'),(3,1,13,'Virgin Mojito Blue Lagoon',180.00,2.00,360.00,18.00,378.00,'Less ice','sent',1,'2026-10-01 18:08:31','2026-10-01 18:08:31'),(4,2,1,'Margherita Pizza',12.50,2.00,25.00,1.25,26.25,'Extra crispy crust','sent',2,'2026-10-03 11:53:33','2026-10-03 11:53:33'),(5,3,1,'Paneer Tikka Angaare',320.00,1.00,320.00,16.00,336.00,'Extra mint chutney','sent',3,'2026-10-03 12:20:03','2026-10-03 12:20:03'),(6,3,10,'Butter Garlic Naan',85.00,2.00,170.00,8.50,178.50,NULL,'sent',3,'2026-10-03 12:20:03','2026-10-03 12:20:03'),(7,4,7,'Butter Chicken Dhabawala',440.00,1.00,440.00,22.00,462.00,NULL,'sent',4,'2026-10-03 12:21:30','2026-10-03 12:21:30'),(8,4,10,'Butter Garlic Naan',85.00,1.00,85.00,4.25,89.25,NULL,'sent',4,'2026-10-03 12:21:30','2026-10-03 12:21:30'),(9,4,7,'Butter Chicken Dhabawala',440.00,1.00,440.00,22.00,462.00,NULL,'ready',5,'2026-10-03 12:21:35','2026-10-03 14:28:44'),(10,4,10,'Butter Garlic Naan',85.00,1.00,85.00,4.25,89.25,NULL,'sent',5,'2026-10-03 12:21:35','2026-10-03 12:21:35'),(11,4,7,'Butter Chicken Dhabawala',440.00,1.00,440.00,22.00,462.00,NULL,'ready',6,'2026-10-03 12:21:44','2026-10-03 13:08:58'),(12,4,10,'Butter Garlic Naan',85.00,1.00,85.00,4.25,89.25,NULL,'sent',6,'2026-10-03 12:21:44','2026-10-03 12:21:44'),(13,4,3,'Chicken Malai Tikka',380.00,1.00,380.00,19.00,399.00,NULL,'sent',7,'2026-10-03 12:22:16','2026-10-03 12:22:16'),(14,5,10,'Butter Garlic Naan',85.00,1.00,85.00,4.25,89.25,NULL,'sent',8,'2026-10-03 13:07:55','2026-10-03 13:07:55'),(15,5,7,'Butter Chicken Dhabawala',440.00,1.00,440.00,22.00,462.00,NULL,'sent',8,'2026-10-03 13:07:55','2026-10-03 13:07:55'),(16,6,10,'Butter Garlic Naan',85.00,2.00,170.00,8.50,178.50,NULL,'sent',9,'2026-10-03 14:28:03','2026-10-03 14:28:03'),(17,6,5,'Dal Makhani Signature',340.00,1.00,340.00,17.00,357.00,NULL,'sent',9,'2026-10-03 14:28:03','2026-10-03 14:28:03'),(18,6,11,'Laccha Paratha',75.00,1.00,75.00,3.75,78.75,NULL,'sent',9,'2026-10-03 14:28:03','2026-10-03 14:28:03'),(19,6,14,'Mango Kesar Lassi',150.00,1.00,150.00,7.50,157.50,NULL,'sent',9,'2026-10-03 14:28:03','2026-10-03 14:28:03'),(20,7,7,'Butter Chicken Dhabawala',440.00,1.00,440.00,22.00,462.00,NULL,'sent',10,'2026-10-03 14:53:25','2026-10-03 14:53:25'),(21,7,10,'Butter Garlic Naan',85.00,1.00,85.00,4.25,89.25,NULL,'sent',10,'2026-10-03 14:53:25','2026-10-03 14:53:25'),(22,8,3,'Chicken Malai Tikka',380.00,1.00,380.00,19.00,399.00,NULL,'sent',11,'2026-10-05 12:30:51','2026-10-05 12:30:51'),(23,8,8,'Dum Gosht Biryani',540.00,1.00,540.00,27.00,567.00,NULL,'sent',11,'2026-10-05 12:30:51','2026-10-05 12:30:51'),(24,11,4,'Tandoori Prawns Zaffrani',520.00,1.00,520.00,26.00,546.00,NULL,'sent',12,'2026-10-05 14:06:48','2026-10-05 14:06:48'),(25,11,11,'Laccha Paratha',75.00,1.00,75.00,3.75,78.75,NULL,'sent',12,'2026-10-05 14:06:48','2026-10-05 14:06:48'),(26,13,2,'Crispy Corn & Water Chestnut',280.00,1.00,280.00,14.00,294.00,NULL,'sent',13,'2026-10-05 17:21:48','2026-10-05 17:21:48'),(27,13,15,'Masala Chai Artisan',90.00,1.00,90.00,4.50,94.50,NULL,'sent',13,'2026-10-05 17:21:48','2026-10-05 17:21:48'),(28,14,16,'Gulab Jamun Flambé',180.00,1.00,180.00,9.00,189.00,NULL,'sent',14,'2026-10-05 17:29:47','2026-10-05 17:29:47'),(29,14,1,'Paneer Tikka Angaare',320.00,1.00,320.00,16.00,336.00,NULL,'sent',14,'2026-10-05 17:29:47','2026-10-05 17:29:47'),(30,14,2,'Crispy Corn & Water Chestnut',280.00,1.00,280.00,14.00,294.00,NULL,'sent',14,'2026-10-05 17:29:47','2026-10-05 17:29:47'),(31,15,15,'Masala Chai Artisan',90.00,4.00,360.00,18.00,378.00,NULL,'sent',15,'2026-10-06 11:13:05','2026-10-06 11:13:05'),(32,16,20,'chicken bharta',250.00,1.00,250.00,12.50,262.50,NULL,'sent',16,'2026-10-06 12:39:12','2026-10-06 12:39:12'),(33,16,22,'rumali roti',10.00,3.00,30.00,1.50,31.50,NULL,'sent',16,'2026-10-06 12:39:12','2026-10-06 12:39:12'),(34,17,20,'chicken bharta',250.00,1.00,250.00,12.50,262.50,NULL,'sent',17,'2026-10-06 12:52:06','2026-10-06 12:52:06'),(35,17,22,'rumali roti',10.00,2.00,20.00,1.00,21.00,NULL,'sent',17,'2026-10-06 12:52:06','2026-10-06 12:52:06'),(36,17,13,'Virgin Mojito Blue Lagoon',180.00,1.00,180.00,9.00,189.00,NULL,'sent',17,'2026-10-06 12:52:06','2026-10-06 12:52:06'),(37,18,17,'Kesar Pista Kulfi',160.00,1.00,160.00,8.00,168.00,NULL,'sent',18,'2026-10-06 14:17:33','2026-10-06 14:17:33'),(38,18,14,'Mango Kesar Lassi',150.00,1.00,150.00,7.50,157.50,NULL,'sent',18,'2026-10-06 14:17:33','2026-10-06 14:17:33'),(39,19,24,'Chicken biryani',179.00,1.00,179.00,8.95,187.95,NULL,'sent',19,'2026-10-06 14:25:04','2026-10-06 14:25:04'),(40,20,5,'Dal Makhani Signature',340.00,1.00,340.00,17.00,357.00,NULL,'sent',20,'2026-10-06 14:56:44','2026-10-06 14:56:44'),(41,20,22,'rumali roti',10.00,2.00,20.00,1.00,21.00,NULL,'sent',20,'2026-10-06 14:56:44','2026-10-06 14:56:44'),(42,21,19,'Mushroom curry',250.00,1.00,250.00,12.50,262.50,NULL,'sent',21,'2026-10-06 15:30:08','2026-10-06 15:30:08'),(43,21,14,'Mango Kesar Lassi',150.00,8.00,1200.00,60.00,1260.00,NULL,'sent',21,'2026-10-06 15:30:08','2026-10-06 15:30:08'),(44,21,24,'Chicken biryani',179.00,1.00,179.00,8.95,187.95,NULL,'sent',21,'2026-10-06 15:30:08','2026-10-06 15:30:08'),(45,21,9,'Hyderabadi Veg Biryani',360.00,9.00,3240.00,162.00,3402.00,NULL,'sent',21,'2026-10-06 15:30:08','2026-10-06 15:30:08'),(46,21,17,'Kesar Pista Kulfi',160.00,1.00,160.00,8.00,168.00,NULL,'sent',21,'2026-10-06 15:30:08','2026-10-06 15:30:08'),(47,21,22,'rumali roti',10.00,3.00,30.00,1.50,31.50,NULL,'sent',21,'2026-10-06 15:30:08','2026-10-06 15:30:08'),(48,21,11,'Laccha Paratha',75.00,1.00,75.00,3.75,78.75,NULL,'sent',21,'2026-10-06 15:30:08','2026-10-06 15:30:08'),(49,21,2,'Crispy Corn & Water Chestnut',280.00,2.00,560.00,28.00,588.00,NULL,'sent',21,'2026-10-06 15:30:08','2026-10-06 15:30:08'),(50,22,24,'Chicken biryani',179.00,1.00,179.00,8.95,187.95,NULL,'sent',22,'2026-10-07 10:52:27','2026-10-07 10:52:27'),(51,22,11,'Laccha Paratha',75.00,1.00,75.00,3.75,78.75,NULL,'sent',22,'2026-10-07 10:52:27','2026-10-07 10:52:27'),(52,23,11,'Laccha Paratha',75.00,2.00,150.00,7.50,157.50,NULL,'sent',23,'2026-10-07 11:03:35','2026-10-07 11:03:35'),(53,23,23,'Mutton kasha',350.00,1.00,350.00,17.50,367.50,NULL,'sent',23,'2026-10-07 11:03:35','2026-10-07 11:03:35'),(54,24,2,'Crispy Corn & Water Chestnut',280.00,1.00,280.00,14.00,294.00,NULL,'sent',24,'2026-10-07 11:37:49','2026-10-07 11:37:49'),(55,24,5,'Dal Makhani Signature',340.00,1.00,340.00,17.00,357.00,NULL,'sent',24,'2026-10-07 11:37:49','2026-10-07 11:37:49'),(56,25,14,'Mango Kesar Lassi',150.00,1.00,150.00,7.50,157.50,NULL,'sent',25,'2026-10-07 12:23:49','2026-10-07 12:23:49'),(57,25,22,'rumali roti',10.00,1.00,10.00,0.50,10.50,NULL,'sent',25,'2026-10-07 12:23:49','2026-10-07 12:23:49'),(58,25,18,'Sizzling Brownie with Ice Cream',240.00,1.00,240.00,12.00,252.00,NULL,'sent',25,'2026-10-07 12:23:49','2026-10-07 12:23:49'),(59,26,9,'Hyderabadi Veg Biryani',360.00,1.00,360.00,18.00,378.00,NULL,'sent',26,'2026-10-07 13:08:19','2026-10-07 13:08:19'),(60,26,17,'Kesar Pista Kulfi',160.00,1.00,160.00,8.00,168.00,NULL,'sent',26,'2026-10-07 13:08:19','2026-10-07 13:08:19'),(61,26,11,'Laccha Paratha',75.00,1.00,75.00,3.75,78.75,NULL,'sent',26,'2026-10-07 13:08:19','2026-10-07 13:08:19'),(62,26,1,'Paneer Tikka Angaare',320.00,1.00,320.00,16.00,336.00,NULL,'sent',26,'2026-10-07 13:08:19','2026-10-07 13:08:19'),(63,27,11,'Laccha Paratha',75.00,1.00,75.00,3.75,78.75,NULL,'sent',27,'2026-10-07 15:19:38','2026-10-07 15:19:38'),(64,27,14,'Mango Kesar Lassi',150.00,1.00,150.00,7.50,157.50,NULL,'sent',27,'2026-10-07 15:19:38','2026-10-07 15:19:38'),(65,28,26,'chicken kabiraji',150.00,1.00,150.00,7.50,157.50,NULL,'sent',28,'2026-10-07 15:27:45','2026-10-07 15:27:45'),(66,28,3,'Chicken Malai Tikka',380.00,1.00,380.00,19.00,399.00,NULL,'sent',28,'2026-10-07 15:27:45','2026-10-07 15:27:45'),(67,29,23,'Mutton kasha',350.00,1.00,350.00,17.50,367.50,NULL,'sent',29,'2026-10-07 16:27:27','2026-10-07 16:27:27'),(68,29,6,'Paneer Butter Masala',360.00,1.00,360.00,18.00,378.00,NULL,'sent',29,'2026-10-07 16:27:27','2026-10-07 16:27:27'),(69,29,12,'Tandoori Roti',45.00,3.00,135.00,6.75,141.75,NULL,'sent',29,'2026-10-07 16:27:27','2026-10-07 16:27:27'),(70,30,20,'chicken bharta',250.00,2.00,500.00,25.00,525.00,NULL,'sent',30,'2026-10-07 16:56:55','2026-10-07 16:56:55'),(71,30,11,'Laccha Paratha',75.00,2.00,150.00,7.50,157.50,NULL,'sent',30,'2026-10-07 16:56:55','2026-10-07 16:56:55'),(72,31,20,'chicken bharta',250.00,1.00,250.00,12.50,262.50,NULL,'sent',31,'2026-10-08 10:38:02','2026-10-08 10:38:02'),(73,31,10,'Butter Garlic Naan',85.00,2.00,170.00,8.50,178.50,NULL,'sent',31,'2026-10-08 10:38:02','2026-10-08 10:38:02'),(74,32,8,'Dum Gosht Biryani',540.00,1.00,540.00,27.00,567.00,NULL,'sent',32,'2026-10-08 10:45:35','2026-10-08 10:45:35'),(75,32,16,'Gulab Jamun Flambé',180.00,1.00,180.00,9.00,189.00,NULL,'sent',32,'2026-10-08 10:45:35','2026-10-08 10:45:35'),(76,33,15,'Masala Chai Artisan',90.00,1.00,90.00,4.50,94.50,NULL,'preparing',33,'2026-10-08 11:02:41','2026-10-08 11:15:33'),(77,33,19,'Mushroom curry',250.00,1.00,250.00,12.50,262.50,NULL,'sent',33,'2026-10-08 11:02:41','2026-10-08 11:02:41'),(78,34,8,'Dum Gosht Biryani',540.00,1.00,540.00,27.00,567.00,NULL,'sent',34,'2026-10-08 11:21:21','2026-10-08 11:21:21'),(79,34,16,'Gulab Jamun Flambé',180.00,1.00,180.00,9.00,189.00,NULL,'sent',34,'2026-10-08 11:21:21','2026-10-08 11:21:21'),(80,34,9,'Hyderabadi Veg Biryani',360.00,1.00,360.00,18.00,378.00,NULL,'sent',34,'2026-10-08 11:21:21','2026-10-08 11:21:21'),(81,34,17,'Kesar Pista Kulfi',160.00,1.00,160.00,8.00,168.00,NULL,'sent',34,'2026-10-08 11:21:21','2026-10-08 11:21:21'),(82,34,29,'kum pao chicken',250.00,1.00,250.00,12.50,262.50,NULL,'sent',34,'2026-10-08 11:21:21','2026-10-08 11:21:21'),(83,35,16,'Gulab Jamun Flambé',180.00,1.00,180.00,9.00,189.00,NULL,'sent',35,'2026-10-08 11:36:20','2026-10-08 11:36:20'),(84,35,3,'Chicken Malai Tikka',380.00,1.00,380.00,19.00,399.00,NULL,'sent',35,'2026-10-08 11:36:20','2026-10-08 11:36:20'),(85,36,10,'Butter Garlic Naan',85.00,1.00,85.00,4.25,89.25,NULL,'sent',36,'2026-10-08 11:56:08','2026-10-08 11:56:08'),(86,36,20,'chicken bharta',250.00,1.00,250.00,12.50,262.50,NULL,'sent',36,'2026-10-08 11:56:08','2026-10-08 11:56:08'),(87,36,5,'Dal Makhani Signature',340.00,1.00,340.00,17.00,357.00,NULL,'sent',36,'2026-10-08 11:56:08','2026-10-08 11:56:08'),(88,37,24,'Chicken biryani',179.00,1.00,179.00,8.95,187.95,NULL,'sent',37,'2026-10-08 12:21:23','2026-10-08 12:21:23'),(89,37,16,'Gulab Jamun Flambé',180.00,1.00,180.00,9.00,189.00,NULL,'sent',37,'2026-10-08 12:21:23','2026-10-08 12:21:23'),(90,37,17,'Kesar Pista Kulfi',160.00,1.00,160.00,8.00,168.00,NULL,'sent',37,'2026-10-08 12:21:23','2026-10-08 12:21:23');
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_payments`
--

DROP TABLE IF EXISTS `order_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_payments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `payment_method_id` int unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `reference_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `received_by` int unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_op_order` (`order_id`),
  KEY `idx_op_method` (`payment_method_id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_payments`
--

LOCK TABLES `order_payments` WRITE;
/*!40000 ALTER TABLE `order_payments` DISABLE KEYS */;
INSERT INTO `order_payments` VALUES (1,2,1,15.00,'CASH-REC-1791008613',1,'2026-10-03 11:53:33',NULL),(2,2,2,12.50,'CARD-AUTH-4659',1,'2026-10-03 11:53:33',NULL),(3,3,1,539.00,'POS-SETTLE-1791010203',1,'2026-10-03 12:20:03',NULL),(4,8,1,1012.00,'ORD-SETTLE-1791185514',3,'2026-10-05 13:01:54','2026-10-05 13:01:54'),(5,9,1,350.00,'POS-SETTLE-1791185957',3,'2026-10-05 13:09:17',NULL),(6,10,4,420.00,'POS-SETTLE-1791185957',3,'2026-10-05 13:09:17',NULL),(7,7,1,577.50,'ORD-SETTLE-1791186477',1,'2026-10-05 13:17:57','2026-10-05 13:17:57'),(8,11,4,654.50,'POS-SETTLE-1791189408',1,'2026-10-05 14:06:48',NULL),(9,12,1,800.00,'POS-SETTLE-1791191113',3,'2026-10-05 14:35:13',NULL),(10,13,1,329.30,'POS-SETTLE-1791201108',4,'2026-10-05 17:21:48',NULL),(11,6,1,771.75,'ORD-SETTLE-1791201258',1,'2026-10-05 17:24:18','2026-10-05 17:24:18'),(12,14,1,819.00,'POS-SETTLE-1791201587',1,'2026-10-05 17:29:47',NULL),(13,15,1,396.00,'ORD-SETTLE-1791265435',1,'2026-10-06 11:13:55','2026-10-06 11:13:55'),(14,16,1,308.00,'ORD-SETTLE-1791270610',1,'2026-10-06 12:40:10','2026-10-06 12:40:10'),(15,17,1,495.00,'ORD-SETTLE-1791271473',4,'2026-10-06 12:54:33','2026-10-06 12:54:33'),(16,20,3,316.80,'POS-SETTLE-1791278886',4,'2026-10-06 14:58:06',NULL),(17,19,1,196.90,'ORD-SETTLE-1791278922',4,'2026-10-06 14:58:42','2026-10-06 14:58:42'),(18,18,1,325.50,'ORD-SETTLE-1791278929',4,'2026-10-06 14:58:49','2026-10-06 14:58:49'),(19,21,1,6263.40,'ORD-SETTLE-1791280848',1,'2026-10-06 15:30:48','2026-10-06 15:30:48'),(20,22,1,279.40,'ORD-SETTLE-1791350737',1,'2026-10-07 10:55:37','2026-10-07 10:55:37'),(21,23,1,550.00,'ORD-SETTLE-1791352581',1,'2026-10-07 11:26:21','2026-10-07 11:26:21'),(22,24,1,682.00,'ORD-SETTLE-1791353280',2,'2026-10-07 11:38:00','2026-10-07 11:38:00'),(23,25,1,356.00,'POS-SETTLE-1791356029',1,'2026-10-07 12:23:49',NULL),(24,26,5,756.50,'POS-SETTLE-1791358700',10,'2026-10-07 13:08:20',NULL),(25,28,1,583.00,'ORD-SETTLE-1791367131',10,'2026-10-07 15:28:51','2026-10-07 15:28:51'),(26,27,1,247.50,'ORD-SETTLE-1791367281',4,'2026-10-07 15:31:21','2026-10-07 15:31:21'),(27,29,1,929.50,'ORD-SETTLE-1791370722',1,'2026-10-07 16:28:42','2026-10-07 16:28:42'),(28,30,1,715.00,'ORD-SETTLE-1791372455',1,'2026-10-07 16:57:35','2026-10-07 16:57:35'),(29,31,1,462.00,'ORD-SETTLE-1791436224',1,'2026-10-08 10:40:24','2026-10-08 10:40:24'),(30,32,1,792.00,'ORD-SETTLE-1791437276',1,'2026-10-08 10:57:56','2026-10-08 10:57:56'),(31,33,4,299.20,'ORD-SETTLE-1791437637',1,'2026-10-08 11:03:57','2026-10-08 11:03:57'),(32,35,4,480.48,'ORD-SETTLE-1791439734',1,'2026-10-08 11:38:54','2026-10-08 11:38:54'),(33,34,4,1639.00,'ORD-SETTLE-1791440049',1,'2026-10-08 11:44:09','2026-10-08 11:44:09'),(34,36,1,594.00,'ORD-SETTLE-1791442186',4,'2026-10-08 12:19:46','2026-10-08 12:19:46'),(35,37,1,435.96,'ORD-SETTLE-1791442646',2,'2026-10-08 12:27:26','2026-10-08 12:27:26');
/*!40000 ALTER TABLE `order_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` int unsigned NOT NULL DEFAULT '1',
  `branch_id` int unsigned DEFAULT NULL,
  `order_number` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_type` enum('dine_in','takeaway','delivery') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'dine_in',
  `table_id` int unsigned DEFAULT NULL,
  `customer_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_address` text COLLATE utf8mb4_unicode_ci,
  `waiter_id` int unsigned DEFAULT NULL,
  `cashier_id` int unsigned DEFAULT NULL,
  `status` enum('pending','confirmed','preparing','ready','served','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'confirmed',
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `coupon_id` int unsigned DEFAULT NULL,
  `discount_reason` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_charge` decimal(10,2) NOT NULL DEFAULT '0.00',
  `final_total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payment_status` enum('unpaid','partially_paid','paid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `payment_method_id` int unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_order_number` (`order_number`),
  KEY `idx_orders_branch` (`branch_id`),
  KEY `idx_orders_table` (`table_id`),
  KEY `idx_orders_status` (`status`),
  KEY `idx_orders_created` (`created_at`),
  KEY `idx_orders_branch_status` (`branch_id`,`status`),
  KEY `idx_orders_payment` (`payment_status`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,1,1,'ORD-20261001-0001','dine_in',3,'Vikram Malhotra','+91 9811122233',NULL,1,1,'served',1580.00,79.00,0.00,NULL,NULL,0.00,1659.00,'paid',1,NULL,'2026-10-01 18:08:31','2026-10-06 13:16:48'),(2,1,1,'ORD-20261003-0001','dine_in',3,'Arthur Pendelton','+1 555-0921','742 Evergreen Terrace',1,1,'completed',25.00,1.25,0.00,NULL,NULL,1.25,27.50,'paid',1,NULL,'2026-10-03 11:53:33','2026-10-05 12:34:18'),(3,1,1,'ORD-20261003-0002','dine_in',1,'Alice Diner','9876543210',NULL,1,1,'completed',490.00,24.50,0.00,NULL,NULL,24.50,539.00,'paid',1,NULL,'2026-10-03 12:20:03','2026-10-05 17:23:57'),(4,1,1,'ORD-20261003-0003','dine_in',1,'',NULL,NULL,1,1,'served',1955.00,97.75,0.00,NULL,NULL,97.75,2150.50,'paid',NULL,NULL,'2026-10-03 12:21:30','2026-10-06 12:26:11'),(5,1,1,'ORD-20261003-0004','dine_in',2,'karan','1478523692',NULL,1,1,'completed',525.00,26.25,0.00,NULL,NULL,26.25,577.50,'paid',NULL,NULL,'2026-10-03 13:07:55','2026-10-03 13:10:48'),(6,1,1,'ORD-20261003-0005','delivery',NULL,'',NULL,NULL,1,1,'completed',735.00,36.75,0.00,NULL,NULL,0.00,771.75,'paid',1,NULL,'2026-10-03 14:28:03','2026-10-05 17:24:18'),(7,1,1,'ORD-20261003-0006','dine_in',7,'',NULL,NULL,1,1,'completed',525.00,26.25,0.00,NULL,NULL,26.25,577.50,'paid',1,NULL,'2026-10-03 14:53:25','2026-10-05 18:15:53'),(8,1,1,'ORD-20261005-0001','dine_in',1,'',NULL,NULL,4,4,'completed',920.00,46.00,0.00,NULL,NULL,46.00,1012.00,'paid',1,NULL,'2026-10-05 12:30:51','2026-10-05 18:15:38'),(9,1,1,'TEST-CASH-1791185957','dine_in',1,NULL,NULL,NULL,1,NULL,'completed',350.00,0.00,0.00,NULL,NULL,0.00,350.00,'paid',1,NULL,'2026-10-05 13:09:17','2026-10-05 13:09:17'),(10,1,1,'TEST-UPI-1791185957','dine_in',2,NULL,NULL,NULL,1,NULL,'completed',420.00,0.00,0.00,NULL,NULL,0.00,420.00,'paid',4,NULL,'2026-10-05 13:09:17','2026-10-05 13:09:17'),(11,1,1,'ORD-20261005-0004','dine_in',2,'',NULL,NULL,1,1,'completed',595.00,29.75,0.00,NULL,NULL,29.75,654.50,'paid',4,NULL,'2026-10-05 14:06:48','2026-10-05 14:07:35'),(12,1,1,'DISC-TEST-1791191113','dine_in',1,NULL,NULL,NULL,1,NULL,'completed',1000.00,0.00,200.00,1,'Coupon: WELCOME20',0.00,800.00,'paid',1,NULL,'2026-10-05 14:35:13','2026-10-05 14:35:13'),(13,1,1,'ORD-20261005-0006','dine_in',11,'',NULL,NULL,4,4,'served',370.00,18.50,77.70,1,'Coupon: WELCOME20',18.50,329.30,'paid',1,NULL,'2026-10-05 17:21:48','2026-10-05 18:15:48'),(14,1,1,'ORD-20261005-0007','delivery',NULL,'',NULL,NULL,1,1,'served',780.00,39.00,0.00,NULL,NULL,0.00,819.00,'paid',1,NULL,'2026-10-05 17:29:47','2026-10-05 18:15:45'),(15,1,1,'ORD-20261006-0001','dine_in',2,'',NULL,NULL,1,1,'completed',360.00,18.00,0.00,NULL,NULL,18.00,396.00,'paid',1,NULL,'2026-10-06 11:13:05','2026-10-06 11:13:55'),(16,1,1,'ORD-20261006-0002','dine_in',2,'',NULL,NULL,1,1,'completed',280.00,14.00,0.00,NULL,NULL,14.00,308.00,'paid',1,NULL,'2026-10-06 12:39:12','2026-10-06 12:40:10'),(17,1,1,'ORD-20261006-0003','dine_in',3,'',NULL,NULL,4,4,'completed',450.00,22.50,0.00,NULL,NULL,22.50,495.00,'paid',1,NULL,'2026-10-06 12:52:06','2026-10-06 12:54:33'),(18,1,1,'ORD-20261006-0004','takeaway',NULL,'subham','das',NULL,1,1,'completed',310.00,15.50,0.00,NULL,NULL,0.00,325.50,'paid',1,NULL,'2026-10-06 14:17:33','2026-10-06 14:58:49'),(19,1,1,'ORD-20261006-0005','dine_in',1,'',NULL,NULL,1,1,'served',179.00,8.95,0.00,NULL,NULL,8.95,196.90,'paid',1,NULL,'2026-10-06 14:25:04','2026-10-06 15:18:37'),(20,1,1,'ORD-20261006-0006','dine_in',2,'',NULL,NULL,4,4,'served',360.00,18.00,79.20,1,'Coupon: WELCOME20',18.00,316.80,'paid',3,NULL,'2026-10-06 14:56:44','2026-10-06 15:16:37'),(21,1,1,'ORD-20261006-0007','dine_in',10,'subhman gill','7418529636',NULL,1,1,'cancelled',5694.00,284.70,0.00,NULL,NULL,284.70,6263.40,'paid',1,NULL,'2026-10-06 15:30:08','2026-10-07 11:49:12'),(22,1,1,'ORD-20261007-0001','dine_in',1,'rahul sharma','7418529635',NULL,1,1,'served',254.00,12.70,0.00,NULL,NULL,12.70,279.40,'paid',1,NULL,'2026-10-07 10:52:27','2026-10-07 11:37:36'),(23,1,1,'ORD-20261007-0002','dine_in',2,'Walk-in Guest',NULL,NULL,4,4,'completed',500.00,25.00,0.00,NULL,NULL,25.00,550.00,'paid',1,NULL,'2026-10-07 11:03:35','2026-10-07 12:02:32'),(24,1,1,'ORD-20261007-0003','dine_in',2,'Walk-in Guest',NULL,NULL,2,2,'served',620.00,31.00,0.00,NULL,NULL,31.00,682.00,'paid',1,NULL,'2026-10-07 11:37:49','2026-10-07 12:59:12'),(25,1,1,'ORD-20261007-0004','dine_in',3,'Walk-in Guest',NULL,NULL,1,1,'served',400.00,20.00,84.00,1,'Coupon: WELCOME20',20.00,356.00,'paid',1,NULL,'2026-10-07 12:23:49','2026-10-07 12:24:09'),(26,1,1,'ORD-20261007-0005','dine_in',2,'Walk-in Guest',NULL,NULL,10,10,'completed',915.00,45.75,250.00,4,'Coupon: FESTIVE50',45.75,756.50,'paid',5,NULL,'2026-10-07 13:08:19','2026-10-07 15:33:33'),(27,1,1,'ORD-20261007-0006','dine_in',2,'suresh rathor','7679199207',NULL,10,10,'completed',225.00,11.25,0.00,NULL,NULL,11.25,247.50,'paid',1,NULL,'2026-10-07 15:19:38','2026-10-07 15:31:21'),(28,1,1,'ORD-20261007-0007','dine_in',1,'sugar prasad','8637046587',NULL,10,10,'served',530.00,26.50,0.00,NULL,NULL,26.50,583.00,'paid',1,NULL,'2026-10-07 15:27:45','2026-10-07 16:12:25'),(29,1,1,'ORD-20261007-0008','dine_in',1,'sayan biswas','8523697412',NULL,1,1,'completed',845.00,42.25,0.00,NULL,NULL,42.25,929.50,'paid',1,NULL,'2026-10-07 16:27:27','2026-10-07 16:28:42'),(30,1,1,'ORD-20261007-0009','dine_in',5,'Walk-in Guest',NULL,NULL,1,1,'served',650.00,32.50,0.00,NULL,NULL,32.50,715.00,'paid',1,NULL,'2026-10-07 16:56:55','2026-10-07 16:59:12'),(31,1,1,'ORD-20261008-0001','dine_in',1,'rupam saha','7412536987',NULL,1,1,'completed',420.00,21.00,0.00,NULL,NULL,21.00,462.00,'paid',1,NULL,'2026-10-08 10:38:02','2026-10-08 10:40:24'),(32,1,1,'ORD-20261008-0002','dine_in',4,'Walk-in Guest',NULL,NULL,1,1,'completed',720.00,36.00,0.00,NULL,NULL,36.00,792.00,'paid',1,NULL,'2026-10-08 10:45:35','2026-10-08 10:57:56'),(33,1,1,'ORD-20261008-0003','dine_in',5,'Walk-in Guest','7418529633',NULL,1,1,'completed',340.00,17.00,74.80,1,'Coupon: WELCOME20',17.00,299.20,'paid',4,NULL,'2026-10-08 11:02:41','2026-10-08 11:19:46'),(34,1,1,'ORD-20261008-0004','dine_in',6,'Walk-in Guest','78596526341',NULL,1,1,'completed',1490.00,74.50,0.00,NULL,NULL,74.50,1639.00,'paid',4,NULL,'2026-10-08 11:21:21','2026-10-08 11:44:09'),(35,1,1,'ORD-20261008-0005','dine_in',2,'Walk-in Guest',NULL,NULL,1,1,'completed',560.00,28.00,135.52,5,'Coupon: KAR2026',28.00,480.48,'paid',4,NULL,'2026-10-08 11:36:20','2026-10-08 12:19:55'),(36,1,1,'ORD-20261008-0006','dine_in',3,'Walk-in Guest',NULL,NULL,1,1,'served',675.00,33.75,148.50,1,'Coupon: WELCOME20',33.75,594.00,'paid',1,NULL,'2026-10-08 11:56:08','2026-10-08 14:24:02'),(37,1,1,'ORD-20261008-0007','delivery',NULL,'Walk-in Guest',NULL,NULL,4,4,'completed',519.00,25.95,108.99,1,'Coupon: WELCOME20',0.00,435.96,'paid',1,NULL,'2026-10-08 12:21:23','2026-10-08 14:32:40');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `token` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pr_token` (`token`),
  KEY `idx_pr_user` (`user_id`),
  CONSTRAINT `fk_pr_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_methods`
--

DROP TABLE IF EXISTS `payment_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_methods` (
  `id` tinyint unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` int unsigned NOT NULL,
  `name` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gateway` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gateway_config` json DEFAULT NULL,
  `surcharge_pct` decimal(4,2) NOT NULL DEFAULT '0.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` tinyint unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pm_restaurant_slug` (`restaurant_id`,`slug`),
  CONSTRAINT `fk_pm_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_methods`
--

LOCK TABLES `payment_methods` WRITE;
/*!40000 ALTER TABLE `payment_methods` DISABLE KEYS */;
INSERT INTO `payment_methods` VALUES (1,1,'Cash','cash',NULL,NULL,NULL,0.00,1,1,'2026-10-01 17:44:43','2026-10-01 17:44:43'),(2,1,'Credit Card','card',NULL,NULL,NULL,0.00,1,2,'2026-10-01 17:44:43','2026-10-01 17:44:43'),(3,1,'Debit Card','debit',NULL,NULL,NULL,0.00,1,3,'2026-10-01 17:44:43','2026-10-01 17:44:43'),(4,1,'UPI','upi',NULL,NULL,NULL,0.00,1,4,'2026-10-01 17:44:43','2026-10-01 17:44:43'),(5,1,'Digital Wallet','wallet',NULL,NULL,NULL,0.00,1,5,'2026-10-01 17:44:43','2026-10-01 17:44:43');
/*!40000 ALTER TABLE `payment_methods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` smallint unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_permissions_slug` (`slug`),
  KEY `idx_permissions_module` (`module`)
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'dashboard','view','dashboard.view','View main dashboard','2026-10-01 17:44:42'),(2,'restaurants','view','restaurants.view','View restaurant settings','2026-10-01 17:44:42'),(3,'restaurants','edit','restaurants.edit','Edit restaurant settings','2026-10-01 17:44:42'),(4,'branches','view','branches.view','View branches','2026-10-01 17:44:42'),(5,'branches','create','branches.create','Create branches','2026-10-01 17:44:42'),(6,'branches','edit','branches.edit','Edit branches','2026-10-01 17:44:42'),(7,'branches','delete','branches.delete','Delete branches','2026-10-01 17:44:42'),(8,'users','view','users.view','View users','2026-10-01 17:44:42'),(9,'users','create','users.create','Create users','2026-10-01 17:44:42'),(10,'users','edit','users.edit','Edit users','2026-10-01 17:44:42'),(11,'users','delete','users.delete','Delete/deactivate users','2026-10-01 17:44:42'),(12,'roles','view','roles.view','View roles','2026-10-01 17:44:42'),(13,'roles','edit','roles.edit','Assign role permissions','2026-10-01 17:44:42'),(14,'menu_categories','view','menu_categories.view','View menu categories','2026-10-01 17:44:42'),(15,'menu_categories','create','menu_categories.create','Create menu categories','2026-10-01 17:44:42'),(16,'menu_categories','edit','menu_categories.edit','Edit menu categories','2026-10-01 17:44:42'),(17,'menu_categories','delete','menu_categories.delete','Delete menu categories','2026-10-01 17:44:42'),(18,'menu_items','view','menu_items.view','View menu items','2026-10-01 17:44:42'),(19,'menu_items','create','menu_items.create','Create menu items','2026-10-01 17:44:42'),(20,'menu_items','edit','menu_items.edit','Edit menu items','2026-10-01 17:44:42'),(21,'menu_items','delete','menu_items.delete','Delete menu items','2026-10-01 17:44:42'),(22,'floors','view','floors.view','View floor layouts','2026-10-01 17:44:42'),(23,'floors','manage','floors.manage','Create/edit floor layouts and tables','2026-10-01 17:44:42'),(24,'reservations','view','reservations.view','View reservations','2026-10-01 17:44:42'),(25,'reservations','create','reservations.create','Create reservations','2026-10-01 17:44:42'),(26,'reservations','edit','reservations.edit','Edit reservations','2026-10-01 17:44:42'),(27,'reservations','delete','reservations.delete','Cancel reservations','2026-10-01 17:44:42'),(28,'orders','view','orders.view','View orders','2026-10-01 17:44:42'),(29,'orders','create','orders.create','Create new orders','2026-10-01 17:44:42'),(30,'orders','edit','orders.edit','Modify open orders','2026-10-01 17:44:42'),(31,'orders','cancel','orders.cancel','Cancel orders','2026-10-01 17:44:42'),(32,'orders','void','orders.void','Void finalized orders','2026-10-01 17:44:42'),(33,'kot','view','kot.view','View KOT queue','2026-10-01 17:44:42'),(34,'kot','manage','kot.manage','Update KOT status','2026-10-01 17:44:42'),(35,'kds','view','kds.view','Access Kitchen Display System','2026-10-01 17:44:42'),(36,'billing','view','billing.view','View bills','2026-10-01 17:44:42'),(37,'billing','create','billing.create','Generate bills','2026-10-01 17:44:42'),(38,'billing','discount','billing.discount','Apply discounts on bills','2026-10-01 17:44:42'),(39,'billing','void','billing.void','Void/cancel bills','2026-10-01 17:44:42'),(40,'payments','view','payments.view','View payments','2026-10-01 17:44:42'),(41,'payments','process','payments.process','Process payments','2026-10-01 17:44:42'),(42,'payments','refund','payments.refund','Process refunds','2026-10-01 17:44:42'),(43,'pos','access','pos.access','Access POS terminal','2026-10-01 17:44:42'),(44,'inventory','view','inventory.view','View inventory','2026-10-01 17:44:42'),(45,'inventory','adjust','inventory.adjust','Manual stock adjustments','2026-10-01 17:44:42'),(46,'inventory','export','inventory.export','Export inventory reports','2026-10-01 17:44:42'),(47,'purchases','view','purchases.view','View purchase orders','2026-10-01 17:44:42'),(48,'purchases','create','purchases.create','Create purchase orders','2026-10-01 17:44:42'),(49,'purchases','approve','purchases.approve','Approve purchase orders','2026-10-01 17:44:42'),(50,'grn','view','grn.view','View goods received notes','2026-10-01 17:44:42'),(51,'grn','create','grn.create','Create GRN entries','2026-10-01 17:44:42'),(52,'waste','view','waste.view','View waste logs','2026-10-01 17:44:42'),(53,'waste','create','waste.create','Log waste entries','2026-10-01 17:44:42'),(54,'customers','view','customers.view','View customer profiles','2026-10-01 17:44:42'),(55,'customers','create','customers.create','Create customer profiles','2026-10-01 17:44:42'),(56,'customers','edit','customers.edit','Edit customer profiles','2026-10-01 17:44:42'),(57,'reports','view','reports.view','Access all reports','2026-10-01 17:44:42'),(58,'reports','export','reports.export','Export reports','2026-10-01 17:44:42'),(59,'staff','view','staff.view','View staff records','2026-10-01 17:44:42'),(60,'staff','manage','staff.manage','Manage staff records','2026-10-01 17:44:42'),(61,'attendance','view','attendance.view','View staff attendance and shifts','2026-10-01 17:44:42'),(62,'attendance','manage','attendance.manage','Manage check-in/out, shifts, and attendance','2026-10-01 17:44:42'),(63,'expenses','view','expenses.view','View expenses and categories','2026-10-01 17:44:42'),(64,'expenses','create','expenses.create','Create expense entries','2026-10-01 17:44:42'),(65,'expenses','approve','expenses.approve','Approve expenses','2026-10-01 17:44:42'),(66,'settings','view','settings.view','View system settings','2026-10-01 17:44:42'),(67,'settings','edit','settings.edit','Edit system settings','2026-10-01 17:44:42'),(68,'audit','view','audit.view','View audit logs','2026-10-01 17:44:42'),(69,'backup','manage','backup.manage','Create and restore backups','2026-10-01 17:44:42'),(72,'coupons','view','coupons.view','View discount coupons and rules','2026-10-03 12:39:12'),(73,'coupons','manage','coupons.manage','Create, edit and manage coupons','2026-10-03 12:39:12'),(74,'delivery','view','delivery.view','View delivery partners and orders','2026-10-03 12:39:12'),(75,'delivery','manage','delivery.manage','Manage delivery assignments and partners','2026-10-03 12:39:12'),(77,'expenses','manage','expenses.manage','Record and approve expenses','2026-10-03 12:39:12'),(78,'performance','view','performance.view','View staff performance metrics','2026-10-03 12:39:12'),(79,'performance','manage','performance.manage','Create and submit performance reviews','2026-10-03 12:39:12'),(80,'maintenance','view','maintenance.view','View equipment and maintenance requests','2026-10-03 12:39:12'),(81,'maintenance','manage','maintenance.manage','Manage equipment and maintenance logs','2026-10-03 12:39:12'),(82,'accounting','view','accounting.view','View sales, purchases, P&L, and financial reports','2026-10-03 13:11:09'),(83,'accounting','manage','accounting.manage','Manage chart of accounts, journal entries, and finances','2026-10-03 13:11:09'),(84,'communication','view','communication.view','View order, reservation, and stock notifications','2026-10-03 13:11:09'),(85,'communication','manage','communication.manage','Send announcements, broadcast alerts, and customer messages','2026-10-03 13:11:09'),(86,'crm','view','crm.view','View CRM customer directory and loyalty profiles','2026-10-05 12:55:41'),(87,'crm','create','crm.create','Create customer profiles and log feedback','2026-10-05 12:55:41'),(88,'crm','edit','crm.edit','Adjust customer loyalty points and edit profiles','2026-10-05 12:55:41'),(89,'floors','edit','floors.edit','Update floor tables and seating status','2026-10-05 12:55:41'),(90,'inventory','create','inventory.create','Create purchase orders and register suppliers','2026-10-05 12:55:41'),(91,'inventory','edit','inventory.edit','Adjust stock levels, recipes and record wastage','2026-10-05 12:55:41');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_order_items`
--

DROP TABLE IF EXISTS `purchase_order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_order_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `purchase_order_id` int unsigned NOT NULL,
  `inventory_item_id` int unsigned NOT NULL,
  `quantity_ordered` decimal(12,3) NOT NULL,
  `quantity_received` decimal(12,3) NOT NULL DEFAULT '0.000',
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_poi_po` (`purchase_order_id`),
  KEY `idx_poi_item` (`inventory_item_id`),
  CONSTRAINT `fk_poi_item` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_poi_po` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_order_items`
--

LOCK TABLES `purchase_order_items` WRITE;
/*!40000 ALTER TABLE `purchase_order_items` DISABLE KEYS */;
INSERT INTO `purchase_order_items` VALUES (1,1,6,20.000,20.000,9.00,180.00),(2,1,4,10.000,10.000,6.00,60.00),(3,2,9,20.000,20.000,4.50,90.00),(4,3,3,10.000,10.000,10.00,100.00);
/*!40000 ALTER TABLE `purchase_order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_orders`
--

DROP TABLE IF EXISTS `purchase_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_orders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `po_number` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `restaurant_id` int unsigned NOT NULL DEFAULT '1',
  `branch_id` int unsigned DEFAULT '1',
  `supplier_id` int unsigned NOT NULL,
  `order_date` date NOT NULL,
  `expected_date` date DEFAULT NULL,
  `status` enum('draft','approved','sent','received','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `total_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` int unsigned DEFAULT NULL,
  `approved_by` int unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_po_num` (`po_number`),
  KEY `idx_po_supplier` (`supplier_id`),
  KEY `idx_po_status` (`status`),
  CONSTRAINT `fk_po_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_orders`
--

LOCK TABLES `purchase_orders` WRITE;
/*!40000 ALTER TABLE `purchase_orders` DISABLE KEYS */;
INSERT INTO `purchase_orders` VALUES (1,'PO-2026-001',1,1,2,'2026-10-03','2026-10-05','received',240.00,'Urgent restocking for Angus beef patties and chicken fillets',1,NULL,'2026-10-03 11:29:44','2026-10-03 13:25:40'),(2,'PO-2026-002',1,1,4,'2026-10-03','2026-10-06','received',90.00,'Emergency dairy restock',1,NULL,'2026-10-03 11:40:32','2026-10-03 11:40:32'),(3,'PO-2026-003',1,1,2,'2026-10-05','2026-10-08','received',100.00,'',1,NULL,'2026-10-05 17:25:14','2026-10-05 17:25:19');
/*!40000 ALTER TABLE `purchase_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reservations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` int unsigned NOT NULL DEFAULT '1',
  `branch_id` int unsigned DEFAULT NULL,
  `table_id` int unsigned DEFAULT NULL,
  `customer_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guest_count` tinyint unsigned NOT NULL DEFAULT '2',
  `reservation_date` date NOT NULL,
  `reservation_time` time NOT NULL,
  `status` enum('confirmed','seated','cancelled','no_show','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'confirmed',
  `special_requests` text COLLATE utf8mb4_unicode_ci,
  `created_by` int unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_res_branch` (`branch_id`),
  KEY `idx_res_date` (`reservation_date`),
  KEY `idx_res_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservations`
--

LOCK TABLES `reservations` WRITE;
/*!40000 ALTER TABLE `reservations` DISABLE KEYS */;
INSERT INTO `reservations` VALUES (1,1,1,5,'Rohan Kapoor','+91 9820011223','rohan.k@example.com',4,'2026-10-01','19:30:00','confirmed','Anniversary celebration. Window table preferred.',1,'2026-10-01 18:22:04','2026-10-01 18:22:04'),(2,1,1,11,'Ananya Sen','+91 9910044556','ananya.s@example.com',8,'2026-10-01','20:00:00','confirmed','VIP lounge seating. Chef specials menu.',1,'2026-10-01 18:22:04','2026-10-01 18:22:04'),(3,1,1,11,'Vikram paul','+919800047856','VP@gmail.com',2,'2026-10-07','19:30:00','seated','Birthday ',1,'2026-10-03 15:13:35','2026-10-03 15:14:13'),(4,1,1,10,'subhman gill','7418529636','subhman@gmail.com',5,'2026-10-06','19:30:00','seated','high on life\r\n',1,'2026-10-06 15:28:45','2026-10-06 15:28:49');
/*!40000 ALTER TABLE `reservations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `restaurant_tables`
--

DROP TABLE IF EXISTS `restaurant_tables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `restaurant_tables` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` int unsigned NOT NULL DEFAULT '1',
  `branch_id` int unsigned DEFAULT NULL,
  `floor_id` int unsigned NOT NULL,
  `table_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `seating_capacity` tinyint unsigned NOT NULL DEFAULT '4',
  `shape` enum('square','rectangle','round') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'square',
  `status` enum('available','occupied','reserved','dirty') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `current_order_id` int unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` smallint unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_branch_table` (`branch_id`,`table_number`),
  KEY `idx_tables_floor` (`floor_id`),
  KEY `idx_tables_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `restaurant_tables`
--

LOCK TABLES `restaurant_tables` WRITE;
/*!40000 ALTER TABLE `restaurant_tables` DISABLE KEYS */;
INSERT INTO `restaurant_tables` VALUES (1,1,1,1,'T-01',2,'square','available',NULL,1,0,'2026-10-01 18:00:47','2026-10-08 12:03:44'),(2,1,1,1,'T-02',2,'square','available',NULL,1,0,'2026-10-01 18:00:47','2026-10-08 14:30:50'),(3,1,1,1,'T-03',4,'square','available',NULL,1,0,'2026-10-01 18:00:47','2026-10-08 14:30:52'),(4,1,1,1,'T-04',4,'square','available',NULL,1,0,'2026-10-01 18:00:47','2026-10-08 12:03:38'),(5,1,1,1,'T-05',6,'rectangle','available',NULL,1,0,'2026-10-01 18:00:47','2026-10-08 12:03:40'),(6,1,1,1,'T-06',8,'rectangle','available',NULL,1,0,'2026-10-01 18:00:47','2026-10-08 12:03:41'),(7,1,1,2,'R-01',2,'round','available',NULL,1,0,'2026-10-01 18:00:47','2026-10-07 16:49:48'),(8,1,1,2,'R-02',4,'round','available',NULL,1,0,'2026-10-01 18:00:47','2026-10-01 18:00:47'),(9,1,1,2,'R-03',4,'round','available',NULL,1,0,'2026-10-01 18:00:47','2026-10-01 18:00:47'),(10,1,1,2,'R-04',6,'rectangle','available',NULL,1,0,'2026-10-01 18:00:47','2026-10-07 11:49:12'),(11,1,1,3,'VIP-1',10,'rectangle','available',NULL,1,0,'2026-10-01 18:00:47','2026-10-06 10:47:53'),(12,1,1,3,'VIP-2',12,'rectangle','available',NULL,1,0,'2026-10-01 18:00:47','2026-10-01 18:00:47'),(13,1,1,3,'VP-03',10,'round','available',NULL,1,1,'2026-10-07 12:22:12','2026-10-07 12:22:12'),(14,1,1,2,'R-05',6,'square','available',NULL,1,1,'2026-10-07 17:08:13','2026-10-07 17:08:13');
/*!40000 ALTER TABLE `restaurant_tables` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `restaurants`
--

DROP TABLE IF EXISTS `restaurants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `restaurants` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `legal_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'India',
  `postal_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gstin` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pan` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fssai_license` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency_code` char(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'INR',
  `currency_symbol` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '₹',
  `timezone` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Asia/Kolkata',
  `date_format` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'd/m/Y',
  `time_format` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '12',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `restaurants`
--

LOCK TABLES `restaurants` WRITE;
/*!40000 ALTER TABLE `restaurants` DISABLE KEYS */;
INSERT INTO `restaurants` VALUES (1,'Kichu Khon 🍱','My Restaurant Pvt. Ltd.',NULL,NULL,NULL,NULL,'India',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'INR','₹','Asia/Kolkata','d/m/Y','12',1,'2026-10-01 17:44:43','2026-10-06 14:50:19');
/*!40000 ALTER TABLE `restaurants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_permissions` (
  `role_id` tinyint unsigned NOT NULL,
  `permission_id` smallint unsigned NOT NULL,
  `granted_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `idx_rp_permission` (`permission_id`),
  CONSTRAINT `fk_rp_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_rp_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permissions`
--

LOCK TABLES `role_permissions` WRITE;
/*!40000 ALTER TABLE `role_permissions` DISABLE KEYS */;
INSERT INTO `role_permissions` VALUES (1,1,'2026-10-01 17:44:42'),(1,2,'2026-10-01 17:44:42'),(1,3,'2026-10-01 17:44:42'),(1,4,'2026-10-01 17:44:42'),(1,5,'2026-10-01 17:44:42'),(1,6,'2026-10-01 17:44:42'),(1,7,'2026-10-01 17:44:42'),(1,8,'2026-10-01 17:44:42'),(1,9,'2026-10-01 17:44:42'),(1,10,'2026-10-01 17:44:42'),(1,11,'2026-10-01 17:44:42'),(1,12,'2026-10-01 17:44:42'),(1,13,'2026-10-01 17:44:42'),(1,14,'2026-10-01 17:44:42'),(1,15,'2026-10-01 17:44:42'),(1,16,'2026-10-01 17:44:42'),(1,17,'2026-10-01 17:44:42'),(1,18,'2026-10-01 17:44:42'),(1,19,'2026-10-01 17:44:42'),(1,20,'2026-10-01 17:44:42'),(1,21,'2026-10-01 17:44:42'),(1,22,'2026-10-01 17:44:42'),(1,23,'2026-10-01 17:44:42'),(1,24,'2026-10-01 17:44:42'),(1,25,'2026-10-01 17:44:42'),(1,26,'2026-10-01 17:44:42'),(1,27,'2026-10-01 17:44:42'),(1,28,'2026-10-01 17:44:42'),(1,29,'2026-10-01 17:44:42'),(1,30,'2026-10-01 17:44:42'),(1,31,'2026-10-01 17:44:42'),(1,32,'2026-10-01 17:44:42'),(1,33,'2026-10-01 17:44:42'),(1,34,'2026-10-01 17:44:42'),(1,35,'2026-10-01 17:44:42'),(1,36,'2026-10-01 17:44:42'),(1,37,'2026-10-01 17:44:42'),(1,38,'2026-10-01 17:44:42'),(1,39,'2026-10-01 17:44:42'),(1,40,'2026-10-01 17:44:42'),(1,41,'2026-10-01 17:44:42'),(1,42,'2026-10-01 17:44:42'),(1,43,'2026-10-01 17:44:42'),(1,44,'2026-10-01 17:44:42'),(1,45,'2026-10-01 17:44:42'),(1,46,'2026-10-01 17:44:42'),(1,47,'2026-10-01 17:44:42'),(1,48,'2026-10-01 17:44:42'),(1,49,'2026-10-01 17:44:42'),(1,50,'2026-10-01 17:44:42'),(1,51,'2026-10-01 17:44:42'),(1,52,'2026-10-01 17:44:42'),(1,53,'2026-10-01 17:44:42'),(1,54,'2026-10-01 17:44:42'),(1,55,'2026-10-01 17:44:42'),(1,56,'2026-10-01 17:44:42'),(1,57,'2026-10-01 17:44:42'),(1,58,'2026-10-01 17:44:42'),(1,59,'2026-10-01 17:44:42'),(1,60,'2026-10-01 17:44:42'),(1,61,'2026-10-01 17:44:42'),(1,62,'2026-10-01 17:44:42'),(1,63,'2026-10-01 17:44:42'),(1,64,'2026-10-01 17:44:42'),(1,65,'2026-10-01 17:44:42'),(1,66,'2026-10-01 17:44:42'),(1,67,'2026-10-01 17:44:42'),(1,68,'2026-10-01 17:44:42'),(1,69,'2026-10-01 17:44:42'),(1,72,'2026-10-03 12:39:12'),(1,73,'2026-10-03 12:39:12'),(1,74,'2026-10-03 12:39:12'),(1,75,'2026-10-03 12:39:12'),(1,77,'2026-10-03 12:39:12'),(1,78,'2026-10-03 12:39:12'),(1,79,'2026-10-03 12:39:12'),(1,80,'2026-10-03 12:39:12'),(1,81,'2026-10-03 12:39:12'),(1,82,'2026-10-03 13:11:09'),(1,83,'2026-10-03 13:11:09'),(1,84,'2026-10-03 13:11:09'),(1,85,'2026-10-03 13:11:09'),(1,86,'2026-10-05 12:55:41'),(1,87,'2026-10-05 12:55:41'),(1,88,'2026-10-05 12:55:41'),(1,89,'2026-10-05 12:55:41'),(1,90,'2026-10-05 12:55:41'),(1,91,'2026-10-05 12:55:41'),(2,1,'2026-10-05 12:10:43'),(2,2,'2026-10-05 12:10:43'),(2,4,'2026-10-05 12:10:43'),(2,5,'2026-10-05 12:10:43'),(2,6,'2026-10-05 12:10:43'),(2,7,'2026-10-05 12:10:43'),(2,8,'2026-10-05 12:10:43'),(2,9,'2026-10-05 12:10:43'),(2,10,'2026-10-05 12:10:43'),(2,12,'2026-10-05 12:10:43'),(2,14,'2026-10-05 12:10:43'),(2,15,'2026-10-05 12:10:43'),(2,16,'2026-10-05 12:10:43'),(2,17,'2026-10-05 12:10:43'),(2,18,'2026-10-05 12:10:43'),(2,19,'2026-10-05 12:10:43'),(2,20,'2026-10-05 12:10:43'),(2,21,'2026-10-05 12:10:43'),(2,22,'2026-10-05 12:10:43'),(2,23,'2026-10-05 12:10:43'),(2,24,'2026-10-05 12:10:43'),(2,25,'2026-10-05 12:10:43'),(2,26,'2026-10-05 12:10:43'),(2,27,'2026-10-05 12:10:43'),(2,28,'2026-10-05 12:10:43'),(2,29,'2026-10-05 12:10:43'),(2,30,'2026-10-05 12:10:43'),(2,31,'2026-10-05 12:10:43'),(2,33,'2026-10-05 12:10:43'),(2,34,'2026-10-05 12:10:43'),(2,35,'2026-10-05 12:10:43'),(2,36,'2026-10-05 12:10:43'),(2,37,'2026-10-05 12:10:43'),(2,38,'2026-10-05 12:10:43'),(2,40,'2026-10-05 12:10:43'),(2,41,'2026-10-05 12:10:43'),(2,43,'2026-10-05 12:10:43'),(2,44,'2026-10-05 12:10:43'),(2,45,'2026-10-05 12:10:43'),(2,46,'2026-10-05 12:10:43'),(2,47,'2026-10-05 12:10:43'),(2,48,'2026-10-05 12:10:43'),(2,49,'2026-10-05 12:10:43'),(2,50,'2026-10-05 12:10:43'),(2,51,'2026-10-05 12:10:43'),(2,52,'2026-10-05 12:10:43'),(2,53,'2026-10-05 12:10:43'),(2,54,'2026-10-05 12:10:43'),(2,55,'2026-10-05 12:10:43'),(2,56,'2026-10-05 12:10:43'),(2,57,'2026-10-05 12:10:43'),(2,58,'2026-10-05 12:10:43'),(2,59,'2026-10-05 12:10:43'),(2,60,'2026-10-05 12:10:43'),(2,61,'2026-10-05 12:10:43'),(2,62,'2026-10-05 12:10:43'),(2,63,'2026-10-05 12:10:43'),(2,64,'2026-10-05 12:10:43'),(2,65,'2026-10-05 12:10:43'),(2,66,'2026-10-05 12:10:43'),(2,72,'2026-10-05 12:10:43'),(2,73,'2026-10-05 12:10:43'),(2,74,'2026-10-05 12:10:43'),(2,75,'2026-10-05 12:10:43'),(2,77,'2026-10-05 12:10:43'),(2,78,'2026-10-05 12:10:43'),(2,79,'2026-10-05 12:10:43'),(2,80,'2026-10-05 12:10:43'),(2,81,'2026-10-05 12:10:43'),(2,82,'2026-10-05 12:10:43'),(2,83,'2026-10-05 12:10:43'),(2,84,'2026-10-05 12:10:43'),(2,85,'2026-10-05 12:10:43'),(2,86,'2026-10-05 12:55:41'),(2,87,'2026-10-05 12:55:41'),(2,88,'2026-10-05 12:55:41'),(2,89,'2026-10-05 12:55:41'),(2,90,'2026-10-05 12:55:41'),(2,91,'2026-10-05 12:55:41'),(3,1,'2026-10-01 17:44:43'),(3,14,'2026-10-03 14:17:02'),(3,18,'2026-10-03 14:17:02'),(3,24,'2026-10-01 17:44:43'),(3,25,'2026-10-01 17:44:43'),(3,28,'2026-10-01 17:44:43'),(3,29,'2026-10-01 17:44:43'),(3,30,'2026-10-01 17:44:43'),(3,31,'2026-10-01 17:44:43'),(3,33,'2026-10-01 17:44:43'),(3,36,'2026-10-01 17:44:43'),(3,37,'2026-10-01 17:44:43'),(3,38,'2026-10-01 17:44:43'),(3,39,'2026-10-03 14:17:02'),(3,40,'2026-10-01 17:44:43'),(3,41,'2026-10-01 17:44:43'),(3,43,'2026-10-01 17:44:43'),(3,54,'2026-10-01 17:44:43'),(3,55,'2026-10-01 17:44:43'),(3,61,'2026-10-03 14:17:02'),(3,84,'2026-10-03 14:17:02'),(3,86,'2026-10-05 12:55:41'),(3,87,'2026-10-05 12:55:41'),(3,89,'2026-10-05 12:55:41'),(4,1,'2026-10-07 17:30:59'),(4,14,'2026-10-07 17:30:59'),(4,18,'2026-10-07 17:30:59'),(4,22,'2026-10-07 17:30:59'),(4,24,'2026-10-07 17:30:59'),(4,25,'2026-10-07 17:30:59'),(4,26,'2026-10-07 17:30:59'),(4,28,'2026-10-07 17:30:59'),(4,29,'2026-10-07 17:30:59'),(4,30,'2026-10-07 17:30:59'),(4,31,'2026-10-07 17:30:59'),(4,33,'2026-10-07 17:30:59'),(4,34,'2026-10-07 17:30:59'),(4,36,'2026-10-07 17:30:59'),(4,37,'2026-10-07 17:30:59'),(4,38,'2026-10-07 17:30:59'),(4,43,'2026-10-07 17:30:59'),(4,54,'2026-10-07 17:30:59'),(4,55,'2026-10-07 17:30:59'),(4,61,'2026-10-07 17:30:59'),(4,84,'2026-10-07 17:30:59'),(4,87,'2026-10-07 17:30:59'),(4,89,'2026-10-07 17:30:59'),(5,1,'2026-10-01 17:44:43'),(5,14,'2026-10-01 17:44:43'),(5,18,'2026-10-01 17:44:43'),(5,33,'2026-10-01 17:44:43'),(5,34,'2026-10-01 17:44:43'),(5,35,'2026-10-01 17:44:43'),(5,44,'2026-10-01 17:44:43'),(5,52,'2026-10-01 17:44:43'),(5,53,'2026-10-01 17:44:43'),(5,61,'2026-10-05 17:19:06'),(5,90,'2026-10-05 12:55:41'),(5,91,'2026-10-05 12:55:41'),(6,1,'2026-10-01 17:44:43'),(6,18,'2026-10-01 17:44:43'),(6,44,'2026-10-01 17:44:43'),(6,45,'2026-10-01 17:44:43'),(6,46,'2026-10-01 17:44:43'),(6,47,'2026-10-01 17:44:43'),(6,48,'2026-10-01 17:44:43'),(6,50,'2026-10-01 17:44:43'),(6,51,'2026-10-01 17:44:43'),(6,52,'2026-10-01 17:44:43'),(6,53,'2026-10-01 17:44:43'),(6,57,'2026-10-01 17:44:43'),(6,61,'2026-10-06 13:13:21');
/*!40000 ALTER TABLE `role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` tinyint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_roles_slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Administrator','admin','Full system access across all branches',1,'2026-10-01 17:44:42','2026-10-01 17:44:42'),(2,'Manager','manager','Branch management and reporting access',1,'2026-10-01 17:44:42','2026-10-01 17:44:42'),(3,'Cashier','cashier','Billing, payments and POS access',1,'2026-10-01 17:44:42','2026-10-01 17:44:42'),(4,'Waiter','waiter','Table service, order taking and KOT',1,'2026-10-01 17:44:42','2026-10-01 17:44:42'),(5,'Chef','chef','Kitchen display, KOT management and recipes',1,'2026-10-01 17:44:42','2026-10-01 17:44:42'),(6,'Inventory Staff','inventory_staff','Inventory, purchases and GRN management',1,'2026-10-01 17:44:42','2026-10-01 17:44:42');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` smallint unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` int unsigned NOT NULL,
  `branch_id` int unsigned DEFAULT NULL,
  `group` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `key` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `value_type` enum('string','integer','decimal','boolean','json') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string',
  `label` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  `updated_by` int unsigned DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_settings_scope_key` (`restaurant_id`,`branch_id`,`key`),
  KEY `idx_settings_branch` (`branch_id`),
  KEY `idx_settings_group` (`group`),
  CONSTRAINT `fk_settings_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_settings_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,1,NULL,'general','restaurant_name','Kichu Khon 🍱','string','Restaurant Name',NULL,0,1,'2026-10-03 15:04:13'),(2,1,NULL,'general','currency_code','INR','string','Currency Code',NULL,0,1,'2026-10-03 11:52:51'),(3,1,NULL,'general','currency_symbol','₹','string','Currency Symbol',NULL,0,1,'2026-10-03 11:52:51'),(4,1,NULL,'billing','service_charge_pct','5','string','Service Charge %',NULL,0,1,'2026-10-03 11:52:51'),(5,1,NULL,'billing','default_tax_inclusive','0','boolean','Prices Include Tax',NULL,0,NULL,'2026-10-01 17:44:43'),(6,1,NULL,'billing','bill_prefix','INV','string','Invoice Prefix',NULL,0,1,'2026-10-03 11:52:51'),(7,1,NULL,'billing','print_footer_note','Thank you for dining with us!','string','Receipt Footer',NULL,0,1,'2026-10-03 11:52:51'),(8,1,NULL,'order','kot_auto_print','1','string','Auto-print KOT',NULL,0,1,'2026-10-03 11:52:51'),(9,1,NULL,'order','delivery_charge_flat','40','string','Default Delivery Charge',NULL,0,1,'2026-10-03 11:52:51'),(10,1,NULL,'notification','low_stock_threshold','10','string','Low Stock Alert Threshold',NULL,0,1,'2026-10-03 11:52:51'),(11,1,NULL,'security','max_login_attempts','5','string','Max Failed Login Attempts',NULL,0,1,'2026-10-03 11:52:51'),(12,1,NULL,'security','lockout_minutes','15','string','Account Lockout Duration (min)',NULL,0,1,'2026-10-03 11:52:51'),(13,1,NULL,'security','session_timeout_min','120','string','Session Timeout (min)',NULL,0,1,'2026-10-03 11:52:51');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shifts`
--

DROP TABLE IF EXISTS `shifts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `shifts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` int unsigned NOT NULL DEFAULT '1',
  `branch_id` int unsigned NOT NULL DEFAULT '1',
  `name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `break_duration_mins` smallint unsigned NOT NULL DEFAULT '30',
  `color_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#3b82f6',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_shifts_branch` (`branch_id`,`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shifts`
--

LOCK TABLES `shifts` WRITE;
/*!40000 ALTER TABLE `shifts` DISABLE KEYS */;
INSERT INTO `shifts` VALUES (1,1,1,'Morning Prep & Lunch','08:00:00','16:00:00',45,'#3b82f6',1,'2026-10-03 12:38:44','2026-10-03 12:38:44'),(2,1,1,'Evening Dinner Rush','16:00:00','00:00:00',45,'#f59e0b',1,'2026-10-03 12:38:44','2026-10-03 12:38:44'),(3,1,1,'Late Night Cleaning','22:00:00','04:00:00',30,'#8b5cf6',1,'2026-10-03 12:38:44','2026-10-03 12:38:44'),(4,1,1,'Weekend Brunch Shift','10:00:00','15:00:00',30,'#10b981',1,'2026-10-03 12:52:00','2026-10-03 12:52:00');
/*!40000 ALTER TABLE `shifts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staff_attendance`
--

DROP TABLE IF EXISTS `staff_attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `staff_attendance` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `branch_id` int unsigned NOT NULL DEFAULT '1',
  `shift_id` int unsigned DEFAULT NULL,
  `date` date NOT NULL,
  `check_in` datetime NOT NULL,
  `check_out` datetime DEFAULT NULL,
  `total_hours` decimal(5,2) NOT NULL DEFAULT '0.00',
  `overtime_hours` decimal(5,2) NOT NULL DEFAULT '0.00',
  `status` enum('present','late','half_day','absent','on_leave') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'present',
  `check_in_ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `check_out_ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_att_user_date` (`user_id`,`date`),
  KEY `idx_att_branch_date` (`branch_id`,`date`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff_attendance`
--

LOCK TABLES `staff_attendance` WRITE;
/*!40000 ALTER TABLE `staff_attendance` DISABLE KEYS */;
INSERT INTO `staff_attendance` VALUES (1,2,1,1,'2026-10-03','2026-10-03 12:52:00','2026-10-03 12:52:01',0.00,0.00,'late','::1','::1',NULL,'2026-10-03 12:52:00','2026-10-03 12:52:01'),(2,3,1,4,'2026-10-05','2026-10-05 14:54:24',NULL,0.00,0.00,'late','::1',NULL,NULL,'2026-10-05 14:54:24','2026-10-05 14:54:24'),(3,4,1,NULL,'2026-10-05','2026-10-05 17:19:13',NULL,0.00,0.00,'present','::1',NULL,NULL,'2026-10-05 17:19:13','2026-10-05 17:19:13'),(4,5,1,1,'2026-10-05','2026-10-05 17:52:45',NULL,0.00,0.00,'late','::1',NULL,NULL,'2026-10-05 17:52:45','2026-10-05 17:52:45'),(5,5,1,NULL,'2026-10-06','2026-10-06 10:50:43',NULL,0.00,0.00,'present','::1',NULL,NULL,'2026-10-06 10:50:43','2026-10-06 10:50:43'),(6,4,1,1,'2026-10-06','2026-10-06 12:54:55','2026-10-06 17:56:44',5.03,0.00,'late','::1','::1',NULL,'2026-10-06 12:54:55','2026-10-06 17:56:44'),(7,3,1,NULL,'2026-10-06','2026-10-06 13:16:18',NULL,0.00,0.00,'present','::1',NULL,NULL,'2026-10-06 13:16:18','2026-10-06 13:16:18'),(8,1,1,NULL,'2026-10-06','2026-10-06 13:22:31',NULL,0.00,0.00,'present','::1',NULL,NULL,'2026-10-06 13:22:31','2026-10-06 13:22:31'),(9,9,1,NULL,'2026-10-06','2026-10-06 17:38:37','2026-10-06 17:54:26',0.26,0.00,'present','::1','::1',NULL,'2026-10-06 17:38:37','2026-10-06 17:54:26'),(10,1,1,NULL,'2026-10-07','2026-10-07 10:31:06','2026-10-07 16:00:36',5.49,0.00,'present','::1','::1',NULL,'2026-10-07 10:31:06','2026-10-07 16:00:36'),(11,4,1,NULL,'2026-10-07','2026-10-07 11:22:54','2026-10-07 11:22:56',0.00,0.00,'present','::1','::1',NULL,'2026-10-07 11:22:54','2026-10-07 11:22:56'),(12,2,1,NULL,'2026-10-07','2026-10-07 11:40:18',NULL,0.00,0.00,'present','::1',NULL,NULL,'2026-10-07 11:40:18','2026-10-07 11:40:18'),(13,3,1,NULL,'2026-10-07','2026-10-07 11:45:12',NULL,0.00,0.00,'present','::1',NULL,NULL,'2026-10-07 11:45:12','2026-10-07 11:45:12'),(14,5,1,NULL,'2026-10-08','2026-10-08 10:39:44',NULL,0.00,0.00,'present','::1',NULL,NULL,'2026-10-08 10:39:44','2026-10-08 10:39:44'),(15,1,1,NULL,'2026-10-08','2026-10-08 11:56:00',NULL,0.00,0.00,'present','::1',NULL,NULL,'2026-10-08 11:56:00','2026-10-08 11:56:00'),(16,4,1,NULL,'2026-10-08','2026-10-08 11:57:58',NULL,0.00,0.00,'present','::1',NULL,NULL,'2026-10-08 11:57:58','2026-10-08 11:57:58'),(17,2,1,NULL,'2026-10-08','2026-10-08 12:25:02',NULL,0.00,0.00,'present','::1',NULL,NULL,'2026-10-08 12:25:02','2026-10-08 12:25:02');
/*!40000 ALTER TABLE `staff_attendance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staff_performance_reviews`
--

DROP TABLE IF EXISTS `staff_performance_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `staff_performance_reviews` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `branch_id` int unsigned NOT NULL DEFAULT '1',
  `reviewer_id` int unsigned NOT NULL,
  `review_period` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating_attendance` tinyint unsigned NOT NULL DEFAULT '5',
  `rating_punctuality` tinyint unsigned NOT NULL DEFAULT '5',
  `rating_order_accuracy` tinyint unsigned NOT NULL DEFAULT '5',
  `rating_hospitality` tinyint unsigned NOT NULL DEFAULT '5',
  `overall_score` decimal(3,2) NOT NULL DEFAULT '5.00',
  `strengths` text COLLATE utf8mb4_unicode_ci,
  `areas_for_improvement` text COLLATE utf8mb4_unicode_ci,
  `goals` text COLLATE utf8mb4_unicode_ci,
  `review_date` date NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_spr_user` (`user_id`),
  KEY `idx_spr_branch_date` (`branch_id`,`review_date`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff_performance_reviews`
--

LOCK TABLES `staff_performance_reviews` WRITE;
/*!40000 ALTER TABLE `staff_performance_reviews` DISABLE KEYS */;
INSERT INTO `staff_performance_reviews` VALUES (1,2,1,1,'October 2026 Monthly Appraisal',5,4,5,5,4.80,'Exceptional punctuality, great team leadership, zero order void errors.','Continue cross-training kitchen station expediting.','Complete advanced POS inventory auditing certification.','2026-10-07','2026-10-03 12:52:03','2026-10-07 17:35:07'),(2,4,1,1,'October 2026 Appraisal',4,2,5,5,4.15,'','','','2026-10-05','2026-10-05 15:03:36','2026-10-05 15:03:36'),(3,5,1,1,'October 2026 Appraisal',3,2,2,2,2.25,'','','','2026-10-05','2026-10-05 15:04:35','2026-10-05 15:20:32'),(4,4,1,1,'October 2026 Appraisal',4,4,3,5,3.95,'very good hospitality and very good customer handeling','you have to maximize your accuracy and make sure what is goal','','2026-10-07','2026-10-05 15:05:18','2026-10-07 17:52:18');
/*!40000 ALTER TABLE `staff_performance_reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staff_shifts`
--

DROP TABLE IF EXISTS `staff_shifts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `staff_shifts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `shift_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  `branch_id` int unsigned NOT NULL DEFAULT '1',
  `shift_date` date NOT NULL,
  `status` enum('scheduled','completed','swapped','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'scheduled',
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ss_date_user` (`shift_date`,`user_id`),
  KEY `idx_ss_branch` (`branch_id`,`shift_date`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff_shifts`
--

LOCK TABLES `staff_shifts` WRITE;
/*!40000 ALTER TABLE `staff_shifts` DISABLE KEYS */;
INSERT INTO `staff_shifts` VALUES (1,2,4,1,'2026-10-06','scheduled','','2026-10-06 12:55:29','2026-10-06 12:55:29');
/*!40000 ALTER TABLE `staff_shifts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_adjustments`
--

DROP TABLE IF EXISTS `stock_adjustments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_adjustments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` int unsigned DEFAULT '1',
  `inventory_item_id` int unsigned NOT NULL,
  `adjustment_type` enum('in','out','reconciliation') COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(12,3) NOT NULL,
  `previous_stock` decimal(12,3) NOT NULL,
  `new_stock` decimal(12,3) NOT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `adjusted_by` int unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sa_item` (`inventory_item_id`),
  CONSTRAINT `fk_sa_item` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_adjustments`
--

LOCK TABLES `stock_adjustments` WRITE;
/*!40000 ALTER TABLE `stock_adjustments` DISABLE KEYS */;
INSERT INTO `stock_adjustments` VALUES (1,1,9,'in',5.000,15.000,20.000,'Received bonus tasting sample carton',1,'2026-10-03 11:40:32'),(2,1,1,'out',0.300,18.500,18.200,'Recipe consumption for Order ORD-20261003-0001 (Qty: 2)',NULL,'2026-10-03 11:53:33'),(3,1,2,'out',0.400,24.000,23.600,'Recipe consumption for Order ORD-20261003-0001 (Qty: 2)',NULL,'2026-10-03 11:53:33'),(4,1,3,'out',0.500,45.000,44.500,'Recipe consumption for Order ORD-20261003-0001 (Qty: 2)',NULL,'2026-10-03 11:53:33'),(5,1,9,'out',0.100,38.000,37.900,'Recipe consumption for Order ORD-20261003-0001 (Qty: 2)',NULL,'2026-10-03 11:53:33'),(6,1,1,'out',0.150,18.200,18.050,'Recipe consumption for Order ORD-20261003-0002 (Qty: 1)',NULL,'2026-10-03 12:20:03'),(7,1,2,'out',0.200,23.600,23.400,'Recipe consumption for Order ORD-20261003-0002 (Qty: 1)',NULL,'2026-10-03 12:20:03'),(8,1,3,'out',0.250,44.500,44.250,'Recipe consumption for Order ORD-20261003-0002 (Qty: 1)',NULL,'2026-10-03 12:20:03'),(9,1,9,'out',0.050,37.900,37.850,'Recipe consumption for Order ORD-20261003-0002 (Qty: 1)',NULL,'2026-10-03 12:20:03'),(10,1,10,'out',5.000,10.000,5.000,'used',1,'2026-10-03 14:49:02'),(11,1,10,'in',10.000,5.000,15.000,'ready to use',1,'2026-10-05 17:24:43'),(12,1,9,'out',10.000,37.850,27.850,'Recipe consumption for Order ORD-20261005-0007 (Qty: 1)',NULL,'2026-10-05 17:29:47'),(13,1,1,'out',0.150,18.050,17.900,'Recipe consumption for Order ORD-20261005-0007 (Qty: 1)',NULL,'2026-10-05 17:29:47'),(14,1,2,'out',0.200,23.400,23.200,'Recipe consumption for Order ORD-20261005-0007 (Qty: 1)',NULL,'2026-10-05 17:29:47'),(15,1,3,'out',0.250,54.250,54.000,'Recipe consumption for Order ORD-20261005-0007 (Qty: 1)',NULL,'2026-10-05 17:29:47'),(16,1,9,'out',0.050,27.850,27.800,'Recipe consumption for Order ORD-20261005-0007 (Qty: 1)',NULL,'2026-10-05 17:29:47'),(17,1,1,'out',0.150,17.900,17.750,'Recipe consumption for Order ORD-20261007-0005 (Qty: 1)',NULL,'2026-10-07 13:08:19'),(18,1,2,'out',0.200,23.200,23.000,'Recipe consumption for Order ORD-20261007-0005 (Qty: 1)',NULL,'2026-10-07 13:08:19'),(19,1,3,'out',0.250,54.000,53.750,'Recipe consumption for Order ORD-20261007-0005 (Qty: 1)',NULL,'2026-10-07 13:08:19'),(20,1,9,'out',0.050,27.800,27.750,'Recipe consumption for Order ORD-20261007-0005 (Qty: 1)',NULL,'2026-10-07 13:08:20'),(21,1,9,'out',10.000,27.750,17.750,'Recipe consumption for Order ORD-20261008-0002 (Qty: 1)',NULL,'2026-10-08 10:45:35'),(22,1,9,'out',10.000,17.750,7.750,'Recipe consumption for Order ORD-20261008-0004 (Qty: 1)',NULL,'2026-10-08 11:21:21'),(23,1,9,'out',10.000,10.000,0.000,'Recipe consumption for Order ORD-20261008-0005 (Qty: 1)',NULL,'2026-10-08 11:36:20'),(24,1,9,'out',10.000,10.000,0.000,'Recipe consumption for Order ORD-20261008-0007 (Qty: 1)',NULL,'2026-10-08 12:21:23');
/*!40000 ALTER TABLE `stock_adjustments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suppliers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` int unsigned NOT NULL DEFAULT '1',
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_person` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `tax_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_terms` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'Net 30',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_suppliers_rest` (`restaurant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES (1,1,'Golden Valley Farm Products','Mark Spencer','orders@goldenvalley.local','+1 555-0144','42 Farm Road, Suburbia','TAX-GV-98421','Net 15',1,'2026-10-03 11:29:44','2026-10-03 11:29:44'),(2,1,'Oceanic Fresh & Meats Supply','Sarah Jenkins','sales@oceanicmeats.local','+1 555-0188','10 Harbour Bay Blvd','TAX-OC-54129','Net 30',1,'2026-10-03 11:29:44','2026-10-03 11:29:44'),(3,1,'Artisan Bakehouse & Mills','David Miller','david@artisanmills.local','+1 555-0199','88 Grain District Way','TAX-AB-77123','Cash on Delivery',1,'2026-10-03 11:29:44','2026-10-03 11:29:44'),(4,1,'Apex Dairy & Butter Co.','Robert Vance','sales@apexdairy.local','+1 555-0988','50 Industrial Dairy Road','TAX-APX-1102','Net 15',1,'2026-10-03 11:40:31','2026-10-03 11:40:31'),(5,1,'sukhesh jana','','','4152631234','delivery of chicken,mutton ,','GSTIN415263789','Cash on Delivery',1,'2026-10-06 12:08:31','2026-10-06 12:08:31');
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tax_rates`
--

DROP TABLE IF EXISTS `tax_rates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tax_rates` (
  `id` smallint unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` int unsigned NOT NULL,
  `branch_id` int unsigned DEFAULT NULL,
  `name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `type` enum('inclusive','exclusive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'exclusive',
  `applies_to` enum('food','beverage','service','all') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'all',
  `is_compound` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_tax_restaurant` (`restaurant_id`),
  KEY `idx_tax_branch` (`branch_id`),
  CONSTRAINT `fk_tax_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_tax_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tax_rates`
--

LOCK TABLES `tax_rates` WRITE;
/*!40000 ALTER TABLE `tax_rates` DISABLE KEYS */;
INSERT INTO `tax_rates` VALUES (1,1,NULL,'GST 5%',5.00,'exclusive','food',0,1,'2026-10-01 17:44:43','2026-10-01 17:44:43'),(2,1,NULL,'GST 12%',12.00,'exclusive','food',0,1,'2026-10-01 17:44:43','2026-10-01 17:44:43'),(3,1,NULL,'GST 18%',18.00,'exclusive','beverage',0,1,'2026-10-01 17:44:43','2026-10-01 17:44:43'),(4,1,NULL,'Service Charge 5%',5.00,'exclusive','service',0,1,'2026-10-01 17:44:43','2026-10-01 17:44:43');
/*!40000 ALTER TABLE `tax_rates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_permissions`
--

DROP TABLE IF EXISTS `user_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_permissions` (
  `user_id` int unsigned NOT NULL,
  `permission_id` smallint unsigned NOT NULL,
  `granted` tinyint(1) NOT NULL DEFAULT '1',
  `granted_by` int unsigned DEFAULT NULL,
  `granted_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`,`permission_id`),
  KEY `idx_up_permission` (`permission_id`),
  CONSTRAINT `fk_up_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_up_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_permissions`
--

LOCK TABLES `user_permissions` WRITE;
/*!40000 ALTER TABLE `user_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_sessions`
--

DROP TABLE IF EXISTS `user_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_sessions` (
  `id` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_agent` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data` blob,
  `last_activity` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_sessions_user` (`user_id`),
  KEY `idx_sessions_activity` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_sessions`
--

LOCK TABLES `user_sessions` WRITE;
/*!40000 ALTER TABLE `user_sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` int unsigned NOT NULL,
  `branch_id` int unsigned DEFAULT NULL,
  `role_id` tinyint unsigned NOT NULL,
  `employee_id` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` enum('male','female','other','prefer_not_to_say') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `emergency_contact` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `last_login_at` datetime DEFAULT NULL,
  `last_login_ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `failed_login_count` tinyint unsigned NOT NULL DEFAULT '0',
  `locked_until` datetime DEFAULT NULL,
  `password_changed_at` datetime DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `deactivated_at` datetime DEFAULT NULL,
  `deactivated_by` int unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email` (`email`),
  KEY `idx_users_restaurant` (`restaurant_id`),
  KEY `idx_users_branch` (`branch_id`),
  KEY `idx_users_role` (`role_id`),
  KEY `idx_users_active` (`is_active`),
  CONSTRAINT `fk_users_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_users_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,1,NULL,1,NULL,'System','Admin','admin@rms.local',NULL,'$2y$10$QsipypSwQ89sURgEx2bFMe6YCyAMHZr5LtKwTxjBaLb6mkdgCKepK',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-10-08 10:40:02','::1',0,NULL,NULL,NULL,NULL,1,NULL,NULL,'2026-10-01 17:44:43','2026-10-08 10:40:02'),(2,1,1,2,'EMP-101','Rahul','Sharma','manager.cp@rms.local','+91 9876543210','$2y$12$/bUljQ2bKPfyraSeWDpFX.Sa3mclUerUnwVI0.FljJFJroRgteG/y',NULL,NULL,NULL,NULL,NULL,NULL,'2026-10-01','2026-10-08 12:22:25','::1',0,NULL,NULL,NULL,NULL,1,NULL,NULL,'2026-10-01 17:51:37','2026-10-08 12:22:25'),(3,1,1,3,'EMP-01','sudesh','kumar','sudesh@gmail.com','1478523698','$2y$12$VX8WbMt1MaNpQXdufhw.vui9iEDhjnWz8ckVIJr9HXmMEKIB6c0XW',NULL,NULL,NULL,NULL,NULL,NULL,'2026-10-15','2026-10-07 11:44:24','::1',0,NULL,NULL,NULL,NULL,1,NULL,NULL,'2026-10-03 13:22:30','2026-10-07 11:44:24'),(4,1,1,4,'EMP-03','subh','singh','subh@gmail.com','1236547896','$2y$12$xW1V8CnTKMCsvRjjNn/mfemz/lFdqGOReUU9fsKA6Dcn/72T883a6',NULL,NULL,NULL,NULL,NULL,NULL,'2026-10-20','2026-10-08 11:57:38','::1',0,NULL,NULL,NULL,NULL,1,NULL,NULL,'2026-10-03 14:09:08','2026-10-08 11:57:38'),(5,1,1,5,'EMP-05','Vikram','Chef','chef@rms.local','9876543210','$2y$12$ihIq1DLqs3ewm/ohgaN0WuApSuAG8/JJZhWp/Rcc5rXv5D5qmVaiu',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-10-08 10:39:24','::1',0,NULL,NULL,NULL,NULL,1,NULL,NULL,'2026-10-05 12:25:51','2026-10-08 10:39:24'),(6,1,NULL,3,NULL,'Test','Staff','staff1791191770@rms.local',NULL,'$2y$12$Xo/2JsHWFwJEv2AWqPDieuOM7HI2P5ThE4R2K3ZiD9zb.Op/v9Noy',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,1,NULL,NULL,'2026-10-05 14:46:10','2026-10-05 14:46:10'),(7,1,NULL,3,NULL,'Anand','Kumar','staff1791191853@rms.local','9876543210','$2y$12$kiqPkKMpe1lcN8PTDAi4xep3bUJGdI5Jg8G/UhC.1x51gLvgWalyC',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,1,NULL,NULL,'2026-10-05 14:47:33','2026-10-05 14:47:33'),(8,1,3,4,'EMP-09','Rishav','dalla','rishav@gmail.com','+919345671944','$2y$12$TCjsoS7G7kU2PJROeGqKdeiWiGMDhzdvV7ECSRcjYL9KPrm/aMduu',NULL,NULL,NULL,NULL,NULL,NULL,'2026-10-05','2026-10-05 14:52:16','::1',0,NULL,NULL,NULL,NULL,1,NULL,NULL,'2026-10-05 14:49:28','2026-10-05 14:52:16'),(9,1,1,6,'EMP-11','Riya','paul','riya@gmail.com','+919887654321','$2y$12$7rL9zku1.U.7TwlQfUFemOc/9eR4LNOgXnmhqQgI66OIzJJP3x1wu',NULL,NULL,NULL,NULL,NULL,NULL,'2026-10-06','2026-10-06 17:37:57','::1',0,NULL,NULL,NULL,NULL,1,NULL,NULL,'2026-10-06 11:28:16','2026-10-06 17:37:57'),(10,1,NULL,2,'EMP-10','karan ','upadhyay','karan@gmail.com','7679199805','$2y$12$R.9TosSlKoaOZMUqPA0q1uXUBjEJig3x22pqKn0HBJTYd8UQF9ovC',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-10-07 12:58:55','::1',0,NULL,NULL,NULL,NULL,1,NULL,NULL,'2026-10-07 12:49:38','2026-10-07 12:58:55');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `waste_logs`
--

DROP TABLE IF EXISTS `waste_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `waste_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` int unsigned DEFAULT '1',
  `inventory_item_id` int unsigned NOT NULL,
  `quantity` decimal(12,3) NOT NULL,
  `cost_impact` decimal(10,2) NOT NULL DEFAULT '0.00',
  `waste_reason` enum('spoilage','expired','damaged','burnt','prep_error','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'spoilage',
  `logged_by` int unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_wl_item` (`inventory_item_id`),
  KEY `idx_wl_reason` (`waste_reason`),
  CONSTRAINT `fk_wl_item` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `waste_logs`
--

LOCK TABLES `waste_logs` WRITE;
/*!40000 ALTER TABLE `waste_logs` DISABLE KEYS */;
INSERT INTO `waste_logs` VALUES (1,1,9,2.000,9.00,'damaged',1,'Accidental drop during kitchen prep','2026-10-03 11:40:33'),(2,1,6,1.000,9.00,'expired',1,'','2026-10-05 17:25:55'),(3,1,4,0.500,3.25,'burnt',1,'half kilo of chicken burnt very badly ','2026-10-06 13:23:39');
/*!40000 ALTER TABLE `waste_logs` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-08 16:02:52

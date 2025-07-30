CREATE DATABASE  IF NOT EXISTS `navys` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `navys`;
-- MySQL dump 10.13  Distrib 8.0.33, for Win64 (x86_64)
--
-- Host: hopper.proxy.rlwy.net    Database: navys
-- ------------------------------------------------------
-- Server version	9.4.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('laravel-cache-45182af75a896f71caf3df1445223430','i:1;',1753591468),('laravel-cache-45182af75a896f71caf3df1445223430:timer','i:1753591468;',1753591468),('laravel-cache-533c2726343317d301713892fb773971','i:3;',1753591098),('laravel-cache-533c2726343317d301713892fb773971:timer','i:1753591098;',1753591098),('laravel-cache-94d92f976fd06fd3e8cf53ec4e03d646','i:1;',1753429021),('laravel-cache-94d92f976fd06fd3e8cf53ec4e03d646:timer','i:1753429021;',1753429021),('laravel-cache-9ca3d0dbed62e8de0b4b26f55ead3d78','i:1;',1753459176),('laravel-cache-9ca3d0dbed62e8de0b4b26f55ead3d78:timer','i:1753459176;',1753459176),('laravel-cache-ad4954e2e38bb42a3ba5cbc5eebbbdbc','i:1;',1753591462),('laravel-cache-ad4954e2e38bb42a3ba5cbc5eebbbdbc:timer','i:1753591462;',1753591462),('laravel-cache-ca3a71b552c6638ed31151adcbc1b06d','i:1;',1753676055),('laravel-cache-ca3a71b552c6638ed31151adcbc1b06d:timer','i:1753676055;',1753676055),('laravel-cache-d2bfa8e8b749d2772a21edee7b70a2b3','i:3;',1753500209),('laravel-cache-d2bfa8e8b749d2772a21edee7b70a2b3:timer','i:1753500209;',1753500209),('laravel-cache-e7cf66797159dc3cd3e85f72e15bb551','i:17;',1753459249),('laravel-cache-e7cf66797159dc3cd3e85f72e15bb551:timer','i:1753459249;',1753459249),('laravel-cache-f1f70ec40aaa556905d4a030501c0ba4','i:2;',1753676295),('laravel-cache-f1f70ec40aaa556905d4a030501c0ba4:timer','i:1753676295;',1753676295);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cart_items`
--

DROP TABLE IF EXISTS `cart_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cart_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `product_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `size` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cart_items_user_id_product_id_size_color_unique` (`user_id`,`product_id`,`size`,`color`),
  KEY `cart_items_user_id_index` (`user_id`),
  KEY `cart_items_product_id_index` (`product_id`),
  CONSTRAINT `cart_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cart_items_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart_items`
--

LOCK TABLES `cart_items` WRITE;
/*!40000 ALTER TABLE `cart_items` DISABLE KEYS */;
INSERT INTO `cart_items` VALUES (15,1,'1e543020-f792-49a0-98f8-5a9671a2348d',1,'M','color 1',1000.00,'2025-07-28 04:09:26','2025-07-28 04:09:26');
/*!40000 ALTER TABLE `cart_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `favorites`
--

DROP TABLE IF EXISTS `favorites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `favorites` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `product_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `favorites_user_id_product_id_unique` (`user_id`,`product_id`),
  KEY `favorites_user_id_index` (`user_id`),
  KEY `favorites_product_id_index` (`product_id`),
  CONSTRAINT `favorites_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `favorites_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `favorites`
--

LOCK TABLES `favorites` WRITE;
/*!40000 ALTER TABLE `favorites` DISABLE KEYS */;
INSERT INTO `favorites` VALUES (7,1,'1e543020-f792-49a0-98f8-5a9671a2348d','2025-07-23 11:52:32','2025-07-23 11:52:32');
/*!40000 ALTER TABLE `favorites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (2,'0001_01_01_000002_create_jobs_table',1),(3,'2025_07_22_012038_create_personal_access_tokens_table',1),(4,'2025_07_22_020839_product',1),(5,'2025_07_22_020856_user',1),(6,'2024_01_01_000002_create_sessions_table',2),(7,'2024_01_15_000001_create_favorites_table',3),(8,'2024_01_15_000002_create_cart_items_table',3),(9,'2024_01_20_000001_create_orders_table',4),(10,'2024_01_20_000002_create_order_items_table',4),(11,'0001_01_01_000001_create_cache_table',5);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `product_image` json DEFAULT NULL,
  `size` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_index` (`order_id`),
  KEY `order_items_product_id_index` (`product_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES ('00456aed-2128-4fe9-aad1-9ac6fc93d08b','aadc12bd-1a4e-496b-b252-ae309ff31919','a08ed878-28f9-4b5c-b1f0-2507f2ad8177','Eclipse de Encanto','XV0021','Incluye Aro flexible y Crinolina','\"img/imgProducts/1753253750_000000_color_0_000000_1747691759454.webp\"','G','color 0',1,12500.00,12500.00,'2025-07-23 14:15:42','2025-07-23 14:15:42'),('2244f551-5134-467c-ad46-c42eb2cbfdd6','91cbfaa4-a693-42f6-aa79-798adbdf3382','1e543020-f792-49a0-98f8-5a9671a2348d','Luz de Nieve','12B','Vestido de bautizo con gorrito y balerina de manga larga al estilo colonial','\"img/imgProducts/1753228242_a8a09d_color_1_ECC98D_1748281796317.jpeg\"','XL','color 1',1,1000.00,1000.00,'2025-07-23 13:18:32','2025-07-23 13:18:32'),('53ddb302-1ba9-4e78-b733-6aebb438d524','91cbfaa4-a693-42f6-aa79-798adbdf3382','1e543020-f792-49a0-98f8-5a9671a2348d','Luz de Nieve','12B','Vestido de bautizo con gorrito y balerina de manga larga al estilo colonial','\"img/imgProducts/1753228242_a8a09d_color_1_ECC98D_1748281796317.jpeg\"','12','Ffffff Color 2',1,1000.00,1000.00,'2025-07-23 13:18:32','2025-07-23 13:18:32'),('6fb57050-51b5-4a97-a865-a4b2e9253c2e','91cbfaa4-a693-42f6-aa79-798adbdf3382','1e543020-f792-49a0-98f8-5a9671a2348d','Luz de Nieve','12B','Vestido de bautizo con gorrito y balerina de manga larga al estilo colonial','\"img/imgProducts/1753228242_a8a09d_color_1_ECC98D_1748281796317.jpeg\"','M',NULL,1,1000.00,1000.00,'2025-07-23 13:18:32','2025-07-23 13:18:32'),('85143802-786f-433b-b862-77b1dc7fc00b','7ada08a5-d376-410b-9e3d-4311ee9082c3','a08ed878-28f9-4b5c-b1f0-2507f2ad8177','Eclipse de Encanto','XV0021','Incluye Aro flexible y Crinolina','\"img/imgProducts/1753253750_000000_color_0_000000_1747691759454.webp\"','G','color 0',1,12500.00,12500.00,'2025-07-26 03:24:01','2025-07-26 03:24:01'),('94334c33-236b-4f87-af30-f2f9f6aa37b6','91cbfaa4-a693-42f6-aa79-798adbdf3382','1e543020-f792-49a0-98f8-5a9671a2348d','Luz de Nieve','12B','Vestido de bautizo con gorrito y balerina de manga larga al estilo colonial','\"img/imgProducts/1753228242_a8a09d_color_1_ECC98D_1748281796317.jpeg\"','12','color 1',1,1000.00,1000.00,'2025-07-23 13:18:32','2025-07-23 13:18:32'),('b7496335-e437-4b60-a970-d8ed6a792230','d95b3049-6cde-46f0-8b6f-b7b6eb846f34','1e543020-f792-49a0-98f8-5a9671a2348d','Luz de Nieve','12B','Vestido de bautizo con gorrito y balerina de manga larga al estilo colonial','\"img/imgProducts/1753228242_a8a09d_color_1_ECC98D_1748281796317.jpeg\"','XL','color 2',1,1000.00,1000.00,'2025-07-27 03:16:13','2025-07-27 03:16:13'),('e3d08b6d-88ca-4769-9851-3198d43508e7','6a0489b9-f892-42df-82c4-3b1bd2bd3fc1','c8ff72ff-1352-4ef9-9b34-1da010b2f8a9','Rosa Eterna','XV0020','Incluye Aro flexible y Crinolina','\"img/imgProducts/1753254057_d10113_rojo_BA000A_1747691197731.webp\"','CH','rojo',1,13500.00,13500.00,'2025-07-25 22:48:25','2025-07-25 22:48:25');
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `order_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','confirmed','processing','shipped','delivered','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `subtotal` decimal(10,2) NOT NULL,
  `tax` decimal(10,2) NOT NULL DEFAULT '0.00',
  `shipping` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL,
  `shipping_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_state` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_postal_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'México',
  `payment_method` enum('cash','card','transfer','paypal') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `payment_status` enum('pending','paid','failed','refunded') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_date` timestamp NULL DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `admin_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `shipped_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_number_unique` (`order_number`),
  KEY `orders_status_created_at_index` (`status`,`created_at`),
  KEY `orders_order_number_index` (`order_number`),
  KEY `orders_user_id_index` (`user_id`),
  CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES ('6a0489b9-f892-42df-82c4-3b1bd2bd3fc1',1,'ORD-LJPQSXGU','pending',13500.00,0.00,0.00,0.00,13500.00,'Super','admin@admin.com','951155555','Privada de niños Heroes','Oaxaca','Oaxaca','68000','México','cash','pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-07-25 22:48:25','2025-07-25 22:48:25'),('7ada08a5-d376-410b-9e3d-4311ee9082c3',1,'ORD-D4XOE6B7','pending',12500.00,0.00,0.00,0.00,12500.00,'Super','iamkevinzg2000@gmail.com','951167888','Privada de niños Heroes','Oaxaca','Oaxaca','68000','México','cash','pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-07-26 03:24:01','2025-07-26 03:24:01'),('91cbfaa4-a693-42f6-aa79-798adbdf3382',1,'ORD-HQENJGSK','confirmed',4000.00,0.00,0.00,0.00,4000.00,'Super','admin@admin.com','951215455','Privada de niños Heroes','Oaxaca','Oaxaca','68000','México','card','pending',NULL,NULL,NULL,NULL,'2025-07-27 05:00:22',NULL,NULL,'2025-07-23 13:18:32','2025-07-27 05:00:22'),('aadc12bd-1a4e-496b-b252-ae309ff31919',1,'ORD-NNDIII0I','shipped',12500.00,0.00,0.00,0.00,12500.00,'Super','admin@admin.com','951215455','Privada de niños Heroes','Oaxaca','Oaxaca','68000','México','cash','pending',NULL,NULL,NULL,NULL,NULL,'2025-07-25 09:08:12',NULL,'2025-07-23 14:15:42','2025-07-25 09:08:12'),('d95b3049-6cde-46f0-8b6f-b7b6eb846f34',1,'ORD-QR1LFAOR','pending',1000.00,0.00,0.00,0.00,1000.00,'Super','21160805@itoaxaca.edu.mx','9513271977','gualaupe victoria mz 82 lote 14','oaxaca','Oaxaca','70704','México','transfer','pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-07-27 03:16:13','2025-07-27 03:16:13');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` VALUES (5,'App\\Models\\User',1,'auth_token','eea649e965d25e892e7e20c7ef617a618b02b1a2026ae30ede66416e4b01fbb2','[\"*\"]','2025-07-22 11:31:54',NULL,'2025-07-22 11:31:53','2025-07-22 11:31:54'),(24,'App\\Models\\User',1,'auth_token','eb5d7ae0ccb4894002bd136737fd71f6cb32ae58849c0ff9466e7dfadabb7ac7','[\"*\"]','2025-07-23 05:42:45',NULL,'2025-07-23 05:13:41','2025-07-23 05:42:45'),(25,'App\\Models\\User',1,'auth_token','61bedfa85c0c34683b3310975598b3a953b95f0572e7cef79743f1860fbdf70d','[\"*\"]','2025-07-23 11:52:51',NULL,'2025-07-23 05:49:45','2025-07-23 11:52:51'),(26,'App\\Models\\User',1,'auth_token','a8ec401bc3d00caa022ace98d1b527b6693629adb85e298078c4f7c0f11d9e2e','[\"*\"]','2025-07-28 04:18:07',NULL,'2025-07-23 12:28:49','2025-07-28 04:18:07'),(27,'App\\Models\\User',1,'auth_token','7f5f0b6fa3b803d64360bfcdee58074b2da8ad1b4f151b844aae908000f0bd33','[\"*\"]','2025-07-23 14:39:31',NULL,'2025-07-23 13:03:28','2025-07-23 14:39:31'),(30,'App\\Models\\User',1,'auth_token','57f85b1241318e6222410abe942b43c6b33d729a31d86b22b54e6a2d15faf347','[\"*\"]','2025-07-24 08:40:22',NULL,'2025-07-24 08:40:21','2025-07-24 08:40:22'),(34,'App\\Models\\User',3,'auth_token','edaf7fdc129eb75ad09aba271cbc7fa7cdd42c85a010d266700d30a081a29979','[\"*\"]','2025-07-24 11:38:23',NULL,'2025-07-24 09:44:46','2025-07-24 11:38:23'),(45,'App\\Models\\User',1,'auth_token','b869b1c6452fc0d4d2ab3f981973d3fc86dbd444999a919c27a9d0f7acdf96ee','[\"*\"]','2025-07-25 10:41:10',NULL,'2025-07-25 09:08:03','2025-07-25 10:41:10'),(50,'App\\Models\\User',1,'auth_token','ffeccea401a3b7ff2d28d57477f223455111b2e81d680c352495d3c31a0d707d','[\"*\"]','2025-07-25 13:23:29',NULL,'2025-07-25 09:35:56','2025-07-25 13:23:29'),(51,'App\\Models\\User',1,'auth_token','94960f6b560d8e6f322b99f2bc8d9b7159126aa7610baf181d8767ee83e11152','[\"*\"]','2025-07-25 13:36:07',NULL,'2025-07-25 13:36:02','2025-07-25 13:36:07'),(52,'App\\Models\\User',1,'auth_token','d422c63c4520e9965da85a69bb12bf3928497dc82cd7dbc66eb3ea1d7a9981cd','[\"*\"]','2025-07-25 09:08:12',NULL,'2025-07-25 08:50:32','2025-07-25 09:08:12'),(53,'App\\Models\\User',1,'auth_token','76b3fd4d3c5648ac310c9f89abef72fb57a1b68c8fe9818c2bb0c6a76f403538','[\"*\"]','2025-07-25 08:54:13',NULL,'2025-07-25 08:54:07','2025-07-25 08:54:13'),(55,'App\\Models\\User',1,'auth_token','8fdde05f72b25d4162e994d582ad7dda37b52ae2f45a9cad923752dfd2fd2aa8','[\"*\"]','2025-07-25 08:58:17',NULL,'2025-07-25 08:57:39','2025-07-25 08:58:17'),(56,'App\\Models\\User',1,'auth_token','e8ba16debb074b7663b83826d54139ca1486047d26e54037b3b2f9fbaa5657bd','[\"*\"]','2025-07-25 09:04:26',NULL,'2025-07-25 08:59:16','2025-07-25 09:04:26'),(57,'App\\Models\\User',6,'auth_token','0694156868be2d64bbe8989e2c1879405dd1a6f59252517a610134c8b305027f','[\"*\"]','2025-07-25 16:00:22',NULL,'2025-07-25 15:58:37','2025-07-25 16:00:22'),(59,'App\\Models\\User',1,'auth_token','9bb4270a0ba5815735c0fabf2a02ac40f751adbb84c04e645464b804cbeef992','[\"*\"]','2025-07-25 23:04:11',NULL,'2025-07-25 22:49:55','2025-07-25 23:04:11'),(61,'App\\Models\\User',1,'auth_token','cfb9aae1df616821e3201d8ba790f31362752dba392ab89b660233d8aa760734','[\"*\"]','2025-07-26 02:46:06',NULL,'2025-07-26 02:28:10','2025-07-26 02:46:06'),(65,'App\\Models\\User',1,'auth_token','a9c6a22e0504923576f7e89d596d90f38873ecf6d564ff2553e699b7a8c198dd','[\"*\"]','2025-07-26 03:24:44',NULL,'2025-07-26 03:23:02','2025-07-26 03:24:44'),(68,'App\\Models\\User',1,'auth_token','fdf3a27b7b89b60bea54e911aac970c64c7cfeee4ed613335d51a4cdf5c32895','[\"*\"]','2025-07-28 04:13:44',NULL,'2025-07-27 04:43:29','2025-07-28 04:13:44'),(69,'App\\Models\\User',1,'auth_token','8b1d49799a7313f68b29aaa57d082abef37e0d1fd1250007b7951450a622b973','[\"*\"]','2025-07-28 04:14:12',NULL,'2025-07-28 04:13:15','2025-07-28 04:14:12');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `images` json DEFAULT NULL,
  `sizes` json DEFAULT NULL,
  `size2` json DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int DEFAULT '0',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `supplier` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purchasePrice` decimal(10,2) DEFAULT NULL,
  `publicPrice` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES ('1e543020-f792-49a0-98f8-5a9671a2348d','12B','Luz de Nieve','{\"b2a7a2_color_1\": \"img/imgProducts/1753228242_a8a09d_color_1_ECC98D_1748281796317.jpeg\", \"ffffff_color_2\": \"img/imgProducts/1753228242_ffffff_color_2_FFFFFF_1748156142439.png\"}','\"[\\\"M\\\",\\\"XL\\\"]\"','\"[\\\"12\\\",\\\"10\\\"]\"','Bautizos - Niña',1000.00,'Bautizos - Niña',5,'Vestido de bautizo con gorrito y balerina de manga larga al estilo colonial','Navys',900.00,1600.00,'2025-07-23 05:28:06','2025-07-27 03:16:13'),('26eb02f8-e18e-46f5-933b-3d46ee55c497','BANI0008','Nube Suave','{\"ffffff_color_0\": \"img/imgProducts/1753252637_ffffff_color_0_nuebe.webp\"}','\"[\\\"CH\\\",\\\"MG\\\"]\"',NULL,'Bautizos - Niño',2100.00,'Bautizos',14,'Consta de camisa, pantaloncito (pesquero), así como de su gorrito','Navys',1500.00,2100.00,'2025-07-23 12:37:17','2025-07-27 04:42:18'),('56c279fa-a789-4c5d-ad91-3c2d7c8d76f8','BA0009','Perlita Sagrada','{\"ebe7e2_cafe_dorado\": \"img/imgProducts/1753252500_ebe7e2_cafe_dorado_D2AC6A_1748155547781.webp\"}','\"[\\\"CH\\\",\\\"M\\\",\\\"G\\\"]\"',NULL,'Bautizos - Niña',1500.00,'Bautizos',23,'vestido bautizo con bordados y gorrito de bautizo','BaUz',1200.00,1700.00,'2025-07-23 12:35:00','2025-07-24 19:47:00'),('a08ed878-28f9-4b5c-b1f0-2507f2ad8177','XV0021','Eclipse de Encanto','{\"000000_color_0\": \"img/imgProducts/1753253750_000000_color_0_000000_1747691759454.webp\"}','\"[\\\"CH\\\",\\\"M\\\",\\\"G\\\"]\"',NULL,'XV Años , Vestidos',12500.00,'XV Años - Vestidos',2,'Incluye Aro flexible y Crinolina','Navys-x',10000.00,12500.00,'2025-07-23 12:55:50','2025-07-26 03:24:01'),('af6ea751-7b06-4f79-a06b-3193672ba0c0','BANI0010','Traje lino Boina','{\"ffffff_color_0\": \"img/imgProducts/1753253657_ffffff_color_0_FFFFFF_1747721393767.jpeg\"}','\"[\\\"CH\\\"]\"',NULL,'Bautizos,Traje, niño',699.00,'Bautizos - Niño',60,'traje de lino con pantalón, camisa, chaleco, boina y moño','Navis-X',600.00,700.00,'2025-07-23 12:54:17','2025-07-23 12:54:17'),('c8ff72ff-1352-4ef9-9b34-1da010b2f8a9','XV0020','Rosa Eterna','{\"d10113_rojo\": \"img/imgProducts/1753254057_d10113_rojo_BA000A_1747691197731.webp\", \"907da7_morado\": \"img/imgProducts/1753254057_907da7_morado_BFB6E0_1747691197734.webp\"}','\"[\\\"CH\\\",\\\"M\\\",\\\"G\\\"]\"',NULL,'Vestidos',13500.00,'XV Años',20,'Incluye Aro flexible y Crinolina','Navys-X',13400.00,13600.00,'2025-07-23 13:00:57','2025-07-25 22:48:25'),('f5f2deb4-6255-4b1a-82dc-13f778497796','XV0013','Muñeca xv','{\"b4a9a5_lila\": \"https://storage.googleapis.com/navys-5eeb9.firebasestorage.app/img/imgProducts/1753499793_b4a9a5_lila_693946_1747705217598%20%281%29.jpg?GoogleAccessId=firebase-adminsdk-fbsvc%40navys-5eeb9.iam.gserviceaccount.com&Expires=1785035793&Signature=Ury9mqvN%2Fzdr3RNMY%2FuEso9Pvfi6ok72FFJLofo%2FH7B1VgUG9Fx2Bg0FE8dheBSd%2BOkknte9fCJXWye63mBA1GDW0K741I4GgF4sRmAZ4n9iIL5bys3AU%2B1GlyUXlyoRlMGF859AaUmvJE3gFPaCyN7e%2BOKgqzXWOYCH5Oe%2BZ7CKo6Myg8oRd9tRHq1OnBWIlvIRUI98ZrBIIQyRGKOUL0ewV7cqvvZZHJBccPDp6ksjGj5r2nwFOT6I%2F7m%2F8u%2BCxw3Czygq%2Ftm6%2BUkY2fJVs9TNCECqC1Lv3Ua%2FxLMVbxuSuAjbenIMIHRvIOZpS99jHnEDSGxGS78IXh7uLgPjzQ%3D%3D\"}','\"[\\\"S\\\"]\"','\"[\\\"10\\\"]\"','Accesorios',890.00,'XV accesorios',5,'Incluye muñeca, vestido del color del vestido de la quinceañera, zapatos aro y base','mue',860.00,1000.00,'2025-07-26 03:15:52','2025-07-26 03:16:33');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('22rbdARh6nfVhO797BgqU7FpUZp6OAyEDRA3WTA2',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRzJUS1dFRmRnbWNlcEZLSkQxajFpcDhiVk5UN0RwdFh2NFBZdFRibCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9sb2dpbiI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==',1753491536),('hWBR25DopIlIWoA8NkDszd3EreuvYqVX5QT9ZVWc',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiSURkQmNFRFlKclpMU0RDd0pNb1NNU3J5UE5mZm9XUG9OSHFHNW40TyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9sb2dpbiI7fX0=',1753498531),('KLmBA8Bjfwl5vW7i9LIMy1Dh7FVIzD5ewtut9Jxq',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZXBvalh5cmI4MnY5UFNtWW5UaW5pWHN1ZjJuYjV6UXlNNlR0RjBwaCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=',1753491567),('poXSmmXHZvn02DEL63IvURuS6YzHwUH9ZoPLD7fF',NULL,'100.64.0.2','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiYTg2RjlENUNEamRQSFhNZGxrS1hIOER2cktkUDh5TVBpNEpZRGh0VSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjY6Imh0dHA6Ly9sYXJhYmVsLWJhY2tlbmQtbmF2eXMtcHJvZHVjdGlvbi51cC5yYWlsd2F5LmFwcC9hZG1pbi9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1753496803),('VcwPTBUrkhDNwREKozXIRjZbdMqx2xGprHdQEgsf',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNFdVNm9XeVJxeTJZU0FEQkNpUkZFamROSkJGSUt6dVpuWWVVN21KRiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=',1753498559),('zddojJyZN0d1vlWXRScyZXfJAUlahTPQX5ytwdkk',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0','YTo0OntzOjY6Il90b2tlbiI7czo0MDoid0xVSWhZWGs3ak9Zc0MxZWNuN2RwWEhVRWQwSmxpanZIbzVZOXJGNCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==',1753491517);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `middleName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numberPhone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('admin','empleado','cliente') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cliente',
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin@admin.com',NULL,'$2y$12$X7QBgd9muezcSX.dEYZYOOIOTYhYLm/CzwtBrdMdFDhrEmCCQ5Vea','Super','Admin','Pro','+1234567890','img/imgPerfil/adminjiji.jpg','admin',NULL,'2025-07-22 08:35:23','2025-07-22 08:35:23'),(2,'empleado@test.com',NULL,'$2y$12$Hn.cIHrzQn5v3fdbRFfFauvIlgatb/KeR1VT8.U5XDh6jpvIPv71C','Juan','Pérez','Carlos','+0987654321',NULL,'empleado',NULL,'2025-07-22 08:35:23','2025-07-22 08:35:23'),(3,'cliente@test.com',NULL,'$2y$12$oGNkrnY/9vNpjvm2FWm1kOloa.mdt./zAkFOLc4T0aBRFcrG39EPa','María','González','Elena','+1122334455',NULL,'cliente',NULL,'2025-07-22 08:35:24','2025-07-22 08:35:24'),(4,'ke451@outlook.com',NULL,'$2y$12$PbF8IfFeoe4WXheXWhJGzeO01rdPExPr74P70yMFUpGb29Hp2sqG.','kevin','zarate','garcia','9511570454','img/imgPerfil/1753162430_angry.jpg','cliente',NULL,'2025-07-22 11:33:50','2025-07-22 11:33:50'),(10,'iamkevinzg2000@gmail.com',NULL,'$2y$12$YLnPe2WYwRtLGSnYEjE7nu5Lt5jAHjdJAXIAbFx1kB9j6mpx.It3S','Kevin','Goat','Reing','951168233','https://storage.googleapis.com/navys-5eeb9.firebasestorage.app/img/imgPerfil/1753500013_6884496d98934.jpg?GoogleAccessId=firebase-adminsdk-fbsvc%40navys-5eeb9.iam.gserviceaccount.com&Expires=1785036013&Signature=Cvv0oTdA9LFYFmwBzR5AuDEx7prA5TcsGjIiUDeNBK%2Fl7KQPBfrF3ihmaADgCHkNjDOfigWfQbEJybisjIMlOt7eMvcPAZe5Q97JBEEE2L%2Ft484Rl3XjxgwI9BdcRqFBUAMt5lJ7nm%2BZMxnZ7lcD8V3ng7fBZCRgjvekm3rXujl413tFJhj7kSIwWmHSxI2B7a2MQtKLiMMtVwfqKUpaXfu54l%2BdmDqURqcirV297aU1tXqErD3AKtrDUKUSnQDC4%2B6Bb0NJezJb4nniHyHp3fdxCXB2ISnjCjDIJGByQqKG5upc0v599C%2B7yI0AsYAva%2FPy%2BD6vfDWrqqeDUt7EYA%3D%3D','admin',NULL,'2025-07-26 03:11:01','2025-07-26 03:20:13');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-07-27 22:32:36

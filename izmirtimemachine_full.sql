-- MySQL dump 10.13  Distrib 9.4.0, for macos15.4 (arm64)
--
-- Host: localhost    Database: izmirtimemachine
-- ------------------------------------------------------
-- Server version	8.4.5

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
-- Table structure for table `app_settings`
--

DROP TABLE IF EXISTS `app_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `app_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `app_settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_settings`
--

LOCK TABLES `app_settings` WRITE;
/*!40000 ALTER TABLE `app_settings` DISABLE KEYS */;
INSERT INTO `app_settings` VALUES (1,'qr_enabled','0',NULL,'2025-08-17 19:16:26','2025-08-17 19:16:42'),(2,'x_url','https://x.com/x',NULL,'2025-08-17 19:16:26','2025-08-17 19:16:26'),(3,'linkedin_url','https://x.com/xa',NULL,'2025-08-17 19:16:26','2025-08-17 19:16:26'),(4,'instagram_url','https://x.com/xa1',NULL,'2025-08-17 19:16:26','2025-08-17 19:16:26'),(5,'email_address','test@gmail.com',NULL,'2025-08-17 19:16:26','2025-08-17 19:16:26'),(6,'about_project.tr','https://x.com/xa2',NULL,'2025-08-17 19:16:26','2025-08-17 19:16:26'),(7,'about_project.en','https://x.com/xa2 enz',NULL,'2025-08-17 19:16:26','2025-08-17 19:16:32'),(8,'about_project.ar',NULL,NULL,'2025-08-17 19:16:26','2025-08-17 19:16:26'),(9,'about_project.de',NULL,NULL,'2025-08-17 19:16:26','2025-08-17 19:16:26');
/*!40000 ALTER TABLE `app_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `archaeological_site_translations`
--

DROP TABLE IF EXISTS `archaeological_site_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `archaeological_site_translations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `archaeological_site_id` bigint unsigned NOT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `audio_guide_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `arch_site_trans_unique` (`archaeological_site_id`,`locale`),
  KEY `archaeological_site_translations_locale_index` (`locale`),
  CONSTRAINT `archaeological_site_translations_archaeological_site_id_foreign` FOREIGN KEY (`archaeological_site_id`) REFERENCES `archaeological_sites` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `archaeological_site_translations`
--

LOCK TABLES `archaeological_site_translations` WRITE;
/*!40000 ALTER TABLE `archaeological_site_translations` DISABLE KEYS */;
INSERT INTO `archaeological_site_translations` VALUES (4,4,'tr','Trajan Tapınağı','Pergamon Trajan Tapınağı, Roma İmparatorluğu’nun en iyi beş imparatorundan ikincisi olan Trajan’a (MS 98-117) ithaf edilmiştir. Roma tapınak mimarisinin tipik bir örneği olan yapı, Pergamon’un yukarı şehrinde, Athena Kutsal Alanı’nın hemen yanında ve antik tiyatronun üstündeki en yüksek noktada inşa edilmiştir. Tapınağın inşası için bu konumun tercih  edilmesinin stratejik ve politik bir sebebi vardır.','audio-guides/tr/qkGn1jPKzMlZbfj5xs9dyNqK00NFwDc0Z0l3n0ev.mp3','2025-08-17 22:03:06','2025-08-17 22:54:11'),(5,5,'tr','TEST Trajan Tapınağı','test yazısı','audio-guides/tr/Hge4AV7z0rFVzpZCGhNCG1YM6HBWnTawHb81QzBw.mp3','2025-08-18 08:06:45','2025-08-18 08:06:45');
/*!40000 ALTER TABLE `archaeological_site_translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `archaeological_sites`
--

DROP TABLE IF EXISTS `archaeological_sites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `archaeological_sites` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sub_region_id` bigint unsigned NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_nearby_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `archaeological_sites_sub_region_id_foreign` (`sub_region_id`),
  CONSTRAINT `archaeological_sites_sub_region_id_foreign` FOREIGN KEY (`sub_region_id`) REFERENCES `sub_regions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `archaeological_sites`
--

LOCK TABLES `archaeological_sites` WRITE;
/*!40000 ALTER TABLE `archaeological_sites` DISABLE KEYS */;
INSERT INTO `archaeological_sites` VALUES (4,3,38.00000000,27.00000000,'archaeological-sites/OXisk6KpviuKp2749yY07T2C1Qkcpf0sbbpZz0Lw.png',1,1,'2025-08-17 22:03:06','2025-08-17 22:03:06'),(5,4,32.00000000,25.00000000,'archaeological-sites/k09GYuawCS9s72UwZzIDhkhCg7tRTgnrp1uhEDVF.png',1,1,'2025-08-18 08:06:45','2025-08-18 08:06:45');
/*!40000 ALTER TABLE `archaeological_sites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audio_guides`
--

DROP TABLE IF EXISTS `audio_guides`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audio_guides` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sub_region_id` bigint unsigned NOT NULL,
  `archaeological_site_id` bigint unsigned DEFAULT NULL,
  `audio_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `duration` int DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audio_guides_sub_region_id_foreign` (`sub_region_id`),
  KEY `audio_guides_archaeological_site_id_foreign` (`archaeological_site_id`),
  CONSTRAINT `audio_guides_archaeological_site_id_foreign` FOREIGN KEY (`archaeological_site_id`) REFERENCES `archaeological_sites` (`id`) ON DELETE CASCADE,
  CONSTRAINT `audio_guides_sub_region_id_foreign` FOREIGN KEY (`sub_region_id`) REFERENCES `sub_regions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audio_guides`
--

LOCK TABLES `audio_guides` WRITE;
/*!40000 ALTER TABLE `audio_guides` DISABLE KEYS */;
/*!40000 ALTER TABLE `audio_guides` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blog_posts`
--

DROP TABLE IF EXISTS `blog_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blog_posts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `featured_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `external_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_posts`
--

LOCK TABLES `blog_posts` WRITE;
/*!40000 ALTER TABLE `blog_posts` DISABLE KEYS */;
/*!40000 ALTER TABLE `blog_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `featured_content`
--

DROP TABLE IF EXISTS `featured_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `featured_content` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `content_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content_id` bigint unsigned NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `featured_content_content_type_content_id_index` (`content_type`,`content_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `featured_content`
--

LOCK TABLES `featured_content` WRITE;
/*!40000 ALTER TABLE `featured_content` DISABLE KEYS */;
/*!40000 ALTER TABLE `featured_content` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2025_08_10_211821_create_regions_table',1),(5,'2025_08_10_211827_create_region_translations_table',1),(6,'2025_08_10_212151_create_sub_regions_table',2),(7,'2025_08_10_212152_create_onboarding_slides_table',2),(8,'2025_08_10_212153_create_archaeological_sites_table',2),(9,'2025_08_10_212154_create_blog_posts_table',2),(10,'2025_08_10_212203_create_reels_table',2),(11,'2025_08_10_212204_create_models_3d_table',2),(12,'2025_08_10_212206_create_audio_guides_table',2),(13,'2025_08_10_212207_create_qr_codes_table',2),(14,'2025_08_10_212208_create_featured_content_table',2),(15,'2025_08_10_212209_create_app_settings_table',2),(16,'2025_08_10_212219_create_sub_region_translations_table',2),(17,'2025_08_10_212220_create_onboarding_slide_translations_table',2),(18,'2025_08_10_212221_create_archaeological_site_translations_table',2),(19,'2025_08_10_212228_create_blog_post_translations_table',2),(20,'2025_08_10_212229_create_reel_translations_table',2),(21,'2025_08_10_212230_create_model_3d_translations_table',2),(22,'2025_08_10_212232_create_audio_guide_translations_table',2),(23,'2025_08_17_104825_add_archaeological_site_id_to_models_3d_table',3),(24,'2025_08_17_104921_add_archaeological_site_id_to_qr_codes_and_audio_guides_tables',4),(25,'2025_08_17_111717_update_models_3d_table_for_sketchfab',5),(26,'2025_08_17_112335_make_sub_region_id_nullable_in_models_3d_table',6),(27,'2025_08_17_112720_create_model3d_translations_table',7),(28,'2025_08_17_113141_add_audio_guide_path_to_archaeological_site_translations_table',8),(29,'2025_08_17_115107_add_qr_fields_to_models_3d_table',9),(30,'2025_08_17_120001_drop_region_and_video_from_onboarding_slides',10),(31,'2025_08_17_120100_add_subtitle_to_region_and_sub_region_translations',11);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model3d_translations`
--

DROP TABLE IF EXISTS `model3d_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model3d_translations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `model3d_id` bigint unsigned NOT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `model3d_trans_unique` (`model3d_id`,`locale`),
  KEY `model3d_translations_locale_index` (`locale`),
  CONSTRAINT `model3d_translations_model3d_id_foreign` FOREIGN KEY (`model3d_id`) REFERENCES `models_3d` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model3d_translations`
--

LOCK TABLES `model3d_translations` WRITE;
/*!40000 ALTER TABLE `model3d_translations` DISABLE KEYS */;
INSERT INTO `model3d_translations` VALUES (5,4,'tr','Trajan Tapınağı',NULL,'2025-08-17 22:03:29','2025-08-17 22:03:29');
/*!40000 ALTER TABLE `model3d_translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_3d_translations`
--

DROP TABLE IF EXISTS `model_3d_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_3d_translations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `model_3d_id` bigint unsigned NOT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `model_3d_trans_unique` (`model_3d_id`,`locale`),
  KEY `model_3d_translations_locale_index` (`locale`),
  CONSTRAINT `model_3d_translations_model_3d_id_foreign` FOREIGN KEY (`model_3d_id`) REFERENCES `models_3d` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_3d_translations`
--

LOCK TABLES `model_3d_translations` WRITE;
/*!40000 ALTER TABLE `model_3d_translations` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_3d_translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `models_3d`
--

DROP TABLE IF EXISTS `models_3d`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `models_3d` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sub_region_id` bigint unsigned DEFAULT NULL,
  `archaeological_site_id` bigint unsigned DEFAULT NULL,
  `sketchfab_model_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sketchfab_thumbnail_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qr_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qr_image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `models_3d_qr_uuid_unique` (`qr_uuid`),
  KEY `models_3d_sub_region_id_foreign` (`sub_region_id`),
  KEY `models_3d_archaeological_site_id_foreign` (`archaeological_site_id`),
  CONSTRAINT `models_3d_archaeological_site_id_foreign` FOREIGN KEY (`archaeological_site_id`) REFERENCES `archaeological_sites` (`id`) ON DELETE CASCADE,
  CONSTRAINT `models_3d_sub_region_id_foreign` FOREIGN KEY (`sub_region_id`) REFERENCES `sub_regions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `models_3d`
--

LOCK TABLES `models_3d` WRITE;
/*!40000 ALTER TABLE `models_3d` DISABLE KEYS */;
INSERT INTO `models_3d` VALUES (4,3,4,'3d15db8ec99744e5ba91dfcad10e1d4b','https://media.sketchfab.com/models/8e08d58c032d4860b4bf0c412a88b0ec/thumbnails/cb46e8f7fd5045d2bc55a06bba3db6fd/56519b77d96e4061b21e5159386a57b3.jpeg',NULL,NULL,1,1,'2025-08-17 22:03:29','2025-08-17 23:07:10');
/*!40000 ALTER TABLE `models_3d` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `onboarding_slide_translations`
--

DROP TABLE IF EXISTS `onboarding_slide_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `onboarding_slide_translations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `onboarding_slide_id` bigint unsigned NOT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `onboarding_slide_translations`
--

LOCK TABLES `onboarding_slide_translations` WRITE;
/*!40000 ALTER TABLE `onboarding_slide_translations` DISABLE KEYS */;
INSERT INTO `onboarding_slide_translations` VALUES (1,1,'tr','test on 11','11',NULL,NULL),(2,1,'en','test on 11 en','e2',NULL,NULL),(3,2,'tr','test onboarding2','test',NULL,NULL),(4,3,'tr','Onboarding Ekranındasınız','Onboarding ekranına uzun açıklama girildi.',NULL,NULL),(5,4,'tr','AR Teknolojisi ile İzmir’de Zaman Yolculuğuna Çıkın',NULL,NULL,NULL),(6,5,'tr','Yakınınızdaki Ören Yerlerini Keşfedin','Konumunuza yakın ören yerlerini listeleyin ve keşfetmeye başlayın.',NULL,NULL),(7,6,'tr','Zengin İçerikleri Keşfedin','Yazılı ve Sesli anlatım seçenekleri ile İzmir’in tarihindeki yolculuğunuza boyut katın.',NULL,NULL);
/*!40000 ALTER TABLE `onboarding_slide_translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `onboarding_slides`
--

DROP TABLE IF EXISTS `onboarding_slides`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `onboarding_slides` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `onboarding_slides`
--

LOCK TABLES `onboarding_slides` WRITE;
/*!40000 ALTER TABLE `onboarding_slides` DISABLE KEYS */;
INSERT INTO `onboarding_slides` VALUES (4,'onboarding/RnB2i4t700YziML8BDyGKNI9BwOfFxea7D2OKdJW.png',1,1,'2025-08-17 21:53:36','2025-08-17 21:53:41'),(5,'onboarding/GyBXmIHvC6cDoLqA8BktXtUoCGq80cdVqs7p23Gt.png',2,1,'2025-08-17 21:54:13','2025-08-17 21:54:13'),(6,'onboarding/Zjzeg0064yVJZLKNcNf5SP3xcnuA4TgOq1iPivCi.png',3,1,'2025-08-17 21:54:58','2025-08-17 21:54:58');
/*!40000 ALTER TABLE `onboarding_slides` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `qr_codes`
--

DROP TABLE IF EXISTS `qr_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `qr_codes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sub_region_id` bigint unsigned NOT NULL,
  `archaeological_site_id` bigint unsigned DEFAULT NULL,
  `qr_content` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ar_model_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_coming_soon` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `qr_codes_qr_content_unique` (`qr_content`),
  KEY `qr_codes_sub_region_id_foreign` (`sub_region_id`),
  KEY `qr_codes_archaeological_site_id_foreign` (`archaeological_site_id`),
  CONSTRAINT `qr_codes_archaeological_site_id_foreign` FOREIGN KEY (`archaeological_site_id`) REFERENCES `archaeological_sites` (`id`) ON DELETE CASCADE,
  CONSTRAINT `qr_codes_sub_region_id_foreign` FOREIGN KEY (`sub_region_id`) REFERENCES `sub_regions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `qr_codes`
--

LOCK TABLES `qr_codes` WRITE;
/*!40000 ALTER TABLE `qr_codes` DISABLE KEYS */;
/*!40000 ALTER TABLE `qr_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reels`
--

DROP TABLE IF EXISTS `reels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reels` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `video_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `thumbnail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `filter_settings` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reels`
--

LOCK TABLES `reels` WRITE;
/*!40000 ALTER TABLE `reels` DISABLE KEYS */;
/*!40000 ALTER TABLE `reels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `region_translations`
--

DROP TABLE IF EXISTS `region_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `region_translations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `region_id` bigint unsigned NOT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtitle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `region_translations_region_id_locale_unique` (`region_id`,`locale`),
  KEY `region_translations_locale_index` (`locale`),
  CONSTRAINT `region_translations_region_id_foreign` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `region_translations`
--

LOCK TABLES `region_translations` WRITE;
/*!40000 ALTER TABLE `region_translations` DISABLE KEYS */;
INSERT INTO `region_translations` VALUES (13,7,'tr','Pergamon','Kadim Bir Andolu Şehri:','Yukarı şehrin batı yamacına dik bir şekilde inşa edilen tiyatro kompleksi, antik kentin silüetini oluşturan en önemli yapılarından biridir.',NULL,NULL),(14,8,'tr','Eski Smyrna','İzmir’e Adını Veren Kent:','Smyrna antik kenti, Yamanlar Dağı’nın güney eteklerinde, Ege Denizi’ne uzanan bir burun üzerinde kurulmuştur.',NULL,NULL),(15,9,'tr','Test bölge','test','test',NULL,NULL),(16,9,'en','test bölge en','test','2',NULL,NULL);
/*!40000 ALTER TABLE `region_translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `regions`
--

DROP TABLE IF EXISTS `regions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `regions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `color_code` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `main_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `regions`
--

LOCK TABLES `regions` WRITE;
/*!40000 ALTER TABLE `regions` DISABLE KEYS */;
INSERT INTO `regions` VALUES (7,'#d400ff','regions/6hv965Zfy4TiIixshUC3b0LgN405fx5PYNyQBlG3.png',1,1,'2025-08-17 22:01:00','2025-08-17 22:01:00'),(8,'#3498db','regions/LLP8ttPyX3Gk7Qz3KetEn4kDDNkWIbwVvqJqDOXR.png',1,2,'2025-08-17 22:12:38','2025-08-17 22:12:38'),(9,'#0ebe5a','regions/3umWMdFUtQaQUAQh8TlpqRpq0W2L2uZ6NfU8jMJG.png',1,3,'2025-08-18 07:57:54','2025-08-18 07:57:54');
/*!40000 ALTER TABLE `regions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
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
INSERT INTO `sessions` VALUES ('GqO6ugMM0yWwmLG6sqx1SHPsNn6GVLx7ykP2ZeMQ',1,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiS0Vwa2NZTkNIdk44RlN2Vnpyd1hsNXBIcm5oR2JuTGJ0N2tOdU56SCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDU6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9vbmJvYXJkaW5nLXNsaWRlcyI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==',1755516561);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sub_region_translations`
--

DROP TABLE IF EXISTS `sub_region_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sub_region_translations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sub_region_id` bigint unsigned NOT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtitle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sub_region_translations_sub_region_id_locale_unique` (`sub_region_id`,`locale`),
  KEY `sub_region_translations_locale_index` (`locale`),
  CONSTRAINT `sub_region_translations_sub_region_id_foreign` FOREIGN KEY (`sub_region_id`) REFERENCES `sub_regions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sub_region_translations`
--

LOCK TABLES `sub_region_translations` WRITE;
/*!40000 ALTER TABLE `sub_region_translations` DISABLE KEYS */;
INSERT INTO `sub_region_translations` VALUES (3,3,'tr','Pergamon’da Antik Dönem',NULL,'Deniz seviyesinden yaklaşık 392 metre yükseklikte, dik bir dağ yamacına inşa edilen Pergamon, antik dünyanın en göz alıcı şehirlerinden biridir. Zorlu coğrafi koşullara rağmen bu sarp yamaçta ....',NULL,NULL),(4,4,'tr','test alt bölge','test','test',NULL,NULL);
/*!40000 ALTER TABLE `sub_region_translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sub_regions`
--

DROP TABLE IF EXISTS `sub_regions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sub_regions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `region_id` bigint unsigned NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sub_regions_region_id_foreign` (`region_id`),
  CONSTRAINT `sub_regions_region_id_foreign` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sub_regions`
--

LOCK TABLES `sub_regions` WRITE;
/*!40000 ALTER TABLE `sub_regions` DISABLE KEYS */;
INSERT INTO `sub_regions` VALUES (3,7,38.00000000,27.00000000,'sub-regions/S4VDByVfYKyqnP3b57G6lGupCzYLuXLnJRJbI56a.png',1,1,'2025-08-17 22:02:01','2025-08-17 22:02:01'),(4,9,38.40000000,27.10000000,'sub-regions/zdpXqyCvhOoSY7IPfi6iyaZxmze1azOx4dlYahrY.png',1,1,'2025-08-18 08:03:58','2025-08-18 08:03:58');
/*!40000 ALTER TABLE `sub_regions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin','admin@izmirtimemachine.local',NULL,'$2y$12$5iWYbvIOYV/I.OG/75ABquDg1rGlXnGqbNJ4.WB..yUzTtG.g0Vqu',NULL,'2025-08-17 19:41:53','2025-08-17 19:42:50');
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

-- Dump completed on 2025-08-20  2:00:43

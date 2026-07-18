-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: nodo_catalog_manager
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `activity_log`
--

DROP TABLE IF EXISTS `activity_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `log_name` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `subject_type` varchar(255) DEFAULT NULL,
  `event` varchar(255) DEFAULT NULL,
  `subject_id` bigint(20) unsigned DEFAULT NULL,
  `causer_type` varchar(255) DEFAULT NULL,
  `causer_id` bigint(20) unsigned DEFAULT NULL,
  `properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`properties`)),
  `batch_uuid` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subject` (`subject_type`,`subject_id`),
  KEY `causer` (`causer_type`,`causer_id`),
  KEY `activity_log_log_name_index` (`log_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_log`
--

LOCK TABLES `activity_log` WRITE;
/*!40000 ALTER TABLE `activity_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `activity_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ai_generations`
--

DROP TABLE IF EXISTS `ai_generations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ai_generations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `task` varchar(255) NOT NULL,
  `provider` varchar(255) NOT NULL,
  `model` varchar(255) NOT NULL,
  `prompt` longtext NOT NULL,
  `response` longtext DEFAULT NULL,
  `input_tokens` int(10) unsigned DEFAULT NULL,
  `output_tokens` int(10) unsigned DEFAULT NULL,
  `estimated_cost` decimal(10,4) DEFAULT NULL,
  `status` enum('completado','error','aprobado','rechazado') NOT NULL DEFAULT 'completado',
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ai_generations_user_id_foreign` (`user_id`),
  KEY `ai_generations_product_id_foreign` (`product_id`),
  KEY `ai_generations_task_created_at_index` (`task`,`created_at`),
  CONSTRAINT `ai_generations_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ai_generations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ai_generations`
--

LOCK TABLES `ai_generations` WRITE;
/*!40000 ALTER TABLE `ai_generations` DISABLE KEYS */;
/*!40000 ALTER TABLE `ai_generations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
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
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `collection_id` bigint(20) unsigned DEFAULT NULL,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`),
  KEY `categories_collection_id_foreign` (`collection_id`),
  CONSTRAINT `categories_collection_id_foreign` FOREIGN KEY (`collection_id`) REFERENCES `collections` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Inteligencia Artificial','inteligencia-artificial',NULL,NULL,0,1,'2026-07-17 13:14:50','2026-07-17 13:14:50',NULL);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `collections`
--

DROP TABLE IF EXISTS `collections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `collections` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `color` varchar(20) DEFAULT NULL,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `collections_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `collections`
--

LOCK TABLES `collections` WRITE;
/*!40000 ALTER TABLE `collections` DISABLE KEYS */;
INSERT INTO `collections` VALUES (1,'Inteligencia Artificial','inteligencia-artificial','Agentes de inteligencia artificial que atienden, venden y dan seguimiento a tus clientes de forma automática.','cpu-chip',NULL,'#2563EB',0,1,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(2,'Automatización Empresarial','automatizacion-empresarial','Conectamos tus sistemas y procesos para eliminar tareas manuales repetitivas.','bolt',NULL,'#7C3AED',1,1,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(3,'Software Empresarial','software-empresarial','Sistemas y plataformas a la medida para operar y escalar tu negocio.','squares-2x2',NULL,'#0F172A',2,1,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(4,'Growth Marketing','growth-marketing','Estrategias de marketing digital orientadas a resultados medibles.','rocket-launch',NULL,'#DC2626',3,1,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(5,'Soluciones por Industria','soluciones-por-industria','Soluciones de inteligencia artificial adaptadas a los procesos específicos de cada industria.','building-storefront',NULL,'#0EA5E9',4,1,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(6,'Transformación Digital','transformacion-digital','Infraestructura, seguridad y consultoría para digitalizar tu operación.','globe-alt',NULL,'#1D4ED8',5,1,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL);
/*!40000 ALTER TABLE `collections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_list_contact`
--

DROP TABLE IF EXISTS `contact_list_contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_list_contact` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `contact_id` bigint(20) unsigned NOT NULL,
  `contact_list_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `contact_list_contact_contact_id_contact_list_id_unique` (`contact_id`,`contact_list_id`),
  KEY `contact_list_contact_contact_list_id_foreign` (`contact_list_id`),
  CONSTRAINT `contact_list_contact_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `contact_list_contact_contact_list_id_foreign` FOREIGN KEY (`contact_list_id`) REFERENCES `contact_lists` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_list_contact`
--

LOCK TABLES `contact_list_contact` WRITE;
/*!40000 ALTER TABLE `contact_list_contact` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact_list_contact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_lists`
--

DROP TABLE IF EXISTS `contact_lists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_lists` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `contact_lists_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_lists`
--

LOCK TABLES `contact_lists` WRITE;
/*!40000 ALTER TABLE `contact_lists` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact_lists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contacts`
--

DROP TABLE IF EXISTS `contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contacts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `whatsapp` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `source` varchar(255) DEFAULT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `consent` tinyint(1) NOT NULL DEFAULT 0,
  `consent_at` timestamp NULL DEFAULT NULL,
  `subscribed` tinyint(1) NOT NULL DEFAULT 1,
  `unsubscribed_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `contacts_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contacts`
--

LOCK TABLES `contacts` WRITE;
/*!40000 ALTER TABLE `contacts` DISABLE KEYS */;
/*!40000 ALTER TABLE `contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `crm_activities`
--

DROP TABLE IF EXISTS `crm_activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crm_activities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `deal_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `due_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `crm_activities_deal_id_foreign` (`deal_id`),
  KEY `crm_activities_user_id_foreign` (`user_id`),
  CONSTRAINT `crm_activities_deal_id_foreign` FOREIGN KEY (`deal_id`) REFERENCES `crm_deals` (`id`) ON DELETE CASCADE,
  CONSTRAINT `crm_activities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `crm_activities`
--

LOCK TABLES `crm_activities` WRITE;
/*!40000 ALTER TABLE `crm_activities` DISABLE KEYS */;
/*!40000 ALTER TABLE `crm_activities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `crm_deals`
--

DROP TABLE IF EXISTS `crm_deals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crm_deals` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `contact_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `stage_id` bigint(20) unsigned NOT NULL,
  `value` decimal(12,2) DEFAULT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'MXN',
  `source` varchar(255) NOT NULL DEFAULT 'manual',
  `status` varchar(255) NOT NULL DEFAULT 'abierto',
  `expected_close_date` date DEFAULT NULL,
  `lost_reason` varchar(255) DEFAULT NULL,
  `assigned_to` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `landing_lead_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `crm_deals_contact_id_foreign` (`contact_id`),
  KEY `crm_deals_product_id_foreign` (`product_id`),
  KEY `crm_deals_stage_id_foreign` (`stage_id`),
  KEY `crm_deals_assigned_to_foreign` (`assigned_to`),
  KEY `crm_deals_created_by_foreign` (`created_by`),
  KEY `crm_deals_landing_lead_id_foreign` (`landing_lead_id`),
  CONSTRAINT `crm_deals_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `crm_deals_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `crm_deals_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `crm_deals_landing_lead_id_foreign` FOREIGN KEY (`landing_lead_id`) REFERENCES `landing_leads` (`id`) ON DELETE SET NULL,
  CONSTRAINT `crm_deals_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `crm_deals_stage_id_foreign` FOREIGN KEY (`stage_id`) REFERENCES `crm_stages` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `crm_deals`
--

LOCK TABLES `crm_deals` WRITE;
/*!40000 ALTER TABLE `crm_deals` DISABLE KEYS */;
/*!40000 ALTER TABLE `crm_deals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `crm_stages`
--

DROP TABLE IF EXISTS `crm_stages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crm_stages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `color` varchar(255) NOT NULL DEFAULT '#2563EB',
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `is_won` tinyint(1) NOT NULL DEFAULT 0,
  `is_lost` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `crm_stages_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `crm_stages`
--

LOCK TABLES `crm_stages` WRITE;
/*!40000 ALTER TABLE `crm_stages` DISABLE KEYS */;
INSERT INTO `crm_stages` VALUES (1,'Nuevo','nuevo','#64748B',1,0,0,'2026-07-18 01:22:04','2026-07-18 01:22:04'),(2,'Contactado','contactado','#0EA5E9',2,0,0,'2026-07-18 01:22:04','2026-07-18 01:22:04'),(3,'Calificado','calificado','#7C3AED',3,0,0,'2026-07-18 01:22:04','2026-07-18 01:22:04'),(4,'Propuesta enviada','propuesta-enviada','#F59E0B',4,0,0,'2026-07-18 01:22:04','2026-07-18 01:22:04'),(5,'Negociación','negociacion','#DC2626',5,0,0,'2026-07-18 01:22:04','2026-07-18 01:22:04'),(6,'Ganado','ganado','#16A34A',6,1,0,'2026-07-18 01:22:04','2026-07-18 01:22:04'),(7,'Perdido','perdido','#94A3B8',7,0,1,'2026-07-18 01:22:04','2026-07-18 01:22:04');
/*!40000 ALTER TABLE `crm_stages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_campaign_sends`
--

DROP TABLE IF EXISTS `email_campaign_sends`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_campaign_sends` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email_campaign_id` bigint(20) unsigned NOT NULL,
  `contact_id` bigint(20) unsigned NOT NULL,
  `token` varchar(64) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pendiente',
  `sent_at` timestamp NULL DEFAULT NULL,
  `opened_at` timestamp NULL DEFAULT NULL,
  `clicked_at` timestamp NULL DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_campaign_sends_email_campaign_id_contact_id_unique` (`email_campaign_id`,`contact_id`),
  UNIQUE KEY `email_campaign_sends_token_unique` (`token`),
  KEY `email_campaign_sends_contact_id_foreign` (`contact_id`),
  CONSTRAINT `email_campaign_sends_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `email_campaign_sends_email_campaign_id_foreign` FOREIGN KEY (`email_campaign_id`) REFERENCES `email_campaigns` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_campaign_sends`
--

LOCK TABLES `email_campaign_sends` WRITE;
/*!40000 ALTER TABLE `email_campaign_sends` DISABLE KEYS */;
/*!40000 ALTER TABLE `email_campaign_sends` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_campaigns`
--

DROP TABLE IF EXISTS `email_campaigns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_campaigns` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'newsletter',
  `subject` varchar(255) NOT NULL,
  `from_name` varchar(255) NOT NULL,
  `from_email` varchar(255) NOT NULL,
  `contact_list_id` bigint(20) unsigned DEFAULT NULL,
  `blocks` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`blocks`)),
  `status` varchar(255) NOT NULL DEFAULT 'borrador',
  `scheduled_at` timestamp NULL DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `sent_count` int(10) unsigned NOT NULL DEFAULT 0,
  `open_count` int(10) unsigned NOT NULL DEFAULT 0,
  `click_count` int(10) unsigned NOT NULL DEFAULT 0,
  `bounce_count` int(10) unsigned NOT NULL DEFAULT 0,
  `unsubscribe_count` int(10) unsigned NOT NULL DEFAULT 0,
  `batch_limit` int(10) unsigned NOT NULL DEFAULT 50,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email_campaigns_contact_list_id_foreign` (`contact_list_id`),
  KEY `email_campaigns_created_by_foreign` (`created_by`),
  CONSTRAINT `email_campaigns_contact_list_id_foreign` FOREIGN KEY (`contact_list_id`) REFERENCES `contact_lists` (`id`) ON DELETE SET NULL,
  CONSTRAINT `email_campaigns_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_campaigns`
--

LOCK TABLES `email_campaigns` WRITE;
/*!40000 ALTER TABLE `email_campaigns` DISABLE KEYS */;
/*!40000 ALTER TABLE `email_campaigns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
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
-- Table structure for table `image_generations`
--

DROP TABLE IF EXISTS `image_generations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `image_generations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `template_id` bigint(20) unsigned DEFAULT NULL,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `cta_text` varchar(255) DEFAULT NULL,
  `price_text` varchar(255) DEFAULT NULL,
  `qr_target_url` varchar(255) DEFAULT NULL,
  `background_source` varchar(255) NOT NULL DEFAULT 'color',
  `file_path` varchar(255) DEFAULT NULL,
  `ai_prompt` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'completado',
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `image_generations_user_id_foreign` (`user_id`),
  KEY `image_generations_template_id_foreign` (`template_id`),
  KEY `image_generations_product_id_foreign` (`product_id`),
  CONSTRAINT `image_generations_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `image_generations_template_id_foreign` FOREIGN KEY (`template_id`) REFERENCES `image_templates` (`id`) ON DELETE SET NULL,
  CONSTRAINT `image_generations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `image_generations`
--

LOCK TABLES `image_generations` WRITE;
/*!40000 ALTER TABLE `image_generations` DISABLE KEYS */;
/*!40000 ALTER TABLE `image_generations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `image_templates`
--

DROP TABLE IF EXISTS `image_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `image_templates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `format` varchar(255) NOT NULL,
  `width` int(10) unsigned NOT NULL,
  `height` int(10) unsigned NOT NULL,
  `background_type` varchar(255) NOT NULL DEFAULT 'color',
  `background_value` varchar(255) DEFAULT NULL,
  `overlay_gradient` tinyint(1) NOT NULL DEFAULT 1,
  `primary_color` varchar(20) NOT NULL DEFAULT '#2563EB',
  `accent_color` varchar(20) NOT NULL DEFAULT '#DC2626',
  `title_position` varchar(255) NOT NULL DEFAULT 'center',
  `show_price` tinyint(1) NOT NULL DEFAULT 0,
  `show_qr` tinyint(1) NOT NULL DEFAULT 0,
  `footer_text` varchar(255) DEFAULT NULL,
  `is_master` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `image_templates_slug_unique` (`slug`),
  KEY `image_templates_created_by_foreign` (`created_by`),
  CONSTRAINT `image_templates_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `image_templates`
--

LOCK TABLES `image_templates` WRITE;
/*!40000 ALTER TABLE `image_templates` DISABLE KEYS */;
INSERT INTO `image_templates` VALUES (1,'Plantilla maestra NODO 360','plantilla-maestra-nodo-360','cuadrado',1080,1080,'color','#F8FAFC',1,'#2563EB','#DC2626','center',1,1,'NODO 360 Marketing Technology',1,NULL,'2026-07-17 11:05:13','2026-07-17 11:05:13',NULL),(2,'Publicación cuadrada','publicacion-cuadrada','cuadrado',1080,1080,'color','#0F172A',1,'#2563EB','#DC2626','center',0,0,'NODO 360 Marketing Technology',0,NULL,'2026-07-17 11:05:13','2026-07-17 11:05:13',NULL),(3,'Publicación vertical (feed)','publicacion-vertical-feed','vertical',1080,1350,'color','#0F172A',1,'#7C3AED','#DC2626','bottom',1,0,'NODO 360 Marketing Technology',0,NULL,'2026-07-17 11:05:13','2026-07-17 11:05:13',NULL),(4,'Historia (stories)','historia-stories','historia',1080,1920,'color','#0F172A',1,'#2563EB','#DC2626','bottom',0,1,NULL,0,NULL,'2026-07-17 11:05:13','2026-07-17 11:05:13',NULL),(5,'Banner horizontal','banner-horizontal','horizontal',1200,628,'color','#0F172A',1,'#2563EB','#DC2626','center',1,0,'NODO 360 Marketing Technology',0,NULL,'2026-07-17 11:05:13','2026-07-17 11:05:13',NULL),(6,'Portada de colección','portada-de-coleccion','portada',1920,1080,'color','#0F172A',1,'#7C3AED','#DC2626','center',0,0,'NODO 360 Marketing Technology',0,NULL,'2026-07-17 11:05:13','2026-07-17 11:05:13',NULL);
/*!40000 ALTER TABLE `image_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `import_batches`
--

DROP TABLE IF EXISTS `import_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `import_batches` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'products',
  `original_filename` varchar(255) NOT NULL,
  `stored_path` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `total_rows` int(10) unsigned NOT NULL DEFAULT 0,
  `processed_rows` int(10) unsigned NOT NULL DEFAULT 0,
  `success_rows` int(10) unsigned NOT NULL DEFAULT 0,
  `error_rows` int(10) unsigned NOT NULL DEFAULT 0,
  `duplicate_strategy` varchar(255) NOT NULL DEFAULT 'skip',
  `column_mapping` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`column_mapping`)),
  `errors` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`errors`)),
  `errors_file_path` varchar(255) DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `finished_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `import_batches_user_id_foreign` (`user_id`),
  CONSTRAINT `import_batches_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `import_batches`
--

LOCK TABLES `import_batches` WRITE;
/*!40000 ALTER TABLE `import_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `import_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `landing_leads`
--

DROP TABLE IF EXISTS `landing_leads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `landing_leads` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `landing_page_id` bigint(20) unsigned NOT NULL,
  `contact_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `utm_source` varchar(255) DEFAULT NULL,
  `utm_medium` varchar(255) DEFAULT NULL,
  `utm_campaign` varchar(255) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `landing_leads_landing_page_id_foreign` (`landing_page_id`),
  KEY `landing_leads_contact_id_foreign` (`contact_id`),
  CONSTRAINT `landing_leads_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `landing_leads_landing_page_id_foreign` FOREIGN KEY (`landing_page_id`) REFERENCES `landing_pages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `landing_leads`
--

LOCK TABLES `landing_leads` WRITE;
/*!40000 ALTER TABLE `landing_leads` DISABLE KEYS */;
/*!40000 ALTER TABLE `landing_leads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `landing_pages`
--

DROP TABLE IF EXISTS `landing_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `landing_pages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'borrador',
  `headline` varchar(255) NOT NULL,
  `subheadline` varchar(255) DEFAULT NULL,
  `hero_image_path` varchar(255) DEFAULT NULL,
  `sections` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`sections`)),
  `cta_text` varchar(255) NOT NULL DEFAULT 'Quiero más información',
  `cta_whatsapp_number` varchar(255) DEFAULT NULL,
  `cta_whatsapp_message` varchar(255) DEFAULT NULL,
  `cta_url` varchar(255) DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  `og_image_path` varchar(255) DEFAULT NULL,
  `structured_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`structured_data`)),
  `ga4_id` varchar(255) DEFAULT NULL,
  `meta_pixel_id` varchar(255) DEFAULT NULL,
  `gtm_id` varchar(255) DEFAULT NULL,
  `capture_form_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `contact_list_id` bigint(20) unsigned DEFAULT NULL,
  `views_count` int(10) unsigned NOT NULL DEFAULT 0,
  `leads_count` int(10) unsigned NOT NULL DEFAULT 0,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `landing_pages_slug_unique` (`slug`),
  KEY `landing_pages_product_id_foreign` (`product_id`),
  KEY `landing_pages_contact_list_id_foreign` (`contact_list_id`),
  KEY `landing_pages_created_by_foreign` (`created_by`),
  CONSTRAINT `landing_pages_contact_list_id_foreign` FOREIGN KEY (`contact_list_id`) REFERENCES `contact_lists` (`id`) ON DELETE SET NULL,
  CONSTRAINT `landing_pages_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `landing_pages_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `landing_pages`
--

LOCK TABLES `landing_pages` WRITE;
/*!40000 ALTER TABLE `landing_pages` DISABLE KEYS */;
/*!40000 ALTER TABLE `landing_pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_07_17_063251_create_permission_tables',1),(5,'2026_07_17_063253_create_activity_log_table',1),(6,'2026_07_17_063254_add_event_column_to_activity_log_table',1),(7,'2026_07_17_063255_add_batch_uuid_column_to_activity_log_table',1),(8,'2026_07_17_063348_add_profile_fields_to_users_table',1),(9,'2026_07_17_063349_create_collections_table',2),(10,'2026_07_17_063350a_create_categories_table',2),(11,'2026_07_17_063351_create_products_table',2),(12,'2026_07_17_063352_create_product_images_table',2),(13,'2026_07_17_063353_create_settings_table',2),(14,'2026_07_17_063354_create_import_batches_table',2),(15,'2026_07_17_041626_create_ai_generations_table',3),(16,'2026_07_17_045903_create_image_templates_table',4),(17,'2026_07_17_045904_create_image_generations_table',4),(18,'2026_07_17_153343_create_social_accounts_table',5),(19,'2026_07_17_153343_create_social_posts_table',5),(20,'2026_07_17_155737_create_contact_lists_table',6),(21,'2026_07_17_155737_create_contacts_table',6),(22,'2026_07_17_155738_create_contact_list_contact_table',6),(23,'2026_07_17_155738_create_email_campaigns_table',6),(24,'2026_07_17_155739_create_email_campaign_sends_table',6),(25,'2026_07_17_170001_create_landing_pages_table',7),(26,'2026_07_17_170002_create_landing_leads_table',7),(27,'2026_07_17_180001_create_crm_stages_table',8),(28,'2026_07_17_180002_create_crm_deals_table',8),(29,'2026_07_17_180003_create_crm_activities_table',8);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',1),(1,'App\\Models\\User',3);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
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
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'ver productos','web','2026-07-17 12:48:52','2026-07-17 12:48:52'),(2,'crear productos','web','2026-07-17 12:48:52','2026-07-17 12:48:52'),(3,'editar productos','web','2026-07-17 12:48:52','2026-07-17 12:48:52'),(4,'eliminar productos','web','2026-07-17 12:48:52','2026-07-17 12:48:52'),(5,'publicar productos','web','2026-07-17 12:48:52','2026-07-17 12:48:52'),(6,'exportar productos','web','2026-07-17 12:48:52','2026-07-17 12:48:52'),(7,'importar productos','web','2026-07-17 12:48:52','2026-07-17 12:48:52'),(8,'ver colecciones','web','2026-07-17 12:48:52','2026-07-17 12:48:52'),(9,'crear colecciones','web','2026-07-17 12:48:52','2026-07-17 12:48:52'),(10,'editar colecciones','web','2026-07-17 12:48:52','2026-07-17 12:48:52'),(11,'eliminar colecciones','web','2026-07-17 12:48:52','2026-07-17 12:48:52'),(12,'ver categorias','web','2026-07-17 12:48:52','2026-07-17 12:48:52'),(13,'crear categorias','web','2026-07-17 12:48:52','2026-07-17 12:48:52'),(14,'editar categorias','web','2026-07-17 12:48:52','2026-07-17 12:48:52'),(15,'eliminar categorias','web','2026-07-17 12:48:52','2026-07-17 12:48:52'),(16,'ver usuarios','web','2026-07-17 12:48:52','2026-07-17 12:48:52'),(17,'crear usuarios','web','2026-07-17 12:48:52','2026-07-17 12:48:52'),(18,'editar usuarios','web','2026-07-17 12:48:52','2026-07-17 12:48:52'),(19,'eliminar usuarios','web','2026-07-17 12:48:52','2026-07-17 12:48:52'),(20,'administrar usuarios','web','2026-07-17 12:48:52','2026-07-17 12:48:52'),(21,'ver configuracion','web','2026-07-17 12:48:52','2026-07-17 12:48:52'),(22,'administrar configuracion','web','2026-07-17 12:48:52','2026-07-17 12:48:52'),(23,'configurar integraciones configuracion','web','2026-07-17 12:48:52','2026-07-17 12:48:52'),(24,'ver actividad','web','2026-07-17 12:48:52','2026-07-17 12:48:52'),(25,'ver reportes','web','2026-07-17 12:48:52','2026-07-17 12:48:52'),(26,'acceder informacion sensible','web','2026-07-17 12:48:52','2026-07-17 12:48:52'),(27,'usar ia','web','2026-07-17 10:17:45','2026-07-17 10:17:45'),(28,'ver historial ia','web','2026-07-17 10:17:45','2026-07-17 10:17:45'),(29,'configurar ia','web','2026-07-17 10:17:45','2026-07-17 10:17:45'),(30,'ver imagenes','web','2026-07-17 11:00:50','2026-07-17 11:00:50'),(31,'crear imagenes','web','2026-07-17 11:00:50','2026-07-17 11:00:50'),(32,'editar imagenes','web','2026-07-17 11:00:50','2026-07-17 11:00:50'),(33,'eliminar imagenes','web','2026-07-17 11:00:50','2026-07-17 11:00:50'),(34,'ver redes','web','2026-07-17 21:35:37','2026-07-17 21:35:37'),(35,'crear redes','web','2026-07-17 21:35:37','2026-07-17 21:35:37'),(36,'editar redes','web','2026-07-17 21:35:37','2026-07-17 21:35:37'),(37,'eliminar redes','web','2026-07-17 21:35:37','2026-07-17 21:35:37'),(38,'aprobar redes','web','2026-07-17 21:35:37','2026-07-17 21:35:37'),(39,'publicar redes','web','2026-07-17 21:35:37','2026-07-17 21:35:37'),(40,'conectar cuentas redes','web','2026-07-17 21:35:37','2026-07-17 21:35:37'),(41,'ver contactos','web','2026-07-17 22:00:25','2026-07-17 22:00:25'),(42,'crear contactos','web','2026-07-17 22:00:25','2026-07-17 22:00:25'),(43,'editar contactos','web','2026-07-17 22:00:25','2026-07-17 22:00:25'),(44,'eliminar contactos','web','2026-07-17 22:00:25','2026-07-17 22:00:25'),(45,'importar contactos','web','2026-07-17 22:00:25','2026-07-17 22:00:25'),(46,'exportar contactos','web','2026-07-17 22:00:25','2026-07-17 22:00:25'),(47,'ver campanas','web','2026-07-17 22:00:25','2026-07-17 22:00:25'),(48,'crear campanas','web','2026-07-17 22:00:25','2026-07-17 22:00:25'),(49,'editar campanas','web','2026-07-17 22:00:25','2026-07-17 22:00:25'),(50,'eliminar campanas','web','2026-07-17 22:00:25','2026-07-17 22:00:25'),(51,'enviar campanas','web','2026-07-17 22:00:25','2026-07-17 22:00:25'),(52,'configurar campanas','web','2026-07-17 22:00:25','2026-07-17 22:00:25'),(53,'ver landing','web','2026-07-17 22:57:32','2026-07-17 22:57:32'),(54,'crear landing','web','2026-07-17 22:57:32','2026-07-17 22:57:32'),(55,'editar landing','web','2026-07-17 22:57:32','2026-07-17 22:57:32'),(56,'eliminar landing','web','2026-07-17 22:57:32','2026-07-17 22:57:32'),(57,'publicar landing','web','2026-07-17 22:57:32','2026-07-17 22:57:32'),(58,'ver crm','web','2026-07-18 01:22:02','2026-07-18 01:22:02'),(59,'crear crm','web','2026-07-18 01:22:02','2026-07-18 01:22:02'),(60,'editar crm','web','2026-07-18 01:22:02','2026-07-18 01:22:02'),(61,'eliminar crm','web','2026-07-18 01:22:02','2026-07-18 01:22:02'),(62,'asignar crm','web','2026-07-18 01:22:02','2026-07-18 01:22:02');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_images`
--

DROP TABLE IF EXISTS `product_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_images` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `path` varchar(255) NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_images_product_id_foreign` (`product_id`),
  CONSTRAINT `product_images_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_images`
--

LOCK TABLES `product_images` WRITE;
/*!40000 ALTER TABLE `product_images` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sku` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `short_name` varchar(255) DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `collection_id` bigint(20) unsigned DEFAULT NULL,
  `type` enum('producto','servicio') NOT NULL DEFAULT 'servicio',
  `short_description` text DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `benefits` longtext DEFAULT NULL,
  `features` longtext DEFAULT NULL,
  `price` decimal(12,2) DEFAULT NULL,
  `old_price` decimal(12,2) DEFAULT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'MXN',
  `pricing_model` varchar(255) DEFAULT NULL,
  `price_prefix_text` varchar(255) DEFAULT NULL,
  `tax_included` tinyint(1) NOT NULL DEFAULT 1,
  `availability` enum('disponible','agotado','bajo_pedido','proximamente') NOT NULL DEFAULT 'disponible',
  `status` enum('borrador','activo','inactivo','archivado') NOT NULL DEFAULT 'borrador',
  `main_image` varchar(255) DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `demo_url` varchar(255) DEFAULT NULL,
  `whatsapp_url` varchar(255) DEFAULT NULL,
  `whatsapp_message` text DEFAULT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `keywords` text DEFAULT NULL,
  `seo_text` text DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  `structured_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`structured_data`)),
  `published_at` timestamp NULL DEFAULT NULL,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_sku_unique` (`sku`),
  UNIQUE KEY `products_slug_unique` (`slug`),
  KEY `products_category_id_foreign` (`category_id`),
  KEY `products_collection_id_foreign` (`collection_id`),
  KEY `products_created_by_foreign` (`created_by`),
  KEY `products_updated_by_foreign` (`updated_by`),
  KEY `products_status_type_index` (`status`,`type`),
  KEY `products_is_featured_index` (`is_featured`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_collection_id_foreign` FOREIGN KEY (`collection_id`) REFERENCES `collections` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'IA-001','Agente IA para WhatsApp','Agente IA para WhatsApp','agente-ia-para-whatsapp',NULL,1,'servicio','Atiende, responde y vende por WhatsApp las 24 horas con un agente de inteligencia artificial entrenado con la voz de tu marca.','El Agente IA para WhatsApp de NODO 360 conversa con tus clientes en tiempo real, responde preguntas frecuentes, comparte tu catálogo y deriva a un asesor humano cuando la conversación lo requiere. Se integra con la API oficial de WhatsApp Business y aprende de tu base de conocimiento para dar respuestas precisas y consistentes.\n\nConcepto visual sugerido: Smartphone con burbuja de chat futurista, gradiente azul-violeta, icono de WhatsApp estilizado.','- Atención inmediata 24/7 sin perder mensajes\n- Reduce tiempos de respuesta y aumenta la conversión\n- Libera a tu equipo de preguntas repetitivas','- Integración con WhatsApp Business API\n- Entrenamiento con tu catálogo y preguntas frecuentes\n- Escalamiento automático a un asesor humano',4990.00,NULL,'MXN','mensual','Desde',0,'disponible','borrador',NULL,NULL,NULL,NULL,NULL,'Hola, quiero información sobre el Agente IA para WhatsApp de NODO 360.','[\"Inteligencia Artificial\"]','agente ia whatsapp, chatbot whatsapp, atencion automatizada, ia conversacional','Atiende, responde y vende por WhatsApp las 24 horas con un agente de inteligencia artificial entrenado con la voz de tu marca.','Agente IA para WhatsApp | NODO 360','Atiende, responde y vende por WhatsApp las 24 horas con un agente de inteligencia artificial entrenado con la voz de tu marca.',NULL,NULL,0,0,NULL,NULL,'2026-07-17 12:48:53','2026-07-17 21:19:05',NULL),(2,'IA-002','Agente IA para Ventas','Agente IA para Ventas','agente-ia-para-ventas',NULL,1,'servicio','Un vendedor virtual que califica prospectos, resuelve objeciones y agenda citas automáticamente.','El Agente IA para Ventas de NODO 360 acompaña a tus prospectos durante todo el proceso comercial: responde dudas sobre tus productos, presenta opciones según sus necesidades y agenda la siguiente cita con tu equipo de ventas, todo sin intervención humana.\n\nConcepto visual sugerido: Figura de asistente digital con gráfica de crecimiento, tonos azul y rojo NODO.','- Califica prospectos automáticamente\n- Aumenta la tasa de cierre\n- Disponible en todos los canales de contacto','- Guiones de venta configurables\n- Calificación de leads por intención de compra\n- Agenda integrada',5990.00,NULL,'MXN','mensual','Desde',0,'disponible','borrador',NULL,NULL,NULL,NULL,NULL,'Hola, quiero información sobre el Agente IA para Ventas de NODO 360.','[\"Inteligencia Artificial\"]','agente ia ventas, vendedor virtual, automatizacion comercial','Un vendedor virtual que califica prospectos, resuelve objeciones y agenda citas automáticamente.','Agente IA para Ventas | NODO 360','Un vendedor virtual que califica prospectos, resuelve objeciones y agenda citas automáticamente.',NULL,NULL,1,0,NULL,NULL,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(3,'IA-003','Agente IA para Atención al Cliente','Agente IA para Atención al Cliente','agente-ia-para-atencion-al-cliente',NULL,1,'servicio','Resuelve dudas, da seguimiento a pedidos y soporta a tus clientes sin filas de espera.','Diseñado para reducir la carga operativa de tu equipo de soporte, el Agente IA para Atención al Cliente responde preguntas frecuentes, da seguimiento a pedidos y escala casos complejos al área correspondiente, manteniendo un tono consistente con tu marca.\n\nConcepto visual sugerido: Icono de auriculares de soporte fusionado con un nodo digital, fondo blanco con gradiente sutil.','- Reduce el tiempo de espera de tus clientes\n- Disminuye la carga del equipo de soporte\n- Mejora la satisfacción del cliente','- Base de conocimiento configurable\n- Historial de conversaciones\n- Reportes de satisfacción',4990.00,NULL,'MXN','mensual','Desde',0,'disponible','borrador',NULL,NULL,NULL,NULL,NULL,'Hola, quiero información sobre el Agente IA para Atención al Cliente de NODO 360.','[\"Inteligencia Artificial\"]','agente ia atencion al cliente, soporte automatizado, ia servicio al cliente','Resuelve dudas, da seguimiento a pedidos y soporta a tus clientes sin filas de espera.','Agente IA para Atención al Cliente | NODO 360','Resuelve dudas, da seguimiento a pedidos y soporta a tus clientes sin filas de espera.',NULL,NULL,2,0,NULL,NULL,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(4,'IA-004','Agente IA para Agendar Citas','Agente IA para Agendar Citas','agente-ia-para-agendar-citas',NULL,1,'servicio','Agenda, confirma y recuerda citas automáticamente, sin choques de horario ni llamadas perdidas.','El Agente IA para Agendar Citas conecta tu calendario con WhatsApp, redes sociales y tu sitio web para que tus clientes reserven un horario disponible, reciban confirmaciones y recordatorios automáticos, reduciendo las inasistencias.\n\nConcepto visual sugerido: Calendario digital con check azul y acentos rojos, estilo minimalista.','- Elimina los choques de horario\n- Reduce inasistencias con recordatorios automáticos\n- Disponible fuera de horario de oficina','- Sincronización con calendario\n- Confirmaciones y recordatorios automáticos\n- Reprogramación sin intervención humana',3990.00,NULL,'MXN','mensual','Desde',0,'disponible','borrador',NULL,NULL,NULL,NULL,NULL,'Hola, quiero información sobre el Agente IA para Agendar Citas de NODO 360.','[\"Inteligencia Artificial\"]','agenda citas ia, reservas automaticas, agente ia citas','Agenda, confirma y recuerda citas automáticamente, sin choques de horario ni llamadas perdidas.','Agente IA para Agendar Citas | NODO 360','Agenda, confirma y recuerda citas automáticamente, sin choques de horario ni llamadas perdidas.',NULL,NULL,3,0,NULL,NULL,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(5,'IA-005','Agente IA para Cotizaciones','Agente IA para Cotizaciones','agente-ia-para-cotizaciones',NULL,1,'servicio','Genera cotizaciones personalizadas en segundos a partir de las necesidades de cada cliente.','El Agente IA para Cotizaciones interpreta las necesidades del cliente y genera una propuesta con precios, condiciones y tiempos de entrega, lista para enviarse por WhatsApp, correo o descargar en PDF.\n\nConcepto visual sugerido: Documento tipo cotización con sello digital azul-violeta y símbolo de rayo IA.','- Cotiza en segundos, no en horas\n- Reduce errores de captura\n- Mejora la experiencia del cliente','- Plantillas de cotización personalizables\n- Cálculo automático de precios\n- Envío directo por WhatsApp o email',4490.00,NULL,'MXN','mensual','Desde',0,'disponible','borrador',NULL,NULL,NULL,NULL,NULL,'Hola, quiero información sobre el Agente IA para Cotizaciones de NODO 360.','[\"Inteligencia Artificial\"]','cotizador ia, cotizaciones automaticas, agente ia cotizaciones','Genera cotizaciones personalizadas en segundos a partir de las necesidades de cada cliente.','Agente IA para Cotizaciones | NODO 360','Genera cotizaciones personalizadas en segundos a partir de las necesidades de cada cliente.',NULL,NULL,4,0,NULL,NULL,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(6,'IA-006','Agente IA para Seguimiento de Prospectos','Agente IA para Seguimiento de Prospectos','agente-ia-para-seguimiento-de-prospectos',NULL,1,'servicio','Da seguimiento constante a tus prospectos hasta convertirlos en clientes, sin que se te escape ninguno.','El Agente IA para Seguimiento de Prospectos identifica leads fríos, reactiva conversaciones detenidas y avisa a tu equipo de ventas cuando un prospecto está listo para cerrar, evitando fugas en el embudo comercial.\n\nConcepto visual sugerido: Embudo digital con puntos de luz descendiendo, colores azul y violeta.','- Ningún prospecto se queda sin seguimiento\n- Reactiva leads fríos automáticamente\n- Prioriza a los prospectos más calientes','- Segmentación automática de prospectos\n- Recordatorios y reactivación programada\n- Alertas al equipo de ventas',4490.00,NULL,'MXN','mensual','Desde',0,'disponible','borrador',NULL,NULL,NULL,NULL,NULL,'Hola, quiero información sobre el Agente IA para Seguimiento de Prospectos de NODO 360.','[\"Inteligencia Artificial\"]','seguimiento de prospectos ia, nurturing automatizado, agente ia leads','Da seguimiento constante a tus prospectos hasta convertirlos en clientes, sin que se te escape ninguno.','Agente IA para Seguimiento de Prospectos | NODO 360','Da seguimiento constante a tus prospectos hasta convertirlos en clientes, sin que se te escape ninguno.',NULL,NULL,5,0,NULL,NULL,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(7,'AUT-001','Automatización Empresarial','Automatización Empresarial','automatizacion-empresarial',NULL,2,'servicio','Conecta tus herramientas y automatiza procesos repetitivos para que tu equipo se enfoque en lo importante.','El servicio de Automatización Empresarial de NODO 360 identifica los procesos manuales de tu operación y los conecta mediante flujos automatizados entre tus sistemas actuales, reduciendo errores y tiempos operativos.\n\nConcepto visual sugerido: Engranajes conectados por líneas de luz azul y violeta sobre fondo blanco.','- Reduce tareas manuales repetitivas\n- Disminuye errores operativos\n- Libera tiempo de tu equipo para tareas estratégicas','- Diagnóstico de procesos\n- Integración entre sistemas existentes\n- Flujos de trabajo automatizados',6990.00,NULL,'MXN','mensual','Desde',0,'disponible','borrador',NULL,NULL,NULL,NULL,NULL,'Hola, quiero información sobre Automatización Empresarial de NODO 360.','[\"Automatizaci\\u00f3n Empresarial\"]','automatizacion empresarial, automatizacion de procesos, eficiencia operativa','Conecta tus herramientas y automatiza procesos repetitivos para que tu equipo se enfoque en lo importante.','Automatización Empresarial | NODO 360','Conecta tus herramientas y automatiza procesos repetitivos para que tu equipo se enfoque en lo importante.',NULL,NULL,0,0,NULL,NULL,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(8,'AUT-002','Automatización de Ventas','Automatización de Ventas','automatizacion-de-ventas',NULL,2,'servicio','Automatiza el seguimiento comercial desde el primer contacto hasta el cierre de venta.','Automatiza recordatorios, seguimientos y actualizaciones del embudo de ventas para que ningún prospecto se pierda y tu equipo comercial trabaje sobre información siempre actualizada.\n\nConcepto visual sugerido: Gráfica de embudo ascendente con acentos rojos sobre fondo azul oscuro.','- Embudo de ventas siempre actualizado\n- Seguimiento automático a prospectos\n- Menos tareas administrativas para el equipo de ventas','- Integración con CRM\n- Recordatorios automáticos\n- Reportes de desempeño comercial',5990.00,NULL,'MXN','mensual','Desde',0,'disponible','borrador',NULL,NULL,NULL,NULL,NULL,'Hola, quiero información sobre Automatización de Ventas de NODO 360.','[\"Automatizaci\\u00f3n Empresarial\"]','automatizacion de ventas, crm automatizado, embudo de ventas','Automatiza el seguimiento comercial desde el primer contacto hasta el cierre de venta.','Automatización de Ventas | NODO 360','Automatiza el seguimiento comercial desde el primer contacto hasta el cierre de venta.',NULL,NULL,1,0,NULL,NULL,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(9,'AUT-003','Automatización de Inventarios','Automatización de Inventarios','automatizacion-de-inventarios',NULL,2,'servicio','Controla existencias, alertas de stock y reabastecimiento sin hojas de cálculo manuales.','El servicio de Automatización de Inventarios sincroniza tus existencias entre canales de venta, genera alertas de stock bajo y facilita el reabastecimiento oportuno, evitando pérdidas por falta o exceso de inventario.\n\nConcepto visual sugerido: Cajas apiladas con código de barras digital y gráfica de nivel de stock.','- Evita quiebres y excesos de inventario\n- Sincroniza existencias entre canales\n- Alertas automáticas de reabastecimiento','- Sincronización multicanal\n- Alertas de stock mínimo\n- Reportes de rotación de inventario',5990.00,NULL,'MXN','mensual','Desde',0,'disponible','borrador',NULL,NULL,NULL,NULL,NULL,'Hola, quiero información sobre Automatización de Inventarios de NODO 360.','[\"Automatizaci\\u00f3n Empresarial\"]','automatizacion de inventarios, control de stock, gestion de inventario','Controla existencias, alertas de stock y reabastecimiento sin hojas de cálculo manuales.','Automatización de Inventarios | NODO 360','Controla existencias, alertas de stock y reabastecimiento sin hojas de cálculo manuales.',NULL,NULL,2,0,NULL,NULL,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(10,'AUT-004','Automatización de Reservas','Automatización de Reservas','automatizacion-de-reservas',NULL,2,'servicio','Gestiona reservas y disponibilidad en tiempo real sin intervención manual.','Ideal para negocios con horarios y disponibilidad limitada, este servicio automatiza la gestión de reservas, evita sobrecupos y envía confirmaciones y recordatorios a tus clientes.\n\nConcepto visual sugerido: Calendario digital con espacios reservados en azul y disponibles en blanco.','- Elimina sobrecupos y errores de agenda\n- Confirmaciones automáticas\n- Disponibilidad visible en tiempo real','- Calendario de disponibilidad en tiempo real\n- Confirmaciones y recordatorios automáticos\n- Panel de gestión de reservas',4990.00,NULL,'MXN','mensual','Desde',0,'disponible','borrador',NULL,NULL,NULL,NULL,NULL,'Hola, quiero información sobre Automatización de Reservas de NODO 360.','[\"Automatizaci\\u00f3n Empresarial\"]','automatizacion de reservas, sistema de reservas, gestion de citas','Gestiona reservas y disponibilidad en tiempo real sin intervención manual.','Automatización de Reservas | NODO 360','Gestiona reservas y disponibilidad en tiempo real sin intervención manual.',NULL,NULL,3,0,NULL,NULL,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(11,'AUT-005','Automatización de Cobranza','Automatización de Cobranza','automatizacion-de-cobranza',NULL,2,'servicio','Da seguimiento a pagos pendientes y recordatorios de cobro sin desgastar la relación con tus clientes.','Automatiza el proceso de cobranza con recordatorios oportunos, seguimiento a facturas vencidas y reportes de cartera, manteniendo una comunicación profesional y consistente con tus clientes.\n\nConcepto visual sugerido: Factura digital con reloj de recordatorio y acento rojo de alerta.','- Mejora el flujo de efectivo\n- Reduce la cartera vencida\n- Comunicación de cobro consistente y profesional','- Recordatorios automáticos de pago\n- Seguimiento de facturas vencidas\n- Reportes de cartera',4990.00,NULL,'MXN','mensual','Desde',0,'disponible','borrador',NULL,NULL,NULL,NULL,NULL,'Hola, quiero información sobre Automatización de Cobranza de NODO 360.','[\"Automatizaci\\u00f3n Empresarial\"]','automatizacion de cobranza, recordatorios de pago, gestion de cartera','Da seguimiento a pagos pendientes y recordatorios de cobro sin desgastar la relación con tus clientes.','Automatización de Cobranza | NODO 360','Da seguimiento a pagos pendientes y recordatorios de cobro sin desgastar la relación con tus clientes.',NULL,NULL,4,0,NULL,NULL,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(12,'AUT-006','Automatización de Email Marketing','Automatización de Email Marketing','automatizacion-de-email-marketing',NULL,2,'servicio','Envía el mensaje correcto al contacto correcto en el momento adecuado, de forma automática.','Configura secuencias automáticas de correo para dar la bienvenida, nutrir prospectos y reactivar clientes, basadas en el comportamiento e intereses de cada contacto.\n\nConcepto visual sugerido: Sobre digital con múltiples flechas de envío automatizado, gradiente violeta.','- Comunicación oportuna sin esfuerzo manual\n- Aumenta la tasa de apertura y clics\n- Nutre prospectos hasta convertirlos en clientes','- Secuencias automáticas configurables\n- Segmentación por comportamiento\n- Reportes de apertura y clics',3990.00,NULL,'MXN','mensual','Desde',0,'disponible','borrador',NULL,NULL,NULL,NULL,NULL,'Hola, quiero información sobre Automatización de Email Marketing de NODO 360.','[\"Automatizaci\\u00f3n Empresarial\"]','automatizacion email marketing, secuencias de correo, nutricion de leads','Envía el mensaje correcto al contacto correcto en el momento adecuado, de forma automática.','Automatización de Email Marketing | NODO 360','Envía el mensaje correcto al contacto correcto en el momento adecuado, de forma automática.',NULL,NULL,5,0,NULL,NULL,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(13,'AUT-007','Automatización de Redes Sociales','Automatización de Redes Sociales','automatizacion-de-redes-sociales',NULL,2,'servicio','Programa y publica contenido en tus redes sociales de forma constante y sin esfuerzo diario.','Automatiza la programación y publicación de contenido en tus canales sociales, manteniendo una presencia constante sin necesidad de publicar manualmente cada día.\n\nConcepto visual sugerido: Calendario editorial con iconos de redes sociales flotando, tonos azul-violeta.','- Presencia constante en redes sociales\n- Ahorra horas de trabajo manual\n- Calendario editorial siempre organizado','- Programación multicanal\n- Calendario editorial visual\n- Reportes de publicación',4490.00,NULL,'MXN','mensual','Desde',0,'disponible','borrador',NULL,NULL,NULL,NULL,NULL,'Hola, quiero información sobre Automatización de Redes Sociales de NODO 360.','[\"Automatizaci\\u00f3n Empresarial\"]','automatizacion redes sociales, programacion de contenido, gestion de redes','Programa y publica contenido en tus redes sociales de forma constante y sin esfuerzo diario.','Automatización de Redes Sociales | NODO 360','Programa y publica contenido en tus redes sociales de forma constante y sin esfuerzo diario.',NULL,NULL,6,0,NULL,NULL,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(14,'SOF-001','CRM Inteligente','CRM Inteligente','crm-inteligente',NULL,3,'servicio','Organiza a tus prospectos y clientes en un solo lugar con inteligencia artificial integrada.','El CRM Inteligente de NODO 360 centraliza la información de tus prospectos y clientes, prioriza oportunidades con ayuda de inteligencia artificial y da visibilidad completa del embudo comercial a tu equipo.\n\nConcepto visual sugerido: Panel Kanban digital con tarjetas de clientes, colores azul y blanco.','- Toda la información comercial en un solo lugar\n- Prioriza oportunidades con IA\n- Visibilidad total del embudo de ventas','- Pipeline visual tipo Kanban\n- Historial de interacciones por contacto\n- Reportes y métricas comerciales',7990.00,NULL,'MXN','mensual','Desde',0,'disponible','borrador',NULL,NULL,NULL,NULL,NULL,'Hola, quiero información sobre el CRM Inteligente de NODO 360.','[\"Software Empresarial\"]','crm inteligente, crm con ia, gestion de clientes','Organiza a tus prospectos y clientes en un solo lugar con inteligencia artificial integrada.','CRM Inteligente | NODO 360','Organiza a tus prospectos y clientes en un solo lugar con inteligencia artificial integrada.',NULL,NULL,0,0,NULL,NULL,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(15,'SOF-002','Sistemas a la Medida','Sistemas a la Medida','sistemas-a-la-medida',NULL,3,'servicio','Desarrollamos el sistema que tu operación necesita, adaptado a tus procesos reales.','Diseñamos y desarrollamos sistemas a la medida cuando las soluciones genéricas no se ajustan a tu operación, garantizando que la herramienta se adapte a tu negocio y no al revés.\n\nConcepto visual sugerido: Planos digitales de software con bloques modulares en azul y violeta.','- Solución adaptada a tus procesos exactos\n- Escalable conforme crece tu negocio\n- Soporte y mantenimiento incluido','- Levantamiento de requerimientos\n- Desarrollo a la medida\n- Documentación técnica y capacitación',25000.00,NULL,'MXN','proyecto','Desde',0,'disponible','borrador',NULL,NULL,NULL,NULL,NULL,'Hola, quiero información sobre Sistemas a la Medida de NODO 360.','[\"Software Empresarial\"]','sistemas a la medida, desarrollo de software, software personalizado','Desarrollamos el sistema que tu operación necesita, adaptado a tus procesos reales.','Sistemas a la Medida | NODO 360','Desarrollamos el sistema que tu operación necesita, adaptado a tus procesos reales.',NULL,NULL,1,0,NULL,NULL,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(16,'SOF-003','Sistema para Hoteles y Moteles','Sistema para Hoteles y Moteles','sistema-para-hoteles-y-moteles',NULL,3,'servicio','Gestiona reservaciones, tarifas y ocupación de tu hotel o motel desde un solo sistema.','Sistema especializado para la operación diaria de hoteles y moteles: control de reservaciones, disponibilidad de habitaciones, tarifas por temporada y reportes de ocupación.\n\nConcepto visual sugerido: Icono de llave de habitación digital sobre mapa de ocupación por pisos.','- Control total de la ocupación en tiempo real\n- Reduce errores de sobreventa\n- Reportes de ingresos por habitación','- Panel de reservaciones y disponibilidad\n- Gestión de tarifas por temporada\n- Reportes de ocupación e ingresos',9990.00,NULL,'MXN','mensual','Desde',0,'disponible','borrador',NULL,NULL,NULL,NULL,NULL,'Hola, quiero información sobre el Sistema para Hoteles y Moteles de NODO 360.','[\"Software Empresarial\"]','sistema para hoteles, software hotelero, gestion de moteles','Gestiona reservaciones, tarifas y ocupación de tu hotel o motel desde un solo sistema.','Sistema para Hoteles y Moteles | NODO 360','Gestiona reservaciones, tarifas y ocupación de tu hotel o motel desde un solo sistema.',NULL,NULL,2,0,NULL,NULL,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(17,'SOF-004','Control de Inventarios','Control de Inventarios','control-de-inventarios',NULL,3,'servicio','Sistema de control de inventarios con alertas y reportes en tiempo real.','Plataforma para el control de existencias, entradas, salidas y valuación de inventario, con alertas automáticas de stock mínimo y reportes de rotación.\n\nConcepto visual sugerido: Estantería de almacén digitalizada con indicadores de nivel de stock.','- Visibilidad en tiempo real de tu inventario\n- Alertas de stock mínimo\n- Reportes de valuación y rotación','- Registro de entradas y salidas\n- Alertas de stock mínimo\n- Reportes de valuación de inventario',6990.00,NULL,'MXN','mensual','Desde',0,'disponible','borrador',NULL,NULL,NULL,NULL,NULL,'Hola, quiero información sobre el sistema de Control de Inventarios de NODO 360.','[\"Software Empresarial\"]','control de inventarios, sistema de inventario, gestion de almacen','Sistema de control de inventarios con alertas y reportes en tiempo real.','Control de Inventarios | NODO 360','Sistema de control de inventarios con alertas y reportes en tiempo real.',NULL,NULL,3,0,NULL,NULL,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(18,'SOF-005','ERP Empresarial','ERP Empresarial','erp-empresarial',NULL,3,'servicio','Integra finanzas, inventarios, ventas y operación en una sola plataforma.','El ERP Empresarial de NODO 360 conecta las áreas clave de tu negocio en una sola plataforma, eliminando la duplicidad de información y dando visibilidad completa a la dirección.\n\nConcepto visual sugerido: Red de módulos conectados alrededor de un núcleo central azul.','- Información unificada entre áreas\n- Elimina duplicidad de datos\n- Visibilidad completa para la dirección','- Módulos de finanzas, inventario y ventas\n- Reportes ejecutivos integrados\n- Control de accesos por rol',14990.00,NULL,'MXN','mensual','Desde',0,'disponible','borrador',NULL,NULL,NULL,NULL,NULL,'Hola, quiero información sobre el ERP Empresarial de NODO 360.','[\"Software Empresarial\"]','erp empresarial, sistema erp, planificacion de recursos','Integra finanzas, inventarios, ventas y operación en una sola plataforma.','ERP Empresarial | NODO 360','Integra finanzas, inventarios, ventas y operación en una sola plataforma.',NULL,NULL,4,0,NULL,NULL,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(19,'SOF-006','Aplicaciones Móviles','Aplicaciones Móviles','aplicaciones-moviles',NULL,3,'servicio','Llevamos tu negocio al bolsillo de tus clientes con una aplicación móvil a la medida.','Desarrollamos aplicaciones móviles para iOS y Android, alineadas a tus procesos de negocio y a la experiencia que quieres ofrecer a tus clientes o colaboradores.\n\nConcepto visual sugerido: Silueta de teléfono móvil con interfaz de app y acentos rojos NODO.','- Presencia directa en el dispositivo del cliente\n- Experiencia de usuario a la medida\n- Disponible en iOS y Android','- Diseño UX/UI a la medida\n- Publicación en tiendas de aplicaciones\n- Mantenimiento y actualizaciones',25000.00,NULL,'MXN','proyecto','Desde',0,'disponible','borrador',NULL,NULL,NULL,NULL,NULL,'Hola, quiero información sobre Aplicaciones Móviles de NODO 360.','[\"Software Empresarial\"]','aplicaciones moviles, desarrollo de apps, app a la medida','Llevamos tu negocio al bolsillo de tus clientes con una aplicación móvil a la medida.','Aplicaciones Móviles | NODO 360','Llevamos tu negocio al bolsillo de tus clientes con una aplicación móvil a la medida.',NULL,NULL,5,0,NULL,NULL,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(20,'SOF-007','Integración de APIs','Integración de APIs','integracion-de-apis',NULL,3,'servicio','Conectamos tus sistemas actuales entre sí para que la información fluya sin fricción.','Diseñamos e implementamos integraciones entre tus plataformas actuales mediante APIs, evitando la doble captura de información y automatizando el intercambio de datos entre sistemas.\n\nConcepto visual sugerido: Nodos conectados por cables de luz representando el flujo de datos entre sistemas.','- Elimina la doble captura de información\n- Sistemas conectados y sincronizados\n- Reduce errores manuales','- Diagnóstico de integración\n- Desarrollo de conectores a la medida\n- Monitoreo de integraciones activas',12990.00,NULL,'MXN','proyecto','Desde',0,'disponible','borrador',NULL,NULL,NULL,NULL,NULL,'Hola, quiero información sobre Integración de APIs de NODO 360.','[\"Software Empresarial\"]','integracion de apis, conectores a medida, integracion de sistemas','Conectamos tus sistemas actuales entre sí para que la información fluya sin fricción.','Integración de APIs | NODO 360','Conectamos tus sistemas actuales entre sí para que la información fluya sin fricción.',NULL,NULL,6,0,NULL,NULL,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(21,'GRW-001','Marketing Inteligente con IA','Marketing Inteligente con IA','marketing-inteligente-con-ia',NULL,4,'servicio','Estrategias de marketing potenciadas con inteligencia artificial para crecer de forma medible.','Combinamos estrategia de marketing con herramientas de inteligencia artificial para segmentar audiencias, generar contenido y optimizar campañas con base en datos reales.\n\nConcepto visual sugerido: Gráfica de crecimiento con destello de IA en tonos azul y rojo.','- Decisiones basadas en datos, no en suposiciones\n- Campañas optimizadas continuamente\n- Mejor retorno de inversión publicitaria','- Segmentación de audiencias con IA\n- Generación de contenido asistido\n- Reportes de desempeño de campañas',8990.00,NULL,'MXN','mensual','Desde',0,'disponible','borrador',NULL,NULL,NULL,NULL,NULL,'Hola, quiero información sobre Marketing Inteligente con IA de NODO 360.','[\"Growth Marketing\"]','marketing con ia, marketing inteligente, estrategia digital','Estrategias de marketing potenciadas con inteligencia artificial para crecer de forma medible.','Marketing Inteligente con IA | NODO 360','Estrategias de marketing potenciadas con inteligencia artificial para crecer de forma medible.',NULL,NULL,0,0,NULL,NULL,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(22,'GRW-002','Google Ads y Meta Ads','Google Ads y Meta Ads','google-ads-y-meta-ads',NULL,4,'servicio','Campañas publicitarias en Google y Meta administradas por especialistas y optimizadas con datos.','Gestionamos tus campañas de Google Ads y Meta Ads (Facebook e Instagram), desde la estrategia y segmentación hasta la optimización continua para mejorar tu costo por resultado.\n\nConcepto visual sugerido: Logos estilizados de Google y Meta con gráfica de resultados ascendente.','- Mayor alcance en los canales donde está tu cliente\n- Optimización continua del presupuesto\n- Reportes claros de retorno de inversión','- Gestión de campañas en Google y Meta\n- Segmentación avanzada de audiencias\n- Reportes mensuales de desempeño',6990.00,NULL,'MXN','mensual','Desde',0,'disponible','borrador',NULL,NULL,NULL,NULL,NULL,'Hola, quiero información sobre Google Ads y Meta Ads de NODO 360.','[\"Growth Marketing\"]','google ads, meta ads, publicidad digital, pauta publicitaria','Campañas publicitarias en Google y Meta administradas por especialistas y optimizadas con datos.','Google Ads y Meta Ads | NODO 360','Campañas publicitarias en Google y Meta administradas por especialistas y optimizadas con datos.',NULL,NULL,1,0,NULL,NULL,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(23,'GRW-003','Landing Pages de Alta Conversión','Landing Pages de Alta Conversión','landing-pages-de-alta-conversion',NULL,4,'servicio','Páginas de aterrizaje diseñadas para convertir visitantes en clientes.','Diseñamos landing pages enfocadas en conversión, con estructura persuasiva, carga rápida y llamadas a la acción claras, alineadas a tus campañas de marketing.\n\nConcepto visual sugerido: Maqueta de página web en dispositivo móvil con botón de llamada a la acción rojo.','- Mayor tasa de conversión de visitantes\n- Diseño alineado a tus campañas\n- Carga rápida y optimizada para móviles','- Estructura orientada a conversión\n- Formularios de captura integrados\n- Optimización para SEO y redes sociales',4990.00,NULL,'MXN','proyecto','Desde',0,'disponible','borrador',NULL,NULL,NULL,NULL,NULL,'Hola, quiero información sobre Landing Pages de Alta Conversión de NODO 360.','[\"Growth Marketing\"]','landing pages, paginas de aterrizaje, alta conversion','Páginas de aterrizaje diseñadas para convertir visitantes en clientes.','Landing Pages de Alta Conversión | NODO 360','Páginas de aterrizaje diseñadas para convertir visitantes en clientes.',NULL,NULL,2,0,NULL,NULL,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(24,'GRW-004','Optimización para IA, AEO y GEO','Optimización para IA, AEO y GEO','optimizacion-para-ia-aeo-y-geo',NULL,4,'servicio','Prepara tu contenido para ser encontrado por buscadores tradicionales y motores de IA.','Optimizamos tu sitio y contenido para mejorar su desempeño en buscadores tradicionales (SEO), motores de respuesta como asistentes de IA (AEO) y búsqueda generativa (GEO), sin prometer resultados garantizados en plataformas que NODO 360 no controla.\n\nConcepto visual sugerido: Radar digital escaneando un sitio web, con iconos de buscadores e IA.','- Contenido mejor estructurado para IA y buscadores\n- Mayor claridad semántica del sitio\n- Base sólida para estrategias de contenido','- Auditoría de SEO, AEO y GEO\n- Recomendaciones de datos estructurados\n- Checklist de optimización priorizado',5990.00,NULL,'MXN','proyecto','Desde',0,'disponible','borrador',NULL,NULL,NULL,NULL,NULL,'Hola, quiero información sobre Optimización para IA, AEO y GEO de NODO 360.','[\"Growth Marketing\"]','aeo, geo, seo con ia, optimizacion para buscadores','Prepara tu contenido para ser encontrado por buscadores tradicionales y motores de IA.','Optimización para IA, AEO y GEO | NODO 360','Prepara tu contenido para ser encontrado por buscadores tradicionales y motores de IA.',NULL,NULL,3,0,NULL,NULL,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(25,'GRW-005','Email Marketing Inteligente','Email Marketing Inteligente','email-marketing-inteligente',NULL,4,'servicio','Campañas de correo segmentadas que llegan al contacto correcto en el momento correcto.','Diseñamos y gestionamos campañas de email marketing segmentadas, con contenido relevante para cada etapa del cliente y reportes de apertura, clics y conversión.\n\nConcepto visual sugerido: Sobre digital abriéndose con gráfica de apertura de correos.','- Comunicación relevante para cada segmento\n- Mejora la relación con tus contactos\n- Reportes claros de desempeño','- Segmentación de listas de contactos\n- Plantillas de correo profesionales\n- Reportes de apertura y clics',3990.00,NULL,'MXN','mensual','Desde',0,'disponible','borrador',NULL,NULL,NULL,NULL,NULL,'Hola, quiero información sobre Email Marketing Inteligente de NODO 360.','[\"Growth Marketing\"]','email marketing, campañas de correo, marketing por email','Campañas de correo segmentadas que llegan al contacto correcto en el momento correcto.','Email Marketing Inteligente | NODO 360','Campañas de correo segmentadas que llegan al contacto correcto en el momento correcto.',NULL,NULL,4,0,NULL,NULL,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(26,'GRW-006','Gestión de Redes Sociales','Gestión de Redes Sociales','gestion-de-redes-sociales',NULL,4,'servicio','Estrategia, contenido y publicación constante en tus redes sociales.','Nos encargamos de la estrategia de contenido, diseño y publicación en tus redes sociales, manteniendo una presencia de marca consistente y profesional.\n\nConcepto visual sugerido: Mosaico de publicaciones de redes sociales en tonos de marca NODO.','- Presencia de marca consistente\n- Contenido alineado a tu identidad visual\n- Reportes de crecimiento y alcance','- Calendario de contenido mensual\n- Diseño de piezas gráficas\n- Reportes de alcance e interacción',5990.00,NULL,'MXN','mensual','Desde',0,'disponible','borrador',NULL,NULL,NULL,NULL,NULL,'Hola, quiero información sobre Gestión de Redes Sociales de NODO 360.','[\"Growth Marketing\"]','gestion de redes sociales, community management, contenido para redes','Estrategia, contenido y publicación constante en tus redes sociales.','Gestión de Redes Sociales | NODO 360','Estrategia, contenido y publicación constante en tus redes sociales.',NULL,NULL,5,0,NULL,NULL,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(27,'GRW-007','Generación de Contenido con IA','Generación de Contenido con IA','generacion-de-contenido-con-ia',NULL,4,'servicio','Contenido de marketing generado con inteligencia artificial, revisado y aprobado por tu equipo.','Utilizamos inteligencia artificial para acelerar la generación de textos, descripciones y publicaciones de marketing, siempre con un proceso de revisión y aprobación antes de publicarse.\n\nConcepto visual sugerido: Pluma digital escribiendo sobre una pantalla, con destellos de IA en azul y violeta.','- Acelera la producción de contenido\n- Mantiene consistencia de tono de marca\n- Contenido revisado antes de publicarse','- Generación de textos con IA\n- Flujo de revisión y aprobación\n- Variantes de contenido por canal',4490.00,NULL,'MXN','mensual','Desde',0,'disponible','borrador',NULL,NULL,NULL,NULL,NULL,'Hola, quiero información sobre Generación de Contenido con IA de NODO 360.','[\"Growth Marketing\"]','generacion de contenido con ia, copywriting con ia, contenido de marketing','Contenido de marketing generado con inteligencia artificial, revisado y aprobado por tu equipo.','Generación de Contenido con IA | NODO 360','Contenido de marketing generado con inteligencia artificial, revisado y aprobado por tu equipo.',NULL,NULL,6,0,NULL,NULL,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(28,'IND-001','IA para Hoteles y Moteles','IA para Hoteles y Moteles','ia-para-hoteles-y-moteles',NULL,5,'servicio','Automatiza reservaciones y atención al huésped con inteligencia artificial.','Solución de inteligencia artificial diseñada para hoteles y moteles: atiende consultas de disponibilidad, gestiona reservaciones y da seguimiento a huéspedes antes, durante y después de su estancia.\n\nConcepto visual sugerido: Fachada de hotel estilizada con burbuja de chat flotante.','- Atención a huéspedes las 24 horas\n- Reduce llamadas y mensajes sin responder\n- Mejora la experiencia de reservación','- Consulta de disponibilidad automatizada\n- Confirmaciones y recordatorios de reservación\n- Seguimiento post-estancia',6990.00,NULL,'MXN','mensual','Desde',0,'disponible','borrador',NULL,NULL,NULL,NULL,NULL,'Hola, quiero información sobre IA para Hoteles y Moteles de NODO 360.','[\"Soluciones por Industria\"]','ia para hoteles, ia para moteles, automatizacion hotelera','Automatiza reservaciones y atención al huésped con inteligencia artificial.','IA para Hoteles y Moteles | NODO 360','Automatiza reservaciones y atención al huésped con inteligencia artificial.',NULL,NULL,0,0,NULL,NULL,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(29,'IND-002','IA para Despachos Jurídicos','IA para Despachos Jurídicos','ia-para-despachos-juridicos',NULL,5,'servicio','Atiende consultas iniciales y agenda citas para tu despacho jurídico automáticamente.','Solución de inteligencia artificial para despachos jurídicos que atiende consultas iniciales de clientes potenciales, clasifica el tipo de caso y agenda una cita con el abogado correspondiente.\n\nConcepto visual sugerido: Balanza de la justicia estilizada con líneas digitales azules.','- Filtra y clasifica consultas automáticamente\n- Agenda citas sin intervención manual\n- Mejora el primer contacto con el cliente','- Clasificación de tipo de caso\n- Agenda integrada con el despacho\n- Respuestas basadas en tu área de práctica',5990.00,NULL,'MXN','mensual','Desde',0,'disponible','borrador',NULL,NULL,NULL,NULL,NULL,'Hola, quiero información sobre IA para Despachos Jurídicos de NODO 360.','[\"Soluciones por Industria\"]','ia para abogados, ia para despachos juridicos, automatizacion legal','Atiende consultas iniciales y agenda citas para tu despacho jurídico automáticamente.','IA para Despachos Jurídicos | NODO 360','Atiende consultas iniciales y agenda citas para tu despacho jurídico automáticamente.',NULL,NULL,1,0,NULL,NULL,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(30,'IND-003','IA para Clínicas','IA para Clínicas','ia-para-clinicas',NULL,5,'servicio','Agenda citas médicas y resuelve dudas de pacientes de forma automática.','Solución de inteligencia artificial para clínicas y consultorios que gestiona la agenda de citas, envía recordatorios a pacientes y responde preguntas frecuentes sobre servicios y horarios.\n\nConcepto visual sugerido: Cruz médica estilizada junto a un calendario digital azul.','- Reduce inasistencias con recordatorios automáticos\n- Libera a tu equipo de recepción\n- Atención disponible fuera de horario','- Agenda de citas médicas\n- Recordatorios automáticos a pacientes\n- Respuestas a preguntas frecuentes',5990.00,NULL,'MXN','mensual','Desde',0,'disponible','borrador',NULL,NULL,NULL,NULL,NULL,'Hola, quiero información sobre IA para Clínicas de NODO 360.','[\"Soluciones por Industria\"]','ia para clinicas, ia para consultorios, agenda medica automatizada','Agenda citas médicas y resuelve dudas de pacientes de forma automática.','IA para Clínicas | NODO 360','Agenda citas médicas y resuelve dudas de pacientes de forma automática.',NULL,NULL,2,0,NULL,NULL,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(31,'IND-004','IA para Inmobiliarias','IA para Inmobiliarias','ia-para-inmobiliarias',NULL,5,'servicio','Califica prospectos y agenda visitas a propiedades de forma automática.','Solución de inteligencia artificial para inmobiliarias que responde consultas sobre propiedades disponibles, califica el interés del prospecto y agenda visitas con el asesor correspondiente.\n\nConcepto visual sugerido: Silueta de casa con pin de ubicación digital y acentos azules.','- Responde consultas de propiedades al instante\n- Califica prospectos por nivel de interés\n- Agenda visitas automáticamente','- Catálogo de propiedades integrado\n- Calificación automática de prospectos\n- Agenda de visitas',5990.00,NULL,'MXN','mensual','Desde',0,'disponible','borrador',NULL,NULL,NULL,NULL,NULL,'Hola, quiero información sobre IA para Inmobiliarias de NODO 360.','[\"Soluciones por Industria\"]','ia para inmobiliarias, ia bienes raices, automatizacion inmobiliaria','Califica prospectos y agenda visitas a propiedades de forma automática.','IA para Inmobiliarias | NODO 360','Califica prospectos y agenda visitas a propiedades de forma automática.',NULL,NULL,3,0,NULL,NULL,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(32,'IND-005','IA para Restaurantes','IA para Restaurantes','ia-para-restaurantes',NULL,5,'servicio','Toma reservaciones, pedidos y responde preguntas frecuentes de tu restaurante.','Solución de inteligencia artificial para restaurantes que gestiona reservaciones, responde preguntas sobre el menú y horarios, y puede recibir pedidos para llevar o a domicilio.\n\nConcepto visual sugerido: Plato estilizado con burbuja de chat y acento rojo NODO.','- Reduce llamadas para reservaciones\n- Disponible en horas pico sin saturarse\n- Mejora la experiencia del comensal','- Reservaciones automatizadas\n- Respuestas sobre menú y horarios\n- Recepción de pedidos',4990.00,NULL,'MXN','mensual','Desde',0,'disponible','borrador',NULL,NULL,NULL,NULL,NULL,'Hola, quiero información sobre IA para Restaurantes de NODO 360.','[\"Soluciones por Industria\"]','ia para restaurantes, reservaciones automaticas, ia gastronomia','Toma reservaciones, pedidos y responde preguntas frecuentes de tu restaurante.','IA para Restaurantes | NODO 360','Toma reservaciones, pedidos y responde preguntas frecuentes de tu restaurante.',NULL,NULL,4,0,NULL,NULL,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(33,'IND-006','IA para Escuelas','IA para Escuelas','ia-para-escuelas',NULL,5,'servicio','Atiende consultas de admisión e informa a padres de familia automáticamente.','Solución de inteligencia artificial para instituciones educativas que atiende consultas de admisión, informa sobre planes de estudio y costos, y agenda citas informativas con el área correspondiente.\n\nConcepto visual sugerido: Birrete de graduación estilizado con líneas digitales azul-violeta.','- Atiende consultas de admisión todo el día\n- Reduce carga en el área administrativa\n- Agenda citas informativas automáticamente','- Información de planes de estudio y costos\n- Agenda de citas informativas\n- Seguimiento a familias interesadas',4990.00,NULL,'MXN','mensual','Desde',0,'disponible','borrador',NULL,NULL,NULL,NULL,NULL,'Hola, quiero información sobre IA para Escuelas de NODO 360.','[\"Soluciones por Industria\"]','ia para escuelas, ia educacion, automatizacion admisiones','Atiende consultas de admisión e informa a padres de familia automáticamente.','IA para Escuelas | NODO 360','Atiende consultas de admisión e informa a padres de familia automáticamente.',NULL,NULL,5,0,NULL,NULL,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(34,'IND-007','IA para Mariachis y Eventos','IA para Mariachis y Eventos','ia-para-mariachis-y-eventos',NULL,5,'servicio','Cotiza y agenda contrataciones para eventos de forma automática, incluso en fin de semana.','Solución de inteligencia artificial pensada para negocios de eventos y grupos musicales, que cotiza servicios según fecha y tipo de evento, y agenda la contratación sin depender de que alguien conteste el teléfono.\n\nConcepto visual sugerido: Nota musical estilizada junto a un calendario de eventos digital.','- Cotiza y agenda incluso fuera de horario\n- No pierdes contrataciones por no contestar a tiempo\n- Disponible en fines de semana y días festivos','- Cotización según tipo de evento\n- Agenda de disponibilidad en tiempo real\n- Confirmación automática de apartado',3990.00,NULL,'MXN','mensual','Desde',0,'disponible','borrador',NULL,NULL,NULL,NULL,NULL,'Hola, quiero información sobre IA para Mariachis y Eventos de NODO 360.','[\"Soluciones por Industria\"]','ia para eventos, ia para mariachis, automatizacion de contrataciones','Cotiza y agenda contrataciones para eventos de forma automática, incluso en fin de semana.','IA para Mariachis y Eventos | NODO 360','Cotiza y agenda contrataciones para eventos de forma automática, incluso en fin de semana.',NULL,NULL,6,0,NULL,NULL,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(35,'TRA-001','Infraestructura Cloud','Infraestructura Cloud','infraestructura-cloud',NULL,6,'servicio','Diseñamos e implementamos la infraestructura en la nube que tu operación necesita.','Diseñamos, migramos e implementamos infraestructura en la nube adaptada al tamaño y necesidades de tu operación, priorizando disponibilidad, escalabilidad y costo.\n\nConcepto visual sugerido: Nube digital con servidores estilizados y líneas de conexión azules.','- Infraestructura escalable según tu crecimiento\n- Mayor disponibilidad y estabilidad\n- Optimización de costos en la nube','- Diagnóstico de infraestructura actual\n- Migración a la nube\n- Monitoreo de disponibilidad',9990.00,NULL,'MXN','mensual','Desde',0,'disponible','borrador',NULL,NULL,NULL,NULL,NULL,'Hola, quiero información sobre Infraestructura Cloud de NODO 360.','[\"Transformaci\\u00f3n Digital\"]','infraestructura cloud, migracion a la nube, servidores en la nube','Diseñamos e implementamos la infraestructura en la nube que tu operación necesita.','Infraestructura Cloud | NODO 360','Diseñamos e implementamos la infraestructura en la nube que tu operación necesita.',NULL,NULL,0,0,NULL,NULL,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(36,'TRA-002','Hosting y Dominios','Hosting y Dominios','hosting-y-dominios',NULL,6,'servicio','Alojamiento web confiable y gestión de dominios para que tu sitio esté siempre disponible.','Ofrecemos servicios de hosting y administración de dominios con monitoreo de disponibilidad, respaldos periódicos y soporte técnico especializado.\n\nConcepto visual sugerido: Servidor estilizado con candado de seguridad y globo terráqueo digital.','- Sitio web siempre disponible\n- Respaldos periódicos incluidos\n- Soporte técnico especializado','- Hosting administrado\n- Gestión de dominios\n- Respaldos automáticos',990.00,NULL,'MXN','mensual','Desde',0,'disponible','borrador',NULL,NULL,NULL,NULL,NULL,'Hola, quiero información sobre Hosting y Dominios de NODO 360.','[\"Transformaci\\u00f3n Digital\"]','hosting, dominios, alojamiento web','Alojamiento web confiable y gestión de dominios para que tu sitio esté siempre disponible.','Hosting y Dominios | NODO 360','Alojamiento web confiable y gestión de dominios para que tu sitio esté siempre disponible.',NULL,NULL,1,0,NULL,NULL,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(37,'TRA-003','Ciberseguridad','Ciberseguridad','ciberseguridad',NULL,6,'servicio','Protege tu información y la de tus clientes con buenas prácticas de ciberseguridad.','Evaluamos y fortalecemos la seguridad de tus sistemas, sitios web y bases de datos, implementando buenas prácticas y monitoreo para reducir el riesgo de incidentes de seguridad.\n\nConcepto visual sugerido: Escudo digital con candado, tonos azul oscuro y acentos rojos.','- Reduce el riesgo de incidentes de seguridad\n- Protege la información de tus clientes\n- Cumple con buenas prácticas del sector','- Auditoría de seguridad\n- Implementación de buenas prácticas\n- Monitoreo de vulnerabilidades',7990.00,NULL,'MXN','mensual','Desde',0,'disponible','borrador',NULL,NULL,NULL,NULL,NULL,'Hola, quiero información sobre Ciberseguridad de NODO 360.','[\"Transformaci\\u00f3n Digital\"]','ciberseguridad, seguridad informatica, proteccion de datos','Protege tu información y la de tus clientes con buenas prácticas de ciberseguridad.','Ciberseguridad | NODO 360','Protege tu información y la de tus clientes con buenas prácticas de ciberseguridad.',NULL,NULL,2,0,NULL,NULL,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(38,'TRA-004','Dashboards Ejecutivos','Dashboards Ejecutivos','dashboards-ejecutivos',NULL,6,'servicio','Visualiza los indicadores clave de tu negocio en un solo panel, en tiempo real.','Diseñamos dashboards ejecutivos que consolidan la información de tus distintas áreas en indicadores claros, facilitando la toma de decisiones basada en datos.\n\nConcepto visual sugerido: Panel de control con gráficas y KPIs en azul, violeta y blanco.','- Decisiones basadas en datos en tiempo real\n- Indicadores clave en un solo lugar\n- Menos tiempo armando reportes manuales','- Conexión con tus fuentes de datos\n- Indicadores y gráficas personalizadas\n- Actualización automática de datos',6990.00,NULL,'MXN','mensual','Desde',0,'disponible','borrador',NULL,NULL,NULL,NULL,NULL,'Hola, quiero información sobre Dashboards Ejecutivos de NODO 360.','[\"Transformaci\\u00f3n Digital\"]','dashboards ejecutivos, tableros de control, business intelligence','Visualiza los indicadores clave de tu negocio en un solo panel, en tiempo real.','Dashboards Ejecutivos | NODO 360','Visualiza los indicadores clave de tu negocio en un solo panel, en tiempo real.',NULL,NULL,3,0,NULL,NULL,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL),(39,'TRA-005','Consultoría en Transformación Digital','Consultoría en Transformación Digital','consultoria-en-transformacion-digital',NULL,6,'servicio','Acompañamiento estratégico para digitalizar tu operación de forma ordenada y con resultados medibles.','Brindamos consultoría en transformación digital para diagnosticar el nivel de madurez tecnológica de tu empresa y diseñar una hoja de ruta priorizada de digitalización.\n\nConcepto visual sugerido: Brújula digital sobre un mapa de ruta de transformación, tonos azul y violeta.','- Hoja de ruta clara de digitalización\n- Priorización de proyectos según impacto\n- Acompañamiento estratégico continuo','- Diagnóstico de madurez digital\n- Hoja de ruta de transformación\n- Seguimiento periódico de avances',8990.00,NULL,'MXN','mensual','Desde',0,'disponible','borrador',NULL,NULL,NULL,NULL,NULL,'Hola, quiero información sobre Consultoría en Transformación Digital de NODO 360.','[\"Transformaci\\u00f3n Digital\"]','transformacion digital, consultoria digital, digitalizacion empresarial','Acompañamiento estratégico para digitalizar tu operación de forma ordenada y con resultados medibles.','Consultoría en Transformación Digital | NODO 360','Acompañamiento estratégico para digitalizar tu operación de forma ordenada y con resultados medibles.',NULL,NULL,4,0,NULL,NULL,'2026-07-17 12:48:53','2026-07-17 12:48:53',NULL);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
INSERT INTO `role_has_permissions` VALUES (1,1),(1,2),(1,3),(1,4),(1,5),(1,6),(1,7),(1,8),(1,9),(2,1),(2,2),(2,3),(2,6),(3,1),(3,2),(3,3),(3,4),(3,6),(4,1),(4,2),(5,1),(5,2),(5,3),(6,1),(6,2),(6,3),(6,5),(7,1),(7,2),(8,1),(8,2),(8,3),(8,4),(8,5),(8,6),(8,7),(8,8),(8,9),(9,1),(9,2),(10,1),(10,2),(11,1),(11,2),(12,1),(12,2),(12,3),(12,4),(12,5),(12,6),(12,7),(12,8),(12,9),(13,1),(13,2),(14,1),(14,2),(15,1),(15,2),(16,1),(16,2),(16,9),(17,1),(17,2),(18,1),(18,2),(19,1),(19,2),(20,1),(20,2),(21,1),(21,2),(21,9),(22,1),(22,2),(23,1),(23,2),(24,1),(24,2),(24,3),(24,7),(24,9),(25,1),(25,2),(25,3),(25,5),(25,7),(25,9),(26,1),(27,1),(27,2),(27,3),(27,4),(27,6),(28,1),(28,2),(28,3),(28,7),(28,9),(29,1),(29,2),(30,1),(30,2),(30,3),(30,4),(30,6),(31,1),(31,2),(31,3),(31,4),(31,6),(32,1),(32,2),(32,3),(32,4),(33,1),(33,2),(33,4),(34,1),(34,2),(34,3),(34,4),(34,6),(34,7),(35,1),(35,2),(35,3),(35,4),(35,6),(36,1),(36,2),(36,3),(36,4),(36,6),(37,1),(37,2),(38,1),(38,2),(38,3),(39,1),(39,2),(39,3),(40,1),(40,2),(40,3),(41,1),(41,2),(41,3),(41,5),(41,7),(41,9),(42,1),(42,2),(42,3),(42,5),(43,1),(43,2),(43,3),(43,5),(44,1),(44,2),(45,1),(45,2),(45,3),(46,1),(46,2),(46,3),(47,1),(47,2),(47,3),(47,4),(47,6),(47,7),(47,9),(48,1),(48,2),(48,3),(48,4),(48,6),(49,1),(49,2),(49,3),(49,4),(49,6),(50,1),(50,2),(51,1),(51,2),(51,3),(52,1),(52,2),(52,3),(53,1),(53,2),(53,3),(53,4),(53,5),(53,6),(53,7),(53,9),(54,1),(54,2),(54,3),(54,4),(54,6),(55,1),(55,2),(55,3),(55,4),(55,6),(56,1),(56,2),(57,1),(57,2),(57,3),(58,1),(58,2),(58,3),(58,5),(58,7),(58,9),(59,1),(59,2),(59,3),(59,5),(60,1),(60,2),(60,3),(60,5),(61,1),(61,2),(62,1),(62,2),(62,3);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Superadministrador','web','2026-07-17 12:48:52','2026-07-17 12:48:52'),(2,'Administrador','web','2026-07-17 12:48:52','2026-07-17 12:48:52'),(3,'Marketing','web','2026-07-17 12:48:52','2026-07-17 12:48:52'),(4,'Diseñador','web','2026-07-17 12:48:52','2026-07-17 12:48:52'),(5,'Ventas','web','2026-07-17 12:48:52','2026-07-17 12:48:52'),(6,'Editor','web','2026-07-17 12:48:52','2026-07-17 12:48:52'),(7,'Analista','web','2026-07-17 12:48:52','2026-07-17 12:48:52'),(8,'Cliente','web','2026-07-17 12:48:52','2026-07-17 12:48:52'),(9,'Solo lectura','web','2026-07-17 12:48:52','2026-07-17 12:48:52');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
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
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `group` varchar(255) NOT NULL DEFAULT 'general',
  `key` varchar(255) NOT NULL,
  `value` longtext DEFAULT NULL,
  `is_encrypted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'general','company_name','NODO 360 MARKETING TECHNOLOGY',0,'2026-07-17 12:48:52','2026-07-17 12:48:52'),(2,'general','system_name','NODO Catalog Manager',0,'2026-07-17 12:48:53','2026-07-17 12:48:53'),(3,'general','system_subtitle','El centro inteligente de contenidos, catálogos y automatización de NODO 360.',0,'2026-07-17 12:48:53','2026-07-17 12:48:53'),(4,'general','company_address','',0,'2026-07-17 12:48:53','2026-07-17 12:48:53'),(5,'general','company_phone','',0,'2026-07-17 12:48:53','2026-07-17 12:48:53'),(6,'general','company_whatsapp','',0,'2026-07-17 12:48:53','2026-07-17 12:48:53'),(7,'general','company_email','info@nodo360mkt.site',0,'2026-07-17 12:48:53','2026-07-17 12:48:53'),(8,'general','company_website','https://nodo360mkt.site',0,'2026-07-17 12:48:53','2026-07-17 12:48:53'),(9,'general','currency','MXN',0,'2026-07-17 12:48:53','2026-07-17 12:48:53'),(10,'general','timezone','America/Mexico_City',0,'2026-07-17 12:48:53','2026-07-17 12:48:53'),(11,'general','locale','es',0,'2026-07-17 12:48:53','2026-07-17 12:48:53'),(12,'general','tax_rate','16',0,'2026-07-17 12:48:53','2026-07-17 12:48:53'),(13,'general','date_format','d/m/Y',0,'2026-07-17 12:48:53','2026-07-17 12:48:53'),(14,'general','time_format','H:i',0,'2026-07-17 12:48:53','2026-07-17 12:48:53'),(15,'general','primary_color','#0F172A',0,'2026-07-17 12:48:53','2026-07-17 12:48:53'),(16,'general','accent_color','#DC2626',0,'2026-07-17 12:48:53','2026-07-17 12:48:53'),(17,'general','gradient_from','#2563EB',0,'2026-07-17 12:48:53','2026-07-17 12:48:53'),(18,'general','gradient_to','#7C3AED',0,'2026-07-17 12:48:53','2026-07-17 12:48:53'),(19,'general','logo_path','',0,'2026-07-17 12:48:53','2026-07-17 12:48:53'),(20,'general','favicon_path','',0,'2026-07-17 12:48:53','2026-07-17 12:48:53'),(21,'general','cta_text','Agenda una demostración',0,'2026-07-17 12:48:53','2026-07-17 12:48:53'),(22,'general','hero_text','Centraliza, crea, automatiza y publica todo el contenido comercial de tu empresa desde un solo lugar.',0,'2026-07-17 12:48:53','2026-07-17 12:48:53'),(23,'security','login_max_attempts','5',0,'2026-07-17 12:48:53','2026-07-17 12:48:53'),(24,'security','login_lockout_minutes','15',0,'2026-07-17 12:48:53','2026-07-17 12:48:53'),(25,'security','require_email_verification','0',0,'2026-07-17 12:48:53','2026-07-17 12:48:53'),(26,'system','installed','1',0,'2026-07-17 12:48:53','2026-07-17 12:48:53'),(27,'ai','ai_enabled','0',0,'2026-07-17 10:17:46','2026-07-17 10:17:46'),(28,'ai','ai_provider','openai',0,'2026-07-17 10:17:46','2026-07-17 10:17:46'),(29,'ai','ai_model','gpt-4o-mini',0,'2026-07-17 10:17:46','2026-07-17 10:17:46'),(30,'ai','ai_base_url','https://api.openai.com/v1',0,'2026-07-17 10:17:46','2026-07-17 10:17:46');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `social_accounts`
--

DROP TABLE IF EXISTS `social_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `social_accounts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `channel` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  `external_account_id` varchar(255) DEFAULT NULL,
  `access_token` text DEFAULT NULL,
  `token_expires_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `social_accounts_created_by_foreign` (`created_by`),
  CONSTRAINT `social_accounts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `social_accounts`
--

LOCK TABLES `social_accounts` WRITE;
/*!40000 ALTER TABLE `social_accounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `social_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `social_posts`
--

DROP TABLE IF EXISTS `social_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `social_posts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `social_account_id` bigint(20) unsigned DEFAULT NULL,
  `channel` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `video_path` varchar(255) DEFAULT NULL,
  `hashtags` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `scheduled_at` timestamp NULL DEFAULT NULL,
  `timezone` varchar(255) NOT NULL DEFAULT 'America/Mexico_City',
  `status` varchar(255) NOT NULL DEFAULT 'borrador',
  `result` varchar(255) DEFAULT NULL,
  `external_post_id` varchar(255) DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `duplicated_from` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `social_posts_user_id_foreign` (`user_id`),
  KEY `social_posts_product_id_foreign` (`product_id`),
  KEY `social_posts_social_account_id_foreign` (`social_account_id`),
  KEY `social_posts_duplicated_from_foreign` (`duplicated_from`),
  KEY `social_posts_status_scheduled_at_index` (`status`,`scheduled_at`),
  KEY `social_posts_channel_index` (`channel`),
  CONSTRAINT `social_posts_duplicated_from_foreign` FOREIGN KEY (`duplicated_from`) REFERENCES `social_posts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `social_posts_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `social_posts_social_account_id_foreign` FOREIGN KEY (`social_account_id`) REFERENCES `social_accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `social_posts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `social_posts`
--

LOCK TABLES `social_posts` WRITE;
/*!40000 ALTER TABLE `social_posts` DISABLE KEYS */;
/*!40000 ALTER TABLE `social_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrador NODO 360','admin@nodo360mkt.site',NULL,NULL,1,'2026-07-18 00:52:20','127.0.0.1','2026-07-17 12:48:53','$2y$12$QGdE0/dB7q6IuCfT8dUTMOoPyhb0MuipanmI.Q9H7aAYpztWTbkGq',NULL,'2026-07-17 12:48:53','2026-07-18 01:54:00',NULL),(3,'Fede','fede@nodo360mkt.site','7712955995',NULL,1,'2026-07-17 10:36:24','127.0.0.1',NULL,'$2y$12$GcSRU0L/EU1cx/fCecJhG.nHj6qLCTMovKzPECM8n91KyM9kI3Q5K',NULL,'2026-07-17 10:35:20','2026-07-17 10:36:24',NULL);
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

-- Dump completed on 2026-07-17 20:04:04

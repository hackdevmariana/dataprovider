/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `achievements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `achievements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `badge_color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#22C55E',
  `category` enum('energy_saving','solar_production','cooperation','sustainability','engagement','milestone','streak','community') COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('single','progressive','recurring') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'single',
  `difficulty` enum('bronze','silver','gold','platinum','legendary') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bronze',
  `conditions` json DEFAULT NULL,
  `points` int NOT NULL DEFAULT '10',
  `required_value` int DEFAULT NULL,
  `required_unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_hidden` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `achievements_slug_unique` (`slug`),
  KEY `achievements_category_is_active_index` (`category`,`is_active`),
  KEY `achievements_difficulty_is_active_index` (`difficulty`,`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `activity_feeds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_feeds` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `activity_type` enum('energy_saved','solar_generated','achievement_unlocked','project_funded','installation_completed','cooperative_joined','roof_published','investment_made','production_right_sold','challenge_completed','milestone_reached','content_published','expert_verified','review_published','topic_created','community_contribution','carbon_milestone','efficiency_improvement','grid_contribution','sustainability_goal','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `related_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `related_id` bigint unsigned NOT NULL,
  `activity_data` json NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `summary` text COLLATE utf8mb4_unicode_ci,
  `energy_amount_kwh` decimal(10,2) DEFAULT NULL,
  `cost_savings_eur` decimal(8,2) DEFAULT NULL,
  `co2_savings_kg` decimal(8,2) DEFAULT NULL,
  `investment_amount_eur` decimal(10,2) DEFAULT NULL,
  `community_impact_score` int DEFAULT NULL,
  `visibility` enum('public','cooperative','followers','private') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `is_milestone` tinyint(1) NOT NULL DEFAULT '0',
  `notify_followers` tinyint(1) NOT NULL DEFAULT '1',
  `show_in_feed` tinyint(1) NOT NULL DEFAULT '1',
  `allow_interactions` tinyint(1) NOT NULL DEFAULT '1',
  `engagement_score` int NOT NULL DEFAULT '0',
  `likes_count` int NOT NULL DEFAULT '0',
  `loves_count` int NOT NULL DEFAULT '0',
  `wow_count` int NOT NULL DEFAULT '0',
  `comments_count` int NOT NULL DEFAULT '0',
  `shares_count` int NOT NULL DEFAULT '0',
  `bookmarks_count` int NOT NULL DEFAULT '0',
  `views_count` int NOT NULL DEFAULT '0',
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `location_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activity_occurred_at` timestamp NULL DEFAULT NULL,
  `is_real_time` tinyint(1) NOT NULL DEFAULT '1',
  `activity_group` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_activity_id` bigint unsigned DEFAULT NULL,
  `relevance_score` decimal(8,2) NOT NULL DEFAULT '100.00',
  `boost_until` timestamp NULL DEFAULT NULL,
  `algorithm_data` json DEFAULT NULL,
  `status` enum('active','hidden','flagged','archived','deleted') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `flags_count` int NOT NULL DEFAULT '0',
  `flag_reasons` json DEFAULT NULL,
  `moderated_by` bigint unsigned DEFAULT NULL,
  `moderated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_feeds_related_type_related_id_index` (`related_type`,`related_id`),
  KEY `activity_feeds_parent_activity_id_foreign` (`parent_activity_id`),
  KEY `activity_feeds_moderated_by_foreign` (`moderated_by`),
  KEY `activity_feeds_user_id_created_at_index` (`user_id`,`created_at`),
  KEY `activity_feeds_activity_type_created_at_index` (`activity_type`,`created_at`),
  KEY `activity_feeds_visibility_status_created_at_index` (`visibility`,`status`,`created_at`),
  KEY `activity_feeds_engagement_score_created_at_index` (`engagement_score`,`created_at`),
  KEY `activity_feeds_is_featured_relevance_score_index` (`is_featured`,`relevance_score`),
  KEY `activity_feeds_activity_occurred_at_index` (`activity_occurred_at`),
  KEY `activity_related_idx` (`related_type`,`related_id`),
  KEY `activity_feeds_activity_group_created_at_index` (`activity_group`,`created_at`),
  KEY `activity_feeds_latitude_longitude_index` (`latitude`,`longitude`),
  CONSTRAINT `activity_feeds_moderated_by_foreign` FOREIGN KEY (`moderated_by`) REFERENCES `users` (`id`),
  CONSTRAINT `activity_feeds_parent_activity_id_foreign` FOREIGN KEY (`parent_activity_id`) REFERENCES `activity_feeds` (`id`),
  CONSTRAINT `activity_feeds_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `activity_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_log` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `log_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_id` bigint unsigned DEFAULT NULL,
  `causer_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `causer_id` bigint unsigned DEFAULT NULL,
  `properties` json DEFAULT NULL,
  `batch_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subject` (`subject_type`,`subject_id`),
  KEY `causer` (`causer_type`,`causer_id`),
  KEY `activity_log_log_name_index` (`log_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `aliases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aliases` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('nickname','stage_name','birth_name','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other',
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `person_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `aliases_person_id_foreign` (`person_id`),
  CONSTRAINT `aliases_person_id_foreign` FOREIGN KEY (`person_id`) REFERENCES `people` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `anniversaries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `anniversaries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `day` tinyint unsigned NOT NULL,
  `month` tinyint unsigned NOT NULL,
  `year` year DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `anniversaries_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `api_keys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `api_keys` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `scope` enum('read-only','write','full-access') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'read-only',
  `rate_limit` int NOT NULL DEFAULT '1000',
  `expires_at` timestamp NULL DEFAULT NULL,
  `is_revoked` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `api_keys_token_unique` (`token`),
  KEY `api_keys_user_id_foreign` (`user_id`),
  CONSTRAINT `api_keys_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `app_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `app_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slogan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `primary_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `secondary_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'es',
  `custom_js` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `app_settings_organization_id_foreign` (`organization_id`),
  CONSTRAINT `app_settings_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `artist_event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `artist_event` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `artist_id` bigint unsigned NOT NULL,
  `event_id` bigint unsigned NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `artist_event_artist_id_foreign` (`artist_id`),
  KEY `artist_event_event_id_foreign` (`event_id`),
  CONSTRAINT `artist_event_artist_id_foreign` FOREIGN KEY (`artist_id`) REFERENCES `artists` (`id`) ON DELETE CASCADE,
  CONSTRAINT `artist_event_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `artist_group_member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `artist_group_member` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `artist_id` bigint unsigned NOT NULL,
  `group_id` bigint unsigned NOT NULL,
  `joined_at` date DEFAULT NULL,
  `left_at` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `artist_group_member_artist_id_group_id_unique` (`artist_id`,`group_id`),
  KEY `artist_group_member_group_id_foreign` (`group_id`),
  CONSTRAINT `artist_group_member_artist_id_foreign` FOREIGN KEY (`artist_id`) REFERENCES `artists` (`id`),
  CONSTRAINT `artist_group_member_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `artists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `artists` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `birth_date` date DEFAULT NULL,
  `genre` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `person_id` bigint unsigned DEFAULT NULL,
  `stage_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `group_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active_years_start` year DEFAULT NULL,
  `active_years_end` year DEFAULT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_links` json DEFAULT NULL,
  `language_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `artists_slug_unique` (`slug`),
  KEY `artists_person_id_foreign` (`person_id`),
  KEY `artists_language_id_foreign` (`language_id`),
  CONSTRAINT `artists_language_id_foreign` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`),
  CONSTRAINT `artists_person_id_foreign` FOREIGN KEY (`person_id`) REFERENCES `people` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `audits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audits` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `event` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auditable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auditable_id` bigint unsigned NOT NULL,
  `old_values` text COLLATE utf8mb4_unicode_ci,
  `new_values` text COLLATE utf8mb4_unicode_ci,
  `url` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(1023) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tags` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audits_auditable_type_auditable_id_index` (`auditable_type`,`auditable_id`),
  KEY `audits_user_id_user_type_index` (`user_id`,`user_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `autonomous_communities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `autonomous_communities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_id` bigint unsigned NOT NULL,
  `latitude` decimal(10,6) DEFAULT NULL,
  `longitude` decimal(10,6) DEFAULT NULL,
  `area_km2` decimal(10,2) DEFAULT NULL,
  `altitude_m` int DEFAULT NULL,
  `timezone_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `autonomous_communities_slug_unique` (`slug`),
  KEY `autonomous_communities_country_id_foreign` (`country_id`),
  KEY `autonomous_communities_timezone_id_foreign` (`timezone_id`),
  CONSTRAINT `autonomous_communities_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `autonomous_communities_timezone_id_foreign` FOREIGN KEY (`timezone_id`) REFERENCES `timezones` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `award_winners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `award_winners` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `person_id` bigint unsigned NOT NULL,
  `award_id` bigint unsigned NOT NULL,
  `year` year DEFAULT NULL,
  `classification` enum('winner','finalist','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'winner',
  `work_id` bigint unsigned DEFAULT NULL,
  `municipality_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `award_winners_person_id_foreign` (`person_id`),
  KEY `award_winners_award_id_foreign` (`award_id`),
  KEY `award_winners_work_id_foreign` (`work_id`),
  KEY `award_winners_municipality_id_foreign` (`municipality_id`),
  CONSTRAINT `award_winners_award_id_foreign` FOREIGN KEY (`award_id`) REFERENCES `awards` (`id`) ON DELETE CASCADE,
  CONSTRAINT `award_winners_municipality_id_foreign` FOREIGN KEY (`municipality_id`) REFERENCES `municipalities` (`id`) ON DELETE SET NULL,
  CONSTRAINT `award_winners_person_id_foreign` FOREIGN KEY (`person_id`) REFERENCES `people` (`id`) ON DELETE CASCADE,
  CONSTRAINT `award_winners_work_id_foreign` FOREIGN KEY (`work_id`) REFERENCES `works` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `awards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `awards` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `awarded_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_year_awarded` year DEFAULT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `awards_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
DROP TABLE IF EXISTS `calendar_holiday_locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `calendar_holiday_locations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `calendar_holiday_id` bigint unsigned NOT NULL,
  `municipality_id` bigint unsigned DEFAULT NULL,
  `province_id` bigint unsigned DEFAULT NULL,
  `autonomous_community_id` bigint unsigned DEFAULT NULL,
  `country_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `calendar_holiday_locations_calendar_holiday_id_foreign` (`calendar_holiday_id`),
  KEY `calendar_holiday_locations_municipality_id_foreign` (`municipality_id`),
  KEY `calendar_holiday_locations_province_id_foreign` (`province_id`),
  KEY `calendar_holiday_locations_autonomous_community_id_foreign` (`autonomous_community_id`),
  KEY `calendar_holiday_locations_country_id_foreign` (`country_id`),
  CONSTRAINT `calendar_holiday_locations_autonomous_community_id_foreign` FOREIGN KEY (`autonomous_community_id`) REFERENCES `autonomous_communities` (`id`) ON DELETE CASCADE,
  CONSTRAINT `calendar_holiday_locations_calendar_holiday_id_foreign` FOREIGN KEY (`calendar_holiday_id`) REFERENCES `calendar_holidays` (`id`) ON DELETE CASCADE,
  CONSTRAINT `calendar_holiday_locations_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `calendar_holiday_locations_municipality_id_foreign` FOREIGN KEY (`municipality_id`) REFERENCES `municipalities` (`id`) ON DELETE CASCADE,
  CONSTRAINT `calendar_holiday_locations_province_id_foreign` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `calendar_holidays`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `calendar_holidays` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `municipality_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `calendar_holidays_slug_unique` (`slug`),
  KEY `calendar_holidays_municipality_id_foreign` (`municipality_id`),
  CONSTRAINT `calendar_holidays_municipality_id_foreign` FOREIGN KEY (`municipality_id`) REFERENCES `municipalities` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `carbon_calculations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carbon_calculations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `carbon_equivalence_id` bigint unsigned NOT NULL,
  `quantity` decimal(10,3) NOT NULL,
  `co2_result` decimal(10,3) NOT NULL,
  `context` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parameters` json DEFAULT NULL,
  `session_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `carbon_calculations_user_id_created_at_index` (`user_id`,`created_at`),
  KEY `carbon_calculations_session_id_created_at_index` (`session_id`,`created_at`),
  KEY `carbon_calculations_carbon_equivalence_id_index` (`carbon_equivalence_id`),
  CONSTRAINT `carbon_calculations_carbon_equivalence_id_foreign` FOREIGN KEY (`carbon_equivalence_id`) REFERENCES `carbon_equivalences` (`id`) ON DELETE CASCADE,
  CONSTRAINT `carbon_calculations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `carbon_equivalences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carbon_equivalences` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `co2_kg_equivalent` decimal(8,2) NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other',
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'kg',
  `efficiency_ratio` decimal(8,4) DEFAULT NULL,
  `loss_factor` decimal(8,4) DEFAULT NULL,
  `calculation_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `calculation_params` json DEFAULT NULL,
  `source` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `source_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `verification_entity` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_updated` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `carbon_equivalences_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `carbon_saving_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carbon_saving_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `carbon_equivalence_id` bigint unsigned DEFAULT NULL,
  `amount_kg` decimal(8,2) NOT NULL,
  `activity_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `carbon_saving_logs_user_id_foreign` (`user_id`),
  KEY `carbon_saving_logs_carbon_equivalence_id_foreign` (`carbon_equivalence_id`),
  CONSTRAINT `carbon_saving_logs_carbon_equivalence_id_foreign` FOREIGN KEY (`carbon_equivalence_id`) REFERENCES `carbon_equivalences` (`id`) ON DELETE SET NULL,
  CONSTRAINT `carbon_saving_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `carbon_saving_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carbon_saving_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `installation_power_kw` decimal(8,2) NOT NULL,
  `production_kwh` decimal(10,2) DEFAULT NULL,
  `municipality_id` bigint unsigned DEFAULT NULL,
  `province_id` bigint unsigned DEFAULT NULL,
  `period` enum('daily','monthly','annual') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'annual',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `efficiency_ratio` decimal(5,4) NOT NULL DEFAULT '0.8500',
  `loss_factor` decimal(5,4) NOT NULL DEFAULT '0.1000',
  `estimated_production_kwh` decimal(10,2) DEFAULT NULL,
  `co2_saved_kg` decimal(10,2) DEFAULT NULL,
  `money_saved_eur` decimal(8,2) DEFAULT NULL,
  `trees_equivalent` int DEFAULT NULL,
  `equivalences` json DEFAULT NULL,
  `calculation_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'standard',
  `emission_factor_used` decimal(8,6) DEFAULT NULL,
  `electricity_price_used` decimal(8,4) DEFAULT NULL,
  `calculation_parameters` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `carbon_saving_requests_province_id_foreign` (`province_id`),
  KEY `carbon_saving_requests_user_id_created_at_index` (`user_id`,`created_at`),
  KEY `carbon_saving_requests_municipality_id_index` (`municipality_id`),
  KEY `carbon_saving_requests_period_index` (`period`),
  CONSTRAINT `carbon_saving_requests_municipality_id_foreign` FOREIGN KEY (`municipality_id`) REFERENCES `municipalities` (`id`),
  CONSTRAINT `carbon_saving_requests_province_id_foreign` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`),
  CONSTRAINT `carbon_saving_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('news','event','work','profession','cooperative','energy','general') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `parent_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int NOT NULL DEFAULT '0',
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`),
  KEY `categories_type_is_active_index` (`type`,`is_active`),
  KEY `categories_parent_id_index` (`parent_id`),
  CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `challenges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `challenges` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `instructions` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `banner_color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#FCD34D',
  `type` enum('individual','community','cooperative') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'individual',
  `category` enum('energy_saving','solar_production','cooperation','sustainability','education') COLLATE utf8mb4_unicode_ci NOT NULL,
  `difficulty` enum('easy','medium','hard','expert') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'easy',
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `goals` json DEFAULT NULL,
  `rewards` json DEFAULT NULL,
  `max_participants` int DEFAULT NULL,
  `min_participants` int NOT NULL DEFAULT '1',
  `entry_fee` decimal(8,2) NOT NULL DEFAULT '0.00',
  `prize_pool` decimal(10,2) NOT NULL DEFAULT '0.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `auto_join` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `challenges_slug_unique` (`slug`),
  KEY `challenges_type_is_active_index` (`type`,`is_active`),
  KEY `challenges_category_is_active_index` (`category`,`is_active`),
  KEY `challenges_start_date_end_date_index` (`start_date`,`end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `colorables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `colorables` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `color_id` bigint unsigned NOT NULL,
  `colorable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `colorable_id` bigint unsigned NOT NULL,
  `usage` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `colorables_color_id_foreign` (`color_id`),
  KEY `colorables_colorable_type_colorable_id_index` (`colorable_type`,`colorable_id`),
  CONSTRAINT `colorables_color_id_foreign` FOREIGN KEY (`color_id`) REFERENCES `colors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `colors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `colors` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hex_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rgb_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hsl_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `colors_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `company_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `company_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icon_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `consultation_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `consultation_services` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `consultant_id` bigint unsigned NOT NULL,
  `client_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('technical','legal','financial','installation','maintenance','custom') COLLATE utf8mb4_unicode_ci NOT NULL,
  `format` enum('online','onsite','hybrid','document_review','phone_call') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('requested','accepted','in_progress','completed','cancelled','disputed') COLLATE utf8mb4_unicode_ci NOT NULL,
  `hourly_rate` decimal(8,2) DEFAULT NULL,
  `fixed_price` decimal(10,2) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'EUR',
  `estimated_hours` int DEFAULT NULL,
  `actual_hours` int DEFAULT NULL,
  `requested_at` datetime NOT NULL,
  `accepted_at` datetime DEFAULT NULL,
  `started_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `deadline` datetime DEFAULT NULL,
  `requirements` json NOT NULL,
  `deliverables` json NOT NULL,
  `milestones` json DEFAULT NULL,
  `client_notes` text COLLATE utf8mb4_unicode_ci,
  `consultant_notes` text COLLATE utf8mb4_unicode_ci,
  `client_rating` int DEFAULT NULL,
  `consultant_rating` int DEFAULT NULL,
  `client_review` text COLLATE utf8mb4_unicode_ci,
  `consultant_review` text COLLATE utf8mb4_unicode_ci,
  `platform_commission` decimal(5,4) NOT NULL DEFAULT '0.1500',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `consultation_services_consultant_id_status_index` (`consultant_id`,`status`),
  KEY `consultation_services_client_id_status_index` (`client_id`,`status`),
  KEY `consultation_services_type_status_index` (`type`,`status`),
  KEY `consultation_services_deadline_status_index` (`deadline`,`status`),
  KEY `consultation_services_completed_at_status_index` (`completed_at`,`status`),
  CONSTRAINT `consultation_services_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `consultation_services_consultant_id_foreign` FOREIGN KEY (`consultant_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `content_hashtags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `content_hashtags` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `hashtag_id` bigint unsigned NOT NULL,
  `hashtaggable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hashtaggable_id` bigint unsigned NOT NULL,
  `added_by` bigint unsigned NOT NULL,
  `clicks_count` int NOT NULL DEFAULT '0',
  `relevance_score` decimal(5,2) NOT NULL DEFAULT '100.00',
  `is_auto_generated` tinyint(1) NOT NULL DEFAULT '0',
  `confidence_score` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ch_unique` (`hashtag_id`,`hashtaggable_type`,`hashtaggable_id`),
  KEY `content_hashtags_hashtaggable_type_hashtaggable_id_index` (`hashtaggable_type`,`hashtaggable_id`),
  KEY `ch_morphs` (`hashtaggable_type`,`hashtaggable_id`),
  KEY `ch_added` (`added_by`,`created_at`),
  CONSTRAINT `content_hashtags_added_by_foreign` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `content_hashtags_hashtag_id_foreign` FOREIGN KEY (`hashtag_id`) REFERENCES `hashtags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `content_votes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `content_votes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `votable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `votable_id` bigint unsigned NOT NULL,
  `vote_type` enum('upvote','downvote') COLLATE utf8mb4_unicode_ci NOT NULL,
  `vote_weight` int NOT NULL DEFAULT '1',
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_helpful_vote` tinyint(1) NOT NULL DEFAULT '0',
  `metadata` json DEFAULT NULL,
  `is_valid` tinyint(1) NOT NULL DEFAULT '1',
  `validated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `content_votes_user_id_votable_type_votable_id_unique` (`user_id`,`votable_type`,`votable_id`),
  KEY `content_votes_votable_type_votable_id_index` (`votable_type`,`votable_id`),
  KEY `content_votes_validated_by_foreign` (`validated_by`),
  KEY `content_votes_votable_type_votable_id_vote_type_index` (`votable_type`,`votable_id`,`vote_type`),
  KEY `content_votes_user_id_created_at_index` (`user_id`,`created_at`),
  CONSTRAINT `content_votes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `content_votes_validated_by_foreign` FOREIGN KEY (`validated_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cooperative_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cooperative_posts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cooperative_id` bigint unsigned NOT NULL,
  `author_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_type` enum('announcement','news','event','discussion','update') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'announcement',
  `status` enum('draft','published','archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `visibility` enum('public','members_only','board_only') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'members_only',
  `attachments` json DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `comments_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `is_pinned` tinyint(1) NOT NULL DEFAULT '0',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `views_count` int NOT NULL DEFAULT '0',
  `likes_count` int NOT NULL DEFAULT '0',
  `comments_count` int NOT NULL DEFAULT '0',
  `published_at` timestamp NULL DEFAULT NULL,
  `pinned_until` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cooperative_posts_cooperative_id_status_index` (`cooperative_id`,`status`),
  KEY `cooperative_posts_cooperative_id_post_type_index` (`cooperative_id`,`post_type`),
  KEY `cooperative_posts_author_id_status_index` (`author_id`,`status`),
  KEY `cooperative_posts_status_published_at_index` (`status`,`published_at`),
  KEY `cooperative_posts_visibility_published_at_index` (`visibility`,`published_at`),
  KEY `cooperative_posts_is_pinned_pinned_until_index` (`is_pinned`,`pinned_until`),
  KEY `cooperative_posts_is_featured_published_at_index` (`is_featured`,`published_at`),
  CONSTRAINT `cooperative_posts_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cooperative_posts_cooperative_id_foreign` FOREIGN KEY (`cooperative_id`) REFERENCES `cooperatives` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cooperative_user_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cooperative_user_members` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cooperative_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `joined_at` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cooperative_user_members_cooperative_id_user_id_unique` (`cooperative_id`,`user_id`),
  KEY `cooperative_user_members_user_id_foreign` (`user_id`),
  CONSTRAINT `cooperative_user_members_cooperative_id_foreign` FOREIGN KEY (`cooperative_id`) REFERENCES `cooperatives` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cooperative_user_members_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cooperatives`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cooperatives` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `legal_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cooperative_type` enum('energy','housing','agriculture','etc') COLLATE utf8mb4_unicode_ci NOT NULL,
  `scope` enum('local','regional','national') COLLATE utf8mb4_unicode_ci NOT NULL,
  `nif` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `founded_at` date DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_id` bigint unsigned DEFAULT NULL,
  `municipality_id` bigint unsigned NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` decimal(10,6) DEFAULT NULL,
  `longitude` decimal(10,6) DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `number_of_members` int unsigned DEFAULT NULL,
  `main_activity` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_open_to_new_members` tinyint(1) NOT NULL DEFAULT '0',
  `source` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `data_source_id` bigint unsigned DEFAULT NULL,
  `has_energy_market_access` tinyint(1) NOT NULL DEFAULT '0',
  `legal_form` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `statutes_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `accepts_new_installations` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cooperatives_slug_unique` (`slug`),
  KEY `cooperatives_image_id_foreign` (`image_id`),
  KEY `cooperatives_municipality_id_foreign` (`municipality_id`),
  KEY `cooperatives_data_source_id_foreign` (`data_source_id`),
  CONSTRAINT `cooperatives_data_source_id_foreign` FOREIGN KEY (`data_source_id`) REFERENCES `data_sources` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cooperatives_image_id_foreign` FOREIGN KEY (`image_id`) REFERENCES `images` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cooperatives_municipality_id_foreign` FOREIGN KEY (`municipality_id`) REFERENCES `municipalities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `countries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `iso_alpha2` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `iso_alpha3` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `iso_numeric` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `demonym` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `official_language` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,6) DEFAULT NULL,
  `longitude` decimal(10,6) DEFAULT NULL,
  `flag_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `population` bigint DEFAULT NULL,
  `gdp_usd` decimal(15,2) DEFAULT NULL,
  `region_group` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `area_km2` decimal(10,2) DEFAULT NULL,
  `altitude_m` int DEFAULT NULL,
  `timezone_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `countries_slug_unique` (`slug`),
  UNIQUE KEY `countries_iso_alpha2_unique` (`iso_alpha2`),
  UNIQUE KEY `countries_iso_alpha3_unique` (`iso_alpha3`),
  KEY `countries_timezone_id_foreign` (`timezone_id`),
  CONSTRAINT `countries_timezone_id_foreign` FOREIGN KEY (`timezone_id`) REFERENCES `timezones` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `country_language`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `country_language` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `country_id` bigint unsigned NOT NULL,
  `language_id` bigint unsigned NOT NULL,
  `is_official` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `country_language_country_id_foreign` (`country_id`),
  KEY `country_language_language_id_foreign` (`language_id`),
  CONSTRAINT `country_language_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `country_language_language_id_foreign` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `currencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `currencies` (
  `iso_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `symbol` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_crypto` tinyint(1) NOT NULL DEFAULT '0',
  `is_supported_by_app` tinyint(1) NOT NULL DEFAULT '1',
  `exchangeable_in_calculator` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`iso_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `data_sources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `data_sources` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('api','scrap','csv','manual') COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `license` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_scraped_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `electricity_offers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `electricity_offers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `energy_company_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price_fixed_eur_month` decimal(8,2) DEFAULT NULL,
  `price_variable_eur_kwh` decimal(8,4) DEFAULT NULL,
  `price_unit_id` bigint unsigned DEFAULT NULL,
  `offer_type` enum('fixed','variable','hybrid') COLLATE utf8mb4_unicode_ci NOT NULL,
  `valid_from` date DEFAULT NULL,
  `valid_until` date DEFAULT NULL,
  `conditions_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contract_length_months` tinyint unsigned DEFAULT NULL,
  `requires_smart_meter` tinyint(1) NOT NULL DEFAULT '0',
  `renewable_origin_certified` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `electricity_offers_energy_company_id_foreign` (`energy_company_id`),
  KEY `electricity_offers_price_unit_id_foreign` (`price_unit_id`),
  CONSTRAINT `electricity_offers_energy_company_id_foreign` FOREIGN KEY (`energy_company_id`) REFERENCES `energy_companies` (`id`),
  CONSTRAINT `electricity_offers_price_unit_id_foreign` FOREIGN KEY (`price_unit_id`) REFERENCES `price_units` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `electricity_price_intervals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `electricity_price_intervals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `electricity_price_id` bigint unsigned NOT NULL,
  `interval_index` tinyint unsigned NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `price_eur_mwh` decimal(8,4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `electricity_price_intervals_electricity_price_id_foreign` (`electricity_price_id`),
  CONSTRAINT `electricity_price_intervals_electricity_price_id_foreign` FOREIGN KEY (`electricity_price_id`) REFERENCES `electricity_prices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `electricity_prices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `electricity_prices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `hour` tinyint DEFAULT NULL,
  `type` enum('pvpc','spot') COLLATE utf8mb4_unicode_ci NOT NULL,
  `price_eur_mwh` decimal(10,4) NOT NULL,
  `price_min` decimal(10,4) DEFAULT NULL,
  `price_max` decimal(10,4) DEFAULT NULL,
  `price_avg` decimal(10,4) DEFAULT NULL,
  `forecast_for_tomorrow` tinyint(1) NOT NULL DEFAULT '0',
  `source` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price_unit_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `electricity_prices_date_hour_type_unique` (`date`,`hour`,`type`),
  KEY `electricity_prices_price_unit_id_foreign` (`price_unit_id`),
  CONSTRAINT `electricity_prices_price_unit_id_foreign` FOREIGN KEY (`price_unit_id`) REFERENCES `price_units` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `emission_factors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `emission_factors` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `activity` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `factor_kg_co2e_per_unit` decimal(10,4) NOT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `energy_certificates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `energy_certificates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `building_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `energy_rating` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `annual_energy_consumption_kwh` decimal(10,2) NOT NULL,
  `annual_emissions_kg_co2e` decimal(10,2) NOT NULL,
  `zone_climate_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `energy_certificates_user_id_foreign` (`user_id`),
  KEY `energy_certificates_zone_climate_id_foreign` (`zone_climate_id`),
  CONSTRAINT `energy_certificates_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `energy_certificates_zone_climate_id_foreign` FOREIGN KEY (`zone_climate_id`) REFERENCES `zone_climates` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `energy_companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `energy_companies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_customer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_commercial` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_customer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_commercial` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `highlighted_offer` text COLLATE utf8mb4_unicode_ci,
  `cnmc_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_id` bigint unsigned DEFAULT NULL,
  `company_type` enum('comercializadora','distribuidora','mixta','cooperativa') COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `coverage_scope` enum('local','regional','nacional') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'nacional',
  `municipality_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `energy_companies_slug_unique` (`slug`),
  KEY `energy_companies_image_id_foreign` (`image_id`),
  KEY `energy_companies_municipality_id_foreign` (`municipality_id`),
  CONSTRAINT `energy_companies_image_id_foreign` FOREIGN KEY (`image_id`) REFERENCES `images` (`id`),
  CONSTRAINT `energy_companies_municipality_id_foreign` FOREIGN KEY (`municipality_id`) REFERENCES `municipalities` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `energy_installations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `energy_installations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('solar','wind','hydro','biomass','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `capacity_kw` double NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner_id` bigint unsigned DEFAULT NULL,
  `commissioned_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `energy_installations_owner_id_foreign` (`owner_id`),
  CONSTRAINT `energy_installations_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `energy_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `energy_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `producer_id` bigint unsigned NOT NULL,
  `consumer_id` bigint unsigned NOT NULL,
  `installation_id` bigint unsigned NOT NULL,
  `amount_kwh` double NOT NULL,
  `price_per_kwh` double NOT NULL,
  `transaction_datetime` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `energy_transactions_producer_id_foreign` (`producer_id`),
  KEY `energy_transactions_consumer_id_foreign` (`consumer_id`),
  KEY `energy_transactions_installation_id_foreign` (`installation_id`),
  CONSTRAINT `energy_transactions_consumer_id_foreign` FOREIGN KEY (`consumer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `energy_transactions_installation_id_foreign` FOREIGN KEY (`installation_id`) REFERENCES `energy_installations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `energy_transactions_producer_id_foreign` FOREIGN KEY (`producer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_types_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `events` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `start_datetime` timestamp NOT NULL,
  `end_datetime` timestamp NULL DEFAULT NULL,
  `venue_id` bigint unsigned DEFAULT NULL,
  `event_type_id` bigint unsigned DEFAULT NULL,
  `festival_id` bigint unsigned DEFAULT NULL,
  `language_id` bigint unsigned DEFAULT NULL,
  `timezone_id` bigint unsigned DEFAULT NULL,
  `municipality_id` bigint unsigned DEFAULT NULL,
  `point_of_interest_id` bigint unsigned DEFAULT NULL,
  `work_id` bigint unsigned DEFAULT NULL,
  `price` decimal(8,2) DEFAULT NULL,
  `is_free` tinyint(1) NOT NULL DEFAULT '0',
  `audience_size_estimate` int unsigned DEFAULT NULL,
  `source_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `events_slug_unique` (`slug`),
  KEY `events_venue_id_foreign` (`venue_id`),
  KEY `events_event_type_id_foreign` (`event_type_id`),
  KEY `events_festival_id_foreign` (`festival_id`),
  KEY `events_language_id_foreign` (`language_id`),
  KEY `events_timezone_id_foreign` (`timezone_id`),
  KEY `events_municipality_id_foreign` (`municipality_id`),
  KEY `events_point_of_interest_id_foreign` (`point_of_interest_id`),
  KEY `events_work_id_foreign` (`work_id`),
  CONSTRAINT `events_event_type_id_foreign` FOREIGN KEY (`event_type_id`) REFERENCES `event_types` (`id`),
  CONSTRAINT `events_festival_id_foreign` FOREIGN KEY (`festival_id`) REFERENCES `festivals` (`id`),
  CONSTRAINT `events_language_id_foreign` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`),
  CONSTRAINT `events_municipality_id_foreign` FOREIGN KEY (`municipality_id`) REFERENCES `municipalities` (`id`),
  CONSTRAINT `events_point_of_interest_id_foreign` FOREIGN KEY (`point_of_interest_id`) REFERENCES `point_of_interests` (`id`),
  CONSTRAINT `events_timezone_id_foreign` FOREIGN KEY (`timezone_id`) REFERENCES `timezones` (`id`),
  CONSTRAINT `events_venue_id_foreign` FOREIGN KEY (`venue_id`) REFERENCES `venues` (`id`),
  CONSTRAINT `events_work_id_foreign` FOREIGN KEY (`work_id`) REFERENCES `works` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `exchange_rates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exchange_rates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `from_currency` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `to_currency` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rate` decimal(20,8) NOT NULL,
  `date` date NOT NULL,
  `source` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `market_type` enum('fiat','crypto','metal') COLLATE utf8mb4_unicode_ci NOT NULL,
  `precision` tinyint unsigned DEFAULT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `volume_usd` decimal(20,2) DEFAULT NULL,
  `market_cap` decimal(20,2) DEFAULT NULL,
  `retrieved_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_promoted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `expert_verifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expert_verifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `expertise_area` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `verification_level` enum('basic','advanced','professional','expert') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'basic',
  `status` enum('pending','under_review','approved','rejected','expired') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `credentials` json DEFAULT NULL,
  `verification_documents` json DEFAULT NULL,
  `expertise_description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `years_experience` int NOT NULL DEFAULT '0',
  `certifications` json DEFAULT NULL,
  `education` json DEFAULT NULL,
  `work_history` json DEFAULT NULL,
  `verification_fee` decimal(8,2) NOT NULL DEFAULT '0.00',
  `verified_by` bigint unsigned DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `verification_notes` text COLLATE utf8mb4_unicode_ci,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `verification_score` int DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `expert_verifications_user_id_expertise_area_index` (`user_id`,`expertise_area`),
  KEY `expert_verifications_user_id_status_index` (`user_id`,`status`),
  KEY `expert_verifications_expertise_area_verification_level_index` (`expertise_area`,`verification_level`),
  KEY `expert_verifications_status_submitted_at_index` (`status`,`submitted_at`),
  KEY `expert_verifications_verified_by_verified_at_index` (`verified_by`,`verified_at`),
  KEY `expert_verifications_expires_at_index` (`expires_at`),
  CONSTRAINT `expert_verifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `expert_verifications_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
DROP TABLE IF EXISTS `family_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `family_members` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `person_id` bigint unsigned NOT NULL,
  `relative_id` bigint unsigned NOT NULL,
  `relationship_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_biological` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `family_members_person_id_foreign` (`person_id`),
  KEY `family_members_relative_id_foreign` (`relative_id`),
  CONSTRAINT `family_members_person_id_foreign` FOREIGN KEY (`person_id`) REFERENCES `people` (`id`) ON DELETE CASCADE,
  CONSTRAINT `family_members_relative_id_foreign` FOREIGN KEY (`relative_id`) REFERENCES `people` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `festivals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `festivals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `month` tinyint unsigned DEFAULT NULL,
  `usual_days` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recurring` tinyint(1) NOT NULL DEFAULT '0',
  `location_id` bigint unsigned DEFAULT NULL,
  `logo_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color_theme` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `festivals_slug_unique` (`slug`),
  KEY `festivals_location_id_foreign` (`location_id`),
  CONSTRAINT `festivals_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `municipalities` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `fontables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fontables` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `font_id` bigint unsigned NOT NULL,
  `fontable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fontable_id` bigint unsigned NOT NULL,
  `usage` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fontables_font_id_foreign` (`font_id`),
  KEY `fontables_fontable_type_fontable_id_index` (`fontable_type`,`fontable_id`),
  CONSTRAINT `fontables_font_id_foreign` FOREIGN KEY (`font_id`) REFERENCES `fonts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `fonts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fonts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `family` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `style` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `weight` int DEFAULT NULL,
  `license` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `hashtags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hashtags` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#3B82F6',
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` enum('technology','legislation','financing','installation','cooperative','market','diy','sustainability','location','general') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `usage_count` int NOT NULL DEFAULT '0',
  `posts_count` int NOT NULL DEFAULT '0',
  `followers_count` int NOT NULL DEFAULT '0',
  `trending_score` decimal(8,2) NOT NULL DEFAULT '0.00',
  `is_trending` tinyint(1) NOT NULL DEFAULT '0',
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `is_blocked` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` bigint unsigned DEFAULT NULL,
  `related_hashtags` json DEFAULT NULL,
  `synonyms` json DEFAULT NULL,
  `auto_suggest` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hashtags_name_unique` (`name`),
  UNIQUE KEY `hashtags_slug_unique` (`slug`),
  KEY `hashtags_created_by_foreign` (`created_by`),
  KEY `hashtags_category_usage_count_index` (`category`,`usage_count`),
  KEY `hashtags_is_trending_trending_score_index` (`is_trending`,`trending_score`),
  KEY `hashtags_is_verified_usage_count_index` (`is_verified`,`usage_count`),
  FULLTEXT KEY `hashtags_name_description_fulltext` (`name`,`description`),
  CONSTRAINT `hashtags_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `imageables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `imageables` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `image_id` bigint unsigned NOT NULL,
  `imageable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `imageable_id` bigint unsigned NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `imageables_image_id_foreign` (`image_id`),
  KEY `imageables_imageable_type_imageable_id_index` (`imageable_type`,`imageable_id`),
  CONSTRAINT `imageables_image_id_foreign` FOREIGN KEY (`image_id`) REFERENCES `images` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `images` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alt_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `width` int unsigned DEFAULT NULL,
  `height` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `images_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
DROP TABLE IF EXISTS `languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `languages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `language` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `native_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `iso_639_1` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `iso_639_2` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rtl` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `languages_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `leaderboards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `leaderboards` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `period` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `scope` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `scope_id` bigint unsigned DEFAULT NULL,
  `criteria` json NOT NULL,
  `rules` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_public` tinyint(1) NOT NULL DEFAULT '1',
  `max_positions` int NOT NULL DEFAULT '100',
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `last_calculated_at` timestamp NULL DEFAULT NULL,
  `current_rankings` json DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leaderboards_type_period_index` (`type`,`period`),
  KEY `leaderboards_scope_scope_id_index` (`scope`,`scope_id`),
  KEY `leaderboards_is_active_is_public_index` (`is_active`,`is_public`),
  KEY `leaderboards_start_date_end_date_index` (`start_date`,`end_date`),
  KEY `leaderboards_last_calculated_at_index` (`last_calculated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `links` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `related_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `related_id` bigint unsigned NOT NULL,
  `type` enum('wikipedia','imdb','official','twitter','instagram','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other',
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `opens_in_new_tab` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `links_related_type_related_id_index` (`related_type`,`related_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `list_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `list_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_list_id` bigint unsigned NOT NULL,
  `listable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `listable_id` bigint unsigned NOT NULL,
  `added_by` bigint unsigned NOT NULL,
  `position` int NOT NULL DEFAULT '0',
  `personal_note` text COLLATE utf8mb4_unicode_ci,
  `tags` json DEFAULT NULL,
  `personal_rating` decimal(3,1) DEFAULT NULL,
  `added_mode` enum('manual','auto_hashtag','auto_keyword','auto_author','suggested','imported') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `status` enum('active','pending','rejected','archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `clicks_count` int NOT NULL DEFAULT '0',
  `likes_count` int NOT NULL DEFAULT '0',
  `last_accessed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `list_items_user_list_id_listable_type_listable_id_unique` (`user_list_id`,`listable_type`,`listable_id`),
  KEY `list_items_listable_type_listable_id_index` (`listable_type`,`listable_id`),
  KEY `list_items_reviewed_by_foreign` (`reviewed_by`),
  KEY `list_items_user_list_id_position_index` (`user_list_id`,`position`),
  KEY `list_items_added_by_created_at_index` (`added_by`,`created_at`),
  KEY `list_items_status_added_mode_index` (`status`,`added_mode`),
  CONSTRAINT `list_items_added_by_foreign` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `list_items_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`),
  CONSTRAINT `list_items_user_list_id_foreign` FOREIGN KEY (`user_list_id`) REFERENCES `user_lists` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `media` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `collection_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `conversions_disk` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` bigint unsigned NOT NULL,
  `manipulations` json NOT NULL,
  `custom_properties` json NOT NULL,
  `generated_conversions` json NOT NULL,
  `responsive_images` json NOT NULL,
  `order_column` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `media_uuid_unique` (`uuid`),
  KEY `media_model_type_model_id_index` (`model_type`,`model_id`),
  KEY `media_order_column_index` (`order_column`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `media_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `media_contacts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `media_outlet_id` bigint unsigned NOT NULL,
  `type` enum('editorial','commercial','general') COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `job_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `secondary_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `specializations` json DEFAULT NULL,
  `coverage_areas` json DEFAULT NULL,
  `preferred_contact_method` enum('email','phone','mobile_phone','social_media','whatsapp') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `availability_schedule` json DEFAULT NULL,
  `language_preference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'es',
  `accepts_press_releases` tinyint(1) NOT NULL DEFAULT '1',
  `accepts_interviews` tinyint(1) NOT NULL DEFAULT '1',
  `accepts_events_invitations` tinyint(1) NOT NULL DEFAULT '1',
  `is_freelancer` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `priority_level` int NOT NULL DEFAULT '3',
  `response_rate` decimal(3,2) DEFAULT NULL,
  `contacts_count` int NOT NULL DEFAULT '0',
  `successful_contacts` int NOT NULL DEFAULT '0',
  `social_media_profiles` json DEFAULT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `recent_articles` json DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `interaction_history` json DEFAULT NULL,
  `last_contacted_at` timestamp NULL DEFAULT NULL,
  `last_response_at` timestamp NULL DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `media_contacts_media_outlet_id_type_is_active_index` (`media_outlet_id`,`type`,`is_active`),
  KEY `media_contacts_type_priority_level_index` (`type`,`priority_level`),
  KEY `media_contacts_is_verified_is_active_index` (`is_verified`,`is_active`),
  KEY `media_contacts_response_rate_index` (`response_rate`),
  KEY `media_contacts_last_contacted_at_index` (`last_contacted_at`),
  KEY `media_contacts_is_freelancer_is_active_index` (`is_freelancer`,`is_active`),
  CONSTRAINT `media_contacts_media_outlet_id_foreign` FOREIGN KEY (`media_outlet_id`) REFERENCES `media_outlets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `media_outlets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `media_outlets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('newspaper','tv','radio','blog','magazine') COLLATE utf8mb4_unicode_ci NOT NULL,
  `media_category` enum('diario','revista','digital','agencia','television','radio','blog') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'digital',
  `description` text COLLATE utf8mb4_unicode_ci,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rss_feed` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `headquarters_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `municipality_id` bigint unsigned DEFAULT NULL,
  `coverage_scope` enum('local','regional','nacional','internacional') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `languages` json DEFAULT NULL,
  `language` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `circulation` int DEFAULT NULL,
  `circulation_type` enum('impreso','digital','mixto','audiencia') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `founding_year` year DEFAULT NULL,
  `owner_company` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `political_leaning` enum('izquierda','centro-izquierda','centro','centro-derecha','derecha','neutral') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `specializations` json DEFAULT NULL,
  `is_digital_native` tinyint(1) NOT NULL DEFAULT '0',
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `covers_sustainability` tinyint(1) NOT NULL DEFAULT '0',
  `credibility_score` decimal(3,1) DEFAULT NULL,
  `influence_score` decimal(3,1) DEFAULT NULL,
  `sustainability_focus` decimal(3,2) DEFAULT NULL,
  `articles_count` int NOT NULL DEFAULT '0',
  `monthly_pageviews` int NOT NULL DEFAULT '0',
  `social_media_followers` int NOT NULL DEFAULT '0',
  `social_media_handles` json DEFAULT NULL,
  `contact_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `press_contact_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `press_contact_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `press_contact_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editorial_team` json DEFAULT NULL,
  `content_licensing` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `allows_reprints` tinyint(1) NOT NULL DEFAULT '0',
  `api_access` json DEFAULT NULL,
  `logo_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_scraped_at` timestamp NULL DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `media_outlets_slug_unique` (`slug`),
  KEY `media_outlets_type_is_active_index` (`type`,`is_active`),
  KEY `media_outlets_coverage_scope_is_verified_index` (`coverage_scope`,`is_verified`),
  KEY `media_outlets_covers_sustainability_sustainability_focus_index` (`covers_sustainability`,`sustainability_focus`),
  KEY `media_outlets_credibility_score_index` (`credibility_score`),
  KEY `media_outlets_influence_score_index` (`influence_score`),
  KEY `media_outlets_is_digital_native_founding_year_index` (`is_digital_native`,`founding_year`),
  KEY `media_outlets_municipality_id_coverage_scope_index` (`municipality_id`,`coverage_scope`),
  CONSTRAINT `media_outlets_municipality_id_foreign` FOREIGN KEY (`municipality_id`) REFERENCES `municipalities` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `municipalities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `municipalities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ine_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `population` bigint DEFAULT NULL,
  `mayor_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mayor_salary` decimal(10,2) DEFAULT NULL,
  `latitude` decimal(10,6) DEFAULT NULL,
  `longitude` decimal(10,6) DEFAULT NULL,
  `area_km2` decimal(10,2) DEFAULT NULL,
  `altitude_m` int DEFAULT NULL,
  `is_capital` tinyint(1) NOT NULL DEFAULT '0',
  `tourism_info` text COLLATE utf8mb4_unicode_ci,
  `region_id` bigint unsigned DEFAULT NULL,
  `province_id` bigint unsigned NOT NULL,
  `autonomous_community_id` bigint unsigned NOT NULL,
  `country_id` bigint unsigned NOT NULL,
  `timezone_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `municipalities_slug_unique` (`slug`),
  KEY `municipalities_region_id_foreign` (`region_id`),
  KEY `municipalities_province_id_foreign` (`province_id`),
  KEY `municipalities_autonomous_community_id_foreign` (`autonomous_community_id`),
  KEY `municipalities_country_id_foreign` (`country_id`),
  KEY `municipalities_timezone_id_foreign` (`timezone_id`),
  CONSTRAINT `municipalities_autonomous_community_id_foreign` FOREIGN KEY (`autonomous_community_id`) REFERENCES `autonomous_communities` (`id`) ON DELETE CASCADE,
  CONSTRAINT `municipalities_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `municipalities_province_id_foreign` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`) ON DELETE CASCADE,
  CONSTRAINT `municipalities_region_id_foreign` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `municipalities_timezone_id_foreign` FOREIGN KEY (`timezone_id`) REFERENCES `timezones` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `news_articles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `news_articles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `summary` text COLLATE utf8mb4_unicode_ci,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `excerpt` text COLLATE utf8mb4_unicode_ci,
  `source_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `featured_start` timestamp NULL DEFAULT NULL,
  `featured_end` timestamp NULL DEFAULT NULL,
  `media_outlet_id` bigint unsigned NOT NULL,
  `author_id` bigint unsigned DEFAULT NULL,
  `municipality_id` bigint unsigned DEFAULT NULL,
  `language_id` bigint unsigned DEFAULT NULL,
  `image_id` bigint unsigned DEFAULT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `topic_focus` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `article_type` enum('noticia','reportaje','entrevista','opinion','analisis','comunicado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'noticia',
  `tag_id` bigint unsigned DEFAULT NULL,
  `is_outstanding` tinyint(1) NOT NULL DEFAULT '0',
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `is_scraped` tinyint(1) NOT NULL DEFAULT '1',
  `is_translated` tinyint(1) NOT NULL DEFAULT '0',
  `is_breaking_news` tinyint(1) NOT NULL DEFAULT '0',
  `is_evergreen` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('draft','review','published','archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `visibility` enum('public','private') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public',
  `views_count` int unsigned NOT NULL DEFAULT '0',
  `shares_count` int NOT NULL DEFAULT '0',
  `comments_count` int NOT NULL DEFAULT '0',
  `reading_time_minutes` decimal(5,2) DEFAULT NULL,
  `word_count` int DEFAULT NULL,
  `sentiment_score` decimal(3,2) DEFAULT NULL,
  `sentiment_label` enum('positivo','neutral','negativo') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keywords` json DEFAULT NULL,
  `entities` json DEFAULT NULL,
  `sustainability_topics` json DEFAULT NULL,
  `environmental_impact_score` decimal(3,1) DEFAULT NULL,
  `related_co2_data` json DEFAULT NULL,
  `geo_scope` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,6) DEFAULT NULL,
  `longitude` decimal(10,6) DEFAULT NULL,
  `seo_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description` text COLLATE utf8mb4_unicode_ci,
  `social_media_meta` json DEFAULT NULL,
  `scraped_at` timestamp NULL DEFAULT NULL,
  `last_engagement_at` timestamp NULL DEFAULT NULL,
  `tags` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `news_articles_slug_unique` (`slug`),
  KEY `news_articles_author_id_foreign` (`author_id`),
  KEY `news_articles_language_id_foreign` (`language_id`),
  KEY `news_articles_image_id_foreign` (`image_id`),
  KEY `news_articles_tag_id_foreign` (`tag_id`),
  KEY `news_articles_status_published_at_index` (`status`,`published_at`),
  KEY `news_articles_category_published_at_index` (`category`,`published_at`),
  KEY `news_articles_is_outstanding_featured_start_featured_end_index` (`is_outstanding`,`featured_start`,`featured_end`),
  KEY `news_articles_is_breaking_news_published_at_index` (`is_breaking_news`,`published_at`),
  KEY `news_articles_media_outlet_id_published_at_index` (`media_outlet_id`,`published_at`),
  KEY `news_articles_municipality_id_published_at_index` (`municipality_id`,`published_at`),
  KEY `news_articles_environmental_impact_score_index` (`environmental_impact_score`),
  KEY `news_articles_views_count_index` (`views_count`),
  KEY `news_articles_latitude_longitude_index` (`latitude`,`longitude`),
  CONSTRAINT `news_articles_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `people` (`id`) ON DELETE SET NULL,
  CONSTRAINT `news_articles_image_id_foreign` FOREIGN KEY (`image_id`) REFERENCES `images` (`id`) ON DELETE SET NULL,
  CONSTRAINT `news_articles_language_id_foreign` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE SET NULL,
  CONSTRAINT `news_articles_media_outlet_id_foreign` FOREIGN KEY (`media_outlet_id`) REFERENCES `media_outlets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `news_articles_municipality_id_foreign` FOREIGN KEY (`municipality_id`) REFERENCES `municipalities` (`id`) ON DELETE SET NULL,
  CONSTRAINT `news_articles_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `notification_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notification_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `type` enum('electricity_price','event','solar_production') COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_id` bigint unsigned DEFAULT NULL,
  `threshold` decimal(10,4) DEFAULT NULL,
  `delivery_method` enum('app','email','sms') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'app',
  `is_silent` tinyint(1) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notification_settings_user_id_foreign` (`user_id`),
  CONSTRAINT `notification_settings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `organization_features`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `organization_features` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `feature_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enabled_dashboard` tinyint(1) NOT NULL DEFAULT '0',
  `enabled_web` tinyint(1) NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `organization_features_organization_id_foreign` (`organization_id`),
  CONSTRAINT `organization_features_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `organizations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `organizations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `domain` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `primary_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `secondary_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `css_files` json DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `organizations_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `payable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payable_id` bigint unsigned NOT NULL,
  `payment_intent_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','processing','completed','failed','cancelled','refunded') COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('subscription','commission','verification','consultation','refund') COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `fee` decimal(8,2) NOT NULL DEFAULT '0.00',
  `net_amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'EUR',
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `processor` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `processor_response` json DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `failure_reason` text COLLATE utf8mb4_unicode_ci,
  `processed_at` datetime DEFAULT NULL,
  `failed_at` datetime DEFAULT NULL,
  `refunded_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_payable_type_payable_id_index` (`payable_type`,`payable_id`),
  KEY `payments_user_id_status_index` (`user_id`,`status`),
  CONSTRAINT `payments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `people`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `people` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `birth_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `birth_date` date DEFAULT NULL,
  `death_date` date DEFAULT NULL,
  `birth_place` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `death_place` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nationality_id` bigint unsigned DEFAULT NULL,
  `language_id` bigint unsigned DEFAULT NULL,
  `image_id` bigint unsigned DEFAULT NULL,
  `gender` enum('male','female','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other',
  `official_website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wikidata_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wikipedia_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notable_for` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `occupation_summary` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_handles` json DEFAULT NULL,
  `is_influencer` tinyint(1) NOT NULL DEFAULT '0',
  `search_boost` int NOT NULL DEFAULT '0',
  `short_bio` text COLLATE utf8mb4_unicode_ci,
  `long_bio` longtext COLLATE utf8mb4_unicode_ci,
  `source_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_updated_from_source` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `people_slug_unique` (`slug`),
  KEY `people_nationality_id_foreign` (`nationality_id`),
  KEY `people_language_id_foreign` (`language_id`),
  CONSTRAINT `people_language_id_foreign` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`),
  CONSTRAINT `people_nationality_id_foreign` FOREIGN KEY (`nationality_id`) REFERENCES `countries` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `person_physical_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `person_physical_profiles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `person_id` bigint unsigned NOT NULL,
  `height_cm` double DEFAULT NULL,
  `weight_kg` double DEFAULT NULL,
  `body_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `person_physical_profiles_person_id_foreign` (`person_id`),
  CONSTRAINT `person_physical_profiles_person_id_foreign` FOREIGN KEY (`person_id`) REFERENCES `people` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `person_profession`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `person_profession` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `person_id` bigint unsigned NOT NULL,
  `profession_id` bigint unsigned NOT NULL,
  `start_year` year DEFAULT NULL,
  `end_year` year DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `is_current` tinyint(1) NOT NULL DEFAULT '1',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `person_profession_person_id_profession_id_unique` (`person_id`,`profession_id`),
  KEY `person_profession_person_id_is_primary_index` (`person_id`,`is_primary`),
  KEY `person_profession_profession_id_is_current_index` (`profession_id`,`is_current`),
  CONSTRAINT `person_profession_person_id_foreign` FOREIGN KEY (`person_id`) REFERENCES `people` (`id`) ON DELETE CASCADE,
  CONSTRAINT `person_profession_profession_id_foreign` FOREIGN KEY (`profession_id`) REFERENCES `professions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `person_professions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `person_professions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `person_id` bigint unsigned NOT NULL,
  `profession_id` bigint unsigned NOT NULL,
  `started_at` date DEFAULT NULL,
  `ended_at` date DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `is_current` tinyint(1) NOT NULL DEFAULT '1',
  `description` text COLLATE utf8mb4_unicode_ci,
  `specialization` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `achievements` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `person_professions_person_id_profession_id_unique` (`person_id`,`profession_id`),
  KEY `person_professions_person_id_is_current_index` (`person_id`,`is_current`),
  KEY `person_professions_profession_id_is_current_index` (`profession_id`,`is_current`),
  CONSTRAINT `person_professions_person_id_foreign` FOREIGN KEY (`person_id`) REFERENCES `people` (`id`) ON DELETE CASCADE,
  CONSTRAINT `person_professions_profession_id_foreign` FOREIGN KEY (`profession_id`) REFERENCES `professions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `person_work`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `person_work` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `person_id` bigint unsigned NOT NULL,
  `work_id` bigint unsigned NOT NULL,
  `role` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `character_name` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `credited_as` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `billing_order` smallint unsigned DEFAULT NULL,
  `contribution_pct` decimal(5,2) DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `person_work_person_id_work_id_index` (`person_id`,`work_id`),
  KEY `person_work_work_id_role_index` (`work_id`,`role`),
  KEY `person_work_billing_order_index` (`billing_order`),
  CONSTRAINT `person_work_person_id_foreign` FOREIGN KEY (`person_id`) REFERENCES `people` (`id`) ON DELETE CASCADE,
  CONSTRAINT `person_work_work_id_foreign` FOREIGN KEY (`work_id`) REFERENCES `works` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `person_works`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `person_works` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `person_id` bigint unsigned NOT NULL,
  `work_id` bigint unsigned NOT NULL,
  `role` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `character_name` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `credited_as` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `billing_order` smallint unsigned DEFAULT NULL,
  `contribution_pct` decimal(5,2) DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `person_works_person_id_work_id_role_unique` (`person_id`,`work_id`,`role`),
  KEY `person_works_person_id_index` (`person_id`),
  KEY `person_works_work_id_index` (`work_id`),
  KEY `person_works_role_index` (`role`),
  KEY `person_works_billing_order_index` (`billing_order`),
  CONSTRAINT `person_works_person_id_foreign` FOREIGN KEY (`person_id`) REFERENCES `people` (`id`) ON DELETE CASCADE,
  CONSTRAINT `person_works_work_id_foreign` FOREIGN KEY (`work_id`) REFERENCES `works` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `plant_species`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `plant_species` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `scientific_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `family` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `co2_absorption_kg_per_year` decimal(8,2) NOT NULL,
  `co2_absorption_min` decimal(8,2) DEFAULT NULL,
  `co2_absorption_max` decimal(8,2) DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `plant_type` enum('tree','shrub','herb','grass','vine','palm','conifer','fern','succulent','bamboo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tree',
  `size_category` enum('small','medium','large','giant') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `height_min` decimal(5,2) DEFAULT NULL,
  `height_max` decimal(5,2) DEFAULT NULL,
  `lifespan_years` int DEFAULT NULL,
  `growth_rate_cm_year` int DEFAULT NULL,
  `climate_zones` json DEFAULT NULL,
  `soil_types` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `water_needs_mm_year` decimal(8,2) DEFAULT NULL,
  `drought_resistant` tinyint(1) NOT NULL DEFAULT '0',
  `frost_resistant` tinyint(1) NOT NULL DEFAULT '0',
  `is_endemic` tinyint(1) NOT NULL DEFAULT '0',
  `is_invasive` tinyint(1) NOT NULL DEFAULT '0',
  `suitable_for_reforestation` tinyint(1) NOT NULL DEFAULT '1',
  `suitable_for_urban` tinyint(1) NOT NULL DEFAULT '0',
  `flowering_season` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fruit_season` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provides_food` tinyint(1) NOT NULL DEFAULT '0',
  `provides_timber` tinyint(1) NOT NULL DEFAULT '0',
  `medicinal_use` tinyint(1) NOT NULL DEFAULT '0',
  `planting_cost_eur` decimal(8,2) DEFAULT NULL,
  `maintenance_cost_eur_year` decimal(8,2) DEFAULT NULL,
  `survival_rate_percent` int DEFAULT NULL,
  `image_id` bigint unsigned DEFAULT NULL,
  `native_region_id` bigint unsigned DEFAULT NULL,
  `source` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `source_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `verification_entity` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `plant_species_slug_unique` (`slug`),
  KEY `plant_species_image_id_foreign` (`image_id`),
  KEY `plant_species_native_region_id_foreign` (`native_region_id`),
  KEY `plant_species_type_co2_idx` (`plant_type`,`co2_absorption_kg_per_year`),
  KEY `plant_species_reforestation_co2_idx` (`suitable_for_reforestation`,`co2_absorption_kg_per_year`),
  KEY `plant_species_verified_co2_idx` (`is_verified`,`co2_absorption_kg_per_year`),
  CONSTRAINT `plant_species_image_id_foreign` FOREIGN KEY (`image_id`) REFERENCES `images` (`id`) ON DELETE SET NULL,
  CONSTRAINT `plant_species_native_region_id_foreign` FOREIGN KEY (`native_region_id`) REFERENCES `regions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `platforms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `platforms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url_pattern` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('social','encyclopedia','video','media','music','professional','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other',
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `requires_verification` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `platforms_slug_unique` (`slug`),
  KEY `platforms_type_is_active_index` (`type`,`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `point_of_interest_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `point_of_interest_tag` (
  `point_of_interest_id` bigint unsigned NOT NULL,
  `tag_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`point_of_interest_id`,`tag_id`),
  KEY `point_of_interest_tag_tag_id_foreign` (`tag_id`),
  CONSTRAINT `point_of_interest_tag_point_of_interest_id_foreign` FOREIGN KEY (`point_of_interest_id`) REFERENCES `point_of_interests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `point_of_interest_tag_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `point_of_interests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `point_of_interests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('hotel','bar','monument','museum','park','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,6) DEFAULT NULL,
  `longitude` decimal(10,6) DEFAULT NULL,
  `municipality_id` bigint unsigned NOT NULL,
  `source` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_cultural_center` tinyint(1) NOT NULL DEFAULT '0',
  `is_energy_installation` tinyint(1) NOT NULL DEFAULT '0',
  `is_cooperative_office` tinyint(1) NOT NULL DEFAULT '0',
  `opening_hours` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `point_of_interests_slug_unique` (`slug`),
  KEY `point_of_interests_municipality_id_foreign` (`municipality_id`),
  CONSTRAINT `point_of_interests_municipality_id_foreign` FOREIGN KEY (`municipality_id`) REFERENCES `municipalities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `price_units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `price_units` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `conversion_factor_to_kwh` decimal(12,6) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `production_rights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `production_rights` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `seller_id` bigint unsigned NOT NULL,
  `buyer_id` bigint unsigned DEFAULT NULL,
  `installation_id` bigint unsigned DEFAULT NULL,
  `project_proposal_id` bigint unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `right_identifier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `right_type` enum('energy_production','excess_energy','carbon_credits','renewable_certificates','grid_injection','virtual_battery','demand_response','capacity_rights','green_certificates','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_capacity_kw` decimal(10,2) NOT NULL,
  `available_capacity_kw` decimal(10,2) NOT NULL,
  `reserved_capacity_kw` decimal(10,2) NOT NULL DEFAULT '0.00',
  `sold_capacity_kw` decimal(10,2) NOT NULL DEFAULT '0.00',
  `estimated_annual_production_kwh` decimal(12,2) DEFAULT NULL,
  `guaranteed_annual_production_kwh` decimal(12,2) DEFAULT NULL,
  `actual_annual_production_kwh` decimal(12,2) NOT NULL DEFAULT '0.00',
  `valid_from` date NOT NULL,
  `valid_until` date NOT NULL,
  `duration_years` int DEFAULT NULL,
  `renewable_right` tinyint(1) NOT NULL DEFAULT '0',
  `renewal_period_years` int DEFAULT NULL,
  `pricing_model` enum('fixed_price_kwh','market_price','premium_over_market','auction_based','performance_based','subscription_model','revenue_sharing','hybrid') COLLATE utf8mb4_unicode_ci NOT NULL,
  `price_per_kwh` decimal(8,4) DEFAULT NULL,
  `market_premium_percentage` decimal(5,2) DEFAULT NULL,
  `minimum_guaranteed_price` decimal(8,4) DEFAULT NULL,
  `maximum_price_cap` decimal(8,4) DEFAULT NULL,
  `price_escalation_terms` json DEFAULT NULL,
  `upfront_payment` decimal(10,2) DEFAULT NULL,
  `periodic_payment` decimal(8,2) DEFAULT NULL,
  `payment_frequency` enum('monthly','quarterly','biannual','annual','on_production') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `security_deposit` decimal(8,2) DEFAULT NULL,
  `payment_terms` json DEFAULT NULL,
  `penalty_clauses` json DEFAULT NULL,
  `production_guaranteed` tinyint(1) NOT NULL DEFAULT '0',
  `production_guarantee_percentage` decimal(5,2) DEFAULT NULL,
  `insurance_included` tinyint(1) NOT NULL DEFAULT '0',
  `insurance_details` text COLLATE utf8mb4_unicode_ci,
  `risk_allocation` json DEFAULT NULL,
  `buyer_rights` json DEFAULT NULL,
  `buyer_obligations` json DEFAULT NULL,
  `seller_rights` json DEFAULT NULL,
  `seller_obligations` json DEFAULT NULL,
  `is_transferable` tinyint(1) NOT NULL DEFAULT '1',
  `max_transfers` int DEFAULT NULL,
  `current_transfers` int NOT NULL DEFAULT '0',
  `transfer_restrictions` json DEFAULT NULL,
  `transfer_fee_percentage` decimal(5,2) DEFAULT NULL,
  `status` enum('available','reserved','under_negotiation','contracted','active','suspended','expired','cancelled','disputed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `status_notes` text COLLATE utf8mb4_unicode_ci,
  `contract_signed_at` timestamp NULL DEFAULT NULL,
  `activated_at` timestamp NULL DEFAULT NULL,
  `current_month_production_kwh` decimal(10,2) NOT NULL DEFAULT '0.00',
  `ytd_production_kwh` decimal(12,2) NOT NULL DEFAULT '0.00',
  `lifetime_production_kwh` decimal(15,2) NOT NULL DEFAULT '0.00',
  `performance_ratio` decimal(5,2) NOT NULL DEFAULT '100.00',
  `monthly_production_history` json DEFAULT NULL,
  `regulatory_framework` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `applicable_regulations` json DEFAULT NULL,
  `grid_code_compliant` tinyint(1) NOT NULL DEFAULT '1',
  `certifications` json DEFAULT NULL,
  `legal_documents` json DEFAULT NULL,
  `contract_template_version` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `electronic_signature_valid` tinyint(1) NOT NULL DEFAULT '0',
  `signature_details` json DEFAULT NULL,
  `views_count` int NOT NULL DEFAULT '0',
  `inquiries_count` int NOT NULL DEFAULT '0',
  `offers_received` int NOT NULL DEFAULT '0',
  `highest_offer_price` decimal(8,4) DEFAULT NULL,
  `average_market_price` decimal(8,4) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `auto_accept_offers` tinyint(1) NOT NULL DEFAULT '0',
  `auto_accept_threshold` decimal(8,4) DEFAULT NULL,
  `allow_partial_sales` tinyint(1) NOT NULL DEFAULT '1',
  `minimum_sale_capacity_kw` decimal(8,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `production_rights_slug_unique` (`slug`),
  UNIQUE KEY `production_rights_right_identifier_unique` (`right_identifier`),
  KEY `production_rights_buyer_id_foreign` (`buyer_id`),
  KEY `production_rights_project_proposal_id_foreign` (`project_proposal_id`),
  KEY `production_rights_seller_id_status_index` (`seller_id`,`status`),
  KEY `production_rights_right_type_is_active_index` (`right_type`,`is_active`),
  KEY `production_rights_valid_from_valid_until_index` (`valid_from`,`valid_until`),
  KEY `production_rights_pricing_model_price_per_kwh_index` (`pricing_model`,`price_per_kwh`),
  KEY `production_rights_installation_id_status_index` (`installation_id`,`status`),
  FULLTEXT KEY `production_rights_title_description_fulltext` (`title`,`description`),
  CONSTRAINT `production_rights_buyer_id_foreign` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `production_rights_installation_id_foreign` FOREIGN KEY (`installation_id`) REFERENCES `energy_installations` (`id`),
  CONSTRAINT `production_rights_project_proposal_id_foreign` FOREIGN KEY (`project_proposal_id`) REFERENCES `project_proposals` (`id`) ON DELETE SET NULL,
  CONSTRAINT `production_rights_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `professions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `professions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_public_facing` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `professions_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `project_commissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_commissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_proposal_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `type` enum('success_fee','listing_fee','verification_fee','premium_fee') COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `rate` decimal(5,4) NOT NULL,
  `base_amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'EUR',
  `status` enum('pending','paid','waived','disputed','refunded') COLLATE utf8mb4_unicode_ci NOT NULL,
  `due_date` datetime NOT NULL,
  `paid_at` datetime DEFAULT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `calculation_details` json NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_commissions_project_proposal_id_status_index` (`project_proposal_id`,`status`),
  KEY `project_commissions_user_id_status_index` (`user_id`,`status`),
  KEY `project_commissions_type_status_index` (`type`,`status`),
  KEY `project_commissions_due_date_status_index` (`due_date`,`status`),
  CONSTRAINT `project_commissions_project_proposal_id_foreign` FOREIGN KEY (`project_proposal_id`) REFERENCES `project_proposals` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_commissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `project_investments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_investments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_proposal_id` bigint unsigned NOT NULL,
  `investor_id` bigint unsigned NOT NULL,
  `investment_amount` decimal(10,2) NOT NULL,
  `investment_percentage` decimal(5,2) DEFAULT NULL,
  `investment_type` enum('monetary','in_kind','labor','materials','expertise','equipment','land_use','mixed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'monetary',
  `investment_details` json DEFAULT NULL,
  `investment_description` text COLLATE utf8mb4_unicode_ci,
  `expected_return_percentage` decimal(5,2) DEFAULT NULL,
  `investment_term_years` int DEFAULT NULL,
  `return_frequency` enum('monthly','quarterly','biannual','annual','at_completion','custom') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `return_schedule` json DEFAULT NULL,
  `reinvest_returns` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('pending','confirmed','paid','active','completed','cancelled','refunded','disputed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_date` timestamp NULL DEFAULT NULL,
  `payment_confirmed_at` timestamp NULL DEFAULT NULL,
  `payment_confirmed_by` bigint unsigned DEFAULT NULL,
  `legal_documents` json DEFAULT NULL,
  `terms_accepted` tinyint(1) NOT NULL DEFAULT '0',
  `terms_accepted_at` timestamp NULL DEFAULT NULL,
  `digital_signature` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contract_details` json DEFAULT NULL,
  `total_returns_received` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pending_returns` decimal(10,2) NOT NULL DEFAULT '0.00',
  `last_return_date` timestamp NULL DEFAULT NULL,
  `next_return_date` timestamp NULL DEFAULT NULL,
  `has_voting_rights` tinyint(1) NOT NULL DEFAULT '0',
  `voting_weight` decimal(5,2) NOT NULL DEFAULT '0.00',
  `can_participate_decisions` tinyint(1) NOT NULL DEFAULT '0',
  `receives_project_updates` tinyint(1) NOT NULL DEFAULT '1',
  `notification_preferences` json DEFAULT NULL,
  `public_investor` tinyint(1) NOT NULL DEFAULT '0',
  `investor_alias` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_roi` decimal(5,2) NOT NULL DEFAULT '0.00',
  `projected_final_roi` decimal(5,2) DEFAULT NULL,
  `months_invested` int NOT NULL DEFAULT '0',
  `performance_metrics` json DEFAULT NULL,
  `exit_requested` tinyint(1) NOT NULL DEFAULT '0',
  `exit_requested_at` timestamp NULL DEFAULT NULL,
  `exit_value` decimal(10,2) DEFAULT NULL,
  `exit_terms` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_project_investor` (`project_proposal_id`,`investor_id`),
  KEY `project_investments_payment_confirmed_by_foreign` (`payment_confirmed_by`),
  KEY `project_investments_investor_id_status_index` (`investor_id`,`status`),
  KEY `project_investments_project_proposal_id_investment_amount_index` (`project_proposal_id`,`investment_amount`),
  KEY `project_investments_status_payment_date_index` (`status`,`payment_date`),
  KEY `project_investments_next_return_date_status_index` (`next_return_date`,`status`),
  CONSTRAINT `project_investments_investor_id_foreign` FOREIGN KEY (`investor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_investments_payment_confirmed_by_foreign` FOREIGN KEY (`payment_confirmed_by`) REFERENCES `users` (`id`),
  CONSTRAINT `project_investments_project_proposal_id_foreign` FOREIGN KEY (`project_proposal_id`) REFERENCES `project_proposals` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `project_proposals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_proposals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `proposer_id` bigint unsigned NOT NULL,
  `cooperative_id` bigint unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `summary` text COLLATE utf8mb4_unicode_ci,
  `objectives` json DEFAULT NULL,
  `benefits` json DEFAULT NULL,
  `project_type` enum('individual_installation','community_installation','shared_installation','energy_storage','smart_grid','efficiency_improvement','research_development','educational','infrastructure','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `scale` enum('residential','commercial','industrial','utility','community') COLLATE utf8mb4_unicode_ci NOT NULL,
  `municipality_id` bigint unsigned DEFAULT NULL,
  `specific_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `estimated_power_kw` decimal(10,2) DEFAULT NULL,
  `estimated_annual_production_kwh` decimal(12,2) DEFAULT NULL,
  `technical_specifications` json DEFAULT NULL,
  `total_investment_required` decimal(12,2) NOT NULL,
  `investment_raised` decimal(12,2) NOT NULL DEFAULT '0.00',
  `min_investment_per_participant` decimal(8,2) DEFAULT NULL,
  `max_investment_per_participant` decimal(8,2) DEFAULT NULL,
  `max_participants` int DEFAULT NULL,
  `current_participants` int NOT NULL DEFAULT '0',
  `estimated_roi_percentage` decimal(5,2) DEFAULT NULL,
  `payback_period_years` int DEFAULT NULL,
  `estimated_annual_savings` decimal(10,2) DEFAULT NULL,
  `financial_projections` json DEFAULT NULL,
  `funding_deadline` date NOT NULL,
  `project_start_date` date DEFAULT NULL,
  `expected_completion_date` date DEFAULT NULL,
  `estimated_duration_months` int DEFAULT NULL,
  `project_milestones` json DEFAULT NULL,
  `documents` json DEFAULT NULL,
  `images` json DEFAULT NULL,
  `technical_reports` json DEFAULT NULL,
  `has_permits` tinyint(1) NOT NULL DEFAULT '0',
  `permits_status` json DEFAULT NULL,
  `is_technically_validated` tinyint(1) NOT NULL DEFAULT '0',
  `technical_validator_id` bigint unsigned DEFAULT NULL,
  `technical_validation_date` timestamp NULL DEFAULT NULL,
  `status` enum('draft','under_review','approved','funding','funded','in_progress','completed','cancelled','on_hold','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `status_notes` text COLLATE utf8mb4_unicode_ci,
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `views_count` int NOT NULL DEFAULT '0',
  `likes_count` int NOT NULL DEFAULT '0',
  `comments_count` int NOT NULL DEFAULT '0',
  `shares_count` int NOT NULL DEFAULT '0',
  `bookmarks_count` int NOT NULL DEFAULT '0',
  `engagement_score` decimal(8,2) NOT NULL DEFAULT '0.00',
  `is_public` tinyint(1) NOT NULL DEFAULT '1',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `allow_comments` tinyint(1) NOT NULL DEFAULT '1',
  `allow_investments` tinyint(1) NOT NULL DEFAULT '1',
  `notify_updates` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_proposals_slug_unique` (`slug`),
  KEY `project_proposals_proposer_id_foreign` (`proposer_id`),
  KEY `project_proposals_cooperative_id_foreign` (`cooperative_id`),
  KEY `project_proposals_technical_validator_id_foreign` (`technical_validator_id`),
  KEY `project_proposals_reviewed_by_foreign` (`reviewed_by`),
  KEY `project_proposals_status_is_public_index` (`status`,`is_public`),
  KEY `project_proposals_project_type_scale_index` (`project_type`,`scale`),
  KEY `project_proposals_funding_deadline_status_index` (`funding_deadline`,`status`),
  KEY `project_proposals_municipality_id_project_type_index` (`municipality_id`,`project_type`),
  KEY `project_proposals_engagement_score_is_featured_index` (`engagement_score`,`is_featured`),
  FULLTEXT KEY `project_proposals_title_description_summary_fulltext` (`title`,`description`,`summary`),
  CONSTRAINT `project_proposals_cooperative_id_foreign` FOREIGN KEY (`cooperative_id`) REFERENCES `cooperatives` (`id`) ON DELETE SET NULL,
  CONSTRAINT `project_proposals_municipality_id_foreign` FOREIGN KEY (`municipality_id`) REFERENCES `municipalities` (`id`),
  CONSTRAINT `project_proposals_proposer_id_foreign` FOREIGN KEY (`proposer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_proposals_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`),
  CONSTRAINT `project_proposals_technical_validator_id_foreign` FOREIGN KEY (`technical_validator_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `project_updates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_updates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_proposal_id` bigint unsigned NOT NULL,
  `author_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `summary` text COLLATE utf8mb4_unicode_ci,
  `update_type` enum('general','milestone','financial','technical','regulatory','delay','completion','issue','success','funding','partnership','media','community') COLLATE utf8mb4_unicode_ci NOT NULL,
  `progress_percentage` decimal(5,2) DEFAULT NULL,
  `previous_progress_percentage` decimal(5,2) DEFAULT NULL,
  `completed_milestones` json DEFAULT NULL,
  `upcoming_milestones` json DEFAULT NULL,
  `revised_completion_date` date DEFAULT NULL,
  `budget_spent` decimal(10,2) DEFAULT NULL,
  `budget_remaining` decimal(10,2) DEFAULT NULL,
  `additional_funding_needed` decimal(10,2) DEFAULT NULL,
  `cost_breakdown` json DEFAULT NULL,
  `financial_notes` text COLLATE utf8mb4_unicode_ci,
  `actual_power_installed_kw` decimal(10,2) DEFAULT NULL,
  `production_to_date_kwh` decimal(12,2) DEFAULT NULL,
  `performance_vs_expected` decimal(5,2) DEFAULT NULL,
  `technical_metrics` json DEFAULT NULL,
  `technical_notes` text COLLATE utf8mb4_unicode_ci,
  `images` json DEFAULT NULL,
  `videos` json DEFAULT NULL,
  `documents` json DEFAULT NULL,
  `reports` json DEFAULT NULL,
  `co2_savings_kg` decimal(10,2) DEFAULT NULL,
  `energy_savings_kwh` decimal(10,2) DEFAULT NULL,
  `cost_savings_eur` decimal(8,2) DEFAULT NULL,
  `environmental_impact` json DEFAULT NULL,
  `social_impact` json DEFAULT NULL,
  `notify_all_investors` tinyint(1) NOT NULL DEFAULT '1',
  `investor_specific_info` json DEFAULT NULL,
  `requires_investor_action` tinyint(1) NOT NULL DEFAULT '0',
  `required_action_description` text COLLATE utf8mb4_unicode_ci,
  `action_deadline` timestamp NULL DEFAULT NULL,
  `views_count` int NOT NULL DEFAULT '0',
  `likes_count` int NOT NULL DEFAULT '0',
  `comments_count` int NOT NULL DEFAULT '0',
  `shares_count` int NOT NULL DEFAULT '0',
  `allow_comments` tinyint(1) NOT NULL DEFAULT '1',
  `allow_questions` tinyint(1) NOT NULL DEFAULT '1',
  `visibility` enum('public','investors_only','team_only','stakeholders','private') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'investors_only',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `is_urgent` tinyint(1) NOT NULL DEFAULT '0',
  `send_email_notification` tinyint(1) NOT NULL DEFAULT '0',
  `send_push_notification` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('draft','published','scheduled','archived','deleted') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'published',
  `published_at` timestamp NULL DEFAULT NULL,
  `scheduled_for` timestamp NULL DEFAULT NULL,
  `investor_satisfaction_score` decimal(3,1) DEFAULT NULL,
  `questions_received` int NOT NULL DEFAULT '0',
  `questions_answered` int NOT NULL DEFAULT '0',
  `all_questions_answered` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_updates_project_proposal_id_published_at_index` (`project_proposal_id`,`published_at`),
  KEY `project_updates_author_id_update_type_index` (`author_id`,`update_type`),
  KEY `project_updates_visibility_status_index` (`visibility`,`status`),
  KEY `project_updates_is_featured_is_urgent_index` (`is_featured`,`is_urgent`),
  KEY `project_updates_scheduled_for_status_index` (`scheduled_for`,`status`),
  FULLTEXT KEY `project_updates_title_content_summary_fulltext` (`title`,`content`,`summary`),
  CONSTRAINT `project_updates_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_updates_project_proposal_id_foreign` FOREIGN KEY (`project_proposal_id`) REFERENCES `project_proposals` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `project_verifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_verifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_proposal_id` bigint unsigned NOT NULL,
  `requested_by` bigint unsigned NOT NULL,
  `verified_by` bigint unsigned DEFAULT NULL,
  `type` enum('basic','advanced','professional','enterprise') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('requested','in_review','approved','rejected','expired') COLLATE utf8mb4_unicode_ci NOT NULL,
  `fee` decimal(10,2) NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'EUR',
  `verification_criteria` json NOT NULL,
  `documents_required` json NOT NULL,
  `documents_provided` json DEFAULT NULL,
  `verification_results` json DEFAULT NULL,
  `verification_notes` text COLLATE utf8mb4_unicode_ci,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `score` int DEFAULT NULL,
  `requested_at` datetime NOT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '1',
  `certificate_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_verifications_certificate_number_unique` (`certificate_number`),
  KEY `project_verifications_project_proposal_id_status_index` (`project_proposal_id`,`status`),
  KEY `project_verifications_requested_by_status_index` (`requested_by`,`status`),
  KEY `project_verifications_verified_by_status_index` (`verified_by`,`status`),
  KEY `project_verifications_type_status_index` (`type`,`status`),
  KEY `project_verifications_expires_at_status_index` (`expires_at`,`status`),
  CONSTRAINT `project_verifications_project_proposal_id_foreign` FOREIGN KEY (`project_proposal_id`) REFERENCES `project_proposals` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_verifications_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_verifications_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `provinces`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `provinces` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ine_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `autonomous_community_id` bigint unsigned NOT NULL,
  `country_id` bigint unsigned NOT NULL,
  `latitude` decimal(10,6) DEFAULT NULL,
  `longitude` decimal(10,6) DEFAULT NULL,
  `area_km2` decimal(10,2) DEFAULT NULL,
  `altitude_m` int DEFAULT NULL,
  `timezone_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `provinces_slug_unique` (`slug`),
  KEY `provinces_autonomous_community_id_foreign` (`autonomous_community_id`),
  KEY `provinces_country_id_foreign` (`country_id`),
  KEY `provinces_timezone_id_foreign` (`timezone_id`),
  CONSTRAINT `provinces_autonomous_community_id_foreign` FOREIGN KEY (`autonomous_community_id`) REFERENCES `autonomous_communities` (`id`) ON DELETE CASCADE,
  CONSTRAINT `provinces_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `provinces_timezone_id_foreign` FOREIGN KEY (`timezone_id`) REFERENCES `timezones` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `regions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `regions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `province_id` bigint unsigned NOT NULL,
  `autonomous_community_id` bigint unsigned DEFAULT NULL,
  `country_id` bigint unsigned NOT NULL,
  `latitude` decimal(10,6) DEFAULT NULL,
  `longitude` decimal(10,6) DEFAULT NULL,
  `area_km2` decimal(10,2) DEFAULT NULL,
  `altitude_m` int DEFAULT NULL,
  `timezone_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `regions_slug_unique` (`slug`),
  KEY `regions_province_id_foreign` (`province_id`),
  KEY `regions_autonomous_community_id_foreign` (`autonomous_community_id`),
  KEY `regions_country_id_foreign` (`country_id`),
  KEY `regions_timezone_id_foreign` (`timezone_id`),
  CONSTRAINT `regions_autonomous_community_id_foreign` FOREIGN KEY (`autonomous_community_id`) REFERENCES `autonomous_communities` (`id`) ON DELETE SET NULL,
  CONSTRAINT `regions_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `regions_province_id_foreign` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`) ON DELETE CASCADE,
  CONSTRAINT `regions_timezone_id_foreign` FOREIGN KEY (`timezone_id`) REFERENCES `timezones` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `relationship_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `relationship_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reciprocal_slug` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` enum('family','legal','sentimental','otro') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'family',
  `degree` tinyint unsigned DEFAULT NULL,
  `gender_specific` tinyint(1) NOT NULL DEFAULT '0',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_symmetrical` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `relationship_types_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `reputation_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reputation_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `action_type` enum('answer_accepted','answer_upvoted','question_upvoted','helpful_comment','tutorial_featured','project_completed','expert_verification','community_award','first_answer','consistency_bonus','answer_downvoted','question_downvoted','spam_detected','rule_violation','answer_deleted','reputation_reversal','daily_login','profile_completed','bounty_awarded','seasonal_bonus') COLLATE utf8mb4_unicode_ci NOT NULL,
  `reputation_change` int NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `topic_id` bigint unsigned DEFAULT NULL,
  `related_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `related_id` bigint unsigned NOT NULL,
  `triggered_by` bigint unsigned DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `metadata` json DEFAULT NULL,
  `is_validated` tinyint(1) NOT NULL DEFAULT '1',
  `is_reversed` tinyint(1) NOT NULL DEFAULT '0',
  `reversed_by` bigint unsigned DEFAULT NULL,
  `reversed_at` timestamp NULL DEFAULT NULL,
  `reversal_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reputation_transactions_topic_id_foreign` (`topic_id`),
  KEY `reputation_transactions_related_type_related_id_index` (`related_type`,`related_id`),
  KEY `reputation_transactions_triggered_by_foreign` (`triggered_by`),
  KEY `reputation_transactions_reversed_by_foreign` (`reversed_by`),
  KEY `reputation_transactions_user_id_created_at_index` (`user_id`,`created_at`),
  KEY `reputation_transactions_action_type_created_at_index` (`action_type`,`created_at`),
  KEY `reputation_transactions_category_created_at_index` (`category`,`created_at`),
  KEY `reputation_transactions_is_validated_is_reversed_index` (`is_validated`,`is_reversed`),
  CONSTRAINT `reputation_transactions_reversed_by_foreign` FOREIGN KEY (`reversed_by`) REFERENCES `users` (`id`),
  CONSTRAINT `reputation_transactions_topic_id_foreign` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`),
  CONSTRAINT `reputation_transactions_triggered_by_foreign` FOREIGN KEY (`triggered_by`) REFERENCES `users` (`id`),
  CONSTRAINT `reputation_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `roof_marketplace`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roof_marketplace` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `owner_id` bigint unsigned NOT NULL,
  `municipality_id` bigint unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `space_type` enum('residential_roof','commercial_roof','industrial_roof','agricultural_land','parking_lot','warehouse_roof','community_space','unused_land','building_facade','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `postal_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `access_instructions` text COLLATE utf8mb4_unicode_ci,
  `nearby_landmarks` json DEFAULT NULL,
  `total_area_m2` decimal(8,2) NOT NULL,
  `usable_area_m2` decimal(8,2) NOT NULL,
  `max_installable_power_kw` decimal(8,2) DEFAULT NULL,
  `roof_orientation` enum('north','northeast','east','southeast','south','southwest','west','northwest','flat','multiple','optimal') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `roof_inclination_degrees` int DEFAULT NULL,
  `roof_material` enum('tile','metal','concrete','asphalt','slate','wood','membrane','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `roof_condition` enum('excellent','good','fair','needs_repair','poor') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `roof_age_years` int DEFAULT NULL,
  `max_load_capacity_kg_m2` decimal(8,2) DEFAULT NULL,
  `annual_solar_irradiation_kwh_m2` decimal(8,2) DEFAULT NULL,
  `annual_sunny_days` int DEFAULT NULL,
  `shading_analysis` json DEFAULT NULL,
  `has_shading_issues` tinyint(1) NOT NULL DEFAULT '0',
  `shading_description` text COLLATE utf8mb4_unicode_ci,
  `access_difficulty` enum('easy','moderate','difficult','very_difficult') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `access_description` text COLLATE utf8mb4_unicode_ci,
  `crane_access` tinyint(1) NOT NULL DEFAULT '0',
  `vehicle_access` tinyint(1) NOT NULL DEFAULT '0',
  `distance_to_electrical_panel_m` decimal(6,2) DEFAULT NULL,
  `has_building_permits` tinyint(1) NOT NULL DEFAULT '0',
  `community_approval_required` tinyint(1) NOT NULL DEFAULT '0',
  `community_approval_obtained` tinyint(1) NOT NULL DEFAULT '0',
  `required_permits` json DEFAULT NULL,
  `obtained_permits` json DEFAULT NULL,
  `legal_restrictions` text COLLATE utf8mb4_unicode_ci,
  `offering_type` enum('rent','sale','partnership','free_use','energy_share','mixed') COLLATE utf8mb4_unicode_ci NOT NULL,
  `monthly_rent_eur` decimal(8,2) DEFAULT NULL,
  `sale_price_eur` decimal(10,2) DEFAULT NULL,
  `energy_share_percentage` decimal(5,2) DEFAULT NULL,
  `contract_duration_years` int DEFAULT NULL,
  `renewable_contract` tinyint(1) NOT NULL DEFAULT '1',
  `additional_terms` json DEFAULT NULL,
  `includes_maintenance` tinyint(1) NOT NULL DEFAULT '0',
  `includes_insurance` tinyint(1) NOT NULL DEFAULT '0',
  `includes_permits_management` tinyint(1) NOT NULL DEFAULT '0',
  `includes_monitoring` tinyint(1) NOT NULL DEFAULT '0',
  `included_services` json DEFAULT NULL,
  `additional_costs` json DEFAULT NULL,
  `availability_status` enum('available','under_negotiation','reserved','contracted','occupied','maintenance','temporarily_unavailable','withdrawn') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `available_from` date DEFAULT NULL,
  `available_until` date DEFAULT NULL,
  `availability_notes` text COLLATE utf8mb4_unicode_ci,
  `owner_lives_onsite` tinyint(1) NOT NULL DEFAULT '0',
  `owner_involvement` enum('none','minimal','moderate','active','full_partnership') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'minimal',
  `owner_preferences` json DEFAULT NULL,
  `owner_requirements` text COLLATE utf8mb4_unicode_ci,
  `views_count` int NOT NULL DEFAULT '0',
  `inquiries_count` int NOT NULL DEFAULT '0',
  `bookmarks_count` int NOT NULL DEFAULT '0',
  `rating` decimal(3,1) DEFAULT NULL,
  `reviews_count` int NOT NULL DEFAULT '0',
  `images` json DEFAULT NULL,
  `documents` json DEFAULT NULL,
  `technical_reports` json DEFAULT NULL,
  `solar_analysis_reports` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `verified_by` bigint unsigned DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `auto_respond_inquiries` tinyint(1) NOT NULL DEFAULT '0',
  `auto_response_message` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roof_marketplace_slug_unique` (`slug`),
  KEY `roof_marketplace_owner_id_foreign` (`owner_id`),
  KEY `roof_marketplace_verified_by_foreign` (`verified_by`),
  KEY `roof_marketplace_municipality_id_space_type_index` (`municipality_id`,`space_type`),
  KEY `roof_marketplace_availability_status_is_active_index` (`availability_status`,`is_active`),
  KEY `roof_marketplace_offering_type_usable_area_m2_index` (`offering_type`,`usable_area_m2`),
  KEY `roof_marketplace_is_featured_views_count_index` (`is_featured`,`views_count`),
  KEY `roof_marketplace_rating_reviews_count_index` (`rating`,`reviews_count`),
  FULLTEXT KEY `roof_marketplace_title_description_address_fulltext` (`title`,`description`,`address`),
  CONSTRAINT `roof_marketplace_municipality_id_foreign` FOREIGN KEY (`municipality_id`) REFERENCES `municipalities` (`id`),
  CONSTRAINT `roof_marketplace_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `roof_marketplace_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `scraping_sources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scraping_sources` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('blog','newspaper','wiki','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other',
  `source_type_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frequency` enum('daily','weekly','monthly') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_scraped_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
DROP TABLE IF EXISTS `social_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `social_accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `platform` enum('twitter','instagram','youtube','tiktok','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `handle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `person_id` bigint unsigned NOT NULL,
  `followers_count` int DEFAULT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `is_public` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `social_accounts_person_id_foreign` (`person_id`),
  CONSTRAINT `social_accounts_person_id_foreign` FOREIGN KEY (`person_id`) REFERENCES `people` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `social_comparisons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `social_comparisons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `comparison_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `period` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `scope` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `scope_id` bigint unsigned DEFAULT NULL,
  `user_value` decimal(15,4) NOT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `average_value` decimal(15,4) DEFAULT NULL,
  `median_value` decimal(15,4) DEFAULT NULL,
  `best_value` decimal(15,4) DEFAULT NULL,
  `user_rank` int DEFAULT NULL,
  `total_participants` int NOT NULL,
  `percentile` decimal(5,2) DEFAULT NULL,
  `breakdown` json DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '1',
  `comparison_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `social_comparisons_user_id_comparison_type_index` (`user_id`,`comparison_type`),
  KEY `social_comparisons_user_id_period_index` (`user_id`,`period`),
  KEY `social_comparisons_comparison_type_scope_index` (`comparison_type`,`scope`),
  KEY `social_comparisons_comparison_date_comparison_type_index` (`comparison_date`,`comparison_type`),
  KEY `social_comparisons_scope_scope_id_index` (`scope`,`scope_id`),
  KEY `social_comparisons_user_rank_total_participants_index` (`user_rank`,`total_participants`),
  CONSTRAINT `social_comparisons_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `social_interactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `social_interactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `interactable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `interactable_id` bigint unsigned NOT NULL,
  `interaction_type` enum('like','love','wow','celebrate','support','share','bookmark','follow','subscribe','report','hide','block') COLLATE utf8mb4_unicode_ci NOT NULL,
  `interaction_note` text COLLATE utf8mb4_unicode_ci,
  `interaction_data` json DEFAULT NULL,
  `source` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '1',
  `notify_author` tinyint(1) NOT NULL DEFAULT '1',
  `show_in_activity` tinyint(1) NOT NULL DEFAULT '1',
  `engagement_weight` int NOT NULL DEFAULT '1',
  `quality_score` decimal(5,2) NOT NULL DEFAULT '100.00',
  `interaction_expires_at` timestamp NULL DEFAULT NULL,
  `is_temporary` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('active','withdrawn','expired','flagged','hidden') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_interaction` (`user_id`,`interactable_type`,`interactable_id`,`interaction_type`),
  KEY `social_interactions_interactable_type_interactable_id_index` (`interactable_type`,`interactable_id`),
  KEY `si_morphs_type_idx` (`interactable_type`,`interactable_id`,`interaction_type`),
  KEY `si_user_type_created` (`user_id`,`interaction_type`,`created_at`),
  KEY `si_type_status_created` (`interaction_type`,`status`,`created_at`),
  KEY `si_public_activity` (`is_public`,`show_in_activity`),
  KEY `si_expires_temp` (`interaction_expires_at`,`is_temporary`),
  CONSTRAINT `social_interactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sponsored_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sponsored_content` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sponsor_id` bigint unsigned NOT NULL,
  `sponsorable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sponsorable_id` bigint unsigned NOT NULL,
  `campaign_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `campaign_description` text COLLATE utf8mb4_unicode_ci,
  `content_type` enum('promoted_post','banner_ad','sponsored_topic','product_placement','native_content','event_promotion','job_posting','service_highlight') COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_audience` json DEFAULT NULL,
  `target_topics` json DEFAULT NULL,
  `target_locations` json DEFAULT NULL,
  `target_demographics` json DEFAULT NULL,
  `ad_label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Patrocinado',
  `call_to_action` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `destination_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creative_assets` json DEFAULT NULL,
  `pricing_model` enum('cpm','cpc','cpa','fixed') COLLATE utf8mb4_unicode_ci NOT NULL,
  `bid_amount` decimal(10,4) NOT NULL,
  `daily_budget` decimal(10,2) DEFAULT NULL,
  `total_budget` decimal(10,2) DEFAULT NULL,
  `spent_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `start_date` timestamp NOT NULL,
  `end_date` timestamp NULL DEFAULT NULL,
  `schedule_config` json DEFAULT NULL,
  `status` enum('draft','pending_review','approved','active','paused','completed','rejected','expired') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `review_notes` text COLLATE utf8mb4_unicode_ci,
  `impressions` int NOT NULL DEFAULT '0',
  `clicks` int NOT NULL DEFAULT '0',
  `conversions` int NOT NULL DEFAULT '0',
  `ctr` decimal(5,2) NOT NULL DEFAULT '0.00',
  `conversion_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `engagement_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `show_sponsor_info` tinyint(1) NOT NULL DEFAULT '1',
  `allow_user_feedback` tinyint(1) NOT NULL DEFAULT '1',
  `disclosure_text` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sponsored_content_sponsorable_type_sponsorable_id_index` (`sponsorable_type`,`sponsorable_id`),
  KEY `sponsored_content_reviewed_by_foreign` (`reviewed_by`),
  KEY `sponsored_content_sponsor_id_status_index` (`sponsor_id`,`status`),
  KEY `sponsored_content_content_type_status_start_date_index` (`content_type`,`status`,`start_date`),
  KEY `sponsored_content_status_start_date_end_date_index` (`status`,`start_date`,`end_date`),
  KEY `sponsored_content_pricing_model_bid_amount_index` (`pricing_model`,`bid_amount`),
  CONSTRAINT `sponsored_content_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`),
  CONSTRAINT `sponsored_content_sponsor_id_foreign` FOREIGN KEY (`sponsor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stats` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `subject_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_id` bigint unsigned NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` decimal(20,4) NOT NULL,
  `year` year NOT NULL,
  `data_source_id` bigint unsigned DEFAULT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `confidence_level` decimal(5,2) DEFAULT NULL,
  `source_note` text COLLATE utf8mb4_unicode_ci,
  `is_projection` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stats_subject_type_subject_id_index` (`subject_type`,`subject_id`),
  KEY `stats_data_source_id_foreign` (`data_source_id`),
  CONSTRAINT `stats_data_source_id_foreign` FOREIGN KEY (`data_source_id`) REFERENCES `data_sources` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `subscription_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscription_plans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('individual','cooperative','business','enterprise') COLLATE utf8mb4_unicode_ci NOT NULL,
  `billing_cycle` enum('monthly','yearly','one_time') COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `setup_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `trial_days` int NOT NULL DEFAULT '0',
  `max_projects` int DEFAULT NULL,
  `max_cooperatives` int DEFAULT NULL,
  `max_investments` int DEFAULT NULL,
  `max_consultations` int DEFAULT NULL,
  `features` json NOT NULL,
  `limits` json NOT NULL,
  `commission_rate` decimal(5,4) NOT NULL DEFAULT '0.0500',
  `priority_support` tinyint(1) NOT NULL DEFAULT '0',
  `verified_badge` tinyint(1) NOT NULL DEFAULT '0',
  `analytics_access` tinyint(1) NOT NULL DEFAULT '0',
  `api_access` tinyint(1) NOT NULL DEFAULT '0',
  `white_label` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscription_plans_slug_unique` (`slug`),
  KEY `subscription_plans_type_is_active_index` (`type`,`is_active`),
  KEY `subscription_plans_billing_cycle_is_active_index` (`billing_cycle`,`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sync_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sync_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `data_source_id` bigint unsigned DEFAULT NULL,
  `status` enum('success','failed') COLLATE utf8mb4_unicode_ci NOT NULL,
  `started_at` timestamp NOT NULL,
  `finished_at` timestamp NULL DEFAULT NULL,
  `processed_items_count` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sync_logs_data_source_id_foreign` (`data_source_id`),
  CONSTRAINT `sync_logs_data_source_id_foreign` FOREIGN KEY (`data_source_id`) REFERENCES `data_sources` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tag_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tag_groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tag_groups_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `taggables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `taggables` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tag_id` bigint unsigned NOT NULL,
  `taggable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `taggable_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `taggables_tag_id_foreign` (`tag_id`),
  KEY `taggables_taggable_type_taggable_id_index` (`taggable_type`,`taggable_id`),
  CONSTRAINT `taggables_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tags` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tag_type` enum('topic','style','theme','mood') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'topic',
  `is_searchable` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tags_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `timezones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `timezones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `offset` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dst_offset` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `topic_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `topic_comments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `topic_post_id` bigint unsigned NOT NULL,
  `author_id` bigint unsigned NOT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `images` json DEFAULT NULL,
  `attachments` json DEFAULT NULL,
  `links` json DEFAULT NULL,
  `status` enum('published','pending','rejected','edited') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'published',
  `edit_reason` text COLLATE utf8mb4_unicode_ci,
  `edited_at` timestamp NULL DEFAULT NULL,
  `likes_count` int NOT NULL DEFAULT '0',
  `replies_count` int NOT NULL DEFAULT '0',
  `is_solution` tinyint(1) NOT NULL DEFAULT '0',
  `is_pinned` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `topic_comments_topic_post_id_status_created_at_index` (`topic_post_id`,`status`,`created_at`),
  KEY `topic_comments_author_id_status_index` (`author_id`,`status`),
  KEY `topic_comments_parent_id_created_at_index` (`parent_id`,`created_at`),
  KEY `topic_comments_is_solution_index` (`is_solution`),
  CONSTRAINT `topic_comments_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `topic_comments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `topic_comments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `topic_comments_topic_post_id_foreign` FOREIGN KEY (`topic_post_id`) REFERENCES `topic_posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `topic_following`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `topic_following` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `topic_id` bigint unsigned NOT NULL,
  `notification_level` enum('all','posts_only','mentions','none') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'posts_only',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `topic_following_user_id_topic_id_unique` (`user_id`,`topic_id`),
  KEY `topic_following_topic_id_foreign` (`topic_id`),
  CONSTRAINT `topic_following_topic_id_foreign` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`) ON DELETE CASCADE,
  CONSTRAINT `topic_following_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `topic_followings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `topic_followings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `topic_id` bigint unsigned NOT NULL,
  `follow_type` enum('following','watching','ignoring') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'following',
  `notifications_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `notification_preferences` json DEFAULT NULL,
  `followed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_visited_at` timestamp NULL DEFAULT NULL,
  `visit_count` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_topic_follow_unique` (`user_id`,`topic_id`),
  KEY `topic_followings_user_id_follow_type_index` (`user_id`,`follow_type`),
  KEY `topic_followings_topic_id_follow_type_index` (`topic_id`,`follow_type`),
  KEY `topic_followings_followed_at_index` (`followed_at`),
  KEY `topic_followings_notifications_enabled_follow_type_index` (`notifications_enabled`,`follow_type`),
  CONSTRAINT `topic_followings_topic_id_foreign` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`) ON DELETE CASCADE,
  CONSTRAINT `topic_followings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `topic_memberships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `topic_memberships` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `topic_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `role` enum('member','moderator','admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'member',
  `status` enum('active','banned','muted') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `notifications_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `email_notifications` tinyint(1) NOT NULL DEFAULT '0',
  `notification_preferences` json DEFAULT NULL,
  `posts_count` int NOT NULL DEFAULT '0',
  `comments_count` int NOT NULL DEFAULT '0',
  `reputation_score` int NOT NULL DEFAULT '0',
  `last_activity_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `topic_memberships_topic_id_user_id_unique` (`topic_id`,`user_id`),
  KEY `topic_memberships_topic_id_role_index` (`topic_id`,`role`),
  KEY `topic_memberships_user_id_status_index` (`user_id`,`status`),
  CONSTRAINT `topic_memberships_topic_id_foreign` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`) ON DELETE CASCADE,
  CONSTRAINT `topic_memberships_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `topic_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `topic_posts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `topic_id` bigint unsigned NOT NULL,
  `author_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('discussion','question','tutorial','news','poll','showcase','help','announcement') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'discussion',
  `images` json DEFAULT NULL,
  `attachments` json DEFAULT NULL,
  `links` json DEFAULT NULL,
  `poll_data` json DEFAULT NULL,
  `tags` json DEFAULT NULL,
  `difficulty_level` enum('beginner','intermediate','advanced') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estimated_cost` decimal(10,2) DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('published','pending','rejected','archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'published',
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `views_count` int NOT NULL DEFAULT '0',
  `likes_count` int NOT NULL DEFAULT '0',
  `comments_count` int NOT NULL DEFAULT '0',
  `shares_count` int NOT NULL DEFAULT '0',
  `bookmarks_count` int NOT NULL DEFAULT '0',
  `engagement_score` decimal(8,2) NOT NULL DEFAULT '0.00',
  `is_pinned` tinyint(1) NOT NULL DEFAULT '0',
  `is_locked` tinyint(1) NOT NULL DEFAULT '0',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `allow_comments` tinyint(1) NOT NULL DEFAULT '1',
  `notify_on_comment` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `topic_posts_approved_by_foreign` (`approved_by`),
  KEY `topic_posts_topic_id_status_created_at_index` (`topic_id`,`status`,`created_at`),
  KEY `topic_posts_author_id_status_index` (`author_id`,`status`),
  KEY `topic_posts_type_status_index` (`type`,`status`),
  KEY `topic_posts_engagement_score_created_at_index` (`engagement_score`,`created_at`),
  KEY `topic_posts_is_pinned_index` (`is_pinned`),
  FULLTEXT KEY `topic_posts_title_content_fulltext` (`title`,`content`),
  CONSTRAINT `topic_posts_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`),
  CONSTRAINT `topic_posts_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `topic_posts_topic_id_foreign` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `topics` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#3B82F6',
  `banner_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator_id` bigint unsigned NOT NULL,
  `moderator_ids` json DEFAULT NULL,
  `rules` json DEFAULT NULL,
  `visibility` enum('public','private','restricted') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public',
  `post_permission` enum('everyone','members','moderators') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'everyone',
  `comment_permission` enum('everyone','members','verified') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'everyone',
  `category` enum('technology','legislation','financing','installation','cooperative','market','diy','news','beginners','professional','regional','general') COLLATE utf8mb4_unicode_ci NOT NULL,
  `members_count` int NOT NULL DEFAULT '0',
  `posts_count` int NOT NULL DEFAULT '0',
  `comments_count` int NOT NULL DEFAULT '0',
  `activity_score` decimal(8,2) NOT NULL DEFAULT '0.00',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `requires_approval` tinyint(1) NOT NULL DEFAULT '0',
  `allow_polls` tinyint(1) NOT NULL DEFAULT '1',
  `allow_images` tinyint(1) NOT NULL DEFAULT '1',
  `allow_links` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `topics_slug_unique` (`slug`),
  KEY `topics_creator_id_foreign` (`creator_id`),
  KEY `topics_category_is_active_index` (`category`,`is_active`),
  KEY `topics_visibility_is_active_index` (`visibility`,`is_active`),
  KEY `topics_activity_score_created_at_index` (`activity_score`,`created_at`),
  FULLTEXT KEY `topics_name_description_fulltext` (`name`,`description`),
  CONSTRAINT `topics_creator_id_foreign` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_achievements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_achievements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `achievement_id` bigint unsigned NOT NULL,
  `progress` int NOT NULL DEFAULT '0',
  `level` int NOT NULL DEFAULT '1',
  `is_completed` tinyint(1) NOT NULL DEFAULT '0',
  `completed_at` datetime DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `value_achieved` decimal(15,4) DEFAULT NULL,
  `points_earned` int NOT NULL DEFAULT '0',
  `is_notified` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_achievements_user_id_achievement_id_level_unique` (`user_id`,`achievement_id`,`level`),
  KEY `user_achievements_user_id_is_completed_index` (`user_id`,`is_completed`),
  KEY `user_achievements_achievement_id_is_completed_index` (`achievement_id`,`is_completed`),
  CONSTRAINT `user_achievements_achievement_id_foreign` FOREIGN KEY (`achievement_id`) REFERENCES `achievements` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_achievements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_badges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_badges` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `badge_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#3B82F6',
  `criteria` json NOT NULL,
  `metadata` json DEFAULT NULL,
  `points_awarded` int NOT NULL DEFAULT '0',
  `is_public` tinyint(1) NOT NULL DEFAULT '1',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `earned_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_badges_user_id_badge_type_index` (`user_id`,`badge_type`),
  KEY `user_badges_user_id_category_index` (`user_id`,`category`),
  KEY `user_badges_badge_type_category_index` (`badge_type`,`category`),
  KEY `user_badges_earned_at_index` (`earned_at`),
  CONSTRAINT `user_badges_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_bookmarks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_bookmarks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `bookmarkable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bookmarkable_id` bigint unsigned NOT NULL,
  `folder` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tags` json DEFAULT NULL,
  `personal_notes` text COLLATE utf8mb4_unicode_ci,
  `priority` int NOT NULL DEFAULT '0',
  `reminder_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `reminder_date` timestamp NULL DEFAULT NULL,
  `reminder_frequency` enum('once','weekly','monthly') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `access_count` int NOT NULL DEFAULT '0',
  `last_accessed_at` timestamp NULL DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_bookmarks_user_id_bookmarkable_type_bookmarkable_id_unique` (`user_id`,`bookmarkable_type`,`bookmarkable_id`),
  KEY `user_bookmarks_bookmarkable_type_bookmarkable_id_index` (`bookmarkable_type`,`bookmarkable_id`),
  KEY `user_bookmarks_user_id_folder_index` (`user_id`,`folder`),
  KEY `user_bookmarks_user_id_priority_created_at_index` (`user_id`,`priority`,`created_at`),
  KEY `user_bookmarks_reminder_enabled_reminder_date_index` (`reminder_enabled`,`reminder_date`),
  CONSTRAINT `user_bookmarks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_challenges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_challenges` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `challenge_id` bigint unsigned NOT NULL,
  `status` enum('registered','active','completed','failed','abandoned') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'registered',
  `joined_at` datetime NOT NULL,
  `completed_at` datetime DEFAULT NULL,
  `progress` json DEFAULT NULL,
  `current_value` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `ranking_position` int DEFAULT NULL,
  `points_earned` int NOT NULL DEFAULT '0',
  `reward_earned` decimal(10,2) NOT NULL DEFAULT '0.00',
  `achievements_unlocked` json DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `is_team_leader` tinyint(1) NOT NULL DEFAULT '0',
  `team_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_challenges_user_id_challenge_id_unique` (`user_id`,`challenge_id`),
  KEY `user_challenges_challenge_id_status_index` (`challenge_id`,`status`),
  KEY `user_challenges_user_id_status_index` (`user_id`,`status`),
  KEY `user_challenges_ranking_position_index` (`ranking_position`),
  CONSTRAINT `user_challenges_challenge_id_foreign` FOREIGN KEY (`challenge_id`) REFERENCES `challenges` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_challenges_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_devices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `device_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `platform` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `browser` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notifications_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_devices_user_id_foreign` (`user_id`),
  CONSTRAINT `user_devices_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_endorsements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_endorsements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `endorser_id` bigint unsigned NOT NULL,
  `endorsed_id` bigint unsigned NOT NULL,
  `skill_category` enum('solar_installation','electrical_work','project_management','energy_consulting','legal_advice','financing','maintenance','design_engineering','sales','customer_service','training','research','policy_analysis','community_building','technical_writing','general_knowledge') COLLATE utf8mb4_unicode_ci NOT NULL,
  `specific_skill` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `endorsement_text` text COLLATE utf8mb4_unicode_ci,
  `skill_rating` decimal(3,1) DEFAULT NULL,
  `relationship_context` enum('colleague','client','supplier','mentor','mentee','collaborator','competitor','community_member','student','teacher','unknown') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'community_member',
  `project_context` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `collaboration_duration_months` int DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `trust_score` decimal(5,2) NOT NULL DEFAULT '100.00',
  `helpful_votes` int NOT NULL DEFAULT '0',
  `total_votes` int NOT NULL DEFAULT '0',
  `is_public` tinyint(1) NOT NULL DEFAULT '1',
  `show_on_profile` tinyint(1) NOT NULL DEFAULT '1',
  `notify_endorsed` tinyint(1) NOT NULL DEFAULT '1',
  `is_mutual` tinyint(1) NOT NULL DEFAULT '0',
  `reciprocal_endorsement_id` bigint unsigned DEFAULT NULL,
  `status` enum('active','pending','rejected','disputed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `disputed_by` bigint unsigned DEFAULT NULL,
  `dispute_reason` text COLLATE utf8mb4_unicode_ci,
  `disputed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_endorsement` (`endorser_id`,`endorsed_id`,`skill_category`,`specific_skill`),
  KEY `user_endorsements_reciprocal_endorsement_id_foreign` (`reciprocal_endorsement_id`),
  KEY `user_endorsements_disputed_by_foreign` (`disputed_by`),
  KEY `user_endorsements_endorsed_id_skill_category_is_public_index` (`endorsed_id`,`skill_category`,`is_public`),
  KEY `user_endorsements_endorser_id_created_at_index` (`endorser_id`,`created_at`),
  KEY `user_endorsements_skill_category_skill_rating_is_verified_index` (`skill_category`,`skill_rating`,`is_verified`),
  KEY `user_endorsements_status_is_public_index` (`status`,`is_public`),
  CONSTRAINT `user_endorsements_disputed_by_foreign` FOREIGN KEY (`disputed_by`) REFERENCES `users` (`id`),
  CONSTRAINT `user_endorsements_endorsed_id_foreign` FOREIGN KEY (`endorsed_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_endorsements_endorser_id_foreign` FOREIGN KEY (`endorser_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_endorsements_reciprocal_endorsement_id_foreign` FOREIGN KEY (`reciprocal_endorsement_id`) REFERENCES `user_endorsements` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_follows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_follows` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `follower_id` bigint unsigned NOT NULL,
  `following_id` bigint unsigned NOT NULL,
  `follow_type` enum('general','expertise','projects','achievements','energy_activity','installations','investments','content','community') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `notify_new_activity` tinyint(1) NOT NULL DEFAULT '1',
  `notify_achievements` tinyint(1) NOT NULL DEFAULT '1',
  `notify_projects` tinyint(1) NOT NULL DEFAULT '1',
  `notify_investments` tinyint(1) NOT NULL DEFAULT '0',
  `notify_milestones` tinyint(1) NOT NULL DEFAULT '1',
  `notify_content` tinyint(1) NOT NULL DEFAULT '1',
  `notification_frequency` enum('instant','daily_digest','weekly_digest','monthly_digest','never') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'instant',
  `show_in_main_feed` tinyint(1) NOT NULL DEFAULT '1',
  `prioritize_in_feed` tinyint(1) NOT NULL DEFAULT '0',
  `feed_weight` int NOT NULL DEFAULT '100',
  `follow_reason` text COLLATE utf8mb4_unicode_ci,
  `interests` json DEFAULT NULL,
  `tags` json DEFAULT NULL,
  `is_mutual` tinyint(1) NOT NULL DEFAULT '0',
  `mutual_since` timestamp NULL DEFAULT NULL,
  `interactions_count` int NOT NULL DEFAULT '0',
  `last_interaction_at` timestamp NULL DEFAULT NULL,
  `engagement_score` decimal(8,2) NOT NULL DEFAULT '0.00',
  `content_views` int NOT NULL DEFAULT '0',
  `is_public` tinyint(1) NOT NULL DEFAULT '1',
  `show_to_followed` tinyint(1) NOT NULL DEFAULT '1',
  `allow_followed_to_see_activity` tinyint(1) NOT NULL DEFAULT '1',
  `content_filters` json DEFAULT NULL,
  `activity_filters` json DEFAULT NULL,
  `minimum_relevance_score` decimal(5,2) NOT NULL DEFAULT '0.00',
  `status` enum('active','paused','muted','blocked','requested','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `status_changed_at` timestamp NULL DEFAULT NULL,
  `status_reason` text COLLATE utf8mb4_unicode_ci,
  `followed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_seen_activity_at` timestamp NULL DEFAULT NULL,
  `days_following` int NOT NULL DEFAULT '0',
  `relevance_decay_rate` decimal(5,2) NOT NULL DEFAULT '1.00',
  `algorithm_preferences` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_follow_relationship` (`follower_id`,`following_id`),
  KEY `uf_follower_status` (`follower_id`,`status`,`created_at`),
  KEY `uf_following_status` (`following_id`,`status`,`created_at`),
  KEY `uf_type_status` (`follow_type`,`status`),
  KEY `uf_mutual` (`is_mutual`,`mutual_since`),
  KEY `uf_notif_freq` (`notification_frequency`,`notify_new_activity`),
  KEY `uf_engagement` (`engagement_score`,`last_interaction_at`),
  KEY `uf_feed_weight` (`show_in_main_feed`,`feed_weight`),
  CONSTRAINT `user_follows_follower_id_foreign` FOREIGN KEY (`follower_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_follows_following_id_foreign` FOREIGN KEY (`following_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_generated_contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_generated_contents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `related_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `related_id` bigint unsigned NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('photo','comment','suggestion') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_generated_contents_user_id_foreign` (`user_id`),
  KEY `user_generated_contents_related_type_related_id_index` (`related_type`,`related_id`),
  CONSTRAINT `user_generated_contents_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_lists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_lists` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#3B82F6',
  `cover_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `list_type` enum('mixed','users','posts','projects','companies','resources','events','custom') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mixed',
  `allowed_content_types` json DEFAULT NULL,
  `visibility` enum('private','public','followers','collaborative') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'private',
  `collaborator_ids` json DEFAULT NULL,
  `allow_suggestions` tinyint(1) NOT NULL DEFAULT '0',
  `allow_comments` tinyint(1) NOT NULL DEFAULT '0',
  `curation_mode` enum('manual','auto_hashtag','auto_keyword','auto_author','auto_topic') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `auto_criteria` json DEFAULT NULL,
  `items_count` int NOT NULL DEFAULT '0',
  `followers_count` int NOT NULL DEFAULT '0',
  `views_count` int NOT NULL DEFAULT '0',
  `shares_count` int NOT NULL DEFAULT '0',
  `engagement_score` decimal(8,2) NOT NULL DEFAULT '0.00',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `is_template` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_lists_user_id_slug_unique` (`user_id`,`slug`),
  UNIQUE KEY `user_lists_slug_unique` (`slug`),
  KEY `user_lists_user_id_list_type_visibility_index` (`user_id`,`list_type`,`visibility`),
  KEY `user_lists_visibility_is_featured_engagement_score_index` (`visibility`,`is_featured`,`engagement_score`),
  KEY `user_lists_list_type_is_active_index` (`list_type`,`is_active`),
  FULLTEXT KEY `user_lists_name_description_fulltext` (`name`,`description`),
  CONSTRAINT `user_lists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_privileges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_privileges` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `privilege_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `scope` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'global',
  `scope_id` bigint unsigned DEFAULT NULL,
  `level` int NOT NULL DEFAULT '1',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `permissions` json DEFAULT NULL,
  `limits` json DEFAULT NULL,
  `reputation_required` int NOT NULL DEFAULT '0',
  `granted_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` timestamp NULL DEFAULT NULL,
  `granted_by` bigint unsigned DEFAULT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_privileges_granted_by_foreign` (`granted_by`),
  KEY `user_privileges_user_id_privilege_type_index` (`user_id`,`privilege_type`),
  KEY `user_privileges_user_id_scope_scope_id_index` (`user_id`,`scope`,`scope_id`),
  KEY `user_privileges_privilege_type_level_index` (`privilege_type`,`level`),
  KEY `user_privileges_is_active_expires_at_index` (`is_active`,`expires_at`),
  KEY `user_privileges_reputation_required_index` (`reputation_required`),
  CONSTRAINT `user_privileges_granted_by_foreign` FOREIGN KEY (`granted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `user_privileges_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_reputations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_reputations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `total_reputation` int NOT NULL DEFAULT '1',
  `category_reputation` json DEFAULT NULL,
  `topic_reputation` json DEFAULT NULL,
  `helpful_answers` int NOT NULL DEFAULT '0',
  `accepted_solutions` int NOT NULL DEFAULT '0',
  `quality_posts` int NOT NULL DEFAULT '0',
  `verified_contributions` int NOT NULL DEFAULT '0',
  `upvotes_received` int NOT NULL DEFAULT '0',
  `downvotes_received` int NOT NULL DEFAULT '0',
  `upvote_ratio` decimal(5,2) NOT NULL DEFAULT '0.00',
  `topics_created` int NOT NULL DEFAULT '0',
  `successful_projects` int NOT NULL DEFAULT '0',
  `mentorship_points` int NOT NULL DEFAULT '0',
  `warnings_received` int NOT NULL DEFAULT '0',
  `content_removed` int NOT NULL DEFAULT '0',
  `is_suspended` tinyint(1) NOT NULL DEFAULT '0',
  `suspended_until` timestamp NULL DEFAULT NULL,
  `global_rank` int DEFAULT NULL,
  `category_ranks` json DEFAULT NULL,
  `monthly_rank` int DEFAULT NULL,
  `is_verified_professional` tinyint(1) NOT NULL DEFAULT '0',
  `professional_credentials` json DEFAULT NULL,
  `expertise_areas` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_reputations_user_id_unique` (`user_id`),
  KEY `user_reputations_total_reputation_global_rank_index` (`total_reputation`,`global_rank`),
  KEY `user_reputations_is_verified_professional_index` (`is_verified_professional`),
  CONSTRAINT `user_reputations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_reviews` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reviewer_id` bigint unsigned NOT NULL,
  `reviewable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reviewable_id` bigint unsigned NOT NULL,
  `overall_rating` decimal(3,1) NOT NULL,
  `detailed_ratings` json DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `pros` json DEFAULT NULL,
  `cons` json DEFAULT NULL,
  `images` json DEFAULT NULL,
  `attachments` json DEFAULT NULL,
  `service_type` enum('installation','maintenance','consulting','design','financing','legal_advice','training','product_sale','project_management','community_service','platform_experience','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `service_date` date DEFAULT NULL,
  `service_cost` decimal(10,2) DEFAULT NULL,
  `service_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_duration_days` int DEFAULT NULL,
  `is_verified_purchase` tinyint(1) NOT NULL DEFAULT '0',
  `verification_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `verified_by` bigint unsigned DEFAULT NULL,
  `would_recommend` tinyint(1) DEFAULT NULL,
  `recommendation_level` enum('highly_recommend','recommend','neutral','not_recommend','strongly_not_recommend') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `helpful_votes` int NOT NULL DEFAULT '0',
  `not_helpful_votes` int NOT NULL DEFAULT '0',
  `total_votes` int NOT NULL DEFAULT '0',
  `helpfulness_ratio` decimal(5,2) NOT NULL DEFAULT '0.00',
  `views_count` int NOT NULL DEFAULT '0',
  `provider_response` text COLLATE utf8mb4_unicode_ci,
  `provider_responded_at` timestamp NULL DEFAULT NULL,
  `provider_responder_id` bigint unsigned DEFAULT NULL,
  `status` enum('published','pending_review','flagged','hidden','rejected','disputed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'published',
  `flags_count` int NOT NULL DEFAULT '0',
  `flag_reasons` json DEFAULT NULL,
  `moderated_by` bigint unsigned DEFAULT NULL,
  `moderated_at` timestamp NULL DEFAULT NULL,
  `moderation_notes` text COLLATE utf8mb4_unicode_ci,
  `is_anonymous` tinyint(1) NOT NULL DEFAULT '0',
  `show_service_cost` tinyint(1) NOT NULL DEFAULT '0',
  `allow_contact` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_reviews_reviewable_type_reviewable_id_index` (`reviewable_type`,`reviewable_id`),
  KEY `user_reviews_verified_by_foreign` (`verified_by`),
  KEY `user_reviews_provider_responder_id_foreign` (`provider_responder_id`),
  KEY `user_reviews_moderated_by_foreign` (`moderated_by`),
  KEY `user_reviews_reviewable_type_reviewable_id_status_index` (`reviewable_type`,`reviewable_id`,`status`),
  KEY `user_reviews_reviewer_id_created_at_index` (`reviewer_id`,`created_at`),
  KEY `user_reviews_overall_rating_status_index` (`overall_rating`,`status`),
  KEY `user_reviews_service_type_is_verified_purchase_index` (`service_type`,`is_verified_purchase`),
  KEY `user_reviews_helpful_votes_total_votes_index` (`helpful_votes`,`total_votes`),
  KEY `user_reviews_status_flags_count_index` (`status`,`flags_count`),
  CONSTRAINT `user_reviews_moderated_by_foreign` FOREIGN KEY (`moderated_by`) REFERENCES `users` (`id`),
  CONSTRAINT `user_reviews_provider_responder_id_foreign` FOREIGN KEY (`provider_responder_id`) REFERENCES `users` (`id`),
  CONSTRAINT `user_reviews_reviewer_id_foreign` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_reviews_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_subscriptions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `subscription_plan_id` bigint unsigned NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'EUR',
  `billing_cycle` enum('monthly','yearly','one_time') COLLATE utf8mb4_unicode_ci NOT NULL,
  `starts_at` datetime NOT NULL,
  `ends_at` datetime DEFAULT NULL,
  `trial_ends_at` datetime DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `next_billing_at` datetime DEFAULT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `external_subscription_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usage_stats` json DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `cancellation_reason` text COLLATE utf8mb4_unicode_ci,
  `auto_renew` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_plan_status_unique` (`user_id`,`subscription_plan_id`,`status`),
  KEY `user_subscriptions_subscription_plan_id_foreign` (`subscription_plan_id`),
  KEY `user_subscriptions_user_id_status_index` (`user_id`,`status`),
  KEY `user_subscriptions_status_ends_at_index` (`status`,`ends_at`),
  KEY `user_subscriptions_next_billing_at_index` (`next_billing_at`),
  CONSTRAINT `user_subscriptions_subscription_plan_id_foreign` FOREIGN KEY (`subscription_plan_id`) REFERENCES `subscription_plans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_subscriptions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `two_factor_secret` text COLLATE utf8mb4_unicode_ci,
  `two_factor_recovery_codes` text COLLATE utf8mb4_unicode_ci,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `venue_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `venue_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `venue_types_name_unique` (`name`),
  UNIQUE KEY `venue_types_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `venues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `venues` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `municipality_id` bigint unsigned NOT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `capacity` int DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `venue_type` enum('auditorium','park','square','club','online','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `venue_status` enum('active','closed','under_construction') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `venues_slug_unique` (`slug`),
  KEY `venues_municipality_id_foreign` (`municipality_id`),
  CONSTRAINT `venues_municipality_id_foreign` FOREIGN KEY (`municipality_id`) REFERENCES `municipalities` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `visual_identities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `visual_identities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `weather_and_solar_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `weather_and_solar_data` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `datetime` datetime NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `municipality_id` bigint unsigned DEFAULT NULL,
  `latitude` decimal(10,6) DEFAULT NULL,
  `longitude` decimal(10,6) DEFAULT NULL,
  `temperature` double DEFAULT NULL,
  `temperature_min` decimal(5,2) DEFAULT NULL,
  `temperature_max` decimal(5,2) DEFAULT NULL,
  `humidity` double DEFAULT NULL,
  `cloud_coverage` double DEFAULT NULL,
  `solar_irradiance` double DEFAULT NULL,
  `solar_irradiance_daily` decimal(8,3) DEFAULT NULL,
  `uv_index` decimal(4,1) DEFAULT NULL,
  `wind_speed` double DEFAULT NULL,
  `wind_direction` decimal(5,1) DEFAULT NULL,
  `wind_gust` decimal(5,2) DEFAULT NULL,
  `precipitation` double DEFAULT NULL,
  `pressure` decimal(7,2) DEFAULT NULL,
  `visibility` decimal(5,1) DEFAULT NULL,
  `weather_condition` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_type` enum('historical','current','forecast') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'current',
  `source` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `source_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `solar_potential` decimal(8,3) DEFAULT NULL,
  `wind_potential` decimal(8,3) DEFAULT NULL,
  `is_optimal_solar` tinyint(1) NOT NULL DEFAULT '0',
  `is_optimal_wind` tinyint(1) NOT NULL DEFAULT '0',
  `air_quality_index` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `weather_and_solar_data_datetime_data_type_index` (`datetime`,`data_type`),
  KEY `weather_and_solar_data_municipality_id_datetime_index` (`municipality_id`,`datetime`),
  KEY `weather_and_solar_data_is_optimal_solar_datetime_index` (`is_optimal_solar`,`datetime`),
  KEY `weather_and_solar_data_is_optimal_wind_datetime_index` (`is_optimal_wind`,`datetime`),
  KEY `weather_and_solar_data_latitude_longitude_index` (`latitude`,`longitude`),
  CONSTRAINT `weather_and_solar_data_municipality_id_foreign` FOREIGN KEY (`municipality_id`) REFERENCES `municipalities` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `works`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `works` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('book','movie','tv_show','theatre_play','article') COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `release_year` year DEFAULT NULL,
  `person_id` bigint unsigned DEFAULT NULL,
  `genre` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `language_id` bigint unsigned DEFAULT NULL,
  `link_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `works_slug_unique` (`slug`),
  KEY `works_person_id_foreign` (`person_id`),
  KEY `works_language_id_foreign` (`language_id`),
  KEY `works_link_id_foreign` (`link_id`),
  CONSTRAINT `works_language_id_foreign` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE SET NULL,
  CONSTRAINT `works_link_id_foreign` FOREIGN KEY (`link_id`) REFERENCES `links` (`id`) ON DELETE SET NULL,
  CONSTRAINT `works_person_id_foreign` FOREIGN KEY (`person_id`) REFERENCES `people` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `zone_climates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `zone_climates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `climate_zone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `average_heating_demand` decimal(8,2) DEFAULT NULL,
  `average_cooling_demand` decimal(8,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'0001_01_01_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2025_07_08_151846_create_personal_access_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2025_07_08_153338_add_two_factor_columns_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2025_07_08_155335_create_permission_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2025_07_08_191508_create_audits_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2025_07_08_191643_create_activity_log_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2025_07_08_191644_add_event_column_to_activity_log_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2025_07_08_191645_add_batch_uuid_column_to_activity_log_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2025_07_08_191801_create_media_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2025_07_08_192547_create_organizations_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2025_07_08_192753_create_app_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2025_07_08_193026_create_organization_features_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2025_07_11_085558_create_timezones_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2025_07_11_091349_create_countries_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2025_07_11_091508_create_languages_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2025_07_11_091833_create_country_language_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2025_07_11_102230_create_autonomous_communities_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2025_07_11_102356_create_provinces_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2025_07_11_102621_create_regions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2025_07_11_102823_create_municipalities_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2025_07_11_103026_create_point_of_interests_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2025_07_11_103443_create_tags_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2025_07_11_103606_create_point_of_interest_tag_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2025_07_11_193306_create_professions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2025_07_11_193545_create_people_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2025_07_11_194139_create_aliases_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2025_07_12_051805_create_images_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2025_07_12_055648_create_links_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2025_07_12_065406_create_works_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2025_07_12_071334_create_awards_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2025_07_12_071603_create_award_winners_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2025_07_12_124657_create_appearances_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35,'2025_07_12_171053_create_family_members_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36,'2025_07_12_171850_create_anniversaries_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37,'2025_07_12_172126_create_calendar_holidays_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38,'2025_07_12_175333_create_calendar_holiday_locations_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (39,'2025_07_13_014139_create_venues_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (40,'2025_07_13_014354_create_festivals_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (41,'2025_07_13_014427_create_event_types_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (42,'2025_07_13_023914_create_events_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (43,'2025_07_13_094520_create_artists_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (44,'2025_07_13_100128_create_groups_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (45,'2025_07_13_184050_create_venue_types_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (46,'2025_07_13_185332_create_colors_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (47,'2025_07_13_185433_create_colorables_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (48,'2025_07_13_185610_create_fonts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (49,'2025_07_13_185634_create_fontables_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (50,'2025_07_13_185723_create_visual_identities_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (51,'2025_07_15_202832_create_media_outlets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (52,'2025_07_15_203332_create_media_contacts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (53,'2025_07_15_203542_create_news_articles_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (54,'2025_07_16_081052_create_price_units_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (55,'2025_07_16_081358_create_electricity_prices_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (56,'2025_07_16_081513_create_energy_companies_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (57,'2025_07_17_123836_create_data_sources_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (58,'2025_07_17_124013_create_currencies_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (59,'2025_07_17_125504_create_user_generated_contents_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (60,'2025_07_17_141525_create_scraping_sources_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (61,'2025_07_17_141648_create_stats_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (62,'2025_07_17_141754_create_api_keys_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (63,'2025_07_17_141905_create_sync_logs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (64,'2025_07_17_191546_create_notification_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (65,'2025_07_17_191710_create_social_accounts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (66,'2025_07_17_191902_create_tag_groups_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (67,'2025_07_18_211508_create_user_devices_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (68,'2025_07_19_074017_create_relationship_types_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (69,'2025_07_19_075254_create_company_types_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (70,'2025_07_19_075455_create_electricity_offers_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (71,'2025_07_19_075712_create_exchange_rates_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (72,'2025_07_19_080945_create_weather_and_solar_data_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (73,'2025_07_19_081153_create_energy_installations_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (74,'2025_07_19_081424_create_energy_transactions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (75,'2025_07_19_090545_create_cooperatives_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (76,'2025_07_20_165551_create_cooperative_user_members_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (77,'2025_07_20_171040_create_electricity_price_intervals_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (78,'2025_07_20_205716_create_plant_species_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (79,'2025_07_20_205724_create_carbon_equivalences_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (80,'2025_07_20_205818_create_carbon_saving_logs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (81,'2025_07_20_210322_create_emission_factors_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (82,'2025_07_20_210446_create_zone_climates_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (83,'2025_07_20_210545_create_energy_certificates_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (84,'2025_08_04_104039_create_imageables_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (85,'2025_08_04_104649_create_taggables_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (86,'2025_08_11_101057_add_municipality_id_to_calendar_holidays_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (87,'2025_08_12_000001_make_venue_fields_nullable',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (88,'2025_08_12_131500_make_location_id_nullable_in_festivals_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (89,'2025_08_12_140000_create_artist_event_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (90,'2025_08_17_184534_add_cooperativa_to_energy_companies_company_type_enum',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (91,'2025_08_17_201112_create_carbon_calculations_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (92,'2025_08_17_201323_add_sustainability_fields_to_carbon_equivalences_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (93,'2025_08_17_204622_create_complete_plant_species_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (94,'2025_08_17_210112_add_optimization_fields_to_weather_and_solar_data_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (95,'2025_08_17_212931_add_media_fields_to_news_articles_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (96,'2025_08_17_213258_add_media_fields_to_media_outlets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (97,'2025_08_17_213904_add_media_fields_to_media_contacts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (98,'2025_08_18_123453_create_achievements_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (99,'2025_08_18_123504_create_challenges_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (100,'2025_08_18_123511_create_user_achievements_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (101,'2025_08_18_123519_create_user_challenges_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (102,'2025_08_18_124953_create_platforms_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (103,'2025_08_18_125044_create_categories_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (104,'2025_08_18_125148_create_person_professions_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (105,'2025_08_18_125245_create_person_works_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (106,'2025_08_18_125409_create_carbon_saving_requests_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (107,'2025_08_18_134021_create_person_profession_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (108,'2025_08_18_134100_create_person_work_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (109,'2025_01_15_100000_create_topics_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (110,'2025_01_15_100100_create_topic_memberships_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (111,'2025_01_15_100200_create_topic_posts_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (112,'2025_01_15_100300_create_topic_comments_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (113,'2025_01_15_100400_create_topic_following_table',9);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (114,'2025_01_15_110000_create_user_reputations_table',10);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (115,'2025_01_15_110100_create_reputation_transactions_table',11);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (116,'2025_01_15_110200_create_content_votes_table',12);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (117,'2025_01_15_120000_create_user_bookmarks_table',13);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (118,'2025_01_15_120100_create_hashtags_table',14);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (119,'2025_01_15_120200_create_content_hashtags_table',15);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (120,'2025_01_15_120300_create_user_lists_table',16);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (121,'2025_01_15_120400_create_list_items_table',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (122,'2025_01_15_120500_create_sponsored_content_table',18);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (123,'2025_01_15_120600_create_user_endorsements_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (124,'2025_01_15_120700_create_user_reviews_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (125,'2025_01_15_130000_create_project_proposals_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (126,'2025_01_15_130100_create_project_investments_table',22);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (127,'2025_01_15_130200_create_roof_marketplace_table',23);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (128,'2025_01_15_130300_create_production_rights_table',24);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (129,'2025_01_15_130400_create_project_updates_table',25);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (130,'2025_01_15_140000_create_activity_feeds_table',26);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (131,'2025_01_15_140100_create_social_interactions_table',27);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (132,'2025_01_15_140200_create_user_follows_table',28);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (133,'2025_08_19_184908_create_subscription_plans_table',29);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (134,'2025_08_19_184916_create_user_subscriptions_table',30);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (135,'2025_08_19_184924_create_project_commissions_table',31);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (136,'2025_08_19_184931_create_project_verifications_table',32);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (137,'2025_08_19_184940_create_consultation_services_table',33);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (138,'2025_08_19_184948_create_payments_table',34);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (139,'2025_08_19_205940_create_user_badges_table',35);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (140,'2025_08_19_210001_create_user_privileges_table',36);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (141,'2025_08_19_210002_create_expert_verifications_table',37);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (142,'2025_08_19_210013_create_topic_followings_table',38);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (143,'2025_08_19_210014_create_cooperative_posts_table',39);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (144,'2025_08_19_210015_create_social_comparisons_table',40);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (145,'2025_08_19_210015_create_leaderboards_table',41);

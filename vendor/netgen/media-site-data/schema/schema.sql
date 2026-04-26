-- MySQL dump 10.13  Distrib 8.4.7, for Linux (x86_64)
--
-- Host: localhost    Database: ngmedia
-- ------------------------------------------------------
-- Server version	8.4.7

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
-- Table structure for table `ezinfocollection`
--

DROP TABLE IF EXISTS `ezinfocollection`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ezinfocollection` (
  `contentobject_id` int NOT NULL DEFAULT '0',
  `created` int NOT NULL DEFAULT '0',
  `creator_id` int NOT NULL DEFAULT '0',
  `id` int NOT NULL AUTO_INCREMENT,
  `modified` int DEFAULT '0',
  `user_identifier` varchar(34) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ezinfocollection_co_id_created` (`contentobject_id`,`created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ezinfocollection_attribute`
--

DROP TABLE IF EXISTS `ezinfocollection_attribute`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ezinfocollection_attribute` (
  `contentclass_attribute_id` int NOT NULL DEFAULT '0',
  `contentobject_attribute_id` int DEFAULT NULL,
  `contentobject_id` int DEFAULT NULL,
  `data_float` double DEFAULT NULL,
  `data_int` int DEFAULT NULL,
  `data_text` longtext,
  `id` int NOT NULL AUTO_INCREMENT,
  `informationcollection_id` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ezinfocollection_attr_cca_id` (`contentclass_attribute_id`),
  KEY `ezinfocollection_attr_co_id` (`contentobject_id`),
  KEY `ezinfocollection_attr_coa_id` (`contentobject_attribute_id`),
  KEY `ezinfocollection_attr_ic_id` (`informationcollection_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `eztags`
--

DROP TABLE IF EXISTS `eztags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `eztags` (
  `id` int NOT NULL AUTO_INCREMENT,
  `parent_id` int NOT NULL DEFAULT '0',
  `main_tag_id` int NOT NULL DEFAULT '0',
  `keyword` varchar(255) NOT NULL DEFAULT '',
  `depth` int NOT NULL DEFAULT '1',
  `path_string` varchar(255) NOT NULL DEFAULT '',
  `modified` int NOT NULL DEFAULT '0',
  `remote_id` varchar(100) NOT NULL DEFAULT '',
  `main_language_id` bigint NOT NULL DEFAULT '0',
  `language_mask` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_eztags_remote_id` (`remote_id`),
  KEY `idx_eztags_keyword` (`keyword`(191)),
  KEY `idx_eztags_keyword_id` (`keyword`(191),`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `eztags_attribute_link`
--

DROP TABLE IF EXISTS `eztags_attribute_link`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `eztags_attribute_link` (
  `id` int NOT NULL AUTO_INCREMENT,
  `keyword_id` int NOT NULL DEFAULT '0',
  `objectattribute_id` int NOT NULL DEFAULT '0',
  `objectattribute_version` int NOT NULL DEFAULT '0',
  `object_id` int NOT NULL DEFAULT '0',
  `priority` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_eztags_attr_link_keyword_id` (`keyword_id`),
  KEY `idx_eztags_attr_link_kid_oaid_oav` (`keyword_id`,`objectattribute_id`,`objectattribute_version`),
  KEY `idx_eztags_attr_link_kid_oid` (`keyword_id`,`object_id`),
  KEY `idx_eztags_attr_link_oaid_oav` (`objectattribute_id`,`objectattribute_version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `eztags_keyword`
--

DROP TABLE IF EXISTS `eztags_keyword`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `eztags_keyword` (
  `keyword_id` int NOT NULL DEFAULT '0',
  `language_id` bigint NOT NULL DEFAULT '0',
  `keyword` varchar(255) NOT NULL DEFAULT '',
  `locale` varchar(20) NOT NULL DEFAULT '',
  `status` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`keyword_id`,`locale`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_binary_file`
--

DROP TABLE IF EXISTS `ibexa_binary_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_binary_file` (
  `contentobject_attribute_id` int NOT NULL DEFAULT '0',
  `version` int NOT NULL DEFAULT '0',
  `download_count` int NOT NULL DEFAULT '0',
  `filename` varchar(255) NOT NULL DEFAULT '',
  `mime_type` varchar(255) NOT NULL DEFAULT '',
  `original_filename` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`contentobject_attribute_id`,`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_content`
--

DROP TABLE IF EXISTS `ibexa_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_content` (
  `id` int NOT NULL AUTO_INCREMENT,
  `content_type_id` int NOT NULL DEFAULT '0',
  `current_version` int DEFAULT NULL,
  `initial_language_id` bigint NOT NULL DEFAULT '0',
  `language_mask` bigint NOT NULL DEFAULT '0',
  `modified` int NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `owner_id` int NOT NULL DEFAULT '0',
  `published` int NOT NULL DEFAULT '0',
  `remote_id` varchar(100) DEFAULT NULL,
  `section_id` int NOT NULL DEFAULT '0',
  `status` int DEFAULT '0',
  `is_hidden` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ibexa_content_remote_id` (`remote_id`),
  KEY `ibexa_content_type_id` (`content_type_id`),
  KEY `ibexa_content_lmask` (`language_mask`),
  KEY `ibexa_content_pub` (`published`),
  KEY `ibexa_content_section` (`section_id`),
  KEY `ibexa_content_currentversion` (`current_version`),
  KEY `ibexa_content_owner` (`owner_id`),
  KEY `ibexa_content_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_content_bookmark`
--

DROP TABLE IF EXISTS `ibexa_content_bookmark`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_content_bookmark` (
  `id` int NOT NULL AUTO_INCREMENT,
  `node_id` int NOT NULL DEFAULT '0',
  `user_id` int NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `ibexa_content_bookmark_location` (`node_id`),
  KEY `ibexa_content_bookmark_user` (`user_id`),
  KEY `ibexa_content_bookmark_user_location` (`user_id`,`node_id`),
  CONSTRAINT `ibexa_content_bookmark_location_fk` FOREIGN KEY (`node_id`) REFERENCES `ibexa_content_tree` (`node_id`) ON DELETE CASCADE,
  CONSTRAINT `ibexa_content_bookmark_user_fk` FOREIGN KEY (`user_id`) REFERENCES `ibexa_user` (`contentobject_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_content_field`
--

DROP TABLE IF EXISTS `ibexa_content_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_content_field` (
  `id` int NOT NULL AUTO_INCREMENT,
  `version` int NOT NULL DEFAULT '0',
  `attribute_original_id` int DEFAULT '0',
  `content_type_field_definition_id` int NOT NULL DEFAULT '0',
  `contentobject_id` int NOT NULL DEFAULT '0',
  `data_float` double DEFAULT NULL,
  `data_int` int DEFAULT NULL,
  `data_text` longtext,
  `data_type_string` varchar(50) DEFAULT '',
  `language_code` varchar(20) NOT NULL DEFAULT '',
  `language_id` bigint NOT NULL DEFAULT '0',
  `sort_key_int` int NOT NULL DEFAULT '0',
  `sort_key_string` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`,`version`),
  KEY `ibexa_content_field_co_id_ver_lang_code` (`contentobject_id`,`version`,`language_code`),
  KEY `ibexa_content_field_field_definition_id` (`content_type_field_definition_id`),
  KEY `sort_key_string` (`sort_key_string`(191)),
  KEY `ibexa_content_field_language_code` (`language_code`),
  KEY `sort_key_int` (`sort_key_int`),
  KEY `ibexa_content_field_co_id_ver` (`contentobject_id`,`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_content_language`
--

DROP TABLE IF EXISTS `ibexa_content_language`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_content_language` (
  `id` bigint NOT NULL DEFAULT '0',
  `disabled` int NOT NULL DEFAULT '0',
  `locale` varchar(20) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `ibexa_content_language_name` (`name`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_content_name`
--

DROP TABLE IF EXISTS `ibexa_content_name`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_content_name` (
  `contentobject_id` int NOT NULL DEFAULT '0',
  `content_version` int NOT NULL DEFAULT '0',
  `content_translation` varchar(20) NOT NULL DEFAULT '',
  `language_id` bigint NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `real_translation` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`contentobject_id`,`content_version`,`content_translation`),
  KEY `ibexa_content_name_lang_id` (`language_id`),
  KEY `ibexa_content_name_cov_id` (`content_version`),
  KEY `ibexa_content_name_name` (`name`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_content_relation`
--

DROP TABLE IF EXISTS `ibexa_content_relation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_content_relation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `content_type_field_definition_id` int NOT NULL DEFAULT '0',
  `from_contentobject_id` int NOT NULL DEFAULT '0',
  `from_contentobject_version` int NOT NULL DEFAULT '0',
  `relation_type` int NOT NULL DEFAULT '1',
  `to_contentobject_id` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ibexa_content_relation_to_co_id` (`to_contentobject_id`),
  KEY `ibexa_content_relation_from` (`from_contentobject_id`,`from_contentobject_version`,`content_type_field_definition_id`),
  KEY `ibexa_content_relation_ccfd_id` (`content_type_field_definition_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_content_trash`
--

DROP TABLE IF EXISTS `ibexa_content_trash`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_content_trash` (
  `node_id` int NOT NULL DEFAULT '0',
  `contentobject_id` int DEFAULT NULL,
  `contentobject_version` int DEFAULT NULL,
  `depth` int NOT NULL DEFAULT '0',
  `is_hidden` int NOT NULL DEFAULT '0',
  `is_invisible` int NOT NULL DEFAULT '0',
  `main_node_id` int DEFAULT NULL,
  `modified_subnode` int DEFAULT '0',
  `parent_node_id` int NOT NULL DEFAULT '0',
  `path_identification_string` longtext,
  `path_string` varchar(255) NOT NULL DEFAULT '',
  `priority` int NOT NULL DEFAULT '0',
  `remote_id` varchar(100) NOT NULL DEFAULT '',
  `sort_field` int DEFAULT '1',
  `sort_order` int DEFAULT '1',
  `trashed` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`node_id`),
  KEY `ibexa_content_trash_depth` (`depth`),
  KEY `ibexa_content_trash_p_node_id` (`parent_node_id`),
  KEY `ibexa_content_trash_path_ident` (`path_identification_string`(50)),
  KEY `ibexa_content_trash_co_id` (`contentobject_id`),
  KEY `ibexa_content_trash_modified_subnode` (`modified_subnode`),
  KEY `ibexa_content_trash_path` (`path_string`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_content_tree`
--

DROP TABLE IF EXISTS `ibexa_content_tree`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_content_tree` (
  `node_id` int NOT NULL AUTO_INCREMENT,
  `contentobject_id` int DEFAULT NULL,
  `contentobject_is_published` int DEFAULT NULL,
  `contentobject_version` int DEFAULT NULL,
  `depth` int NOT NULL DEFAULT '0',
  `is_hidden` int NOT NULL DEFAULT '0',
  `is_invisible` int NOT NULL DEFAULT '0',
  `main_node_id` int DEFAULT NULL,
  `modified_subnode` int DEFAULT '0',
  `parent_node_id` int NOT NULL DEFAULT '0',
  `path_identification_string` longtext,
  `path_string` varchar(255) NOT NULL DEFAULT '',
  `priority` int NOT NULL DEFAULT '0',
  `remote_id` varchar(100) NOT NULL DEFAULT '',
  `sort_field` int DEFAULT '1',
  `sort_order` int DEFAULT '1',
  PRIMARY KEY (`node_id`),
  KEY `ibexa_content_tree_p_node_id` (`parent_node_id`),
  KEY `ibexa_content_tree_path_ident` (`path_identification_string`(50)),
  KEY `ibexa_content_tree_contentobject_id_path_string` (`path_string`(191),`contentobject_id`),
  KEY `ibexa_content_tree_co_id` (`contentobject_id`),
  KEY `ibexa_content_tree_depth` (`depth`),
  KEY `ibexa_content_tree_path` (`path_string`(191)),
  KEY `ibexa_content_modified_subnode` (`modified_subnode`),
  KEY `ibexa_content_tree_remote_id` (`remote_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_content_type`
--

DROP TABLE IF EXISTS `ibexa_content_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_content_type` (
  `id` int NOT NULL AUTO_INCREMENT,
  `status` int NOT NULL DEFAULT '0',
  `always_available` int NOT NULL DEFAULT '0',
  `contentobject_name` varchar(255) DEFAULT NULL,
  `created` int NOT NULL DEFAULT '0',
  `creator_id` int NOT NULL DEFAULT '0',
  `identifier` varchar(50) NOT NULL DEFAULT '',
  `initial_language_id` bigint NOT NULL DEFAULT '0',
  `is_container` int NOT NULL DEFAULT '0',
  `language_mask` bigint NOT NULL DEFAULT '0',
  `modified` int NOT NULL DEFAULT '0',
  `modifier_id` int NOT NULL DEFAULT '0',
  `remote_id` varchar(100) NOT NULL DEFAULT '',
  `serialized_description_list` longtext,
  `serialized_name_list` longtext,
  `sort_field` int NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '1',
  `url_alias_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`,`status`),
  KEY `ibexa_content_type_status` (`status`),
  KEY `ibexa_content_type_identifier` (`identifier`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_content_type_field_definition`
--

DROP TABLE IF EXISTS `ibexa_content_type_field_definition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_content_type_field_definition` (
  `id` int NOT NULL AUTO_INCREMENT,
  `status` int NOT NULL DEFAULT '0',
  `can_translate` int DEFAULT '1',
  `category` varchar(25) NOT NULL DEFAULT '',
  `content_type_id` int NOT NULL DEFAULT '0',
  `data_float1` double DEFAULT NULL,
  `data_float2` double DEFAULT NULL,
  `data_float3` double DEFAULT NULL,
  `data_float4` double DEFAULT NULL,
  `data_int1` int DEFAULT NULL,
  `data_int2` int DEFAULT NULL,
  `data_int3` int DEFAULT NULL,
  `data_int4` int DEFAULT NULL,
  `data_text1` varchar(255) DEFAULT NULL,
  `data_text2` varchar(50) DEFAULT NULL,
  `data_text3` varchar(50) DEFAULT NULL,
  `data_text4` varchar(255) DEFAULT NULL,
  `data_text5` longtext,
  `data_type_string` varchar(50) NOT NULL DEFAULT '',
  `identifier` varchar(50) NOT NULL DEFAULT '',
  `is_information_collector` int NOT NULL DEFAULT '0',
  `is_required` int NOT NULL DEFAULT '0',
  `is_searchable` int NOT NULL DEFAULT '0',
  `is_thumbnail` tinyint(1) NOT NULL DEFAULT '0',
  `placement` int NOT NULL DEFAULT '0',
  `serialized_data_text` longtext,
  `serialized_description_list` longtext,
  `serialized_name_list` longtext NOT NULL,
  PRIMARY KEY (`id`,`status`),
  KEY `ibexa_content_type_field_definition_ctid` (`content_type_id`),
  KEY `ibexa_content_type_field_definition_dts` (`data_type_string`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_content_type_field_definition_ml`
--

DROP TABLE IF EXISTS `ibexa_content_type_field_definition_ml`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_content_type_field_definition_ml` (
  `content_type_field_definition_id` int NOT NULL,
  `status` int NOT NULL,
  `language_id` bigint NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `data_text` text,
  `data_json` text,
  PRIMARY KEY (`content_type_field_definition_id`,`status`,`language_id`),
  KEY `ibexa_content_type_field_definition_ml_lang_fk` (`language_id`),
  CONSTRAINT `ibexa_content_type_field_definition_ml_lang_fk` FOREIGN KEY (`language_id`) REFERENCES `ibexa_content_language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_content_type_group`
--

DROP TABLE IF EXISTS `ibexa_content_type_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_content_type_group` (
  `id` int NOT NULL AUTO_INCREMENT,
  `created` int NOT NULL DEFAULT '0',
  `creator_id` int NOT NULL DEFAULT '0',
  `modified` int NOT NULL DEFAULT '0',
  `modifier_id` int NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_content_type_group_assignment`
--

DROP TABLE IF EXISTS `ibexa_content_type_group_assignment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_content_type_group_assignment` (
  `content_type_id` int NOT NULL DEFAULT '0',
  `content_type_status` int NOT NULL DEFAULT '0',
  `group_id` int NOT NULL DEFAULT '0',
  `group_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`content_type_id`,`content_type_status`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_content_type_name`
--

DROP TABLE IF EXISTS `ibexa_content_type_name`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_content_type_name` (
  `content_type_id` int NOT NULL DEFAULT '0',
  `content_type_status` int NOT NULL DEFAULT '0',
  `language_id` bigint NOT NULL DEFAULT '0',
  `language_locale` varchar(20) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`content_type_id`,`content_type_status`,`language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_content_version`
--

DROP TABLE IF EXISTS `ibexa_content_version`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_content_version` (
  `id` int NOT NULL AUTO_INCREMENT,
  `contentobject_id` int DEFAULT NULL,
  `created` int NOT NULL DEFAULT '0',
  `creator_id` int NOT NULL DEFAULT '0',
  `initial_language_id` bigint NOT NULL DEFAULT '0',
  `language_mask` bigint NOT NULL DEFAULT '0',
  `modified` int NOT NULL DEFAULT '0',
  `status` int NOT NULL DEFAULT '0',
  `user_id` int NOT NULL DEFAULT '0',
  `version` int NOT NULL DEFAULT '0',
  `workflow_event_pos` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ibexa_content_version_status` (`status`),
  KEY `ibexa_content_version_idx_ver` (`contentobject_id`,`version`),
  KEY `ibexa_content_version_idx_status` (`contentobject_id`,`status`),
  KEY `ibexa_content_version_creator_id` (`creator_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_dfs_file`
--

DROP TABLE IF EXISTS `ibexa_dfs_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_dfs_file` (
  `name_hash` varchar(34) NOT NULL DEFAULT '',
  `name` text NOT NULL,
  `name_trunk` text NOT NULL,
  `datatype` varchar(255) NOT NULL DEFAULT 'application/octet-stream',
  `scope` varchar(25) NOT NULL DEFAULT '',
  `size` bigint unsigned NOT NULL DEFAULT '0',
  `mtime` int NOT NULL DEFAULT '0',
  `expired` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`name_hash`),
  KEY `ibexa_dfs_file_name_trunk` (`name_trunk`(191)),
  KEY `ibexa_dfs_file_expired_name` (`expired`,`name`(191)),
  KEY `ibexa_dfs_file_name` (`name`(191)),
  KEY `ibexa_dfs_file_mtime` (`mtime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_image_file`
--

DROP TABLE IF EXISTS `ibexa_image_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_image_file` (
  `id` int NOT NULL AUTO_INCREMENT,
  `contentobject_attribute_id` int NOT NULL DEFAULT '0',
  `filepath` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ibexa_image_file_file` (`filepath`(191)),
  KEY `ibexa_image_file_coid` (`contentobject_attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_keyword`
--

DROP TABLE IF EXISTS `ibexa_keyword`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_keyword` (
  `id` int NOT NULL AUTO_INCREMENT,
  `class_id` int NOT NULL DEFAULT '0',
  `keyword` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ibexa_keyword_keyword` (`keyword`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_keyword_field_link`
--

DROP TABLE IF EXISTS `ibexa_keyword_field_link`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_keyword_field_link` (
  `id` int NOT NULL AUTO_INCREMENT,
  `keyword_id` int NOT NULL DEFAULT '0',
  `objectattribute_id` int NOT NULL DEFAULT '0',
  `version` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ibexa_keyword_field_link_oaid` (`objectattribute_id`),
  KEY `ibexa_keyword_field_link_kid_oaid` (`keyword_id`,`objectattribute_id`),
  KEY `ibexa_keyword_field_link_oaid_ver` (`objectattribute_id`,`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_map_location`
--

DROP TABLE IF EXISTS `ibexa_map_location`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_map_location` (
  `contentobject_attribute_id` int NOT NULL DEFAULT '0',
  `contentobject_version` int NOT NULL DEFAULT '0',
  `latitude` double NOT NULL DEFAULT '0',
  `longitude` double NOT NULL DEFAULT '0',
  `address` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`contentobject_attribute_id`,`contentobject_version`),
  KEY `ibexa_map_location_latitude_longitude_key` (`latitude`,`longitude`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_media`
--

DROP TABLE IF EXISTS `ibexa_media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_media` (
  `contentobject_attribute_id` int NOT NULL DEFAULT '0',
  `version` int NOT NULL DEFAULT '0',
  `controls` varchar(50) DEFAULT NULL,
  `filename` varchar(255) NOT NULL DEFAULT '',
  `has_controller` int DEFAULT '0',
  `height` int DEFAULT NULL,
  `is_autoplay` int DEFAULT '0',
  `is_loop` int DEFAULT '0',
  `mime_type` varchar(50) NOT NULL DEFAULT '',
  `original_filename` varchar(255) NOT NULL DEFAULT '',
  `pluginspage` varchar(255) DEFAULT NULL,
  `quality` varchar(50) DEFAULT NULL,
  `width` int DEFAULT NULL,
  PRIMARY KEY (`contentobject_attribute_id`,`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_node_assignment`
--

DROP TABLE IF EXISTS `ibexa_node_assignment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_node_assignment` (
  `id` int NOT NULL AUTO_INCREMENT,
  `contentobject_id` int DEFAULT NULL,
  `contentobject_version` int DEFAULT NULL,
  `from_node_id` int DEFAULT '0',
  `is_main` int NOT NULL DEFAULT '0',
  `op_code` int NOT NULL DEFAULT '0',
  `parent_node` int DEFAULT NULL,
  `parent_remote_id` varchar(100) NOT NULL DEFAULT '',
  `remote_id` varchar(100) NOT NULL DEFAULT '0',
  `sort_field` int DEFAULT '1',
  `sort_order` int DEFAULT '1',
  `priority` int NOT NULL DEFAULT '0',
  `is_hidden` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ibexa_node_assignment_is_main` (`is_main`),
  KEY `ibexa_node_assignment_coid_cov` (`contentobject_id`,`contentobject_version`),
  KEY `ibexa_node_assignment_parent_node` (`parent_node`),
  KEY `ibexa_node_assignment_co_version` (`contentobject_version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_notification`
--

DROP TABLE IF EXISTS `ibexa_notification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_notification` (
  `id` int NOT NULL AUTO_INCREMENT,
  `owner_id` int NOT NULL DEFAULT '0',
  `is_pending` tinyint(1) NOT NULL DEFAULT '1',
  `type` varchar(128) NOT NULL DEFAULT '',
  `created` int NOT NULL DEFAULT '0',
  `data` longtext,
  PRIMARY KEY (`id`),
  KEY `ibexa_notification_owner_is_pending` (`owner_id`,`is_pending`),
  KEY `ibexa_notification_owner` (`owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_object_state`
--

DROP TABLE IF EXISTS `ibexa_object_state`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_object_state` (
  `id` int NOT NULL AUTO_INCREMENT,
  `default_language_id` bigint NOT NULL DEFAULT '0',
  `group_id` int NOT NULL DEFAULT '0',
  `identifier` varchar(45) NOT NULL DEFAULT '',
  `language_mask` bigint NOT NULL DEFAULT '0',
  `priority` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ibexa_object_state_identifier` (`group_id`,`identifier`),
  KEY `ibexa_object_state_priority` (`priority`),
  KEY `ibexa_object_state_lmask` (`language_mask`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_object_state_group`
--

DROP TABLE IF EXISTS `ibexa_object_state_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_object_state_group` (
  `id` int NOT NULL AUTO_INCREMENT,
  `default_language_id` bigint NOT NULL DEFAULT '0',
  `identifier` varchar(45) NOT NULL DEFAULT '',
  `language_mask` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ibexa_object_state_group_identifier` (`identifier`),
  KEY `ibexa_object_state_group_lmask` (`language_mask`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_object_state_group_language`
--

DROP TABLE IF EXISTS `ibexa_object_state_group_language`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_object_state_group_language` (
  `contentobject_state_group_id` int NOT NULL DEFAULT '0',
  `real_language_id` bigint NOT NULL DEFAULT '0',
  `description` longtext NOT NULL,
  `language_id` bigint NOT NULL DEFAULT '0',
  `name` varchar(45) NOT NULL DEFAULT '',
  PRIMARY KEY (`contentobject_state_group_id`,`real_language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_object_state_language`
--

DROP TABLE IF EXISTS `ibexa_object_state_language`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_object_state_language` (
  `contentobject_state_id` int NOT NULL DEFAULT '0',
  `language_id` bigint NOT NULL DEFAULT '0',
  `description` longtext NOT NULL,
  `name` varchar(45) NOT NULL DEFAULT '',
  PRIMARY KEY (`contentobject_state_id`,`language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_object_state_link`
--

DROP TABLE IF EXISTS `ibexa_object_state_link`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_object_state_link` (
  `contentobject_id` int NOT NULL DEFAULT '0',
  `contentobject_state_id` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`contentobject_id`,`contentobject_state_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_package`
--

DROP TABLE IF EXISTS `ibexa_package`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_package` (
  `id` int NOT NULL AUTO_INCREMENT,
  `install_date` int NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL DEFAULT '',
  `version` varchar(30) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_policy`
--

DROP TABLE IF EXISTS `ibexa_policy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_policy` (
  `id` int NOT NULL AUTO_INCREMENT,
  `function_name` varchar(255) DEFAULT NULL,
  `module_name` varchar(255) DEFAULT NULL,
  `original_id` int NOT NULL DEFAULT '0',
  `role_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ibexa_policy_role_id` (`role_id`),
  KEY `ibexa_policy_original_id` (`original_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_policy_limitation`
--

DROP TABLE IF EXISTS `ibexa_policy_limitation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_policy_limitation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `identifier` varchar(255) NOT NULL DEFAULT '',
  `policy_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ibexa_policy_id` (`policy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_policy_limitation_value`
--

DROP TABLE IF EXISTS `ibexa_policy_limitation_value`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_policy_limitation_value` (
  `id` int NOT NULL AUTO_INCREMENT,
  `limitation_id` int DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ibexa_policy_limit_value_limit_id` (`limitation_id`),
  KEY `ibexa_policy_limitation_value_val` (`value`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_role`
--

DROP TABLE IF EXISTS `ibexa_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_role` (
  `id` int NOT NULL AUTO_INCREMENT,
  `is_new` int NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `value` char(1) DEFAULT NULL,
  `version` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_search_object_word_link`
--

DROP TABLE IF EXISTS `ibexa_search_object_word_link`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_search_object_word_link` (
  `id` int NOT NULL AUTO_INCREMENT,
  `content_type_field_definition_id` int NOT NULL DEFAULT '0',
  `content_type_id` int NOT NULL DEFAULT '0',
  `contentobject_id` int NOT NULL DEFAULT '0',
  `frequency` double NOT NULL DEFAULT '0',
  `identifier` varchar(255) NOT NULL DEFAULT '',
  `integer_value` int NOT NULL DEFAULT '0',
  `next_word_id` int NOT NULL DEFAULT '0',
  `placement` int NOT NULL DEFAULT '0',
  `prev_word_id` int NOT NULL DEFAULT '0',
  `published` int NOT NULL DEFAULT '0',
  `section_id` int NOT NULL DEFAULT '0',
  `word_id` int NOT NULL DEFAULT '0',
  `language_mask` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ibexa_search_object_word_link_object` (`contentobject_id`),
  KEY `ibexa_search_object_word_link_identifier` (`identifier`(191)),
  KEY `ibexa_search_object_word_link_integer_value` (`integer_value`),
  KEY `ibexa_search_object_word_link_word` (`word_id`),
  KEY `ibexa_search_object_word_link_frequency` (`frequency`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_search_word`
--

DROP TABLE IF EXISTS `ibexa_search_word`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_search_word` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_count` int NOT NULL DEFAULT '0',
  `word` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ibexa_search_word_word_i` (`word`),
  KEY `ibexa_search_word_obj_count` (`object_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_section`
--

DROP TABLE IF EXISTS `ibexa_section`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_section` (
  `id` int NOT NULL AUTO_INCREMENT,
  `identifier` varchar(255) DEFAULT NULL,
  `locale` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `navigation_part_identifier` varchar(100) DEFAULT 'ezcontentnavigationpart',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_setting`
--

DROP TABLE IF EXISTS `ibexa_setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_setting` (
  `id` int NOT NULL AUTO_INCREMENT,
  `group` varchar(128) NOT NULL,
  `identifier` varchar(128) NOT NULL,
  `value` json NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ibexa_setting_group_identifier` (`group`,`identifier`),
  KEY `ibexa_setting_id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_site_data`
--

DROP TABLE IF EXISTS `ibexa_site_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_site_data` (
  `name` varchar(60) NOT NULL DEFAULT '',
  `value` longtext NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_token`
--

DROP TABLE IF EXISTS `ibexa_token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_token` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type_id` int NOT NULL,
  `token` varchar(255) NOT NULL,
  `identifier` varchar(128) DEFAULT NULL,
  `created` int NOT NULL DEFAULT '0',
  `expires` int NOT NULL DEFAULT '0',
  `revoked` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ibexa_token_unique` (`token`,`identifier`,`type_id`),
  KEY `IDX_B5412887C54C8C93` (`type_id`),
  CONSTRAINT `ibexa_token_type_id_fk` FOREIGN KEY (`type_id`) REFERENCES `ibexa_token_type` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_token_type`
--

DROP TABLE IF EXISTS `ibexa_token_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_token_type` (
  `id` int NOT NULL AUTO_INCREMENT,
  `identifier` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ibexa_token_type_unique` (`identifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_url`
--

DROP TABLE IF EXISTS `ibexa_url`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_url` (
  `id` int NOT NULL AUTO_INCREMENT,
  `created` int NOT NULL DEFAULT '0',
  `is_valid` int NOT NULL DEFAULT '1',
  `last_checked` int NOT NULL DEFAULT '0',
  `modified` int NOT NULL DEFAULT '0',
  `original_url_md5` varchar(32) NOT NULL DEFAULT '',
  `url` longtext,
  PRIMARY KEY (`id`),
  KEY `ibexa_url_url` (`url`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_url_alias`
--

DROP TABLE IF EXISTS `ibexa_url_alias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_url_alias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `destination_url` longtext NOT NULL,
  `forward_to_id` int NOT NULL DEFAULT '0',
  `is_imported` int NOT NULL DEFAULT '0',
  `is_internal` int NOT NULL DEFAULT '1',
  `is_wildcard` int NOT NULL DEFAULT '0',
  `source_md5` varchar(32) DEFAULT NULL,
  `source_url` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ibexa_url_alias_source_md5` (`source_md5`),
  KEY `ibexa_url_alias_wcard_fwd` (`is_wildcard`,`forward_to_id`),
  KEY `ibexa_url_alias_forward_to_id` (`forward_to_id`),
  KEY `ibexa_url_alias_imp_wcard_fwd` (`is_imported`,`is_wildcard`,`forward_to_id`),
  KEY `ibexa_url_alias_source_url` (`source_url`(191)),
  KEY `ibexa_url_alias_desturl` (`destination_url`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_url_alias_ml`
--

DROP TABLE IF EXISTS `ibexa_url_alias_ml`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_url_alias_ml` (
  `parent` int NOT NULL DEFAULT '0',
  `text_md5` varchar(32) NOT NULL DEFAULT '',
  `action` longtext NOT NULL,
  `action_type` varchar(32) NOT NULL DEFAULT '',
  `alias_redirects` int NOT NULL DEFAULT '1',
  `id` int NOT NULL DEFAULT '0',
  `is_alias` int NOT NULL DEFAULT '0',
  `is_original` int NOT NULL DEFAULT '0',
  `lang_mask` bigint NOT NULL DEFAULT '0',
  `link` int NOT NULL DEFAULT '0',
  `text` longtext NOT NULL,
  PRIMARY KEY (`parent`,`text_md5`),
  KEY `ibexa_url_alias_ml_actt_org_al` (`action_type`,`is_original`,`is_alias`),
  KEY `ibexa_url_alias_ml_text_lang` (`text`(32),`lang_mask`,`parent`),
  KEY `ibexa_url_alias_ml_par_act_id_lnk` (`action`(32),`id`,`link`,`parent`),
  KEY `ibexa_url_alias_ml_par_lnk_txt` (`parent`,`text`(32),`link`),
  KEY `ibexa_url_alias_ml_act_org` (`action`(32),`is_original`),
  KEY `ibexa_url_alias_ml_text` (`text`(32),`id`,`link`),
  KEY `ibexa_url_alias_ml_link` (`link`),
  KEY `ibexa_url_alias_ml_id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_url_alias_ml_incr`
--

DROP TABLE IF EXISTS `ibexa_url_alias_ml_incr`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_url_alias_ml_incr` (
  `id` int NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_url_content_link`
--

DROP TABLE IF EXISTS `ibexa_url_content_link`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_url_content_link` (
  `contentobject_attribute_id` int NOT NULL DEFAULT '0',
  `contentobject_attribute_version` int NOT NULL DEFAULT '0',
  `url_id` int NOT NULL DEFAULT '0',
  KEY `ibexa_url_ol_coa_id` (`contentobject_attribute_id`),
  KEY `ibexa_url_ol_url_id` (`url_id`),
  KEY `ibexa_url_ol_coa_version` (`contentobject_attribute_version`),
  KEY `ibexa_url_ol_coa_id_cav` (`contentobject_attribute_id`,`contentobject_attribute_version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_url_wildcard`
--

DROP TABLE IF EXISTS `ibexa_url_wildcard`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_url_wildcard` (
  `id` int NOT NULL AUTO_INCREMENT,
  `destination_url` longtext NOT NULL,
  `source_url` longtext NOT NULL,
  `type` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_user`
--

DROP TABLE IF EXISTS `ibexa_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_user` (
  `contentobject_id` int NOT NULL DEFAULT '0',
  `email` varchar(150) NOT NULL DEFAULT '',
  `login` varchar(150) NOT NULL DEFAULT '',
  `password_hash` varchar(255) DEFAULT NULL,
  `password_hash_type` int NOT NULL DEFAULT '1',
  `password_updated_at` int DEFAULT NULL,
  PRIMARY KEY (`contentobject_id`),
  UNIQUE KEY `ibexa_user_login` (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_user_accountkey`
--

DROP TABLE IF EXISTS `ibexa_user_accountkey`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_user_accountkey` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_key` varchar(32) NOT NULL DEFAULT '',
  `time` int NOT NULL DEFAULT '0',
  `user_id` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `hash_key` (`hash_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_user_invitation`
--

DROP TABLE IF EXISTS `ibexa_user_invitation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_user_invitation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `site_access_name` varchar(255) NOT NULL,
  `hash` varchar(255) NOT NULL,
  `creation_date` int NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ibexa_user_invitation_email_uindex` (`email`(191)),
  UNIQUE KEY `ibexa_user_invitation_hash_uindex` (`hash`(191)),
  KEY `ibexa_user_invitation_email_idx` (`email`),
  KEY `ibexa_user_invitation_hash_idx` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_user_invitation_assignment`
--

DROP TABLE IF EXISTS `ibexa_user_invitation_assignment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_user_invitation_assignment` (
  `id` int NOT NULL AUTO_INCREMENT,
  `invitation_id` int NOT NULL,
  `user_group_id` int DEFAULT NULL,
  `role_id` int DEFAULT NULL,
  `limitation_type` varchar(255) DEFAULT NULL,
  `limitation_value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_9E1E6F70A35D7AF0` (`invitation_id`),
  CONSTRAINT `ibexa_user_invitation_assignment_ibexa_user_invitation_id_fk` FOREIGN KEY (`invitation_id`) REFERENCES `ibexa_user_invitation` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_user_preference`
--

DROP TABLE IF EXISTS `ibexa_user_preference`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_user_preference` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `user_id` int NOT NULL DEFAULT '0',
  `value` longtext,
  PRIMARY KEY (`id`),
  KEY `ibexa_user_preference_user_id_idx` (`user_id`,`name`),
  KEY `ibexa_user_preference_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_user_role`
--

DROP TABLE IF EXISTS `ibexa_user_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_user_role` (
  `id` int NOT NULL AUTO_INCREMENT,
  `contentobject_id` int DEFAULT NULL,
  `limit_identifier` varchar(255) DEFAULT '',
  `limit_value` varchar(255) DEFAULT '',
  `role_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ibexa_user_role_role_id` (`role_id`),
  KEY `ibexa_user_role_contentobject_id` (`contentobject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibexa_user_setting`
--

DROP TABLE IF EXISTS `ibexa_user_setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibexa_user_setting` (
  `user_id` int NOT NULL DEFAULT '0',
  `is_enabled` int NOT NULL DEFAULT '0',
  `max_login` int DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_block`
--

DROP TABLE IF EXISTS `nglayouts_block`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_block` (
  `id` int NOT NULL AUTO_INCREMENT,
  `status` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `layout_id` int NOT NULL,
  `depth` int NOT NULL,
  `path` varchar(191) NOT NULL,
  `parent_id` int DEFAULT NULL,
  `placeholder` varchar(191) DEFAULT NULL,
  `position` int DEFAULT NULL,
  `definition_identifier` varchar(191) NOT NULL,
  `view_type` varchar(191) NOT NULL,
  `item_view_type` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `config` longtext NOT NULL,
  `translatable` tinyint NOT NULL,
  `main_locale` varchar(191) NOT NULL,
  `always_available` tinyint NOT NULL,
  PRIMARY KEY (`id`,`status`),
  UNIQUE KEY `idx_ngl_block_uuid` (`uuid`,`status`),
  KEY `idx_ngl_layout` (`layout_id`,`status`),
  KEY `idx_ngl_parent_block` (`parent_id`,`placeholder`,`status`),
  CONSTRAINT `fk_ngl_block_layout` FOREIGN KEY (`layout_id`, `status`) REFERENCES `nglayouts_layout` (`id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_block_collection`
--

DROP TABLE IF EXISTS `nglayouts_block_collection`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_block_collection` (
  `block_id` int NOT NULL,
  `block_status` int NOT NULL,
  `identifier` varchar(191) NOT NULL,
  `collection_id` int NOT NULL,
  `collection_status` int NOT NULL,
  PRIMARY KEY (`block_id`,`block_status`,`identifier`),
  KEY `idx_ngl_block` (`block_id`,`block_status`),
  KEY `idx_ngl_collection` (`collection_id`,`collection_status`),
  CONSTRAINT `fk_ngl_block_collection_block` FOREIGN KEY (`block_id`, `block_status`) REFERENCES `nglayouts_block` (`id`, `status`),
  CONSTRAINT `fk_ngl_block_collection_collection` FOREIGN KEY (`collection_id`, `collection_status`) REFERENCES `nglayouts_collection` (`id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_block_translation`
--

DROP TABLE IF EXISTS `nglayouts_block_translation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_block_translation` (
  `block_id` int NOT NULL,
  `status` int NOT NULL,
  `locale` varchar(191) NOT NULL,
  `parameters` longtext NOT NULL,
  PRIMARY KEY (`block_id`,`status`,`locale`),
  CONSTRAINT `fk_ngl_block_translation_block` FOREIGN KEY (`block_id`, `status`) REFERENCES `nglayouts_block` (`id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_collection`
--

DROP TABLE IF EXISTS `nglayouts_collection`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_collection` (
  `id` int NOT NULL AUTO_INCREMENT,
  `status` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `start` int NOT NULL,
  `length` int DEFAULT NULL,
  `translatable` tinyint NOT NULL,
  `main_locale` varchar(191) NOT NULL,
  `always_available` tinyint NOT NULL,
  PRIMARY KEY (`id`,`status`),
  UNIQUE KEY `idx_ngl_collection_uuid` (`uuid`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_collection_item`
--

DROP TABLE IF EXISTS `nglayouts_collection_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_collection_item` (
  `id` int NOT NULL AUTO_INCREMENT,
  `status` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `collection_id` int NOT NULL,
  `position` int NOT NULL,
  `value` varchar(191) DEFAULT NULL,
  `value_type` varchar(191) NOT NULL,
  `view_type` varchar(191) DEFAULT NULL,
  `config` longtext NOT NULL,
  PRIMARY KEY (`id`,`status`),
  UNIQUE KEY `idx_ngl_collection_item_uuid` (`uuid`,`status`),
  KEY `idx_ngl_collection` (`collection_id`,`status`),
  CONSTRAINT `fk_ngl_item_collection` FOREIGN KEY (`collection_id`, `status`) REFERENCES `nglayouts_collection` (`id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_collection_query`
--

DROP TABLE IF EXISTS `nglayouts_collection_query`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_collection_query` (
  `id` int NOT NULL AUTO_INCREMENT,
  `status` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `collection_id` int NOT NULL,
  `type` varchar(191) NOT NULL,
  PRIMARY KEY (`id`,`status`),
  UNIQUE KEY `idx_ngl_collection_query_uuid` (`uuid`,`status`),
  KEY `idx_ngl_collection` (`collection_id`,`status`),
  CONSTRAINT `fk_ngl_query_collection` FOREIGN KEY (`collection_id`, `status`) REFERENCES `nglayouts_collection` (`id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_collection_query_translation`
--

DROP TABLE IF EXISTS `nglayouts_collection_query_translation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_collection_query_translation` (
  `query_id` int NOT NULL,
  `status` int NOT NULL,
  `locale` varchar(191) NOT NULL,
  `parameters` longtext NOT NULL,
  PRIMARY KEY (`query_id`,`status`,`locale`),
  CONSTRAINT `fk_ngl_query_translation_query` FOREIGN KEY (`query_id`, `status`) REFERENCES `nglayouts_collection_query` (`id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_collection_slot`
--

DROP TABLE IF EXISTS `nglayouts_collection_slot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_collection_slot` (
  `id` int NOT NULL AUTO_INCREMENT,
  `status` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `collection_id` int NOT NULL,
  `position` int NOT NULL,
  `view_type` varchar(191) DEFAULT NULL,
  PRIMARY KEY (`id`,`status`),
  UNIQUE KEY `idx_ngl_collection_slot_uuid` (`uuid`,`status`),
  KEY `idx_ngl_collection` (`collection_id`,`status`),
  KEY `idx_ngl_position` (`collection_id`,`position`),
  CONSTRAINT `fk_ngl_slot_collection` FOREIGN KEY (`collection_id`, `status`) REFERENCES `nglayouts_collection` (`id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_collection_translation`
--

DROP TABLE IF EXISTS `nglayouts_collection_translation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_collection_translation` (
  `collection_id` int NOT NULL,
  `status` int NOT NULL,
  `locale` varchar(191) NOT NULL,
  PRIMARY KEY (`collection_id`,`status`,`locale`),
  CONSTRAINT `fk_ngl_collection_translation_collection` FOREIGN KEY (`collection_id`, `status`) REFERENCES `nglayouts_collection` (`id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_layout`
--

DROP TABLE IF EXISTS `nglayouts_layout`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_layout` (
  `id` int NOT NULL AUTO_INCREMENT,
  `status` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `type` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `description` longtext NOT NULL,
  `created` int NOT NULL,
  `modified` int NOT NULL,
  `shared` tinyint(1) NOT NULL,
  `main_locale` varchar(191) NOT NULL,
  PRIMARY KEY (`id`,`status`),
  UNIQUE KEY `idx_ngl_layout_uuid` (`uuid`,`status`),
  KEY `idx_ngl_layout_name` (`name`),
  KEY `idx_ngl_layout_type` (`type`),
  KEY `idx_ngl_layout_shared` (`shared`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_layout_translation`
--

DROP TABLE IF EXISTS `nglayouts_layout_translation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_layout_translation` (
  `layout_id` int NOT NULL,
  `status` int NOT NULL,
  `locale` varchar(191) NOT NULL,
  PRIMARY KEY (`layout_id`,`status`,`locale`),
  CONSTRAINT `fk_ngl_layout_translation_layout` FOREIGN KEY (`layout_id`, `status`) REFERENCES `nglayouts_layout` (`id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_migration_versions`
--

DROP TABLE IF EXISTS `nglayouts_migration_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_role`
--

DROP TABLE IF EXISTS `nglayouts_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_role` (
  `id` int NOT NULL AUTO_INCREMENT,
  `status` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `name` varchar(191) NOT NULL,
  `identifier` varchar(191) NOT NULL,
  `description` longtext NOT NULL,
  PRIMARY KEY (`id`,`status`),
  UNIQUE KEY `idx_ngl_role_uuid` (`uuid`,`status`),
  KEY `idx_ngl_role_identifier` (`identifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_role_policy`
--

DROP TABLE IF EXISTS `nglayouts_role_policy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_role_policy` (
  `id` int NOT NULL AUTO_INCREMENT,
  `status` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `role_id` int NOT NULL,
  `component` varchar(191) DEFAULT NULL,
  `permission` varchar(191) DEFAULT NULL,
  `limitations` longtext NOT NULL,
  PRIMARY KEY (`id`,`status`),
  UNIQUE KEY `idx_ngl_role_policy_uuid` (`uuid`,`status`),
  KEY `idx_ngl_role` (`role_id`,`status`),
  KEY `idx_ngl_policy_component` (`component`),
  KEY `idx_ngl_policy_component_permission` (`component`,`permission`),
  CONSTRAINT `fk_ngl_policy_role` FOREIGN KEY (`role_id`, `status`) REFERENCES `nglayouts_role` (`id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_rule`
--

DROP TABLE IF EXISTS `nglayouts_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_rule` (
  `id` int NOT NULL AUTO_INCREMENT,
  `status` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `rule_group_id` int NOT NULL,
  `layout_uuid` char(36) DEFAULT NULL,
  `description` longtext NOT NULL,
  PRIMARY KEY (`id`,`status`),
  UNIQUE KEY `idx_ngl_rule_uuid` (`uuid`,`status`),
  KEY `idx_ngl_related_layout` (`layout_uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_rule_condition`
--

DROP TABLE IF EXISTS `nglayouts_rule_condition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_rule_condition` (
  `id` int NOT NULL AUTO_INCREMENT,
  `status` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `type` varchar(191) NOT NULL,
  `value` longtext,
  PRIMARY KEY (`id`,`status`),
  UNIQUE KEY `idx_ngl_rule_condition_uuid` (`uuid`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_rule_condition_rule`
--

DROP TABLE IF EXISTS `nglayouts_rule_condition_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_rule_condition_rule` (
  `condition_id` int NOT NULL,
  `condition_status` int NOT NULL,
  `rule_id` int NOT NULL,
  `rule_status` int NOT NULL,
  PRIMARY KEY (`condition_id`,`condition_status`),
  KEY `idx_ngl_rule` (`rule_id`,`rule_status`),
  CONSTRAINT `fk_ngl_rule_condition_rule_rule` FOREIGN KEY (`rule_id`, `rule_status`) REFERENCES `nglayouts_rule` (`id`, `status`),
  CONSTRAINT `fk_ngl_rule_condition_rule_rule_condition` FOREIGN KEY (`condition_id`, `condition_status`) REFERENCES `nglayouts_rule_condition` (`id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_rule_condition_rule_group`
--

DROP TABLE IF EXISTS `nglayouts_rule_condition_rule_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_rule_condition_rule_group` (
  `condition_id` int NOT NULL,
  `condition_status` int NOT NULL,
  `rule_group_id` int NOT NULL,
  `rule_group_status` int NOT NULL,
  PRIMARY KEY (`condition_id`,`condition_status`),
  KEY `idx_ngl_rule_group` (`rule_group_id`,`rule_group_status`),
  CONSTRAINT `fk_ngl_rule_condition_rule_group_rule_condition` FOREIGN KEY (`condition_id`, `condition_status`) REFERENCES `nglayouts_rule_condition` (`id`, `status`),
  CONSTRAINT `fk_ngl_rule_condition_rule_group_rule_group` FOREIGN KEY (`rule_group_id`, `rule_group_status`) REFERENCES `nglayouts_rule_group` (`id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_rule_data`
--

DROP TABLE IF EXISTS `nglayouts_rule_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_rule_data` (
  `rule_id` int NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `priority` int NOT NULL,
  PRIMARY KEY (`rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_rule_group`
--

DROP TABLE IF EXISTS `nglayouts_rule_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_rule_group` (
  `id` int NOT NULL AUTO_INCREMENT,
  `status` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `depth` int NOT NULL,
  `path` varchar(191) NOT NULL,
  `parent_id` int DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `description` longtext NOT NULL,
  PRIMARY KEY (`id`,`status`),
  UNIQUE KEY `idx_ngl_rule_group_uuid` (`uuid`,`status`),
  KEY `idx_ngl_parent_rule_group` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_rule_group_data`
--

DROP TABLE IF EXISTS `nglayouts_rule_group_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_rule_group_data` (
  `rule_group_id` int NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `priority` int NOT NULL,
  PRIMARY KEY (`rule_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_rule_target`
--

DROP TABLE IF EXISTS `nglayouts_rule_target`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_rule_target` (
  `id` int NOT NULL AUTO_INCREMENT,
  `status` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `rule_id` int NOT NULL,
  `type` varchar(191) NOT NULL,
  `value` longtext,
  PRIMARY KEY (`id`,`status`),
  UNIQUE KEY `idx_ngl_rule_target_uuid` (`uuid`,`status`),
  KEY `idx_ngl_rule` (`rule_id`,`status`),
  KEY `idx_ngl_target_type` (`type`),
  CONSTRAINT `fk_ngl_target_rule` FOREIGN KEY (`rule_id`, `status`) REFERENCES `nglayouts_rule` (`id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nglayouts_zone`
--

DROP TABLE IF EXISTS `nglayouts_zone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nglayouts_zone` (
  `identifier` varchar(191) NOT NULL,
  `layout_id` int NOT NULL,
  `status` int NOT NULL,
  `root_block_id` int NOT NULL,
  `linked_layout_uuid` char(36) DEFAULT NULL,
  `linked_zone_identifier` varchar(191) DEFAULT NULL,
  PRIMARY KEY (`identifier`,`layout_id`,`status`),
  KEY `idx_ngl_layout` (`layout_id`,`status`),
  KEY `idx_ngl_root_block` (`root_block_id`,`status`),
  KEY `idx_ngl_linked_zone` (`linked_layout_uuid`,`linked_zone_identifier`),
  CONSTRAINT `fk_ngl_zone_block` FOREIGN KEY (`root_block_id`, `status`) REFERENCES `nglayouts_block` (`id`, `status`),
  CONSTRAINT `fk_ngl_zone_layout` FOREIGN KEY (`layout_id`, `status`) REFERENCES `nglayouts_layout` (`id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nguser_setting`
--

DROP TABLE IF EXISTS `nguser_setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nguser_setting` (
  `user_id` int NOT NULL DEFAULT '0',
  `is_activated` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `novaseo_meta`
--

DROP TABLE IF EXISTS `novaseo_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `novaseo_meta` (
  `objectattribute_id` bigint unsigned NOT NULL,
  `meta_name` varchar(191) NOT NULL,
  `meta_content` text NOT NULL,
  `objectattribute_version` int unsigned NOT NULL,
  PRIMARY KEY (`objectattribute_id`,`objectattribute_version`,`meta_name`),
  KEY `novaseo_idx_content` (`objectattribute_id`,`objectattribute_version`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `novaseo_redirect_import_history`
--

DROP TABLE IF EXISTS `novaseo_redirect_import_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `novaseo_redirect_import_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name_file` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  `path` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sckenhancedselection`
--

DROP TABLE IF EXISTS `sckenhancedselection`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sckenhancedselection` (
  `contentobject_attribute_id` int NOT NULL DEFAULT '0',
  `contentobject_attribute_version` int NOT NULL DEFAULT '0',
  `identifier` varchar(255) NOT NULL DEFAULT '',
  KEY `sckenhancedselection_coaid_coav` (`contentobject_attribute_id`,`contentobject_attribute_version`),
  KEY `sckenhancedselection_coaid_coav_iden` (`contentobject_attribute_id`,`contentobject_attribute_version`,`identifier`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-10 12:00:00

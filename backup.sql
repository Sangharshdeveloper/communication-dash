-- MySQL dump 10.13  Distrib 9.4.0, for macos15.4 (arm64)
--
-- Host: localhost    Database: cbuae_insurance
-- ------------------------------------------------------
-- Server version	9.4.0

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
  `user_id` bigint unsigned DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Email even if user not yet authenticated',
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'link_generated, email_sent, login_success, login_failed, logout, otp_sent, otp_verified, token_expired, token_revoked, suspicious_activity',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'success' COMMENT 'success, failure, warning',
  `description` text COLLATE utf8mb4_unicode_ci,
  `metadata` json DEFAULT NULL COMMENT 'Additional context data',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `country_code` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Should be AE for UAE compliance',
  `request_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Unique request identifier for tracing',
  `session_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `audit_logs_user_id_action_created_at_index` (`user_id`,`action`,`created_at`),
  KEY `audit_logs_email_created_at_index` (`email`,`created_at`),
  KEY `audit_logs_action_status_created_at_index` (`action`,`status`,`created_at`),
  KEY `audit_logs_ip_address_index` (`ip_address`),
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES (1,1,'admin@insurance.ae','login_success','success','Staff login successful for admin@insurance.ae (role: admin)',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e66331ece7e','ecHIcXcYv4jy6dtemHbA853JG7JM48ciMYhXMF7z','2026-04-20 16:02:33'),(2,4,'sangharshsulke@gmail.com','link_generated','success','Magic link generated for sangharshsulke@gmail.com','{\"token_id\": 1, \"expires_at\": \"2026-04-20T17:48:00.000000Z\"}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e66478442e8','syPPU6CkvRKzP8o2U5AsdeucjquTKDBMg9znKSLB','2026-04-20 16:08:00'),(3,4,'sangharshsulke@gmail.com','email_sent','success','Magic link email dispatched to sangharshsulke@gmail.com',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e6647b56356','syPPU6CkvRKzP8o2U5AsdeucjquTKDBMg9znKSLB','2026-04-20 16:08:03'),(4,4,'sangharshsulke@gmail.com','link_generated','success','Magic link generated for sangharshsulke@gmail.com','{\"token_id\": 2, \"expires_at\": \"2026-04-20T17:48:09.000000Z\"}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e66481d1b50','syPPU6CkvRKzP8o2U5AsdeucjquTKDBMg9znKSLB','2026-04-20 16:08:09'),(5,4,'sangharshsulke@gmail.com','email_sent','success','Magic link email dispatched to sangharshsulke@gmail.com',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e66484b06f7','syPPU6CkvRKzP8o2U5AsdeucjquTKDBMg9znKSLB','2026-04-20 16:08:12'),(6,4,'sangharshsulke@gmail.com','login_failed','failure','Magic link revoked: superseded',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e6648fd9f71','syPPU6CkvRKzP8o2U5AsdeucjquTKDBMg9znKSLB','2026-04-20 16:08:23'),(7,4,'sangharshsulke@gmail.com','link_generated','success','Magic link generated for sangharshsulke@gmail.com','{\"token_id\": 3, \"expires_at\": \"2026-04-20T17:48:59.000000Z\"}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e664b357cf8','syPPU6CkvRKzP8o2U5AsdeucjquTKDBMg9znKSLB','2026-04-20 16:08:59'),(8,4,'sangharshsulke@gmail.com','email_sent','success','Magic link email dispatched to sangharshsulke@gmail.com',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e664b639e58','syPPU6CkvRKzP8o2U5AsdeucjquTKDBMg9znKSLB','2026-04-20 16:09:02'),(9,4,'sangharshsulke@gmail.com','login_success','success','Successful magic link login for sangharshsulke@gmail.com','{\"token_id\": 3}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e664ce9568d','syPPU6CkvRKzP8o2U5AsdeucjquTKDBMg9znKSLB','2026-04-20 16:09:26'),(10,1,'admin@insurance.ae','logout','success','User admin@insurance.ae logged out',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e6658f4a146','ecHIcXcYv4jy6dtemHbA853JG7JM48ciMYhXMF7z','2026-04-20 16:12:39'),(11,4,'sangharshsulke@gmail.com','link_generated','success','Magic link generated for sangharshsulke@gmail.com','{\"token_id\": 4, \"expires_at\": \"2026-04-20T17:52:58.000000Z\"}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e665a2185fb','MCJOlr1NsldJfBDSfTMD5LPTlFHxD2VoyBXoPHSl','2026-04-20 16:12:58'),(12,4,'sangharshsulke@gmail.com','email_sent','success','Magic link email dispatched to sangharshsulke@gmail.com',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e665a4e3146','MCJOlr1NsldJfBDSfTMD5LPTlFHxD2VoyBXoPHSl','2026-04-20 16:13:00'),(13,2,'agent@insurance.ae','login_success','success','Staff login successful for agent@insurance.ae (role: agent)',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e665c6955a4','6Gieps05W4m9R970BtJt5CUn3mxjQushHgsf8pq4','2026-04-20 16:13:34'),(14,2,'agent@insurance.ae','login_success','success','Staff login successful for agent@insurance.ae (role: agent)',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e66642855f8','P7r1sdtUlX47Iq93bFGYJ71wXRd7DdmXGxTczWMn','2026-04-20 16:15:38'),(15,2,'agent@insurance.ae','login_success','success','Staff login successful for agent@insurance.ae (role: agent)',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e6d5d674dbd','oIUDSQLTHaToXQPLNNl4P6sbBPt6D3lB6hWccP6d','2026-04-21 00:11:42'),(16,2,'agent@insurance.ae','logout','success','User agent@insurance.ae logged out',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e6d5e8ce6d9','oIUDSQLTHaToXQPLNNl4P6sbBPt6D3lB6hWccP6d','2026-04-21 00:12:00'),(17,1,'admin@insurance.ae','login_success','success','Staff login successful for admin@insurance.ae (role: admin)',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e6d5f54a054','jEnWZkPxfpv9R5oGYLodCUxTsJyqmuTcTG1jtgpJ','2026-04-21 00:12:13'),(18,1,'admin@insurance.ae','logout','success','User admin@insurance.ae logged out',NULL,'127.0.0.1','Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Mobile Safari/537.36',NULL,'req_69e6d92ab9465','jEnWZkPxfpv9R5oGYLodCUxTsJyqmuTcTG1jtgpJ','2026-04-21 00:25:54'),(19,2,'agent@insurance.ae','login_success','success','Staff login successful for agent@insurance.ae (role: agent)',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e6d93609fee','QJYLH6XRoun511WIOCh3yiTupLGphjyedYbAexSw','2026-04-21 00:26:06'),(20,2,'agent@insurance.ae','logout','success','User agent@insurance.ae logged out',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e6da0f4728c','QJYLH6XRoun511WIOCh3yiTupLGphjyedYbAexSw','2026-04-21 00:29:43'),(21,1,'admin@insurance.ae','login_success','success','Staff login successful for admin@insurance.ae (role: admin)',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e6da14eb073','LvW9rWARsYOtWdCY7McMozkUu6Z8QdW1t86AgreK','2026-04-21 00:29:48'),(22,1,'admin@insurance.ae','logout','success','User admin@insurance.ae logged out',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e6da6e2b08f','LvW9rWARsYOtWdCY7McMozkUu6Z8QdW1t86AgreK','2026-04-21 00:31:18'),(23,2,'agent@insurance.ae','login_success','success','Staff login successful for agent@insurance.ae (role: agent)',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e6da7bf37ae','qRz7763kyQxma6uprH9LnQC6H5VFm3rHd5yS5x3L','2026-04-21 00:31:31'),(24,2,'agent@insurance.ae','logout','success','Session auto-expired due to inactivity for agent@insurance.ae','{\"inactivity_seconds\": 2291}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e6eb742f58b','qRz7763kyQxma6uprH9LnQC6H5VFm3rHd5yS5x3L','2026-04-21 01:43:56'),(25,4,'sangharshsulke@gmail.com','link_generated','success','Magic link generated for sangharshsulke@gmail.com','{\"token_id\": 5, \"expires_at\": \"2026-04-21T03:27:11.000000Z\"}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e6ec371c671','grrJl0ywwtQEW8O5We9Jmi8t1EzPO9ZiLFGfRkBp','2026-04-21 01:47:11'),(26,4,'sangharshsulke@gmail.com','email_sent','success','Magic link email dispatched to sangharshsulke@gmail.com',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e6ec3aea162','grrJl0ywwtQEW8O5We9Jmi8t1EzPO9ZiLFGfRkBp','2026-04-21 01:47:14'),(27,1,'admin@insurance.ae','login_success','success','Staff login successful for admin@insurance.ae (role: admin)',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e6ecd006abc','2gVnAloGysbgkZpA16Kki5wtdZlZnh4M2DS8MICP','2026-04-21 01:49:44'),(28,3,'auditor@insurance.ae','login_success','success','Staff login successful for auditor@insurance.ae (role: auditor)',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e6ed56868e9','IkA9LJ6TY3Ni0izGsP6gU2b14JRFAwYUj6sdz5DY','2026-04-21 01:51:58'),(29,3,'auditor@insurance.ae','logout','success','Session auto-expired due to inactivity for auditor@insurance.ae','{\"inactivity_seconds\": 1875}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e6f4bb14da0','IkA9LJ6TY3Ni0izGsP6gU2b14JRFAwYUj6sdz5DY','2026-04-21 02:23:31'),(30,1,'admin@insurance.ae','login_success','success','Staff login successful for admin@insurance.ae (role: admin)',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e6f4cac1b4c','AYvTd7OdzCYj5MyrBh5sQUao897F6VsYq7ocsMuv','2026-04-21 02:23:46'),(31,1,'admin@insurance.ae','logout','success','User admin@insurance.ae logged out',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e6f4e4a49cc','AYvTd7OdzCYj5MyrBh5sQUao897F6VsYq7ocsMuv','2026-04-21 02:24:12'),(32,1,'admin@insurance.ae','login_success','success','Staff login successful for admin@insurance.ae (role: admin)',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e6f605e7ce3','BFwAaV1iTe3Jna4HlNacyXoWXRK9ZMRLxiicAJ64','2026-04-21 02:29:01'),(33,4,'sangharshsulke@gmail.com','link_generated','success','Magic link generated for sangharshsulke@gmail.com','{\"token_id\": 6, \"expires_at\": \"2026-04-21T04:09:21.000000Z\"}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e6f61904faa','BFwAaV1iTe3Jna4HlNacyXoWXRK9ZMRLxiicAJ64','2026-04-21 02:29:21'),(34,4,'sangharshsulke@gmail.com','email_sent','success','Magic link email dispatched to sangharshsulke@gmail.com',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e6f61b7db3b','BFwAaV1iTe3Jna4HlNacyXoWXRK9ZMRLxiicAJ64','2026-04-21 02:29:23'),(35,4,'sangharshsulke@gmail.com','login_success','success','Successful magic link login for sangharshsulke@gmail.com','{\"token_id\": 6}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e6f6316ba73','v2hiIjR6OHL0xLQzoSxHD26xCS044e7Zs2CdHQXe','2026-04-21 02:29:45'),(36,1,'admin@insurance.ae','email_sent','success','Sent chat link to sangharshsulke@gmail.com (agent: agent@insurance.ae)',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e6f6459f61b','BFwAaV1iTe3Jna4HlNacyXoWXRK9ZMRLxiicAJ64','2026-04-21 02:30:05'),(37,1,'admin@insurance.ae','logout','success','User admin@insurance.ae logged out',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e6f6866441f','BFwAaV1iTe3Jna4HlNacyXoWXRK9ZMRLxiicAJ64','2026-04-21 02:31:10'),(38,2,'agent@insurance.ae','login_success','success','Staff login successful for agent@insurance.ae (role: agent)',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e6f693c6d6b','OnzMDUporJ9pv7Bi42CAXle4sO8HVa0G95I8wT8A','2026-04-21 02:31:23'),(39,2,'agent@insurance.ae','logout','success','Session auto-expired due to inactivity for agent@insurance.ae','{\"inactivity_seconds\": 1777}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e6fdbe8c44f','OnzMDUporJ9pv7Bi42CAXle4sO8HVa0G95I8wT8A','2026-04-21 03:01:58'),(40,2,'agent@insurance.ae','login_success','success','Staff login successful for agent@insurance.ae (role: agent)',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e6fdc3a9d79','CLrHe3xZS1A9kPfYT4BZA3Ew10mB9vdZbWsEdD4z','2026-04-21 03:02:03'),(41,4,'sangharshsulke@gmail.com','logout','success','Session auto-expired due to inactivity for sangharshsulke@gmail.com','{\"inactivity_seconds\": 2032}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e6feb6bda78','7y0kKB9x5ScKJGFdIY1zHDs0xwymibXwxJHx4mma','2026-04-21 03:06:06'),(42,NULL,'admin@gmail.com','login_failed','failure','Staff login failed: account not found',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e769971f9cd','vP15ZzM2HdBBDq8JoSA1bSJOn1G31KxLF7bHu26x','2026-04-21 10:42:07'),(43,1,'admin@insurance.ae','login_success','success','Staff login successful for admin@insurance.ae (role: admin)',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e769a6b031c','rzZGh3Wah1DQwwmN3x2JYA2MQ3ALMVRVTNj5O0D7','2026-04-21 10:42:22'),(44,1,'admin@insurance.ae','logout','success','User admin@insurance.ae logged out',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e769ca8cf07','rzZGh3Wah1DQwwmN3x2JYA2MQ3ALMVRVTNj5O0D7','2026-04-21 10:42:58'),(45,2,'agent@insurance.ae','login_failed','failure','Staff login failed: wrong password (attempt 1)',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e769e09d316','rzZGh3Wah1DQwwmN3x2JYA2MQ3ALMVRVTNj5O0D7','2026-04-21 10:43:20'),(46,2,'agent@insurance.ae','login_success','success','Staff login successful for agent@insurance.ae (role: agent)',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e769eeac4a9','7mBaVdjoQsVAZdUzbNtjBYOwnnJNlUF9vfx6YRla','2026-04-21 10:43:34'),(47,2,'agent@insurance.ae','logout','success','User agent@insurance.ae logged out',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e76c9d0a281','7mBaVdjoQsVAZdUzbNtjBYOwnnJNlUF9vfx6YRla','2026-04-21 10:55:01'),(48,1,'admin@insurance.ae','login_success','success','Staff login successful for admin@insurance.ae (role: admin)',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e76cb0df729','mc4zKGayUb6ZYdeKuzfDbDMiqjSNTOfG1uA31lcp','2026-04-21 10:55:20'),(49,1,'admin@insurance.ae','logout','success','User admin@insurance.ae logged out',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e76de420bb1','mc4zKGayUb6ZYdeKuzfDbDMiqjSNTOfG1uA31lcp','2026-04-21 11:00:28'),(50,1,'admin@insurance.ae','login_success','success','Staff login successful for admin@insurance.ae (role: admin)',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e76e2965e9d','UKUC7nGY6wGDjgw8fg3uy6VjGT0Yy35YIAvoPSw3','2026-04-21 11:01:37'),(51,4,'sangharshsulke@gmail.com','link_generated','success','Magic link generated for sangharshsulke@gmail.com','{\"token_id\": 7, \"expires_at\": \"2026-04-21T12:42:04.000000Z\"}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e76e44eff3e','UKUC7nGY6wGDjgw8fg3uy6VjGT0Yy35YIAvoPSw3','2026-04-21 11:02:04'),(52,4,'sangharshsulke@gmail.com','email_sent','success','Magic link email dispatched to sangharshsulke@gmail.com',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e76e472ff22','UKUC7nGY6wGDjgw8fg3uy6VjGT0Yy35YIAvoPSw3','2026-04-21 11:02:07'),(53,4,'sangharshsulke@gmail.com','link_generated','success','Magic link generated for sangharshsulke@gmail.com','{\"token_id\": 8, \"expires_at\": \"2026-04-21T12:45:31.000000Z\"}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e76f13b7499','UKUC7nGY6wGDjgw8fg3uy6VjGT0Yy35YIAvoPSw3','2026-04-21 11:05:31'),(54,4,'sangharshsulke@gmail.com','email_sent','success','Magic link email dispatched to sangharshsulke@gmail.com',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e76f15cafec','UKUC7nGY6wGDjgw8fg3uy6VjGT0Yy35YIAvoPSw3','2026-04-21 11:05:33'),(55,4,'sangharshsulke@gmail.com','login_success','success','Successful magic link login for sangharshsulke@gmail.com','{\"token_id\": 8}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e76f2681141','hoqHnx0OoBtycBdFT7kyFFAyfepCI6WlPf2GehKn','2026-04-21 11:05:50'),(56,1,'admin@insurance.ae','email_sent','success','Sent chat link to sangharshsulke@gmail.com (agent: agent@insurance.ae)',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e76f3ca579f','UKUC7nGY6wGDjgw8fg3uy6VjGT0Yy35YIAvoPSw3','2026-04-21 11:06:12'),(57,1,'admin@insurance.ae','logout','success','User admin@insurance.ae logged out',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e770098e70a','UKUC7nGY6wGDjgw8fg3uy6VjGT0Yy35YIAvoPSw3','2026-04-21 11:09:37'),(58,2,'agent@insurance.ae','login_success','success','Staff login successful for agent@insurance.ae (role: agent)',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e77010d4473','hOTKuyYBCrYJlRMki3idIawKb6QbDR5beRAKrHrq','2026-04-21 11:09:44'),(59,2,'agent@insurance.ae','logout','success','Session auto-expired due to inactivity for agent@insurance.ae','{\"inactivity_seconds\": 1489}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e77a7567444','hOTKuyYBCrYJlRMki3idIawKb6QbDR5beRAKrHrq','2026-04-21 11:54:05'),(60,NULL,'admin@gmail.com','login_failed','failure','Staff login failed: account not found',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e77ca330571','ZEsC4ojjjlEqMDSDvRMoaCBxOI43tfk6E16hAf2l','2026-04-21 12:03:23'),(61,2,'agent@insurance.ae','login_success','success','Staff login successful for agent@insurance.ae (role: agent)',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e77caead427','UrWwLqyiNyPY1LGfi0Z6xBP0XlJ0wBg5MqHd1qcQ','2026-04-21 12:03:34'),(62,2,'agent@insurance.ae','logout','success','User agent@insurance.ae logged out',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e77ddf1286e','UrWwLqyiNyPY1LGfi0Z6xBP0XlJ0wBg5MqHd1qcQ','2026-04-21 12:08:39'),(63,1,'admin@insurance.ae','login_success','success','Staff login successful for admin@insurance.ae (role: admin)',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e77ded06628','CDCuIhHiSCkHh73D7KcGWkPDo8h38N8LntoKvjgr','2026-04-21 12:08:53'),(64,1,'admin@insurance.ae','logout','success','User admin@insurance.ae logged out',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e780083b437','CDCuIhHiSCkHh73D7KcGWkPDo8h38N8LntoKvjgr','2026-04-21 12:17:52'),(65,2,'agent@insurance.ae','login_success','success','Staff login successful for agent@insurance.ae (role: agent)',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'req_69e7801cbd2e7','kqIFFQodvCe1d8mdBErUkqxOYJxDVlEQQNGpOGp9','2026-04-21 12:18:12');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chat_messages`
--

DROP TABLE IF EXISTS `chat_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `chat_room_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chat_messages_user_id_foreign` (`user_id`),
  KEY `chat_messages_chat_room_id_created_at_index` (`chat_room_id`,`created_at`),
  CONSTRAINT `chat_messages_chat_room_id_foreign` FOREIGN KEY (`chat_room_id`) REFERENCES `chat_rooms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `chat_messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chat_messages`
--

LOCK TABLES `chat_messages` WRITE;
/*!40000 ALTER TABLE `chat_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `chat_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chat_room_user`
--

DROP TABLE IF EXISTS `chat_room_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_room_user` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `chat_room_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `last_read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chat_room_user_chat_room_id_foreign` (`chat_room_id`),
  KEY `chat_room_user_user_id_foreign` (`user_id`),
  CONSTRAINT `chat_room_user_chat_room_id_foreign` FOREIGN KEY (`chat_room_id`) REFERENCES `chat_rooms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `chat_room_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chat_room_user`
--

LOCK TABLES `chat_room_user` WRITE;
/*!40000 ALTER TABLE `chat_room_user` DISABLE KEYS */;
INSERT INTO `chat_room_user` VALUES (1,1,4,NULL,'2026-04-20 16:09:37','2026-04-20 16:09:37'),(2,2,4,NULL,'2026-04-20 16:09:37','2026-04-20 16:09:37'),(3,3,4,NULL,'2026-04-20 16:09:37','2026-04-20 16:09:37'),(4,4,4,NULL,'2026-04-20 16:09:37','2026-04-20 16:09:37'),(5,1,2,'2026-04-21 00:26:19','2026-04-21 00:26:15','2026-04-21 00:26:19'),(6,2,2,'2026-04-21 00:26:17','2026-04-21 00:26:15','2026-04-21 00:26:17'),(7,3,2,'2026-04-21 00:26:17','2026-04-21 00:26:15','2026-04-21 00:26:17'),(8,4,2,'2026-04-21 00:26:18','2026-04-21 00:26:15','2026-04-21 00:26:18');
/*!40000 ALTER TABLE `chat_room_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chat_rooms`
--

DROP TABLE IF EXISTS `chat_rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_rooms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chat_rooms`
--

LOCK TABLES `chat_rooms` WRITE;
/*!40000 ALTER TABLE `chat_rooms` DISABLE KEYS */;
INSERT INTO `chat_rooms` VALUES (1,'General','general','General discussion for all staff',1,'2026-04-20 16:02:14','2026-04-20 16:02:14'),(2,'Claims','claims','Claims processing and updates',1,'2026-04-20 16:02:14','2026-04-20 16:02:14'),(3,'Underwriting','underwriting','Underwriting team channel',1,'2026-04-20 16:02:14','2026-04-20 16:02:14'),(4,'Support','support','Customer support escalations',1,'2026-04-20 16:02:14','2026-04-20 16:02:14');
/*!40000 ALTER TABLE `chat_rooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `direct_chat_sessions`
--

DROP TABLE IF EXISTS `direct_chat_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `direct_chat_sessions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint unsigned NOT NULL,
  `agent_id` bigint unsigned NOT NULL,
  `customer_ref` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'External customer ID from URL param',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `last_activity_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `direct_chat_sessions_session_token_unique` (`session_token`),
  KEY `direct_chat_sessions_agent_id_foreign` (`agent_id`),
  KEY `direct_chat_sessions_session_token_index` (`session_token`),
  KEY `direct_chat_sessions_customer_id_agent_id_index` (`customer_id`,`agent_id`),
  CONSTRAINT `direct_chat_sessions_agent_id_foreign` FOREIGN KEY (`agent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `direct_chat_sessions_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `direct_chat_sessions`
--

LOCK TABLES `direct_chat_sessions` WRITE;
/*!40000 ALTER TABLE `direct_chat_sessions` DISABLE KEYS */;
INSERT INTO `direct_chat_sessions` VALUES (1,'EIwn1vbmKuQiBaB9Lo6g18kWC5ZUMXTVZO7Ci7dwiQrAgmg0',5,2,'CUST-001','active','2026-04-20 16:10:47','2026-04-27 16:10:47','2026-04-20 16:10:47','2026-04-20 16:10:47'),(2,'mb7xIKNMF5zJNpVVrhppcYnbVphVHdcHmFLtAHAWcocmyfkg',6,2,'4','active','2026-04-21 02:20:58','2026-04-28 00:23:44','2026-04-21 00:23:44','2026-04-21 02:20:58'),(3,'hhJpZSoCzRTlZBVIHLPGGSL1475WppCCsr44qTxcfaRhkU1Z',6,2,'4','active','2026-04-21 00:56:33','2026-04-28 00:30:34','2026-04-21 00:30:34','2026-04-21 00:56:33'),(4,'mg4Imuehs4lnUxe3ovENXZOzBITdpNHTpv7yGew6dtpIhXIq',4,2,'4','active','2026-04-21 12:26:23','2026-05-21 02:30:31','2026-04-21 02:30:31','2026-04-21 12:26:23'),(5,'63uODcy9eew40ytrRXTTrr31L1SB4rQcYADLbnAUFAalaKf3',7,2,'7','active','2026-04-21 12:07:37','2026-05-21 12:01:46','2026-04-21 12:01:46','2026-04-21 12:07:37');
/*!40000 ALTER TABLE `direct_chat_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `direct_message_attachments`
--

DROP TABLE IF EXISTS `direct_message_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `direct_message_attachments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `message_id` bigint unsigned NOT NULL,
  `original_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stored_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `size` bigint unsigned NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `direct_message_attachments_message_id_foreign` (`message_id`),
  CONSTRAINT `direct_message_attachments_message_id_foreign` FOREIGN KEY (`message_id`) REFERENCES `direct_messages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `direct_message_attachments`
--

LOCK TABLES `direct_message_attachments` WRITE;
/*!40000 ALTER TABLE `direct_message_attachments` DISABLE KEYS */;
INSERT INTO `direct_message_attachments` VALUES (1,4,'ChatGPT Image Apr 6, 2026, 11_15_02 AM.png','4kYWkAJUAAKDWAJiGXL8rwhnSDc2S3aUnQ5Zx2a6.png','image/png',1722424,'chat/2/4kYWkAJUAAKDWAJiGXL8rwhnSDc2S3aUnQ5Zx2a6.png','2026-04-21 00:25:24','2026-04-21 00:25:24'),(2,6,'emi-fron.jpeg','aJgXTrK8XlIgbHAX4VrYWfuYCQuavbPCwOSf6kNa.jpg','image/jpeg',510817,'chat/2/aJgXTrK8XlIgbHAX4VrYWfuYCQuavbPCwOSf6kNa.jpg','2026-04-21 00:26:58','2026-04-21 00:26:58'),(3,18,'emi-fron.jpeg','o0AZNRZe3MfdUzhwgwtmNIxKvOww8U6T8rdYotmA.jpg','image/jpeg',510817,'chat/4/o0AZNRZe3MfdUzhwgwtmNIxKvOww8U6T8rdYotmA.jpg','2026-04-21 11:21:35','2026-04-21 11:21:35'),(4,22,'image001.png','EZLhZzTLsq8pB24M2oFZ2lbPrn6b2HY5wZnU7Q0N.png','image/png',15487,'chat/4/EZLhZzTLsq8pB24M2oFZ2lbPrn6b2HY5wZnU7Q0N.png','2026-04-21 12:19:01','2026-04-21 12:19:01');
/*!40000 ALTER TABLE `direct_message_attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `direct_messages`
--

DROP TABLE IF EXISTS `direct_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `direct_messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` bigint unsigned NOT NULL,
  `sender_id` bigint unsigned NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `direct_messages_sender_id_foreign` (`sender_id`),
  KEY `direct_messages_session_id_created_at_index` (`session_id`,`created_at`),
  CONSTRAINT `direct_messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `direct_messages_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `direct_chat_sessions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `direct_messages`
--

LOCK TABLES `direct_messages` WRITE;
/*!40000 ALTER TABLE `direct_messages` DISABLE KEYS */;
INSERT INTO `direct_messages` VALUES (1,1,2,'Hello! I\'m Insurance Agent, your dedicated insurance agent. How can I help you today?','system',0,NULL,'2026-04-20 16:10:47','2026-04-20 16:10:47'),(2,2,2,'Hello! I\'m Insurance Agent, your dedicated insurance agent. How can I help you today?','system',1,'2026-04-21 00:23:48','2026-04-21 00:23:44','2026-04-21 00:23:48'),(3,2,6,'hello','text',1,'2026-04-21 00:54:33','2026-04-21 00:23:49','2026-04-21 00:54:33'),(4,2,6,'','attachment',1,'2026-04-21 00:54:33','2026-04-21 00:25:24','2026-04-21 00:54:33'),(5,2,2,'Send me you invoice here','text',1,'2026-04-21 00:26:47','2026-04-21 00:26:46','2026-04-21 00:26:47'),(6,2,2,'','attachment',1,'2026-04-21 00:26:59','2026-04-21 00:26:58','2026-04-21 00:26:59'),(7,3,2,'Hello! I\'m Insurance Agent, your dedicated insurance agent. How can I help you today?','system',1,'2026-04-21 00:30:37','2026-04-21 00:30:34','2026-04-21 00:30:37'),(8,3,6,'Good morning','text',1,'2026-04-21 00:54:39','2026-04-21 00:30:53','2026-04-21 00:54:39'),(9,3,2,'Send your documents\r\n1. Emirate ID Front-back\r\n2. Mukiya Card Front-Back\r\n3. Driving Licence \r\netc','text',1,'2026-04-21 00:56:18','2026-04-21 00:55:40','2026-04-21 00:56:18'),(10,3,6,'hi','text',1,'2026-04-21 00:56:53','2026-04-21 00:56:33','2026-04-21 00:56:53'),(11,2,6,'hello','text',1,'2026-04-21 01:04:56','2026-04-21 01:04:39','2026-04-21 01:04:56'),(12,2,6,'d','text',1,'2026-04-21 02:31:33','2026-04-21 02:20:58','2026-04-21 02:31:33'),(13,4,2,'Hello! I\'m Insurance Agent, your dedicated insurance agent. How can I help you today?','system',1,'2026-04-21 02:30:31','2026-04-21 02:30:31','2026-04-21 02:30:31'),(14,4,4,'HELLO','text',1,'2026-04-21 02:31:30','2026-04-21 02:30:37','2026-04-21 02:31:30'),(15,4,4,'j','text',1,'2026-04-21 10:43:50','2026-04-21 03:14:53','2026-04-21 10:43:50'),(16,4,4,'Hello','text',1,'2026-04-21 10:43:50','2026-04-21 10:42:48','2026-04-21 10:43:50'),(17,4,4,'HEllo','text',1,'2026-04-21 10:54:26','2026-04-21 10:54:13','2026-04-21 10:54:26'),(18,4,4,'','attachment',1,'2026-04-21 12:07:26','2026-04-21 11:21:35','2026-04-21 12:07:26'),(19,5,2,'Hello Ali Hassan! I\'m Insurance Agent, your dedicated agent. How can I help you today?','system',1,'2026-04-21 12:02:07','2026-04-21 12:01:46','2026-04-21 12:02:07'),(20,5,2,'Helllo','text',1,'2026-04-21 12:07:42','2026-04-21 12:07:37','2026-04-21 12:07:42'),(21,4,4,'hello','text',1,'2026-04-21 12:19:07','2026-04-21 12:17:42','2026-04-21 12:19:07'),(22,4,4,'','attachment',1,'2026-04-21 12:19:07','2026-04-21 12:19:01','2026-04-21 12:19:07'),(23,4,4,'d','text',0,NULL,'2026-04-21 12:26:23','2026-04-21 12:26:23');
/*!40000 ALTER TABLE `direct_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `magic_login_tokens`
--

DROP TABLE IF EXISTS `magic_login_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `magic_login_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `token_hash` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'SHA-256 hash of the raw token',
  `expires_at` timestamp NOT NULL COMMENT 'Token expires 10-15 min after creation',
  `is_used` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'One-time use only',
  `used_at` timestamp NULL DEFAULT NULL,
  `created_ip` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'IP address that requested the link',
  `used_ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'IP address that used the link',
  `created_user_agent` text COLLATE utf8mb4_unicode_ci,
  `used_user_agent` text COLLATE utf8mb4_unicode_ci,
  `device_fingerprint` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otp_required` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'OTP required if IP/device mismatch',
  `otp_hash` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Hashed OTP for secondary verification',
  `otp_expires_at` timestamp NULL DEFAULT NULL,
  `otp_verified` tinyint(1) NOT NULL DEFAULT '0',
  `invalidated_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invalidated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `magic_login_tokens_token_hash_unique` (`token_hash`),
  KEY `magic_login_tokens_user_id_is_used_expires_at_index` (`user_id`,`is_used`,`expires_at`),
  KEY `magic_login_tokens_expires_at_index` (`expires_at`),
  CONSTRAINT `magic_login_tokens_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `magic_login_tokens`
--

LOCK TABLES `magic_login_tokens` WRITE;
/*!40000 ALTER TABLE `magic_login_tokens` DISABLE KEYS */;
INSERT INTO `magic_login_tokens` VALUES (1,4,'aa134df1766d4308a2fd4007ae9a01d5d23793c4328e1601d0105fe41084bc11','2026-04-20 16:18:00',0,NULL,'127.0.0.1',NULL,'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'9fe6337678473b8691aec0b6bb38d50592889fbb2d1c71603270abb144d2caa0',0,NULL,NULL,0,'superseded','2026-04-20 16:08:09','2026-04-20 16:08:00','2026-04-20 16:08:09'),(2,4,'c1dbc6ce64d8485077fbde48b31e6c29f5d951d024e426734e57ea6c375cca48','2026-04-20 16:18:09',0,NULL,'127.0.0.1',NULL,'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'9fe6337678473b8691aec0b6bb38d50592889fbb2d1c71603270abb144d2caa0',0,NULL,NULL,0,'superseded','2026-04-20 16:08:59','2026-04-20 16:08:09','2026-04-20 16:08:59'),(3,4,'824c2c0dcc32fde0b1e09e7b6795604cbd2d0940466f895c2492f5838a598c86','2026-04-20 16:18:59',1,'2026-04-20 16:09:26','127.0.0.1','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','9fe6337678473b8691aec0b6bb38d50592889fbb2d1c71603270abb144d2caa0',0,NULL,NULL,0,'used','2026-04-20 16:09:26','2026-04-20 16:08:59','2026-04-20 16:09:26'),(4,4,'b01db15882c2407202d3d0fb13cbbf5c8b98fe39dc4aff5a0f0e114e87a473d3','2026-04-20 16:22:58',0,NULL,'127.0.0.1',NULL,'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'9fe6337678473b8691aec0b6bb38d50592889fbb2d1c71603270abb144d2caa0',0,NULL,NULL,0,NULL,NULL,'2026-04-20 16:12:58','2026-04-20 16:12:58'),(5,4,'ce6b7a7582fa4a217b5e75c67d020e762ebfc0ef350bdddd0e66dde13542ebba','2026-04-21 01:57:11',0,NULL,'127.0.0.1',NULL,'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'9fe6337678473b8691aec0b6bb38d50592889fbb2d1c71603270abb144d2caa0',0,NULL,NULL,0,NULL,NULL,'2026-04-21 01:47:11','2026-04-21 01:47:11'),(6,4,'35c4b16626e36641266295b15a13edc2c0594d4cc0870d143fe910ef125800e5','2026-04-21 02:39:21',1,'2026-04-21 02:29:45','127.0.0.1','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','9fe6337678473b8691aec0b6bb38d50592889fbb2d1c71603270abb144d2caa0',0,NULL,NULL,0,'used','2026-04-21 02:29:45','2026-04-21 02:29:21','2026-04-21 02:29:45'),(7,4,'30b1fca1629ac8e69a53b2f6b4487c475ea1bb684d811fb8fe73f097ef770ae7','2026-04-21 11:12:04',0,NULL,'127.0.0.1',NULL,'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',NULL,'9fe6337678473b8691aec0b6bb38d50592889fbb2d1c71603270abb144d2caa0',0,NULL,NULL,0,'superseded','2026-04-21 11:05:31','2026-04-21 11:02:04','2026-04-21 11:05:31'),(8,4,'fc3862910f9d6e9bb5d140c37533d1be6e3485f47bd07ee4c81fd4edcc2a50ca','2026-04-21 11:15:31',1,'2026-04-21 11:05:50','127.0.0.1','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','9fe6337678473b8691aec0b6bb38d50592889fbb2d1c71603270abb144d2caa0',0,NULL,NULL,0,'used','2026-04-21 11:05:50','2026-04-21 11:05:31','2026-04-21 11:05:50');
/*!40000 ALTER TABLE `magic_login_tokens` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2024_01_01_000001_create_users_table',1),(2,'2024_01_01_000002_create_magic_login_tokens_table',1),(3,'2024_01_01_000003_create_audit_logs_table',1),(4,'2024_01_01_000004_create_chat_tables',1),(5,'2024_01_01_000005_create_direct_chat_tables',1),(6,'2026_04_21_000001_add_mobile_to_users_table',2),(7,'2026_04_21_172030_create_personal_access_tokens_table',3);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
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
-- Table structure for table `personal_access_tokens`
--

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
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
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
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `failed_login_attempts` smallint unsigned NOT NULL DEFAULT '0',
  `locked_until` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_email_is_active_index` (`email`,`is_active`),
  KEY `users_role_index` (`role`),
  KEY `users_mobile_index` (`mobile`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Platform Administrator','admin@insurance.ae',NULL,'$2y$12$xwQ/raOmB/M8uYpwZXbb0.N2VL06LStr6.wTvnAeYHpp/jSNt7iJ6',NULL,'admin',1,1,NULL,'2026-04-21 12:08:53','127.0.0.1',0,NULL,'IexYMo9yBsJhhxy6fP4iWaN5MuM4Ze02hoEpLH140sFSfjg7zXwIlUJOoQvu','2026-04-20 16:02:14','2026-04-21 12:08:53',NULL),(2,'Insurance Agent','agent@insurance.ae',NULL,'$2y$12$pQXgkW0IYVjN2NINTQLPne4gnMI3rqbv3mnYFJoHM.OOiE2AM2iDW',NULL,'agent',1,1,NULL,'2026-04-21 12:18:12','127.0.0.1',0,NULL,'L5cg8GLFdtMU9mFMyDIKyqKmu5bF3fxQNem5sWM8Ye46XolNHTbiGitgzAVx','2026-04-20 16:02:14','2026-04-21 12:18:12',NULL),(3,'Compliance Auditor','auditor@insurance.ae',NULL,'$2y$12$hov2GhJ0CgRaLqv5vZ2W3eFQ4ZVctKbAfcoO0HmFyuVdHv2owd322',NULL,'auditor',1,1,NULL,'2026-04-21 01:51:58','127.0.0.1',0,NULL,NULL,'2026-04-20 16:02:14','2026-04-21 01:51:58',NULL),(4,'Sample Customer','sangharshsulke@gmail.com',NULL,NULL,NULL,'customer',1,1,NULL,'2026-04-21 11:05:50','127.0.0.1',0,NULL,NULL,'2026-04-20 16:02:14','2026-04-21 11:05:50',NULL),(5,'Customer #CUST-001','guest_CUST-001@cbuae.internal',NULL,NULL,NULL,'customer',1,0,NULL,NULL,NULL,0,NULL,NULL,'2026-04-20 16:10:47','2026-04-20 16:10:47',NULL),(6,'Customer #4','guest_4@cbuae.internal',NULL,NULL,NULL,'customer',1,0,NULL,NULL,NULL,0,NULL,NULL,'2026-04-21 00:23:44','2026-04-21 00:23:44',NULL),(7,'Ali Hassan','ali@example.com','+971501234567',NULL,NULL,'customer',1,0,NULL,NULL,NULL,0,NULL,NULL,'2026-04-21 12:01:46','2026-04-21 12:01:46',NULL);
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

-- Dump completed on 2026-04-21 19:32:56

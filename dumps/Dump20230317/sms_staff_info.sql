CREATE DATABASE  IF NOT EXISTS `sms` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `sms`;
-- MySQL dump 10.13  Distrib 8.0.20, for Win64 (x86_64)
--
-- Host: localhost    Database: sms
-- ------------------------------------------------------
-- Server version	8.0.20

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
-- Table structure for table `staff_info`
--

DROP TABLE IF EXISTS `staff_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `staff_info` (
  `id` int NOT NULL AUTO_INCREMENT,
  `staff_id` int DEFAULT NULL,
  `position` int DEFAULT NULL,
  `job_title` varchar(45) DEFAULT NULL,
  `ext` int DEFAULT NULL,
  `home_number` varchar(45) DEFAULT NULL,
  `phone_number` varchar(45) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff_info`
--

LOCK TABLES `staff_info` WRITE;
/*!40000 ALTER TABLE `staff_info` DISABLE KEYS */;
INSERT INTO `staff_info` VALUES /* (3,13,1,'General Manager',107,'016-9268979','','sharon@zipkod.com'),(4,9,2,'Sales Manager',102,'+60 11-5173 0480','','surhen@zipkod.com'),(5,6,4,'Programming Team Lead',106,'+60 16-269 6822','','juping@zipkod.com'),(6,5,5,'Program Leader',109,'+(60)19-2828055','','richard@zipkod.com'),(7,11,3,'Software Developer',122,'+60 11-1068 1623','','ares@zipkod.com'); */
(3,2,1,'General Manager',107,'012-3537611','','stephen@sbit.edu.my'),
(4,3,2,'Manager',102,'012-3742331 / 012-3537637','','kit@sbit.edu.my'),
(5,1,3,'Admin and Account Cum Course Coordinator',122,'016-6808076 / 012-3532185','','lim@sbit.edu.my'),
(6,5,5,'Program Leader Cum Lecturer',109,'+(60)19-2828055','','richard@sibt.edu.my'),
(7,4,4,'Training Cum IT Services Manager',106,'016-6134421 / 012-3532185','','david@sbit.edu.my'),
(8,16,4,'Training Cum IT Services Executive',106,'016-2334063','','steven@sbit.edu.my')

/*!40000 ALTER TABLE `staff_info` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-06-14  5:21:32

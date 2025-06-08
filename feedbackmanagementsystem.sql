-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 20, 2024 at 03:29 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `feedbackmanagementsystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `batch`
--

CREATE TABLE `batch` (
  `BatchNo` enum('E19','E20','E21','E22','E23') DEFAULT NULL,
  `Batch_Name` enum('Engineering 2019','Engineering 2020','Engineering 2021','Engineering 2022','Engineering 2023') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `batch`
--

INSERT INTO `batch` (`BatchNo`, `Batch_Name`) VALUES
('E19', 'Engineering 2019'),
('E20', 'Engineering 2020'),
('E21', 'Engineering 2021'),
('E22', 'Engineering 2022'),
('E23', 'Engineering 2023');

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `CourseId` varchar(6) NOT NULL,
  `Course_Name` varchar(150) DEFAULT NULL,
  `Department` enum('Interdisciplinary studies','Computer Engineering','Electrical and Elocronics Engineering','Civil Engineering','Mechanical Engineering') DEFAULT NULL,
  `Semester` enum('1','2','3','4','5','6','7','8') DEFAULT NULL,
  `Credit` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`CourseId`, `Course_Name`, `Department`, `Semester`, `Credit`) VALUES
('EC1050', 'Computing ', 'Computer Engineering', '1', 4),
('EC1060', 'English', 'Interdisciplinary studies', '1', 3),
('EC1070', 'Metrology', 'Civil Engineering', '1', 3),
('EC2040', 'Computer Programming', 'Computer Engineering', '2', 3),
('EC3050', 'Engineering Mechanics', 'Civil Engineering', '3', 3),
('EC5011', 'Digital Signal Processing', 'Electrical and Elocronics Engineering', '5', 4),
('EC5020', 'Control System', 'Electrical and Elocronics Engineering', '5', 4),
('EC5030', 'Analogue and Digital Communication', 'Electrical and Elocronics Engineering', '5', 4),
('EC5070', 'Database', 'Computer Engineering', '5', 4),
('EC5080', 'Software Construction', 'Computer Engineering', '5', 4),
('EC5110', 'Computer Architecture and Organization', 'Computer Engineering', '5', 4),
('MC2030', 'Mechanics of Materials', 'Civil Engineering', '2', 3);

-- --------------------------------------------------------

--
-- Table structure for table `course_feedback`
--

CREATE TABLE `course_feedback` (
  `FeedbackId` int(11) NOT NULL,
  `BatchNo` enum('E19','E20','E21','E22','E23') NOT NULL,
  `CourseId` varchar(6) NOT NULL,
  `CQ01` int(1) NOT NULL,
  `CQ02` int(1) NOT NULL,
  `CQ03` int(1) NOT NULL,
  `CQ04` int(1) NOT NULL,
  `CQ05` int(1) NOT NULL,
  `CQ06` int(1) NOT NULL,
  `CQ07` int(1) NOT NULL,
  `CQ08` int(1) NOT NULL,
  `CQ09` int(1) NOT NULL,
  `CQ10` int(1) NOT NULL,
  `CQ11` int(1) NOT NULL,
  `CQ12` int(1) NOT NULL,
  `CQ13` int(1) NOT NULL,
  `CQ14` int(1) NOT NULL,
  `CQ15` int(1) NOT NULL,
  `comments` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_feedback`
--

INSERT INTO `course_feedback` (`FeedbackId`, `BatchNo`, `CourseId`, `CQ01`, `CQ02`, `CQ03`, `CQ04`, `CQ05`, `CQ06`, `CQ07`, `CQ08`, `CQ09`, `CQ10`, `CQ11`, `CQ12`, `CQ13`, `CQ14`, `CQ15`, `comments`) VALUES
(1, 'E21', 'EC5070', 4, 3, 5, 2, 1, 4, 3, 5, 2, 1, 4, 3, 5, 2, 1, 'Very informative course.'),
(2, 'E21', 'EC5070', 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 'Excellent course, highly recommended!'),
(3, 'E21', 'EC5070', 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 'The instructor was very knowledgeable and helpful.'),
(4, 'E21', 'EC5070', 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 'Enjoyed the practical exercises and hands-on learning.'),
(5, 'E21', 'EC5070', 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 'Clear explanations and relevant course material.'),
(6, 'E21', 'EC5070', 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 'The course content was well-organized and easy to follow.'),
(7, 'E21', 'EC5070', 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 'Enjoyed the interactive sessions and group discussions.'),
(8, 'E21', 'EC5070', 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 'The assignments were challenging and helped in learning.'),
(9, 'E21', 'EC5070', 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 'The course exceeded my expectations.'),
(10, 'E21', 'EC5070', 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 'The course content was well-organized and easy to follow.'),
(11, 'E21', 'EC5070', 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 'Enjoyed the interactive sessions and group discussions.'),
(12, 'E21', 'EC5070', 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 'The assignments were challenging and helped in learning.'),
(13, 'E21', 'EC5070', 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 'The course exceeded my expectations.'),
(14, 'E21', 'EC5011', 3, 4, 2, 5, 1, 3, 4, 2, 5, 1, 3, 4, 2, 5, 1, 'Appreciated the real-world examples.'),
(15, 'E21', 'EC5011', 2, 3, 5, 1, 4, 2, 3, 5, 1, 4, 2, 3, 5, 1, 4, 'Great use of technology in teaching.'),
(16, 'E21', 'EC5011', 1, 5, 4, 3, 2, 1, 5, 4, 3, 2, 1, 5, 4, 3, 2, 'Clear expectations and grading.'),
(17, 'E21', 'EC5020', 5, 1, 3, 2, 4, 5, 1, 3, 2, 4, 5, 1, 3, 2, 4, 'Valuable learning experience.'),
(18, 'E21', 'EC5070', 4, 2, 5, 3, 1, 4, 2, 5, 3, 1, 4, 2, 5, 3, 1, 'Detailed and thorough explanations.'),
(19, 'E21', 'EC5011', 3, 1, 4, 2, 5, 3, 1, 4, 2, 5, 3, 1, 4, 2, 5, 'Excellent course materials.'),
(20, 'E21', 'EC5030', 2, 5, 1, 4, 3, 2, 5, 1, 4, 3, 2, 5, 1, 4, 3, 'Well-paced course delivery.'),
(21, 'E21', 'EC5080', 1, 4, 3, 5, 2, 1, 4, 3, 5, 2, 1, 4, 3, 5, 2, 'Challenging but rewarding.'),
(22, 'E21', 'EC5011', 5, 3, 2, 4, 1, 5, 3, 2, 4, 1, 5, 3, 2, 4, 1, 'Encouraged critical thinking.'),
(23, 'E21', 'EC5011', 4, 5, 1, 3, 2, 4, 5, 1, 3, 2, 4, 5, 1, 3, 2, 'Supportive learning environment.'),
(24, 'E21', 'EC5011', 3, 2, 5, 4, 1, 3, 2, 5, 4, 1, 3, 2, 5, 4, 1, 'Learned a lot from this course.'),
(25, 'E21', 'EC5011', 2, 4, 3, 1, 5, 2, 4, 3, 1, 5, 2, 4, 3, 1, 5, 'Great course overall.'),
(26, 'E21', 'EC5110', 1, 3, 2, 5, 4, 1, 3, 2, 5, 4, 1, 3, 2, 5, 4, 'Course exceeded expectations.'),
(27, 'E21', 'EC5110', 5, 1, 4, 2, 3, 5, 1, 4, 2, 3, 5, 1, 4, 2, 3, 'Would recommend to others.'),
(28, 'E21', 'EC5110', 4, 2, 3, 5, 1, 4, 2, 3, 5, 1, 4, 2, 3, 5, 1, 'Informative and engaging.'),
(29, 'E21', 'EC5110', 3, 5, 1, 4, 2, 3, 5, 1, 4, 2, 3, 5, 1, 4, 2, 'Well-organized curriculum.'),
(30, 'E21', 'EC5110', 2, 4, 5, 3, 1, 2, 4, 5, 3, 1, 2, 4, 5, 3, 1, 'Learned valuable skills.'),
(31, 'E21', 'EC5110', 1, 3, 4, 5, 2, 1, 3, 4, 5, 2, 1, 3, 4, 5, 2, 'Good use of multimedia resources.'),
(32, 'E21', 'EC5070', 5, 2, 3, 1, 4, 5, 2, 3, 1, 4, 5, 2, 3, 1, 4, 'Instructor was very approachable.'),
(67, 'E21', 'EC5070', 4, 3, 5, 2, 1, 4, 3, 5, 2, 1, 4, 3, 5, 2, 1, 'Very informative course.'),
(68, 'E21', 'EC5070', 3, 4, 2, 5, 3, 4, 2, 5, 3, 4, 2, 5, 3, 4, 2, 'Great teaching style.'),
(69, 'E21', 'EC5070', 5, 2, 4, 1, 5, 2, 4, 1, 5, 2, 4, 1, 5, 2, 4, 'Loved the hands-on projects.'),
(70, 'E21', 'EC5070', 2, 5, 1, 3, 4, 2, 5, 1, 3, 4, 2, 5, 1, 3, 4, 'Assignments were very challenging.'),
(71, 'E21', 'EC5070', 1, 3, 2, 4, 5, 1, 3, 2, 4, 5, 1, 3, 2, 4, 5, 'Clear and concise lectures.'),
(72, 'E21', 'EC5070', 4, 1, 5, 3, 2, 4, 1, 5, 3, 2, 4, 1, 5, 3, 2, 'Instructor was very knowledgeable.'),
(73, 'E21', 'EC5070', 3, 2, 4, 5, 1, 3, 2, 4, 5, 1, 3, 2, 4, 5, 1, 'Well-structured course material.'),
(74, 'E21', 'EC5030', 5, 3, 1, 2, 4, 5, 3, 1, 2, 4, 5, 3, 1, 2, 4, 'Interesting topics covered.'),
(75, 'E21', 'EC5030', 2, 5, 1, 4, 3, 2, 5, 1, 4, 3, 2, 5, 1, 4, 3, 'Well-paced course delivery.'),
(76, 'E21', 'EC5030', 5, 3, 1, 2, 4, 5, 3, 1, 2, 4, 5, 3, 1, 2, 4, 'Interesting topics covered.'),
(77, 'E21', 'EC5030', 2, 5, 1, 4, 3, 2, 5, 1, 4, 3, 2, 5, 1, 4, 3, 'Well-paced course delivery.'),
(78, 'E21', 'EC5030', 5, 3, 1, 2, 4, 5, 3, 1, 2, 4, 5, 3, 1, 2, 4, 'Interesting topics covered.'),
(79, 'E21', 'EC5030', 2, 5, 1, 4, 3, 2, 5, 1, 4, 3, 2, 5, 1, 4, 3, 'Well-paced course delivery.'),
(80, 'E21', 'EC5030', 2, 5, 1, 4, 3, 2, 5, 1, 4, 3, 2, 5, 1, 4, 3, 'Well-paced course delivery.'),
(81, 'E21', 'EC5030', 5, 3, 1, 2, 4, 5, 3, 1, 2, 4, 5, 3, 1, 2, 4, 'Interesting topics covered.'),
(82, 'E21', 'EC5030', 2, 5, 1, 4, 3, 2, 5, 1, 4, 3, 2, 5, 1, 4, 3, 'Well-paced course delivery.'),
(83, 'E21', 'EC5030', 5, 3, 1, 2, 4, 5, 3, 1, 2, 4, 5, 3, 1, 2, 4, 'Interesting topics covered.'),
(84, 'E21', 'EC5030', 2, 5, 1, 4, 3, 2, 5, 1, 4, 3, 2, 5, 1, 4, 3, 'Well-paced course delivery.'),
(85, 'E21', 'EC5030', 5, 3, 1, 2, 4, 5, 3, 1, 2, 4, 5, 3, 1, 2, 4, 'Interesting topics covered.'),
(86, 'E21', 'EC5030', 2, 5, 1, 4, 3, 2, 5, 1, 4, 3, 2, 5, 1, 4, 3, 'Well-paced course delivery.'),
(87, 'E21', 'EC5011', 3, 4, 2, 5, 1, 3, 4, 2, 5, 1, 3, 4, 2, 5, 1, 'Appreciated the real-world examples.'),
(88, 'E21', 'EC5011', 2, 3, 5, 1, 4, 2, 3, 5, 1, 4, 2, 3, 5, 1, 4, 'Great use of technology in teaching.'),
(89, 'E21', 'EC5011', 1, 5, 4, 3, 2, 1, 5, 4, 3, 2, 1, 5, 4, 3, 2, 'Clear expectations and grading.'),
(90, 'E21', 'EC5011', 3, 1, 4, 2, 5, 3, 1, 4, 2, 5, 3, 1, 4, 2, 5, 'Excellent course materials.'),
(91, 'E21', 'EC5011', 5, 3, 2, 4, 1, 5, 3, 2, 4, 1, 5, 3, 2, 4, 1, 'Encouraged critical thinking.'),
(92, 'E21', 'EC5011', 4, 5, 1, 3, 2, 4, 5, 1, 3, 2, 4, 5, 1, 3, 2, 'Supportive learning environment.'),
(93, 'E21', 'EC5011', 3, 2, 5, 4, 1, 3, 2, 5, 4, 1, 3, 2, 5, 4, 1, 'Learned a lot from this course.'),
(94, 'E21', 'EC5011', 2, 4, 3, 1, 5, 2, 4, 3, 1, 5, 2, 4, 3, 1, 5, 'Great course overall.'),
(95, 'E21', 'EC5110', 1, 3, 2, 5, 4, 1, 3, 2, 5, 4, 1, 3, 2, 5, 4, 'Course exceeded expectations.'),
(96, 'E21', 'EC5110', 5, 1, 4, 2, 3, 5, 1, 4, 2, 3, 5, 1, 4, 2, 3, 'Would recommend to others.'),
(97, 'E21', 'EC5110', 4, 2, 3, 5, 1, 4, 2, 3, 5, 1, 4, 2, 3, 5, 1, 'Informative and engaging.'),
(98, 'E21', 'EC5110', 3, 5, 1, 4, 2, 3, 5, 1, 4, 2, 3, 5, 1, 4, 2, 'Well-organized curriculum.'),
(99, 'E21', 'EC5110', 2, 4, 5, 3, 1, 2, 4, 5, 3, 1, 2, 4, 5, 3, 1, 'Learned valuable skills.'),
(100, 'E21', 'EC5110', 1, 3, 4, 5, 2, 1, 3, 4, 5, 2, 1, 3, 4, 5, 2, 'Good use of multimedia resources.'),
(101, 'E21', 'EC5080', 4, 3, 5, 2, 1, 4, 3, 5, 2, 1, 4, 3, 5, 2, 1, 'Very informative course.'),
(102, 'E21', 'EC5080', 3, 4, 2, 5, 3, 4, 2, 5, 3, 4, 2, 5, 3, 4, 2, 'Great teaching style.'),
(103, 'E21', 'EC5080', 5, 2, 4, 1, 5, 2, 4, 1, 5, 2, 4, 1, 5, 2, 4, 'Loved the hands-on projects.'),
(104, 'E21', 'EC5080', 2, 5, 1, 3, 4, 2, 5, 1, 3, 4, 2, 5, 1, 3, 4, 'Assignments were very challenging.'),
(105, 'E21', 'EC5080', 1, 3, 2, 4, 5, 1, 3, 2, 4, 5, 1, 3, 2, 4, 5, 'Clear and concise lectures.'),
(106, 'E21', 'EC5080', 4, 1, 5, 3, 2, 4, 1, 5, 3, 2, 4, 1, 5, 3, 2, 'Instructor was very knowledgeable.'),
(107, 'E21', 'EC5080', 3, 2, 4, 5, 1, 3, 2, 4, 5, 1, 3, 2, 4, 5, 1, 'Well-structured course material.'),
(108, 'E21', 'EC5080', 4, 5, 2, 3, 1, 4, 5, 2, 3, 1, 4, 5, 2, 3, 1, 'Enjoyed the group discussions.'),
(109, 'E21', 'EC5080', 3, 1, 4, 5, 2, 3, 1, 4, 5, 2, 3, 1, 4, 5, 2, 'Very engaging instructor.'),
(110, 'E21', 'EC5080', 2, 4, 5, 1, 3, 2, 4, 5, 1, 3, 2, 4, 5, 1, 3, 'Good balance of theory and practice.'),
(111, 'E21', 'EC5080', 1, 5, 3, 4, 2, 1, 5, 3, 4, 2, 1, 5, 3, 4, 2, 'Helpful feedback on assignments.'),
(112, 'E21', 'EC5080', 4, 2, 1, 5, 3, 4, 2, 1, 5, 3, 4, 2, 1, 5, 3, 'Well-organized course content.'),
(113, 'E21', 'EC5011', 5, 5, 5, 4, 4, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 'good'),
(114, 'E19', 'EC5020', 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 'good'),
(115, 'E19', 'EC5011', 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 'good'),
(116, 'E19', 'EC5011', 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 'good'),
(117, 'E19', 'EC5020', 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 'thank you'),
(118, 'E21', 'EC5030', 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 'good'),
(119, 'E21', 'EC5011', 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 'good'),
(120, 'E21', 'EC5011', 5, 4, 4, 5, 5, 5, 5, 5, 5, 5, 5, 5, 4, 5, 5, 'good'),
(121, 'E21', 'EC5070', 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 'Excellent course, highly recommended!'),
(122, 'E21', 'EC5070', 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 'The instructor was very knowledgeable and helpful.'),
(123, 'E21', 'EC5070', 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 'Enjoyed the practical exercises and hands-on learning.'),
(124, 'E21', 'EC5070', 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 'Clear explanations and relevant course material.'),
(125, 'E21', 'EC5070', 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 'The course content was well-organized and easy to follow.'),
(126, 'E21', 'EC5070', 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 'Enjoyed the interactive sessions and group discussions.'),
(127, 'E21', 'EC5070', 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 'The assignments were challenging and helped in learning.'),
(128, 'E21', 'EC5070', 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 'The course exceeded my expectations.'),
(129, 'E21', 'EC5070', 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 'Very informative and practical course.'),
(130, 'E21', 'EC5070', 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 'The instructor provided valuable insights.'),
(131, 'E21', 'EC5070', 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 'Enjoyed the real-world examples and case studies.'),
(132, 'E21', 'EC5070', 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 'The course materials were comprehensive and useful.'),
(133, 'E21', 'EC5070', 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 'Helpful instructor and responsive to questions.'),
(134, 'E21', 'EC5070', 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 'The course schedule was well-paced and manageable.'),
(135, 'E21', 'EC5070', 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 5, 4, 'Great learning experience overall.'),
(136, 'E21', 'EC5070', 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 'good'),
(137, 'E21', 'EC5080', 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 'thank you'),
(138, 'E21', 'EC5080', 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 'good'),
(139, 'E21', 'EC5080', 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 'good'),
(140, 'E21', 'EC5070', 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 'good'),
(141, 'E21', 'EC5070', 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 'good'),
(142, 'E21', 'EC5070', 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 'good'),
(143, 'E21', 'EC5070', 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 'good'),
(144, 'E21', 'EC5080', 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 'thank you');

-- --------------------------------------------------------

--
-- Table structure for table `course_feedback_contains`
--

CREATE TABLE `course_feedback_contains` (
  `QueId` varchar(4) NOT NULL,
  `QueType` varchar(100) NOT NULL,
  `QueText` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_feedback_contains`
--

INSERT INTO `course_feedback_contains` (`QueId`, `QueType`, `QueText`) VALUES
('CQ01', 'Course', 'This course helped me to enhance my knowledge?'),
('CQ02', 'Course', 'The workload of the course was manageable?'),
('CQ03', 'Course', 'The course was interesting?'),
('CQ04', 'Materials', 'Adequate Materials (handouts) were provided?'),
('CQ05', 'Materials', 'Handouts were easy to understand?'),
('CQ06', 'Materials', 'Enough reference books were used?'),
('CQ07', 'Tutorials/ Examples', 'Given problems (examples/ tutorials/ exercises) were enough?'),
('CQ08', 'Tutorials/ Examples', 'Given problems (examples/ tutorials/ exercises) were challenging?'),
('CQ09', 'Lab/ Fieldwork', 'I could relate what I learnt from lectures to lab/ field classes?'),
('CQ10', 'Lab/ Fieldwork', 'Labs & Fieldwork helped to improve my skills and practical knowledge?'),
('CQ11', 'Lab/ Fieldwork', 'I can conduct experiments/ fieldwork myself through set of instructions in future?'),
('CQ12', 'About Myself', 'I prepared thoroughly for each class?'),
('CQ13', 'About Myself', 'I attended lectures, lab/fieldwork regularly?'),
('CQ14', 'About Myself', 'I did all assigned work (homework/ assignments/ lab & field report) on time?'),
('CQ15', 'About Myself', 'I did all assigned work (homework/ assignments/ lab & field report) on time?'),
('CQ16', 'Comments', 'Any other comments?');

-- --------------------------------------------------------

--
-- Table structure for table `course_feedback_notice`
--

CREATE TABLE `course_feedback_notice` (
  `CourseId` varchar(6) NOT NULL,
  `Allow` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_feedback_notice`
--

INSERT INTO `course_feedback_notice` (`CourseId`, `Allow`) VALUES
('EC1050', 1),
('EC1060', 1),
('EC1070', 1),
('EC2040', 1),
('EC3050', 1),
('EC3070', 1),
('EC5011', 1),
('EC5020', 1),
('EC5030', 1),
('EC5070', 1),
('EC5080', 1),
('EC5110', 0),
('MC2030', 1);

-- --------------------------------------------------------

--
-- Table structure for table `enroll`
--

CREATE TABLE `enroll` (
  `RegNo` varchar(8) NOT NULL,
  `CourseId` varchar(6) NOT NULL,
  `AY` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enroll`
--

INSERT INTO `enroll` (`RegNo`, `CourseId`, `AY`) VALUES
('2020E001', 'EC5070', '22/23'),
('2021E016', 'EC1050', '22/23'),
('2021E016', 'EC3050', '22/23'),
('2021E016', 'EC5011', '23/24'),
('2021E016', 'EC5020', '23/24'),
('2021E016', 'EC5030', '23/24'),
('2021E024', 'EC3050', '22/23'),
('2021E024', 'EC5030', '23/24'),
('2021E024', 'EC5070', '23/24'),
('2021E024', 'EC5110', '23/24'),
('2021E025', 'EC1050', '22/23'),
('2021E025', 'EC3050', '22/23'),
('2021E025', 'EC5011', '23/24'),
('2021E025', 'EC5020', '23/24'),
('2021E025', 'EC5030', '23/24'),
('2021E025', 'EC5110', '22/23'),
('2021E025', 'EC5110', '23/24'),
('2021E059', 'EC1050', '22/23'),
('2021E059', 'EC5020', '23/24'),
('2021E059', 'EC5110', '23/24'),
('2021E064', 'EC5011', '23/24'),
('2021E064', 'EC5020', '23/24'),
('2021E064', 'EC5030', '23/24'),
('2021E064', 'EC5070', '23/24'),
('2021E064', 'EC5080', '23/24'),
('2021E064', 'EC5110', '23/24'),
('2021E065', 'EC5011', '23/24'),
('2021E066', 'EC1050', '22/23'),
('2021E066', 'EC3050', '22/23'),
('2021E066', 'EC5011', '23/24'),
('2021E066', 'EC5020', '23/24'),
('2021E066', 'EC5030', '23/24'),
('2021E066', 'EC5110', '22/23'),
('2021E066', 'EC5110', '23/24'),
('2021E094', 'EC1050', '22/23'),
('2021E094', 'EC3050', '22/23'),
('2021E094', 'EC5011', '23/24'),
('2021E094', 'EC5020', '23/24'),
('2021E094', 'EC5030', '23/24'),
('2021E094', 'EC5110', '23/24'),
('2021E095', 'EC1050', '22/23'),
('2021E095', 'EC3050', '22/23'),
('2021E095', 'EC5011', '23/24'),
('2021E095', 'EC5020', '23/24'),
('2021E095', 'EC5030', '23/24'),
('2021E095', 'EC5110', '22/23'),
('2021E095', 'EC5110', '23/24'),
('2021E112', 'CE1060', '23/24'),
('2021E112', 'EC1050', '22/23'),
('2021E112', 'EC3050', '22/23'),
('2021E112', 'EC5011', '23/24'),
('2021E112', 'EC5020', '23/24'),
('2021E112', 'EC5030', '23/24'),
('2021E112', 'EC5070', '23/24'),
('2021E112', 'EC5080', '23/24'),
('2021E112', 'EC5110', '22/23'),
('2021E112', 'EC5110', '23/24'),
('2021E190', 'EC1050', '22/23'),
('2021E190', 'EC3050', '22/23'),
('2021E190', 'EC5011', '23/24'),
('2021E190', 'EC5020', '23/24'),
('2021E190', 'EC5030', '23/24'),
('2021E190', 'EC5110', '22/23'),
('2021E190', 'EC5110', '23/24'),
('2022E001', 'EC1060', '22/23'),
('2023E001', 'EC1050', '23/24'),
('2023E001', 'EC1060', '23/24'),
('2023E001', 'EC3050', '23/24'),
('2023E001', 'MC2030', '23/24'),
('2023E002', 'EC1050', '23/24'),
('2023E002', 'EC1060', '23/24'),
('2023E002', 'EC3050', '23/24'),
('2023E002', 'MC2030', '23/24'),
('2023E003', 'EC1060', '23/24'),
('2023E003', 'EC3050', '23/24');

-- --------------------------------------------------------

--
-- Table structure for table `gives_course_feedback`
--

CREATE TABLE `gives_course_feedback` (
  `BatchNo` enum('E19','E20','E21','E22','E23') NOT NULL,
  `RegNo` varchar(8) NOT NULL,
  `CourseId` varchar(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gives_course_feedback`
--

INSERT INTO `gives_course_feedback` (`BatchNo`, `RegNo`, `CourseId`) VALUES
('E20', '2021E006', 'EC5070'),
('E21', '2021E011', 'EC5070'),
('E21', '2021E025', 'EC5070'),
('E21', '2021E062', 'EC5070'),
('E21', '2021E064', 'EC5070'),
('E21', '2021E105', 'EC5110');

-- --------------------------------------------------------

--
-- Table structure for table `gives_lecturer_feedback`
--

CREATE TABLE `gives_lecturer_feedback` (
  `BatchNo` enum('E19','E20','E21','E22','E23') NOT NULL,
  `RegNo` varchar(8) NOT NULL,
  `CourseId` varchar(6) NOT NULL,
  `LecturerId` varchar(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lecturer`
--

CREATE TABLE `lecturer` (
  `LecturerId` varchar(8) NOT NULL,
  `Lecturer_Name` varchar(150) DEFAULT NULL,
  `Department` enum('Interdisciplinary studies','Computer Engineering','Civil Engineering','Mechanical Engineering','Electrical and Electronics Engineering') DEFAULT NULL,
  `Email` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lecturer`
--

INSERT INTO `lecturer` (`LecturerId`, `Lecturer_Name`, `Department`, `Email`) VALUES
('CEE11211', 'Prof.S.Sathiparan', 'Civil Engineering', 'sathiparan@eng.jfn.ac.lk'),
('COM11111', 'Dr.S.Jananie', 'Computer Engineering', 'jananie@eng.jfn.ac.lk'),
('COM11117', 'Eng.M.Sujanthika', 'Computer Engineering', 'sujanthiha@eng.jfn.ac.lk'),
('COM11119', 'Eng.Y.Pirunthapan', 'Computer Engineering', 'pirunthapany@eng.jfn.ac.lk'),
('EEE12110', 'Prof.K.Ahilan', 'Electrical and Electronics Engineering', 'ahilan@eng.jfn.ac.lk');

-- --------------------------------------------------------

--
-- Table structure for table `lecturer_feedback`
--

CREATE TABLE `lecturer_feedback` (
  `FeedbackId` int(11) NOT NULL,
  `BatchNo` enum('E19','E20','E21','E22','E23') NOT NULL,
  `LecturerId` varchar(8) NOT NULL,
  `CourseId` varchar(6) NOT NULL,
  `LQ01` int(1) NOT NULL,
  `LQ02` int(1) NOT NULL,
  `LQ03` int(1) NOT NULL,
  `LQ04` int(1) NOT NULL,
  `LQ05` int(1) NOT NULL,
  `LQ06` int(1) NOT NULL,
  `LQ07` int(1) NOT NULL,
  `LQ08` int(1) NOT NULL,
  `LQ09` int(1) NOT NULL,
  `LQ10` int(1) NOT NULL,
  `LQ11` int(1) NOT NULL,
  `LQ12` int(1) NOT NULL,
  `Comments` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lecturer_feedback`
--

INSERT INTO `lecturer_feedback` (`FeedbackId`, `BatchNo`, `LecturerId`, `CourseId`, `LQ01`, `LQ02`, `LQ03`, `LQ04`, `LQ05`, `LQ06`, `LQ07`, `LQ08`, `LQ09`, `LQ10`, `LQ11`, `LQ12`, `Comments`) VALUES
(1, 'E21', 'COM11112', 'EC5011', 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 2, 'Thank you'),
(2, 'E21', 'COM11112', 'EC5011', 4, 3, 2, 5, 4, 3, 2, 1, 4, 3, 2, 5, 'Good'),
(3, 'E21', 'COM11112', 'EC5011', 3, 2, 1, 4, 3, 2, 5, 4, 3, 2, 1, 4, 'Average'),
(4, 'E21', 'COM11112', 'EC5011', 2, 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 'Needs improvement'),
(5, 'E21', 'COM11112', 'EC5011', 1, 4, 3, 2, 5, 4, 3, 2, 1, 4, 3, 2, 'Not bad'),
(6, 'E21', 'COM11112', 'EC5011', 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 2, 1, 'Good job'),
(7, 'E21', 'COM11112', 'EC5011', 3, 2, 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 'Excellent'),
(8, 'E21', 'COM11112', 'EC5011', 2, 1, 4, 3, 2, 5, 4, 3, 2, 1, 4, 3, 'Very good'),
(9, 'E21', 'COM11112', 'EC5011', 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 2, 'Awesome'),
(10, 'E21', 'COM11112', 'EC5011', 1, 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 'Nice'),
(11, 'E21', 'COM11112', 'EC5011', 4, 3, 2, 1, 5, 4, 3, 2, 1, 4, 3, 2, 'Well done'),
(12, 'E21', 'COM11112', 'EC5011', 3, 2, 1, 4, 3, 2, 5, 4, 3, 2, 1, 4, 'Keep it up'),
(13, 'E21', 'COM11112', 'EC5011', 2, 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 'Impressive'),
(14, 'E21', 'COM11112', 'EC5011', 1, 4, 3, 2, 5, 4, 3, 2, 1, 4, 3, 2, 'Fantastic'),
(15, 'E21', 'COM11112', 'EC5011', 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 2, 1, 'Great work'),
(16, 'E21', 'COM11112', 'EC5011', 3, 2, 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 'Highly recommended'),
(17, 'E21', 'COM11112', 'EC5011', 2, 1, 4, 3, 2, 5, 4, 3, 2, 1, 4, 3, 'Well executed'),
(18, 'E21', 'COM11112', 'EC5011', 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 2, 'Superb'),
(19, 'E21', 'COM11112', 'EC5011', 1, 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 'Splendid'),
(20, 'E21', 'COM11112', 'EC5011', 4, 3, 2, 1, 5, 4, 3, 2, 1, 4, 3, 2, 'Magnificent'),
(21, 'E21', 'EEE12110', 'EC5020', 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 2, 'Thank you'),
(22, 'E21', 'EEE12110', 'EC5020', 4, 3, 2, 5, 4, 3, 2, 1, 4, 3, 2, 5, 'Good'),
(23, 'E21', 'EEE12110', 'EC5020', 3, 2, 1, 4, 3, 2, 5, 4, 3, 2, 1, 4, 'Average'),
(24, 'E21', 'EEE12110', 'EC5020', 2, 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 'Needs improvement'),
(25, 'E21', 'EEE12110', 'EC5020', 1, 4, 3, 2, 5, 4, 3, 2, 1, 4, 3, 2, 'Not bad'),
(26, 'E21', 'EEE12110', 'EC5020', 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 2, 1, 'Good job'),
(27, 'E21', 'EEE12110', 'EC5020', 3, 2, 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 'Excellent'),
(28, 'E21', 'EEE12110', 'EC5020', 2, 1, 4, 3, 2, 5, 4, 3, 2, 1, 4, 3, 'Very good'),
(29, 'E21', 'EEE12110', 'EC5020', 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 2, 'Awesome'),
(30, 'E21', 'EEE12110', 'EC5020', 1, 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 'Nice'),
(31, 'E21', 'EEE12110', 'EC5020', 4, 3, 2, 1, 5, 4, 3, 2, 1, 4, 3, 2, 'Well done'),
(32, 'E21', 'EEE12110', 'EC5020', 3, 2, 1, 4, 3, 2, 5, 4, 3, 2, 1, 4, 'Keep it up'),
(33, 'E21', 'EEE12110', 'EC5020', 2, 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 'Impressive'),
(34, 'E21', 'EEE12110', 'EC5020', 1, 4, 3, 2, 5, 4, 3, 2, 1, 4, 3, 2, 'Fantastic'),
(35, 'E21', 'EEE12110', 'EC5020', 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 2, 1, 'Great work'),
(36, 'E21', 'EEE12110', 'EC5020', 3, 2, 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 'Highly recommended'),
(37, 'E21', 'EEE12110', 'EC5020', 2, 1, 4, 3, 2, 5, 4, 3, 2, 1, 4, 3, 'Well executed'),
(38, 'E21', 'EEE12110', 'EC5020', 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 2, 'Superb'),
(39, 'E21', 'EEE12110', 'EC5020', 1, 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 'Splendid'),
(40, 'E21', 'EEE12110', 'EC5020', 4, 3, 2, 1, 5, 4, 3, 2, 1, 4, 3, 2, 'Magnificent'),
(41, 'E21', 'COM11112', 'EC5030', 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 2, 'Thank you'),
(42, 'E21', 'COM11112', 'EC5030', 4, 3, 2, 5, 4, 3, 2, 1, 4, 3, 2, 5, 'Good'),
(43, 'E21', 'COM11112', 'EC5030', 3, 2, 1, 4, 3, 2, 5, 4, 3, 2, 1, 4, 'Average'),
(44, 'E21', 'COM11112', 'EC5030', 2, 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 'Needs improvement'),
(45, 'E21', 'COM11112', 'EC5030', 1, 4, 3, 2, 5, 4, 3, 2, 1, 4, 3, 2, 'Not bad'),
(46, 'E21', 'COM11112', 'EC5030', 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 2, 1, 'Good job'),
(47, 'E21', 'COM11112', 'EC5030', 3, 2, 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 'Excellent'),
(48, 'E21', 'COM11112', 'EC5030', 2, 1, 4, 3, 2, 5, 4, 3, 2, 1, 4, 3, 'Very good'),
(49, 'E21', 'COM11112', 'EC5030', 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 2, 'Awesome'),
(50, 'E21', 'COM11112', 'EC5030', 1, 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 'Nice'),
(51, 'E21', 'COM11112', 'EC5030', 4, 3, 2, 1, 5, 4, 3, 2, 1, 4, 3, 2, 'Well done'),
(52, 'E21', 'COM11112', 'EC5030', 3, 2, 1, 4, 3, 2, 5, 4, 3, 2, 1, 4, 'Keep it up'),
(53, 'E21', 'COM11112', 'EC5030', 2, 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 'Impressive'),
(54, 'E21', 'COM11112', 'EC5030', 1, 4, 3, 2, 5, 4, 3, 2, 1, 4, 3, 2, 'Fantastic'),
(55, 'E21', 'COM11112', 'EC5030', 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 2, 1, 'Great work'),
(56, 'E21', 'COM11112', 'EC5030', 3, 2, 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 'Highly recommended'),
(57, 'E21', 'COM11112', 'EC5030', 2, 1, 4, 3, 2, 5, 4, 3, 2, 1, 4, 3, 'Well executed'),
(58, 'E21', 'COM11112', 'EC5030', 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 2, 'Superb'),
(59, 'E21', 'COM11112', 'EC5030', 1, 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 'Splendid'),
(60, 'E21', 'COM11112', 'EC5030', 4, 3, 2, 1, 5, 4, 3, 2, 1, 4, 3, 2, 'Magnificent'),
(61, 'E21', 'COM11111', 'EC5070', 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 'super'),
(62, 'E21', 'COM11111', 'EC5070', 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 'super'),
(63, 'E21', 'COM11111', 'EC5070', 5, 5, 4, 5, 5, 5, 5, 5, 5, 5, 5, 5, 'super'),
(64, 'E21', 'COM11111', 'EC5070', 5, 5, 4, 5, 5, 5, 5, 4, 4, 4, 4, 4, 'good'),
(65, 'E21', 'COM11111', 'EC5070', 5, 5, 5, 3, 3, 3, 3, 3, 3, 3, 3, 3, 'super'),
(66, 'E21', 'COM11111', 'EC5070', 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 'super'),
(67, 'E21', 'COM11111', 'EC5070', 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 'super'),
(68, 'E21', 'COM11111', 'EC5070', 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 'all good'),
(69, 'E21', 'COM11111', 'EC5070', 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 'super'),
(70, 'E21', 'COM11111', 'EC5070', 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 'super'),
(71, 'E21', 'COM11111', 'EC5070', 5, 5, 4, 5, 5, 5, 5, 5, 5, 5, 5, 5, 'super'),
(72, 'E21', 'COM11111', 'EC5070', 5, 5, 4, 5, 5, 5, 5, 4, 4, 4, 4, 4, 'good'),
(73, 'E21', 'COM11111', 'EC5070', 5, 5, 5, 3, 3, 3, 3, 3, 3, 3, 3, 3, 'super'),
(74, 'E21', 'COM11111', 'EC5070', 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, ''),
(76, 'E21', 'COM11111', 'EC5070', 3, 2, 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 'Highly recommended'),
(77, 'E21', 'COM11111', 'EC5070', 2, 1, 4, 3, 2, 5, 4, 3, 2, 1, 4, 3, 'Well executed'),
(78, 'E21', 'COM11111', 'EC5070', 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 2, 'Superb'),
(79, 'E21', 'COM11111', 'EC5070', 1, 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 'Splendid'),
(80, 'E21', 'COM11111', 'EC5070', 4, 3, 2, 1, 5, 4, 3, 2, 1, 4, 3, 2, 'Magnificent'),
(81, 'E21', 'COM11117', 'EC5080', 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 2, 'Thank you'),
(82, 'E21', 'COM11117', 'EC5080', 4, 3, 2, 5, 4, 3, 2, 1, 4, 3, 2, 5, 'Good'),
(83, 'E21', 'COM11117', 'EC5080', 3, 2, 1, 4, 3, 2, 5, 4, 3, 2, 1, 4, 'Average'),
(84, 'E21', 'COM11117', 'EC5080', 2, 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 'Needs improvement'),
(85, 'E21', 'COM11117', 'EC5080', 1, 4, 3, 2, 5, 4, 3, 2, 1, 4, 3, 2, 'Not bad'),
(86, 'E21', 'COM11117', 'EC5080', 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 2, 1, 'Good job'),
(87, 'E21', 'COM11117', 'EC5080', 3, 2, 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 'Excellent'),
(88, 'E21', 'COM11117', 'EC5080', 2, 1, 4, 3, 2, 5, 4, 3, 2, 1, 4, 3, 'Very good'),
(89, 'E21', 'COM11117', 'EC5080', 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 2, 'Awesome'),
(90, 'E21', 'COM11117', 'EC5080', 1, 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 'Nice'),
(91, 'E21', 'COM11117', 'EC5080', 4, 3, 2, 1, 5, 4, 3, 2, 1, 4, 3, 2, 'Well done'),
(92, 'E21', 'COM11117', 'EC5080', 3, 2, 1, 4, 3, 2, 5, 4, 3, 2, 1, 4, 'Keep it up'),
(93, 'E21', 'COM11117', 'EC5080', 2, 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 'Impressive'),
(94, 'E21', 'COM11117', 'EC5080', 1, 4, 3, 2, 5, 4, 3, 2, 1, 4, 3, 2, 'Fantastic'),
(95, 'E21', 'COM11117', 'EC5080', 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 2, 1, 'Great work'),
(96, 'E21', 'COM11117', 'EC5080', 3, 2, 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 'Highly recommended'),
(97, 'E21', 'COM11117', 'EC5080', 2, 1, 4, 3, 2, 5, 4, 3, 2, 1, 4, 3, 'Well executed'),
(98, 'E21', 'COM11117', 'EC5080', 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 2, 'Superb'),
(99, 'E21', 'COM11117', 'EC5080', 1, 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 'Splendid'),
(100, 'E21', 'COM11117', 'EC5080', 4, 3, 2, 1, 5, 4, 3, 2, 1, 4, 3, 2, 'Magnificent'),
(101, 'E21', 'COM11119', 'EC5110', 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 2, 'Thank you'),
(102, 'E21', 'COM11119', 'EC5110', 4, 3, 2, 5, 4, 3, 2, 1, 4, 3, 2, 5, 'Good'),
(103, 'E21', 'COM11119', 'EC5110', 3, 2, 1, 4, 3, 2, 5, 4, 3, 2, 1, 4, 'Average'),
(104, 'E21', 'COM11119', 'EC5110', 2, 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 'Needs improvement'),
(105, 'E21', 'COM11119', 'EC5110', 1, 4, 3, 2, 5, 4, 3, 2, 1, 4, 3, 2, 'Not bad'),
(106, 'E21', 'COM11119', 'EC5110', 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 2, 1, 'Good job'),
(107, 'E21', 'COM11119', 'EC5110', 3, 2, 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 'Excellent'),
(108, 'E21', 'COM11119', 'EC5110', 2, 1, 4, 3, 2, 5, 4, 3, 2, 1, 4, 3, 'Very good'),
(109, 'E21', 'COM11119', 'EC5110', 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 2, 'Awesome'),
(110, 'E21', 'COM11119', 'EC5110', 1, 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 'Nice'),
(111, 'E21', 'COM11119', 'EC5110', 4, 3, 2, 1, 5, 4, 3, 2, 1, 4, 3, 2, 'Well done'),
(112, 'E21', 'COM11119', 'EC5110', 3, 2, 1, 4, 3, 2, 5, 4, 3, 2, 1, 4, 'Keep it up'),
(113, 'E21', 'COM11119', 'EC5110', 2, 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 'Impressive'),
(114, 'E21', 'COM11119', 'EC5110', 1, 4, 3, 2, 5, 4, 3, 2, 1, 4, 3, 2, 'Fantastic'),
(115, 'E21', 'COM11119', 'EC5110', 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 2, 1, 'Great work'),
(116, 'E21', 'COM11119', 'EC5110', 3, 2, 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 'Highly recommended'),
(117, 'E21', 'COM11119', 'EC5110', 2, 1, 4, 3, 2, 5, 4, 3, 2, 1, 4, 3, 'Well executed'),
(118, 'E21', 'COM11119', 'EC5110', 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 2, 'Superb'),
(119, 'E21', 'COM11119', 'EC5110', 1, 5, 4, 3, 2, 1, 4, 3, 2, 5, 4, 3, 'Splendid'),
(120, 'E21', 'COM11119', 'EC5110', 4, 3, 2, 1, 5, 4, 3, 2, 1, 4, 3, 2, 'Magnificent'),
(134, 'E21', 'EEE12110', 'EC5020', 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 'super'),
(143, 'E21', 'COM11111', 'EC5070', 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 'super'),
(144, 'E21', 'COM11111', 'EC5080', 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 'good'),
(145, 'E21', 'COM11117', 'EC5080', 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 'nothing');

-- --------------------------------------------------------

--
-- Table structure for table `lecturer_feedback_contains`
--

CREATE TABLE `lecturer_feedback_contains` (
  `QueId` varchar(4) NOT NULL,
  `QueType` varchar(100) NOT NULL,
  `QueText` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lecturer_feedback_contains`
--

INSERT INTO `lecturer_feedback_contains` (`QueId`, `QueType`, `QueText`) VALUES
('LQ01', 'Time Management', 'Lectures/ Labs/ Fieldworks started and finished on time?'),
('LQ02', 'Time Management', 'The lecturer managed class time effectively?'),
('LQ03', 'Time Management', 'The lecturer was readily available for consultation with students?'),
('LQ04', 'Delivery Method', 'Use of teaching aids (multimedia, white board)?'),
('LQ05', 'Delivery Method', 'Lectures were easy to understand?'),
('LQ06', 'Delivery Method', 'The lecturer encouraged students to participate in discussions?'),
('LQ07', 'Subject Command', 'The lecturer focused on syllabus?'),
('LQ08', 'Subject Command', 'The lecturer was self-confident in subject and teaching?'),
('LQ09', 'Subject Command', 'The lecturer linked real-world applications and creating interest in the subject?'),
('LQ10', 'Subject Command', 'The lecturer updated latest development in the field?'),
('LQ11', 'About Myself', 'I asked questions from the lecturer in the class?'),
('LQ12', 'About Myself', 'I consulted with the lecturer after lecture hours?'),
('LQ13', 'Comments', 'Any other comments?');

-- --------------------------------------------------------

--
-- Table structure for table `lecturer_feedback_notice`
--

CREATE TABLE `lecturer_feedback_notice` (
  `CourseId` varchar(6) NOT NULL,
  `AY` varchar(5) NOT NULL,
  `Allow` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lecturer_feedback_notice`
--

INSERT INTO `lecturer_feedback_notice` (`CourseId`, `AY`, `Allow`) VALUES
('EC1050', '23/24', 0),
('EC1060', '22/23', 1),
('EC1060', '23/24', 0),
('EC1070', '23/24', 1),
('EC2040', '23/24', 1),
('EC3050', '23/24', 1),
('EC3070', '23/24', 1),
('EC5011', '23/24', 1),
('EC5020', '23/24', 1),
('EC5070', '23/24', 1),
('EC5080', '23/24', 1);

-- --------------------------------------------------------

--
-- Table structure for table `management_assistant`
--

CREATE TABLE `management_assistant` (
  `MA_Id` varchar(8) NOT NULL,
  `MA_Name` varchar(150) DEFAULT NULL,
  `Department` enum('Interdisciplinary studies','Computer Engineering','Electrical and Elocronics Engineering','Civil Engineering','Mechanical Engineering') DEFAULT NULL,
  `PhoneNo` int(10) DEFAULT NULL,
  `Email` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `management_assistant`
--

INSERT INTO `management_assistant` (`MA_Id`, `MA_Name`, `Department`, `PhoneNo`, `Email`) VALUES
('CIVIL523', 'Prof. Davis', 'Civil Engineering', 2147483647, 'ma.kanistan@gmail.com'),
('COM11111', 'Dr. Smith', 'Computer Engineering', 1234567890, ''),
('EEE34453', 'Dr. Brown', '', 1357924680, ''),
('IDS22423', 'Dr. White', '', 2147483647, ''),
('MEC23123', 'Prof. Johnson', 'Mechanical Engineering', 2147483647, '');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `RegNo` varchar(8) NOT NULL,
  `Student_Name` varchar(150) DEFAULT NULL,
  `Address` varchar(150) DEFAULT NULL,
  `PhoneNo` int(10) DEFAULT NULL,
  `BatchNo` enum('E19','E20','E21','E22','E23') DEFAULT NULL,
  `Email` varchar(150) DEFAULT NULL,
  `Semester` enum('1','2','3','4','5','6','7','8') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`RegNo`, `Student_Name`, `Address`, `PhoneNo`, `BatchNo`, `Email`, `Semester`) VALUES
('2020E001', 'G. Dilaxshan', 'Jaffna', 764534543, 'E20', '2020e001@eng.jfn.ac.lk', '6'),
('2021E016', 'Arumairasa Karnan', 'Killinochchi', 774470271, 'E21', '2021e016@eng.jfn.ac.lk', '5'),
('2021E025', 'Dhevakumar Kidhurshan', 'Nuwarellia', 774567892, 'E21', '2021e025@eng.jfn.ac.lk', '5'),
('2021E059', 'Jeyakumar Voshithan', 'Killinochchi', 773452143, 'E21', '2021e059@eng.jfn.ac.lk', '5'),
('2021E064', 'Kanesalingam Kanistan', 'Jaffna', 772664192, 'E21', '2021e064@eng.jfn.ac.lk', '5'),
('2021E065', 'Tharindu Hemal', 'Rathnapura', 734556678, 'E21', '2021e065@eng.jfn.ac.lk', '5'),
('2021E066', 'Karunakaran Sathurjan', 'Batticaloa', 755553453, 'E21', '2021e066@eng.jfn.ac.lk', '5'),
('2021E094', 'Maruthanayakam Arunan', 'Kilinochchi', 774576342, 'E21', '2021e094@eng.jfn.ac.lk', '5'),
('2021E095', 'Masilamani Sanjeevan', 'Ambara', 774567892, 'E21', '2021e095@eng.jfn.ac.lk', '5'),
('2021E112', 'Paransothinathan Pogitha', 'Batticaloa', 740950608, 'E21', '2021e112@eng.jfn.ac.lk', '5'),
('2021E146', 'Sivapatham Thilookshan', 'Ambara', 773452143, 'E21', '2021e146@eng.jfn.ac.lk', '5'),
('2021E190', 'Sriskantharajah Nathiskar', 'Jaffna', 773452143, 'E21', '2021e190@eng.jfn.ac.lk', '5'),
('2022E001', 'kanesalingam Kanistan', 'Pointpedro', 773452143, 'E22', '2022e001@eng.jfn.ac.lk', '3'),
('2023E001', 'Kanesalingam Kageepan', 'Pointpedro', 773452143, 'E23', '2023e001@eng.jfn.ac.lk', '1'),
('2023E002', 'kanesalingam Gowsika', 'Pointpedro', 773452143, 'E23', '2023e002@eng.jfn.ac.lk', '1');

-- --------------------------------------------------------

--
-- Table structure for table `teach`
--

CREATE TABLE `teach` (
  `CourseId` varchar(6) NOT NULL,
  `LecturerId` varchar(8) NOT NULL,
  `AY` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teach`
--

INSERT INTO `teach` (`CourseId`, `LecturerId`, `AY`) VALUES
('EC1050', 'COM11111', '23/24'),
('EC1050', 'COM11113', '23/24'),
('EC1050', 'COM11117', '23/24'),
('EC1060', 'COM11113', '23/24'),
('EC1060', 'COM11119', '22/23'),
('EC1070', 'CEE11211', '23/24'),
('EC2040', 'COM11111', '23/24'),
('EC3050', 'CEE11211', '23/24'),
('EC3070', 'IDS22423', '23/24'),
('EC5011', 'EEE12110', '23/24'),
('EC5020', 'EEE12110', '23/24'),
('EC5030', 'COM11112', '23/24'),
('EC5030', 'EEE12110', '23/24'),
('EC5070', 'COM11111', '23/24'),
('EC5080', 'COM11111', '23/24'),
('EC5080', 'COM11117', '23/24'),
('EC5110', 'COM11119', '23/24'),
('MC2030', 'CEE11211', '23/24');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `UserType` enum('Student','Lecturer','Managing assistant','') NOT NULL,
  `Email` varchar(150) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `Approved` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`UserType`, `Email`, `Password`, `Approved`) VALUES
('Student', '2020e001@eng.jfn.ac.lk', '$2y$10$TvJRsPGuAjpZadg0cjF8rORsW00RmTEGockWlmaP.CbiUpy46Qgtq', 1),
('Student', '2021e016@eng.jfn.ac.lk', '$2y$10$qp/4ve6JoFhxv5758homheGtsLRd4COYxHpTIvM4wqGENyW9ecP.S', 1),
('Student', '2021e025@eng.jfn.ac.lk', '$2y$10$kvxszjrwC4XfaC1ts3jBGOEu2YawlvyrNod1Z6ZHtXaE9w4wUGTUO', 1),
('Student', '2021e045@eng.jfn.ac.lk', '$2y$10$/p.CpjFF4NfEiYYfiLoWdOTnZwj7GcDz0bDRzfFlLhWniX7ZLQS1G', 1),
('Student', '2021e059@eng.jfn.ac.lk', '$2y$10$Gm0p/Ugze8dGn25aSXno7OKqz1h7Uw2kMVXYYq5A80GJy.gGB3PH2', 1),
('Student', '2021e064@eng.jfn.ac.lk', '$2y$10$m.wwxOhvpw.jwr8TgNp7sOhPaIX1mgkynfKtrn3I9wHWzELju57k.', 1),
('Student', '2021e065@eng.jfn.ac.lk', '$2y$10$ueR8IsSX3wLHqPGjJapGLuqPz7wMHUIkOK/uHpXMSkwxaoDyw2JnO', 1),
('Student', '2021e066@eng.jfn.ac.lk', '$2y$10$5dFmIDh./SfRFOxitOT2T.U0yhA1gmlpskqJpAlJOXPa1g5ldzYpS', 0),
('Student', '2021e094@eng.jfn.ac.lk', '$2y$10$kvxszjrwC4XfaC1ts3jBGOEu2YawlvyrNod1Z6ZHtXaE9w4wUGTUO', 0),
('Student', '2021e095@eng.jfn.ac.lk', '$2y$10$kvxszjrwC4XfaC1ts3jBGOEu2YawlvyrNod1Z6ZHtXaE9w4wUGTUO', 0),
('Student', '2021e112@eng.jfn.ac.lk', '$2y$10$BQYPQINbFP7RMYMSLCSSTOgvWVlmNvO4HoAr23Pi8WL6kyUlokx6O', 1),
('Student', '2021e146@eng.jfn.ac.lk', '$2y$10$bpEdaFLiVYGQ6lrmSjwf8udzYREJIuBD0sVQchwRxNYRvaZmlOJhO', 1),
('Student', '2021e147@eng.jfn.ac.lk', '$2y$10$GZkI1HH3AT43bOmXh0jk1.EyfSO40v152/B31QaueNts1pp06IGQa', 1),
('Student', '2021e190@eng.jfn.ac.lk', '$2y$10$M9.d4pxxqFnBtrC.HcFPPeKSgjSU/wOYC3GKgjS/jFZah0bDS7lqC', 1),
('Student', '2022e001@eng.jfn.ac.lk', '$2y$10$rFMdVlZ/jWqv3vAPNIBHCOdQuHU5ZM9tE4VjXtzxYmUpwF/PNit/K', 1),
('Student', '2023e001@eng.jfn.ac.lk', '$2y$10$NoA1zodNQqojt5O2D/hqNON4Wwoskj7UHRnNAyxvRDaYfJjRcWv9C', 1),
('Student', '2023e002@eng.jfn.ac.lk', '$2y$10$LyjE/vkj79.3ISe28zjv5uzne4iQoBAfBHIcDzbHEDLsQYBLqJxAa', 1),
('Lecturer', 'jananie@eng.jfn.ac.lk', '$2y$10$OH6c7TbeVokb1Od81Vlde..YmPGgY2Jcb/.dctG7JihcW5iGHJHnO', 1),
('Lecturer', 'kanistan@eng.jfn.ac.lk', '$2y$10$2kH2GEKyvzKgIbrnjAVp9uoNMDR9l1SvOATp1rkF5U0Rj8.wBgzmq', 1),
('Managing assistant', 'ma.kanistan@eng.jfn.ac.lk', '$2y$10$xoAG0J341lzI..tSdQodaO.qoEUHn7ZVQWlHh0suFPITLXjFS0SHC', 1),
('Lecturer', 'pogitha@eng.jfn.ac.lk', '$2y$10$K2V6kRZLDkMWgzRCJRN9qe6XATjFD4yNMUPkjbsOzUGo7RmrPL/Si', 1),
('Lecturer', 'sujanthiha@eng.jfn.ac.lk', '$2y$10$vjP5bMPI2wZTUSPUkMfRneYHXEE/uZRDHojAhWnilojUApCVZ40Ze', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`CourseId`);

--
-- Indexes for table `course_feedback`
--
ALTER TABLE `course_feedback`
  ADD PRIMARY KEY (`FeedbackId`);

--
-- Indexes for table `course_feedback_contains`
--
ALTER TABLE `course_feedback_contains`
  ADD PRIMARY KEY (`QueId`);

--
-- Indexes for table `course_feedback_notice`
--
ALTER TABLE `course_feedback_notice`
  ADD PRIMARY KEY (`CourseId`);

--
-- Indexes for table `enroll`
--
ALTER TABLE `enroll`
  ADD PRIMARY KEY (`RegNo`,`CourseId`,`AY`);

--
-- Indexes for table `gives_course_feedback`
--
ALTER TABLE `gives_course_feedback`
  ADD PRIMARY KEY (`RegNo`,`CourseId`);

--
-- Indexes for table `gives_lecturer_feedback`
--
ALTER TABLE `gives_lecturer_feedback`
  ADD PRIMARY KEY (`RegNo`,`CourseId`,`LecturerId`);

--
-- Indexes for table `lecturer`
--
ALTER TABLE `lecturer`
  ADD PRIMARY KEY (`LecturerId`);

--
-- Indexes for table `lecturer_feedback`
--
ALTER TABLE `lecturer_feedback`
  ADD PRIMARY KEY (`FeedbackId`);

--
-- Indexes for table `lecturer_feedback_contains`
--
ALTER TABLE `lecturer_feedback_contains`
  ADD PRIMARY KEY (`QueId`);

--
-- Indexes for table `lecturer_feedback_notice`
--
ALTER TABLE `lecturer_feedback_notice`
  ADD PRIMARY KEY (`CourseId`,`AY`);

--
-- Indexes for table `management_assistant`
--
ALTER TABLE `management_assistant`
  ADD PRIMARY KEY (`MA_Id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`RegNo`);

--
-- Indexes for table `teach`
--
ALTER TABLE `teach`
  ADD PRIMARY KEY (`CourseId`,`LecturerId`,`AY`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `course_feedback`
--
ALTER TABLE `course_feedback`
  MODIFY `FeedbackId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=145;

--
-- AUTO_INCREMENT for table `lecturer_feedback`
--
ALTER TABLE `lecturer_feedback`
  MODIFY `FeedbackId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

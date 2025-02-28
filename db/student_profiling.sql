-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 11, 2025 at 04:39 AM
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
-- Database: `student_profiling`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin123');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `admin_id`, `title`, `content`, `created_at`, `updated_at`) VALUES
(1, 1, 'Upcoming Seminar', 'There will be a seminar on Web Development on Dec 15th at 10 AM.', '2024-11-28 13:57:41', '2024-11-28 13:57:41'),
(2, 1, 'Meeting', 'mag meeting ta kung nganong di na ako... </3', '2024-11-28 13:58:27', '2025-01-05 07:30:47'),
(5, 1, 'CodeChum', 'Pamayad namo kay makig partner nata nila... ', '2025-01-05 07:48:50', '2025-01-05 07:48:50'),
(7, 1, 'Start of New Semester', 'way uniform way sud ha.', '2025-01-07 22:39:12', '2025-01-07 22:39:12');

-- --------------------------------------------------------

--
-- Table structure for table `form_fields`
--

CREATE TABLE `form_fields` (
  `id` int(11) NOT NULL,
  `field_name` varchar(255) NOT NULL,
  `field_type` varchar(50) NOT NULL,
  `is_required` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `field_options` text DEFAULT NULL,
  `form_status` enum('open','locked') DEFAULT 'locked'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `form_fields`
--

INSERT INTO `form_fields` (`id`, `field_name`, `field_type`, `is_required`, `created_at`, `field_options`, `form_status`) VALUES
(18, 'Section', 'dropdown', 1, '2024-11-28 15:22:00', '[\"A\",\" B\",\" C\",\" D\",\" E\"]', ''),
(19, 'Address', 'text', 1, '2024-11-28 15:22:06', NULL, ''),
(20, 'BirthDate', 'date', 1, '2024-11-28 15:22:32', NULL, ''),
(27, 'Age', 'number', 0, '2025-01-04 12:33:05', NULL, 'locked'),
(28, 'Year Level', 'dropdown', 0, '2025-01-04 13:43:58', '[\"1st Year\",\" 2nd Year\",\" 3rd Year\",\" 4th Year\"]', 'locked'),
(29, 'Gender', 'dropdown', 0, '2025-01-04 14:35:24', '[\"Female\",\" Male\"]', 'locked'),
(30, 'Enrollment Status', 'dropdown', 0, '2025-01-05 07:35:13', '[\"Regular\",\" Irregular\"]', 'locked');

-- --------------------------------------------------------

--
-- Table structure for table `form_status`
--

CREATE TABLE `form_status` (
  `id` int(11) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('open','closed') NOT NULL DEFAULT 'closed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `form_status`
--

INSERT INTO `form_status` (`id`, `updated_at`, `status`) VALUES
(1, '2025-01-03 12:08:19', 'closed');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_picture` varchar(255) DEFAULT 'default.png',
  `bio` text DEFAULT NULL,
  `form_locked` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `first_name`, `last_name`, `email`, `password`, `created_at`, `profile_picture`, `bio`, `form_locked`) VALUES
(30, 'Gemry Delle', 'Taparan', 'gemrydelle@gmail.com', '$2y$10$xj6wqUKbNSZ12vXxnGmkK.iD0IGkACalqKTMGUg9v2fRnVZAIWK.y', '2025-01-04 14:37:05', 'uploads/30_1736059426_dplogo.png', 'Skwis Skwis?', 1),
(31, 'Cassandra', 'Noel', 'cassandra@gmail.com', '$2y$10$Q1ntZjas409ov3F8.E0cNOqH2CfAcxiZ5YVhopfvyGIGdVW57Ruve', '2025-01-04 14:50:19', 'default.png', NULL, 1),
(34, 'Kawhi', 'Leonard', 'kawhi@gmail.com', '$2y$10$6yeXo17xVkCT37Nlo5btVOX15MiGntk/F8bRyExegl6IxEIjenrUO', '2025-01-05 07:19:35', 'uploads/34_1736284740_kawhi.png', 'The Klaw🖐🏽', 1),
(35, 'Kyrie', 'Irving', 'kyrie@gmail.com', '$2y$10$gHpAhLhVLRLTAMJMqxukfOVWtTttmvFtM19uBnWu9c5xGOsCkb2p.', '2025-01-05 07:36:01', 'uploads/35_1736284776_kyrie.png', 'anta is the best...', 1),
(37, 'Fred', 'Vanvleet', 'fred@gmail.com', '$2y$10$LZBZD7W/8FjlYu6Gj350Xu2ECgVxFJn6l2/TtBuALO1rDbWfHRfu2', '2025-01-07 22:15:13', 'uploads/37_1736288292_fred.png', 'Rockets for life !🚀', 1),
(38, 'Luka', 'Doncic', 'luka@gmail.com', '$2y$10$VHNe7b2X1pWJSCcHjYuhBuSebwSNk0DKHufRv45JTzp4WwiUI5FF2', '2025-01-07 22:21:38', 'uploads/38_1736288612_luka.png', 'Luka Magic... ✨✨', 1),
(39, 'Rodney', 'Agsoy', 'rodney@gmail.com', '$2y$10$J74676236laVk7Wy9Ulu3.DhI7ZC2wX9vYPhEhVdeNVXNoDi8Tr8G', '2025-01-10 13:49:19', 'default.png', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `student_details`
--

CREATE TABLE `student_details` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `field_name` varchar(255) NOT NULL,
  `field_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_details`
--

INSERT INTO `student_details` (`id`, `student_id`, `field_name`, `field_value`) VALUES
(439, 30, 'Section', 'A'),
(440, 30, 'Address', 'Guinabasan'),
(441, 30, 'BirthDate', '2025-01-16'),
(442, 30, 'Age', '12'),
(443, 30, 'Year_Level', ' 4th Year'),
(444, 30, 'Gender', ' Male'),
(445, 31, 'Section', ' C'),
(446, 31, 'Address', 'GUINABASAN'),
(447, 31, 'BirthDate', '2025-01-14'),
(448, 31, 'Age', '21'),
(449, 31, 'Year_Level', ' 3rd Year'),
(450, 31, 'Gender', 'Female'),
(541, 34, 'Section', ' E'),
(542, 34, 'Address', 'Toledo, Cebu'),
(543, 34, 'BirthDate', '2025-01-02'),
(544, 34, 'Age', '33'),
(545, 34, 'Year_Level', ' 4th Year'),
(546, 34, 'Gender', ' Male'),
(547, 35, 'Section', ' B'),
(548, 35, 'Address', 'Putat, Tuburan, Cebu'),
(549, 35, 'BirthDate', '2025-01-08'),
(550, 35, 'Age', '32'),
(551, 35, 'Year_Level', ' 4th Year'),
(552, 35, 'Gender', ' Male'),
(553, 35, 'Enrollment_Status', 'Regular'),
(560, 30, 'Enrollment_Status', 'Regular'),
(574, 31, 'Enrollment_Status', 'Regular'),
(581, 34, 'Enrollment_Status', 'Irregular'),
(609, 37, 'Section', 'A'),
(610, 37, 'Address', 'tuburan cebu'),
(611, 37, 'BirthDate', '2025-01-07'),
(612, 37, 'Age', '28'),
(613, 37, 'Year_Level', ' 2nd Year'),
(614, 37, 'Gender', ' Male'),
(615, 37, 'Enrollment_Status', ' Irregular'),
(637, 38, 'Section', ' D'),
(638, 38, 'Address', 'Caridad, Tuburan, Cebu'),
(639, 38, 'BirthDate', '2025-01-01'),
(640, 38, 'Age', '27'),
(641, 38, 'Year_Level', '1st Year'),
(642, 38, 'Gender', ' Male'),
(643, 38, 'Enrollment_Status', 'Regular'),
(658, 39, 'Section', ' D'),
(659, 39, 'Address', 'Caridad, Tuburan, Cebu'),
(660, 39, 'BirthDate', '2025-01-11'),
(661, 39, 'Age', '26'),
(662, 39, 'Year_Level', ' 3rd Year'),
(663, 39, 'Gender', ' Male'),
(664, 39, 'Enrollment_Status', 'Regular');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `form_fields`
--
ALTER TABLE `form_fields`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `form_status`
--
ALTER TABLE `form_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `student_details`
--
ALTER TABLE `student_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`,`field_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `form_fields`
--
ALTER TABLE `form_fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `form_status`
--
ALTER TABLE `form_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `student_details`
--
ALTER TABLE `student_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=665;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_details`
--
ALTER TABLE `student_details`
  ADD CONSTRAINT `student_details_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

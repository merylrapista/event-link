-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 06, 2025 at 11:23 AM
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
-- Database: `event_management`
--

-- --------------------------------------------------------

--
-- Stand-in structure for view `booking_details`
-- (See below for the actual view)
--
CREATE TABLE `booking_details` (
`event_id` int(11)
,`user_id` int(11)
,`event_name` varchar(100)
,`event_date` date
,`event_time` time
,`venue_name` varchar(100)
,`theme_name` varchar(100)
,`status` enum('pending','confirmed','completed','cancelled')
,`service_name` varchar(100)
,`rate` decimal(10,2)
);

-- --------------------------------------------------------

--
-- Table structure for table `catering`
--

CREATE TABLE `catering` (
  `catering_id` int(11) NOT NULL,
  `catering_name` varchar(100) NOT NULL,
  `menu_details` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `event_name` varchar(100) NOT NULL,
  `event_date` date NOT NULL,
  `event_time` time NOT NULL,
  `venue_id` int(11) DEFAULT NULL,
  `theme_id` int(11) DEFAULT NULL,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `customer_id`, `event_name`, `event_date`, `event_time`, `venue_id`, `theme_id`, `status`, `created_at`) VALUES
(2, 19, 'wedding ni jerccel', '1111-11-11', '11:11:00', 1, 3, 'confirmed', '2025-10-06 05:57:54'),
(3, 19, 'wedding ulit', '2222-02-22', '14:22:00', 1, 2, 'confirmed', '2025-10-06 05:58:44'),
(4, 20, 'debut', '2222-02-22', '12:22:00', 1, 2, 'confirmed', '2025-10-06 06:49:30'),
(5, 20, 'sdcsa', '2025-11-10', '11:11:00', 2, 2, 'pending', '2025-10-06 08:32:56');

-- --------------------------------------------------------

--
-- Table structure for table `event_catering`
--

CREATE TABLE `event_catering` (
  `event_id` int(11) NOT NULL,
  `catering_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event_schedule`
--

CREATE TABLE `event_schedule` (
  `schedule_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `activity_name` varchar(150) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `assigned_staff` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event_services`
--

CREATE TABLE `event_services` (
  `event_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_services`
--

INSERT INTO `event_services` (`event_id`, `service_id`) VALUES
(2, 5),
(2, 9),
(3, 4),
(3, 7),
(4, 5),
(4, 9),
(5, 5),
(5, 9);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('booking','staff','payment','general') DEFAULT 'general',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','paid','refunded') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `service_id` int(11) NOT NULL,
  `service_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `rate` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`service_id`, `service_name`, `description`, `rate`, `status`) VALUES
(4, 'Photographer', 'Professional photography services', 2500.00, 'active'),
(5, 'Videographer', 'Event videography services', 3000.00, 'active'),
(6, 'Host', 'Event hosting and emceeing services', 4000.00, 'active'),
(7, 'DJ', 'DJ and music services for events. \r\n\r\n\r\nService rates are subject to change depending on the hours requested.', 1800.00, 'active'),
(9, 'Event Designer', 'Plans and executes the visual aspects of an event', 3500.00, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `themes`
--

CREATE TABLE `themes` (
  `theme_id` int(11) NOT NULL,
  `theme_name` varchar(100) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `themes`
--

INSERT INTO `themes` (`theme_id`, `theme_name`, `category`, `description`, `price`) VALUES
(2, 'dasfdfgg', 'Wedding', 'asfghf', 600.00),
(3, 'hehe', 'Birthday', 'wlaa charchar', 600.00);

-- --------------------------------------------------------

--
-- Table structure for table `theme_images`
--

CREATE TABLE `theme_images` (
  `image_id` int(11) NOT NULL,
  `theme_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `theme_images`
--

INSERT INTO `theme_images` (`image_id`, `theme_id`, `image_path`, `uploaded_at`) VALUES
(2, 2, '68d56e234f581_White Cream Wedding And Event Logo.png', '2025-09-25 16:30:27'),
(3, 2, '68d56e2383451_Eventlink Logo Design.png', '2025-09-25 16:30:27'),
(4, 2, '68d56e238cbec_Copy of thumbnails (3).png', '2025-09-25 16:30:27'),
(5, 2, '68d56e239e918_Copy of thumbnails (2).png', '2025-09-25 16:30:27'),
(6, 3, '68d68717de058_hd_68c1a9bc08047.png', '2025-09-26 12:29:11'),
(7, 3, '68d68717ec0f3_Copy of thumbnails (1).png', '2025-09-26 12:29:11'),
(8, 3, '68d68718045c9_7d487531a7888558cd40a9c5ac2fa593-removebg-preview.png', '2025-09-26 12:29:12'),
(9, 3, '68d687182bf52_d58901f98ccb274a6b4a2e3cb9681e6e-removebg-preview.png', '2025-09-26 12:29:12'),
(10, 3, '68d6871838611_f04057952497fac2d850b6579b010bdd-removebg-preview.png', '2025-09-26 12:29:12');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','admin','staff') NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `password`, `role`, `contact_number`, `created_at`, `status`) VALUES
(18, 'admin123', 'admin@gmail.com', '$2y$10$ju7hSFpSBhfj/mHb8a48Z.gC1QYIGvAzeoZyLXsx3YNkd7CJtdKxS', 'admin', '', '2025-10-05 02:21:43', 'active'),
(19, 'ashe paul', 'lailamarielenarne@gmail.com', '$2y$10$y70J0F4hv7G1MpuZ04RzNuhOSDgJ.UHalKAQwbEpnm3x09G5BeYjC', 'customer', '', '2025-10-05 10:12:43', 'active'),
(20, 'jercel latoza', 'lilamcathlene@gmail.com', '$2y$10$zJsDmLlG0wVdOUimOXE22u1ZsmdMftPGAoRqK49zPiiuq/wOBeXN6', 'customer', '', '2025-10-06 06:47:11', 'active'),
(21, 'cathlene arana', 'enarnelailamariel@gmail.com', '$2y$10$Pik/oba90cYLK7UkHhP.0eLondyokWqR6MFcVFzAWotwHpeQwSlQa', 'customer', '', '2025-10-06 07:20:44', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `venues`
--

CREATE TABLE `venues` (
  `venue_id` int(11) NOT NULL,
  `venue_name` varchar(100) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `venues`
--

INSERT INTO `venues` (`venue_id`, `venue_name`, `location`, `capacity`, `price`) VALUES
(1, 'kilid lang', NULL, NULL, 0.00),
(2, 'sdsadsa', NULL, NULL, 0.00);

-- --------------------------------------------------------

--
-- Structure for view `booking_details`
--
DROP TABLE IF EXISTS `booking_details`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `booking_details`  AS SELECT `e`.`event_id` AS `event_id`, `e`.`customer_id` AS `user_id`, `e`.`event_name` AS `event_name`, `e`.`event_date` AS `event_date`, `e`.`event_time` AS `event_time`, `v`.`venue_name` AS `venue_name`, `t`.`theme_name` AS `theme_name`, `e`.`status` AS `status`, `s`.`service_name` AS `service_name`, `s`.`rate` AS `rate` FROM ((((`events` `e` left join `venues` `v` on(`e`.`venue_id` = `v`.`venue_id`)) left join `themes` `t` on(`e`.`theme_id` = `t`.`theme_id`)) left join `event_services` `es` on(`e`.`event_id` = `es`.`event_id`)) left join `services` `s` on(`es`.`service_id` = `s`.`service_id`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `catering`
--
ALTER TABLE `catering`
  ADD PRIMARY KEY (`catering_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `venue_id` (`venue_id`),
  ADD KEY `theme_id` (`theme_id`);

--
-- Indexes for table `event_catering`
--
ALTER TABLE `event_catering`
  ADD PRIMARY KEY (`event_id`,`catering_id`),
  ADD KEY `catering_id` (`catering_id`);

--
-- Indexes for table `event_schedule`
--
ALTER TABLE `event_schedule`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `assigned_staff` (`assigned_staff`);

--
-- Indexes for table `event_services`
--
ALTER TABLE `event_services`
  ADD PRIMARY KEY (`event_id`,`service_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `fk_notifications_user` (`user_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`service_id`);

--
-- Indexes for table `themes`
--
ALTER TABLE `themes`
  ADD PRIMARY KEY (`theme_id`);

--
-- Indexes for table `theme_images`
--
ALTER TABLE `theme_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `theme_id` (`theme_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `unique_email` (`email`);

--
-- Indexes for table `venues`
--
ALTER TABLE `venues`
  ADD PRIMARY KEY (`venue_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `catering`
--
ALTER TABLE `catering`
  MODIFY `catering_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `event_schedule`
--
ALTER TABLE `event_schedule`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `themes`
--
ALTER TABLE `themes`
  MODIFY `theme_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `theme_images`
--
ALTER TABLE `theme_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `venues`
--
ALTER TABLE `venues`
  MODIFY `venue_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `events_ibfk_2` FOREIGN KEY (`venue_id`) REFERENCES `venues` (`venue_id`),
  ADD CONSTRAINT `events_ibfk_3` FOREIGN KEY (`theme_id`) REFERENCES `themes` (`theme_id`);

--
-- Constraints for table `event_catering`
--
ALTER TABLE `event_catering`
  ADD CONSTRAINT `event_catering_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`),
  ADD CONSTRAINT `event_catering_ibfk_2` FOREIGN KEY (`catering_id`) REFERENCES `catering` (`catering_id`);

--
-- Constraints for table `event_schedule`
--
ALTER TABLE `event_schedule`
  ADD CONSTRAINT `event_schedule_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`);

--
-- Constraints for table `event_services`
--
ALTER TABLE `event_services`
  ADD CONSTRAINT `event_services_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`),
  ADD CONSTRAINT `event_services_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`);

--
-- Constraints for table `theme_images`
--
ALTER TABLE `theme_images`
  ADD CONSTRAINT `theme_images_ibfk_1` FOREIGN KEY (`theme_id`) REFERENCES `themes` (`theme_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

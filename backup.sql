-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 10, 2025 at 01:20 AM
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
-- Database: `emergency_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `dispatch_assignments`
--

CREATE TABLE `dispatch_assignments` (
  `id` int(11) NOT NULL,
  `incident_id` int(11) NOT NULL,
  `responder_name` varchar(100) NOT NULL,
  `dispatch_status` enum('Assigned','In Progress','Completed') DEFAULT 'Assigned',
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `emergency_calls`
--

CREATE TABLE `emergency_calls` (
  `id` int(11) NOT NULL,
  `caller_name` varchar(100) NOT NULL,
  `incident_type` varchar(50) NOT NULL,
  `location` varchar(255) NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `severity` enum('Low','Medium','High','Critical') DEFAULT 'Low',
  `status` enum('Pending','Ongoing','Completed') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `response_time` int(11) DEFAULT NULL,
  `expected_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emergency_calls`
--

INSERT INTO `emergency_calls` (`id`, `caller_name`, `incident_type`, `location`, `latitude`, `longitude`, `severity`, `status`, `created_at`, `response_time`, `expected_time`) VALUES
(31, 'Von', 'Fire', 'Quezon City Circle', 14.65082360, 121.04819790, 'Low', 'Ongoing', '2025-09-09 22:16:36', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `feedback` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `department` varchar(50) NOT NULL DEFAULT 'General'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `user_id`, `message`, `created_at`, `department`) VALUES
(1, 2, 'Hello', '2025-09-09 19:42:31', 'General'),
(2, 2, 'hi', '2025-09-09 20:02:55', 'Fire');

-- --------------------------------------------------------

--
-- Table structure for table `resources`
--

CREATE TABLE `resources` (
  `id` int(11) NOT NULL,
  `unit_name` varchar(100) NOT NULL,
  `resource_type` enum('EMS','Fire','Police') NOT NULL,
  `status` enum('Available','Dispatched') DEFAULT 'Available',
  `current_location` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resources`
--

INSERT INTO `resources` (`id`, `unit_name`, `resource_type`, `status`, `current_location`) VALUES
(1, 'Unit 1', 'EMS', 'Available', 'Station A'),
(2, 'Unit 2', 'EMS', 'Available', 'Station B'),
(3, 'Unit 3', 'EMS', 'Available', 'Station C'),
(4, 'Unit 4', 'EMS', 'Available', 'Station D'),
(5, 'Unit 5', 'EMS', 'Available', 'Station E'),
(6, 'Unit 1', 'Fire', 'Dispatched', 'Station A'),
(7, 'Unit 2', 'Fire', 'Available', 'Station B'),
(8, 'Unit 3', 'Fire', 'Available', 'Station C'),
(9, 'Unit 4', 'Fire', 'Available', 'Station D'),
(10, 'Unit 5', 'Fire', 'Available', 'Station E'),
(11, 'Unit 1', 'Police', 'Available', 'Station A'),
(12, 'Unit 2', 'Police', 'Available', 'Station B'),
(13, 'Unit 3', 'Police', 'Available', 'Station C'),
(14, 'Unit 4', 'Police', 'Available', 'Station D'),
(15, 'Unit 5', 'Police', 'Available', 'Station E');

-- --------------------------------------------------------

--
-- Table structure for table `resource_allocations`
--

CREATE TABLE `resource_allocations` (
  `id` int(11) NOT NULL,
  `emergency_call_id` int(11) NOT NULL,
  `resource_id` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `profile_image`, `created_at`) VALUES
(1, 'admin', '123', 'admin', NULL, '2025-09-05 22:15:26'),
(2, 'staff', '123', 'staff', 'user_2.jpg', '2025-09-05 22:15:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dispatch_assignments`
--
ALTER TABLE `dispatch_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `incident_id` (`incident_id`);

--
-- Indexes for table `emergency_calls`
--
ALTER TABLE `emergency_calls`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `resources`
--
ALTER TABLE `resources`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `resource_allocations`
--
ALTER TABLE `resource_allocations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `emergency_call_id` (`emergency_call_id`),
  ADD KEY `resource_id` (`resource_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dispatch_assignments`
--
ALTER TABLE `dispatch_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `emergency_calls`
--
ALTER TABLE `emergency_calls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `resources`
--
ALTER TABLE `resources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `resource_allocations`
--
ALTER TABLE `resource_allocations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dispatch_assignments`
--
ALTER TABLE `dispatch_assignments`
  ADD CONSTRAINT `dispatch_assignments_ibfk_1` FOREIGN KEY (`incident_id`) REFERENCES `emergency_calls` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `resource_allocations`
--
ALTER TABLE `resource_allocations`
  ADD CONSTRAINT `resource_allocations_ibfk_1` FOREIGN KEY (`emergency_call_id`) REFERENCES `emergency_calls` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `resource_allocations_ibfk_2` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

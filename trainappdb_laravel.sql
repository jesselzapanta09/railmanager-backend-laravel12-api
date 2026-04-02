-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 02, 2026 at 12:49 PM
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
-- Database: `trainappdb_laravel`
--

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2024_01_01_000001_create_users_table', 1),
(2, '2024_01_01_000002_create_user_tokens_table', 1),
(3, '2024_01_01_000003_create_trains_table', 1),
(4, '2024_01_01_000004_create_token_blacklist_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `token_blacklist`
--

CREATE TABLE `token_blacklist` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `token` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `token_blacklist`
--

INSERT INTO `token_blacklist` (`id`, `token`, `created_at`) VALUES
(1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwidXNlcm5hbWUiOiJhZG1pbiIsImVtYWlsIjoiamVzc2VsemFwYW50YUBnbWFpbC5jb20iLCJyb2xlIjoiYWRtaW4iLCJpYXQiOjE3NzUxMjY0ODQsImV4cCI6MTc3NTIxMjg4NH0.8Pe9JBaDzoSdqm7UMC8HeI8qw0g-lEe1nzKj1D4TWcQ', '2026-04-02 02:46:52');

-- --------------------------------------------------------

--
-- Table structure for table `trains`
--

CREATE TABLE `trains` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `train_name` varchar(150) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `route` varchar(255) NOT NULL,
  `image` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `trains`
--

INSERT INTO `trains` (`id`, `train_name`, `price`, `route`, `image`, `created_at`, `updated_at`) VALUES
(1, 'LRT Line 1', 20.00, 'Baclaran - Fernando Poe Jr. Station', '/uploads/trains/train-1774789654.jpg', '2026-03-28 23:34:59', '2026-03-28 21:07:34'),
(2, 'LRT Line 2', 25.00, 'Recto - Antipolo', '/uploads/trains/train-1774789640.jpg', '2026-03-28 23:34:59', '2026-03-28 21:07:20'),
(3, 'MRT Line 3', 24.00, 'North Avenue - Taft Avenue', '/uploads/trains/train-1774789631.jpg', '2026-03-28 23:34:59', '2026-03-28 21:07:11'),
(4, 'PNR Metro Commuter Line', 30.00, 'Tutuban - Alabang', '/uploads/trains/train-1774789626.jpg', '2026-03-28 23:34:59', '2026-03-28 21:07:06'),
(5, 'PNR Bicol Express', 450.00, 'Manila - Naga', '/uploads/trains/train-1774789619.jpg', '2026-03-28 23:34:59', '2026-03-28 21:06:59'),
(6, 'PNR Mayon Limited', 500.00, 'Manila - Legazpi', '/uploads/trains/train-1774789613.jpg', '2026-03-28 23:34:59', '2026-03-28 21:06:53'),
(7, 'LRT Cavite Extension', 35.00, 'Baclaran - Niog', '/uploads/trains/train-1774789608.jpg', '2026-03-28 23:34:59', '2026-03-28 21:06:48'),
(8, 'MRT Line 7', 28.00, 'North Avenue - San Jose del Monte', '/uploads/trains/train-1774789596.jpg', '2026-03-28 23:34:59', '2026-03-28 21:06:36'),
(9, 'North–South Commuter Railway', 60.00, 'Clark - Calamba', '/uploads/trains/train-1774789589.jpg', '2026-03-28 23:34:59', '2026-03-28 21:06:29'),
(10, 'Metro Manila Subway', 35.00, 'Valenzuela - NAIA Terminal 3', '/uploads/trains/train-1774789585.jpg', '2026-03-28 23:34:59', '2026-03-28 21:06:25'),
(11, 'PNR South Long Haul', 800.00, 'Manila - Matnog', '/uploads/trains/train-1774789577.jpg', '2026-03-28 23:34:59', '2026-03-28 21:06:17'),
(12, 'Clark Airport Express', 120.00, 'Clark Airport - Manila', '/uploads/trains/train-1774789572.jpg', '2026-03-28 23:34:59', '2026-03-28 21:06:12'),
(13, 'Mindanao Railway Phase 1', 50.00, 'Tagum - Davao - Digos', '/uploads/trains/train-1774789568.jpg', '2026-03-28 23:34:59', '2026-03-28 21:06:08'),
(14, 'Panay Rail Revival', 40.00, 'Iloilo - Roxas City', '/uploads/trains/train-1774789563.jpg', '2026-03-28 23:34:59', '2026-03-28 21:06:03'),
(15, 'Cebu Monorail', 25.00, 'Cebu City - Mactan Airport', '/uploads/trains/train-1774789559.jpg', '2026-03-28 23:34:59', '2026-03-28 21:05:59');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `avatar` varchar(500) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `avatar`, `email_verified_at`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'jesselzapanta@gmail.com', '$2b$10$2Y/vKNqzuoXYNofyNQpIkeaUCCw7aJZ2wqZEMjfI78rUN8Z4LbvHm', 'admin', '/uploads/avatars/avatar-1774789375.jpg', '2026-03-28 15:34:59', '2026-03-28 15:34:59', '2026-03-28 21:02:55'),
(3, 'useraccount1234', 'useraccount1234@gmail.com', '$2y$10$qgLlILzqH/qX/7IOk7BI3.MfoJ.grKdnN9.Zac.j19/IRbrTr5V.q', 'admin', '/uploads/avatars/avatar-1774789666.png', NULL, '2026-03-28 21:03:26', '2026-03-28 21:07:46'),
(4, 'Eren Yeager', 'erenyeager@gmail.com', '$2y$10$DkSjQe.AXEiRPgImzhHDwOvHzYbaDh6RwP2.4Jw7/QNkuF.nYlcGG', 'user', '/uploads/avatars/avatar-1774789447.png', NULL, '2026-03-28 21:04:07', '2026-03-28 21:04:07'),
(5, 'raidenshogun', 'raidenshogun@gmail.com', '$2y$10$GavW0j2GPZXxEyHS35TOLeRgsN/y61afG5lAeGjrj5aduB75Lc6te', 'admin', '/uploads/avatars/avatar-1774789529.jpg', NULL, '2026-03-28 21:05:15', '2026-03-28 21:05:29'),
(6, 'jeszapanta1211', 'jeszapanta1211@gmail.com', '$2y$10$185Bgs0DOetbCPaUuON/K.iZcKBxy8KvLlwyOerwC1Tk9nT76RZ/6', 'admin', NULL, '2026-03-28 21:14:59', '2026-03-28 21:14:42', '2026-03-28 21:16:03');

-- --------------------------------------------------------

--
-- Table structure for table `user_tokens`
--

CREATE TABLE `user_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `token` varchar(255) NOT NULL,
  `type` enum('email_verify','password_reset') NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `token_blacklist`
--
ALTER TABLE `token_blacklist`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trains`
--
ALTER TABLE `trains`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_tokens_token_unique` (`token`),
  ADD KEY `user_tokens_user_id_foreign` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `token_blacklist`
--
ALTER TABLE `token_blacklist`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `trains`
--
ALTER TABLE `trains`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_tokens`
--
ALTER TABLE `user_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD CONSTRAINT `user_tokens_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

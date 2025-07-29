-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 03, 2025 at 05:25 AM
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
-- Database: `work_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `blacklist_tokens`
--

CREATE TABLE `blacklist_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `token_id` varchar(255) NOT NULL COMMENT 'JWT token ID (jti)',
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Thời gian hết hạn của token',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blacklist_tokens`
--

INSERT INTO `blacklist_tokens` (`id`, `token_id`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, '2y4ewdOCE1tAfco7', '2025-05-22 12:02:45', '2025-05-22 11:03:31', '2025-05-22 11:03:31'),
(2, 'z33THeuq0Nq59alH', '2025-05-22 12:03:35', '2025-05-22 11:04:02', '2025-05-22 11:04:02'),
(3, 'xrobvn32OQIs27t3', '2025-05-22 12:04:49', '2025-05-22 11:07:51', '2025-05-22 11:07:51'),
(4, '3k2hf2tPvp2wsBpv', '2025-05-22 12:08:10', '2025-05-22 11:08:19', '2025-05-22 11:08:19'),
(5, 'yHT4WRbkRqL3wYSL', '2025-05-22 12:13:57', '2025-05-22 11:14:25', '2025-05-22 11:14:25'),
(6, 'NP1NUOzCbURfiGNK', '2025-05-22 12:14:29', '2025-05-22 11:15:09', '2025-05-22 11:15:09'),
(7, 'Qt0ns3Nnr9K4yNGM', '2025-05-22 12:15:18', '2025-05-22 11:15:28', '2025-05-22 11:15:28'),
(8, 'UATEI6pa3WlyKOT0', '2025-05-22 12:15:34', '2025-05-22 11:16:42', '2025-05-22 11:16:42'),
(9, 'zWkvafBlmhtRC0b7', '2025-05-22 12:17:23', '2025-05-22 11:18:05', '2025-05-22 11:18:05'),
(10, 'l5iptBtYcAI4VXpO', '2025-05-22 12:18:13', '2025-05-22 11:19:31', '2025-05-22 11:19:31'),
(11, 'TRr0pkMyG6oU3PBN', '2025-05-22 12:19:37', '2025-05-22 11:19:52', '2025-05-22 11:19:52'),
(12, '4qg7MDhhLtxKsdzJ', '2025-05-22 12:25:32', '2025-05-22 11:25:36', '2025-05-22 11:25:36'),
(13, 'NQAAdfKapGhKcKOB', '2025-05-22 12:25:41', '2025-05-22 11:25:45', '2025-05-22 11:25:45'),
(14, 'TmhVZHmGKSLcCcAW', '2025-05-22 12:25:49', '2025-05-22 11:25:52', '2025-05-22 11:25:52'),
(15, 'hRNWWA2FNAudI6Nj', '2025-05-22 12:25:56', '2025-05-22 11:26:10', '2025-05-22 11:26:10'),
(16, 'xAwywGR2vMUDbSkE', '2025-05-22 12:28:34', '2025-05-22 11:29:13', '2025-05-22 11:29:13'),
(17, 'FNLJS0IexZslr8BK', '2025-05-22 12:29:31', '2025-05-22 11:30:31', '2025-05-22 11:30:31'),
(18, 'NhCSCMHb59L8u4ll', '2025-05-22 12:30:38', '2025-05-22 11:31:25', '2025-05-22 11:31:25'),
(19, 'dgq8bAa3hgeZu4nO', '2025-05-22 12:32:35', '2025-05-22 11:32:47', '2025-05-22 11:32:47'),
(20, 'RFzmv4oBPsp6rBT7', '2025-05-22 12:33:17', '2025-05-22 11:33:21', '2025-05-22 11:33:21'),
(21, '2jXR51nQzlfOHyiN', '2025-05-22 12:33:28', '2025-05-22 11:33:31', '2025-05-22 11:33:31'),
(22, 'lvmi4Zg9oHsFz4Am', '2025-05-22 12:33:35', '2025-05-22 11:35:00', '2025-05-22 11:35:00'),
(23, 'J6MryCEpQXRPMo0S', '2025-05-22 12:35:01', '2025-05-22 11:36:09', '2025-05-22 11:36:09'),
(24, '55Aw3Ps3GFPp14Ru', '2025-05-22 12:36:10', '2025-05-22 11:36:35', '2025-05-22 11:36:35'),
(25, 'GSAe6EPlT0SQqAMj', '2025-05-22 12:36:39', '2025-05-22 11:36:46', '2025-05-22 11:36:46'),
(26, 'iKrR5Bk9jRY20WWT', '2025-05-22 12:38:38', '2025-05-22 11:39:00', '2025-05-22 11:39:00'),
(27, 'EwwAdJc8CDOuXXjp', '2025-05-22 12:39:08', '2025-05-22 11:41:33', '2025-05-22 11:41:33'),
(28, 's5OQqLzv16NH169S', '2025-05-22 12:41:41', '2025-05-22 11:42:56', '2025-05-22 11:42:56'),
(29, 'x1r6PTMP2MuIGPBJ', '2025-05-22 12:43:03', '2025-05-22 11:45:28', '2025-05-22 11:45:28'),
(30, 'PKh4hwanaAWoaVa3', '2025-05-22 12:45:34', '2025-05-22 11:47:42', '2025-05-22 11:47:42'),
(31, 'pzMZi6xNoqlOcut2', '2025-05-22 12:47:48', '2025-05-22 11:50:43', '2025-05-22 11:50:43'),
(32, 'yTb8xQvBKmxgNAt8', '2025-05-22 12:51:01', '2025-05-22 11:52:42', '2025-05-22 11:52:42'),
(33, 'sSXC14YMJOUnoBVB', '2025-05-22 12:52:50', '2025-05-22 11:53:32', '2025-05-22 11:53:32'),
(34, 'ywRzWzxVuFid2MND', '2025-05-22 12:53:41', '2025-05-22 11:53:45', '2025-05-22 11:53:45'),
(35, 'pvWFxrv9LTj616km', '2025-05-22 12:54:25', '2025-05-22 11:55:09', '2025-05-22 11:55:09'),
(36, 'NU1KVOHXH3EoGZwH', '2025-05-22 12:55:13', '2025-05-22 11:56:39', '2025-05-22 11:56:39'),
(37, '7QjieSOb2GvLdUOr', '2025-05-22 12:56:46', '2025-05-22 11:56:54', '2025-05-22 11:56:54'),
(38, 'ZqGO9EieuxCOvUs4', '2025-05-22 12:56:59', '2025-05-22 11:59:29', '2025-05-22 11:59:29'),
(39, 'DU2SRAoT8nKUjTju', '2025-05-22 12:59:30', '2025-05-22 11:59:40', '2025-05-22 11:59:40'),
(40, '7yPPIn52VvTM1QBN', '2025-05-22 13:00:24', '2025-05-22 12:01:04', '2025-05-22 12:01:04'),
(41, 'U4OU3jogEo1CRNud', '2025-05-22 13:04:33', '2025-05-22 12:04:38', '2025-05-22 12:04:38'),
(42, '7QhllaMovpHJ5wBh', '2025-05-22 13:04:46', '2025-05-22 12:05:23', '2025-05-22 12:05:23'),
(43, '0QkhsvVMYlGbSCP2', '2025-05-22 13:05:29', '2025-05-22 12:06:11', '2025-05-22 12:06:11'),
(44, 'IdEcl7Cu3Dqifmjk', '2025-05-22 13:06:18', '2025-05-22 12:07:14', '2025-05-22 12:07:14'),
(45, 'Tr7Ojy8PS5wmXFkc', '2025-05-22 13:07:23', '2025-05-22 12:12:17', '2025-05-22 12:12:17'),
(46, 'yJ1AjM5GYSRa8Kpf', '2025-05-22 13:12:24', '2025-05-22 12:12:30', '2025-05-22 12:12:30'),
(47, '8atUYq0e6P0DkBxF', '2025-05-22 13:12:37', '2025-05-22 12:15:22', '2025-05-22 12:15:22'),
(48, 'dFvCPnLwxFfsx6Im', '2025-05-22 13:15:32', '2025-05-22 12:17:08', '2025-05-22 12:17:08'),
(49, 'GVRQ7mu006jmfBhu', '2025-05-22 13:23:44', '2025-05-22 12:39:26', '2025-05-22 12:39:26'),
(50, 'zu7VC34EB0l15QVX', '2025-05-22 13:39:35', '2025-05-22 12:39:39', '2025-05-22 12:39:39'),
(51, 'aygaFaJ22ADwItAD', '2025-05-22 13:39:44', '2025-05-22 12:42:26', '2025-05-22 12:42:26'),
(52, 'hXaeda43iLGkgM5f', '2025-05-22 13:42:33', '2025-05-22 12:43:57', '2025-05-22 12:43:57'),
(53, 'i40KDsOEVGgDSCUL', '2025-05-22 13:45:21', '2025-05-22 12:48:11', '2025-05-22 12:48:11'),
(54, 'R66tXO508YaQL1PU', '2025-05-22 14:15:55', '2025-05-22 13:17:27', '2025-05-22 13:17:27'),
(55, 'k4wy2BvZTSXLUP1N', '2025-05-22 14:17:27', '2025-05-22 13:21:36', '2025-05-22 13:21:36'),
(56, '2YG1AYak9338xxxL', '2025-05-22 14:25:50', '2025-05-22 13:28:04', '2025-05-22 13:28:04'),
(57, '20jmG9bbilUV2t8S', '2025-05-22 14:32:35', '2025-05-22 13:32:52', '2025-05-22 13:32:52'),
(58, 'rVAUNVj2DPf7M7Pd', '2025-05-22 14:34:06', '2025-05-22 13:37:36', '2025-05-22 13:37:36'),
(59, '6hHFSezt0JF2QIyY', '2025-05-22 14:37:42', '2025-05-22 13:38:08', '2025-05-22 13:38:08'),
(60, 'C0J6WlFWrZ0zzkbE', '2025-05-26 03:02:59', '2025-05-26 02:34:34', '2025-05-26 02:34:34'),
(61, 'yKm61ONTIoKh6ZsU', '2025-05-26 03:34:35', '2025-05-26 02:34:40', '2025-05-26 02:34:40'),
(62, 'vfssRWCfRXOmi3kT', '2025-05-26 03:38:58', '2025-05-26 02:39:29', '2025-05-26 02:39:29'),
(63, 'BNMTRAvX93Y554cq', '2025-05-26 03:39:57', '2025-05-26 02:40:35', '2025-05-26 02:40:35'),
(64, 'ffWY1DW0XxmVojL0', '2025-05-26 03:42:10', '2025-05-26 02:42:33', '2025-05-26 02:42:33'),
(65, 'AAYVtti1nCDoLKO5', '2025-05-26 03:42:34', '2025-05-26 02:43:35', '2025-05-26 02:43:35'),
(66, '83tapV2ztmPTevss', '2025-05-26 03:43:42', '2025-05-26 02:44:22', '2025-05-26 02:44:22'),
(67, 'iKAfoI2Aa5Yzysv5', '2025-05-26 03:44:26', '2025-05-26 02:44:41', '2025-05-26 02:44:41'),
(68, 'xfjGRnce75CUScqY', '2025-05-26 03:45:35', '2025-05-26 02:46:15', '2025-05-26 02:46:15'),
(69, 'BH05rV8Ow4SXCfyD', '2025-05-26 04:29:28', '2025-05-26 03:29:59', '2025-05-26 03:29:59'),
(70, 'FIPrc3NISyBiV9k2', '2025-05-26 05:38:25', '2025-05-26 04:41:51', '2025-05-26 04:41:51'),
(71, 'j1D9a6LEPy3kjGMn', '2025-05-26 04:45:39', '2025-05-26 04:45:20', '2025-05-26 04:45:20'),
(72, 'FwNsRZA2ZbMYU3O8', '2025-05-26 05:35:42', '2025-05-26 05:35:03', '2025-05-26 05:35:03'),
(73, 'LSqgmUOB5X0AUkns', '2025-05-26 18:45:44', '2025-05-26 18:45:03', '2025-05-26 18:45:03'),
(74, 'XulWmapcahe9xvOi', '2025-05-26 18:59:57', '2025-05-26 18:59:06', '2025-05-26 18:59:06'),
(75, 'GPA71RTLIZmWnBmK', '2025-05-26 19:01:48', '2025-05-26 19:00:55', '2025-05-26 19:00:55'),
(76, '6L21oL103UJb5u7K', '2025-05-26 19:02:04', '2025-05-26 19:01:07', '2025-05-26 19:01:07'),
(77, 'RgACZ9US6ff28uf9', '2025-05-26 19:02:22', '2025-05-26 19:01:26', '2025-05-26 19:01:26'),
(78, 'YW9cWZ0tFDF0wwDb', '2025-05-26 19:02:32', '2025-05-26 19:01:40', '2025-05-26 19:01:40'),
(79, 'WJdIqdvaiVpyn7DK', '2025-05-26 19:47:25', '2025-05-26 19:32:28', '2025-05-26 19:32:28'),
(80, 'fnLDKjFpfANOGTt9', '2025-05-26 19:49:48', '2025-05-26 19:40:49', '2025-05-26 19:40:49'),
(81, 'jlKABMijvXVXRZyz', '2025-05-26 19:56:23', '2025-05-26 19:42:15', '2025-05-26 19:42:15'),
(82, '6A9HjuDhRq3Wl4Mm', '2025-05-26 19:57:19', '2025-05-26 19:44:58', '2025-05-26 19:44:58'),
(83, 'zxBGuUJk6vZjyMr1', '2025-05-26 20:00:04', '2025-05-26 19:45:20', '2025-05-26 19:45:20'),
(84, '5ralrMkMQ3IpiI7S', '2025-05-26 20:00:27', '2025-05-26 19:50:30', '2025-05-26 19:50:30'),
(85, 'MubgN4XJliL6zoam', '2025-05-26 20:05:41', '2025-05-26 19:50:55', '2025-05-26 19:50:55'),
(86, 'Va9Gbjy4PlmmyPiS', '2025-05-26 20:06:00', '2025-05-26 19:53:50', '2025-05-26 19:53:50'),
(87, 'RqurTq6LeRjLNCBt', '2025-05-26 20:13:55', '2025-05-26 20:02:12', '2025-05-26 20:02:12'),
(88, 'dJjn0JEw6m5Pa6WP', '2025-05-26 20:17:19', '2025-05-26 20:02:25', '2025-05-26 20:02:25'),
(89, 'm54ZFtSSQNB70u69', '2025-05-26 20:17:30', '2025-05-26 20:02:50', '2025-05-26 20:02:50'),
(90, 'xHKdxIyJ51nZNYFA', '2025-05-26 22:01:44', '2025-05-26 21:49:01', '2025-05-26 21:49:01'),
(91, 'x3fSiySCoY5Pz3UT', '2025-05-26 22:15:35', '2025-05-26 22:00:52', '2025-05-26 22:00:52'),
(92, 'xBivIkUJxInKlxZW', '2025-05-26 22:15:58', '2025-05-26 22:01:23', '2025-05-26 22:01:23'),
(93, 'AvFFmBLcb8dDGwuk', '2025-05-26 22:16:28', '2025-05-26 22:02:03', '2025-05-26 22:02:03'),
(94, 'Cebwx7kBFn9zFGUk', '2025-05-26 22:17:12', '2025-05-26 22:02:24', '2025-05-26 22:02:24'),
(95, '4nbKGsS9byUgo3JR', '2025-05-26 22:17:28', '2025-05-26 22:05:54', '2025-05-26 22:05:54'),
(96, 'sw1UE0Bj1O7LKlhy', '2025-05-26 22:22:47', '2025-05-26 22:07:55', '2025-05-26 22:07:55'),
(97, 'gyZ0AhO37Hsc513J', '2025-05-26 22:23:30', '2025-05-26 22:08:48', '2025-05-26 22:08:48'),
(98, 'zbLjApwDfFvt1Yhj', '2025-05-26 22:24:23', '2025-05-26 22:13:53', '2025-05-26 22:13:53'),
(99, '8TicMj19ZeAs6FUh', '2025-05-26 22:29:01', '2025-05-26 22:14:34', '2025-05-26 22:14:34'),
(100, 'W0939YPRJLEeHRqp', '2025-05-26 22:29:41', '2025-05-26 22:15:59', '2025-05-26 22:15:59'),
(101, '02ejh5Qwc7ME07mh', '2025-05-26 22:31:08', '2025-05-26 22:17:24', '2025-05-26 22:17:24'),
(102, 'zOsNEA8ZyyFrWbhx', '2025-05-26 22:33:50', '2025-05-26 22:19:02', '2025-05-26 22:19:02'),
(103, 'fDYhH9VOTvTrVJkC', '2025-05-26 22:34:06', '2025-05-26 22:19:25', '2025-05-26 22:19:25'),
(104, 'dXjjPHewU7Z9pZNQ', '2025-05-26 22:34:29', '2025-05-26 22:20:09', '2025-05-26 22:20:09'),
(105, 'ATKSvvsaqXXbFup6', '2025-05-26 22:37:22', '2025-05-26 22:22:45', '2025-05-26 22:22:45'),
(106, 'KC1jphRTn8z72wGj', '2025-05-27 00:32:26', '2025-05-27 00:18:02', '2025-05-27 00:18:02'),
(107, '64f0CTqJmunL8k0z', '2025-05-27 00:33:08', '2025-05-27 00:19:32', '2025-05-27 00:19:32'),
(108, 'gBIjNkgrWvQr3ZPB', '2025-05-27 00:34:42', '2025-05-27 00:20:12', '2025-05-27 00:20:12'),
(109, 'YYUcRmOJrTslS0rL', '2025-05-27 00:35:16', '2025-05-27 00:20:42', '2025-05-27 00:20:42'),
(110, 'HWhybOmORSGfUBMu', '2025-05-27 01:02:05', '2025-05-27 00:47:47', '2025-05-27 00:47:47'),
(111, 'LuewvyT02n77uj3d', '2025-05-27 01:03:47', '2025-05-27 00:49:01', '2025-05-27 00:49:01'),
(112, 'MC46Z7IQJOFrlzia', '2025-05-27 01:04:05', '2025-05-27 00:49:13', '2025-05-27 00:49:13'),
(113, '1pRO2KvWXVW952xI', '2025-05-27 01:04:17', '2025-05-27 00:49:35', '2025-05-27 00:49:35'),
(114, 'K5yUpjAnS1bw3fCE', '2025-05-27 01:10:35', '2025-05-27 00:55:46', '2025-05-27 00:55:46'),
(115, 'hTyqW3xTgcupJZpG', '2025-05-27 01:11:07', '2025-05-27 00:56:58', '2025-05-27 00:56:58'),
(116, 'QpFcww12Qr4ugSlW', '2025-05-27 01:12:02', '2025-05-27 00:57:06', '2025-05-27 00:57:06'),
(117, 'Ne6SkCFgZAsmMve7', '2025-05-27 01:17:11', '2025-05-27 01:02:15', '2025-05-27 01:02:15'),
(118, 'hJWuJoRWvSlb0tmu', '2025-05-27 06:12:46', '2025-05-27 05:58:31', '2025-05-27 05:58:31'),
(119, 'nHnzNYBCVFxvzPvF', '2025-05-27 06:13:38', '2025-05-27 05:59:30', '2025-05-27 05:59:30'),
(120, 'YF4MFxkoUl245dGS', '2025-05-27 06:14:35', '2025-05-27 06:01:34', '2025-05-27 06:01:34'),
(121, 'tCv3x0Vvm3eVSIGX', '2025-05-27 22:42:33', '2025-05-27 22:36:27', '2025-05-27 22:36:27'),
(122, 'OeTPtuRh8xTFZgC4', '2025-05-27 23:09:50', '2025-05-27 23:07:32', '2025-05-27 23:07:32'),
(123, '32rLmC1NINu8902U', '2025-05-27 23:10:37', '2025-05-27 23:08:38', '2025-05-27 23:08:38'),
(124, 'q9islbBpbIIj4qgG', '2025-05-28 00:56:26', '2025-05-28 00:41:35', '2025-05-28 00:41:35'),
(125, 'SC9wwPQhN3keWmgg', '2025-05-28 00:56:54', '2025-05-28 00:44:41', '2025-05-28 00:44:41'),
(126, 'sKNHhxvdvnhq30i2', '2025-05-28 00:59:50', '2025-05-28 00:45:50', '2025-05-28 00:45:50'),
(127, 'u0an3GDKq5URkCUs', '2025-05-28 01:01:02', '2025-05-28 00:46:14', '2025-05-28 00:46:14'),
(128, 'FylE9IR7xAK9Tjho', '2025-05-28 01:01:22', '2025-05-28 00:46:36', '2025-05-28 00:46:36'),
(129, 'STDGJF4JsDgv3SKy', '2025-05-28 01:14:58', '2025-05-28 01:00:30', '2025-05-28 01:00:30'),
(130, 'R6D68LqvQxVevTwN', '2025-05-28 02:24:52', '2025-05-28 02:10:10', '2025-05-28 02:10:10'),
(131, 'ydUy4afoUUNyxHwv', '2025-05-28 02:25:17', '2025-05-28 02:10:29', '2025-05-28 02:10:29'),
(132, 'RgFuzKlN6bkiRt4k', '2025-05-28 02:25:33', '2025-05-28 02:10:38', '2025-05-28 02:10:38'),
(133, '96tTAVDSiV43pEgz', '2025-05-28 03:55:00', '2025-05-28 03:40:15', '2025-05-28 03:40:15'),
(134, '713Q9bRRCGs4QKrS', '2025-05-28 03:55:23', '2025-05-28 03:40:38', '2025-05-28 03:40:38'),
(135, 'vme5kzYJ1TkLjsBs', '2025-05-28 04:06:51', '2025-05-28 03:52:48', '2025-05-28 03:52:48'),
(136, 'H19DlYLMr0RyTH2U', '2025-05-28 04:12:03', '2025-05-28 03:57:22', '2025-05-28 03:57:22'),
(137, 'GXQyznSCZRm4LCj5', '2025-05-28 04:12:28', '2025-05-28 03:58:35', '2025-05-28 03:58:35'),
(138, 'tJ3htrkPtlyLkApQ', '2025-06-02 20:30:02', '2025-06-02 20:18:38', '2025-06-02 20:18:38'),
(139, 'i3eTpSGTFO3mOYTq', '2025-06-02 20:33:45', '2025-06-02 20:19:13', '2025-06-02 20:19:13');

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
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_04_23_093627_create_users_table', 1),
(5, '2025_04_23_093655_create_tasks_table', 1),
(6, '2025_05_01_000000_add_start_date_to_tasks_table', 1),
(7, '2025_05_17_233003_add_remember_token_to_users_table', 1),
(8, '2025_05_21_151739_add_profile_fields_to_users_table', 1),
(9, '2025_05_21_152130_create_blacklist_tokens_table', 1),
(10, '2025_05_21_152810_create_notifications_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('a96Pj6BdqBxpRsF4sJPVoIMLihWvQRxlfz6mt45Z', NULL, '127.0.0.1', 'axios/1.9.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoic1JhTk81TktFcjVDek9sMGNJRDNOUUVIOFJLOWxwZFlZeDFoeUdIMiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9oZWFsdGgtY2hlY2siO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1748429605),
('eRcSqErG7OupNpynLwlPOJkLEiCgmPJVjMA43IJi', NULL, '127.0.0.1', 'axios/1.9.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZENxeUVMSFJMbkd1a1Fjd0tpMEczOFpkU2gzVzAza0ZpZ0pyMHlnSyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9oZWFsdGgtY2hlY2siO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1748429583),
('G0jtZyyHAWS5w2e8MGpTW7E14ZFZQxTVYk2iRYGp', NULL, '127.0.0.1', 'axios/1.9.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNExRQ29aaThnd3JBR1pMa2JBdTdJYnN3SGVFcnRHQUFDajdqNno3eCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9oZWFsdGgtY2hlY2siO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1748418470),
('g5svBiozSzSJboFRPnqMCwHd45mHntqJ2q0IwraF', NULL, '127.0.0.1', 'axios/1.9.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiT2dxYmRvTHFmaFl1TEJtMUQ3aTk4MWVZd2hGeURuWVFQMWFGdHFDZyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9oZWFsdGgtY2hlY2siO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1748429798),
('pa6j9v7lEQzzdTwrRj0uYc3OOD2JcnUaoAEU5xtJ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiN2Z6WUF4UjIzdkdCMno3MHc2ZjBWNnh2bEVoZ3hIbGFLWnp5dlpmUSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fX0=', 1748429917),
('Q805gdN0XQt1s1EC31lrAiuV7zgbRKYbKb046HtG', NULL, '127.0.0.1', 'PostmanRuntime/7.43.3', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZWtJV3JKaXV5V2k4eDRrTWp6dGloZ2Jnd2hKVWh6TFBzR2JyVlZjZyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1748424327),
('SWYZ6D5lxdQucXe4rTKN9j7nXgJz7DpQclXHJEPU', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiNWJaM1B2UlNPYjBlSmRXMVNaMG40Nm9pdFlqVkU1aVZFZlFnU2tScSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fXM6OToiand0X3Rva2VuIjtzOjMxMToiZXlKMGVYQWlPaUpLVjFRaUxDSmhiR2NpT2lKSVV6STFOaUo5LmV5SnBjM01pT2lKb2RIUndPaTh2TVRJM0xqQXVNQzR4T2pnd01EQXZiRzluYVc0aUxDSnBZWFFpT2pFM05EZzVNakE0TVRrc0ltVjRjQ0k2TVRjME9Ea3lNVGN4T1N3aWJtSm1Jam94TnpRNE9USXdPREU1TENKcWRHa2lPaUoxVmxaa1VWbDFRbXAyY0haVFptUnVJaXdpYzNWaUlqb2lNaUlzSW5CeWRpSTZJakl6WW1RMVl6ZzVORGxtTmpBd1lXUmlNemxsTnpBeFl6UXdNRGczTW1SaU4yRTFPVGMyWmpjaWZRLlZPckY5MXpVUDF0QkotNHV5M2xUTG5UdlFSMTB6dHJCM3FZb1VEVExwVkUiO3M6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7fQ==', 1748920879),
('zOJvzvPRB5yfQkK8Bdq8KDexeyguWUAQmro4NbGt', NULL, '127.0.0.1', 'axios/1.9.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQndoNG9xUUZUdGJiaFFwMlFzWldCN3ZPeHFzZVN5Q0V0ME12NkVhOCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9oZWFsdGgtY2hlY2siO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1748418412);

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `status` enum('pending','in_progress','completed') NOT NULL DEFAULT 'pending',
  `priority` enum('low','medium','high') NOT NULL DEFAULT 'medium',
  `creator_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `title`, `description`, `start_date`, `due_date`, `status`, `priority`, `creator_id`, `created_at`, `updated_at`) VALUES
(1, 'Hoàn thành báo cáo dự án', 'Hoàn thành báo cáo dự án quản lý công việc và gửi cho quản lý', '2025-05-25', '2025-05-28', 'pending', 'high', 6, '2025-05-22 11:12:19', '2025-05-27 23:08:30'),
(2, 'CNTT', 'CNTT', '2025-05-23', '2025-05-28', 'in_progress', 'medium', 5, '2025-05-22 11:12:19', '2025-06-02 20:16:25'),
(3, 'Kiểm thử chức năng đăng nhập', 'Kiểm thử chức năng đăng nhập và báo cáo lỗi nếu có', '2025-05-24', '2025-05-26', 'pending', 'low', 4, '2025-05-22 11:12:20', '2025-05-22 11:12:20'),
(4, 'Họp với khách hàng', 'Họp trực tuyến với khách hàng để thảo luận về yêu cầu mới', '2025-05-26', '2025-05-26', 'pending', 'high', 6, '2025-05-22 11:12:37', '2025-05-22 11:12:37'),
(5, 'Phát triển API', 'Phát triển các API cho ứng dụng di động', '2025-05-24', '2025-05-29', 'pending', 'high', 5, '2025-05-22 11:12:37', '2025-05-22 11:12:37'),
(6, 'Viết tài liệu hướng dẫn sử dụng', 'Viết tài liệu hướng dẫn sử dụng cho người dùng cuối', '2025-05-25', '2025-05-31', 'pending', 'medium', 4, '2025-05-22 11:12:37', '2025-05-22 11:12:37'),
(7, 'Nghiên cứu công nghệ mới', 'Nghiên cứu và đánh giá các công nghệ mới cho dự án', '2025-05-20', '2025-05-22', 'completed', 'medium', 6, '2025-05-22 11:12:55', '2025-05-26 04:50:23'),
(8, 'Chuẩn bị tài liệu kỹ thuật', 'Chuẩn bị tài liệu kỹ thuật cho dự án', '2025-05-19', '2025-05-19', 'completed', 'medium', 5, '2025-05-22 11:12:55', '2025-05-28 00:21:06'),
(9, 'Thiết lập môi trường phát triển', 'Thiết lập môi trường phát triển cho dự án mới', '2025-05-18', '2025-05-20', 'completed', 'high', 4, '2025-05-22 11:12:55', '2025-05-26 05:54:33'),
(15, 'Cong viec xay dung', 'xay dung cung nhom', '2025-05-07', '2025-05-07', 'in_progress', 'high', 8, '2025-05-28 03:48:06', '2025-05-28 03:48:31');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role` enum('user','manager','admin') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `address`, `password`, `created_at`, `updated_at`, `role`) VALUES
(2, 'Vo Minh Thịnh', 'thinhfaptv@gmail.com', '0796596217', '32/20 Lê Tự Tài', '$2y$12$nVofY0kU29thBd8/3eumZObP0AgaS5qxOmbWQVn2NZK.K5hHJ1WSK', '2025-05-22 10:58:47', '2025-05-26 22:08:30', 'manager'),
(3, 'Vo Minh Thịnh', 'vminhthinh03@gmail.com', '0796596217', '32/20 Lê Tự Tài', '$2y$12$TVU7Gh.UZeghmsaL.ROkWejhb9zs8bDat3ROyO04dIG6ye4WfiNa2', '2025-05-22 10:59:27', '2025-05-26 22:22:32', 'admin'),
(4, 'Vo Minh Thịnh', 'thinharmy2@gmail.com', '0796596217', '32/20 Lê Tự Tài', '$2y$12$1JDgcS34lDb/GXyCDLBWJ.5Jy8w/G4QrvnPEdMkGUE3/INCrjEFRK', '2025-05-22 11:04:45', '2025-05-22 11:04:45', 'user'),
(5, 'Vo Minh Thịnh', 'thinh@gmail.com', '0796596217', '32/20 Lê Tự Tài', '$2y$12$d92vv843SteTfZ30iRNzK.R94XuXa2Yqmy5FQMRTPpjoN3b46Db/y', '2025-05-22 11:08:36', '2025-05-22 11:53:08', 'user'),
(6, 'Vo Minh Thịnh', 'thinhfaptvv@gmail.com', '0796596217', '32/20 Lê Tự Tài', '$2y$12$x6bKBGcYUDkS4.Zzh85EUuQvVi4myQLENERZOmR/Uzfy.T8crK1.y', '2025-05-22 11:08:53', '2025-05-26 02:45:35', 'user'),
(8, 'LeDinh', 'ldinhhuy@gmail.com', '012456781', 'Govap A 256', '$2y$12$N8sGtQ2LiTbLJc1tJ1Go4OaJFqJusCj/dA89ePySDH.xJcP4SSJDy', '2025-05-28 03:46:39', '2025-05-28 03:51:51', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blacklist_tokens`
--
ALTER TABLE `blacklist_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `blacklist_tokens_token_id_unique` (`token_id`),
  ADD KEY `blacklist_tokens_token_id_index` (`token_id`),
  ADD KEY `blacklist_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tasks_creator_id_foreign` (`creator_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blacklist_tokens`
--
ALTER TABLE `blacklist_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=140;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_creator_id_foreign` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

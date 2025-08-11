-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 04, 2025 at 06:35 AM
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
-- Database: `work_management_application`
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
(1, '2y4ewdOCE1tAfco7', '2025-05-22 05:02:45', '2025-05-22 04:03:31', '2025-05-22 04:03:31'),
(2, 'z33THeuq0Nq59alH', '2025-05-22 05:03:35', '2025-05-22 04:04:02', '2025-05-22 04:04:02'),
(3, 'xrobvn32OQIs27t3', '2025-05-22 05:04:49', '2025-05-22 04:07:51', '2025-05-22 04:07:51'),
(4, '3k2hf2tPvp2wsBpv', '2025-05-22 05:08:10', '2025-05-22 04:08:19', '2025-05-22 04:08:19'),
(5, 'yHT4WRbkRqL3wYSL', '2025-05-22 05:13:57', '2025-05-22 04:14:25', '2025-05-22 04:14:25'),
(6, 'NP1NUOzCbURfiGNK', '2025-05-22 05:14:29', '2025-05-22 04:15:09', '2025-05-22 04:15:09'),
(7, 'Qt0ns3Nnr9K4yNGM', '2025-05-22 05:15:18', '2025-05-22 04:15:28', '2025-05-22 04:15:28'),
(8, 'UATEI6pa3WlyKOT0', '2025-05-22 05:15:34', '2025-05-22 04:16:42', '2025-05-22 04:16:42'),
(9, 'zWkvafBlmhtRC0b7', '2025-05-22 05:17:23', '2025-05-22 04:18:05', '2025-05-22 04:18:05'),
(10, 'l5iptBtYcAI4VXpO', '2025-05-22 05:18:13', '2025-05-22 04:19:31', '2025-05-22 04:19:31'),
(11, 'TRr0pkMyG6oU3PBN', '2025-05-22 05:19:37', '2025-05-22 04:19:52', '2025-05-22 04:19:52'),
(12, '4qg7MDhhLtxKsdzJ', '2025-05-22 05:25:32', '2025-05-22 04:25:36', '2025-05-22 04:25:36'),
(13, 'NQAAdfKapGhKcKOB', '2025-05-22 05:25:41', '2025-05-22 04:25:45', '2025-05-22 04:25:45'),
(14, 'TmhVZHmGKSLcCcAW', '2025-05-22 05:25:49', '2025-05-22 04:25:52', '2025-05-22 04:25:52'),
(15, 'hRNWWA2FNAudI6Nj', '2025-05-22 05:25:56', '2025-05-22 04:26:10', '2025-05-22 04:26:10'),
(16, 'xAwywGR2vMUDbSkE', '2025-05-22 05:28:34', '2025-05-22 04:29:13', '2025-05-22 04:29:13'),
(17, 'FNLJS0IexZslr8BK', '2025-05-22 05:29:31', '2025-05-22 04:30:31', '2025-05-22 04:30:31'),
(18, 'NhCSCMHb59L8u4ll', '2025-05-22 05:30:38', '2025-05-22 04:31:25', '2025-05-22 04:31:25'),
(19, 'dgq8bAa3hgeZu4nO', '2025-05-22 05:32:35', '2025-05-22 04:32:47', '2025-05-22 04:32:47'),
(20, 'RFzmv4oBPsp6rBT7', '2025-05-22 05:33:17', '2025-05-22 04:33:21', '2025-05-22 04:33:21'),
(21, '2jXR51nQzlfOHyiN', '2025-05-22 05:33:28', '2025-05-22 04:33:31', '2025-05-22 04:33:31'),
(22, 'lvmi4Zg9oHsFz4Am', '2025-05-22 05:33:35', '2025-05-22 04:35:00', '2025-05-22 04:35:00'),
(23, 'J6MryCEpQXRPMo0S', '2025-05-22 05:35:01', '2025-05-22 04:36:09', '2025-05-22 04:36:09'),
(24, '55Aw3Ps3GFPp14Ru', '2025-05-22 05:36:10', '2025-05-22 04:36:35', '2025-05-22 04:36:35'),
(25, 'GSAe6EPlT0SQqAMj', '2025-05-22 05:36:39', '2025-05-22 04:36:46', '2025-05-22 04:36:46'),
(26, 'iKrR5Bk9jRY20WWT', '2025-05-22 05:38:38', '2025-05-22 04:39:00', '2025-05-22 04:39:00'),
(27, 'EwwAdJc8CDOuXXjp', '2025-05-22 05:39:08', '2025-05-22 04:41:33', '2025-05-22 04:41:33'),
(28, 's5OQqLzv16NH169S', '2025-05-22 05:41:41', '2025-05-22 04:42:56', '2025-05-22 04:42:56'),
(29, 'x1r6PTMP2MuIGPBJ', '2025-05-22 05:43:03', '2025-05-22 04:45:28', '2025-05-22 04:45:28'),
(30, 'PKh4hwanaAWoaVa3', '2025-05-22 05:45:34', '2025-05-22 04:47:42', '2025-05-22 04:47:42'),
(31, 'pzMZi6xNoqlOcut2', '2025-05-22 05:47:48', '2025-05-22 04:50:43', '2025-05-22 04:50:43'),
(32, 'yTb8xQvBKmxgNAt8', '2025-05-22 05:51:01', '2025-05-22 04:52:42', '2025-05-22 04:52:42'),
(33, 'sSXC14YMJOUnoBVB', '2025-05-22 05:52:50', '2025-05-22 04:53:32', '2025-05-22 04:53:32'),
(34, 'ywRzWzxVuFid2MND', '2025-05-22 05:53:41', '2025-05-22 04:53:45', '2025-05-22 04:53:45'),
(35, 'pvWFxrv9LTj616km', '2025-05-22 05:54:25', '2025-05-22 04:55:09', '2025-05-22 04:55:09'),
(36, 'NU1KVOHXH3EoGZwH', '2025-05-22 05:55:13', '2025-05-22 04:56:39', '2025-05-22 04:56:39'),
(37, '7QjieSOb2GvLdUOr', '2025-05-22 05:56:46', '2025-05-22 04:56:54', '2025-05-22 04:56:54'),
(38, 'ZqGO9EieuxCOvUs4', '2025-05-22 05:56:59', '2025-05-22 04:59:29', '2025-05-22 04:59:29'),
(39, 'DU2SRAoT8nKUjTju', '2025-05-22 05:59:30', '2025-05-22 04:59:40', '2025-05-22 04:59:40'),
(40, '7yPPIn52VvTM1QBN', '2025-05-22 06:00:24', '2025-05-22 05:01:04', '2025-05-22 05:01:04'),
(60, 'C0J6WlFWrZ0zzkbE', '2025-05-25 20:02:59', '2025-05-25 19:34:34', '2025-05-25 19:34:34'),
(100, 'W0939YPRJLEeHRqp', '2025-05-26 15:29:41', '2025-05-26 15:15:59', '2025-05-26 15:15:59'),
(138, 'tJ3htrkPtlyLkApQ', '2025-06-02 13:30:02', '2025-06-02 13:18:38', '2025-06-02 13:18:38'),
(145, 'G1uwKDQdABXFMGYc', '2025-07-31 01:21:59', '2025-07-31 01:07:05', '2025-07-31 01:07:05'),
(146, 'QXKKQXm5I883TgIq', '2025-07-31 01:22:27', '2025-07-31 01:07:45', '2025-07-31 01:07:45'),
(147, 'CJypK29XR7B5RvlA', '2025-07-31 01:22:47', '2025-07-31 01:08:12', '2025-07-31 01:08:12'),
(148, 'Uh0GvyPRK6ox2JOB', '2025-07-31 01:23:18', '2025-07-31 01:10:31', '2025-07-31 01:10:31'),
(149, 'hmLgV1SpVrAY9cU4', '2025-07-31 01:25:31', '2025-07-31 01:11:51', '2025-07-31 01:11:51'),
(150, '3nMcIeI795DIm9M6', '2025-07-31 01:26:52', '2025-07-31 01:12:22', '2025-07-31 01:12:22'),
(151, 'CTLCyRpQkihB3R4n', '2025-07-31 01:27:26', '2025-07-31 01:12:41', '2025-07-31 01:12:41'),
(152, 'nK3fMOJvZ7ZzW88c', '2025-07-31 01:27:46', '2025-07-31 01:12:53', '2025-07-31 01:12:53'),
(153, 'pufzMd5URLW3pM4o', '2025-07-31 01:27:57', '2025-07-31 01:13:02', '2025-07-31 01:13:02'),
(154, '6f9qiTA9BSZRc1xb', '2025-07-31 01:28:10', '2025-07-31 01:14:06', '2025-07-31 01:14:06'),
(155, 'koIBFjVBLUaBbfPi', '2025-07-31 01:29:06', '2025-07-31 01:15:01', '2025-07-31 01:15:01'),
(156, 'B0FM8YiWm3cFYTli', '2025-07-31 01:30:01', '2025-07-31 01:15:04', '2025-07-31 01:15:04'),
(157, 'koVdFJkTjJjXOJMc', '2025-07-31 01:30:09', '2025-07-31 01:15:22', '2025-07-31 01:15:22'),
(158, 'RJTbLttEtmqgEeTS', '2025-07-31 01:30:29', '2025-07-31 01:17:30', '2025-07-31 01:17:30'),
(159, '1ba1wRTKPj36ISgq', '2025-07-31 01:33:04', '2025-07-31 01:20:02', '2025-07-31 01:20:02'),
(160, '91f5WlzALtNbJK7X', '2025-07-31 01:35:03', '2025-07-31 01:20:07', '2025-07-31 01:20:07'),
(161, 'JgIKykCBZICKbrZP', '2025-07-31 01:35:16', '2025-07-31 01:25:47', '2025-07-31 01:25:47'),
(162, 'QVjyuFRQERgvs0Ay', '2025-07-31 01:40:52', '2025-07-31 01:25:59', '2025-07-31 01:25:59'),
(163, 'SxFwp8lFsrlYIfgj', '2025-07-31 01:41:05', '2025-07-31 01:26:51', '2025-07-31 01:26:51'),
(164, 'kFL9BoLwfBEssETl', '2025-07-31 01:47:40', '2025-07-31 01:42:26', '2025-07-31 01:42:26'),
(165, 'gGzaJ2KwDyVmQASv', '2025-07-31 01:57:26', '2025-07-31 01:47:35', '2025-07-31 01:47:35'),
(166, '5BrUOTUzR8xXmg3z', '2025-07-31 02:02:39', '2025-07-31 01:54:14', '2025-07-31 01:54:14'),
(167, '9DCOb7zIAcwWPpkI', '2025-07-31 02:09:22', '2025-07-31 01:54:34', '2025-07-31 01:54:34'),
(168, 'PcyhkDoKGByxCL4k', '2025-07-31 02:09:48', '2025-07-31 02:01:26', '2025-07-31 02:01:26'),
(169, 'm9Cuzy3aGnG9Ui5p', '2025-07-31 02:16:31', '2025-07-31 02:01:40', '2025-07-31 02:01:40'),
(170, 'KNRBSf8vR7ndKf67', '2025-07-31 02:16:44', '2025-07-31 02:03:46', '2025-07-31 02:03:46'),
(171, 'glHQzylDWLqqcpuY', '2025-07-31 02:18:50', '2025-07-31 02:04:27', '2025-07-31 02:04:27'),
(172, 'u5gyxuGbPOlooWWd', '2025-07-31 02:19:32', '2025-07-31 02:07:51', '2025-07-31 02:07:51'),
(173, '7bUw9Xu1oeLk8B45', '2025-07-31 02:22:55', '2025-07-31 02:08:16', '2025-07-31 02:08:16'),
(174, 'RX1PhiUkeehTtefg', '2025-07-31 02:23:22', '2025-07-31 02:11:32', '2025-07-31 02:11:32'),
(175, 'xCdaliDyw9pV4bxh', '2025-07-31 02:28:28', '2025-07-31 02:18:11', '2025-07-31 02:18:11'),
(176, '6CRKkX7eYth4q4Ax', '2025-07-31 02:33:14', '2025-07-31 02:18:19', '2025-07-31 02:18:19'),
(177, 'eVy2eVauswomUAlk', '2025-07-31 02:33:24', '2025-07-31 02:19:06', '2025-07-31 02:19:06'),
(178, 'OQ3CC9VIqwH2n7PA', '2025-07-31 02:34:11', '2025-07-31 02:20:58', '2025-07-31 02:20:58'),
(179, 'E3arAMfe0F0bg5iF', '2025-07-31 02:36:03', '2025-07-31 02:21:12', '2025-07-31 02:21:12'),
(180, 'uIqRsr9yOstluD8m', '2025-07-31 02:36:19', '2025-07-31 02:28:34', '2025-07-31 02:28:34'),
(181, 'Flk9WS2QhS870RhS', '2025-07-31 02:43:41', '2025-07-31 02:29:26', '2025-07-31 02:29:26'),
(182, 'M6B4P62Nq0zSQJgX', '2025-07-31 02:44:30', '2025-07-31 02:30:00', '2025-07-31 02:30:00'),
(183, 'Acm0CdQXZzi1S6EX', '2025-07-31 02:45:05', '2025-07-31 02:31:40', '2025-07-31 02:31:40'),
(184, 's2zN9pdJbSJpAXmO', '2025-07-31 02:47:41', '2025-07-31 02:39:15', '2025-07-31 02:39:15'),
(185, 'lmJFMtW12DAabH35', '2025-07-31 02:54:19', '2025-07-31 02:39:29', '2025-07-31 02:39:29'),
(186, 'Y332ciAUtOx74m7S', '2025-07-31 02:54:39', '2025-07-31 02:39:50', '2025-07-31 02:39:50');

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
(1, '2025_01_02_000001_create_users_table', 1),
(2, '2025_01_02_000002_create_tasks_table', 1),
(3, '2025_01_02_000003_create_sessions_table', 1),
(4, '2025_01_02_000004_create_blacklist_tokens_table', 1),
(5, '2025_07_29_093247_create_teams_table', 1),
(6, '2025_07_29_093315_create_team_members_table', 1),
(7, '2025_07_29_093335_add_team_id_to_tasks_table', 1),
(8, '2025_07_29_095701_drop_team_members_table', 1),
(9, '2025_07_29_095724_add_team_id_to_users_table', 1);

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
  `updated_at` timestamp NULL DEFAULT NULL,
  `assigned_to` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `title`, `description`, `start_date`, `due_date`, `status`, `priority`, `creator_id`, `created_at`, `updated_at`, `assigned_to`) VALUES
(1, 'Kiểm thử chức năng đăng nhập', 'Kiểm thử chức năng đăng nhập và báo cáo lỗi nếu có', '2025-05-24', '2025-05-26', 'pending', 'low', 10, '2025-05-22 11:12:20', '2025-05-22 11:12:20', 7),
(2, 'Họp với khách hàng', 'Họp trực tuyến với khách hàng để thảo luận về yêu cầu mới', '2025-05-26', '2025-05-26', 'pending', 'high', 3, '2025-05-22 11:12:37', '2025-05-22 11:12:37', 5),
(3, 'Phát triển API', 'Phát triển các API cho ứng dụng di động', '2025-05-24', '2025-05-29', 'pending', 'high', 6, '2025-05-22 11:12:37', '2025-05-22 11:12:37', 6),
(4, 'Viết tài liệu hướng dẫn sử dụng', 'Viết tài liệu hướng dẫn sử dụng cho người dùng cuối', '2025-05-25', '2025-05-31', 'pending', 'medium', 7, '2025-05-22 11:12:37', '2025-05-22 11:12:37', 7),
(5, 'Nghiên cứu công nghệ mới', 'Nghiên cứu và đánh giá các công nghệ mới cho dự án', '2025-05-20', '2025-05-22', 'completed', 'medium', 11, '2025-05-22 11:12:55', '2025-05-26 04:50:23', 9),
(6, 'Chuẩn bị tài liệu kỹ thuật', 'Chuẩn bị tài liệu kỹ thuật cho dự án', '2025-05-19', '2025-05-19', 'completed', 'medium', 10, '2025-05-22 11:12:55', '2025-05-28 00:21:06', 7),
(7, 'Thiết lập môi trường phát triển', 'Thiết lập môi trường phát triển cho dự án mới', '2025-05-18', '2025-05-20', 'completed', 'high', 9, '2025-05-22 11:12:55', '2025-05-26 05:54:33', 9),
(8, 'Phát triển phần mềm', 'Phát triển phần mềm mới cho doanh nghiệp', '2025-07-17', '2025-07-24', 'pending', 'medium', 6, '2025-07-31 02:05:24', '2025-07-31 02:05:24', 6);

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE `teams` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `leader_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `teams`
--

INSERT INTO `teams` (`id`, `name`, `description`, `leader_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Development Team', 'Đội phát triển phần mềm', 2, 'active', '2025-07-29 02:59:48', '2025-07-29 02:59:48'),
(2, 'Marketing Team', 'Đội marketing và truyền thông', 3, 'active', '2025-07-29 02:59:48', '2025-07-29 02:59:48'),
(3, 'Sales Team', 'Đội bán hàng', 10, 'active', '2025-07-29 02:59:48', '2025-07-29 02:59:48'),
(4, 'HR Team', 'Đội nhân sự', 11, 'active', '2025-07-29 02:59:48', '2025-07-29 02:59:48');

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
  `role` enum('user','manager','admin') NOT NULL DEFAULT 'user',
  `team_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `address`, `password`, `created_at`, `updated_at`, `role`, `team_id`) VALUES
(1, 'Admin', 'admin@gmail.com', NULL, NULL, '$2y$12$uXVcYgq.tebrx4IEb9UvTedNZTaNDG.yUHxEfb8ZcYSP/qcM31QvK', '2025-07-29 03:02:45', '2025-07-29 03:02:45', 'admin', NULL),
(2, 'Vo Thị Phương Thảo', 'thinhfaptv@gmail.com', NULL, NULL, '$2y$12$WujqdZTbemiHgnFdENo5reHh5SH6Uiy9cCFAFod.h4ezBvtWFLVk2', '2025-07-29 03:02:45', '2025-07-31 02:17:18', 'manager', NULL),
(3, 'Le Đình Huy', 'ldinhhuy@gmail.com', NULL, NULL, '$2y$12$g28RdzyDbQKqSoLj7fQ5wuXB/8V6MJ2cFGcFYH6vHT6/Xl5oeSRNu', '2025-07-29 03:02:46', '2025-07-31 02:18:44', 'manager', NULL),
(4, 'Võ Minh Thịnh', 'vminhthinh03@gmail.com', NULL, NULL, '$2y$12$rLbb5BTZKDqFA93fuIlCeOYwv5DOBXor6fYQUXMtkjMtGhTiPJe5G', '2025-07-29 03:02:46', '2025-07-29 03:02:55', 'admin', NULL),
(5, 'Huỳnh Kim Thoả', 'thinharmy2@gmail.com', NULL, NULL, '$2y$12$4Pjzh16ErnvE33hioZkCHetgiZGLWAH1LVC8UCCJoVy/Lmqf3pBLm', '2025-07-29 03:02:46', '2025-07-29 03:02:55', 'user', 4),
(6, 'Lê Thị Bích Mỹ', 'thinh@gmail.com', NULL, NULL, '$2y$12$7qxPymgCZwVGmNYzKXfo7.HQUosuRFHFswPYTuiaUNEZhT9gmai.q', '2025-07-29 03:02:46', '2025-07-29 03:02:55', 'user', 3),
(7, 'Bùi Thị Mai Trâm', 'thinhfaptvv@gmail.com', NULL, NULL, '$2y$12$9oiJ4encaYYMOR0sWL7U1OmXz3Suu0sV5Idpz3vYLcAN2kXoFE9zi', '2025-07-29 03:02:46', '2025-07-29 03:02:46', 'user', 2),
(9, 'Nguyễn Tuệ Nhi', 'tuenhi010710@gmail.com', NULL, NULL, '$2y$12$/nXyX1qhweDj16e4OBAdrevIOFULqo3Rpma2ZiNcnCkui.7E/I..O', '2025-07-31 01:44:04', '2025-07-31 01:44:04', 'user', 1),
(10, 'Pham Đăng Thịnh', 'dangthinh1902@gmail.com', NULL, NULL, '$2y$12$kte9rI.V2BJD.JHOMz9tPONNTxTWMdHNgdqYkXTX1CzXDXSY.sjP.', '2025-07-31 02:33:17', '2025-07-31 02:33:17', 'manager', NULL),
(11, 'Pham Thanh Son', 'thanhson1904@gmail.com', NULL, NULL, '$2y$12$26TE6eohpCeEX9.Qqu6mluON5t92RceDOCk4y2i9KzwNJEB3SVhbG', '2025-07-31 02:34:05', '2025-07-31 02:34:05', 'manager', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blacklist_tokens`
--
ALTER TABLE `blacklist_tokens`
  ADD PRIMARY KEY (`id`);

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
  ADD KEY `assigned_to` (`assigned_to`);

--
-- Indexes for table `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teams_leader_id_foreign` (`leader_id`),
  ADD KEY `teams_status_created_at_index` (`status`,`created_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_team_id_foreign` (`team_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blacklist_tokens`
--
ALTER TABLE `blacklist_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=187;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `teams`
--
ALTER TABLE `teams`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `teams`
--
ALTER TABLE `teams`
  ADD CONSTRAINT `teams_leader_id_foreign` FOREIGN KEY (`leader_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

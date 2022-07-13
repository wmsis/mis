-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1
-- 生成日期： 2021-09-29 04:37:13
-- 服务器版本： 10.3.16-MariaDB
-- PHP 版本： 7.3.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `sis_yongqiang`
--

-- --------------------------------------------------------

--
-- 表的结构 `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `desc` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `area` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `isopen` int(11) NOT NULL DEFAULT 0,
  `password` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preferences` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `period` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

--
-- 转存表中的数据 `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `desc`, `mobile`, `area`, `address`, `isopen`, `password`, `type`, `preferences`, `period`, `member_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, '管理员', '2574522520@qq.com', 'iicat', '17091952061', '[\"辽宁省\",\"大连市\",\"甘井子区\"]', '凌海路1号', 1, '$2y$10$lxNRv15qdLjOJLNWvM5hz.MeProVzYudK6EuGDXTcqP.kMG.md4iS', 'admin', NULL, NULL, 5, '2017-06-17 01:07:35', '2020-07-07 08:17:23', NULL),
(219, '123456789', 'xx@qq.com', NULL, '15012341234', NULL, NULL, 1, '$2y$10$D65l2eSr1k5lqOCRzSb5EO73IpXRYoBzWfGhVo38ZRWp4aISP6yQK', 'admin', NULL, NULL, NULL, '2019-07-26 08:08:20', '2019-07-30 00:34:50', NULL),
(225, 'zxwang', NULL, NULL, '15512341234', NULL, NULL, 1, '$2y$10$KEtzttwtgIdKAABnj3MJneLjkv4TQhcarMepp9VFBTWOyP/ORYcaW', NULL, NULL, NULL, NULL, '2019-07-26 08:20:13', '2020-03-14 01:00:22', NULL),
(245, 'dwc887', NULL, NULL, '13738337123', NULL, NULL, 1, '$2y$10$9WWI2ZP21tpZhX6z8AAAhOwdlcmgkTftPE3ghdVyjqr6PbaHCftR6', NULL, NULL, NULL, NULL, '2020-05-18 07:37:46', '2020-05-18 07:54:20', NULL),
(246, 'test', NULL, NULL, '123456', NULL, NULL, 1, '$2y$10$.qXxSBSmX0Gr1izSpkqVuuRuwRM8SLaf5KDbpuPAX6oxAgVQzQZ4e', 'admin', NULL, 'first', NULL, '2020-06-11 01:29:57', '2020-06-29 07:28:04', NULL);

--
-- 转储表的索引
--

--
-- 表的索引 `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone` (`mobile`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=247;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

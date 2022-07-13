-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1
-- 生成日期： 2021-09-25 09:42:00
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
-- 数据库： `sis`
--

-- --------------------------------------------------------

--
-- 表的结构 `check_notification`
--

CREATE TABLE `check_notification` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `boiler_check_id` int(11) DEFAULT NULL COMMENT '锅炉考核指标ID',
  `member_id` int(11) DEFAULT NULL COMMENT '微信用户ID',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `check_notification`
--

INSERT INTO `check_notification` (`id`, `boiler_check_id`, `member_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 144, '2021-09-16 03:49:31', '2021-09-16 03:49:31', NULL),
(2, 2, 144, '2021-09-16 03:49:31', '2021-09-16 03:49:31', NULL),
(3, 1, 132, '2021-09-17 07:01:49', '2021-09-17 07:11:10', '2021-09-17 07:11:10'),
(4, 2, 132, '2021-09-17 07:09:43', '2021-09-17 07:10:13', '2021-09-17 07:10:13'),
(5, 2, 132, '2021-09-17 07:11:10', '2021-09-17 07:36:56', '2021-09-17 07:36:56'),
(6, 1, NULL, '2021-09-17 07:15:24', '2021-09-17 07:15:24', NULL);

--
-- 转储表的索引
--

--
-- 表的索引 `check_notification`
--
ALTER TABLE `check_notification`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `check_notification`
--
ALTER TABLE `check_notification`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

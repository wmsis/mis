-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1
-- 生成日期： 2021-09-25 09:42:11
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
-- 表的结构 `diy_class`
--

CREATE TABLE `diy_class` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `date` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '日期',
  `morning_value` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '早班值日人',
  `morning_begin` datetime DEFAULT NULL COMMENT '早班开始时间',
  `morning_end` datetime DEFAULT NULL COMMENT '早班结束时间',
  `day_value` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '白班值日人',
  `day_begin` datetime DEFAULT NULL COMMENT '白班开始时间',
  `day_end` datetime DEFAULT NULL COMMENT '白班结束时间',
  `evening_value` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '中班值日人',
  `evening_begin` datetime DEFAULT NULL COMMENT '中班开始时间',
  `evening_end` datetime DEFAULT NULL COMMENT '中班结束时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `diy_class`
--

INSERT INTO `diy_class` (`id`, `date`, `morning_value`, `morning_begin`, `morning_end`, `day_value`, `day_begin`, `day_end`, `evening_value`, `evening_begin`, `evening_end`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '2021-09-14', '乙', '2021-09-14 08:00:00', '2021-09-14 15:30:00', '甲', '2021-09-14 15:30:00', '2021-09-14 23:00:00', '丙', '2021-09-14 23:00:00', '2021-09-14 22:00:00', '2021-09-19 07:40:57', '2021-09-19 07:40:57', NULL),
(2, '2021-09-15', '乙', '2021-09-14 22:00:00', '2021-09-15 06:00:00', '丙', '2021-09-15 06:00:00', '2021-09-15 16:00:00', '甲', '2021-09-15 16:00:00', '2021-09-15 23:00:00', '2021-09-19 07:40:57', '2021-09-19 07:45:34', NULL),
(3, '2021-09-16', '甲', '2021-09-15 23:00:00', '2021-09-16 15:30:00', '丙', '2021-09-16 15:30:00', '2021-09-16 23:00:00', '丁', '2021-09-16 23:00:00', '2021-09-17 08:00:00', '2021-09-19 07:40:57', '2021-09-19 07:45:34', NULL),
(4, '2021-09-07', '甲', '2021-09-07 08:00:00', '2021-09-07 15:30:00', '丙', '2021-09-07 15:30:00', '2021-09-07 23:00:00', '丁', '2021-09-07 23:00:00', '2021-09-07 22:00:00', '2021-09-19 07:47:53', '2021-09-19 07:54:12', NULL),
(5, '2021-09-08', '丙', '2021-09-07 22:00:00', '2021-09-08 04:00:00', '甲', '2021-09-08 04:00:00', '2021-09-08 16:00:00', '乙', '2021-09-08 16:00:00', '2021-09-08 23:30:00', '2021-09-19 07:47:53', '2021-09-19 07:54:12', NULL),
(6, '2021-09-09', '丙', '2021-09-08 23:30:00', '2021-09-09 15:30:00', '丁', '2021-09-09 15:30:00', '2021-09-09 23:00:00', '乙', '2021-09-09 23:00:00', '2021-09-10 08:00:00', '2021-09-19 07:47:53', '2021-09-19 07:47:53', NULL),
(7, '2021-09-03', '丁', '2021-09-03 08:00:00', '2021-09-03 15:30:00', '乙', '2021-09-03 15:30:00', '2021-09-03 23:00:00', '甲', '2021-09-03 23:00:00', '2021-09-03 21:00:00', '2021-09-19 08:00:44', '2021-09-19 08:00:44', NULL),
(8, '2021-09-04', '甲', '2021-09-03 21:00:00', '2021-09-04 03:00:00', '乙', '2021-09-04 03:00:00', '2021-09-04 17:00:00', '丙', '2021-09-04 17:00:00', '2021-09-04 23:00:00', '2021-09-19 08:00:44', '2021-09-19 08:00:44', NULL),
(9, '2021-09-05', '乙', '2021-09-04 23:00:00', '2021-09-05 15:30:00', '甲', '2021-09-05 15:30:00', '2021-09-05 23:00:00', '丙', '2021-09-05 23:00:00', '2021-09-06 08:00:00', '2021-09-19 08:00:44', '2021-09-19 08:00:44', NULL);

--
-- 转储表的索引
--

--
-- 表的索引 `diy_class`
--
ALTER TABLE `diy_class`
  ADD PRIMARY KEY (`id`),
  ADD KEY `diy_class_date_index` (`date`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `diy_class`
--
ALTER TABLE `diy_class`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

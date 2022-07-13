-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1
-- 生成日期： 2021-09-27 03:04:21
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
-- 表的结构 `boiler`
--

CREATE TABLE `boiler` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `hash` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '锅炉名称',
  `rubbish_ability` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '设计垃圾处理能力',
  `rubbish_entry_tag` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '垃圾入炉量tag',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `boiler`
--

INSERT INTO `boiler` (`id`, `hash`, `name`, `rubbish_ability`, `rubbish_entry_tag`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '1679091c5a880faf6fb5e6087eb1b2dc', '1#炉', '6688', 'gggg', '2021-03-20 02:39:01', '2021-03-20 02:58:55', '2021-03-20 02:58:55'),
(2, '8f14e45fceea167a5a36dedd4bea2543', '2#炉', '8888', 'ljrll', '2021-03-20 02:40:50', '2021-03-20 02:40:50', NULL),
(3, 'c9f0f895fb98ab9159f51fd0297e236d', '3#锅炉', '1111', '2222', '2021-03-22 07:18:47', '2021-03-22 07:18:47', NULL);

--
-- 转储表的索引
--

--
-- 表的索引 `boiler`
--
ALTER TABLE `boiler`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `boiler`
--
ALTER TABLE `boiler`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

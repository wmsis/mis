-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1
-- 生成日期： 2021-09-27 03:02:25
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
-- 表的结构 `boiler_tag`
--

CREATE TABLE `boiler_tag` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `boiler_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '锅炉ID',
  `category` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '所属分类',
  `cn_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '中文名称',
  `tag_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'tag名称',
  `min_val` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `boiler_tag`
--

INSERT INTO `boiler_tag` (`id`, `boiler_id`, `category`, `cn_name`, `tag_name`, `min_val`, `created_at`, `updated_at`, `deleted_at`) VALUES
(5, 2, 'run_conf', '上部炉温', 'DESKTOP-T2UTQV2.Applications.BurningLine1.TRA_17L', 500, '2021-03-20 02:40:50', '2021-03-20 02:40:50', NULL),
(6, 2, 'run_conf', '下部炉温', 'DESKTOP-T2UTQV2.Applications.BurningLine1.TRA_16L', 500, '2021-03-20 02:40:50', '2021-03-20 02:40:50', NULL),
(7, 2, 'temperature_conf', '上部炉温', 'DESKTOP-T2UTQV2.Applications.BurningLine1.TRA_17L', NULL, '2021-03-20 02:40:50', '2021-03-20 02:40:50', NULL),
(8, 2, 'temperature_conf', '下部炉温', 'DESKTOP-T2UTQV2.Applications.BurningLine1.TRA_16L', NULL, '2021-03-20 02:40:50', '2021-03-20 02:40:50', NULL),
(69, 3, 'run_conf', 'rrr', 'DESKTOP-T2UTQV2.Applications.BurningLine1.TRA_15L', 123, '2021-03-22 07:18:47', '2021-03-22 07:18:47', NULL),
(70, 3, 'temperature_conf', 'iiii', 'DESKTOP-T2UTQV2.Applications.BurningLine1.TR_18L', NULL, '2021-03-22 07:18:47', '2021-03-22 07:18:47', NULL);

--
-- 转储表的索引
--

--
-- 表的索引 `boiler_tag`
--
ALTER TABLE `boiler_tag`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `boiler_tag`
--
ALTER TABLE `boiler_tag`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

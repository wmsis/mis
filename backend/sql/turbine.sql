-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1
-- 生成日期： 2021-09-27 03:05:01
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
-- 表的结构 `turbine`
--

CREATE TABLE `turbine` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `hash` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '汽轮机名称',
  `electricity_tag` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '功率tag名称',
  `electricity_ability` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '设计日发电功率',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `turbine`
--

INSERT INTO `turbine` (`id`, `hash`, `name`, `electricity_tag`, `electricity_ability`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'c4ca4238a0b923820dcc509a6f75849b', '1#汽轮机', 'DESKTOP-T2UTQV2.Applications.TurbineParts.DEH_5R2', '9999', '2021-03-20 03:03:18', '2021-03-20 03:04:34', NULL),
(2, '8f14e45fceea167a5a36dedd4bea2543', '3#汽轮机', 'DESKTOP-T2UTQV2.Applications.TurbineParts.DEH_4R2', '29999', '2021-03-23 05:47:47', '2021-03-23 05:47:47', NULL);

--
-- 转储表的索引
--

--
-- 表的索引 `turbine`
--
ALTER TABLE `turbine`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `turbine`
--
ALTER TABLE `turbine`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

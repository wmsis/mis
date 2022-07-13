-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1
-- 生成日期： 2021-09-25 09:58:21
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
-- 表的结构 `boiler_check`
--

CREATE TABLE `boiler_check` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `module_id` int(11) DEFAULT NULL COMMENT '模块ID',
  `tag_en_name` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'tag英文名',
  `tag_cn_name` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'tag中文名',
  `upper_limit` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '报警上限',
  `lower_limit` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '报警下限',
  `time_limit` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '报警时限',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `boiler_check`
--

INSERT INTO `boiler_check` (`id`, `module_id`, `tag_en_name`, `tag_cn_name`, `upper_limit`, `lower_limit`, `time_limit`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 7, 'Applications.BurningLine1.TR_19L(Applications.BurningLine1.TR_19L)', '测试111', '10', '1', '100', '2021-09-16 02:01:54', '2021-09-25 07:52:21', NULL),
(2, 22, 'Applications.BurningLine1.TR_18R(Applications.BurningLine1.TR_18R)', '测试222', '11', '3', '999', '2021-09-16 03:16:17', '2021-09-25 07:53:05', NULL),
(3, 23, 'DESKTOP-Q6VQP4H.Simulation00003(Sim00003)', '测试333', '11', '2', '111', '2021-09-16 08:36:20', '2021-09-16 08:36:26', '2021-09-16 08:36:26');

--
-- 转储表的索引
--

--
-- 表的索引 `boiler_check`
--
ALTER TABLE `boiler_check`
  ADD PRIMARY KEY (`id`),
  ADD KEY `boiler_check_tag_en_name_index` (`tag_en_name`),
  ADD KEY `boiler_check_tag_cn_name_index` (`tag_cn_name`),
  ADD KEY `boiler_check_upper_limit_index` (`upper_limit`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `boiler_check`
--
ALTER TABLE `boiler_check`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

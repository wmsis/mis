-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1
-- 生成日期： 2021-09-27 03:04:42
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
-- 表的结构 `factory_tag`
--

CREATE TABLE `factory_tag` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cn_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '中文名称',
  `en_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '英文名称',
  `tag_name` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'tag名称',
  `min_value` int(11) DEFAULT NULL COMMENT '最小值',
  `max_value` int(11) DEFAULT NULL COMMENT '最大值',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `factory_tag`
--

INSERT INTO `factory_tag` (`id`, `cn_name`, `en_name`, `tag_name`, `min_value`, `max_value`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '渗沥液产生量', 'leachate_produce_tag', 'leachate_produce_tag', 11, 22, '2021-04-26 06:44:11', '2021-04-26 06:44:11', NULL),
(2, '渗沥液处理量', 'leachate_handle_tag', 'leachate_handle_tag', 111, 222, '2021-04-26 06:44:11', '2021-04-26 06:44:11', NULL),
(3, '垃圾焚烧量', 'rubbish_incinerate_tag', 'rubbish_incinerate_tag', 1, 2, '2021-04-26 06:44:11', '2021-04-26 06:44:11', NULL),
(4, '盐酸产生量', 'hcl_tag', 'hcl_tag', 1, 222, '2021-04-26 06:44:11', '2021-04-26 06:44:11', NULL),
(5, 'SO2产生量', 'so2_tag', 'so2_tag', 1, 222, '2021-04-26 06:44:11', '2021-04-26 06:44:11', NULL),
(6, 'NOX产生量', 'nox_tag', 'nox_tag', 11, 22, '2021-04-26 06:44:11', '2021-04-26 06:44:11', NULL),
(7, '粉尘产生量', 'fc_tag', 'fc_tag', 12, 23, '2021-04-26 06:44:11', '2021-04-26 06:44:11', NULL),
(8, '柴油消耗量', 'chai_oil_tag', 'chai_oil_tag', 1, 222, '2021-04-26 06:44:11', '2021-04-26 06:44:11', NULL),
(9, '消石灰消耗量', 'lime_tag', 'lime_tag', 2, 3, '2021-04-26 06:44:11', '2021-04-26 06:44:11', NULL),
(10, '活性炭消耗量', 'carbon_tag', 'carbon_tag', 2, 44, '2021-04-26 06:44:11', '2021-04-26 06:44:11', NULL),
(11, '氨水消耗量', 'ammonium_tag', 'ammonium_tag', 3, 55, '2021-04-26 06:44:11', '2021-04-26 06:44:11', NULL);

--
-- 转储表的索引
--

--
-- 表的索引 `factory_tag`
--
ALTER TABLE `factory_tag`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `factory_tag`
--
ALTER TABLE `factory_tag`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

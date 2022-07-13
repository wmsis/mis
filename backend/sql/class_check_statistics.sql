-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1
-- 生成日期： 2021-09-25 09:58:38
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
-- 表的结构 `class_check_statistics`
--

CREATE TABLE `class_check_statistics` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `module_id` int(11) DEFAULT NULL COMMENT '锅炉ID',
  `start_time` datetime DEFAULT NULL COMMENT '报警开始时间',
  `end_time` datetime DEFAULT NULL COMMENT '报警结束时间',
  `tag_en_name` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'tag英文名',
  `tag_cn_name` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'tag中文名',
  `value` int(11) DEFAULT NULL COMMENT '报警次数',
  `class_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '班次名称',
  `class_date` date DEFAULT NULL COMMENT '班次日期',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转储表的索引
--

--
-- 表的索引 `class_check_statistics`
--
ALTER TABLE `class_check_statistics`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `class_check_statistics`
--
ALTER TABLE `class_check_statistics`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

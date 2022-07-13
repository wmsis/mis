-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1
-- 生成日期： 2021-12-31 09:14:54
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
-- 数据库： `sis_yq_first`
--

-- --------------------------------------------------------

--
-- 表的结构 `class_statement`
--

CREATE TABLE `class_statement` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tag_en_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'tag英文名',
  `tag_cn_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'tag中文名',
  `value` int(11) DEFAULT NULL COMMENT '值',
  `class_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '班次名称',
  `duty_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '值班人',
  `date` date DEFAULT NULL COMMENT '班次日期',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转储表的索引
--

--
-- 表的索引 `class_statement`
--
ALTER TABLE `class_statement`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `class_statement`
--
ALTER TABLE `class_statement`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

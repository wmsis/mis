-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1
-- 生成日期： 2021-09-25 09:58:29
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
-- 表的结构 `check_alarm_record`
--

CREATE TABLE `check_alarm_record` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `boiler_check_id` int(11) DEFAULT NULL COMMENT '锅炉考核指标ID',
  `module_id` int(11) DEFAULT NULL,
  `start_time` datetime DEFAULT NULL COMMENT '报警开始时间',
  `end_time` datetime DEFAULT NULL COMMENT '报警结束时间',
  `tag_en_name` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'tag英文名',
  `tag_cn_name` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'tag中文名',
  `upper_limit` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '报警上限',
  `lower_limit` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '报警下限',
  `time_limit` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '报警时限',
  `is_notify` int(11) DEFAULT NULL COMMENT '是否已通知',
  `need_notify` int(11) DEFAULT NULL COMMENT '是否需要通知',
  `notify_member_ids` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '通知用户ID',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转储表的索引
--

--
-- 表的索引 `check_alarm_record`
--
ALTER TABLE `check_alarm_record`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `check_alarm_record`
--
ALTER TABLE `check_alarm_record`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

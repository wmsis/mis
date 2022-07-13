-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1
-- 生成日期： 2021-09-29 07:55:42
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
-- 表的结构 `wx_menu`
--

CREATE TABLE `wx_menu` (
  `id` int(10) UNSIGNED NOT NULL,
  `path` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '路径',
  `name` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '名称',
  `level` int(11) DEFAULT NULL COMMENT '层级',
  `is_root` int(11) DEFAULT NULL COMMENT '是否根节点',
  `sort` int(11) DEFAULT NULL COMMENT '排序号',
  `is_open` int(11) DEFAULT NULL COMMENT '是否开启',
  `type` enum('view','miniprogram','click') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keyword` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `appid` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pagepath` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `wx_menu`
--

INSERT INTO `wx_menu` (`id`, `path`, `name`, `level`, `is_root`, `sort`, `is_open`, `type`, `keyword`, `appid`, `pagepath`, `url`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '1', '锅炉统计', 1, 1, 1, 1, 'view', NULL, NULL, NULL, 'https://www.baidu.com', NULL, '2020-07-10 05:42:50', NULL),
(2, '2', '汽机统计', 1, 1, 1, 1, 'view', NULL, NULL, NULL, 'https://www.baidu.com', NULL, '2020-07-10 05:42:59', NULL),
(3, '3', '个人中心', 1, 1, 1, 1, 'view', NULL, NULL, NULL, 'https://www.baidu.com', NULL, '2019-08-05 01:47:58', NULL),
(4, '1/4', '百度2下', 2, NULL, 1, 1, 'view', NULL, 'wxa434eb05e32c4cdf', 'pages/home/home', 'https://www.baidu.com', NULL, '2021-09-25 01:39:17', NULL),
(7, '2/7', '分享海报', 2, NULL, 2, 1, 'click', 'xxx', 'wxa434eb05e32c4cdf', 'pages/mine/mine', 'https://www.baidu.com', NULL, '2021-09-25 01:05:16', NULL),
(8, '3/8', '本月生产', 2, NULL, 2, 1, 'click', '本月生产', 'wxa434eb05e32c4cdf', 'pages/order/list', 'https://www.baidu.com', NULL, '2021-09-25 08:16:01', NULL),
(11, '2/11', '关键字您好', 2, NULL, 2, 1, 'click', '您好', NULL, NULL, 'https://www.baidu.com', NULL, '2021-09-25 09:27:09', NULL),
(12, '3/12', '我是图片', 2, NULL, 4, 1, 'click', '图片', NULL, NULL, 'https://www.baidu.com', NULL, '2019-11-05 08:57:55', NULL),
(22, '3/22', '会员中心', 2, 0, 3, 1, 'view', NULL, 'wxa434eb05e32c4cdf', 'pages/mine/mine', 'https://www.baidu.com', NULL, '2021-09-25 01:05:47', NULL),
(23, '3/23', '今日生产', 2, 0, 1, 1, 'click', '今日生产', 'wxa434eb05e32c4cdf', 'pages/enterapply/enterapply', 'https://www.baidu.com', NULL, '2021-09-25 08:15:18', NULL),
(24, '3/24', '电厂后台', 2, 0, 5, 1, 'view', NULL, NULL, NULL, 'http://baidu.com', NULL, '2021-09-25 01:06:06', NULL);

--
-- 转储表的索引
--

--
-- 表的索引 `wx_menu`
--
ALTER TABLE `wx_menu`
  ADD PRIMARY KEY (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

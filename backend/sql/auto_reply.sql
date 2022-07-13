-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1
-- 生成日期： 2021-09-25 09:35:11
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
-- 表的结构 `auto_reply`
--

CREATE TABLE `auto_reply` (
  `id` int(10) UNSIGNED NOT NULL,
  `category` enum('subscribe','keyword','open','default') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('text','pic_txt','img') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pic_txt_id` int(11) DEFAULT NULL,
  `img` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keyword` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '关键词回复时需要',
  `interval_time` int(11) DEFAULT NULL COMMENT '间隔时间（分钟），打开回复时需要',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `auto_reply`
--

INSERT INTO `auto_reply` (`id`, `category`, `type`, `text`, `pic_txt_id`, `img`, `keyword`, `interval_time`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'subscribe', 'text', '[$name]，您好，欢迎光临，现在时间是 [$time] 。', 2, NULL, NULL, NULL, '2019-08-05 01:38:01', '2021-09-25 01:13:21', NULL),
(2, 'keyword', 'pic_txt', NULL, 2, NULL, '您好', NULL, '2019-08-05 08:26:08', '2019-08-05 08:26:08', NULL),
(3, 'open', 'text', '[$name]，您好，这是打开公众号的回复，现在时间为[$time]，您的地理位置为[$province][$city][$district]', 2, NULL, NULL, 1, '2019-08-05 08:27:13', '2021-09-25 01:48:42', NULL),
(4, 'default', 'text', '[$name]，您好，这是默认回复，现在时间为[$time]。', 1, NULL, NULL, NULL, '2019-08-05 08:27:30', '2021-09-25 01:17:11', NULL),
(5, 'keyword', 'img', NULL, NULL, '/uploads/wechat/20210925/CJxQJmgFunciwtYogMJw3kmaRlb5DohOFeOMamT9.png', '图片', NULL, '2019-08-07 02:37:11', '2021-09-25 01:11:58', NULL),
(6, 'keyword', 'text', 'xx', NULL, NULL, NULL, NULL, '2019-10-23 06:19:18', '2019-10-30 06:42:37', '2019-10-30 06:42:37'),
(7, 'keyword', 'text', '222', NULL, NULL, 'xxx', NULL, '2019-10-23 06:20:17', '2019-10-23 06:20:17', NULL),
(8, 'keyword', 'pic_txt', NULL, 2, NULL, 'xxx', NULL, '2019-10-23 06:27:46', '2019-10-23 06:27:46', NULL),
(9, 'keyword', 'text', 'xxx', NULL, NULL, 'xx', NULL, '2019-10-23 06:29:37', '2019-10-23 06:29:59', '2019-10-23 06:29:59'),
(10, 'keyword', 'img', NULL, NULL, '/uploads/wechat/20210925/R9t9R1AUxhnBdnIyJYDHhpvykraiTTQcWgbOWjDr.jpeg', 'x\'x2', NULL, '2019-10-23 06:33:35', '2021-09-25 01:12:32', NULL);

--
-- 转储表的索引
--

--
-- 表的索引 `auto_reply`
--
ALTER TABLE `auto_reply`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `auto_reply`
--
ALTER TABLE `auto_reply`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

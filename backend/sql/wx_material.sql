-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1
-- 生成日期： 2021-09-29 07:55:31
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
-- 表的结构 `wx_material`
--

CREATE TABLE `wx_material` (
  `id` int(10) UNSIGNED NOT NULL,
  `img` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `url` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  `send_num` int(11) DEFAULT NULL,
  `read_num` int(11) DEFAULT NULL,
  `buy_num` int(11) DEFAULT NULL,
  `type` enum('pictxt') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pic_txt_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `wx_material`
--

INSERT INTO `wx_material` (`id`, `img`, `title`, `description`, `url`, `sort`, `send_num`, `read_num`, `buy_num`, `type`, `pic_txt_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '/uploads/wechat/20210925/IN0qGdWLECQS7ASZUFevzewWmt8cE9q9fkKdGc4a.jpeg', '关注图文消息', '锁有不凡，源于坚持', 'https://www.baidu.com', 1, NULL, NULL, NULL, 'pictxt', 1, '2019-08-05 01:08:05', '2021-09-25 01:19:58', NULL),
(2, '/uploads/wechat/20210925/yIDy7vcIPlf1sDwONy5ncH5McmTTDtIG73yXIIPu.png', '祖国70周年庆', '中国万岁', 'https://www.baidu.com', NULL, NULL, NULL, NULL, 'pictxt', 2, '2019-08-05 08:18:06', '2021-09-25 01:18:59', NULL),
(3, '/uploads/wechat/20210925/1BzWKeBsijtMR5dzHC4pDT8yL6woizfM8D50dJ9n.jpeg', '大桥通车', '祖国您好', 'https://www.baidu.com', NULL, NULL, NULL, NULL, 'pictxt', 2, '2019-08-05 08:19:10', '2021-09-25 01:19:40', NULL),
(4, '/uploads/wechat/20191022/8qRVUDufLrcbWgnnfm5o3eM1OjzgNpiqZd23x48E.jpeg', '2XX', 'XX2', 'XX2', 102, NULL, NULL, NULL, 'pictxt', 2, '2019-10-22 08:33:17', '2019-10-22 09:03:00', '2019-10-22 09:03:00'),
(5, '/uploads/wechat/20210925/CSbmuhKAOni5xM5DMkqVxK1JxzfJgY6ZKF5ukFkb.png', '伟明环保', '耕耘美好明天', 'http://baidu.com', 112, NULL, NULL, NULL, 'pictxt', 4, '2019-10-22 09:16:03', '2021-09-25 08:03:13', NULL),
(6, '/uploads/wechat/20210925/KwGaD5WZmal8G3xQMBbkLiWGzUlxBLNe2yaxG1Wa.jpeg', '猫头鹰', '老鼠的天敌', 'http://baidu.com', 1, NULL, NULL, NULL, 'pictxt', 4, '2019-10-22 09:16:31', '2021-09-25 08:03:38', NULL);

--
-- 转储表的索引
--

--
-- 表的索引 `wx_material`
--
ALTER TABLE `wx_material`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `wx_material`
--
ALTER TABLE `wx_material`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

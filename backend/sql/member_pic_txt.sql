-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1
-- 生成日期： 2021-09-25 09:35:48
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
-- 表的结构 `member_pic_txt`
--

CREATE TABLE `member_pic_txt` (
  `id` int(10) UNSIGNED NOT NULL,
  `openid` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('pictxt','text','image','voice','video') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pic_txt_id` int(11) DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL,
  `text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `media_path` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('success','fail') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `result` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `member_pic_txt`
--

INSERT INTO `member_pic_txt` (`id`, `openid`, `type`, `pic_txt_id`, `member_id`, `text`, `media_path`, `status`, `result`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'ohqcn1kg9izcwhtAjU5Un8rAbj60', 'image', NULL, 5, NULL, '/mnt/www/mall_admin_api/public/uploads/wechat/20190805/mNwdgF9yOuosMyc8qSUk1yBVQBymUJZtwlXcXIq8.jpeg', 'success', NULL, '2019-08-07 02:39:37', '2019-08-07 02:42:31', NULL),
(2, 'ohqcn1kg9izcwhtAjU5Un8rAbj60', 'image', NULL, 5, NULL, '/mnt/www/mall_admin_api/public/uploads/wechat/20190805/mNwdgF9yOuosMyc8qSUk1yBVQBymUJZtwlXcXIq8.jpeg', 'success', NULL, '2019-08-07 02:51:09', '2019-08-07 02:51:11', NULL),
(3, 'ohqcn1kg9izcwhtAjU5Un8rAbj60', 'image', NULL, 5, NULL, '/mnt/www/mall_admin_api/public/uploads/wechat/20190805/mNwdgF9yOuosMyc8qSUk1yBVQBymUJZtwlXcXIq8.jpeg', 'success', NULL, '2019-08-07 03:02:57', '2019-08-07 03:02:58', NULL),
(4, 'ohqcn1kg9izcwhtAjU5Un8rAbj60', 'image', NULL, 5, NULL, '/mnt/www/mall_admin_api/public/uploads/wechat/20190805/mNwdgF9yOuosMyc8qSUk1yBVQBymUJZtwlXcXIq8.jpeg', 'success', NULL, '2019-08-07 03:04:22', '2019-08-07 03:04:22', NULL),
(5, 'ohqcn1kg9izcwhtAjU5Un8rAbj60', 'image', NULL, 5, NULL, '/mnt/www/mall_admin_api/public/uploads/wechat/20190805/mNwdgF9yOuosMyc8qSUk1yBVQBymUJZtwlXcXIq8.jpeg', 'success', NULL, '2019-08-07 03:04:31', '2019-08-07 03:04:32', NULL),
(15, 'ohqcn1kg9izcwhtAjU5Un8rAbj60', 'pictxt', 1, 5, NULL, NULL, 'success', NULL, '2019-10-23 02:17:22', '2019-10-23 02:17:23', NULL),
(16, 'ohqcn1kg9izcwhtAjU5Un8rAbj60', 'pictxt', 1, 5, NULL, NULL, 'success', NULL, '2019-10-23 02:21:02', '2019-10-23 02:21:02', NULL),
(17, 'ohqcn1kg9izcwhtAjU5Un8rAbj60', 'pictxt', 1, 5, NULL, NULL, 'success', NULL, '2019-10-23 04:52:10', '2019-10-23 04:52:10', NULL),
(18, 'ohqcn1kg9izcwhtAjU5Un8rAbj60', 'pictxt', 1, 5, NULL, NULL, 'success', NULL, '2019-10-23 04:57:51', '2019-10-23 04:57:52', NULL),
(19, 'ohqcn1kg9izcwhtAjU5Un8rAbj60', 'pictxt', 1, 5, NULL, NULL, 'success', NULL, '2019-10-23 05:00:09', '2019-10-23 14:21:11', '2019-10-23 14:21:11'),
(20, 'ohqcn1kg9izcwhtAjU5Un8rAbj60', 'pictxt', 1, 5, NULL, NULL, 'success', NULL, '2019-10-23 05:02:43', '2019-10-23 14:16:05', '2019-10-23 14:16:05'),
(22, 'ohqcn1kg9izcwhtAjU5Un8rAbj60', 'pictxt', 1, 5, NULL, NULL, 'success', '{\"errcode\":0,\"errmsg\":\"ok\"}', '2019-10-23 05:08:02', '2019-10-23 05:08:03', NULL),
(23, 'ohqcn1kg9izcwhtAjU5Un8rAbj60', 'pictxt', 1, 5, NULL, NULL, 'success', '{\"errcode\":0,\"errmsg\":\"ok\"}', '2019-10-23 05:09:43', '2019-10-23 05:09:44', NULL),
(24, 'ohqcn1kg9izcwhtAjU5Un8rAbj60', 'pictxt', 1, 5, NULL, NULL, 'success', '{\"errcode\":0,\"errmsg\":\"ok\"}', '2019-10-23 05:13:38', '2019-10-23 14:15:25', '2019-10-23 14:15:25'),
(25, 'ohqcn1kg9izcwhtAjU5Un8rAbj60', 'text', NULL, 5, '你好', NULL, 'success', '{\"errcode\":0,\"errmsg\":\"ok\"}', '2019-10-23 05:14:11', '2019-10-23 05:14:11', NULL),
(26, NULL, 'text', NULL, 83, '12321321321323', NULL, 'fail', '{\"errcode\":40003,\"errmsg\":\"invalid openid hint: [.gkvaa03294108]\"}', '2019-10-23 12:05:28', '2019-10-23 12:05:29', NULL),
(27, 'ohqcn1ljvKHXYZfBk3vNxIHSqYWA', 'image', NULL, 12, NULL, '/mnt/www/mall_admin_api/public/uploads/wechat/20191023/bA9BvU8fwSPmlRl5hN7aMLN0hmS31wFHTqPirg1L.jpeg', 'success', '{\"errcode\":0,\"errmsg\":\"ok\"}', '2019-10-28 07:31:48', '2019-10-28 07:31:50', NULL),
(28, 'ohqcn1kg9izcwhtAjU5Un8rAbj60', 'image', NULL, 5, NULL, '/mnt/www/mall_admin_api/public/uploads/wechat/20190805/mNwdgF9yOuosMyc8qSUk1yBVQBymUJZtwlXcXIq8.jpeg', 'success', '{\"errcode\":0,\"errmsg\":\"ok\"}', '2019-11-06 05:20:03', '2019-11-06 05:20:06', NULL),
(29, 'ohqcn1kg9izcwhtAjU5Un8rAbj60', 'image', NULL, 5, NULL, '/mnt/www/mall_admin_api/public/uploads/wechat/20190805/mNwdgF9yOuosMyc8qSUk1yBVQBymUJZtwlXcXIq8.jpeg', 'success', '{\"errcode\":0,\"errmsg\":\"ok\"}', '2019-11-12 06:15:07', '2019-11-12 06:15:11', NULL),
(30, 'oKmdD6qbDRdzCEMbrvNjca-RA5SQ', 'image', NULL, 146, NULL, 'E:\\workspace\\backend_api_laravel7\\public\\uploads/wechat/20210925/CJxQJmgFunciwtYogMJw3kmaRlb5DohOFeOMamT9.png', 'success', '{\"errcode\":0,\"errmsg\":\"ok\"}', '2021-09-25 01:31:23', '2021-09-25 01:31:27', NULL);

--
-- 转储表的索引
--

--
-- 表的索引 `member_pic_txt`
--
ALTER TABLE `member_pic_txt`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `member_pic_txt`
--
ALTER TABLE `member_pic_txt`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

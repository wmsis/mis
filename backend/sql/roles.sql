-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1
-- 生成日期： 2021-09-29 04:37:29
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
-- 表的结构 `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `desc` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `roles`
--

INSERT INTO `roles` (`id`, `name`, `desc`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '管理员', '系统管理员，最高权限', '2018-02-28 03:10:30', '2018-08-17 01:01:27', NULL),
(2, '维护人员', '维护人员', '2018-02-28 03:58:50', '2020-06-20 06:06:50', NULL),
(3, '财务', '公司财务组', '2018-02-28 03:59:11', '2018-08-17 01:01:08', NULL),
(4, '仓储人员', '仓储人员', '2018-08-10 05:22:02', '2020-05-19 02:26:50', NULL),
(5, '设计人员', '设计人员', '2018-08-17 01:05:30', '2020-05-19 02:26:35', NULL),
(6, '打杂的', '打杂的', '2018-08-30 04:53:47', '2019-04-19 01:31:54', NULL),
(7, '程序人员', '程序人员', '2018-09-08 08:53:56', '2020-05-19 02:27:10', NULL),
(8, 'xx2', 'xx2', '2019-07-29 06:05:31', '2019-07-29 07:59:12', '2019-07-29 07:59:12'),
(9, 'xx', 'xx', '2019-07-29 07:58:26', '2019-07-29 07:59:06', '2019-07-29 07:59:06'),
(10, 'dwc测试', '测试111111', '2020-05-18 07:56:46', '2020-05-18 07:56:46', NULL);

--
-- 转储表的索引
--

--
-- 表的索引 `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1
-- 生成日期： 2022-10-11 16:33:58
-- 服务器版本： 5.7.26
-- PHP 版本： 7.3.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `wmmis_system`
--

-- --------------------------------------------------------

--
-- 表的结构 `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '登录用户名',
  `nickname` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '昵称',
  `password` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '密码',
  `type` enum('system','guest') COLLATE utf8mb4_unicode_ci DEFAULT 'guest' COMMENT '用户类型',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `admin`
--

INSERT INTO `admin` (`id`, `username`, `nickname`, `password`, `type`, `created_at`, `updated_at`) VALUES
(1, '17091952061', '神猫', '$2y$10$lxNRv15qdLjOJLNWvM5hz.MeProVzYudK6EuGDXTcqP.kMG.md4iS', 'system', '2022-09-05 22:26:13', '2022-09-05 22:26:13'),
(2, '13600672232', '猫小鱼', '$2y$10$s87PgwkhoYDOVXDQF/GtIeyesjMm0vp0d6HVWTVl/gK9T0ST1P6Hy', 'system', '2022-09-06 23:16:14', '2022-09-24 00:16:44');

-- --------------------------------------------------------

--
-- 表的结构 `tenement`
--

CREATE TABLE `tenement` (
  `id` int(11) NOT NULL,
  `ip` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '租户名称',
  `code` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '租户编码',
  `db_name` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '数据库名称',
  `db_user` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '数据库用户名',
  `db_pwd` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '数据库密码',
  `memo` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `tenement`
--

INSERT INTO `tenement` (`id`, `ip`, `username`, `code`, `db_name`, `db_user`, `db_pwd`, `memo`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '127.0.0.1', '伟明环保股份有限公司', 'wmhb', 'wmmis_data', 'root', '64y7nudx', '伟明环保股份有限公司', '2022-09-06 23:16:14', '2022-09-08 02:08:15', NULL);

--
-- 转储表的索引
--

--
-- 表的索引 `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- 表的索引 `tenement`
--
ALTER TABLE `tenement`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用表AUTO_INCREMENT `tenement`
--
ALTER TABLE `tenement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

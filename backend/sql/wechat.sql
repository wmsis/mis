-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1
-- 生成日期： 2021-09-25 09:40:09
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
-- 表的结构 `wechat`
--

CREATE TABLE `wechat` (
  `id` int(10) UNSIGNED NOT NULL,
  `headimgurl` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nickname` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `openid` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subscribe` int(11) DEFAULT NULL,
  `city` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `province` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unionid` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` enum('man','woman') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `platform` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `wechat`
--

INSERT INTO `wechat` (`id`, `headimgurl`, `nickname`, `openid`, `subscribe`, `city`, `province`, `country`, `unionid`, `gender`, `platform`, `member_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'http://thirdwx.qlogo.cn/mmopen/icm1VysytlsQFeibRMaxRUF6qicJj23KUy8VAzVxfAicjCpb8QMqycyROOLw8jNbN8LxVEmtc9PfiaBUic7dU7RTbh57jCCmzW4NiaT/132', '猫cat', 'ohqcn1kg9izcwhtAjU5Un8rAbj60', 1, '浦东新区', '上海', '中国', 'ogITH1H5G-CA7juySesllbSHMq2o', 'man', 'zjkdwl', 5, '2019-08-06 08:04:41', '2019-11-12 00:40:19', NULL),
(2, 'http://thirdwx.qlogo.cn/mmopen/ajNVdqHZLLCOl79iadjbv7SyzYgmJ9eGkuSG5CVYKxibNKpPt7JOxP6UYyEvoAvkW08B14d5K42nfh0licu2h6ZCw/132', '匆匆了那年', 'ohqcn1jolVCZ3SefvmHio8ItKkJQ', 1, '杭州', '浙江', '中国', 'ogITH1H9PEg-cNfzYcUGPtuXoFlw', 'man', 'ogITH1H9PEg-cNfzYcUGPtuXoFlw', 6, '2019-08-06 10:05:47', '2019-08-06 10:05:47', NULL),
(3, 'http://thirdwx.qlogo.cn/mmopen/Q3auHgzwzM7xEK5fqS7R4homJRtGicvMDq8H5dYkon8EnvSdaug6T9rMga7Yqd1QS5dhLjGU2JhQkiaX0l7icpcbBLTeVHolWRfl6m7rsrDK3I/132', '꯭꯭', 'ohqcn1nUZ7r6xwdNpjwD-Vee57CY', 1, '杭州', '浙江', '中国', 'ogITH1GRACeRrn0J8nOM1igUWO-A', 'man', 'ogITH1GRACeRrn0J8nOM1igUWO-A', 7, '2019-08-06 10:12:47', '2019-08-06 10:12:47', NULL),
(4, 'http://thirdwx.qlogo.cn/mmopen/C6nnRGnPbvwiaZP0IQfVdc1UsPSFf8fBTRmaAmNQ6hAvibqgEOx9mzzCQAjoNZLibk1eVqckddDwInMich6qV9zZRg/132', '🐻小熊仔的爸爸🐻', 'ohqcn1kjlOgw9F6wtpR6zT7S0_Oc', 0, '西宁', '青海', '中国', 'ogITH1B8Msmw56opg8usDzktNGdI', 'man', 'ogITH1B8Msmw56opg8usDzktNGdI', 8, '2019-08-06 15:42:15', '2019-08-06 15:42:37', NULL),
(5, 'http://thirdwx.qlogo.cn/mmopen/gXPAuUMcicuT0rVIwM8XoxqtBROOsquvMQ0CAR8hO1NBCzI8YiaYibKhMthu5oAWj722n034vqM267gT77QGDRNqsypKhoRFoqu/132', 'w＿', 'ohqcn1jeOoMnWfiy58t2_HOQSt1g', 0, '益阳', '湖南', '中国', 'ogITH1ICVprKPkrcMBl_okazn9Z0', 'man', 'ogITH1ICVprKPkrcMBl_okazn9Z0', 9, '2019-08-06 16:16:10', '2019-08-06 16:16:27', NULL),
(6, 'http://thirdwx.qlogo.cn/mmopen/U20cfeIUhrpTFz7LKJyHFyyUysvDnKk7r39W3RlIqXW6Te80roJ0Y2Yle4AeJaL8ackibXZP30TJP0MPl4KL7yNzzIQMeOU43/132', '不将就', 'ohqcn1nfbaiZcvhdWac0JJROtnbE', 0, '', '', '安道尔', 'ogITH1GMbMXulGdYHsYPp4eWdcIQ', 'man', 'ogITH1GMbMXulGdYHsYPp4eWdcIQ', 10, '2019-08-06 16:38:26', '2019-08-06 16:39:04', NULL),
(7, 'http://thirdwx.qlogo.cn/mmopen/icm1VysytlsTpK0DLoMT0oibNepicXWWyXXRBqtxpmE0VTBKbIXdl93SnsXyawBQEtpJ3LgF0lzotRbY2JuhPClnPZfqk6mvgHq/132', '伊...畘', 'ohqcn1h1QBpQAxbAQwOpBIGnLADI', 0, '杭州', '浙江', '中国', 'ogITH1OZzgwCN33Fq9HFpJaV8kN0', 'man', 'ogITH1OZzgwCN33Fq9HFpJaV8kN0', 11, '2019-08-06 23:54:22', '2019-08-06 23:54:47', NULL),
(9, 'http://thirdwx.qlogo.cn/mmopen/CtuoKI2Yn2bgz3FBIQS17W5MtbYtnPV8k9TWSI3acr5v82iblxFFkmWvxk61IzYgErNqQpAiaEPBKhw8xpicNyXEibadU1dHyiaZk/132', '王忠星', 'ohqcn1ljvKHXYZfBk3vNxIHSqYWA', 1, '', '', '泽西岛', 'ogITH1D_-k3qVBdjl_Yg9GcTBhsU', 'man', 'zjkdwl', 12, '2019-10-19 08:00:19', '2019-10-28 07:31:46', NULL),
(10, 'http://thirdwx.qlogo.cn/mmopen/8IqXSB33R1dtA5jYBZJibB2hDKzQHPuGN5kI52swWRDHlUvYpnn4e2Jy3lCadNiaHrKWXxicrvJTmUMjNRHfU7N5cSBIHiaG0e37/132', '姚十二', 'ohqcn1vLKQIaGyQcgeNamExxjj7M', 0, '杭州', '浙江', '中国', 'ogITH1AhChbd7ZawwJjpaT44KJqE', 'man', 'ogITH1AhChbd7ZawwJjpaT44KJqE', 58, '2019-10-20 13:00:30', '2019-10-20 13:02:36', NULL),
(11, 'http://thirdwx.qlogo.cn/mmopen/DlgWNxBvl81SE3pdTMibEMjZOk4lHEs4dTwGB6o3522On8uvnPAyt76dlC3YRgQyzicibpykQRcR0xpMYPUMJGqn1V6s5wEyWIz/132', '月上柳梢头', 'ohqcn1qAvqdVWYqOC9_X0QyGXP8s', 1, '遵义', '贵州', '中国', 'ogITH1D97QEYYUgaGqVhRYBdYUjQ', 'man', 'ogITH1D97QEYYUgaGqVhRYBdYUjQ', 76, '2019-10-23 03:28:28', '2019-10-23 03:28:28', NULL),
(12, 'http://thirdwx.qlogo.cn/mmopen/gXPAuUMcicuT0rVIwM8XoxoUoGRT3PIpvPFibRauK9k9EvZEcjfFnMnib5MJRI9mcxatolE5T8td1Qm4pyZEfOuAcyibbzGVqJiad/132', '有时候', 'ohqcn1iy5FA-6BOR3UNougO4K2YM', 1, '杭州', '浙江', '中国', 'ogITH1Jiv_WUqN01FFz8mQrKMufc', 'man', 'ogITH1Jiv_WUqN01FFz8mQrKMufc', 77, '2019-10-23 03:56:34', '2019-10-23 03:56:34', NULL),
(13, 'http://thirdwx.qlogo.cn/mmopen/OpDgtmSY9rWRGkseG1t5OYBttnAEKJY6aZl8qyCCl3psE8FMMoxaKa4RO94CbZ1EGkbGvzoLiaOTasfny8QXCKGQq5BtnFW9t/132', '🍧', 'ohqcn1n_w0YEuub-tO0JAaVH0k0A', 1, '杭州', '浙江', '中国', 'ogITH1C3inp74GipRuA6NAhhDK5U', 'man', 'ogITH1C3inp74GipRuA6NAhhDK5U', 84, '2019-10-23 11:15:50', '2019-10-23 11:15:50', NULL),
(14, 'http://thirdwx.qlogo.cn/mmopen/CtuoKI2Yn2bgz3FBIQS17SOibdkQS0D1CHBVHpJlTYPpP8JApZBl5ibN1Gj1YBV4k4ia0d9v0AqztmXvF96rCialfiaz0UHS25vQs/132', '3\'1', 'ohqcn1ixTwcuzUMUTHHs2Ffp1-kI', 1, '成都', '四川', '中国', 'ogITH1MlP-jWNqVie-Jk2fwqApmA', 'man', 'ogITH1MlP-jWNqVie-Jk2fwqApmA', 88, '2019-10-24 07:31:13', '2019-10-24 07:31:13', NULL),
(15, 'http://thirdwx.qlogo.cn/mmopen/C6nnRGnPbvxZDibepChKBhm95MukRicQus7L3MP2g1B8ZYpMBPfODagdy4KTUGWh5oLd1TYuqgQsG2dKV2Iiaib7kibWG9CxoNw0r/132', '叶竹与宁คิดถึง', 'ohqcn1lZt5d8m3R4AcX32FZ7m-ho', 0, '', '', '', 'ogITH1L4l37xEWodIIPEX691c-40', 'woman', 'ogITH1L4l37xEWodIIPEX691c-40', 89, '2019-10-24 10:45:51', '2019-10-24 10:46:20', NULL),
(16, 'http://thirdwx.qlogo.cn/mmopen/8IqXSB33R1dtA5jYBZJibB8oZ8SxPwNTOwicWsdJYcR7vmrlxAtjiaanw6x5VmB1EAScSy1em7I3Unx1xrknzWoBqX4OUsBo23M/132', 'nI_JIe', 'ohqcn1vA_s46UTPKWODl5dN8o97M', 0, '湖州', '浙江', '中国', 'ogITH1IY4aAiIiEizJ4QH8YEuhak', 'man', 'ogITH1IY4aAiIiEizJ4QH8YEuhak', 110, '2019-11-08 04:19:06', '2019-11-08 04:20:32', NULL),
(21, 'http://thirdwx.qlogo.cn/mmopen/PiajxSqBRaELqRJtoSwGto4bkJrF4ohYm6C8K2DuoatzdaEw6yNn2maAKx16bEaMmYticblOICYMgBSsBdc2NHiaw/132', 'ぃ丽丽稳', 'ohqcn1ra55ZRgaaMYpoa3T0thNCM', 1, '松江', '上海', '中国', 'ogITH1Bez2tbtkGyfVDw-Fhd7ZM0', 'man', 'zjkdwl', 91, '2019-11-17 09:25:13', '2019-11-17 09:25:13', NULL),
(23, 'http://thirdwx.qlogo.cn/mmopen/8IqXSB33R1emE0vREuEoMsxdjmwSicMQLnfTOu7Nq09VmDE6VfRO9Jg3yicWMCOMsVaoyUg5AEkUYichgVFbKjuqw/132', '携途|周斌 ¹⁸⁸¹⁴⁸⁶⁷⁶²⁵', 'ohqcn1uN1qtJI1-5tXUqB7w7npaU', 1, '', '东京', '日本', 'ogITH1DC2riUOINhBAbm1eHv5Fck', 'man', 'zjkdwl', 14, '2019-11-17 10:38:06', '2019-11-17 10:38:06', NULL),
(24, 'http://thirdwx.qlogo.cn/mmopen/8IqXSB33R1dtA5jYBZJibB5ARw87O9FtX2K8rvaobWTlSKSusXRpDoOsEXXzibVV4g95JeIGNNejMxcdoVqUbxXnsT3l8U3cY2/132', '沈斌', 'ohqcn1k0q-wloyCqjs9kC84369AQ', 1, '松江', '上海', '中国', 'ogITH1JDVXXO3axB_pNNZjfw2Dso', 'man', 'zjkdwl', 16, '2019-11-18 03:11:24', '2019-11-18 03:11:24', NULL),
(25, 'http://thirdwx.qlogo.cn/mmopen/CtuoKI2Yn2a1f3DWtU1tCLBj9EwBkb3oKtJn5whjNJV5EjpcflHySVPEQ3MQQvuZm4Fd8O4V8gn7V4Ou5NicML6RQyJ7DoeqW/132', '1111111-口袋', 'ohqcn1uQyGZ6ZRj3Q7cyqMIbbTgs', 1, '', '', '', 'ogITH1L1sqPY8QFhff9XesWdCg_0', 'woman', 'zjkdwl', 105, '2019-11-18 03:19:40', '2019-11-18 03:19:40', NULL),
(26, 'http://thirdwx.qlogo.cn/mmopen/8IqXSB33R1dtA5jYBZJibBwIVAqY7VTqER2jpXYvK50At5SJ1TiaianpG7ia202sN09rLxanuSXqypHQKheQt2hSbUjkfEPm6MCf/132', '开心', 'ohqcn1gLYh0aoo0eNn6SLCO-AIds', 1, '青浦', '上海', '中国', 'ogITH1IfXdeWOcOXEhPPdrol1MAY', 'man', 'zjkdwl', 132, '2019-11-18 06:17:37', '2019-11-18 06:17:37', NULL),
(29, 'http://thirdwx.qlogo.cn/mmopen/ajNVdqHZLLCFSnVjQpIV0zNWcEcsljiaZKXzEwic7qBhjo21iaws3vDcDibJDcqicId1OtFXl6ibakY1GIuBqDLLJmLA/132', 'cat', 'ohqcn1j7GzjaFzUoQTL5zqlujaqc', 1, '徐汇', '上海', '中国', 'ogITH1HzCrqWa96EQzNVSOOfTgxk', 'man', 'zjkdwl', 144, '2019-11-19 02:18:10', '2019-11-19 02:18:10', NULL),
(30, 'http://thirdwx.qlogo.cn/mmopen/IxSNsOria2NTibspvIDKrMicvcYh49J24kqSBia160xXbJSic6JI8ibBT09PCnwibcK1fVkat24uan3A2b0nKFpoD3b5VsibEkwbibkor/132', '猫cat', 'oKmdD6qbDRdzCEMbrvNjca-RA5SQ', 1, '浦东新区', '上海', '中国', 'ojTR4uDKySrO8Pu0Lbu1megT9FFU', 'man', 'test', 146, '2021-09-25 01:01:07', '2021-09-25 01:39:33', NULL),
(31, 'http://thirdwx.qlogo.cn/mmopen/ajNVdqHZLLBSdWeL4N1DI0TzeKu8zluVllicdPxPyVD3a97gibTvOmaC8NjZiciaQ5mTCFJbNHKoNz1dquWsibd9C9g/132', 'ChristDing', 'oKmdD6vznXHxiSGmH0at_ugZd4tw', 1, '温州', '浙江', '中国', 'ojTR4uO-QMl19ijI7T-FDizWZXA8', 'man', 'test', 147, '2021-09-25 01:24:14', '2021-09-25 01:24:14', NULL);

--
-- 转储表的索引
--

--
-- 表的索引 `wechat`
--
ALTER TABLE `wechat`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `wechat`
--
ALTER TABLE `wechat`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

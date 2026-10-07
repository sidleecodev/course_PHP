-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2026-10-07 10:16:12
-- 伺服器版本： 10.4.32-MariaDB
-- PHP 版本： 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `mydb_backup`
--

-- --------------------------------------------------------

--
-- 資料表結構 `city`
--

CREATE TABLE `city` (
  `id` int(11) NOT NULL,
  `title` varchar(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 傾印資料表的資料 `city`
--

INSERT INTO `city` (`id`, `title`) VALUES
(1, '台中市'),
(2, '台北市'),
(3, '高雄市');

-- --------------------------------------------------------

--
-- 資料表結構 `edu`
--

CREATE TABLE `edu` (
  `id` int(11) NOT NULL,
  `title` varchar(3) DEFAULT NULL COMMENT '課程名稱'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 傾印資料表的資料 `edu`
--

INSERT INTO `edu` (`id`, `title`) VALUES
(1, '高中職'),
(2, '大學'),
(3, '研究所');

-- --------------------------------------------------------

--
-- 資料表結構 `member`
--

CREATE TABLE `member` (
  `id` int(11) NOT NULL COMMENT '流水號',
  `userId` char(10) NOT NULL COMMENT '身分證',
  `userName` varchar(30) NOT NULL COMMENT '姓名',
  `eduId` int(11) DEFAULT NULL COMMENT '學歷id',
  `cityId` int(11) DEFAULT NULL COMMENT '城市id',
  `createTime` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '創建時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='會員';

--
-- 傾印資料表的資料 `member`
--

INSERT INTO `member` (`id`, `userId`, `userName`, `eduId`, `cityId`, `createTime`) VALUES
(1, 'A000000001', '測試一', 1, NULL, '2026-10-07 01:48:25'),
(2, 'A000000002', '測試二', 2, 2, '2026-10-07 01:48:25'),
(3, 'A000000003', '測試三', 2, 3, '2026-10-07 03:33:48'),
(4, 'A000000004', '測試四', 1, 3, '2026-10-07 03:33:48'),
(5, 'A000000005', '測試五', 0, 1, '2026-10-07 06:26:01');

-- --------------------------------------------------------

--
-- 資料表結構 `order_detail`
--

CREATE TABLE `order_detail` (
  `id` int(11) NOT NULL,
  `orderId` char(3) NOT NULL,
  `productId` int(11) NOT NULL,
  `createTime` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 傾印資料表的資料 `order_detail`
--

INSERT INTO `order_detail` (`id`, `orderId`, `productId`, `createTime`) VALUES
(1, '123', 1, '2026-10-07 08:08:06'),
(2, '456', 1, '2026-10-07 08:08:36'),
(3, '456', 2, '2026-10-07 08:08:36');

-- --------------------------------------------------------

--
-- 資料表結構 `order_master`
--

CREATE TABLE `order_master` (
  `orderId` char(3) NOT NULL,
  `memberId` int(11) NOT NULL,
  `totalAmount` int(11) NOT NULL,
  `createTime` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 傾印資料表的資料 `order_master`
--

INSERT INTO `order_master` (`orderId`, `memberId`, `totalAmount`, `createTime`) VALUES
('123', 1, 100, '2026-10-07 08:07:30'),
('456', 3, 200, '2026-10-07 08:07:30');

-- --------------------------------------------------------

--
-- 資料表結構 `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `productName` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 傾印資料表的資料 `product`
--

INSERT INTO `product` (`id`, `productName`) VALUES
(1, '產品一'),
(2, '產品二');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `city`
--
ALTER TABLE `city`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `edu`
--
ALTER TABLE `edu`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `order_detail`
--
ALTER TABLE `order_detail`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `order_master`
--
ALTER TABLE `order_master`
  ADD PRIMARY KEY (`orderId`);

--
-- 資料表索引 `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `city`
--
ALTER TABLE `city`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `edu`
--
ALTER TABLE `edu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `member`
--
ALTER TABLE `member`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '流水號', AUTO_INCREMENT=6;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `order_detail`
--
ALTER TABLE `order_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

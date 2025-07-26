-- Adminer 4.7.1 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP VIEW IF EXISTS `_stock_history_daily`;
CREATE TABLE `_stock_history_daily` (`date` date, `item_id` int(11), `quantity_in` double, `quantity_out` double);


DROP TABLE IF EXISTS `_stock_history_daily`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `_stock_history_daily` AS select `_stock_history`.`date` AS `date`,`_stock_history`.`item_id` AS `item_id`,sum(case when `_stock_history`.`transaction_type` = 'i' then `_stock_history`.`quantity` else 0 end) AS `quantity_in`,sum(case when `_stock_history`.`transaction_type` = 'o' then `_stock_history`.`quantity` else 0 end) AS `quantity_out` from `_stock_history` group by `_stock_history`.`item_id`,`_stock_history`.`date` order by `_stock_history`.`date`;

-- 2019-10-22 00:09:27

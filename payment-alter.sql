ALTER TABLE `incoming`
ADD `total_payment` double NULL AFTER `count_of_items`,
ADD `payment_status` int NULL AFTER `total_payment`;

ALTER TABLE `outgoing`
ADD `total_payment` double NULL AFTER `count_of_items`,
ADD `payment_status` int NULL AFTER `total_payment`;
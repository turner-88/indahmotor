CREATE OR REPLACE VIEW `_balance_history` AS
select 
`payment`.`date` AS `date`,
'i' AS `transaction_type`,
`payment`.`id` AS `transaction_id`,
`payment`.`amount` AS `in`,
'' AS `out`,
if(`payment`.`remark` <> '',concat(`customer`.`name`,' - ',`payment`.`remark`),`customer`.`name`) AS `remark` 
from (`payment` left join `customer` on(`payment`.`customer_id` = `customer`.`id`)) 
where `payment`.`customer_id` is not null

union 

select 
`payment`.`date` AS `date`,
'o' AS `transaction_type`,
`payment`.`id` AS `transaction_id`,
'' AS `in`,
`payment`.`amount` AS `out`,
if(`payment`.`remark` <> '',concat(`supplier`.`name`,' - ',`payment`.`remark`),`supplier`.`name`) AS `remark` 
from (`payment` left join `supplier` on(`payment`.`supplier_id` = `supplier`.`id`)) 
where `payment`.`supplier_id` is not null

union 

select 
`expense`.`date` AS `date`,
'o' AS `transaction_type`,
`expense`.`id` AS `transaction_id`,
'' AS `in`,
`expense`.`amount` AS `out`,
if(`expense`.`person_in_charge` <> '',concat(`expense`.`person_in_charge`,' - ',`expense`.`name`),`expense`.`name`) AS `remark` 
from `expense` 

union 

select 
`capital`.`date` AS `date`,
'i' AS `transaction_type`,
`capital`.`id` AS `transaction_id`,
`capital`.`amount` AS `in`,
'' AS `out`,
`capital`.`name` AS `remark` 
from `capital` 

order by `date`,`transaction_type`
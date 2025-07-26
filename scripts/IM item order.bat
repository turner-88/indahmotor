cd C:\Users\Indah Motor\Documents
C:\xampp\mysql\bin\mysqldump -d -u root indahmotor > indahmotor-structure.sql
C:\xampp\mysql\bin\mysqldump -u root indahmotor user auth_assignment auth_item auth_item_child auth_rule config item item_location order order_item > indahmotor-item-order.sql
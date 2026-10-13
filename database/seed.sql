USE inventory_db;
INSERT INTO users(name,email,password_hash,role) VALUES ('Admin Demo','admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llCw5XlmT6By9wZL8o6u', 'admin'),('Staff Demo','staff@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llCw5XlmT6By9wZL8o6u', 'staff');
INSERT INTO categories(name) VALUES ('Elektronik'),('Aksesori'),('Kantor');
INSERT INTO suppliers(name,phone,email) VALUES ('PT Sumber Teknologi','08123456789','supplier@example.com'),('CV Maju Jaya','082233445566','maju@example.com');
INSERT INTO products(category_id,sku,name,price,stock,minimum_stock) VALUES (1,'EL-001','Keyboard Mekanik',450000,12,5),(1,'EL-002','Mouse Wireless',175000,4,5),(2,'AK-001','USB-C Hub',225000,8,3);
INSERT INTO product_suppliers VALUES (1,1,400000),(1,2,410000),(2,1,150000),(3,2,200000);
INSERT INTO stock_movements(product_id,user_id,type,quantity,note) VALUES (1,1,'IN',12,'Seed demo'),(2,1,'IN',4,'Seed demo'),(3,1,'IN',8,'Seed demo');

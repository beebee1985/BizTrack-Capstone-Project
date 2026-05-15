-- Sample data for BizTrack

-- Products
INSERT INTO products (user_id, name, description, category, price, cost, stock_quantity, sku, status) VALUES
(1, 'Office Chair', 'Ergonomic adjustable office chair', 'Furniture', 249.99, 120.00, 15, 'FRN-001', 'active'),
(1, 'Standing Desk', 'Height-adjustable standing desk', 'Furniture', 499.99, 260.00, 8, 'FRN-002', 'active'),
(1, 'Laptop Stand', 'Aluminium portable laptop stand', 'Accessories', 49.99, 18.00, 40, 'ACC-001', 'active'),
(1, 'Wireless Keyboard', 'Compact wireless keyboard', 'Electronics', 79.99, 35.00, 30, 'ELC-001', 'active'),
(1, 'Wireless Mouse', 'Ergonomic wireless mouse', 'Electronics', 39.99, 15.00, 50, 'ELC-002', 'active'),
(1, 'USB-C Hub', '7-in-1 USB-C hub', 'Electronics', 59.99, 22.00, 25, 'ELC-003', 'active'),
(1, 'Desk Lamp', 'LED dimmable desk lamp', 'Accessories', 34.99, 12.00, 35, 'ACC-002', 'active'),
(1, 'Monitor Arm', 'Dual monitor arm mount', 'Accessories', 89.99, 40.00, 20, 'ACC-003', 'active'),
(1, 'Whiteboard', '4x3 ft magnetic whiteboard', 'Office Supplies', 119.99, 55.00, 10, 'OFC-001', 'active'),
(1, 'Printer Paper', 'A4 printer paper 500 sheets', 'Office Supplies', 12.99, 5.00, 100, 'OFC-002', 'active');

-- Customers (Canadian names, emails, and locations)
INSERT INTO customers (user_id, name, email, phone, address, city, state, zip_code, notes) VALUES
(1, 'Sarah Thompson', 'sarah.thompson@gmail.com', '+1-416-555-0182', '245 Bay Street', 'Toronto', 'Ontario', 'M5J 2T3', 'Regular corporate client'),
(1, 'Michael Chen', 'mchen@techworks.ca', '+1-604-555-0247', '1055 West Georgia St', 'Vancouver', 'British Columbia', 'V6E 3P3', 'Prefers card payments'),
(1, 'Emma Tremblay', 'emma.tremblay@outlook.com', '+1-514-555-0318', '1000 Rue de la Gauchetiere', 'Montreal', 'Quebec', 'H3B 4W5', ''),
(1, 'James MacDonald', 'j.macdonald@northernbiz.ca', '+1-403-555-0463', '888 3rd Street SW', 'Calgary', 'Alberta', 'T2P 5C5', 'Bulk buyer'),
(1, 'Olivia Patel', 'olivia.patel@gmail.com', '+1-613-555-0574', '150 Slater Street', 'Ottawa', 'Ontario', 'K1P 5H3', ''),
(1, 'Liam Bourgeois', 'liam.bourgeois@startupca.com', '+1-780-555-0629', '10060 Jasper Ave', 'Edmonton', 'Alberta', 'T5J 3R8', 'Startup owner'),
(1, 'Sophia Kowalski', 'sophia.k@corp.ca', '+1-204-555-0735', '360 Main Street', 'Winnipeg', 'Manitoba', 'R3C 3Z3', 'Prefers online payment'),
(1, 'Noah Williams', 'noah.williams@gmail.com', '+1-902-555-0891', '1701 Hollis Street', 'Halifax', 'Nova Scotia', 'B3J 3M8', '');

-- Sales (April & May 2026)
INSERT INTO sales (user_id, customer_id, sale_date, total_amount, discount, tax, payment_method, status, notes) VALUES
(1, 1, '2026-04-02', 499.98, 0.00, 0.00, 'card', 'completed', ''),
(1, 2, '2026-04-05', 119.98, 10.00, 0.00, 'cash', 'completed', ''),
(1, 3, '2026-04-08', 249.99, 0.00, 0.00, 'card', 'completed', ''),
(1, 4, '2026-04-10', 719.96, 50.00, 0.00, 'online', 'completed', 'Bulk order'),
(1, 5, '2026-04-14', 74.98, 0.00, 0.00, 'cash', 'completed', ''),
(1, 6, '2026-04-17', 109.98, 0.00, 0.00, 'card', 'completed', ''),
(1, 1, '2026-04-21', 59.99, 0.00, 0.00, 'online', 'completed', ''),
(1, 7, '2026-04-25', 319.98, 20.00, 0.00, 'card', 'completed', ''),
(1, 8, '2026-04-28', 119.99, 0.00, 0.00, 'cash', 'completed', ''),
(1, 3, '2026-04-30', 97.97, 0.00, 0.00, 'card', 'completed', ''),
(1, 2, '2026-05-02', 289.98, 0.00, 0.00, 'cash', 'completed', ''),
(1, 4, '2026-05-05', 499.99, 0.00, 0.00, 'online', 'completed', ''),
(1, 5, '2026-05-07', 129.98, 0.00, 0.00, 'card', 'completed', ''),
(1, 6, '2026-05-09', 79.99, 0.00, 0.00, 'cash', 'completed', ''),
(1, 1, '2026-05-11', 599.98, 30.00, 0.00, 'card', 'completed', ''),
(1, 7, '2026-05-12', 94.98, 0.00, 0.00, 'online', 'completed', ''),
(1, 8, '2026-05-13', 299.98, 0.00, 0.00, 'card', 'completed', ''),
(1, 3, '2026-05-14', 154.98, 0.00, 0.00, 'cash', 'completed', ''),
(1, 2, '2026-05-14', 49.99, 0.00, 0.00, 'card', 'completed', ''),
(1, 4, '2026-05-15', 359.97, 15.00, 0.00, 'online', 'completed', '');

-- Sale items
INSERT INTO sale_items (sale_id, product_id, product_name, quantity, unit_price, subtotal) VALUES
(1, 1, 'Office Chair', 2, 249.99, 499.98),
(2, 4, 'Wireless Keyboard', 1, 79.99, 79.99),
(2, 5, 'Wireless Mouse', 1, 39.99, 39.99),
(3, 1, 'Office Chair', 1, 249.99, 249.99),
(4, 2, 'Standing Desk', 1, 499.99, 499.99),
(4, 4, 'Wireless Keyboard', 1, 79.99, 79.99),
(4, 5, 'Wireless Mouse', 1, 39.99, 39.99),
(4, 6, 'USB-C Hub', 1, 59.99, 59.99),
(4, 7, 'Desk Lamp', 1, 34.99, 34.99),
(5, 5, 'Wireless Mouse', 1, 39.99, 39.99),
(5, 7, 'Desk Lamp', 1, 34.99, 34.99),
(6, 3, 'Laptop Stand', 2, 49.99, 99.98),
(6, 6, 'USB-C Hub', 1, 59.99, 59.99),
(7, 6, 'USB-C Hub', 1, 59.99, 59.99),
(8, 1, 'Office Chair', 1, 249.99, 249.99),
(8, 8, 'Monitor Arm', 1, 89.99, 89.99),
(9, 9, 'Whiteboard', 1, 119.99, 119.99),
(10, 3, 'Laptop Stand', 1, 49.99, 49.99),
(10, 7, 'Desk Lamp', 1, 34.99, 34.99),
(10, 10, 'Printer Paper', 1, 12.99, 12.99),
(11, 4, 'Wireless Keyboard', 1, 79.99, 79.99),
(11, 1, 'Office Chair', 1, 249.99, 249.99),
(12, 2, 'Standing Desk', 1, 499.99, 499.99),
(13, 5, 'Wireless Mouse', 1, 39.99, 39.99),
(13, 8, 'Monitor Arm', 1, 89.99, 89.99),
(14, 4, 'Wireless Keyboard', 1, 79.99, 79.99),
(15, 2, 'Standing Desk', 1, 499.99, 499.99),
(15, 3, 'Laptop Stand', 2, 49.99, 99.98),
(16, 5, 'Wireless Mouse', 1, 39.99, 39.99),
(16, 7, 'Desk Lamp', 1, 34.99, 34.99),
(16, 6, 'USB-C Hub', 1, 59.99, 59.99),
(17, 1, 'Office Chair', 1, 249.99, 249.99),
(17, 3, 'Laptop Stand', 1, 49.99, 49.99),
(18, 5, 'Wireless Mouse', 1, 39.99, 39.99),
(18, 4, 'Wireless Keyboard', 1, 79.99, 79.99),
(18, 7, 'Desk Lamp', 1, 34.99, 34.99),
(19, 3, 'Laptop Stand', 1, 49.99, 49.99),
(20, 8, 'Monitor Arm', 2, 89.99, 179.98),
(20, 6, 'USB-C Hub', 1, 59.99, 59.99),
(20, 10, 'Printer Paper', 10, 12.99, 129.90);

-- Expenses (April & May 2026)
INSERT INTO expenses (user_id, category, description, amount, expense_date, payment_method, notes) VALUES
(1, 'Rent', 'Office space monthly rent', 1200.00, '2026-04-01', 'online', 'April rent'),
(1, 'Utilities', 'Electricity and water bill', 185.50, '2026-04-03', 'online', ''),
(1, 'Inventory', 'Restocked office chairs x5', 600.00, '2026-04-06', 'card', ''),
(1, 'Marketing', 'Facebook and Google ads', 250.00, '2026-04-10', 'card', 'April campaign'),
(1, 'Salaries', 'Staff salaries', 3500.00, '2026-04-30', 'online', 'April payroll'),
(1, 'Transport', 'Delivery costs for customer orders', 95.00, '2026-04-15', 'cash', ''),
(1, 'Internet', 'Monthly internet subscription', 60.00, '2026-04-01', 'online', ''),
(1, 'Inventory', 'Restocked keyboards and mice', 420.00, '2026-04-20', 'card', ''),
(1, 'Rent', 'Office space monthly rent', 1200.00, '2026-05-01', 'online', 'May rent'),
(1, 'Utilities', 'Electricity and water bill', 172.00, '2026-05-03', 'online', ''),
(1, 'Internet', 'Monthly internet subscription', 60.00, '2026-05-01', 'online', ''),
(1, 'Marketing', 'Instagram promotion', 150.00, '2026-05-07', 'card', ''),
(1, 'Transport', 'Delivery costs', 75.00, '2026-05-10', 'cash', ''),
(1, 'Inventory', 'Standing desks restock x3', 780.00, '2026-05-12', 'card', ''),
(1, 'Salaries', 'Staff salaries', 3500.00, '2026-05-15', 'online', 'May payroll');

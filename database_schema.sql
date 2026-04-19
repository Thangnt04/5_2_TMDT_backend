-- CƠ SỞ DỮ LIỆU CHỨC NĂNG 1: USER AUTHENTICATION & PHÂN QUYỀN
-- Gồm bảng người dùng, phân quyền (Admin / User)
CREATE TABLE roles (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL,
    description TEXT
);

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(150),
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE RESTRICT
);

-- Dữ liệu seed (mẫu) cho Role và User
INSERT INTO roles (role_name, description) VALUES 
('ADMIN', 'Quản trị viên toàn hệ thống'),
('USER', 'Khách hàng mua sắm');

INSERT INTO users (role_id, username, email, password_hash, full_name, phone) VALUES 
(1, 'admin_shop', 'admin@shop.com', '$2y$10$abcdefghijklmnopqrstuv', 'Admin Quản Lý', '0123456789'),
(2, 'hoangtucodon', 'nguyenvan@gmail.com', '$2y$10$abcdefghijklmnopqrstuv', 'Nguyễn Văn A', '0987654321');

-- -----------------------------------------------------
-- CƠ SỞ DỮ LIỆU CHỨC NĂNG 2: SẢN PHẨM & ĐẶT HÀNG (LOGISTICS)
-- Gồm bảng danh mục, sản phẩm, và đơn hàng
CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
);

CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    sku VARCHAR(50) UNIQUE,
    price INT NOT NULL,
    discount_price INT,
    stock_quantity INT DEFAULT 0,
    image_url VARCHAR(255),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE CASCADE
);

CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount INT NOT NULL,
    status ENUM('PENDING', 'PROCESSING', 'SHIPPED', 'DELIVERED', 'CANCELLED') DEFAULT 'PENDING',
    shipping_address TEXT NOT NULL,
    tracking_number VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE RESTRICT
);

CREATE TABLE order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL CHECK (quantity > 0),
    price_at_time INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE RESTRICT
);

-- Dữ liệu seed (mẫu) chức năng 2
INSERT INTO categories (name, slug, description) VALUES 
('Áo Thun', 'ao-thun', 'Áo thun mặc mát mẻ mùa hè'),
('Quần Jean', 'quan-jean', 'Quần jean phong cách hàn quốc');

INSERT INTO products (category_id, name, sku, price, stock_quantity) VALUES 
(1, 'Áo thun local brand Đen', 'AT_BLK_01', 250000, 100),
(2, 'Quần Jean ống rộng', 'QJ_RNG_01', 350000, 50);

INSERT INTO orders (user_id, total_amount, status, shipping_address) VALUES 
(2, 600000, 'PENDING', '123 Đường D1, Quận 9, TP.HCM');

INSERT INTO order_items (order_id, product_id, quantity, price_at_time) VALUES 
(1, 1, 1, 250000),
(1, 2, 1, 350000);

CREATE DATABASE IF NOT EXISTS shilpanepal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE shilpanepal;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY, full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE, phone VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL, address TEXT NOT NULL,
    role ENUM('admin','customer') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY, category_id INT NOT NULL,
    name VARCHAR(200) NOT NULL, slug VARCHAR(200) NOT NULL UNIQUE,
    description TEXT, price DECIMAL(10,2) NOT NULL, stock INT NOT NULL DEFAULT 0,
    image VARCHAR(255), featured TINYINT(1) DEFAULT 0, low_stock_threshold INT DEFAULT 5,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1, added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart (user_id, product_id)
);
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL,
    full_name VARCHAR(100) NOT NULL, email VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL, address TEXT NOT NULL, city VARCHAR(100) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
    payment_status ENUM('unpaid','paid','refunded') DEFAULT 'unpaid',
    notes TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY, order_id INT NOT NULL, product_id INT NOT NULL,
    quantity INT NOT NULL, price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY, order_id INT NOT NULL,
    transaction_id VARCHAR(255), ref_id VARCHAR(255),
    method ENUM('esewa') DEFAULT 'esewa', amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending','completed','failed','refunded') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, product_id INT NOT NULL,
    rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5), comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY one_review (user_id, product_id)
);
CREATE TABLE email_logs (
    id INT AUTO_INCREMENT PRIMARY KEY, order_id INT NOT NULL,
    type ENUM('order_placed','order_shipped','order_delivered') NOT NULL,
    sent_to VARCHAR(100) NOT NULL, sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- Admin user (password: Admin@1234)
INSERT INTO users (full_name,email,phone,password,address,role) VALUES
('ShilpaNepal Admin','admin@shilpanepal.com','9840339908',
'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Kirtipur, Kathmandu, Nepal','admin');

INSERT INTO categories (name,slug) VALUES
('Thangka Paintings','thangka-paintings'),('Pashmina Shawls','pashmina-shawls'),
('Singing Bowls','singing-bowls'),('Dhaka Fabric','dhaka-fabric'),
('Lokta Paper','lokta-paper'),('Wooden Carvings','wooden-carvings'),
('Handmade Jewelry','handmade-jewelry'),('Felt Products','felt-products');

INSERT INTO products (category_id,name,slug,description,price,stock,featured,low_stock_threshold) VALUES
(1,'Green Tara Thangka','green-tara-thangka','Hand-painted Green Tara Thangka on cotton canvas with 24K gold details.',4500.00,10,1,5),
(1,'Medicine Buddha Thangka','medicine-buddha-thangka','Traditional Medicine Buddha Thangka by skilled artists of Bhaktapur.',5500.00,8,0,5),
(1,'White Tara Thangka','white-tara-thangka','Hand-painted White Tara Thangka symbolizing compassion and long life.',5200.00,8,0,5),
(1,'Mandala Thangka','mandala-thangka','Intricate Kalachakra Mandala Thangka painted by master artists.',6800.00,5,1,3),
(2,'Pure Pashmina Shawl','pure-pashmina-shawl','100% pure Pashmina from Himalayan goats. Lightweight, soft and warm.',3200.00,25,1,5),
(2,'Embroidered Pashmina Wrap','embroidered-pashmina-wrap','Hand-embroidered Pashmina shawl with traditional Nepali motifs.',3800.00,15,0,5),
(2,'Cashmere Pashmina Blanket','cashmere-pashmina-blanket','Extra-large pure cashmere Pashmina blanket.',7500.00,10,1,3),
(2,'Pashmina Scarf Set','pashmina-scarf-set','Set of 3 lightweight Pashmina scarves in complementary colors.',2200.00,25,0,5),
(3,'Himalayan Singing Bowl Set','himalayan-singing-bowl-set','Handcrafted 7-metal singing bowl with mallet and cushion.',2500.00,20,1,5),
(3,'Large Bronze Singing Bowl','large-bronze-singing-bowl','Extra large handmade bronze singing bowl.',4200.00,12,0,5),
(3,'Seven Chakra Singing Bowl','seven-chakra-singing-bowl','Tuned 7-metal singing bowl engraved with chakra symbols.',3200.00,15,1,5),
(3,'Mini Singing Bowl Set','mini-singing-bowl-set','Set of 3 mini singing bowls in different sizes.',1800.00,20,0,5),
(4,'Dhaka Topi & Shawl Set','dhaka-topi-shawl-set','Traditional Nepali Dhaka fabric Topi with matching shawl.',1800.00,30,1,5),
(4,'Dhaka Table Runner','dhaka-table-runner','Handwoven Dhaka fabric table runner in traditional patterns.',850.00,40,0,8),
(4,'Dhaka Wallet','dhaka-wallet','Handcrafted bifold wallet made from authentic Dhaka fabric.',650.00,35,0,8),
(4,'Dhaka Cushion Cover Set','dhaka-cushion-cover-set','Set of 2 handwoven Dhaka fabric cushion covers.',1200.00,22,1,5),
(5,'Lokta Paper Journal','lokta-paper-journal','Handmade Lokta paper journal with traditional block-printed cover.',650.00,50,0,8),
(5,'Lokta Paper Gift Bags Set','lokta-paper-gift-bags-set','Set of 5 handmade Lokta paper gift bags.',450.00,60,0,10),
(5,'Lokta Paper Photo Album','lokta-paper-photo-album','Handmade Lokta paper photo album with hand-stitched binding.',950.00,30,1,5),
(5,'Lokta Paper Greeting Cards','lokta-paper-greeting-cards','Pack of 10 handmade Lokta paper greeting cards.',350.00,80,0,15),
(6,'Ganesh Wood Carving','ganesh-wood-carving','Intricately hand-carved Lord Ganesh statue from Sal wood.',3500.00,7,1,3),
(6,'Lakshmi Wood Carving','lakshmi-wood-carving','Hand-carved Goddess Lakshmi statue from seasoned Sal wood.',4200.00,6,1,3),
(6,'Carved Window Panel','carved-window-panel','Traditional Newari hand-carved wooden window panel.',8500.00,4,0,2),
(7,'Silver Filigree Necklace','silver-filigree-necklace','Hand-crafted 925 sterling silver filigree necklace with turquoise.',2800.00,18,1,5),
(7,'Turquoise Beaded Necklace','turquoise-beaded-necklace','Traditional Tibetan-style turquoise and coral beaded necklace.',1800.00,20,1,5),
(7,'Silver Om Bracelet','silver-om-bracelet','Sterling silver bracelet engraved with Om symbol.',1500.00,25,0,5),
(7,'Pote Necklace Set','pote-necklace-set','Traditional Nepali Pote necklace with green glass beads.',950.00,30,0,8),
(8,'Felt Elephant Toy','felt-elephant-toy','Handmade needle-felted elephant toy. Safe and eco-friendly.',450.00,60,0,10),
(8,'Felt Wool Slippers','felt-wool-slippers','Handmade 100% wool felt slippers with non-slip sole.',750.00,40,1,8),
(8,'Felt Decorative Balls Set','felt-decorative-balls-set','Set of 12 colorful handmade needle-felted wool balls.',550.00,50,0,10),
(8,'Felt Laptop Bag','felt-laptop-bag','Handmade thick wool felt laptop bag. Fits 15-inch laptops.',1650.00,18,1,5);

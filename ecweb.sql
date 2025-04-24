-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 24, 2025 at 12:10 PM
-- Server version: 8.3.0
-- PHP Version: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ecweb`
--

-- --------------------------------------------------------

--
-- Table structure for table `about_us`
--

DROP TABLE IF EXISTS `about_us`;
CREATE TABLE IF NOT EXISTS `about_us` (
  `id` int NOT NULL AUTO_INCREMENT,
  `section` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `role` varchar(100) DEFAULT 'N/A',
  `content` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `about_us`
--

INSERT INTO `about_us` (`id`, `section`, `name`, `role`, `content`, `image_path`, `created_at`, `status`) VALUES
(1, 'Meet Our Team', 'VICHEKA NY', 'Founder', 'hi', '../assets/images/about_us/67dc1b1c17b72_Vicheka.jpg', '2025-03-20 13:41:48', 1);

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `email`, `created_at`) VALUES
(1, 'admin', '$2y$12$x5eds76Jwgs6r2DGFFG7GuzRaxGgmUTW8iCScxq8CQ0gZ0gQYsCkK', 'admin@example.com', '2025-03-20 17:24:14'),
(14, 'vicheka', '$2y$12$q2NK.ILFFyOkm7x7ykTzee3mbAFo.Ba8WKRmYT3iAfx/IZQkJwGC6', 'vicheka@gmail.com', '2025-03-23 23:22:10'),
(15, 'sim', '$2y$12$o4fwRhP5Mo/7o33lWWPxfe1mLdkbpnMrVJsXY3bJP7.PkyGNVR4zi', 'sim@gmail.com', '2025-03-23 23:25:17'),
(16, 'kaka', '$2y$12$lAdIOiavcqmccxWd8ZoTceIPi7qTv4t/Fkjo5fhF7qJa3ix3EsNUe', 'kaka@gmail.com', '2025-04-23 10:22:49');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
CREATE TABLE IF NOT EXISTS `cart` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `size` varchar(50) NOT NULL,
  `color` varchar(50) NOT NULL,
  `added_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`, `updated_at`) VALUES
(2, 'Women', '2025-03-21 13:50:19', '2025-03-21 14:08:26'),
(46, 'Men', '2025-03-21 17:44:17', '2025-03-21 17:44:17'),
(66, 'Kids', '2025-03-22 00:16:56', '2025-03-30 21:21:20');

-- --------------------------------------------------------

--
-- Table structure for table `collections`
--

DROP TABLE IF EXISTS `collections`;
CREATE TABLE IF NOT EXISTS `collections` (
  `id` int NOT NULL AUTO_INCREMENT,
  `image` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `collections`
--

INSERT INTO `collections` (`id`, `image`, `title`, `created_at`) VALUES
(1, '67dd468709148_collections_nike1.jpg', 'collections_nike1', '2025-03-20 12:10:22'),
(2, '67dd4751236cf_collections_nike2.jpg', 'collections_nike2', '2025-03-20 12:10:22');

-- --------------------------------------------------------

--
-- Table structure for table `contact_details`
--

DROP TABLE IF EXISTS `contact_details`;
CREATE TABLE IF NOT EXISTS `contact_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(191) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `map_link` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `contact_details`
--

INSERT INTO `contact_details` (`id`, `email`, `phone`, `map_link`, `created_at`, `updated_at`) VALUES
(1, 'kasimstore@gmail.com', '+855 (717)944545', 'https://maps.app.goo.gl/T8cTuRfpjpTRbCjH7', '2025-03-20 12:26:11', '2025-03-23 07:42:05');

-- --------------------------------------------------------

--
-- Table structure for table `discounts`
--

DROP TABLE IF EXISTS `discounts`;
CREATE TABLE IF NOT EXISTS `discounts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `discount_percentage` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `code` varchar(50) DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `discounts`
--

INSERT INTO `discounts` (`id`, `discount_percentage`, `title`, `description`, `link_url`, `created_at`, `code`, `expires_at`) VALUES
(1, '50% OFF', '50% OFF Discount Coupons', 'Subscribe us to get 50% OFF on all the purchases', 'mailto:kasimstore@gmail.com ', '2025-03-20 12:16:25', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `footer_sections`
--

DROP TABLE IF EXISTS `footer_sections`;
CREATE TABLE IF NOT EXISTS `footer_sections` (
  `id` int NOT NULL AUTO_INCREMENT,
  `section_title` varchar(255) DEFAULT NULL,
  `items` text,
  `links` text,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `footer_sections`
--

INSERT INTO `footer_sections` (`id`, `section_title`, `items`, `links`, `updated_at`) VALUES
(35, 'Women Shoes', 'Track Your Order, Our Blog, Privacy policy', '', '2025-03-22 15:46:55'),
(34, 'About', 'History, Our Team, Services, Company', 'http://localhost:3000/pages/orders.php,http://localhost:3000/pages/about_us.php, http://localhost:3000/index.php, http://localhost:3000/index.php', '2025-03-22 15:46:03'),
(33, 'Info', 'Track Your Order, Our Blog, Privacy policy, Shipping, Contact Us', 'http://localhost:3000/pages/orders.php, http://localhost:3000/, http://localhost:3000/, http://localhost:3000/pages/orders.php, http://localhost:3000/pages/contact.php', '2025-03-22 15:41:09'),
(36, 'Popular', 'Prices Drop, New Products, Best Sales', '', '2025-03-22 15:47:46'),
(37, 'Mens Collection', 'Delivery, About Us, Shoes', '', '2025-03-22 15:48:31'),
(38, 'Get In Touch', 'KaSim Store Sorya Shopping Center, Phnom Penh Cambodia., Call us: (+855) 717 945 45, kasimstore@gmail.com.kh', '', '2025-03-22 15:49:10');

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

DROP TABLE IF EXISTS `login`;
CREATE TABLE IF NOT EXISTS `login` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `login_time` datetime NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `success` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_tokens`
--

DROP TABLE IF EXISTS `login_tokens`;
CREATE TABLE IF NOT EXISTS `login_tokens` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `user_type` varchar(20) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `logo`
--

DROP TABLE IF EXISTS `logo`;
CREATE TABLE IF NOT EXISTS `logo` (
  `id` int NOT NULL AUTO_INCREMENT,
  `logo_key` varchar(50) NOT NULL,
  `logo_value` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT 'Logo',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `logo`
--

INSERT INTO `logo` (`id`, `logo_key`, `logo_value`, `title`, `created_at`, `updated_at`) VALUES
(1, 'logo_image', '67dd4500d7bb8_aaaaaaa-removebg-preview (1).png', 'Logo', '2025-03-20 13:48:28', '2025-03-21 10:52:48');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `order_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','shipped','delivered') DEFAULT 'pending',
  `order_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `shipping_address` text NOT NULL,
  `username` varchar(50) NOT NULL,
  PRIMARY KEY (`order_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `total_amount`, `status`, `order_date`, `shipping_address`, `username`) VALUES
(4, 1, 121.60, 'delivered', '2025-03-21 19:59:14', 'cambodia', 'vicheka'),
(5, 1, 243.20, 'delivered', '2025-03-31 04:34:34', 'pp', 'vicheka');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `size` varchar(50) NOT NULL,
  `color` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `order_items_ibfk_1` (`order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`, `size`, `color`) VALUES
(3, 5, 31, 2, 121.60, 'W7.5/M6', 'Black'),
(2, 4, 31, 1, 121.60, 'W6.5/M5', 'Black');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT 'default.jpg',
  `price` decimal(10,2) NOT NULL,
  `discount` decimal(5,2) DEFAULT '0.00',
  `category` varchar(100) NOT NULL,
  `description` text,
  `sizes` varchar(255) DEFAULT NULL,
  `colors` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `stock` int NOT NULL DEFAULT '0',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `category_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `title`, `image`, `price`, `discount`, `category`, `description`, `sizes`, `colors`, `created_at`, `stock`, `updated_at`, `category_id`) VALUES
(14, 'Nike Air Force 1 \'07 LV8', '67dd4de22cc43_Nike Air Force 1 \'07 LV8.png', 120.00, 0.00, 'Men', 'Comfortable, durable and timeless—it’s number one for a reason. This version of the Air Force 1 pairs its classic ‘80s construction with patent leather and oversized rope laces for sleek style that tracks whether you’re on court or on the go.', 'M9/W10.5,M9.5/W11,M10/W11.5,M10.5/W12,M11/W12.5,M11.5/W13', 'PsychicBlue,UniversityBlue,White', '2025-03-21 11:11:12', 100, '2025-03-21 18:08:34', NULL),
(15, 'Nike C1TY', '67dd4dea129a3_Nike C1TY.png', 100.00, 0.00, 'Men', 'Nike C1TY is engineered to overcome anything the city throws your way. A mesh upper keeps the fit breathable, while the reinforced sides and toe box help protect your feet from the elements. Each colorway is inspired by the spirit of city life—giving street style a whole new meaning.', 'M5.5/W7,M6/W7.5,M6.5/W8,M7/W8.5,M7.5/W9,M8/W9.5', 'SummitWhite,FireRed,Black', '2025-03-21 11:16:15', 100, '2025-03-21 18:43:39', NULL),
(16, 'Nike Dunk Low Retro SE Leather/Suede', '67dd4dfd88af2_Nike Dunk Low Retro SE Leather Suede.png', 125.00, 24.00, 'Men', 'You can always count on a classic. This color-blocked design combines leather and suede with plush padding for game-changing comfort that lasts. The possibilities are endless—how will you wear your Dunks?', '6,6.5,7,7.5,8,8.5,9,9.5,10,10.5,11,11.5,12,12.5,13,14,15', 'PlatinumViolet,PaleIvory,CaveStone', '2025-03-21 11:19:31', 100, '2025-03-21 18:43:49', NULL),
(17, 'Air Jordan 1 Low OG \"Obsidian\"', '67dd4e0658e97_Air Jordan 1 Low OG Obsidian.png', 140.00, 0.00, 'Men', 'The Air Jordan 1 Low OG remakes the classic sneaker with new colors and textures. Premium materials and accents give fresh expression to an all-time favorite.', 'M5.5/W7,M6/W7.5,M6.5/W8,M7/W8.5,M7.5/W9,M8/W9.5', 'Obsidian,Sail,UniversityBlue', '2025-03-21 11:22:28', 100, '2025-03-21 18:43:58', NULL),
(18, 'Nike Field General', '67dd4dc5c7290_Nike Field General.png', 100.00, 0.00, 'Men', 'The Field General returns from its gritty football roots to shake up the sneaker scene. It pairs textured suede and a nubby Waffle sole with a metal Swoosh charm, adding a modern finish to that vintage gridiron look.', 'M10/W11.5,M10.5/W12,M11/W12.5,M11.5/W13,M12/W13.5,M12.5/W14', 'LightOrewoodBrown,Black', '2025-03-21 11:25:51', 100, '2025-03-21 18:44:04', NULL),
(19, 'Nike Air Max 1 Essential', '67dd55b6cddee_Nike Air Max 1 Essential.png', 140.00, 0.00, 'Men', 'Meet the leader of the pack. Walking on clouds above the noise, the Air Max 1 blends timeless design with cushioned comfort. Sporting a Max Air unit and mixed materials, this icon hit the scene in ‘87 and continues to be the soul of the franchise today.', '9,9.5,10,10.5,11', 'SoftPearl,LightKhaki,Black,SmokeyBlue', '2025-03-21 12:04:06', 100, '2025-03-21 18:44:41', NULL),
(20, 'Nike Air Max 90', '67dd569ec312e_Nike Air Max 90.png', 130.00, 10.00, 'Men', 'Lace up and feel the legacy. Produced at the intersection of art, music and culture, this champion running shoe helped define the ‘90s. Worn by presidents, revolutionized through collabs and celebrated through rare colorways, its striking visuals, Waffle outsole, and exposed Air cushioning keep it alive and well.', '10,10.5,11,11.5,12', 'CoolGrey,DustyCactus,Black,Wolf', '2025-03-21 12:07:58', 100, '2025-03-21 18:44:52', NULL),
(21, 'Nike Gato', '67dd573a66090_Nike Gato.png', 110.00, 0.00, 'Men', 'The 2010 indoor soccer shoe has been seeking its revival for some time now. Well, the wait is over—the beloved Gato is back! Primed and ready for the streets, this clean edition mixes premium leather, suede and breezy textile for a layered look that\'s easy to style.', 'M11/W12.5,M11.5/W13,M12/W13.5,M12.5/W14,M13/W14.5,M14/W15.5,M15/W16.5', 'Monarch,GumLightBrown,SoftPearl', '2025-03-21 12:10:34', 100, '2025-03-21 18:45:00', NULL),
(22, 'Nike Air Max 2013', '67dd57dd00361_Nike Air Max 2013.png', 180.00, 14.00, 'Men', 'The Air Max 2013 brings back an all-time favorite from the Air Max franchise. Just as stylish and sporty as ever, it combines airy mesh and no-sew overlays to help keep you looking and feeling fresh. Plus, Flywire lacing and Air cushioning provide lasting comfort and support.', 'M10/W11.5,M10.5/W12,M11/W12.5,M11.5/W13,M12/W13.5,M12.5/W14,M13/W14.5', 'Black,LaserOrange,UniversityRed', '2025-03-21 12:13:17', 100, '2025-03-21 18:44:30', NULL),
(23, 'Air Jordan 1 Mid SE', '67dd58944b415_Air Jordan 1 Mid SE.png', 135.00, 0.00, 'Men', 'Take your neutral game to the next level with this special edition AJ1. Genuine leather ensures you step out in luxury style, while a plush mid-top collar and classic Nike Air cushioning make for a premium look and feel.', 'M10/W11.5,M10.5/W12,M11/W12.5,M11.5/W13,M12/W13.5,M12.5/W14,M13/W14.5', 'White,Black,DarkPony', '2025-03-21 12:16:20', 100, '2025-03-21 18:44:22', NULL),
(24, 'Nike Dunk Low Next Nature', '67dd599c5adda_Nike Dunk Low Next Nature.png', 120.00, 34.00, 'Women', 'You can always count on a classic. The \'80s icon returns with premium materials and plush padding for comfort that lasts. The possibilities are endless—how will you wear your Dunks?', 'W5/M3.5,W5.5/M4,W6/M4.5,W6.5/M5,W7/M5.5,W7.5/M6,W8/M6.5,W8.5/M7', 'Phantom,PaleIvory,Metallic', '2025-03-21 12:20:44', 100, '2025-03-21 12:20:44', NULL),
(25, 'Nike Air Max Dn8', '67dd5a47a2c53_Nike Air Max Dn8.png', 190.00, 0.00, 'Women', 'More Air, less bulk. The Dn8 takes our Dynamic Air system and condenses it into a sleek, low-profile package. Powered by eight pressurized Air tubes, it gives you a responsive sensation with every step. Enter an unreal experience of movement.', '5,5.5,6,6.5,7,7.5,8,8.5,9,9.5,10,10.5,11,11.5,12', 'Sail,Fossil,Black', '2025-03-21 12:23:35', 100, '2025-03-21 12:23:35', NULL),
(26, 'Nike Zoom Vomero 5', '67dd5b172702d_Nike Zoom Vomero 5.png', 160.00, 0.00, 'Women', 'Carve a new lane for yourself in the Zoom Vomero 5—your go-to for complexity, depth and easy styling. The richly layered design includes textiles, synthetic leather and plastic accents that come together to make one of the coolest sneakers of the season.', 'W5/M3.5,W5.5/M4,W6/M4.5,W6.5/M5,W7/M5.5,W7.5/M6,W8/M6.5,W8.5/M7,W9/M7.5,W9.5/M8,W10/M8.5', 'BarelyGrape,PhotonDust,SummitWhite,MetallicSilver', '2025-03-21 12:27:03', 100, '2025-03-21 12:27:03', NULL),
(27, 'Nike LD-1000', '67dd5d366ccb7_Nike LD-1000.png', 100.00, 0.00, 'Women', 'First released in 1977, the LD-1000\'s dramatically flared heel was originally created to support long-distance runners. A fan favorite, now you can get your hands on one of Nike\'s most famous innovations too.', 'W5/M3.5,W5.5/M4,W6/M4.5,W6.5/M5,W7/M5.5,W7.5/M6,W8/M6.5,W8.5/M7', 'SummitWhite,Sail,GumMediumBrown,DarkTeamRed', '2025-03-21 12:36:06', 100, '2025-03-21 12:36:06', NULL),
(28, 'Nike AL8', '67dd5dc44e9bd_Nike AL8.png', 95.00, 0.00, 'Women', 'Inspired by the \'90s but ready for the future, the AL8 perfectly mixes nostalgia with modern comfort. The plush upper combines breathable mesh and premium leather with a chunky silhouette for a look that\'s easy to style.', 'W5/M3.5,W5.5/M4,W6/M4.5,W6.5/M5,W7/M5.5,W7.5/M6,W8/M6.5,W8.5/M7,W9/M7.5,W9.5/M8,W10/M8.5', 'SummitWhite,Light', '2025-03-21 12:38:28', 100, '2025-03-21 12:38:28', NULL),
(29, 'Nike Field General', '67dd5e93f0f24_Nike Field General.png', 100.00, 0.00, 'Women', 'The Field General returns from its gritty football roots to shake up the sneaker scene. It pairs hairy suede and durable woven textiles with a nubby Waffle sole—creating that vintage gridiron look.', 'W5/M3.5,W5.5/M4,W6/M4.5,W6.5/M5,W7/M5.5,W7.5/M6,W8/M6.5,W8.5/M7,W9/M7.5,W9.5/M8,W10/M8.5', 'Malachite,GumDarkBrown,SoftPearl', '2025-03-21 12:41:55', 100, '2025-03-21 12:41:55', NULL),
(30, 'Nike Pacific', '67dd5ef8daa61_Nike Pacific.png', 75.00, 0.00, 'Women', 'The Nike Pacific takes inspiration from old-school styles to give you a new kind of low-profile look. Mesh and suede are topped with a puffed Swoosh logo, and a herringbone sole with flex grooves adds a \'70s vibe with a twist.', 'W5/M3.5,W5.5/M4,W6/M4.5,W6.5/M5,W7/M5.5,W7.5/M6,W8/M6.5,W8.5/M7,W9/M7.5', 'GameRoyal,Black,SafetyOrange,White', '2025-03-21 12:43:36', 100, '2025-03-21 12:43:36', NULL),
(31, 'Nike Metcon 9 Premium', '67dd5f9d927d9_Nike Metcon 9 Premium.png', 160.00, 24.00, 'Women', 'Whatever your \"why\" is for working out, the Metcon 9 makes it all worth it. We improved on the 8 with a larger Hyperlift plate and added rubber rope wrap. Sworn by some of the greatest athletes in the world, intended for lifters, cross trainers, go-getters, it’s still the gold standard that delivers day after day. A glittered rubber rope wrap, silver Swoosh design and soft pink give this design a premium look.', 'W5/M3.5,W5.5/M4,W6/M4.5,W6.5/M5,W7/M5.5,W7.5/M6,W8/M6.5,W8.5/M7,W9/M7.5,W9.5/M8,W10/M8.5', 'PinkOxford,Black,LightSoftPink,MetallicBlack', '2025-03-21 12:46:21', 100, '2025-03-21 12:46:21', NULL),
(32, 'Nike Air Force 1 \'07 Next Nature', '67dd606ccfa2f_Nike Air Force 1 \'07 Next Nature.png', 115.00, 0.00, 'Women', 'This hoops original gives \"fresh air\" a whole new meaning. Breezy canvas, embroidered details and a bouquet of spring colors bring summertime vibes to what you already know and love: Nike Air cushioning, classic construction and style for days.', 'W5/M3.5,W5.5/M4,W6/M4.5,W6.5/M5,W7/M5.5,W7.5/M6,W8/M6.5,W8.5/M7,W9/M7.5,W9.5/M8,W10/M8.5', 'White,StadiumGreen', '2025-03-21 12:49:48', 100, '2025-03-21 12:49:48', NULL),
(33, 'Nike Air Force 1 Shadow', '67dd61181ed62_Nike Air Force 1 Shadow.png', 135.00, 0.00, 'Women', 'Everything you love about the AF1—but doubled! The Air Force 1 Shadow puts a playful twist on a hoops icon to highlight the best of AF1 DNA. With 2 eyestays, 2 mudguards, 2 backtabs and 2 Swoosh logos, you get a layered look with double the branding.', 'W6.5/M5,W7/M5.5,W7.5/M6,W8/M6.5,W8.5/M7,W9/M7.5,W9.5/M8,W10/M8.5', 'White,TeamGold,White,Hemp', '2025-03-21 12:52:40', 100, '2025-03-21 12:52:40', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `slides`
--

DROP TABLE IF EXISTS `slides`;
CREATE TABLE IF NOT EXISTS `slides` (
  `id` int NOT NULL AUTO_INCREMENT,
  `image` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `slides`
--

INSERT INTO `slides` (`id`, `image`, `title`, `created_at`) VALUES
(1, '67dd40b5e1ee2_Slide_nike1.png', 'Slide_nike1', '2025-03-20 12:08:48'),
(2, '67dd425855f53_slide_nike2.jpg', 'slide_nike2', '2025-03-20 12:08:48'),
(3, '67dd41ec7ec44_slide_nike3.jpg', 'slide_nike3', '2025-03-20 12:08:48'),
(4, '67dd45cb6e93c_slide_nike4.jpg', 'slide_nike4', '2025-03-21 10:56:11');

-- --------------------------------------------------------

--
-- Table structure for table `social_links`
--

DROP TABLE IF EXISTS `social_links`;
CREATE TABLE IF NOT EXISTS `social_links` (
  `id` int NOT NULL AUTO_INCREMENT,
  `link` varchar(255) NOT NULL,
  `icon_name` varchar(50) NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `platform` varchar(50) NOT NULL DEFAULT 'Unknown',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `social_links`
--

INSERT INTO `social_links` (`id`, `link`, `icon_name`, `status`, `platform`) VALUES
(1, 'https://www.facebook.com/kasimstore', 'facebook', 1, 'Facebook'),
(2, 'https://x.com/kasimstore', 'twitter', 1, 'X'),
(3, 'https://www.instagram.com/kasimstore', 'instagram', 1, 'Instagram'),
(4, 'https://www.linkedin.com/in/kasimstore', 'linkedin', 1, 'LinkedIn'),
(5, 'https://t.me/kasimstore', 'telegram', 1, 'Telegram');

-- --------------------------------------------------------

--
-- Table structure for table `social_media_links`
--

DROP TABLE IF EXISTS `social_media_links`;
CREATE TABLE IF NOT EXISTS `social_media_links` (
  `id` int NOT NULL AUTO_INCREMENT,
  `platform` varchar(50) NOT NULL,
  `url` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `platform` (`platform`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `social_media_links`
--

INSERT INTO `social_media_links` (`id`, `platform`, `url`, `created_at`, `updated_at`) VALUES
(1, 'Instagram', 'https://www.instagram.com/kasimstore', '2025-03-20 12:25:58', '2025-03-23 07:42:50'),
(2, 'X', 'https://x.com/kasimstore', '2025-03-20 12:25:58', '2025-03-21 07:24:28'),
(3, 'Telegram', 'https://t.me/kasimstore', '2025-03-20 12:25:58', '2025-03-21 07:24:28'),
(4, 'Facebook', 'https://facebook.com/kasimstore', '2025-03-20 12:25:58', '2025-03-21 07:24:28'),
(5, 'WhatsApp', 'https://wa.me/1234567890', '2025-03-20 12:25:58', '2025-03-21 07:24:28'),
(6, 'TikTok', 'https://tiktok.com/@kasimstore', '2025-03-20 12:25:58', '2025-03-21 07:24:28');

-- --------------------------------------------------------

--
-- Table structure for table `special_offer`
--

DROP TABLE IF EXISTS `special_offer`;
CREATE TABLE IF NOT EXISTS `special_offer` (
  `id` int NOT NULL AUTO_INCREMENT,
  `special_key` varchar(50) NOT NULL,
  `special_value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `special_offer`
--

INSERT INTO `special_offer` (`id`, `special_key`, `special_value`) VALUES
(1, 'special_offer_title', 'Special Offer'),
(2, 'special_offer', 'Free Shipping on all the orders above $50');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(191) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `birth_date` date NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `phone_number` (`phone_number`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `username`, `email`, `phone_number`, `birth_date`, `gender`, `password_hash`, `created_at`) VALUES
(1, 'VICHEKA', 'vicheka', 'vicheka@gmail.com', '0717944545', '2002-04-11', '', '$2y$12$bfFru225NnsPYhdw4ffWM.JJGtbyPSBor.PGvjCD4HldA.vEL/yh6', '2025-03-20 05:19:29');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

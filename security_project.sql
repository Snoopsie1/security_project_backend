-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 17, 2023 at 11:18 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `security_project`
--

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `email` varchar(30) NOT NULL,
  `password` varchar(60) NOT NULL,
  `role_id` int(11) NOT NULL,
  `purchases` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`id`, `name`, `email`, `password`, `role_id`, `purchases`) VALUES
(2, 'mikkel', 'mikkel@test.dk', '$2y$10$Pobakkwm3v/rCVXMx55FlOGJqBUqnkBC7vYDDCpf6bGdbbcfFzyNO', 1, NULL),
(3, 'oliver', 'oliver@test.dk', '$2y$10$o/v2VAcbFOr7S1sYhfd7SO3ZPXCQsBDyKLmnFMnA0rnEQLsnkLiNe', 1, NULL),
(4, 'emil', 'emil@test.dk', '$2y$10$q1M2kHTenJRjcimkzdij2u0fqD0zG.s8KArvOeaPFYt1h7qSJyjrG', 2, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customer_purchase`
--

CREATE TABLE `customer_purchase` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `purchase_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_purchase`
--

INSERT INTO `customer_purchase` (`id`, `customer_id`, `purchase_id`) VALUES
(12, 2, 45);

-- --------------------------------------------------------

--
-- Table structure for table `logging`
--

CREATE TABLE `logging` (
  `customer_id` int(11) NOT NULL,
  `message` varchar(60) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logging`
--

INSERT INTO `logging` (`customer_id`, `message`, `timestamp`) VALUES
(2, 'User logged in', '2023-09-13 07:45:31'),
(2, 'User created a new order', '2023-09-13 07:45:56'),
(3, 'User Failed to create order', '2023-09-13 07:46:47'),
(2, 'User deleted purchase', '2023-09-13 08:06:07'),
(2, 'User deleted purchase', '2023-09-13 08:16:49');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `name`, `price`) VALUES
(10, 'tape', 40),
(11, 'webcam', 100),
(12, 'weight gainer', 400),
(13, 'roids', 1000),
(14, 'cykelhjelm', 40),
(15, 'neon lys', 80);

-- --------------------------------------------------------

--
-- Table structure for table `purchase`
--

CREATE TABLE `purchase` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase`
--

INSERT INTO `purchase` (`id`) VALUES
(45);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_product`
--

CREATE TABLE `purchase_product` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `purchase_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_product`
--

INSERT INTO `purchase_product` (`id`, `product_id`, `purchase_id`) VALUES
(71, 10, 45),
(72, 11, 45);

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id` int(11) NOT NULL,
  `role` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`id`, `role`) VALUES
(1, 'admin'),
(2, 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `purchases` (`purchases`),
  ADD KEY `purchases_2` (`purchases`),
  ADD KEY `role_id_2` (`role_id`);

--
-- Indexes for table `customer_purchase`
--
ALTER TABLE `customer_purchase`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `purchase_id` (`purchase_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `purchase`
--
ALTER TABLE `purchase`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `purchase_product`
--
ALTER TABLE `purchase_product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `product_id_2` (`product_id`),
  ADD KEY `purchase_id` (`purchase_id`),
  ADD KEY `product_id_3` (`product_id`),
  ADD KEY `purchase_id_2` (`purchase_id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `customer_purchase`
--
ALTER TABLE `customer_purchase`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `purchase`
--
ALTER TABLE `purchase`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `purchase_product`
--
ALTER TABLE `purchase_product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `customer`
--
ALTER TABLE `customer`
  ADD CONSTRAINT `purchases` FOREIGN KEY (`purchases`) REFERENCES `customer_purchase` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `role_id` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`);

--
-- Constraints for table `customer_purchase`
--
ALTER TABLE `customer_purchase`
  ADD CONSTRAINT `customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `purchase_id` FOREIGN KEY (`purchase_id`) REFERENCES `purchase` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints for table `purchase_product`
--
ALTER TABLE `purchase_product`
  ADD CONSTRAINT `product_id` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `purchase_product_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `purchase` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

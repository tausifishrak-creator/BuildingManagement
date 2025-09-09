-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 09, 2025 at 07:13 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `buildingmanagement`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_email` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `message`, `created_at`, `user_email`) VALUES
(7, 'Water outage', 'Water will be out from 8AM to 5PM', '2025-09-09 05:10:07', 'tausif@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `avg_utility_rates`
--

CREATE TABLE `avg_utility_rates` (
  `utility_type` enum('Electricity','Gas','Water') NOT NULL,
  `rate_per_person` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `complaint_text` text NOT NULL,
  `status` enum('pending','resolved') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `phone_number` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `complaints`
--

INSERT INTO `complaints` (`id`, `user_name`, `complaint_text`, `status`, `created_at`, `phone_number`) VALUES
(1, 'Siraj Hasan', 'My complaint is the water issues', 'resolved', '2025-08-30 14:45:45', '01447519231'),
(2, 'Siraj Hasan', 'Gas leak has been found, send someone to fix it.', 'resolved', '2025-08-30 14:52:17', '01447519231'),
(3, 'Siraj Hasan', 'electrical problem', 'pending', '2025-09-09 04:43:43', '01447519231');

-- --------------------------------------------------------

--
-- Table structure for table `family_details`
--

CREATE TABLE `family_details` (
  `phone_number` varchar(255) NOT NULL,
  `num_of_member` int(11) DEFAULT NULL,
  `adults` int(11) DEFAULT NULL,
  `children` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `family_details`
--

INSERT INTO `family_details` (`phone_number`, `num_of_member`, `adults`, `children`) VALUES
('01447519231', NULL, 2, 3);

-- --------------------------------------------------------

--
-- Table structure for table `flat_details`
--

CREATE TABLE `flat_details` (
  `flat_no` varchar(5) NOT NULL,
  `area` varchar(50) NOT NULL,
  `rent` int(11) NOT NULL,
  `number_of_rooms` int(11) NOT NULL,
  `status` enum('Vacant','Occupied') NOT NULL,
  `manager_nid` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `flat_details`
--

INSERT INTO `flat_details` (`flat_no`, `area`, `rent`, `number_of_rooms`, `status`, `manager_nid`) VALUES
('A-0', '1024X1024', 20000, 4, 'Vacant', NULL),
('A-1', '2048X2048', 30000, 5, 'Vacant', NULL),
('A-2', '4096X4096', 60000, 10, 'Occupied', NULL),
('A-3', '2048X2048', 30000, 5, 'Vacant', NULL),
('A-4', '2048X2048', 30000, 5, 'Occupied', NULL),
('A-5', '2048X2048', 30000, 5, 'Vacant', NULL),
('B-1', '2048X2048', 30000, 5, 'Vacant', NULL),
('B-3', '2048X2048', 30000, 5, 'Vacant', NULL),
('B-4', '2048X2048', 30000, 5, 'Vacant', NULL),
('B-5', '2048X2048', 30000, 5, 'Vacant', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `garage`
--

CREATE TABLE `garage` (
  `spot_label` varchar(5) NOT NULL,
  `status` enum('Vacant','Occupied','For Rent','Rented') DEFAULT NULL,
  `rent` int(11) NOT NULL,
  `flat_no` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `garage`
--

INSERT INTO `garage` (`spot_label`, `status`, `rent`, `flat_no`) VALUES
('G-0', 'Vacant', 2000, 'A-0'),
('G-1', 'Vacant', 3500, 'A-1'),
('G-10', 'Vacant', 3500, 'B-5'),
('G-2', 'Rented', 7000, 'A-2'),
('G-3', 'Vacant', 3500, 'A-3'),
('G-4', 'Occupied', 3500, 'A-4'),
('G-5', 'Vacant', 3500, 'A-5'),
('G-6', 'Vacant', 3500, 'B-1'),
('G-8', 'Vacant', 3500, 'B-3'),
('G-9', 'Vacant', 3500, 'B-4');

-- --------------------------------------------------------

--
-- Table structure for table `manager`
--

CREATE TABLE `manager` (
  `nid` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `home_address` varchar(255) NOT NULL,
  `hire_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `manager`
--

INSERT INTO `manager` (`nid`, `name`, `phone_number`, `email`, `home_address`, `hire_date`) VALUES
('245872362', 'Nahid Rafi', '01964537985', 'rafi@gmail.com', 'House-5/5, Road-6, Nurjahan Road, Mohammadpur, Dhaka', '2025-08-18'),
('2537654199', 'KM Riaz', '01821456789', 'riaz@gmail.com', 'House-3, Road-12, Notun Bazar, Badda, Dhaka', '2025-08-18');

-- --------------------------------------------------------

--
-- Table structure for table `non_tenants`
--

CREATE TABLE `non_tenants` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `garage_spot_label` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `non_tenants`
--

INSERT INTO `non_tenants` (`id`, `name`, `phone_number`, `address`, `email`, `garage_spot_label`) VALUES
(2147483647, 'Nafiul Alom', '01999687844', 'BracU, Badda', 'nafiul@gmail.com', 'G-2');

-- --------------------------------------------------------

--
-- Table structure for table `student_details`
--

CREATE TABLE `student_details` (
  `student_id` varchar(90) DEFAULT NULL,
  `phone_number` varchar(255) NOT NULL,
  `institute` varchar(255) DEFAULT NULL,
  `emergency_contact` varchar(20) DEFAULT NULL,
  `emg_cont_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_details`
--

INSERT INTO `student_details` (`student_id`, `phone_number`, `institute`, `emergency_contact`, `emg_cont_name`) VALUES
('2231434566', '01548754124', 'Brac University', '01387482918', 'Syed Ahmed');

-- --------------------------------------------------------

--
-- Table structure for table `tenants`
--

CREATE TABLE `tenants` (
  `flat_no` varchar(10) NOT NULL,
  `tenant_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone_number` varchar(255) NOT NULL,
  `tenant_type` enum('Family','Students') NOT NULL,
  `movein_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tenants`
--

INSERT INTO `tenants` (`flat_no`, `tenant_name`, `email`, `phone_number`, `tenant_type`, `movein_date`) VALUES
('A-4', 'Siraj Hasan', 'sirajmia@gmail.com', '01447519231', 'Family', '2025-08-29'),
('A-2', 'Rayan Syed', 'rayan@gmail.com', '01548754124', 'Students', '2025-08-30'),
('A-2', 'Tahin Nafi', 'nafi@gmail.com', '01548765265', 'Students', '2025-08-30'),
('A-2', 'Mashfiq', 'mashfiq@gmail.com', '01821456789', 'Students', '2025-08-29');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `name` text NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` text NOT NULL,
  `phone_number` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`name`, `email`, `password`, `role`, `phone_number`) VALUES
('Mashfiq', 'mashfiq@gmail.com', '$2y$10$FNGfaa7.77N1eF96aXS0HOux7rh3dinsfG8vE5C3Tenbp3x2Sjee2', 'tenant', '01821456789'),
('Rayan Syed', 'rayan@gmail.com', '$2y$10$SLBO6CmIvozY.PwP3NnF8ukREWGHClhuXrHffiaW4LXaVdc4fQeoS', 'tenant', '01548754124'),
('Siraj Hasan', 'sirajmia@gmail.com', '$2y$10$2j7LuEQettZ7uvz8WZl3f.LCyOaIT321AkExUypROj0Ndyuw8CgWy', 'tenant', '01447519231'),
('Tausif Ishrak', 'tausif@gmail.com', '$2y$10$2p7H6IcxIXd1mUk4kda5YeOYGH/bflWNADXVOxcvcaY66NlR5rSUy', 'admin', '01556350190'),
('Zihad Hasan', 'zihad@gmail.com', '$2y$10$2dT/sgOIFYkyeAmoUk4CseUW/uJG9rHsDdfU1OSO0W3UaSVM67UaO', 'admin', '01679845632');

-- --------------------------------------------------------

--
-- Table structure for table `workers`
--

CREATE TABLE `workers` (
  `serial_no` int(11) NOT NULL,
  `name` text NOT NULL,
  `post` varchar(5) DEFAULT NULL,
  `phone_number` varchar(15) NOT NULL,
  `wages` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_announcements_user_email` (`user_email`);

--
-- Indexes for table `avg_utility_rates`
--
ALTER TABLE `avg_utility_rates`
  ADD PRIMARY KEY (`utility_type`);

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_complaints_tenants` (`phone_number`);

--
-- Indexes for table `family_details`
--
ALTER TABLE `family_details`
  ADD PRIMARY KEY (`phone_number`);

--
-- Indexes for table `flat_details`
--
ALTER TABLE `flat_details`
  ADD PRIMARY KEY (`flat_no`),
  ADD UNIQUE KEY `flat_no` (`flat_no`),
  ADD KEY `fk_manager_nid` (`manager_nid`);

--
-- Indexes for table `garage`
--
ALTER TABLE `garage`
  ADD PRIMARY KEY (`spot_label`),
  ADD UNIQUE KEY `spot_label` (`spot_label`),
  ADD UNIQUE KEY `flat_no` (`flat_no`);

--
-- Indexes for table `manager`
--
ALTER TABLE `manager`
  ADD PRIMARY KEY (`nid`),
  ADD UNIQUE KEY `nid` (`nid`);

--
-- Indexes for table `non_tenants`
--
ALTER TABLE `non_tenants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_non_tenant_garage` (`garage_spot_label`);

--
-- Indexes for table `student_details`
--
ALTER TABLE `student_details`
  ADD UNIQUE KEY `phone_number` (`phone_number`);

--
-- Indexes for table `tenants`
--
ALTER TABLE `tenants`
  ADD PRIMARY KEY (`phone_number`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `flat_no` (`flat_no`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `workers`
--
ALTER TABLE `workers`
  ADD KEY `fk_workers_flat_no` (`post`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `non_tenants`
--
ALTER TABLE `non_tenants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2147483648;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `fk_announcements_user_email` FOREIGN KEY (`user_email`) REFERENCES `users` (`email`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `fk_complaints_tenants` FOREIGN KEY (`phone_number`) REFERENCES `tenants` (`phone_number`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `family_details`
--
ALTER TABLE `family_details`
  ADD CONSTRAINT `fk_family_tenants` FOREIGN KEY (`phone_number`) REFERENCES `tenants` (`phone_number`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `flat_details`
--
ALTER TABLE `flat_details`
  ADD CONSTRAINT `fk_manager_nid` FOREIGN KEY (`manager_nid`) REFERENCES `manager` (`nid`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `garage`
--
ALTER TABLE `garage`
  ADD CONSTRAINT `fk_garage_flat` FOREIGN KEY (`flat_no`) REFERENCES `flat_details` (`flat_no`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `non_tenants`
--
ALTER TABLE `non_tenants`
  ADD CONSTRAINT `fk_non_tenant_garage` FOREIGN KEY (`garage_spot_label`) REFERENCES `garage` (`spot_label`) ON UPDATE CASCADE;

--
-- Constraints for table `student_details`
--
ALTER TABLE `student_details`
  ADD CONSTRAINT `fk_student_tenants` FOREIGN KEY (`phone_number`) REFERENCES `tenants` (`phone_number`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tenants`
--
ALTER TABLE `tenants`
  ADD CONSTRAINT `fk_tenants_flat_no` FOREIGN KEY (`flat_no`) REFERENCES `flat_details` (`flat_no`);

--
-- Constraints for table `workers`
--
ALTER TABLE `workers`
  ADD CONSTRAINT `fk_workers_flat_no` FOREIGN KEY (`post`) REFERENCES `flat_details` (`flat_no`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

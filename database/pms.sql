-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 02, 2025 at 04:25 AM
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
-- Database: `pms`
--

-- --------------------------------------------------------

--
-- Table structure for table `behavior_logs`
--

CREATE TABLE `behavior_logs` (
  `log_id` int(11) NOT NULL,
  `inmate_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `log_date` date DEFAULT curdate(),
  `notes` text DEFAULT NULL,
  `behavior_rating` enum('Excellent','Good','Fair','Poor') DEFAULT 'Fair'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `behavior_logs`
--

INSERT INTO `behavior_logs` (`log_id`, `inmate_id`, `staff_id`, `log_date`, `notes`, `behavior_rating`) VALUES
(1, 2, 4, '2025-11-02', 'Very Good', 'Good');

-- --------------------------------------------------------

--
-- Table structure for table `incidents`
--

CREATE TABLE `incidents` (
  `incident_id` int(11) NOT NULL,
  `inmate_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `incident_type` enum('Violence','Contraband','Escape Attempt','Health Emergency','Other') DEFAULT 'Other',
  `severity_level` enum('Low','Medium','High','Critical') DEFAULT 'Low',
  `location` varchar(100) DEFAULT NULL,
  `reported_by` varchar(100) DEFAULT NULL,
  `witnesses` text DEFAULT NULL,
  `description` text NOT NULL,
  `action_taken` text DEFAULT NULL,
  `status` enum('Under Investigation','Resolved','Reported','Dismissed') DEFAULT 'Under Investigation',
  `remarks` text DEFAULT NULL,
  `incident_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `incidents`
--

INSERT INTO `incidents` (`incident_id`, `inmate_id`, `staff_id`, `incident_type`, `severity_level`, `location`, `reported_by`, `witnesses`, `description`, `action_taken`, `status`, `remarks`, `incident_date`) VALUES
(1, 2, 3, 'Other', 'Low', NULL, NULL, NULL, 'adsads', 'dasas', 'Under Investigation', NULL, '2025-11-02 10:48:00'),
(2, 3, 3, 'Other', 'Low', NULL, NULL, NULL, 'asdadsad', 'adsasd', 'Under Investigation', NULL, '2025-11-02 02:13:00'),
(3, 2, 3, 'Other', 'Low', NULL, NULL, NULL, 'dsada', 'ads', 'Under Investigation', NULL, '2025-11-02 11:13:00'),
(4, 2, 3, 'Other', 'Low', NULL, NULL, NULL, 'dsada', 'ads', 'Under Investigation', NULL, '2025-11-02 11:13:00'),
(5, 2, 3, 'Other', 'Low', NULL, NULL, NULL, 'dsad', 'adasd', 'Under Investigation', NULL, '2025-11-02 11:15:00');

-- --------------------------------------------------------

--
-- Table structure for table `inmates`
--

CREATE TABLE `inmates` (
  `inmate_id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `crime` text DEFAULT NULL,
  `sentence_years` int(11) DEFAULT NULL,
  `court_details` text DEFAULT NULL,
  `cell_block` varchar(50) DEFAULT NULL,
  `admission_date` date DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `status` enum('Active','Released','Transferred') DEFAULT 'Active',
  `photo_path` varchar(255) DEFAULT NULL,
  `height_cm` decimal(5,2) DEFAULT NULL,
  `weight_kg` decimal(5,2) DEFAULT NULL,
  `blood_type` enum('A','B','AB','O') DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `eye_color` varchar(50) DEFAULT NULL,
  `hair_color` varchar(50) DEFAULT NULL,
  `identifying_marks` text DEFAULT NULL,
  `emergency_contact_name` varchar(100) DEFAULT NULL,
  `emergency_contact_number` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inmates`
--

INSERT INTO `inmates` (`inmate_id`, `first_name`, `last_name`, `birthdate`, `gender`, `crime`, `sentence_years`, `court_details`, `cell_block`, `admission_date`, `release_date`, `status`, `photo_path`, `height_cm`, `weight_kg`, `blood_type`, `nationality`, `eye_color`, `hair_color`, `identifying_marks`, `emergency_contact_name`, `emergency_contact_number`) VALUES
(1, 'John', 'Doe', '1988-04-12', 'Male', 'Robbery', 8, 'Manila Regional Court', 'A1', '2019-06-15', '2027-06-15', 'Active', '../../assets/uploads/images (7).jpg', 178.00, 80.00, '', 'Filipino', 'Brown', 'Black', 'Scar on right cheek', 'Maria Doe', '09171234567'),
(2, 'Michael', 'Santos', '1992-11-03', 'Male', 'Drug Trafficking', 12, 'Quezon City Trial Court', 'C3', '2018-02-10', '2030-02-10', 'Active', '../../assets/uploads/images (6).jpg', 172.00, 76.00, '', 'Filipino', 'Brown', 'Black', 'Tattoo: dragon on left arm', 'Anna Santos', '09183456721'),
(3, 'Carlos', 'Reyes', '1985-09-21', 'Male', 'Homicide', 20, 'Cebu City Court', 'A2', '2015-08-02', '2035-08-02', 'Active', '../../assets/uploads/images (5).jpg', 180.00, 85.00, '', 'Filipino', 'Dark Brown', 'Black', 'Burn mark on left forearm', 'Liza Reyes', '09184561234'),
(4, 'Jose', 'Marquez', '1990-02-18', 'Male', 'Fraud', 6, 'Davao Regional Court', 'D1', '2021-09-09', '2027-09-09', 'Active', '../../assets/uploads/il_fullxfull.4064010196_szsr.webp', 170.00, 70.00, '', 'Filipino', 'Brown', 'Black', 'Tattoo: “Truth” on wrist', 'Elena Marquez', '09196784321'),
(5, 'Peter', 'Lopez', '1995-12-22', 'Male', 'Assault', 5, 'Taguig Trial Court', 'E4', '2020-04-05', '2025-04-05', 'Active', '../../assets/uploads/images (4).jpg', 174.00, 78.00, '', 'Filipino', 'Hazel', 'Black', 'Scar on left eyebrow', 'Juan Lopez', '09172349876'),
(6, 'Raymond', 'Chua', '1983-06-07', 'Male', 'Tax Evasion', 4, 'Makati City Court', 'F2', '2022-01-12', '2026-01-12', 'Active', '../../assets/uploads/images (3).jpg', 169.00, 68.00, '', 'Filipino-Chinese', 'Brown', 'Black', 'Mole on chin', 'Cynthia Chua', '09175671234'),
(7, 'David', 'Lim', '1998-03-14', 'Male', 'Car Theft', 7, 'Pasig Court', 'C1', '2021-07-10', '2028-07-10', 'Active', '../../assets/uploads/images (2).jpg', 177.00, 82.00, '', 'Filipino', 'Brown', 'Black', 'Tattoo: skull on neck', 'Rebecca Lim', '09181239876'),
(8, 'Allan', 'Garcia', '1989-05-19', 'Male', 'Kidnapping', 15, 'Cavite Regional Court', 'A1', '2016-11-20', '2031-11-20', 'Active', '../../assets/uploads/8500551855efb1dca1c3dca9c6df26d1.webp', 182.00, 88.00, '', 'Filipino', 'Dark Brown', 'Black', 'Scar on right hand', 'Rosa Garcia', '09188887766'),
(9, 'Erwin', 'Torres', '1979-10-01', 'Male', 'Murder', 25, 'Baguio City Court', 'A3', '2012-03-12', '2037-03-12', 'Active', '../../assets/uploads/images (1).jpg', 176.00, 79.00, '', 'Filipino', 'Brown', 'Black', 'Tattoo: dagger on forearm', 'Mina Torres', '09174562345'),
(10, 'Francis', 'Villanueva', '1993-07-23', 'Male', 'Cybercrime', 8, 'Manila Cyber Court', 'D2', '2020-05-18', '2028-05-18', 'Active', '../../assets/uploads/images.jpg', 171.00, 73.00, '', 'Filipino', 'Brown', 'Black', 'Wrist tattoo: binary code', 'Carla Villanueva', '09183455678'),
(11, 'Samantha', 'Ramos', '1997-11-12', 'Female', 'Fraud', 5, 'Pasay City Court', 'E3', '2021-03-09', '2026-03-09', 'Active', '../../assets/uploads/download (2).jpg', 162.00, 58.00, '', 'Filipino', 'Brown', 'Black', 'Birthmark on neck', 'Miguel Ramos', '09187651234'),
(12, 'Angela', 'Castro', '1984-09-30', 'Female', 'Drug Possession', 10, 'Mandaluyong Court', 'C4', '2019-01-14', '2029-01-14', 'Active', '../../assets/uploads/download (1).jpg', 165.00, 60.00, '', 'Filipino', 'Brown', 'Black', 'Tattoo: rose on shoulder', 'Rico Castro', '09192233445'),
(13, 'Melissa', 'Tan', '1990-01-05', 'Female', 'Forgery', 6, 'Makati City Court', 'D3', '2020-10-20', '2026-10-20', 'Active', '../../assets/uploads/download.jpg', 160.00, 55.00, '', 'Filipino-Chinese', 'Brown', 'Black', 'Scar on left hand', 'Allan Tan', '09193344567'),
(14, 'Patricia', 'Navarro', '1995-08-15', 'Female', 'Arson', 12, 'Iloilo Regional Court', 'B2', '2018-12-03', '2030-12-03', 'Active', '../../assets/uploads/1000_F_774545132_t5PCk1SoSbTEgUe2dfcW19lOYR2DD52m.jpg', 168.00, 63.00, '', 'Filipino', 'Brown', 'Black', 'Tattoo: phoenix on back', 'Carmen Navarro', '09194567890'),
(15, 'Rosa', 'Jimenez', '1988-04-10', 'Female', 'Embezzlement', 9, 'Cebu City Court', 'D4', '2017-07-25', '2026-07-25', 'Active', '../../assets/uploads/prison-mugshot-of-black-woman-in-orange-jumpsuit-and-white-shirt-photo.jpg', 166.00, 59.00, '', 'Filipino', 'Brown', 'Black', 'Mole near lip', 'Lorenzo Jimenez', '09195553456'),
(16, 'Liza', 'Fernandez', '1991-05-24', 'Female', 'Bribery', 4, 'Taguig City Court', 'E1', '2022-02-18', '2026-02-18', 'Active', '../../assets/uploads/ai-generated-prison-mugshot-of-middle-aged-african-american-woman-in-orange-jumpsuit-photo.jpg', 163.00, 57.00, '', 'Filipino', 'Brown', 'Black', 'Tattoo: heart on ankle', 'Arnel Fernandez', '09191234567'),
(17, 'Catherine', 'Cruz', '1983-02-08', 'Female', 'Smuggling', 14, 'Manila Regional Court', 'B3', '2015-10-11', '2029-10-11', 'Active', '../../assets/uploads/images.jpg', 167.00, 61.00, '', 'Filipino', 'Brown', 'Black', 'Scar on right forearm', 'Marco Cruz', '09192223344'),
(18, 'Veronica', 'Delos Reyes', '1987-09-11', 'Female', 'Extortion', 7, 'Cavite Trial Court', 'C2', '2020-08-05', '2027-08-05', 'Active', '../../assets/uploads/1000_F_728201850_8LmFfeLxLvAJDdpwEPMy6d4bvyDrmVdt.jpg', 164.00, 60.00, '', 'Filipino', 'Brown', 'Black', 'Tattoo: initials VDR', 'Paulo Delos Reyes', '09193331234'),
(19, 'Janet', 'Santiago', '1999-06-19', 'Female', 'Theft', 3, 'Pasig City Court', 'E2', '2023-04-21', '2026-04-21', 'Active', '../../assets/uploads/1000_F_774541913_ehl9D1CI4XT5OtfWDPU4g9roSRi2Q6uf.jpg', 161.00, 54.00, '', 'Filipino', 'Brown', 'Black', 'Tattoo: butterfly on wrist', 'Jose Santiago', '09197778899'),
(20, 'Maria', 'Valdez', '1978-03-27', 'Female', 'Murder', 20, 'Bacolod City Court', 'A4', '2010-05-16', '2030-05-16', 'Active', '../../assets/uploads/360_F_734957369_H2vRqAbeza7BkCRwj7IHd5oyCqPfAdIJ.jpg', 170.00, 65.00, '', 'Filipino', 'Dark Brown', 'Black', 'Scar on cheek', 'Daniel Valdez', '09191112222');

-- --------------------------------------------------------

--
-- Table structure for table `inmate_programs`
--

CREATE TABLE `inmate_programs` (
  `inmate_program_id` int(11) NOT NULL,
  `inmate_id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `progress` enum('Ongoing','Completed','Dropped') DEFAULT 'Ongoing'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inmate_programs`
--

INSERT INTO `inmate_programs` (`inmate_program_id`, `inmate_id`, `program_id`, `staff_id`, `start_date`, `end_date`, `progress`) VALUES
(1, 1, 1, 4, '2025-11-02', NULL, 'Ongoing'),
(2, 2, 1, 4, '2025-11-02', NULL, 'Ongoing'),
(3, 3, 1, 4, '2025-11-02', NULL, 'Ongoing'),
(4, 6, 1, 1, '2025-11-03', NULL, 'Ongoing'),
(5, 7, 1, 1, '2025-11-03', '2025-11-02', 'Completed'),
(6, 8, 1, 1, '2025-11-03', '2025-11-02', 'Completed');

-- --------------------------------------------------------

--
-- Table structure for table `medical_records`
--

CREATE TABLE `medical_records` (
  `record_id` int(11) NOT NULL,
  `inmate_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `visit_type` enum('Routine Checkup','Emergency','Follow-up','Mental Health','Other') DEFAULT 'Routine Checkup',
  `diagnosis` text DEFAULT NULL,
  `vital_signs` text DEFAULT NULL,
  `blood_pressure` varchar(10) DEFAULT NULL,
  `temperature_c` decimal(4,1) DEFAULT NULL,
  `pulse_rate` int(11) DEFAULT NULL,
  `respiratory_rate` int(11) DEFAULT NULL,
  `treatment` text DEFAULT NULL,
  `medication` text DEFAULT NULL,
  `medical_condition` varchar(255) DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `next_checkup_date` date DEFAULT NULL,
  `hospital_referred` varchar(255) DEFAULT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `record_date` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medical_records`
--

INSERT INTO `medical_records` (`record_id`, `inmate_id`, `staff_id`, `visit_type`, `diagnosis`, `vital_signs`, `blood_pressure`, `temperature_c`, `pulse_rate`, `respiratory_rate`, `treatment`, `medication`, `medical_condition`, `allergies`, `remarks`, `next_checkup_date`, `hospital_referred`, `attachment_path`, `record_date`) VALUES
(1, 8, 2, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-01'),
(2, 12, 2, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-01'),
(3, 3, 2, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-01'),
(4, 17, 2, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-01'),
(5, 7, 5, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-01');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(50) DEFAULT 'general',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `title`, `message`, `type`, `is_read`, `created_at`) VALUES
(1, 6, 'Visitation Request Sent', 'Your visitation request has been submitted and is pending approval.', 'visitation', 0, '2025-11-02 01:22:17'),
(2, 6, 'Visitation Request Sent', 'Your visitation request has been submitted and is pending approval.', 'visitation', 0, '2025-11-02 01:32:03');

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `program_id` int(11) NOT NULL,
  `program_name` varchar(100) NOT NULL,
  `program_type` enum('Educational','Vocational','Psychological','Other') NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `duration_weeks` int(11) DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `assigned_staff_id` int(11) DEFAULT NULL,
  `status` enum('Active','Inactive','Completed','Cancelled') DEFAULT 'Active',
  `requirements` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`program_id`, `program_name`, `program_type`, `description`, `start_date`, `end_date`, `duration_weeks`, `capacity`, `location`, `assigned_staff_id`, `status`, `requirements`) VALUES
(1, 'Anger Management Workshop', 'Psychological', '', '2025-11-02', '2025-11-12', 2, 15, 'Tacloban City', 4, 'Active', 'ID');

-- --------------------------------------------------------

--
-- Table structure for table `releases`
--

CREATE TABLE `releases` (
  `release_id` int(11) NOT NULL,
  `inmate_id` int(11) NOT NULL,
  `release_date` date DEFAULT NULL,
  `reason` enum('Sentence Completed','Parole','Other') NOT NULL,
  `approved_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(1, 'Administrator'),
(2, 'Correctional Officer'),
(3, 'Medical Staff'),
(4, 'Rehabilitation Staff'),
(5, 'Visitor');

-- --------------------------------------------------------

--
-- Table structure for table `transfers`
--

CREATE TABLE `transfers` (
  `transfer_id` int(11) NOT NULL,
  `inmate_id` int(11) NOT NULL,
  `from_block` varchar(50) DEFAULT NULL,
  `to_block` varchar(50) DEFAULT NULL,
  `transfer_date` date DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transfers`
--

INSERT INTO `transfers` (`transfer_id`, `inmate_id`, `from_block`, `to_block`, `transfer_date`, `approved_by`, `reason`) VALUES
(1, 1, 'B1', 'A1', '2025-11-02', 1, 'dsadas'),
(2, 1, 'B1', 'A1', '2025-11-02', 1, 'dsadas'),
(3, 1, 'B1', 'A1', '2025-11-02', 1, 'dsadsa');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `role_id`, `full_name`, `email`, `password`, `created_at`) VALUES
(1, 1, 'System Administrator', 'admin@pms.com', '$2y$10$2VOGTpNuNvbUiH6M.OVaZ.Y.d/wFJtDxXKl40daBSzmvsKklqqVti', '2025-11-01 13:04:17'),
(2, 3, 'Danna Villanueva', 'medical@pms.com', '$2y$10$TyZAUJBuyXDUO4by4SP/6uLiVaYEgVL9bZWuVk.SOHBprLBIk4pni', '2025-11-01 13:25:04'),
(3, 2, 'Denise Claire Ocena Lledo', 'officer@pms.com', '$2y$10$.THnmryvdYajosxpnboMVuDJzsFprHaT1ds.TGxlkAA215ZI10qsa', '2025-11-01 13:25:29'),
(4, 4, 'Quenyss Almaden', 'rehab@pms.com', '$2y$10$zrfU9ms5vW3jHmfrUXtPZeEqVEe0SQK6UteZ9cFT0OlElq5nrXGpS', '2025-11-01 13:25:59'),
(5, 3, 'Elizabeth Baker', 'medical2@pms.com', '$2y$10$722YuvHsswQTeGC1sySQJuuNUsqY6Lho5HX22m.sdLc93iIvX94V.', '2025-11-01 13:32:02'),
(6, 5, 'Dhina Lazaro', 'one@gmail.com', '$2y$10$UfRKT6KcGZ8i9jBXzlKJ8.WVLrNPJqQIKvPfJMStePgmFaAzWbIxm', '2025-11-02 01:21:39');

-- --------------------------------------------------------

--
-- Table structure for table `visitations`
--

CREATE TABLE `visitations` (
  `visit_id` int(11) NOT NULL,
  `inmate_id` int(11) NOT NULL,
  `visitor_id` int(11) NOT NULL,
  `visit_type` enum('Conjugal Visit','Paduhol Visit','Visit to the Inmate') DEFAULT 'Visit to the Inmate',
  `scheduled_date` datetime NOT NULL,
  `status` enum('Pending','Approved','Denied','Completed','Cancelled') DEFAULT 'Pending',
  `notes` text DEFAULT NULL,
  `relationship` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `visitations`
--

INSERT INTO `visitations` (`visit_id`, `inmate_id`, `visitor_id`, `visit_type`, `scheduled_date`, `status`, `notes`, `relationship`) VALUES
(1, 16, 1, 'Conjugal Visit', '2025-11-02 09:22:00', 'Approved', 'adsadsa', 'Family'),
(2, 8, 1, 'Visit to the Inmate', '2025-11-02 09:31:00', 'Approved', 'dsada', 'Family');

-- --------------------------------------------------------

--
-- Table structure for table `visitors`
--

CREATE TABLE `visitors` (
  `visitor_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `relationship` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `visitors`
--

INSERT INTO `visitors` (`visitor_id`, `user_id`, `relationship`) VALUES
(1, 6, 'Family');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `behavior_logs`
--
ALTER TABLE `behavior_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `inmate_id` (`inmate_id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `incidents`
--
ALTER TABLE `incidents`
  ADD PRIMARY KEY (`incident_id`),
  ADD KEY `inmate_id` (`inmate_id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `inmates`
--
ALTER TABLE `inmates`
  ADD PRIMARY KEY (`inmate_id`);

--
-- Indexes for table `inmate_programs`
--
ALTER TABLE `inmate_programs`
  ADD PRIMARY KEY (`inmate_program_id`),
  ADD KEY `inmate_id` (`inmate_id`),
  ADD KEY `program_id` (`program_id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `medical_records`
--
ALTER TABLE `medical_records`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `inmate_id` (`inmate_id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`program_id`);

--
-- Indexes for table `releases`
--
ALTER TABLE `releases`
  ADD PRIMARY KEY (`release_id`),
  ADD KEY `inmate_id` (`inmate_id`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `transfers`
--
ALTER TABLE `transfers`
  ADD PRIMARY KEY (`transfer_id`),
  ADD KEY `inmate_id` (`inmate_id`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `visitations`
--
ALTER TABLE `visitations`
  ADD PRIMARY KEY (`visit_id`),
  ADD KEY `inmate_id` (`inmate_id`),
  ADD KEY `visitor_id` (`visitor_id`);

--
-- Indexes for table `visitors`
--
ALTER TABLE `visitors`
  ADD PRIMARY KEY (`visitor_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `behavior_logs`
--
ALTER TABLE `behavior_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `incidents`
--
ALTER TABLE `incidents`
  MODIFY `incident_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `inmates`
--
ALTER TABLE `inmates`
  MODIFY `inmate_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `inmate_programs`
--
ALTER TABLE `inmate_programs`
  MODIFY `inmate_program_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `medical_records`
--
ALTER TABLE `medical_records`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `program_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `releases`
--
ALTER TABLE `releases`
  MODIFY `release_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `transfers`
--
ALTER TABLE `transfers`
  MODIFY `transfer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `visitations`
--
ALTER TABLE `visitations`
  MODIFY `visit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `visitors`
--
ALTER TABLE `visitors`
  MODIFY `visitor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `behavior_logs`
--
ALTER TABLE `behavior_logs`
  ADD CONSTRAINT `behavior_logs_ibfk_1` FOREIGN KEY (`inmate_id`) REFERENCES `inmates` (`inmate_id`),
  ADD CONSTRAINT `behavior_logs_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `incidents`
--
ALTER TABLE `incidents`
  ADD CONSTRAINT `incidents_ibfk_1` FOREIGN KEY (`inmate_id`) REFERENCES `inmates` (`inmate_id`),
  ADD CONSTRAINT `incidents_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `inmate_programs`
--
ALTER TABLE `inmate_programs`
  ADD CONSTRAINT `inmate_programs_ibfk_1` FOREIGN KEY (`inmate_id`) REFERENCES `inmates` (`inmate_id`),
  ADD CONSTRAINT `inmate_programs_ibfk_2` FOREIGN KEY (`program_id`) REFERENCES `programs` (`program_id`),
  ADD CONSTRAINT `inmate_programs_ibfk_3` FOREIGN KEY (`staff_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `medical_records`
--
ALTER TABLE `medical_records`
  ADD CONSTRAINT `medical_records_ibfk_1` FOREIGN KEY (`inmate_id`) REFERENCES `inmates` (`inmate_id`),
  ADD CONSTRAINT `medical_records_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `releases`
--
ALTER TABLE `releases`
  ADD CONSTRAINT `releases_ibfk_1` FOREIGN KEY (`inmate_id`) REFERENCES `inmates` (`inmate_id`),
  ADD CONSTRAINT `releases_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `transfers`
--
ALTER TABLE `transfers`
  ADD CONSTRAINT `transfers_ibfk_1` FOREIGN KEY (`inmate_id`) REFERENCES `inmates` (`inmate_id`),
  ADD CONSTRAINT `transfers_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);

--
-- Constraints for table `visitations`
--
ALTER TABLE `visitations`
  ADD CONSTRAINT `visitations_ibfk_1` FOREIGN KEY (`inmate_id`) REFERENCES `inmates` (`inmate_id`),
  ADD CONSTRAINT `visitations_ibfk_2` FOREIGN KEY (`visitor_id`) REFERENCES `visitors` (`visitor_id`);

--
-- Constraints for table `visitors`
--
ALTER TABLE `visitors`
  ADD CONSTRAINT `visitors_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

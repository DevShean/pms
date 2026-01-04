-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 31, 2025 at 06:39 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.5.0

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
  `marital_status` varchar(50) DEFAULT NULL COMMENT 'Marital Status',
  `place_of_birth` varchar(255) DEFAULT NULL COMMENT 'Place of Birth',
  `height` varchar(50) DEFAULT NULL COMMENT 'Height (e.g., 5''10")',
  `weight` varchar(50) DEFAULT NULL COMMENT 'Weight (e.g., 180 lbs)',
  `hair_description` varchar(100) DEFAULT NULL COMMENT 'Hair color/description',
  `complexion` varchar(100) DEFAULT NULL COMMENT 'Complexion description',
  `eyes_description` varchar(100) DEFAULT NULL COMMENT 'Eye color/description',
  `citizenship` varchar(100) DEFAULT NULL COMMENT 'Citizenship',
  `religion` varchar(100) DEFAULT NULL COMMENT 'Religion',
  `race` varchar(100) DEFAULT NULL COMMENT 'Race/Ethnicity',
  `occupation` varchar(255) DEFAULT NULL COMMENT 'Previous occupation',
  `no_of_children` int(11) DEFAULT NULL COMMENT 'Number of children',
  `permanent_address` text DEFAULT NULL COMMENT 'Permanent address',
  `provincial_address` text DEFAULT NULL COMMENT 'Provincial address',
  `educational_attainment` varchar(100) DEFAULT NULL COMMENT 'Educational attainment',
  `course` varchar(255) DEFAULT NULL COMMENT 'Course/Major studied',
  `school_attended` varchar(255) DEFAULT NULL COMMENT 'School/University attended',
  `father_name` varchar(255) DEFAULT NULL COMMENT 'Father''s full name',
  `father_address` text DEFAULT NULL COMMENT 'Father''s address',
  `mother_name` varchar(255) DEFAULT NULL COMMENT 'Mother''s full name',
  `mother_address` text DEFAULT NULL COMMENT 'Mother''s address',
  `wife_clw_name` varchar(255) DEFAULT NULL COMMENT 'Wife/Common-Law Wife name',
  `wife_clw_address` text DEFAULT NULL COMMENT 'Wife/Common-Law Wife address',
  `relative_name` varchar(255) DEFAULT NULL COMMENT 'Relative contact name',
  `relative_address` text DEFAULT NULL COMMENT 'Relative contact address',
  `contact_number` varchar(20) DEFAULT NULL COMMENT 'Contact Number',
  `return_rate` varchar(100) DEFAULT NULL COMMENT 'Return Rate',
  `date_time_received` datetime DEFAULT NULL COMMENT 'Date/Time Received',
  `turned_over_by` varchar(255) DEFAULT NULL COMMENT 'Turned Over by',
  `receiving_duty_officer` varchar(255) DEFAULT NULL COMMENT 'Receiving Duty Officer',
  `offense_charged` text DEFAULT NULL COMMENT 'Offense/s Charged',
  `criminal_case_number` varchar(255) DEFAULT NULL COMMENT 'Criminal Case No./s',
  `case_court` varchar(255) DEFAULT NULL COMMENT 'Court',
  `case_status` varchar(100) DEFAULT NULL COMMENT 'Case Status',
  `prisoner_property` text DEFAULT NULL COMMENT 'Prisoner''s Property',
  `property_receipt_number` varchar(255) DEFAULT NULL COMMENT 'Property Receipt No.',
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
(5, 3, 'Elizabeth Baker', 'medical2@pms.com', '$2y$10$722YuvHsswQTeGC1sySQJuuNUsqY6Lho5HX22m.sdLc93iIvX94V.', '2025-11-01 13:32:02');

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
  `relationship` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
  MODIFY `visit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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

-- Migration: Add Contact, Case, and Property Information Columns to Inmates Table
-- Date: 2025-12-31
-- Description: Add contact, legal case, and prisoner property information fields

ALTER TABLE `inmates` 
ADD COLUMN `contact_number` VARCHAR(20) DEFAULT NULL COMMENT 'Contact Number' AFTER `relative_address`,
ADD COLUMN `return_rate` VARCHAR(100) DEFAULT NULL COMMENT 'Return Rate' AFTER `contact_number`,
ADD COLUMN `date_time_received` DATETIME DEFAULT NULL COMMENT 'Date/Time Received' AFTER `return_rate`,
ADD COLUMN `turned_over_by` VARCHAR(255) DEFAULT NULL COMMENT 'Turned Over by' AFTER `date_time_received`,
ADD COLUMN `receiving_duty_officer` VARCHAR(255) DEFAULT NULL COMMENT 'Receiving Duty Officer' AFTER `turned_over_by`,
ADD COLUMN `offense_charged` TEXT DEFAULT NULL COMMENT 'Offense/s Charged' AFTER `receiving_duty_officer`,
ADD COLUMN `criminal_case_number` VARCHAR(255) DEFAULT NULL COMMENT 'Criminal Case No./s' AFTER `offense_charged`,
ADD COLUMN `case_court` VARCHAR(255) DEFAULT NULL COMMENT 'Court' AFTER `criminal_case_number`,
ADD COLUMN `case_status` VARCHAR(100) DEFAULT NULL COMMENT 'Case Status' AFTER `case_court`,
ADD COLUMN `prisoner_property` TEXT DEFAULT NULL COMMENT 'Prisoner\'s Property' AFTER `case_status`,
ADD COLUMN `property_receipt_number` VARCHAR(255) DEFAULT NULL COMMENT 'Property Receipt No.' AFTER `prisoner_property`;

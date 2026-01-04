-- Migration: Add Personal Information Columns to Inmates Table
-- Date: 2025-12-31
-- Description: Add personal, family contact, and address information fields

ALTER TABLE `inmates` 
ADD COLUMN `marital_status` VARCHAR(50) DEFAULT NULL COMMENT 'Marital Status' AFTER `photo_path`,
ADD COLUMN `place_of_birth` VARCHAR(255) DEFAULT NULL COMMENT 'Place of Birth' AFTER `marital_status`,
ADD COLUMN `height` VARCHAR(50) DEFAULT NULL COMMENT 'Height (e.g., 5\'10")' AFTER `place_of_birth`,
ADD COLUMN `weight` VARCHAR(50) DEFAULT NULL COMMENT 'Weight (e.g., 180 lbs)' AFTER `height`,
ADD COLUMN `hair_description` VARCHAR(100) DEFAULT NULL COMMENT 'Hair color/description' AFTER `weight`,
ADD COLUMN `complexion` VARCHAR(100) DEFAULT NULL COMMENT 'Complexion description' AFTER `hair_description`,
ADD COLUMN `eyes_description` VARCHAR(100) DEFAULT NULL COMMENT 'Eye color/description' AFTER `complexion`,
ADD COLUMN `citizenship` VARCHAR(100) DEFAULT NULL COMMENT 'Citizenship' AFTER `eyes_description`,
ADD COLUMN `religion` VARCHAR(100) DEFAULT NULL COMMENT 'Religion' AFTER `citizenship`,
ADD COLUMN `race` VARCHAR(100) DEFAULT NULL COMMENT 'Race/Ethnicity' AFTER `religion`,
ADD COLUMN `occupation` VARCHAR(255) DEFAULT NULL COMMENT 'Previous occupation' AFTER `race`,
ADD COLUMN `no_of_children` INT(11) DEFAULT NULL COMMENT 'Number of children' AFTER `occupation`,
ADD COLUMN `permanent_address` TEXT DEFAULT NULL COMMENT 'Permanent address' AFTER `no_of_children`,
ADD COLUMN `provincial_address` TEXT DEFAULT NULL COMMENT 'Provincial address' AFTER `permanent_address`,
ADD COLUMN `educational_attainment` VARCHAR(100) DEFAULT NULL COMMENT 'Educational attainment' AFTER `provincial_address`,
ADD COLUMN `course` VARCHAR(255) DEFAULT NULL COMMENT 'Course/Major studied' AFTER `educational_attainment`,
ADD COLUMN `school_attended` VARCHAR(255) DEFAULT NULL COMMENT 'School/University attended' AFTER `course`,
ADD COLUMN `father_name` VARCHAR(255) DEFAULT NULL COMMENT 'Father\'s full name' AFTER `school_attended`,
ADD COLUMN `father_address` TEXT DEFAULT NULL COMMENT 'Father\'s address' AFTER `father_name`,
ADD COLUMN `mother_name` VARCHAR(255) DEFAULT NULL COMMENT 'Mother\'s full name' AFTER `father_address`,
ADD COLUMN `mother_address` TEXT DEFAULT NULL COMMENT 'Mother\'s address' AFTER `mother_name`,
ADD COLUMN `wife_clw_name` VARCHAR(255) DEFAULT NULL COMMENT 'Wife/Common-Law Wife name' AFTER `mother_address`,
ADD COLUMN `wife_clw_address` TEXT DEFAULT NULL COMMENT 'Wife/Common-Law Wife address' AFTER `wife_clw_name`,
ADD COLUMN `relative_name` VARCHAR(255) DEFAULT NULL COMMENT 'Relative contact name' AFTER `wife_clw_address`,
ADD COLUMN `relative_address` TEXT DEFAULT NULL COMMENT 'Relative contact address' AFTER `relative_name`;

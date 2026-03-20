-- database.sql

CREATE DATABASE IF NOT EXISTS `hostel_db`;
USE `hostel_db`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `role` ENUM('admin', 'student') NOT NULL DEFAULT 'student',
  PRIMARY KEY (`id`)
);

INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES
('admin', 'admin@hostel.com', 'admin123', 'admin'),
('Test Student', 'test@test.com', 'student123', 'student');

CREATE TABLE `students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `college_name` varchar(100) NOT NULL,
  `roll_no` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `gender` ENUM('Boy', 'Girl', 'Other') NOT NULL,
  `fee_due_date` date DEFAULT NULL,
  `fee_due_amount` int(11) DEFAULT 5000,
  `room_id` int(11) DEFAULT '0',
  `room_rent` int(11) DEFAULT 2000,
  `mess_fee` int(11) DEFAULT 2500,
  `maintenance_fee` int(11) DEFAULT 500,
  `room_assigned_date` date DEFAULT NULL,
  `room_expiry_date` date DEFAULT NULL,
  `photo` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

INSERT INTO `students` (`name`, `college_name`, `roll_no`, `email`, `phone`, `gender`, `fee_due_date`, `fee_due_amount`, `room_id`, `room_assigned_date`, `room_expiry_date`, `photo`) VALUES
('Test Student', 'ABC College', 'BCA001', 'test@test.com', '1234567890', 'Boy', '2026-04-20', 5000, 0, NULL, NULL, '');

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_no` varchar(10) NOT NULL,
  `capacity` int(11) NOT NULL,
  `available_beds` int(11) NOT NULL,
  `gender` ENUM('Boy', 'Girl') NOT NULL DEFAULT 'Boy',
  PRIMARY KEY (`id`)
);

INSERT INTO `rooms` (`room_no`, `capacity`, `available_beds`) VALUES
('A-101', 3, 3),
('A-102', 2, 2);

CREATE TABLE `complaints` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `date` date NOT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  PRIMARY KEY (`id`)
);

CREATE TABLE `notices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `room_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `status` ENUM('Pending', 'Approved', 'Rejected', 'Waitlisted') NOT NULL DEFAULT 'Pending',
  `request_date` date NOT NULL,
  PRIMARY KEY (`id`)
);

INSERT INTO `notices` (`title`, `content`, `date`) VALUES
('Welcome Notice', 'Welcome to the Hostel Management System.', '2024-01-01');

CREATE TABLE `fees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date` date NOT NULL,
  `room_rent` int(11) DEFAULT 0,
  `mess_fee` int(11) DEFAULT 0,
  `maintenance_fee` int(11) DEFAULT 0,
  `total_amount` decimal(10,2) DEFAULT 0,
  PRIMARY KEY (`id`)
);

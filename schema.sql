CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usename` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `event_categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(50) NOT NULL,
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `category_name` (`category_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `event_details` (
  `eid` int(10) NOT NULL AUTO_INCREMENT,
  `event_name` varchar(255) DEFAULT NULL COMMENT 'name of the evnet',
  `event_date` date NOT NULL,
  `event_location` varchar(255) DEFAULT NULL COMMENT 'location of the evnet/venue',
  `event_maps_link` varchar(1024) DEFAULT NULL,
  `event_image_path` varchar(1024) DEFAULT NULL,
  `event_description` text DEFAULT NULL,
  `event_status` enum('active','not-active') NOT NULL DEFAULT 'active',
  `category_id` int(11) NOT NULL,
  `entered_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`eid`),
  UNIQUE KEY `event_name` (`event_name`),
  KEY `fk_event_category` (`category_id`),
  CONSTRAINT `fk_event_category` FOREIGN KEY (`category_id`) REFERENCES `event_categories` (`category_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `ticket_type` (
  `ticket_id` int(10) NOT NULL,
  `eid` int(11) NOT NULL,
  `ticket_name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `capacity` int(11) NOT NULL,
  `sold_count` int(11) DEFAULT 0,
  `sale_start` datetime DEFAULT NULL,
  `sale_end` datetime DEFAULT NULL,
  `status` enum('available','sold_out','hidden','expired') DEFAULT 'available',
  PRIMARY KEY (`ticket_id`),
  KEY `eid` (`eid`),
  CONSTRAINT `ticket_type_ibfk_1` FOREIGN KEY (`eid`) REFERENCES `event_details` (`eid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `bookings` (
  `booking_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `booking_reference` varchar(20) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_status` enum('pending','completed','failed','refunded') DEFAULT 'pending',
  `payment_method` enum('khalti','esewa','cash') DEFAULT 'khalti',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`booking_id`),
  UNIQUE KEY `booking_reference` (`booking_reference`),
  KEY `fk_booking_user` (`user_id`),
  CONSTRAINT `fk_booking_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `booking_items` (
  `item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` int(10) unsigned NOT NULL,
  `ticket_type_id` int(11) NOT NULL,
  `quantity` int(5) NOT NULL,
  `price_at_purchase` decimal(10,2) NOT NULL,
  PRIMARY KEY (`item_id`),
  KEY `fk_item_booking` (`booking_id`),
  KEY `fk_item_ticket` (`ticket_type_id`),
  CONSTRAINT `fk_item_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_item_ticket` FOREIGN KEY (`ticket_type_id`) REFERENCES `ticket_type` (`ticket_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `tickets` (
  `ticket_instance_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` int(10) unsigned NOT NULL,
  `ticket_type_id` int(10) NOT NULL,
  `ticket_hash` varchar(255) NOT NULL,
  -- `is_scanned` tinyint(1) DEFAULT 0,
  -- `scanned_at` datetime DEFAULT NULL,
  PRIMARY KEY (`ticket_instance_id`),
  UNIQUE KEY `ticket_hash` (`ticket_hash`),
  KEY `fk_ticket_booking` (`booking_id`),
  KEY `fk_ticket_type` (`ticket_type_id`),
  CONSTRAINT `fk_ticket_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ticket_type` FOREIGN KEY (`ticket_type_id`) REFERENCES `ticket_type` (`ticket_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `bookmarks` (
  `eid` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`eid`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
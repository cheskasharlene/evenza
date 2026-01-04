-- EVENZA Payments Table Schema
-- This script creates the payments table for PayPal transaction tracking

-- Create payments table if it doesn't exist
CREATE TABLE IF NOT EXISTS `payments` (
  `paymentId` INT(11) NOT NULL AUTO_INCREMENT,
  `reservationId` INT(11) NOT NULL,
  `userId` INT(11) NOT NULL,
  `transactionId` VARCHAR(255) NOT NULL COMMENT 'PayPal Transaction Reference',
  `amount` DECIMAL(10, 2) NOT NULL,
  `packageName` VARCHAR(100) NOT NULL COMMENT 'Bronze/Silver/Gold Package',
  `paymentStatus` ENUM('pending', 'completed', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
  `createdAt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`paymentId`),
  UNIQUE KEY `idx_transactionId` (`transactionId`),
  KEY `idx_reservationId` (`reservationId`),
  KEY `idx_userId` (`userId`),
  KEY `idx_paymentStatus` (`paymentStatus`),
  CONSTRAINT `fk_payments_reservation` FOREIGN KEY (`reservationId`) REFERENCES `reservations` (`reservationId`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_payments_user` FOREIGN KEY (`userId`) REFERENCES `users` (`userid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


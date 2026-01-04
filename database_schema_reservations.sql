-- EVENZA Reservations Table Schema
-- This script works with the EXISTING database structure
-- The reservations table uses packageId (FK to packages table) not packageTier
-- Column names: reservationDate (not bookingDate), totalAmount (not totalPrice)

-- Note: The reservations table already exists with this structure:
-- reservationId, userId, eventId, packageId, reservationDate, startTime, endTime, 
-- totalAmount, status, createdAt, updatedAt

-- This script ensures the table structure is correct and adds any missing foreign key constraints

-- Add foreign key constraint for packageId if it doesn't exist
SET @dbname = DATABASE();
SET @constraint_name = 'fk_reservations_package';

SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
    WHERE
      (table_schema = @dbname)
      AND (table_name = 'reservations')
      AND (constraint_name = @constraint_name)
  ) > 0,
  "SELECT 1",
  "ALTER TABLE reservations ADD CONSTRAINT fk_reservations_package FOREIGN KEY (packageId) REFERENCES packages(packageId) ON DELETE RESTRICT ON UPDATE CASCADE"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Ensure packages table exists with correct structure
CREATE TABLE IF NOT EXISTS `packages` (
  `packageId` INT(11) NOT NULL AUTO_INCREMENT,
  `packageName` VARCHAR(100) NOT NULL,
  `price` DECIMAL(10, 2) NOT NULL,
  PRIMARY KEY (`packageId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default packages if they don't exist
INSERT IGNORE INTO `packages` (`packageId`, `packageName`, `price`) VALUES
(1, 'Bronze Package', 7000.00),
(2, 'Silver Package', 10000.00),
(3, 'Gold Package', 15000.00);


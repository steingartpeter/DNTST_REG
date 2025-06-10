CREATE DATABASE `dentist_regs` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin */;


-- Drop table if it exists to allow for easy re-creation during development
DROP TABLE IF EXISTS `dentist_regs`.`Users`;

CREATE TABLE `dentist_regs`.`Users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `first_name` VARCHAR(100) NOT NULL,
    `last_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL, -- For storing securely hashed passwords (e.g., using password_hash())
    `phone_number` VARCHAR(20) DEFAULT NULL,
    `date_of_birth` DATE DEFAULT NULL,
    -- If storing SSN, it MUST be encrypted by your application before storing.
    -- The actual SSN should never be stored in plain text.
    `ssn_encrypted` VARCHAR(255) DEFAULT NULL,
    `role` ENUM('patient', 'dentist','med-assistant', 'admin') NOT NULL DEFAULT 'patient',
    `is_active` BOOLEAN DEFAULT TRUE, -- Useful for deactivating accounts instead of deleting
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- You might also want an index on email for faster lookups, though UNIQUE already creates one.
-- CREATE INDEX idx_email ON Users(email);

-- Example of how you might add a dentist-specific table later, linking to Users
DROP TABLE IF EXISTS `dentist_regs`.`DentistProfiles`;
CREATE TABLE `dentist_regs`.`DentistProfiles` (
	`user_id` INT PRIMARY KEY,
	`specialization` VARCHAR(255) DEFAULT NULL,
	`bio` TEXT DEFAULT NULL,
	`license_number` VARCHAR(100) DEFAULT NULL,
	FOREIGN KEY (`user_id`) REFERENCES `Users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Some dummy user data
INSERT INTO 
`dentist_regs`.`users` 
(`first_name`, `last_name`, `email`, `password_hash`, `phone_number`, `date_of_birth`, `role`) 
VALUES 
('Lilla', 'Pesthenlehrer', 'lilla.pesthenlehrer@maxmail.com', 'a', '06301234567', '1986-10-21', 'patient'),
('Renata', 'Nemeth', 'renata.nemeth@maxmail.com', 'a', '06301234567', '1989-12-24', 'patient'),
('Viktor', 'Elek', 'viktor.elek@maxmail.com', 'a', '06301234567', '1977-07-25', 'med-assistant'),
('Balint', 'Simicska', 'balint.simicska@maxmail.com', 'a', '06301234567', '1985-07-04', 'dentist'),
('Szabolcs', 'Simicska', 'szabolcs.simicska@maxmail.com', 'a', '06301234567', '2004-04-05', 'patient'),
('Istvan', 'Nemeth', 'istvan.nemeth@maxmail.com', 'a', '06301234567', '2005-12-27', 'med-assistant'),
('Maria', 'Kiss', 'maria.kiss@maxmail.com', 'a', '06301234567', '1979-03-16', 'med-assistant'),
('Maria', 'Kovacs', 'maria.kovacs@maxmail.com', 'a', '06301234567', '1985-10-17', 'patient'),
('Jozsef', 'Nemeth', 'jozsef.nemeth@maxmail.com', 'a', '06301234567', '2011-12-10', 'patient'),
('Csilla', 'Teplan', 'csilla.teplan@maxmail.com', 'a', '06301234567', '1983-07-24', 'patient'),
('Csilla', 'Popper', 'csilla.popper@maxmail.com', 'a', '06301234567', '2000-03-28', 'patient'),
('Balint', 'Parcsami', 'balint.parcsami@maxmail.com', 'a', '06301234567', '1997-01-31', 'patient'),
('Viktor', 'Ingram', 'viktor.ingram@maxmail.com', 'a', '06301234567', '2007-04-23', 'patient'),
('Maria', 'Ingram', 'maria.ingram0@maxmail.com', 'a', '06301234567', '1981-06-17', 'dentist'),
('Istvan', 'Pesthenlehrer', 'istvan.pesthenlehrer@maxmail.com', 'a', '06301234567', '2001-12-21', 'patient'),
('Istvan', 'Szabo', 'istvan.szabo@maxmail.com', 'a', '06301234567', '2006-06-13', 'dentist'),
('Anita', 'Szabo', 'anita.szabo@maxmail.com', 'a', '06301234567', '2010-08-07', 'patient'),
('Marta', 'Lovasz', 'marta.lovasz@maxmail.com', 'a', '06301234567', '2004-08-28', 'admin'),
('Szabolcs', 'Ingram', 'szabolcs.ingram@maxmail.com', 'a', '06301234567', '2000-10-08', 'patient'),
('Istvan', 'Simicska', 'istvan.simicska@maxmail.com', 'a', '06301234567', '1994-11-21', 'patient'),
('Peter', 'Ingram', 'peter.ingram@maxmail.com', 'a', '06301234567', '2013-02-26', 'dentist'),
('Szabolcs', 'Lovasz', 'szabolcs.lovasz@maxmail.com', 'a', '06301234567', '1996-02-18', 'patient'),
('Ingrid', 'Popper', 'ingrid.popper@maxmail.com', 'a', '06301234567', '1976-01-19', 'patient'),
('Maria', 'Ingram', 'maria.ingram@maxmail.com', 'a', '06301234567', '1975-03-31', 'patient'),
('Csilla', 'Popper', 'csilla.popper2@maxmail.com', 'a', '06301234567', '1979-12-23', 'patient'),
('Tamas', 'Elek', 'tamas.elek@maxmail.com', 'a', '06301234567', '1985-11-10', 'patient'),
('Antal', 'Nagy', 'antal.nagy@maxmail.com', 'a', '06301234567', '1989-01-14', 'patient'),
('Laszlo', 'Popper', 'laszlo.popper@maxmail.com', 'a', '06301234567', '2005-04-26', 'admin'),
('Renata', 'Teplan', 'renata.teplan@maxmail.com', 'a', '06301234567', '1997-01-10', 'patient'),
('Geza', 'Elek', 'geza.elek@maxmail.com', 'a', '06301234567', '1988-09-23', 'patient'),
('Istvan', 'Romhanyi', 'istvan.romhanyi@maxmail.com', 'a', '06301234567', '1984-09-30', 'patient'),
('Szabolcs', 'Racz', 'szabolcs.racz@maxmail.com', 'a', '06301234567', '2015-08-19', 'dentist'),
('Jozsef', 'Alapi', 'jozsef.alapi@maxmail.com', 'a', '06301234567', '2014-10-31', 'patient'),
('Istvan', 'Szabo', 'istvan.szabo2@maxmail.com', 'a', '06301234567', '2001-04-10', 'patient'),
('Maria', 'Elek', 'maria.elek@maxmail.com', 'a', '06301234567', '2009-07-24', 'med-assistant'),
('Geza', 'Popper', 'geza.popper@maxmail.com', 'a', '06301234567', '2001-10-29', 'med-assistant'),
('Jozsef', 'Szabo', 'jozsef.szabo@maxmail.com', 'a', '06301234567', '2004-04-14', 'dentist'),
('Maria', 'Nemeth', 'maria.nemeth@maxmail.com', 'a', '06301234567', '1990-10-24', 'patient'),
('Szabolcs', 'Alapi', 'szabolcs.alapi@maxmail.com', 'a', '06301234567', '1996-03-28', 'patient'),
('Zoltan', 'Lovasz', 'zoltan.lovasz@maxmail.com', 'a', '06301234567', '2005-01-06', 'patient'),
('Szabolcs', 'Pesthenlehrer', 'szabolcs.pesthenlehrer@maxmail.com', 'a', '06301234567', '2003-04-09', 'patient'),
('Istvan', 'Kocsis', 'istvan.kocsis@maxmail.com', 'a', '06301234567', '2001-07-01', 'patient'),
('Viktor', 'Simicska', 'viktor.simicska@maxmail.com', 'a', '06301234567', '1984-08-22', 'patient'),
('Csilla', 'Teplan', 'csilla.teplan2@maxmail.com', 'a', '06301234567', '1976-02-05', 'patient'),
('Maria', 'Szentpeteri', 'maria.szentpeteri@maxmail.com', 'a', '06301234567', '2008-09-06', 'patient'),
('Szabolcs', 'Teplan', 'szabolcs.teplan@maxmail.com', 'a', '06301234567', '1981-12-28', 'patient'),
('Zoltan', 'Simicska', 'zoltan.simicska@maxmail.com', 'a', '06301234567', '2000-10-04', 'patient'),
('Zoltan', 'Ingram', 'zoltan.ingram@maxmail.com', 'a', '06301234567', '1977-06-14', 'patient'),
('Marta', 'Szentpeteri', 'marta.szentpeteri@maxmail.com', 'a', '06301234567', '1984-02-18', 'patient'),
('Lilla', 'Racz', 'lilla.racz@maxmail.com', 'a', '06301234567', '1991-06-01', 'patient'),
('Balint', 'Romhanyi', 'balint.romhanyi@maxmail.com', 'a', '06301234567', '1999-11-03', 'patient');

-- Some dentist rpofiles
INSERT INTO `dentist_regs`.`dentistprofiles` 
(`user_id`, `specialization`, `bio`, `license_number`) 
VALUES 
('373', 'Ínysorvadás, gyökérkezelés', 'Ide jön a bemutatkozás ...', '000000000000'),
('375', 'Infekciós elváltozások', 'Ide jön a bemutatkozás ...', '000000000000'),
('380', 'Daganatok, idült elváltozások', 'Ide jön a bemutatkozás ...', '000000000000'),
('381', 'Aneszetziológia, plasztikai beavtkozások', 'Ide jön a bemutatkozás ...', '000000000000'),
('396', 'Ínysorvadás, gyökérkezelés', 'Ide jön a bemutatkozás ...', '000000000000');

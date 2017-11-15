

-- Example of the animal list we will be implementing
-- --------------------------------------------------------
-- Birds
-- Cats
-- Cattle – Beef
-- Cattle – Dairy
-- Dogs
-- Horses
-- Pigs
-- Pocket pets
-- Poultry
-- Reptiles
-- Sheep – Meat
-- Sheep - Wool
-- Wildlife
-- Other exotic
-- Other large animal
-- --------------------------------------------------------

-- DROP TABLE IF EXISTS animal_type;
CREATE TABLE IF NOT EXISTS animal_type (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  profile_id INT UNSIGNED NOT NULL DEFAULT 0,
  name VARCHAR(128) NOT NULL DEFAULT '',
  description TEXT,
  min INT UNSIGNED NOT NULL DEFAULT 1,
  max INT UNSIGNED NOT NULL DEFAULT 0,
  notes TEXT,
  order_by INT UNSIGNED NOT NULL DEFAULT 0,
  del TINYINT(1) NOT NULL DEFAULT 0,
  modified DATETIME NOT NULL,
  created DATETIME NOT NULL,
  PRIMARY KEY (id),
  INDEX (profile_id)
) ENGINE=InnoDB;

-- DROP TABLE IF EXISTS animal_value;
CREATE TABLE IF NOT EXISTS animal_value (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  type_id INT UNSIGNED NOT NULL DEFAULT 0,
  placement_id INT UNSIGNED NOT NULL DEFAULT 0,
  name VARCHAR(128) NOT NULL DEFAULT '',
  value INT UNSIGNED NOT NULL DEFAULT 1,
  notes TEXT,
  modified DATETIME NOT NULL,
  created DATETIME NOT NULL,
  PRIMARY KEY (id),
  INDEX (placement_id)
) ENGINE=InnoDB;

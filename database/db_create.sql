-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema cgnst
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema cgnst
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `cgnst` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci ;
USE `cgnst` ;

-- -----------------------------------------------------
-- Table `cgnst`.`user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cgnst`.`user` ;

CREATE TABLE IF NOT EXISTS `cgnst`.`user` (
  `iduser` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(45) NOT NULL,
  `email` VARCHAR(45) NOT NULL,
  `birthDate` DATE NULL DEFAULT NULL,
  `tel` VARCHAR(15) NULL DEFAULT NULL,
  `password` VARCHAR(300) NOT NULL,
  `administrator` TINYINT NULL DEFAULT '0',
  PRIMARY KEY (`iduser`))
ENGINE = InnoDB
AUTO_INCREMENT = 18
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `cgnst`.`artikel`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cgnst`.`artikel` ;

CREATE TABLE IF NOT EXISTS `cgnst`.`artikel` (
  `idartikel` INT NOT NULL AUTO_INCREMENT,
  `contentHtml` TEXT NULL DEFAULT NULL,
  `author` INT NOT NULL,
  `date` DATETIME NOT NULL,
  `title` VARCHAR(300) NOT NULL,
  PRIMARY KEY (`idartikel`),
  INDEX `fk_artikel_users1_idx` (`author` ASC) VISIBLE,
  CONSTRAINT `fk_artikel_users1`
    FOREIGN KEY (`author`)
    REFERENCES `cgnst`.`user` (`iduser`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `cgnst`.`discipline`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cgnst`.`discipline` ;

CREATE TABLE IF NOT EXISTS `cgnst`.`discipline` (
  `iddiscipline` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `distance` INT NULL DEFAULT NULL,
  PRIMARY KEY (`iddiscipline`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `cgnst`.`exercise`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cgnst`.`exercise` ;

CREATE TABLE IF NOT EXISTS `cgnst`.`exercise` (
  `idexercise` INT NOT NULL AUTO_INCREMENT,
  `time` INT NOT NULL,
  `pauseAfter` INT NOT NULL,
  `name` VARCHAR(200) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `intensity` INT NULL DEFAULT NULL,
  `aim` VARCHAR(100) NULL DEFAULT NULL,
  PRIMARY KEY (`idexercise`))
ENGINE = InnoDB
AUTO_INCREMENT = 268
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `cgnst`.`exerciseGroup`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cgnst`.`exerciseGroup` ;

CREATE TABLE IF NOT EXISTS `cgnst`.`exerciseGroup` (
  `idexerciseGroup` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(200) NOT NULL DEFAULT 'Session group',
  `description` VARCHAR(1024) NULL DEFAULT NULL,
  PRIMARY KEY (`idexerciseGroup`))
ENGINE = InnoDB
AUTO_INCREMENT = 223
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `cgnst`.`exercise_has_exerciseGroup`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cgnst`.`exercise_has_exerciseGroup` ;

CREATE TABLE IF NOT EXISTS `cgnst`.`exercise_has_exerciseGroup` (
  `exercise_idexercise` INT NOT NULL,
  `exerciseGroup_idexerciseGroup` INT NOT NULL,
  PRIMARY KEY (`exercise_idexercise`, `exerciseGroup_idexerciseGroup`),
  INDEX `fk_exercise_has_exerciseGroup_exerciseGroup1_idx` (`exerciseGroup_idexerciseGroup` ASC) VISIBLE,
  INDEX `fk_exercise_has_exerciseGroup_exercise1_idx` (`exercise_idexercise` ASC) VISIBLE,
  CONSTRAINT `fk_exercise_has_exerciseGroup_exercise1`
    FOREIGN KEY (`exercise_idexercise`)
    REFERENCES `cgnst`.`exercise` (`idexercise`),
  CONSTRAINT `fk_exercise_has_exerciseGroup_exerciseGroup1`
    FOREIGN KEY (`exerciseGroup_idexerciseGroup`)
    REFERENCES `cgnst`.`exerciseGroup` (`idexerciseGroup`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `cgnst`.`group`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cgnst`.`group` ;

CREATE TABLE IF NOT EXISTS `cgnst`.`group` (
  `idgroup` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(500) NOT NULL,
  PRIMARY KEY (`idgroup`),
  UNIQUE INDEX `name` (`name` ASC) VISIBLE,
  UNIQUE INDEX `name_2` (`name` ASC) VISIBLE)
ENGINE = InnoDB
AUTO_INCREMENT = 101
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `cgnst`.`group_has_admin`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cgnst`.`group_has_admin` ;

CREATE TABLE IF NOT EXISTS `cgnst`.`group_has_admin` (
  `group_idgroup` INT NOT NULL,
  `user_iduser` INT NOT NULL,
  PRIMARY KEY (`group_idgroup`, `user_iduser`),
  INDEX `fk_group_has_user1_user1_idx` (`user_iduser` ASC) VISIBLE,
  INDEX `fk_group_has_user1_group1_idx` (`group_idgroup` ASC) VISIBLE,
  CONSTRAINT `fk_group_has_user1_group1`
    FOREIGN KEY (`group_idgroup`)
    REFERENCES `cgnst`.`group` (`idgroup`),
  CONSTRAINT `fk_group_has_user1_user1`
    FOREIGN KEY (`user_iduser`)
    REFERENCES `cgnst`.`user` (`iduser`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `cgnst`.`group_has_user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cgnst`.`group_has_user` ;

CREATE TABLE IF NOT EXISTS `cgnst`.`group_has_user` (
  `group_idgroup` INT NOT NULL,
  `user_iduser` INT NOT NULL,
  PRIMARY KEY (`group_idgroup`, `user_iduser`),
  INDEX `fk_group_has_user_user1_idx` (`user_iduser` ASC) VISIBLE,
  INDEX `fk_group_has_user_group1_idx` (`group_idgroup` ASC) VISIBLE,
  CONSTRAINT `fk_group_has_user_group1`
    FOREIGN KEY (`group_idgroup`)
    REFERENCES `cgnst`.`group` (`idgroup`),
  CONSTRAINT `fk_group_has_user_user1`
    FOREIGN KEY (`user_iduser`)
    REFERENCES `cgnst`.`user` (`iduser`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `cgnst`.`kalenderNote`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cgnst`.`kalenderNote` ;

CREATE TABLE IF NOT EXISTS `cgnst`.`kalenderNote` (
  `idkalenderNote` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(500) NOT NULL,
  `startDate` DATETIME NOT NULL,
  `endDate` DATETIME NOT NULL,
  `comment` TEXT NULL DEFAULT NULL,
  `uploadUser` INT NOT NULL,
  PRIMARY KEY (`idkalenderNote`),
  INDEX `uploadUser` (`uploadUser` ASC) VISIBLE,
  CONSTRAINT `kalenderNote_ibfk_1`
    FOREIGN KEY (`uploadUser`)
    REFERENCES `cgnst`.`user` (`iduser`))
ENGINE = InnoDB
AUTO_INCREMENT = 6
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `cgnst`.`kalenderNote_has_group`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cgnst`.`kalenderNote_has_group` ;

CREATE TABLE IF NOT EXISTS `cgnst`.`kalenderNote_has_group` (
  `kalenderNote_idkalenderNote` INT NOT NULL,
  `group_idgroup` INT NOT NULL,
  PRIMARY KEY (`kalenderNote_idkalenderNote`, `group_idgroup`),
  INDEX `group_idgroup` (`group_idgroup` ASC) VISIBLE,
  CONSTRAINT `kalenderNote_has_group_ibfk_1`
    FOREIGN KEY (`kalenderNote_idkalenderNote`)
    REFERENCES `cgnst`.`kalenderNote` (`idkalenderNote`),
  CONSTRAINT `kalenderNote_has_group_ibfk_2`
    FOREIGN KEY (`group_idgroup`)
    REFERENCES `cgnst`.`group` (`idgroup`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `cgnst`.`kalenderNotes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cgnst`.`kalenderNotes` ;

CREATE TABLE IF NOT EXISTS `cgnst`.`kalenderNotes` (
  `idkalenderNotes` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(500) NOT NULL,
  `startDate` DATETIME NOT NULL,
  `endDate` DATETIME NOT NULL,
  `comment` TEXT NULL DEFAULT NULL,
  `uploadUser` INT NOT NULL,
  PRIMARY KEY (`idkalenderNotes`),
  INDEX `uploadUser` (`uploadUser` ASC) VISIBLE,
  CONSTRAINT `kalenderNotes_ibfk_1`
    FOREIGN KEY (`uploadUser`)
    REFERENCES `cgnst`.`user` (`iduser`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `cgnst`.`permissionGroup`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cgnst`.`permissionGroup` ;

CREATE TABLE IF NOT EXISTS `cgnst`.`permissionGroup` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `writeArticles` TINYINT NULL DEFAULT '0',
  `configureUser` TINYINT NULL DEFAULT '0',
  `hierarchy` INT NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) VISIBLE)
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `cgnst`.`race`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cgnst`.`race` ;

CREATE TABLE IF NOT EXISTS `cgnst`.`race` (
  `idrace` INT NOT NULL AUTO_INCREMENT,
  `startDate` DATE NOT NULL,
  `endDate` DATE NOT NULL,
  `descriptionHtml` TEXT NULL DEFAULT NULL,
  `name` VARCHAR(200) NOT NULL,
  PRIMARY KEY (`idrace`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `cgnst`.`trainingFacility`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cgnst`.`trainingFacility` ;

CREATE TABLE IF NOT EXISTS `cgnst`.`trainingFacility` (
  `idtrainingFacility` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(200) NOT NULL,
  `descriptionHtml` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`idtrainingFacility`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `cgnst`.`race_has_trainingFacility`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cgnst`.`race_has_trainingFacility` ;

CREATE TABLE IF NOT EXISTS `cgnst`.`race_has_trainingFacility` (
  `race_idrace` INT NOT NULL,
  `trainingFacility_idtrainingFacility` INT NOT NULL,
  PRIMARY KEY (`race_idrace`, `trainingFacility_idtrainingFacility`),
  INDEX `fk_race_has_trainingFacility_trainingFacility1_idx` (`trainingFacility_idtrainingFacility` ASC) VISIBLE,
  INDEX `fk_race_has_trainingFacility_race1_idx` (`race_idrace` ASC) VISIBLE,
  CONSTRAINT `fk_race_has_trainingFacility_race1`
    FOREIGN KEY (`race_idrace`)
    REFERENCES `cgnst`.`race` (`idrace`),
  CONSTRAINT `fk_race_has_trainingFacility_trainingFacility1`
    FOREIGN KEY (`trainingFacility_idtrainingFacility`)
    REFERENCES `cgnst`.`trainingFacility` (`idtrainingFacility`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `cgnst`.`race_has_user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cgnst`.`race_has_user` ;

CREATE TABLE IF NOT EXISTS `cgnst`.`race_has_user` (
  `race_idrace` INT NOT NULL,
  `user_iduser` INT NOT NULL,
  `participates` TINYINT NOT NULL,
  PRIMARY KEY (`race_idrace`, `user_iduser`),
  INDEX `fk_race_has_user_user1_idx` (`user_iduser` ASC) VISIBLE,
  INDEX `fk_race_has_user_race1_idx` (`race_idrace` ASC) VISIBLE,
  CONSTRAINT `fk_race_has_user_race1`
    FOREIGN KEY (`race_idrace`)
    REFERENCES `cgnst`.`race` (`idrace`),
  CONSTRAINT `fk_race_has_user_user1`
    FOREIGN KEY (`user_iduser`)
    REFERENCES `cgnst`.`user` (`iduser`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `cgnst`.`trackRecords`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cgnst`.`trackRecords` ;

CREATE TABLE IF NOT EXISTS `cgnst`.`trackRecords` (
  `idbestTime` INT NOT NULL AUTO_INCREMENT,
  `time` INT NOT NULL,
  `user_iduser` INT NOT NULL,
  `discipline_iddiscipline` INT NOT NULL,
  PRIMARY KEY (`idbestTime`),
  INDEX `fk_trackRecords_user1_idx` (`user_iduser` ASC) VISIBLE,
  INDEX `fk_trackRecords_discipline1_idx` (`discipline_iddiscipline` ASC) VISIBLE,
  CONSTRAINT `fk_trackRecords_discipline1`
    FOREIGN KEY (`discipline_iddiscipline`)
    REFERENCES `cgnst`.`discipline` (`iddiscipline`),
  CONSTRAINT `fk_trackRecords_user1`
    FOREIGN KEY (`user_iduser`)
    REFERENCES `cgnst`.`user` (`iduser`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `cgnst`.`trainingsBlueprint`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cgnst`.`trainingsBlueprint` ;

CREATE TABLE IF NOT EXISTS `cgnst`.`trainingsBlueprint` (
  `idtrainingsBlueprint` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `creator` INT NOT NULL,
  PRIMARY KEY (`idtrainingsBlueprint`),
  INDEX `fk_trainingsBlueprint_user1_idx` (`creator` ASC) VISIBLE,
  CONSTRAINT `fk_trainingsBlueprint_user1`
    FOREIGN KEY (`creator`)
    REFERENCES `cgnst`.`user` (`iduser`))
ENGINE = InnoDB
AUTO_INCREMENT = 37
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `cgnst`.`training`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cgnst`.`training` ;

CREATE TABLE IF NOT EXISTS `cgnst`.`training` (
  `idtraining` INT NOT NULL AUTO_INCREMENT,
  `comment` TEXT NULL DEFAULT NULL,
  `distance` INT NULL DEFAULT NULL,
  `avgHeartFrequency` INT NULL DEFAULT NULL,
  `intensity` DOUBLE NULL DEFAULT NULL,
  `trainingsBlueprint_idtrainingsBlueprint1` INT NULL DEFAULT NULL,
  `trainingFacility_idtrainingFacility` INT NULL DEFAULT NULL,
  `uploadUser` INT NOT NULL,
  `validated` TINYINT NULL DEFAULT '0',
  `startDate` DATETIME NULL DEFAULT NULL,
  `endDate` DATETIME NULL DEFAULT NULL,
  `name` VARCHAR(500) NULL DEFAULT 'Unbenannt',
  PRIMARY KEY (`idtraining`),
  INDEX `fk_trainingDone_trainingsBlueprint1_idx` (`trainingsBlueprint_idtrainingsBlueprint1` ASC) VISIBLE,
  INDEX `fk_trainingDone_trainingFacility1_idx` (`trainingFacility_idtrainingFacility` ASC) VISIBLE,
  INDEX `fk_trainingDone_user2_idx` (`uploadUser` ASC) VISIBLE,
  CONSTRAINT `fk_trainingDone_trainingFacility1`
    FOREIGN KEY (`trainingFacility_idtrainingFacility`)
    REFERENCES `cgnst`.`trainingFacility` (`idtrainingFacility`),
  CONSTRAINT `fk_trainingDone_trainingsBlueprint1`
    FOREIGN KEY (`trainingsBlueprint_idtrainingsBlueprint1`)
    REFERENCES `cgnst`.`trainingsBlueprint` (`idtrainingsBlueprint`),
  CONSTRAINT `fk_trainingDone_user2`
    FOREIGN KEY (`uploadUser`)
    REFERENCES `cgnst`.`user` (`iduser`))
ENGINE = InnoDB
AUTO_INCREMENT = 55
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `cgnst`.`trainingFacility_has_trackRecords`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cgnst`.`trainingFacility_has_trackRecords` ;

CREATE TABLE IF NOT EXISTS `cgnst`.`trainingFacility_has_trackRecords` (
  `trainingFacility_idtrainingFacility` INT NOT NULL,
  `trackRecords_idbestTime` INT NOT NULL,
  PRIMARY KEY (`trainingFacility_idtrainingFacility`, `trackRecords_idbestTime`),
  INDEX `fk_trainingFacility_has_trackRecords_trackRecords1_idx` (`trackRecords_idbestTime` ASC) VISIBLE,
  INDEX `fk_trainingFacility_has_trackRecords_trainingFacility1_idx` (`trainingFacility_idtrainingFacility` ASC) VISIBLE,
  CONSTRAINT `fk_trainingFacility_has_trackRecords_trackRecords1`
    FOREIGN KEY (`trackRecords_idbestTime`)
    REFERENCES `cgnst`.`trackRecords` (`idbestTime`),
  CONSTRAINT `fk_trainingFacility_has_trackRecords_trainingFacility1`
    FOREIGN KEY (`trainingFacility_idtrainingFacility`)
    REFERENCES `cgnst`.`trainingFacility` (`idtrainingFacility`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `cgnst`.`training_has_athletes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cgnst`.`training_has_athletes` ;

CREATE TABLE IF NOT EXISTS `cgnst`.`training_has_athletes` (
  `training_idtraining` INT NOT NULL,
  `user_iduser` INT NOT NULL,
  `participates` TINYINT NOT NULL,
  `comment` VARCHAR(500) NULL DEFAULT NULL,
  PRIMARY KEY (`training_idtraining`, `user_iduser`),
  INDEX `fk_trainingDone_has_user_user1_idx` (`user_iduser` ASC) VISIBLE,
  INDEX `fk_trainingDone_has_user_trainingDone1_idx` (`training_idtraining` ASC) VISIBLE,
  CONSTRAINT `fk_trainingDone_has_user_trainingDone1`
    FOREIGN KEY (`training_idtraining`)
    REFERENCES `cgnst`.`training` (`idtraining`),
  CONSTRAINT `fk_trainingDone_has_user_user1`
    FOREIGN KEY (`user_iduser`)
    REFERENCES `cgnst`.`user` (`iduser`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `cgnst`.`training_has_group`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cgnst`.`training_has_group` ;

CREATE TABLE IF NOT EXISTS `cgnst`.`training_has_group` (
  `training_idtraining` INT NOT NULL,
  `group_idgroup` INT NOT NULL,
  PRIMARY KEY (`training_idtraining`, `group_idgroup`),
  INDEX `fk_training_has_group_group1_idx` (`group_idgroup` ASC) VISIBLE,
  INDEX `fk_training_has_group_training1_idx` (`training_idtraining` ASC) VISIBLE,
  CONSTRAINT `fk_training_has_group_group1`
    FOREIGN KEY (`group_idgroup`)
    REFERENCES `cgnst`.`group` (`idgroup`),
  CONSTRAINT `fk_training_has_group_training1`
    FOREIGN KEY (`training_idtraining`)
    REFERENCES `cgnst`.`training` (`idtraining`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `cgnst`.`training_has_group_has_user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cgnst`.`training_has_group_has_user` ;

CREATE TABLE IF NOT EXISTS `cgnst`.`training_has_group_has_user` (
  `training_idtraining` INT NOT NULL,
  `group_has_user_group_idgroup` INT NOT NULL,
  `group_has_user_user_iduser` INT NOT NULL,
  PRIMARY KEY (`training_idtraining`, `group_has_user_group_idgroup`, `group_has_user_user_iduser`),
  INDEX `fk_training_has_group_has_user_group_has_user1_idx` (`group_has_user_group_idgroup` ASC, `group_has_user_user_iduser` ASC) VISIBLE,
  INDEX `fk_training_has_group_has_user_training1_idx` (`training_idtraining` ASC) VISIBLE,
  CONSTRAINT `fk_training_has_group_has_user_group_has_user1`
    FOREIGN KEY (`group_has_user_group_idgroup` , `group_has_user_user_iduser`)
    REFERENCES `cgnst`.`group_has_user` (`group_idgroup` , `user_iduser`),
  CONSTRAINT `fk_training_has_group_has_user_training1`
    FOREIGN KEY (`training_idtraining`)
    REFERENCES `cgnst`.`training` (`idtraining`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `cgnst`.`training_has_trainer`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cgnst`.`training_has_trainer` ;

CREATE TABLE IF NOT EXISTS `cgnst`.`training_has_trainer` (
  `training_idtraining` INT NOT NULL,
  `user_iduser` INT NOT NULL,
  PRIMARY KEY (`training_idtraining`, `user_iduser`),
  INDEX `fk_training_has_user_user1_idx` (`user_iduser` ASC) VISIBLE,
  INDEX `fk_training_has_user_training1_idx` (`training_idtraining` ASC) VISIBLE,
  CONSTRAINT `fk_training_has_user_training1`
    FOREIGN KEY (`training_idtraining`)
    REFERENCES `cgnst`.`training` (`idtraining`),
  CONSTRAINT `fk_training_has_user_user1`
    FOREIGN KEY (`user_iduser`)
    REFERENCES `cgnst`.`user` (`iduser`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `cgnst`.`trainingsBlueprint_has_exerciseGroup`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cgnst`.`trainingsBlueprint_has_exerciseGroup` ;

CREATE TABLE IF NOT EXISTS `cgnst`.`trainingsBlueprint_has_exerciseGroup` (
  `trainingsBlueprint_idtrainingsBlueprint` INT NOT NULL,
  `exerciseGroup_idexerciseGroup` INT NOT NULL,
  PRIMARY KEY (`trainingsBlueprint_idtrainingsBlueprint`, `exerciseGroup_idexerciseGroup`),
  INDEX `fk_trainingsBlueprint_has_exerciseGroup_exerciseGroup1_idx` (`exerciseGroup_idexerciseGroup` ASC) VISIBLE,
  INDEX `fk_trainingsBlueprint_has_exerciseGroup_trainingsBlueprint1_idx` (`trainingsBlueprint_idtrainingsBlueprint` ASC) VISIBLE,
  CONSTRAINT `fk_trainingsBlueprint_has_exerciseGroup_exerciseGroup1`
    FOREIGN KEY (`exerciseGroup_idexerciseGroup`)
    REFERENCES `cgnst`.`exerciseGroup` (`idexerciseGroup`),
  CONSTRAINT `fk_trainingsBlueprint_has_exerciseGroup_trainingsBlueprint1`
    FOREIGN KEY (`trainingsBlueprint_idtrainingsBlueprint`)
    REFERENCES `cgnst`.`trainingsBlueprint` (`idtrainingsBlueprint`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `cgnst`.`trainingsCamp`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cgnst`.`trainingsCamp` ;

CREATE TABLE IF NOT EXISTS `cgnst`.`trainingsCamp` (
  `idtrainingsCamp` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(200) NULL DEFAULT NULL,
  `startDate` DATE NULL DEFAULT NULL,
  `endDate` DATE NOT NULL,
  `descriptionHtml` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`idtrainingsCamp`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `cgnst`.`trainingsCamp_has_training`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cgnst`.`trainingsCamp_has_training` ;

CREATE TABLE IF NOT EXISTS `cgnst`.`trainingsCamp_has_training` (
  `trainingsCamp_idtrainingsCamp` INT NOT NULL,
  `training_idtraining` INT NOT NULL,
  PRIMARY KEY (`trainingsCamp_idtrainingsCamp`, `training_idtraining`),
  INDEX `fk_trainingsCamp_has_training_training1_idx` (`training_idtraining` ASC) VISIBLE,
  INDEX `fk_trainingsCamp_has_training_trainingsCamp1_idx` (`trainingsCamp_idtrainingsCamp` ASC) VISIBLE,
  CONSTRAINT `fk_trainingsCamp_has_training_training1`
    FOREIGN KEY (`training_idtraining`)
    REFERENCES `cgnst`.`training` (`idtraining`),
  CONSTRAINT `fk_trainingsCamp_has_training_trainingsCamp1`
    FOREIGN KEY (`trainingsCamp_idtrainingsCamp`)
    REFERENCES `cgnst`.`trainingsCamp` (`idtrainingsCamp`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `cgnst`.`trainingsCamp_has_user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cgnst`.`trainingsCamp_has_user` ;

CREATE TABLE IF NOT EXISTS `cgnst`.`trainingsCamp_has_user` (
  `trainingsCamp_idtrainingsCamp` INT NOT NULL,
  `user_iduser` INT NOT NULL,
  `participating` TINYINT NOT NULL,
  `comment` VARCHAR(200) NULL DEFAULT NULL,
  PRIMARY KEY (`trainingsCamp_idtrainingsCamp`, `user_iduser`),
  INDEX `fk_trainingsCamp_has_user_user1_idx` (`user_iduser` ASC) VISIBLE,
  INDEX `fk_trainingsCamp_has_user_trainingsCamp1_idx` (`trainingsCamp_idtrainingsCamp` ASC) VISIBLE,
  CONSTRAINT `fk_trainingsCamp_has_user_trainingsCamp1`
    FOREIGN KEY (`trainingsCamp_idtrainingsCamp`)
    REFERENCES `cgnst`.`trainingsCamp` (`idtrainingsCamp`),
  CONSTRAINT `fk_trainingsCamp_has_user_user1`
    FOREIGN KEY (`user_iduser`)
    REFERENCES `cgnst`.`user` (`iduser`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `cgnst`.`trainingsGroup`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cgnst`.`trainingsGroup` ;

CREATE TABLE IF NOT EXISTS `cgnst`.`trainingsGroup` (
  `idtrainingsgroup` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`idtrainingsgroup`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `cgnst`.`trainingsGroup_has_user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cgnst`.`trainingsGroup_has_user` ;

CREATE TABLE IF NOT EXISTS `cgnst`.`trainingsGroup_has_user` (
  `trainingsGroup_idtraininGsgroup` INT NOT NULL,
  `user_iduser` INT NOT NULL,
  PRIMARY KEY (`trainingsGroup_idtraininGsgroup`, `user_iduser`),
  INDEX `fk_trainingsgroup_has_user_user1_idx` (`user_iduser` ASC) VISIBLE,
  INDEX `fk_trainingsgroup_has_user_trainingsgroup1_idx` (`trainingsGroup_idtraininGsgroup` ASC) VISIBLE,
  CONSTRAINT `fk_trainingsgroup_has_user_trainingsgroup1`
    FOREIGN KEY (`trainingsGroup_idtraininGsgroup`)
    REFERENCES `cgnst`.`trainingsGroup` (`idtrainingsgroup`),
  CONSTRAINT `fk_trainingsgroup_has_user_user1`
    FOREIGN KEY (`user_iduser`)
    REFERENCES `cgnst`.`user` (`iduser`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `cgnst`.`users_are_friends`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cgnst`.`users_are_friends` ;

CREATE TABLE IF NOT EXISTS `cgnst`.`users_are_friends` (
  `user_iduser` INT NOT NULL,
  `user_iduser1` INT NOT NULL,
  PRIMARY KEY (`user_iduser`, `user_iduser1`),
  INDEX `fk_user_has_user_user2_idx` (`user_iduser1` ASC) VISIBLE,
  INDEX `fk_user_has_user_user1_idx` (`user_iduser` ASC) VISIBLE,
  CONSTRAINT `fk_user_has_user_user1`
    FOREIGN KEY (`user_iduser`)
    REFERENCES `cgnst`.`user` (`iduser`),
  CONSTRAINT `fk_user_has_user_user2`
    FOREIGN KEY (`user_iduser1`)
    REFERENCES `cgnst`.`user` (`iduser`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- GHG Schema
USE [ghgdb];

IF EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = N'Scenario')
	DROP TABLE dbo.[Scenario];
IF EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = N'Distance')
	DROP TABLE dbo.[Distance];
IF EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = N'SourceByDest')
	DROP TABLE dbo.[SourceByDest];
IF EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = N'SourceByComp')
	DROP TABLE dbo.[SourceByComp];
IF EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = N'DestByComp')
	DROP TABLE dbo.[DestByComp];
IF EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = N'CO2EmissionFactor')
	DROP TABLE dbo.[CO2EmissionFactor];
IF EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = N'ElectConRate')
	DROP TABLE dbo.[ElectConRate];
IF EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = N'ElectEmissionFactor')
	DROP TABLE dbo.[ElectEmissionFactor];
IF EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = N'ElectEmissionOption')
	DROP TABLE dbo.[ElectEmissionOption];
IF EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = N'Source')
	DROP TABLE dbo.[Source];
IF EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = N'Destination')
	DROP TABLE dbo.[Destination];
IF EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = N'Composition')
	DROP TABLE dbo.[Composition];
IF EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = N'Vehicle')
	DROP TABLE dbo.[Vehicle];
IF EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = N'User')
	DROP TABLE dbo.[User];
IF EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = N'Policy')
	DROP TABLE dbo.[Policy];

CREATE TABLE dbo.[User] (
	userId		INT			NOT NULL	IDENTITY(1,1),
	email		VARCHAR(30)	NOT NULL,
	passwd		VARCHAR(32)	NOT NULL,
	firstName	VARCHAR(30)	NOT NULL,
	lastName	VARCHAR(30)	NOT NULL,
	phone		VARCHAR(20)	NOT NULL,
	address		VARCHAR(70)	NOT NULL,
	level		TINYINT		DEFAULT 1,
	isValid		BIT			DEFAULT 1,
	PRIMARY KEY	(userId)
);

CREATE TABLE dbo.[Policy] (
	policyNumber	INT			NOT NULL	IDENTITY(1,1),
	worksheetName	VARCHAR(30)	NOT NULL,
	scenarioYear	CHAR(4)		NOT NULL,
	description		TEXT,
	isRun			BIT			DEFAULT 1,
	errorCheck		BIT			DEFAULT 0,
	PRIMARY KEY (policyNumber)
);

CREATE TABLE dbo.[Source] (
	sourceId	TINYINT		NOT NULL,
	sourceName	VARCHAR(30)	NOT NULL,
	PRIMARY KEY (sourceId)
);

CREATE TABLE dbo.[Destination] (
	destinationId		TINYINT		NOT NULL,
	destinationName		VARCHAR(30)	NOT NULL,
	distance			INT,
	PRIMARY KEY (destinationId)
);

CREATE TABLE dbo.[Composition] (
	compositionId	TINYINT	NOT NULL,
	compositionName	VARCHAR(30)	NOT NULL,
	PRIMARY KEY (compositionId)
);

CREATE TABLE dbo.[Vehicle] (
	modelId	TINYINT		NOT NULL,
	model				VARCHAR(10) NOT NULL,
	tonnage				INT			NOT NULL,
	emissionFactor		DECIMAL(5,3),
	emissionFactorTonne DECIMAL(3,2),
	PRIMARY KEY (modelId)
);

CREATE TABLE dbo.[ElectEmissionFactor] (
	emissionFactor	DECIMAL(5,4)
);

CREATE TABLE dbo.[ElectEmissionOption] (
	emissionOption	VARCHAR(30)	NOT NULL,
	emissionFactor	DECIMAL(5,4),
	PRIMARY KEY (emissionOption)
);

CREATE TABLE dbo.[Scenario] (
	scenarioId		INT		NOT NULL	IDENTITY(1,1),
	userId			INT		NOT NULL,
	policyNumber	INT		NOT NULL,
	PRIMARY KEY (scenarioId),
	FOREIGN KEY (userId) REFERENCES dbo.[User](userId)	ON DELETE CASCADE,
	FOREIGN KEY (policyNumber) REFERENCES dbo.[Policy](policyNumber)	ON DELETE CASCADE
);

CREATE TABLE dbo.[CO2EmissionFactor] (
	compositionId		TINYINT		NOT NULL,
	destinationId		TINYINT		NOT NULL,
	emissionFactor		DECIMAL(6,1),
	FOREIGN KEY (compositionId) REFERENCES dbo.[Composition](compositionId)	ON DELETE CASCADE,
	FOREIGN KEY (destinationId) REFERENCES dbo.[Destination](destinationId)	ON DELETE CASCADE
);

CREATE TABLE dbo.[ElectConRate] (
	destinationId		TINYINT		NOT NULL,
	consumptionRate		INT,
	FOREIGN KEY (destinationId) REFERENCES dbo.[Destination](destinationId)	ON DELETE CASCADE
);

CREATE TABLE dbo.[SourceByDest] (
	sourceId		TINYINT		NOT NULL,
	destinationId	TINYINT		NOT NULL,
	scenarioYear	CHAR(4)		NOT NULL,
	tonnage			INT			NOT NULL,
	vehicleModelId	TINYINT		NOT NULL,
	transferPercent	VARCHAR(10),
	FOREIGN KEY (sourceId) REFERENCES dbo.[Source](sourceId)	ON DELETE CASCADE,
	FOREIGN KEY (destinationId) REFERENCES dbo.[Destination](destinationId)	ON DELETE CASCADE,
	FOREIGN KEY (vehicleModelId) REFERENCES dbo.[Vehicle](modelId)	ON DELETE CASCADE
);

CREATE TABLE dbo.[SourceByComp] (
	sourceId		TINYINT		NOT NULL,
	compositionId	TINYINT		NOT NULL,
	scenarioYear	CHAR(4)		NOT NULL,
	tonnage			INT			NOT NULL,
	FOREIGN KEY (sourceId) REFERENCES dbo.[Source](sourceId)	ON DELETE CASCADE,
	FOREIGN KEY (compositionId) REFERENCES dbo.[Composition](compositionId)	ON DELETE CASCADE
);

CREATE TABLE dbo.[DestByComp] (
	destinationId	TINYINT		NOT NULL,
	compositionId	TINYINT		NOT NULL,
	scenarioYear	CHAR(4)		NOT NULL,
	tonnage			INT			NOT NULL,
	FOREIGN KEY (destinationId) REFERENCES dbo.[Destination](destinationId)	ON DELETE CASCADE,
	FOREIGN KEY (compositionId) REFERENCES dbo.[Composition](compositionId)	ON DELETE CASCADE
);

-- GHG Data
INSERT INTO dbo.[User](email, passwd, firstName, lastName, phone, address, level)
VALUES('admin_ghg@gmail.com','5f4dcc3b5aa765d61d8327deb882cf99','admin','admin','6041234567','515 Ontario St, Vancouver, BC V4X 1A7', 2);
INSERT INTO dbo.[User](email, passwd, firstName, lastName, phone, address)
VALUES('test_ghg@gmail.com','5f4dcc3b5aa765d61d8327deb882cf99','test','test','6041234568','515 Ontario St, Vancouver, BC V4X 1A7');

INSERT INTO dbo.[Policy](worksheetName, scenarioYear, description)
VALUES('TV-P1','2015','Committed Policies: All policies that are underway');
INSERT INTO dbo.[Policy](worksheetName, scenarioYear, description)
VALUES('TV-P2','2015','Uncommitted Policies: Existing policies and targets lacking detailed implementation or financial plans');
INSERT INTO dbo.[Policy](worksheetName, scenarioYear, description)
VALUES('TV-P3','2015','Potential Policy and Technology Futures: Speculative policies');

INSERT INTO dbo.[Source](sourceId, sourceName) VALUES(1,'Single Family');
INSERT INTO dbo.[Source](sourceId, sourceName) VALUES(2,'Organic');
INSERT INTO dbo.[Source](sourceId, sourceName) VALUES(3,'Non-Organic');
INSERT INTO dbo.[Source](sourceId, sourceName) VALUES(4,'Multi Family');
INSERT INTO dbo.[Source](sourceId, sourceName) VALUES(5,'Commercial(ICI)');
INSERT INTO dbo.[Source](sourceId, sourceName) VALUES(6,'DLC');
INSERT INTO dbo.[Source](sourceId, sourceName) VALUES(7,'Farm');

INSERT INTO dbo.[Destination](destinationId, destinationName, distance) VALUES(1,'Vancouver Landfill',25);
INSERT INTO dbo.[Destination](destinationId, destinationName, distance) VALUES(2,'Cache Creek Landfill',250);
INSERT INTO dbo.[Destination](destinationId, destinationName, distance) VALUES(3,'Burnaby WtE',20);
INSERT INTO dbo.[Destination](destinationId, destinationName, distance) VALUES(4,'Richmond Harvest Power',20);
INSERT INTO dbo.[Destination](destinationId, destinationName, distance) VALUES(5,'New Technology',20);
INSERT INTO dbo.[Destination](destinationId, destinationName, distance) VALUES(6,'Leakage',200);

INSERT INTO dbo.[Composition](compositionId, compositionName) VALUES(1,'Organic');
INSERT INTO dbo.[Composition](compositionId, compositionName) VALUES(2,'Paper');
INSERT INTO dbo.[Composition](compositionId, compositionName) VALUES(3,'Wood');
INSERT INTO dbo.[Composition](compositionId, compositionName) VALUES(4,'Plastic');
INSERT INTO dbo.[Composition](compositionId, compositionName) VALUES(5,'Other');

INSERT INTO dbo.[Vehicle](modelId, model, tonnage, emissionFactor, emissionFactorTonne) 
VALUES(1,'Truck A',5,3.221,0.64);
INSERT INTO dbo.[Vehicle](modelId, model, tonnage, emissionFactor, emissionFactorTonne) 
VALUES(2,'Truck B',10,3.865,0.39);
INSERT INTO dbo.[Vehicle](modelId, model, tonnage, emissionFactor, emissionFactorTonne) 
VALUES(3,'Truck C',15,4.639,0.31);
INSERT INTO dbo.[Vehicle](modelId, model, tonnage, emissionFactor, emissionFactorTonne) 
VALUES(4,'Truck D',20,5.566,0.28);

INSERT INTO dbo.[ElectEmissionFactor](emissionFactor) VALUES(0.0545);
INSERT INTO dbo.[ElectEmissionOption](emissionOption, emissionFactor) VALUES('BCMoF(2012)',0.025);
INSERT INTO dbo.[ElectEmissionOption](emissionOption, emissionFactor) VALUES('Dowlatabadi(2011)',0.084);
INSERT INTO dbo.[ElectEmissionOption](emissionOption, emissionFactor) VALUES('Average',0.0545);

INSERT INTO dbo.[CO2EmissionFactor](compositionId, destinationId, emissionFactor) VALUES(1,1,570);
INSERT INTO dbo.[CO2EmissionFactor](compositionId, destinationId, emissionFactor) VALUES(1,2,570);
INSERT INTO dbo.[CO2EmissionFactor](compositionId, destinationId, emissionFactor) VALUES(1,3,21);
INSERT INTO dbo.[CO2EmissionFactor](compositionId, destinationId, emissionFactor) VALUES(1,4,6);
INSERT INTO dbo.[CO2EmissionFactor](compositionId, destinationId, emissionFactor) VALUES(1,5,6);
INSERT INTO dbo.[CO2EmissionFactor](compositionId, destinationId, emissionFactor) VALUES(1,6,570);

INSERT INTO dbo.[CO2EmissionFactor](compositionId, destinationId, emissionFactor) VALUES(2,1,553);
INSERT INTO dbo.[CO2EmissionFactor](compositionId, destinationId, emissionFactor) VALUES(2,2,553);
INSERT INTO dbo.[CO2EmissionFactor](compositionId, destinationId, emissionFactor) VALUES(2,3,21);
INSERT INTO dbo.[CO2EmissionFactor](compositionId, destinationId, emissionFactor) VALUES(2,4,21);
INSERT INTO dbo.[CO2EmissionFactor](compositionId, destinationId, emissionFactor) VALUES(2,5,21);
INSERT INTO dbo.[CO2EmissionFactor](compositionId, destinationId, emissionFactor) VALUES(2,6,553);

INSERT INTO dbo.[CO2EmissionFactor](compositionId, destinationId, emissionFactor) VALUES(3,1,851);
INSERT INTO dbo.[CO2EmissionFactor](compositionId, destinationId, emissionFactor) VALUES(3,2,851);
INSERT INTO dbo.[CO2EmissionFactor](compositionId, destinationId, emissionFactor) VALUES(3,3,21);
INSERT INTO dbo.[CO2EmissionFactor](compositionId, destinationId, emissionFactor) VALUES(3,4,21);
INSERT INTO dbo.[CO2EmissionFactor](compositionId, destinationId, emissionFactor) VALUES(3,5,21);
INSERT INTO dbo.[CO2EmissionFactor](compositionId, destinationId, emissionFactor) VALUES(3,6,851);

INSERT INTO dbo.[CO2EmissionFactor](compositionId, destinationId, emissionFactor) VALUES(4,1,34);
INSERT INTO dbo.[CO2EmissionFactor](compositionId, destinationId, emissionFactor) VALUES(4,2,34);
INSERT INTO dbo.[CO2EmissionFactor](compositionId, destinationId, emissionFactor) VALUES(4,3,21);
INSERT INTO dbo.[CO2EmissionFactor](compositionId, destinationId, emissionFactor) VALUES(4,4,0.1);
INSERT INTO dbo.[CO2EmissionFactor](compositionId, destinationId, emissionFactor) VALUES(4,5,0.1);
INSERT INTO dbo.[CO2EmissionFactor](compositionId, destinationId, emissionFactor) VALUES(4,6,34);

INSERT INTO dbo.[CO2EmissionFactor](compositionId, destinationId, emissionFactor) VALUES(5,1,290);
INSERT INTO dbo.[CO2EmissionFactor](compositionId, destinationId, emissionFactor) VALUES(5,2,290);
INSERT INTO dbo.[CO2EmissionFactor](compositionId, destinationId, emissionFactor) VALUES(5,3,21);
INSERT INTO dbo.[CO2EmissionFactor](compositionId, destinationId, emissionFactor) VALUES(5,4,12);
INSERT INTO dbo.[CO2EmissionFactor](compositionId, destinationId, emissionFactor) VALUES(5,5,12);
INSERT INTO dbo.[CO2EmissionFactor](compositionId, destinationId, emissionFactor) VALUES(5,6,290);

INSERT INTO dbo.[ElectConRate](destinationId, consumptionRate) VALUES(1,30);
INSERT INTO dbo.[ElectConRate](destinationId, consumptionRate) VALUES(2,30);
INSERT INTO dbo.[ElectConRate](destinationId, consumptionRate) VALUES(3,0);
INSERT INTO dbo.[ElectConRate](destinationId, consumptionRate) VALUES(4,45);
INSERT INTO dbo.[ElectConRate](destinationId, consumptionRate) VALUES(5,45);
INSERT INTO dbo.[ElectConRate](destinationId, consumptionRate) VALUES(6,0);

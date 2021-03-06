CREATE TABLE IF NOT EXISTS `Users` (
	`SSN` INT(9) NOT NULL
	`FirstName` VARCHAR(100) NOT NULL
	,`LastName` VARCHAR(100) NOT NULL
	, `PhoneNo` VARCHAR(100)
	,`PBAVerified` VARCHAR(100) NOT NULL
	,`BankId` VARCHAR(100)
	,`BANumber` VARCHAR(100)
	, FOREIGN KEY (BankID, BANumber) REFERENCES BANK_ACCOUNT (Bankid, BANumber)
	, FOREIGN KEY (PhoneNo) REFERENCES ELECTRONIC_ADDRESS (Identifier)
	,PRIMARY KEY (`SSN`)
	);

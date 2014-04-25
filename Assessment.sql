CREATE TABLE Assessments (
      Test_ID INT NOT NULL AUTO_INCREMENT, 
	Test_Date char(30) default NULL,
	user varchar(50) default NULL,
      Test_Type varchar(50),
      List_of_LO tinyblob,
      Test_txt mediumblob,
      PRIMARY KEY (Test_ID)
) /*$wgDBTableOptions*/;


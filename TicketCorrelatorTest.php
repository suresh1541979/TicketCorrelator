<?php

class TicketGeneratorTest extends PHPUnit_Framework_TestCase
{
	# File Existence check
	public  function testInputFileExist()
	{
		$this->assertFileExists('./JobStatus.txt');
	}
	
	public function testOutputFileExist()
	{
		$this->assertFileExists('./TicketReport.txt');
	}
	
	# Empty file check
	public function testInputFileEmpty()
	{
		$filename = "./JobStatus.txt";
		$myfile = fopen($filename, "r");
		$mydata = fgets($myfile);
		fclose($myfile);
		$this->assertNotEmpty($mydata, "File $filename is empty.");
	}
	
	# Class Object creation and backup data/record count check
	public function testBackupDataCount()
	{
		$myobj = new TicketCorrelator;
		$myobj->printHeader();
		$this->assertGreaterThanOrEqual(10, $myobj->RECORD_COUNT, "Minimum 10 records should be available in Backup file");
	}
}
?>

<?php

class TicketCorrelator
{
  public $bgcolor    = 'bgcolor=white';
  public $HEADSTYLE  = "style='background-color: #EE9292;'";
  public $BGSTYLE    = "style='background-color: white;'"; 
  public $ALTSTYLE   = "style='background-color: #F0C1C1;'"; 
  public $TITLESTYLE = "color='red'";
  public $RECORD_COUNT = 0;
  public $BACKUP_FILE = "./JobStatus.txt";
  public $TICKET_FILE = "./TicketReport.txt";

  function printHeader()
  {
    $FILENAME = basename(__FILE__);
    echo <<<HTML
<html>
<body $this->bgcolor>
<head>
 <style type="text/css"> 
 body {font:10pt Arial,Helvetica,sans-serif; }
 table,tr,td {font:10pt Arial,Helvetica,sans-serif; }
 th {font:bold 12pt Arial,Helvetica,sans-serif; }
 </style>
 <link rel="stylesheet" href="styles.css">
</head>
<div id='cssmenu'>
<ul>
   <li class='active'><a href='$FILENAME?TYPE=SHOW_HOME'><span>Home</span></a></li>
   <li class='active'><a href='$FILENAME'><span>Job Status</span></a></li>
   <li class='active'><a href='$FILENAME?TYPE=SHOW_HISTORY'><span>Ticket Report</span></a></li>
   <li class='active'><a href='$FILENAME?TYPE=SHOW_CONTACT'><span>Contact</span></a></li>
</ul>
</div>
<center><br>
HTML;

    if (isset($_POST['Generate_Ticket'])) { $this->showTicketData(); }
    else {
      if (isset($_REQUEST['TYPE']) && $_REQUEST['TYPE'] == 'SHOW_HISTORY') {
        if (isset($_REQUEST['Clear_History'])) { $myfile= fopen($this->TICKET_FILE, "w") or die("Unable to open file for reading"); fclose($myfile); }
        $this->showTicketHistory();
      }
      elseif (isset($_REQUEST['TYPE']) && $_REQUEST['TYPE'] == 'SHOW_CONTACT') {
	    $this->showContact();
      }
      elseif (isset($_REQUEST['TYPE']) && $_REQUEST['TYPE'] == 'SHOW_HOME') {
	    $this->showHome();
      }
      else {
        $this->showBackupData();
      }
    }
  }

  function showHome()
  {
	echo <<<HTML
	<br><font size=7 $this->TITLESTYLE ><b>Ticket Correlator<br></b></font>
	<br><font size=6 $this->TITLESTYLE ><b><br>@<br><br>VDS India</b></font><br>
	<img src="https://cloud.githubusercontent.com/assets/13945804/9526123/2d9f1f3a-4d05-11e5-9026-e64bb6ce66c3.jpg">
HTML;
  }

  function showContact()
  {
	echo <<<HTML
	<br><font size=6 align='left' $this->TITLESTYLE ><b>Thank you for visiting us</b></font><br><br><br><br>
	<table border=1 width=25% align=center><tr><th colspan=5 $this->HEADSTYLE><b>Contact Details<br></b></th></tr>
	<tr><td $this->ALTSTYLE>Team Name</td><td $this->ALTSTYLE>Tech Warriors<br></td></tr>
	<tr><td $this->ALTSTYLE>Organization</td><td $this->ALTSTYLE>PDO Cloud<br></td></tr>
	<tr><td $this->ALTSTYLE>Contact</td><td $this->ALTSTYLE><br>Sureshkumar Thanabal<br>Sankarkumar Shanmugagani<br>Manojkumar Rajendran<br><br></td></tr>
HTML;
  }
  
  function showTicketHistory()
  {
    $cnt=0;
    $myfile = fopen($this->TICKET_FILE, "r") or die("Unable to open file for reading");
    echo "<font size=5 $this->TITLESTYLE ><b>Ticket Report</b></font><br><br>";
    echo "<form action='' name='myform' method='post'>";
    echo "<center><table border=1 width=60% align=center><tr><th $this->HEADSTYLE>Ticket Number</th><th $this->HEADSTYLE>Node name</th><th $this->HEADSTYLE>Policy</th><th $this->HEADSTYLE>Status</th><th $this->HEADSTYLE>Description</th></tr>";
    while(!feof($myfile)) {
      $data = fgets($myfile);
	  if (!$data) { continue; }
      $result = explode("##", $data);
	  $STYLE = $this->ALTSTYLE; if ($cnt%2 == 0) { $STYLE="$this->BGSTYLE"; } $cnt++;
      echo "<tr><td $STYLE>$result[0]</td><td $STYLE>$result[1]</td><td $STYLE>$result[2]</td><td $STYLE>$result[3]</td><td $STYLE>$result[4]</td></tr>";
    }
    fclose($myfile);
    echo "</table><br><center><input type='submit' name='Clear History' value='Clear History'></center></form>";
  }

  function showTicketData()
  {
	$cnt = 0;
    $DataSet = array();
    $ticket_no = 1001;  
    $myfile = fopen($this->BACKUP_FILE, "r") or die("Unable to open file for reading");
    echo "<font size=5 $this->TITLESTYLE ><b>Customer Backup Jobs Status Dashboard</b></font><br><br>";
    echo "<center><table border=1 width=60% align=center><tr><th $this->HEADSTYLE>Customer</th><th $this->HEADSTYLE>Nodename</th><th $this->HEADSTYLE>Policy</th><th $this->HEADSTYLE>Backup Type</th><th $this->HEADSTYLE>Exit Code</th><th $this->HEADSTYLE>Created Time</th></tr>";
    while(!feof($myfile)) {
      $data = fgets($myfile);
      if (preg_match("/^Nodename##Policy##BackupType##ExitCode##Time/", $data) || preg_match("/^#.*/", $data) ) { continue; }
      $result = explode("##", $data);
      $STYLE = $this->ALTSTYLE; if ($cnt%2 == 0) { $STYLE="$this->BGSTYLE"; } $cnt++;
      echo "<tr><td $STYLE>$result[5]</td><td $STYLE>$result[0]</td><td $STYLE>$result[1]</td><td $STYLE>$result[2]</td><td $STYLE>$result[3]</td><td $STYLE>$result[4]</td></tr>";
      if ($result[3] != 0) {
	    if (isset($DataSet[$result[0]]) && isset($DataSet[$result[0]][$result[1]]) && isset($DataSet[$result[0]][$result[1]][$result[2]])) {
          $DataSet[$result[0]][$result[1]][$result[2]] .= "Node: $result[0]<br>Policy: $result[1]<br>Type: $result[2]<br>Exit Code: $result[3]<br><br>";
	    }
	    else {
          $DataSet[$result[0]][$result[1]][$result[2]] = "Node: $result[0]<br>Policy: $result[1]<br>Type: $result[2]<br>Exit Code: $result[3]<br><br>";
	    }
      }
      else {
        if (isset($DataSet[$result[0]]) && isset($DataSet[$result[0]][$result[1]])) {
          if ($result[2] == "INC") { unset($DataSet[$result[0]][$result[1]][$result[2]]); } else { unset($DataSet[$result[0]][$result[1]]); }
        }
      }
    }
    fclose($myfile);
  
    $TicketData = ''; $cnt=0; $HTMLDATA = '';
    foreach (array_keys($DataSet) as $nodename) {
      foreach (array_keys($DataSet[$nodename]) as $policy) {
        if (empty($DataSet[$nodename][$policy])) { continue; }
	    $STYLE = $this->ALTSTYLE; if ($cnt%2 == 0) { $STYLE="$this->BGSTYLE"; } $cnt++;
        $TicketData .= "<tr><td $STYLE>TKT$ticket_no</td><td $STYLE>";
        $HTMLDATA   .= "TKT$ticket_no##$nodename##$policy##Assigned##";
	    $ticket_no++;
        foreach (array_keys($DataSet[$nodename][$policy]) as $type) {
          $TicketData .= $DataSet[$nodename][$policy][$type];
		  $HTMLDATA .= $DataSet[$nodename][$policy][$type];
        }
	    $TicketData .= "</td></tr>";
	    $HTMLDATA .= "\n";
      }
    }
    $myfile = fopen($this->TICKET_FILE, "a") or die("Unable to open file for writing");
    fwrite($myfile, "$HTMLDATA");
    fclose($myfile);
    if ($TicketData) {
      echo "</table><br><br><table border=1 width=50%><tr><th colspan=5 $this->HEADSTYLE><b>Ticket Details</b></th></tr><tr><th $this->HEADSTYLE>Ticket Number</th><th $this->HEADSTYLE>Description</th></tr>$TicketData</table>";
    }
  }

  function showBackupData()
  {
	$this->drawBackupTable();
    echo "</table><br><center><input type='submit' name='Generate Ticket' value='Correlate Failures'></center></form>";
  }
  
  function drawBackupTable()
  {
    $cnt = 0;
    $myfile = fopen($this->BACKUP_FILE, "r") or die("Unable to open file for reading");
    echo "<font size=5 $this->TITLESTYLE ><b>Customer Backup Jobs Status Dashboard</b></font><br><br> <form action='' name='myform' method='post'>";
    echo "<center><table border=1 width=60% align=center><tr><th $this->HEADSTYLE>Customer</th><th $this->HEADSTYLE>Nodename</th><th $this->HEADSTYLE>Policy</th><th $this->HEADSTYLE>Backup Type</th><th $this->HEADSTYLE>Exit Code</th><th $this->HEADSTYLE>Created Time</th></tr>";
    while(!feof($myfile)) {
      $data = fgets($myfile);
      if (preg_match("/^Nodename##Policy##BackupType##ExitCode##Time/", $data) || preg_match("/^#.*/", $data) || preg_match("/^\n/",$data)) { continue; }
      $result = explode("##", $data);
	  if (count($result) < 6) { continue; }
	  $STYLE = $this->ALTSTYLE; if ($cnt%2 == 0) { $STYLE=$this->BGSTYLE; } $cnt++;
      echo "<tr><td $STYLE>$result[5]</td><td $STYLE>$result[0]</td><td $STYLE>$result[1]</td><td $STYLE>$result[2]</td><td $STYLE>$result[3]</td><td $STYLE>$result[4]</td></tr>";
    }
    fclose($myfile);
    $this->RECORD_COUNT = $cnt;
  }
}
$myclass = new TicketCorrelator;
$myclass->printHeader();
?>

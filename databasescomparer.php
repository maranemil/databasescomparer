<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', false);
ini_set('display_startup_errors', false);

/*

Homepage.............: http://maran-emil.de
Released.............: 25.02.2009
Update...............: 12.09.2013
Created by...........: Emil Maran (maran-emil.de)
Release type.........: Script PHP/mySQL
Price................: Freeware
Version..............: 1.0 Beta
Contact..............: maran_emil@yahoo.com
----------------------------------------------------------------------------------*/

/**
 * @param string $sDB
 *
 * @return array
 */
function buildArrayDB1($sDB) {
   global $link;

   $db_selected = mysql_select_db($sDB, $link);

   $sql       = "SHOW TABLES FROM $sDB";
   $result    = mysql_query($sql);
   $arTotalDB = array();
   while ($row = mysql_fetch_row($result)) {
	  //echo "{$row[0]} <br>";
	  $table                   = $row[0];
	  $arTotalDB[$sDB][$table] = buildArrayFields($table);
   }
   return $arTotalDB;
}

/**
 * @param string $sDB
 *
 * @return array
 */
function buildArrayDB2($sDB) {
   global $link;
   $db_selected = mysql_select_db($sDB, $link);
   $sql         = "SHOW TABLES FROM $sDB";
   $result      = mysql_query($sql);
   $arTotalDB   = array();
   while ($row = mysql_fetch_row($result)) {
	  $table                   = $row[0];
	  $arTotalDB[$sDB][$table] = buildArrayFields($table);
   }
   return $arTotalDB;
}

/**
 * @param string $table
 *
 * @return array
 */
function buildArrayFields($table) {
   $result        = mysql_query("SHOW COLUMNS FROM {$table}");
   $arTableFields = array();
   if (mysql_num_rows($result) > 0) {
	  while ($row = mysql_fetch_assoc($result)) {
		 $arTableFields[] = $row;
	  }
   }
   return $arTableFields;
}

/**
 * @param string $sDB1
 * @param string $sDB2
 */
function compareDB($sDB1, $sDB2) {
   global $arDB1, $arDB2;

   $arDB1 = buildArrayDB1($sDB1);
   $arDB2 = buildArrayDB2($sDB2);

   echo "<B>Compare:<BR> ($sDB1) with ($sDB2)</B> <br><br>";

   if (is_array($arDB1[$sDB1])) {
	  foreach ($arDB1[$sDB1] as $key => $val) {
		 //echo $key."--".print_r($val)."<BR>";
		 echo "<div>";
		 // check if the table name is the identical
		 if ((trim($arDB1[$sDB1][$key])) == (trim($arDB2[$sDB2][$key]))) {
			echo "<span style='color:green'><b>" . $key . "</b> is present in $sDB2 </span><BR>";

			// check if are same number of fields
			if ((count($arDB1[$sDB1][$key])) == (count($arDB2[$sDB2][$key]))) {
			   echo "<B>IDENTICAL</B><BR>";
			}
			else {
			   echo "<span style='color:red'><B>NOT IDENTICAL :: COLUMNS: </B></span><HR>";
			   //compareFields($arDB1[$sDB1][$key]);
			   foreach ($arDB1[$sDB1][$key] as $column) {
				  echo "<B>" . $column ['Field'] . "</B> " . $column ['Type'] . "<br>";
			   }
			   //print '<pre>'; print_r($arDB1[$sDB1][$key]); print '</pre>';
			}
		 }
		 else {
			echo "<span style='color:red'><b>" . $key . "</b> is missing from $sDB2</span><BR>";
		 }

		 echo "</div>";
	  }
   }
}

/**
 * @noinspection PhpUnused
 *
 * @param $table
 */
function compareFields($table) {
   global $arDB1, $arDB2;
// if are not identical show fields
   foreach ($table as $keyf => $valf) {
	  // todo - fix key
	  if ((trim($arDB1[$key][$keyf]['Field'])) === (trim($arDB2[$key][$keyf]['Field']))) {
		 print "<span color='blue'>" . $table[$keyf]['Field'] . " </span><BR>";
	  }
	  else {
		 print "<span color='red'>" . $table[$keyf]['Field'] . " </span><BR>";
	  }
   }
}

/**
 * @return string
 */
function checkConnectionAndDB() {
   global $sDB1, $sDB2, $dbservername, $dbserveruser, $dbserverpass;

   if (
	   (!empty($_POST["sDB1"])) &&
	   (!empty($_POST["sDB2"])) &&
	   (!empty($_POST["dbservername"])) &&
	   (!empty($_POST["dbserveruser"]))
   ) {
	  $sDB1 = $_POST["sDB1"];
	  $sDB2 = $_POST["sDB2"];

	  /* DEFAULT CONNECTION SETTINGS */
	  $dbservername = $_POST["dbservername"];
	  $dbserveruser = $_POST["dbserveruser"];
	  $dbserverpass = $_POST["dbserverpass"];

	  //print_r($_POST);
	  return "valid";
   }
   else {
	  /* DEFAULT DEFINE 2 DB FOR COMPARE */

	  $sDB1 = "bestprice";
	  $sDB2 = "bestpricee";

	  //$sDB1prefix = "";
	  //$sDB2prefix = "";

	  /* DEFAULT CONNECTION SETTINGS */

	  $dbservername = "localhost";
	  $dbserveruser = "root";
	  $dbserverpass = "";

	  return "notvalid";
   }
}

?>

<style>
    td {
        font: 11px tahoma;
        vertical-align: top;
        padding: 10px
    }

    div {
        border: 1px solid #999999;
        padding: 10px
    }
</style>

<TABLE style="padding:10px">
    <TR>
        <TD>
            <form action="" method="post"><!-- <?php echo $_SERVER["REQUEST_URI"] ?> -->
                <label>
                    <input type="text" name="sDB1" value="<?php echo $_POST["sDB1"] ?>">
                </label>&nbsp; DB NAME 1<BR>
                <label>
                    <input type="text" name="sDB2" value="<?php echo $_POST["sDB2"] ?>">
                </label>&nbsp; DB NAME 2<BR>
                <label>
                    <input type="text" name="dbservername" value="<?php echo $_POST["dbservername"] ?>">
                </label>&nbsp; SERVER NAME<BR>
                <label>
                    <input type="text" name="dbserveruser" value="<?php echo $_POST["dbserveruser"] ?>">
                </label>&nbsp; SERVER USERNAME<BR>
                <label>
                    <input type="text" name="dbserverpass" value="<?php echo $_POST["dbserverpass"] ?>">
                </label>&nbsp; SERVER PASSWORD<BR>
                <input type="submit" value="Comapre">
            </form>
        </TD>
        <TD>
		   <?php
		   $valid_data = checkConnectionAndDB();

		   if ($valid_data == "valid") {
			  $dbservername = $_POST["dbservername"];
			  $dbserveruser = $_POST["dbserveruser"];
			  $dbserverpass = $_POST["dbserverpass"];
			  $link         = mysql_connect($dbservername, $dbserveruser, $dbserverpass);
			  $sDB1         = $_POST["sDB1"];
			  $sDB2         = $_POST["sDB2"];
			  compareDB($sDB1, $sDB2);
		   }
		   ?>
        </TD>
        <TD>
		   <?php
		   if ($valid_data == "valid") {
			  compareDB($sDB2, $sDB1);
		   }
		   ?>
        </TD>
    </TR>
</TABLE>

<pre>
<?php
/*
print_r($arDB1);
print_r($arDB2); die();
*/
/*
while(list($key,$val)=each($arTableFields)){
echo $key."--".$val."<BR>";
}
*/
?>
</pre>

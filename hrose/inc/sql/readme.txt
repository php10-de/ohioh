/*** INSTRUCTIONS ***/

The following parameters can be used in the sql files:

***
$sql[]
String
One or more statements to execute
Example:
$sql[] = "DELETE FROM log WHERE dbupdate < '".$date."'";

***
$autoExec
Bool
when set to true it will be automatically executed when the Autoexec Button is pressed.
Can be used with $date parameter
Example:
$autoExec = true;

***
$date
YYYY-MM-DD
Date of file creation. When set the difference between this date and today will be calculated in days
and compared with SQL_AUTOEXEC_DAYS. If larger the file will not be executed.
Used only for $autoExec
Example:
$date = '2017-11-02';

***
$hrose
Array
Execute only for these systems (inc/serial.txt, all: inc/sql/serials.txt) and destroy
file after users defined in the array did execute the sql file. Is automatically executing
Example:
$hrose = array('huQFYjc8jPTvO0OL0G59','WZ7TFaL2GvkFKMfigtSl','kxrnT3OCeJYUbcunzTvL','buuN8ulHi8WRE02AEcPL');

***
$single
Execute only once. Maximum one statement allowed. Only in combination with $hroses
Example:
$single = true;

<?php

header('content-type: text/xml');

function parseToXML($htmlStr) {

       $xmlStr = str_replace('<','&lt;',$htmlStr);

       $xmlStr = str_replace('>','&gt;',$htmlStr);

       $xmlStr = str_replace('"','&quot;',$htmlStr);

       $xmlStr = str_replace("'",'&#39;',$htmlStr);

       $xmlStr = str_replace('&','&amp;',$htmlStr);

return $xmlStr;
}

require_once('config.php');

/* get parameters from URL */
$lat = $_GET['lat'];
$lon = $_GET['lon'];
$radius = $_GET['radius'];

/* opens a connection to a MySQL server */
$db = mysql_connect($host,$username,$password);

if(!$db) {

    die('No connected: '. mysql_error());
}

/* set the active database */
$sel = mysql_select_db($database,$db);

if(!$sel) {

    die('Cannot select database: '. mysql_error());
}


$query = sprintf("SELECT address, name, lat, lon, ( 3959 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lon ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM markers HAVING distance < '%s' ORDER BY distance LIMIT 0 , 20",

         mysql_real_escape_string($lat),

         mysql_real_escape_string($lon),

         mysql_real_escape_string($lat),

         mysql_real_escape_string($radius));

$result = mysql_query($query);

if(!$result) {

    die('Invalid query'. mysql_error());
}

//start XML file, echo parent node

echo"<markers>\n";

//iterate through the rows and printing XML nodes for each

while($row = @mysql_fetch_assoc($result)) {

      //add to XML document NODE

      echo'<marker ';  

      echo'name="'. parseToXML($row['name']) . '" ';

      echo'address="'. parseToXML($row['address']) . '" ';

      echo'lat="'. ($row['lat']) . '" ';

      echo'lon="'. ($row['lon']) . '" ';

      echo'distance = "' .$row['distance'] . '" ';

      echo "/>\n";  
}

//end XML file
echo"</markers>\n";
?>
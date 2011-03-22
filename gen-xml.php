<?php

header("content-type: text/xml");

require_once('config.php');

/* get parameters from URL */
$lat = $_GET['lat'];

$lon = $_GET['lon'];

$radius = $_GET['radius'];


//start XML file, create parent node
$dom = new DOMDocument("1.0");

$node = $dom->createElement("markers");

$parnode = $dom->appendChild($node);


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

//iterate through the rows , adding XML nodes for each

while($row = @mysql_fetch_assoc($result)) {

      //add to XML document NODE
      $node = $dom->createElement("marker");
      $newnode = $parnode->appendChild($node);
      $newnode->setAttribute("name", $row['name']);
      $newnode->setAttribute("address", $row['address']);
      $newnode->setAttribute("lat", $row['lat']);
      $newnode->setAttribute("lon", $row['lon']);
      $newnode->setAttribute("distance", $row['distance']);

}//end while

echo $dom->saveXML();

?>
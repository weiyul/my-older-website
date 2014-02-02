<?php 

function die_with_error($error) {   
    $ret = array(
        "status" => "Failed",
        "error" => $error
        );
    die(json_encode($ret));
}
 



$lat = $_GET["lat"];
$lng = $_GET["lng"];
if (!$lat || !$lng)
    die_with_error("invalid parameters");
 
$hostname = 'localhost';
$username = 'weiyul';
$password = '1234';
$dbname = 'myapp';
 
mysql_connect($hostname, $username, $password) or die_with_error(mysql_error());
mysql_select_db($dbname) or die_with_error(mysql_error());
mysql_set_charset('utf8');
 
$query = sprintf(
        "SELECT *,
            round( 3959 * acos( cos(radians(%s)) * cos(radians(lat)) * cos(radians(lng) - radians(%s)) +
              sin(radians(%s)) * sin(radians(lat)) ), 2 ) AS distance
        FROM `data_Tim`
        HAVING distance < 50
        ORDER BY distance LIMIT 30;",   
        mysql_real_escape_string($lat),     
        mysql_real_escape_string($lng),     
        mysql_real_escape_string($lat)  ); 
$result = mysql_query($query); 
if (! $result)  
    die_with_error(mysql_error()); 
    $result_array = array(); 
    while ($row = mysql_fetch_assoc($result)) {     
    array_push($result_array, array(
            "id" => $row['id'],
            "lat" => $row['lat'],
            "lng" => $row['lng'],
            "address" => $row['address'],
            "distance" => $row['distance']
        ));
}
 
$ret = array(
    "status" => "OK",
    "data" => $result_array
    );
die(json_encode($ret));
?>

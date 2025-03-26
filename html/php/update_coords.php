<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/file_paths-utils.php');
require_once($_SERVER['DOCUMENT_ROOT'] . CONNECT_PARAMS);

header('Content-Type: application/json');

// Database connection
$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$dbh->prepare("SET SCHEMA 'sae';")->execute();
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

// Decode JSON from the request body
$input = json_decode(file_get_contents('php://input'), true);

$id_offre = $input['id_offre'];
$lat      = $input['lat'];
$lon      = $input['lon'];

// Update query
$stmt = $dbh->prepare("UPDATE _offre
                       SET lat = :lat, lon = :lon
                       WHERE id_offre = :id_offre");
print "update\n";                      
$stmt->execute([
  ':lat' => $lat,
  ':lon' => $lon,
  ':id_offre' => $id_offre
]);

echo json_encode(['status' => 'ok']);

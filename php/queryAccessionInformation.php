<?php

include '../../config.php';
include 'pdoResultFilter.php';

$dataset = trim($_GET['Dataset']);

$db = "soykb";
$table = "act_" . $dataset . "_Accession_Mapping";


$query_str = "SELECT * FROM " . $db . "." . $table;


$stmt = $PDO->prepare($query_str);
$stmt->execute();
$result = $stmt->fetchAll();

$result_arr = pdoResultFilter($result);

echo json_encode(array("data" => $result_arr), JSON_INVALID_UTF8_IGNORE);

?>
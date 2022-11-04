<?php

include '../../config.php';
include 'pdoResultFilter.php';
include 'getTableNames.php';

$dataset = trim($_GET['Dataset']);

$db = "soykb";

// Table names and datasets
$table_names = getTableNames($dataset);
$key_column = $table_names["key_column"];
$gff_table = $table_names["gff_table"];
$accession_mapping_table = $table_names["accession_mapping_table"];

// Query gene from database
$query_str = "SELECT DISTINCT Name AS Gene FROM " . $db . "." . $gff_table . " WHERE Name IS NOT NULL LIMIT 3;";

// Perform query
$stmt = $PDO->prepare($query_str);
$stmt->execute();
$result = $stmt->fetchAll();

$gene_result_arr = pdoResultFilter($result);

// Query accession from database
$query_str = "SELECT DISTINCT SoyKB_Accession AS Accession FROM " . $db . "." . $accession_mapping_table;
$query_str = $query_str . " WHERE (SoyKB_Accession IS NOT NULL) AND (SoyKB_Accession LIKE 'HN%') LIMIT 2;";

// Perform query
$stmt = $PDO->prepare($query_str);
$stmt->execute();
$result = $stmt->fetchAll();

$accession_result_arr = pdoResultFilter($result);

$result_arr = [
    "Gene" => $gene_result_arr,
    "Accession" => $accession_result_arr,
];

echo json_encode(array("data" => $result_arr), JSON_INVALID_UTF8_IGNORE);

?>
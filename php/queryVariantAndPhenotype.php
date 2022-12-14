<?php

include '../../config.php';
include 'pdoResultFilter.php';

$chromosome = $_GET['Chromosome'];
$position = $_GET['Position'];
$genotype = $_GET['Genotype'];
$phenotype = $_GET['Phenotype'];
$dataset = $_GET['Dataset'];

if (is_string($genotype)) {
    $genotype_array = preg_split("/[;, \n]+/", $genotype);
    for ($i = 0; $i < count($genotype_array); $i++) {
        $genotype_array[$i] = trim($genotype_array[$i]);
    }
} elseif (is_array($genotype)) {
    $genotype_array = $genotype;
    for ($i = 0; $i < count($genotype_array); $i++) {
        $genotype_array[$i] = trim($genotype_array[$i]);
    }
}

if (is_string($phenotype)) {
    $phenotype_array = preg_split("/[;, \n]+/", $phenotype);
    for ($i = 0; $i < count($phenotype_array); $i++) {
        $phenotype_array[$i] = trim($phenotype_array[$i]);
    }
} elseif (is_array($phenotype)) {
    $phenotype_array = $phenotype;
    for ($i = 0; $i < count($phenotype_array); $i++) {
        $phenotype_array[$i] = trim($phenotype_array[$i]);
    }
}

// Construct query string
$query_str = "SELECT G.Chromosome, G.Position, G.Accession, M.SoyKB_Accession, M.GRIN_Accession, M.Improvement_Status, M.Classification, G.Genotype, ";
$query_str = $query_str . "G.Functional_Effect, G.Imputation ";
for ($i = 0; $i < count($phenotype_array); $i++) {
    $query_str = $query_str . ", PH." . $phenotype_array[$i] . " ";
}
$query_str = $query_str . "FROM soykb.act_" . $dataset . "_" . $chromosome . " AS G ";
$query_str = $query_str . "LEFT JOIN soykb.act_" . $dataset . "_Accession_Mapping AS M ";
$query_str = $query_str . "ON BINARY G.Accession = M.Accession ";
$query_str = $query_str . "LEFT JOIN soykb.germplasm AS PH ";
$query_str = $query_str . "ON BINARY M.GRIN_Accession = PH.ACNO ";
$query_str = $query_str . "WHERE (G.Chromosome = '" . $chromosome . "') ";
$query_str = $query_str . "AND (G.Position = " . $position . ") ";
if (count($genotype_array) > 0) {
    $query_str = $query_str . "AND (G.Genotype IN ('";
    for ($i = 0; $i < count($genotype_array); $i++) {
        if($i < (count($genotype_array)-1)){
            $query_str = $query_str . trim($genotype_array[$i]) . "', '";
        } elseif ($i == (count($genotype_array)-1)) {
            $query_str = $query_str . trim($genotype_array[$i]);
        }
    }
    $query_str = $query_str . "')) ";
}
$query_str = $query_str . "ORDER BY G.Chromosome, G.Position, G.Genotype;";


$stmt = $PDO->prepare($query_str);
$stmt->execute();
$result = $stmt->fetchAll();

$result_arr = pdoResultFilter($result);

echo json_encode(array("data" => $result_arr), JSON_INVALID_UTF8_IGNORE);
?>
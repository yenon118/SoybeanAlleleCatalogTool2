<?php

include '../../config.php';
include 'pdoResultFilter.php';

$dataset = trim($_GET['Dataset']);
$gene = trim($_GET['Gene']);
$accession = $_GET['Accession_Array'];


if (is_string($accession)) {
    $accession_array = preg_split("/[;, \n]+/", $accession);
    for ($i = 0; $i < count($accession_array); $i++) {
        $accession_array[$i] = trim($accession_array[$i]);
    }
} elseif (is_array($accession)) {
    $accession_array = $accession;
    for ($i = 0; $i < count($accession_array); $i++) {
        $accession_array[$i] = trim($accession_array[$i]);
    }
}


$db = "soykb";


// Generate SQL string
$query_str = $query_str . "SELECT Chromosome, Start, End, Name AS Gene ";
$query_str = $query_str . "FROM act_Soybean_Wm82a2v1_GFF ";
$query_str = $query_str . "WHERE Name IN ('" . $gene . "');";

$stmt = $PDO->prepare($query_str);
$stmt->execute();
$result = $stmt->fetchAll();

$gene_result_arr = pdoResultFilter($result);


// Generate SQL string
$query_str = "SELECT ";
$query_str = $query_str . "ACD.Classification, ACD.Improvement_Status, ";
$query_str = $query_str . "ACD.Maturity_Group, ACD.Country, ACD.State, ";
$query_str = $query_str . "ACD.Accession, ACD.SoyKB_Accession, ACD.GRIN_Accession, ";
$query_str = $query_str . "ACD.Gene, ACD.Chromosome, ACD.Position, ACD.Genotype, ACD.Genotype_Description, ACD.Imputation ";
$query_str = $query_str . "FROM ( ";
$query_str = $query_str . "    SELECT AM.Classification, AM.Improvement_Status, ";
$query_str = $query_str . "    AM.Maturity_Group, AM.Country, AM.State, ";
$query_str = $query_str . "    GD.Accession, AM.SoyKB_Accession, AM.GRIN_Accession, ";
$query_str = $query_str . "    GD.Gene, GD.Chromosome, ";
$query_str = $query_str . "    GROUP_CONCAT(GD.Position SEPARATOR ' ') AS Position, ";
$query_str = $query_str . "    GROUP_CONCAT(GD.Genotype SEPARATOR ' ') AS Genotype, ";
$query_str = $query_str . "    GROUP_CONCAT(GD.Genotype_Description SEPARATOR ' ') AS Genotype_Description, ";
$query_str = $query_str . "    GROUP_CONCAT(GD.Imputation SEPARATOR ' ') AS Imputation ";
$query_str = $query_str . "    FROM ( ";
$query_str = $query_str . "        SELECT G.Chromosome, G.Position, G.Accession, GFF.Gene, G.Genotype, ";
$query_str = $query_str . "        CONCAT_WS('|', G.Genotype, G.Functional_Effect, G.Amino_Acid_Change, G.Imputation) AS Genotype_Description, ";
$query_str = $query_str . "        IFNULL(G.Imputation, '-') AS Imputation ";
$query_str = $query_str . "        FROM ( ";
$query_str = $query_str . "            SELECT Chromosome, Start, End, Name AS Gene ";
$query_str = $query_str . "            FROM act_Soybean_Wm82a2v1_GFF ";
$query_str = $query_str . "            WHERE Name IN ('" . $gene . "') ";
$query_str = $query_str . "        ) AS GFF ";
$query_str = $query_str . "        INNER JOIN " . $db . ".act_" . $dataset . "_" . $gene_result_arr[0]["Chromosome"] . " AS G ";
$query_str = $query_str . "        ON (G.Chromosome = GFF.Chromosome) AND (G.Position >= GFF.Start) AND (G.Position <= GFF.End) ";
$query_str = $query_str . "        ORDER BY G.Position ";
$query_str = $query_str . "    ) AS GD ";
$query_str = $query_str . "    LEFT JOIN " . $db . ".act_" . $dataset . "_Accession_Mapping AS AM ";
$query_str = $query_str . "    ON AM.Accession = GD.Accession ";
$query_str = $query_str . "    GROUP BY AM.Classification, AM.Improvement_Status, AM.Maturity_Group, AM.Country, AM.State,GD.Accession, AM.SoyKB_Accession, AM.GRIN_Accession, GD.Gene, GD.Chromosome ";
$query_str = $query_str . ") AS ACD ";
$query_str = $query_str . "WHERE (ACD.SoyKB_Accession IN ('";
for ($i = 0; $i < count($accession_array); $i++) {
    if($i < (count($accession_array)-1)){
        $query_str = $query_str . trim($accession_array[$i]) . "', '";
    } elseif ($i == (count($accession_array)-1)) {
        $query_str = $query_str . trim($accession_array[$i]);
    }
}
$query_str = $query_str . "')) ";
$query_str = $query_str . "ORDER BY ACD.Improvement_Status; ";

$stmt = $PDO->prepare($query_str);
$stmt->execute();
$result = $stmt->fetchAll();

$result_arr = pdoResultFilter($result);


for ($i = 0; $i < count($result_arr); $i++) {
    if (preg_match("/\+/i", $result_arr[$i]["Imputation"])) {
        $result_arr[$i]["Imputation"] = "+";
    } else{
        $result_arr[$i]["Imputation"] = "";
    }
}

echo json_encode(array("data" => $result_arr), JSON_INVALID_UTF8_IGNORE);

?>
<?php

include '../../config.php';
include 'pdoResultFilter.php';

$dataset = trim($_GET['Dataset']);
$key = trim($_GET['Key']);
$gene = trim($_GET['Gene']);
$chromosome = trim($_GET['Chromosome']);
$position = trim($_GET['Position']);
$genotype = trim($_GET['Genotype']);
$genotype_description = trim($_GET['Genotype_Description']);

$db = "soykb";
$table = "act_" . $dataset . "_" . $chromosome;
$accession_mapping_table = "act_" . $dataset . "_Accession_Mapping";


$query_str = "SELECT ";
$query_str = $query_str . "AM.Classification, AM.Improvement_Status, ";
$query_str = $query_str . "AM.Maturity_group, AM.Country, AM.State, ";
$query_str = $query_str . "GD.Accession, AM.SoyKB_Accession, AM.GRIN_Accession, ";
$query_str = $query_str . "GD.Gene, GD.Chromosome, ";
$query_str = $query_str . "GROUP_CONCAT(GD.Position SEPARATOR ' ') AS Position, ";
$query_str = $query_str . "GROUP_CONCAT(GD.Genotype SEPARATOR ' ') AS Genotype, ";
$query_str = $query_str . "GROUP_CONCAT(GD.Genotype SEPARATOR ' ') AS Genotype, ";
$query_str = $query_str . "GROUP_CONCAT(GD.Genotype_Description SEPARATOR ' ') AS Genotype_Description, ";
$query_str = $query_str . "GROUP_CONCAT(GD.Imputation SEPARATOR ' ') AS Imputation ";
$query_str = $query_str . "FROM ( ";
$query_str = $query_str . "    SELECT G.Chromosome, G.Position, G.Accession, GFF.Gene, G.Genotype, ";
$query_str = $query_str . "    CONCAT_WS('|', G.Genotype, G.Functional_Effect, G.Amino_Acid_Change, G.Imputation) AS Genotype_Description, ";
$query_str = $query_str . "    G.Imputation ";
$query_str = $query_str . "    FROM ( ";
$query_str = $query_str . "        SELECT Chromosome, Start, End, Name AS Gene ";
$query_str = $query_str . "        FROM act_Soybean_Wm82a2v1_GFF ";
$query_str = $query_str . "        WHERE Name IN ('" . $gene . "') ";
$query_str = $query_str . "    ) AS GFF ";
$query_str = $query_str . "    INNER JOIN " . $db . "." . $table . " AS G ";
$query_str = $query_str . "    ON (G.Chromosome = GFF.Chromosome) AND (G.Position >= GFF.Start) AND (G.Position <= GFF.End) ";
$query_str = $query_str . "    ORDER BY G.Position ";
$query_str = $query_str . ") AS GD ";
$query_str = $query_str . "LEFT JOIN " . $db . "." . $accession_mapping_table . " AS AM ";
$query_str = $query_str . "ON AM.Accession = GD.Accession ";
$query_str = $query_str . "GROUP BY AM.Classification, AM.Improvement_Status, AM.Maturity_group, AM.Country, AM.State, GD.Accession, AM.SoyKB_Accession, AM.GRIN_Accession, GD.Gene, GD.Chromosome ";
$query_str = $query_str . "HAVING ";
if ($key == "G. soja" || $key == "Landrace" || $key == "Elite" || $key == "Genetic"){
    $query_str = $query_str . "(AM.Improvement_Status = '" . $key . "') AND ";
} elseif ($key == "Cultivar"){
    $query_str = $query_str . "(AM.Classification = 'NA Cultivar') AND ";
}
$query_str = $query_str . "(GD.Gene = '" . $gene . "') AND ";
$query_str = $query_str . "(GD.Chromosome = '" . $chromosome . "') AND ";
$query_str = $query_str . "(Position = '" . $position . "') AND ";
$query_str = $query_str . "(Genotype = '" . $genotype . "'); ";

$stmt = $PDO->prepare($query_str);
$stmt->execute();
$result = $stmt->fetchAll();

$result_arr = pdoResultFilter($result);

echo json_encode(array("data" => $result_arr), JSON_INVALID_UTF8_IGNORE);

?>
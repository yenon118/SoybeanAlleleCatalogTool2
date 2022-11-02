<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

<?php
$TITLE = "Soybean Allele Catalog Tool";

include '../config.php';
include './php/pdoResultFilter.php';
?>

<link rel="stylesheet" href="css/modal.css" />


<!-- Back button -->
<a href="/SoybeanAlleleCatalogTool2/"><button> &lt; Back </button></a>

<br />
<br />


<!-- Get and process the variables -->
<?php
$gene = trim($_GET['gene_2']);
$dataset = trim($_GET['dataset_2']);
$accession = $_GET['accession_2'];

$db = "soykb";

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
?>


<!-- Query data from database and render data-->
<?php
// Color for functional effects
$ref_color_code = "#D1D1D1";
$missense_variant_color_code = "#7FC8F5";
$frameshift_variant_color_code = "#F26A55";
$exon_loss_variant_color_code = "#F26A55";
$lost_color_code = "#F26A55";
$gain_color_code = "#F26A55";
$disruptive_color_code = "#F26A55";
$conservative_color_code = "#FF7F50";
$splice_color_code = "#9EE85C";


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
$query_str = $query_str . "        G.Imputation ";
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
$query_str = $query_str . "WHERE (ACD.Accession IN ('";
for ($i = 0; $i < count($accession_array); $i++) {
    if($i < (count($accession_array)-1)){
        $query_str = $query_str . trim($accession_array[$i]) . "', '";
    } elseif ($i == (count($accession_array)-1)) {
        $query_str = $query_str . trim($accession_array[$i]);
    }
}
$query_str = $query_str . "')) ";
$query_str = $query_str . "OR (ACD.SoyKB_Accession IN ('";
for ($i = 0; $i < count($accession_array); $i++) {
    if($i < (count($accession_array)-1)){
        $query_str = $query_str . trim($accession_array[$i]) . "', '";
    } elseif ($i == (count($accession_array)-1)) {
        $query_str = $query_str . trim($accession_array[$i]);
    }
}
$query_str = $query_str . "')) ";
$query_str = $query_str . "OR (ACD.GRIN_Accession IN ('";
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

// Render result to a table
if(isset($result_arr) && is_array($result_arr) && !empty($result_arr)) {

    // Make table
    echo "<div style='width:100%; height:auto; border:3px solid #000; overflow:scroll; max-height:1000px;'>";
    echo "<table style='text-align:center;'>";

    // Table header
    echo "<tr>";
    foreach ($result_arr[0] as $key => $value) {
        if ($key != "Gene" && $key != "Chromosome" && $key != "Position" && $key != "Genotype" && $key != "Genotype_Description") {
            // Improvement status count section
            echo "<th style=\"border:1px solid black; min-width:80px;\">" . $key . "</th>";
        } elseif ($key == "Gene") {
            echo "<th style=\"border:1px solid black; min-width:80px;\">" . $key . "</th>";
        } elseif ($key == "Chromosome") {
            echo "<th style=\"border:1px solid black; min-width:80px;\">" . $key . "</th>";
        } elseif ($key == "Position") {
            // Position and genotype_description section
            $position_array = preg_split("/[;, \n]+/", $value);
            for ($j = 0; $j < count($position_array); $j++) {
                echo "<th style=\"border:1px solid black; min-width:80px;\">" . $position_array[$j] . "</th>";
            }
        }
    }
    echo "</tr>";

    // Table body
    for ($j = 0; $j < count($result_arr); $j++) {
        $tr_bgcolor = ($j % 2 ? "#FFFFFF" : "#DDFFDD");

        $row_id_prefix = $result_arr[$j]["Gene"] . "_" . $result_arr[$j]["Chromosome"] . "_" . $j;

        echo "<tr bgcolor=\"" . $tr_bgcolor . "\">";

        foreach ($result_arr[$j] as $key => $value) {
            if ($key != "Position" && $key != "Genotype" && $key != "Genotype_Description" && $key != "Imputation") {
                if (intval($value) > 0) {
                    echo "<td style=\"border:1px solid black;min-width:80px;\">";
                    echo $value;
                    echo "</td>";
                } else {
                    echo "<td style=\"border:1px solid black;min-width:80px;\">" . $value . "</td>";
                }
            } elseif ($key == "Genotype_Description") {
                // Position and genotype_description section
                $position_array = preg_split("/[;, \n]+/", $result_arr[$j]["Position"]);
                $genotype_description_array = preg_split("/[;, \n]+/", $value);
                for ($k = 0; $k < count($genotype_description_array); $k++) {

                    // Change genotype_description background color
                    $td_bg_color = "#FFFFFF";
                    if (preg_match("/missense.variant/i", $genotype_description_array[$k])) {
                        $td_bg_color = $missense_variant_color_code;
                        $temp_value_arr = preg_split("/[;, |\n]+/", $genotype_description_array[$k]);
                        if (count($temp_value_arr) > 3) {
                            $genotype_description_array[$k] = $temp_value_arr[0] . "|" . $temp_value_arr[2] . "|" . $temp_value_arr[3];
                        } elseif (count($temp_value_arr) > 2) {
                            $genotype_description_array[$k] = $temp_value_arr[0] . "|" . $temp_value_arr[2];
                        }
                    } else if (preg_match("/frameshift/i", $genotype_description_array[$k])) {
                        $td_bg_color = $frameshift_variant_color_code;
                    } else if (preg_match("/exon.loss/i", $genotype_description_array[$k])) {
                        $td_bg_color = $exon_loss_variant_color_code;
                    } else if (preg_match("/lost/i", $genotype_description_array[$k])) {
                        $td_bg_color = $lost_color_code;
                        $temp_value_arr = preg_split("/[;, |\n]+/", $genotype_description_array[$k]);
                        if (count($temp_value_arr) > 3) {
                            $genotype_description_array[$k] = $temp_value_arr[0] . "|" . $temp_value_arr[2] . "|" . $temp_value_arr[3];
                        } elseif (count($temp_value_arr) > 2) {
                            $genotype_description_array[$k] = $temp_value_arr[0] . "|" . $temp_value_arr[2];
                        }
                    } else if (preg_match("/gain/i", $genotype_description_array[$k])) {
                        $td_bg_color = $gain_color_code;
                        $temp_value_arr = preg_split("/[;, |\n]+/", $genotype_description_array[$k]);
                        if (count($temp_value_arr) > 3) {
                            $genotype_description_array[$k] = $temp_value_arr[0] . "|" . $temp_value_arr[2] . "|" . $temp_value_arr[3];
                        } elseif (count($temp_value_arr) > 2) {
                            $genotype_description_array[$k] = $temp_value_arr[0] . "|" . $temp_value_arr[2];
                        }
                    } else if (preg_match("/disruptive/i", $genotype_description_array[$k])) {
                        $td_bg_color = $disruptive_color_code;
                    } else if (preg_match("/conservative/i", $genotype_description_array[$k])) {
                        $td_bg_color = $conservative_color_code;
                    } else if (preg_match("/splice/i", $genotype_description_array[$k])) {
                        $td_bg_color = $splice_color_code;
                    } else if (preg_match("/ref/i", $genotype_description_array[$k])) {
                        $td_bg_color = $ref_color_code;
                    }

                    echo "<td id=\"" . $row_id_prefix . "_" . $position_array[$k] . "\" style=\"border:1px solid black;min-width:80px;background-color:" . $td_bg_color . "\">" . $genotype_description_array[$k] . "</td>";
                }
            } elseif ($key == "Imputation") {
                if (preg_match("/\\+/i", $value)) {
                    echo "<td style=\"border:1px solid black;min-width:80px;\">+</td>";
                } else {
                    echo "<td style=\"border:1px solid black;min-width:80px;\">-</td>";
                }
            }
        }

        echo "</tr>";
    }

    echo "</table>";
    echo "</div>";

    echo "<div style='margin-top:10px;' align='right'>";
    echo "<button onclick=\"queryAllByAccessionsAndGene('" . $dataset . "', '" . $result_arr[0]["Gene"] . "', '" . implode(";", $accession_array) . "')\"> Download</button>";
    echo "</div>";

    echo "<br />";
    echo "<br />";

} else {
    echo "<p>No Allele Catalog data available for this gene!!!</p>";
}


echo "<br/><br/>";
echo "<div style='margin-top:10px;' align='center'>";
echo "<button onclick=\"queryAccessionInformation('" . $dataset . "')\" style=\"margin-right:20px;\">Download Accession Information</button>";
echo "</div>";
echo "<br/><br/>";

?>


<script type="text/javascript" language="javascript" src="./js/viewAllByAccessionsAndGene.js"></script>

<?php include '../footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

<?php
$TITLE = "Soybean Allele Catalog Tool";

include '../header.php';
?>

<div>
    <table width="100%" cellspacing="14" cellpadding="14">
        <tr>
            <td width="50%" align="center" valign="top" style="border:1px solid #999999; padding:10px; background-color:#f8f8f8; text-align:left;">
                <form action="viewAllByGenes.php" method="get" target="_blank">
                    <h2>Search by Gene IDs</h2>
                    <br />
                    <label for="dataset_1"><b>Dataset:</b></label>
                    <select name="dataset_1" id="dataset_1">
                        <option value="Soy775">Soy775 Allele Catalog</option>
                        <option value="Soy1066" selected>Soy1066 Allele Catalog</option>
                    </select>
                    <br />
                    <br />
                    <b>Gene IDs:</b><span style="font-size:9pt">&nbsp;(eg Glyma.01G049100 Glyma.01G049200 Glyma.01G049300)</span>
                    <br />
                    <textarea id="gene_1" name="gene_1" rows="12" cols="50" placeholder="&#10;Please separate each gene into a new line. &#10;&#10;Example:&#10;Glyma.01G049100&#10;Glyma.01G049200&#10;Glyma.01G049300"></textarea>
                    <br />
                    <br />
                    <label><b>Improvement Status:</b></label>
                    <table>
                        <tr>
                            <td style="min-width:100px">
                                <input type="checkbox" id="Soja" name="improvement_status_1[]" value="Soja" checked><label> Soja</label>
                            </td>
                            <td style="min-width:100px">
                                <input type="checkbox" id="Elite" name="improvement_status_1[]" value="Elite" checked><label> Elite</label>
                            </td>
                            <td style="min-width:100px">
                                <input type="checkbox" id="Landrace" name="improvement_status_1[]" value="Landrace" checked><label> Landrace</label>
                            </td>
                            <td style="min-width:100px">
                                <input type="checkbox" id="Cultivar" name="improvement_status_1[]" value="Cultivar" checked><label> Cultivar</label>
                            </td>
                        </tr>
                    </table>
                    <br />
                    <input type="submit" value="Search">
                </form>
            </td>
            <td width="50%" align="center" valign="top" style="border:1px solid #999999; padding:10px; background-color:#f8f8f8; text-align:left;">
                <form action="viewAllByAccessionsAndGene.php" method="get" target="_blank">
                    <h2>Search by Accessions and Gene ID</h2>
                    <br />
                    <label for="dataset_2"><b>Dataset:</b></label>
                    <select name="dataset_2" id="dataset_2">
                        <option value="Soy775">Soy775 Allele Catalog</option>
                        <option value="Soy1066" selected>Soy1066 Allele Catalog</option>
                    </select>
                    <br />
                    <br />
                    <b>Accessions:</b><span style="font-size:9pt">&nbsp;(eg HN058_PI458515 PI_479752)</span>
                    <br />
                    <textarea id="accession_2" name="accession_2" rows="12" cols="50" placeholder="&#10;Please separate each accession into a new line. &#10;&#10;Example:&#10;HN052_PI424079&#10;PI_479752"></textarea>
                    <br /><br />
                    <b>Gene ID:</b><span style="font-size:9pt">&nbsp;(One gene name only; eg Glyma.01G049100)</span>
                    <br />
                    <input type="text" id="gene_2" name="gene_2" size="55"></input>
                    <br /><br />
                    <input type="submit" value="Search">
                </form>
            </td>
        </tr>
    </table>
</div>

<br />
<br />

<div style='margin-top:10px;' align='center'>
    <button onclick="queryAccessionInformation()" style="margin-right:20px;">Download Accession Information</button>
</div>

<script type="text/javascript" language="javascript" src="./js/index.js"></script>

<script type="text/javascript" language="javascript">
</script>

<?php include '../footer.php'; ?>
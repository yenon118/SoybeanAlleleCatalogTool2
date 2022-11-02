function convertJsonToCsv(jsonObject) {
    let csvString = '';
    let th_keys = Object.keys(jsonObject[0]);
    for (let i = 0; i < th_keys.length; i++) {
        th_keys[i] = "\"" + th_keys[i] + "\"";
    }
    csvString += th_keys.join(',') + '\n';
    for (let i = 0; i < jsonObject.length; i++) {
        let tr_keys = Object.keys(jsonObject[i]);
        for (let j = 0; j < tr_keys.length; j++) {
            csvString += ((jsonObject[i][tr_keys[j]] === null) || (jsonObject[i][tr_keys[j]] === undefined)) ? '\"\"' : "\"" + jsonObject[i][tr_keys[j]] + "\"";
            if (j < (tr_keys.length-1)) {
                csvString += ',';
            }
        }
        csvString += '\n';
    }
    return csvString;
}


function createAndDownloadCsvFile(csvString, filename) {
    let dataStr = "data:text/csv;charset=utf-8," + encodeURI(csvString);
    let downloadAnchorNode = document.createElement('a');
    downloadAnchorNode.setAttribute("href", dataStr);
    downloadAnchorNode.setAttribute("download", filename + ".csv");
    document.body.appendChild(downloadAnchorNode); // required for firefox
    downloadAnchorNode.click();
    downloadAnchorNode.remove();
}


function queryAccessionInformation() {

    var selectedDataset1 = document.getElementById("dataset_1").value;
    var selectedDataset2 = document.getElementById("dataset_2").value;

    if (selectedDataset1 == selectedDataset2) {
        selectedDataset = selectedDataset1;
    } else {
        selectedDataset = "Soy1066";
    }

    if (selectedDataset) {
        $.ajax({
            url: './php/queryAccessionInformation.php',
            type: 'GET',
            contentType: 'application/json',
            data: {
                Dataset: selectedDataset
            },
            success: function (response) {
                res = JSON.parse(response);
                res = res.data;

                if (res.length > 0) {
                    let csvString = convertJsonToCsv(res);
                    createAndDownloadCsvFile(csvString, String(selectedDataset) + "_Accession_Information");
                    
                } else {
                    alert("Accession information of the " + selectedDataset + " dataset is not available!!!");
                }
                
            },
            error: function (xhr, status, error) {
                console.log('Error with code ' + xhr.status + ': ' + xhr.statusText);
                alert("Accession information of the " + selectedDataset + " dataset is not available!!!");
            }
        });
    } else {
        alert("Accession information of the " + selectedDataset + " dataset is not available!!!");
    }

}

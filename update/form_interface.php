<?php
include ('../config.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Currency Rate Interface</title>
<style>
    body {
        font-family: Arial, sans-serif;
    }
    .container {
        max-width: 800px;
        margin: 0 auto;
        padding: 10px;
    }
    label {
        display: block;
        margin-bottom: 20px;
    }
    input[type="text"], input[type="number"], select, button {
        width: 100%;
        padding: 10px;
        margin-bottom: 10px;
    }
    button {
        background-color: #4CAF50;
        color: white;
        border: none;
        cursor: pointer;
    }
    button:hover {
        background-color: #45a049;
    }
    textarea {
        width: 100%;
        height: 200px;
        margin-top: 10px;
    }
    .radio-container {
        display: inline-flex;
    }
    .radio-container input[type="radio"] {
        margin-right: 20px;
        display: inline-flex;
    }
</style>
</head>
<body>
<div class="container">
    <h2>Form Interface For POST & PUT & DElETE</h2>
    <form id="currencyForm" method="get">
        <div class="radio-container">
            <label>Action:</label>
            <input type="radio" id="post" name="action" value="post">
            <label for="post">POST</label>
            <input type="radio" id="put" name="action" value="put">
            <label for="put">PUT</label>
            <input type="radio" id="del" name="action" value="del">
            <label for="del">DEL</label>
        </div>
        <label for="currencyCode">Currency Code:</label>
        <select id="currencyCode" name="cur">
            <option value="default_curr_code">Currency Code</option>
        </select>
        <button type="submit">Submit</button>
    </form>
    <textarea id="response" class="response" readonly><?php echo isset($response_xml) ? htmlentities($response_xml) : ''; ?></textarea>
</div>

<script>
// Handle form submission
document.getElementById("currencyForm").addEventListener("submit", function(event) {
    event.preventDefault();
    var form = event.target;
    var formData = new FormData(form);

    // Get the selected currency code
    var currencyCode = form.querySelector('#currencyCode').value;

    var xhr = new XMLHttpRequest();
    var action = document.querySelector('input[name="action"]:checked').value;
    xhr.open("GET", "index.php?" + new URLSearchParams(formData).toString()+ "&action=" + action, true); // Pass form data as query parameters
    xhr.onload = function () {
        if (xhr.status === 200) {
            document.getElementById("response").value = xhr.responseText; // Update textarea with response
        } else {
            console.error(xhr.statusText);
        }
    };
    xhr.onerror = function () {
        console.error("Network Error");
    };
    xhr.send();
});

// Fetch currency codes from place.xml and populate dropdown menu
window.onload = function() {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            var xmlDoc = xhr.responseXML;
            var ccys = xmlDoc.querySelectorAll("ISO_4217 CcyNtry Ccy");
            var selectElement = document.getElementById("currencyCode");
            var uniqueCurrencies = new Set();
            ccys.forEach(function(ccy) {
                var currencyCode = ccy.textContent;
                if (!uniqueCurrencies.has(currencyCode)) { // Check if currency code is unique
                    var option = document.createElement("option");
                    option.value = currencyCode;
                    option.text = currencyCode;
                    selectElement.appendChild(option);
                    uniqueCurrencies.add(currencyCode); // Add currency code to the set
                }
            });
        }
    };
    xhr.open("GET", "../data/list-one.xml", true); // Adjust the path to place.xml
    xhr.send();
};
</script>
</body>
</html>


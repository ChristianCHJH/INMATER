<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consultas API</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        .form-group button {
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        .results {
            margin-top: 20px;
        }
        .results pre {
            background-color: #f8f9fa;
            padding: 15px;
            border: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
    <h1>Consultas API</h1>

    <div class="form-group">
        <label for="dni">Consultar DNI:</label>
        <input type="text" id="dni" placeholder="Ingrese el DNI">
        <button onclick="consultaDni()">Consultar</button>
    </div>

    <div class="form-group">
        <label for="passport">Consultar Pasaporte:</label>
        <input type="text" id="passport" placeholder="Ingrese el Pasaporte">
        <button onclick="consultaPassport()">Consultar</button>
    </div>

    <div class="form-group">
        <label for="residencyCard">Consultar Carnet de Extranjería:</label>
        <input type="text" id="residencyCard" placeholder="Ingrese el Carnet de Extranjería">
        <button onclick="consultaResidencyCard()">Consultar</button>
    </div>

    <div class="form-group">
        <label for="ruc">Consultar RUC:</label>
        <input type="text" id="ruc" placeholder="Ingrese el RUC">
        <button onclick="consultaRuc()">Consultar</button>
    </div>

    <div class="results" id="results"></div>

    <script>
        function consultaDni() {
            let dni = $('#dni').val();
            $.ajax({
                url: `/users/showDniData/${dni}`,
                method: 'GET',
                success: function(data) {
                    $('#results').html('<pre>' + JSON.stringify(data, null, 4) + '</pre>');
                },
                error: function(err) {
                    alert('Error: ' + err.responseText);
                }
            });
        }

        function consultaPassport() {
            let passport = $('#passport').val();
            $.ajax({
                url: `/users/showPassportData/${passport}`,
                method: 'GET',
                success: function(data) {
                    $('#results').html('<pre>' + JSON.stringify(data, null, 4) + '</pre>');
                },
                error: function(err) {
                    alert('Error: ' + err.responseText);
                }
            });
        }

        function consultaResidencyCard() {
            let residencyCard = $('#residencyCard').val();
            $.ajax({
                url: `/users/getResidencyCard/${residencyCard}`,
                method: 'GET',
                success: function(data) {
                    $('#results').html('<pre>' + JSON.stringify(data, null, 4) + '</pre>');
                },
                error: function(err) {
                    alert('Error: ' + err.responseText);
                }
            });
        }

        function consultaRuc() {
            let ruc = $('#ruc').val();
            $.ajax({
                url: `/users/showRucData/${ruc}`,
                method: 'GET',
                success: function(data) {
                    $('#results').html('<pre>' + JSON.stringify(data, null, 4) + '</pre>');
                },
                error: function(err) {
                    alert('Error: ' + err.responseText);
                }
            });
        }
    </script>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <title>Test Backup API</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>Test Backup API</h1>
    <button onclick="testLoadBackups()">Cargar Backups</button>
    <button onclick="testGenerateBackup()">Generar Backup</button>
    <div id="results"></div>

    <script>
        function testLoadBackups() {
            fetch('/api/backups', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('results').innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
            })
            .catch(error => {
                document.getElementById('results').innerHTML = '<pre>Error: ' + error + '</pre>';
            });
        }

        function testGenerateBackup() {
            fetch('/api/backups/generate', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('results').innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
            })
            .catch(error => {
                document.getElementById('results').innerHTML = '<pre>Error: ' + error + '</pre>';
            });
        }
    </script>
</body>
</html>

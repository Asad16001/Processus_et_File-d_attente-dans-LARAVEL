<!DOCTYPE html>
<html>
<head>
    <title>Exécuteur Python</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 20px auto;
            padding: 0 20px;
        }

        textarea {
            width: 100%;
            height: 200px;
            padding: 10px;
            margin: 10px 0;
            font-family: monospace;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            background-color: #4CAF50;
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 4px;
        }

        #output {
            margin-top: 20px;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 4px;
            min-height: 100px;
            white-space: pre-wrap;
            font-family: monospace;
        }
    </style>
</head>
<body>
<h1>Exécuteur de Code Python</h1>

<textarea id="code" placeholder="Écrivez votre code Python ici...">print("Hello from Python!")</textarea>

<button onclick="executePython()">Exécuter</button>

<div id="output"></div>

<script>
    async function executePython() {
        const code = document.getElementById('code').value;
        const outputElement = document.getElementById('output');
        outputElement.textContent = 'Exécution en cours...';

        try {
            const response = await fetch('/execute-python', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ code })
            });

            const { execution_id } = await response.json();

            const checkResult = async () => {
                const resultResponse = await fetch(`/python-result/${execution_id}`);
                const result = await resultResponse.json();

                if (result.output !== undefined) {
                    outputElement.textContent = result.output || 'Aucune sortie';
                    return;
                }

                setTimeout(checkResult, 1000);
            };

            checkResult();

        } catch (error) {
            outputElement.textContent = `Erreur: ${error.message}`;
        }
    }
</script>
</body>
</html>

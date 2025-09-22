<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $file;
    if (!file_exists("perguntas_texto.txt")) {
        $file = fopen("perguntas_texto.txt", "w") or die("Erro ao criar arquivo");
        fputcsv($file, ["id", "pergunta", "resposta"], ";");
        fclose($file);
    }

    $linecount = 0;
    $file = fopen("perguntas_texto.txt", "r");
    while (!feof($file) && fgets($file)) {
        $linecount++;
    }
    fclose($file);

    $file = fopen("perguntas_texto.txt", "a") or die("Erro ao abrir arquivo");
    fputcsv($file, [$linecount - 1, $_POST["pergunta"], $_POST["resposta"]], ";");
    fclose($file);


    header('Location: http://localhost:80/alunos/perguntas/listar.php');

}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/franken-ui@2.1.0/dist/css/core.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/franken-ui@2.1.0/dist/js/core.iife.js" type="module"></script>
    <script src="https://cdn.jsdelivr.net/npm/franken-ui@2.1.0/dist/js/icon.iife.js" type="module"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title>Adicionar pergunta de texto</title>
</head>

<body class="flex flex-col p-5">
    <form action="./criar.php" method="post" class="flex flex-col w-lg mx-auto border p-5 gap-3">
        <label class="!w-full">
            <p>Pergunta:</p>
            <textarea name="pergunta" class="w-full border"></textarea>
        </label>

        <label class="!w-full">
            <p>Resposta:</p>
            <textarea name="resposta" class="w-full border"></textarea>
        </label>
        <input type="submit" class=" uk-btn uk-btn-primary" value="Enviar">
    </form>
</body>

</html>
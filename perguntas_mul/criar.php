<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $file;
    if (!file_exists("perguntas_mul.txt")) {
        $file = fopen("perguntas_mul.txt", "w") or die("Erro ao criar arquivo");
        fputcsv($file, ["id", "pergunta", "a", "b", "c", "d", "e"], ";");
        fclose($file);
    }

    $linecount = 0;
    $file = fopen("perguntas_mul.txt", "r");
    while (!feof($file) && fgets($file)) {
        $linecount++;
    }
    fclose($file);

    $file = fopen("perguntas_mul.txt", "a") or die("Erro ao abrir arquivo");
    fputcsv(
        $file,
        [
            $linecount - 1,
            $_POST["pergunta"],
            $_POST["a"],
            $_POST["b"],
            $_POST["c"],
            $_POST["d"],
            $_POST["e"],
        ],
        ";"
    );
    fclose($file);


    header('Location: http://localhost:80/alunos/perguntas_mul/listar.php');

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
    <title>Adicionar usuario</title>
</head>

<body class="flex flex-col p-5">
    <form action="./criar.php" method="post" class="flex flex-col w-lg mx-auto border p-5 gap-3">
        <label class="!w-full">
            <p>Pergunta:</p>
            <input type="text" name="pergunta" class="w-full border" required>

        </label>

        <label class="!w-full">
            <p>A:</p>
            <input type="text" name="a" class="w-full border" required>
        </label>

        <label class="!w-full">
            <p>B:</p>
            <input type="text" name="b" class="w-full border" required>
        </label>

        <label class="!w-full">
            <p>C:</p>
            <input type="text" name="c" class="w-full border" required>
        </label>

        <label class="!w-full">
            <p>D:</p>
            <input type="text" name="d" class="w-full border" required>
        </label>

        <label class="!w-full">
            <p>E:</p>
            <input type="text" name="e" class="w-full border" required>
        </label>

        <input type="submit" class=" uk-btn uk-btn-primary" value="Enviar">
    </form>
</body>

</html>
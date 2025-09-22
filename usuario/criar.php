<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $file;
    if (!file_exists("usuarios.txt")) {
        $file = fopen("usuarios.txt", "w") or die("Erro ao criar arquivo");
        fputcsv($file, ["id", "pergunta", "resposta"], ";");
        fclose($file);
    }

    $linecount = 0;
    $file = fopen("usuarios.txt", "r");
    while (!feof($file) && fgets($file)) {
        $linecount++;
    }
    fclose($file);

    $file = fopen("usuarios.txt", "a") or die("Erro ao abrir arquivo");
    fputcsv($file, [$linecount - 1, $_POST["nome"], $_POST["email"]], ";");
    fclose($file);


    header('Location: http://localhost:80/alunos/usuario/listar.php');

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
            <p>Nome:</p>
            <input type="text" name="nome" class="w-full border" required>

        </label>

        <label class="!w-full">
            <p>Email:</p>
            <input type="email" name="email" class="w-full border" required>
        </label>
        
        <input type="submit" class=" uk-btn uk-btn-primary" value="Enviar">
    </form>
</body>

</html>
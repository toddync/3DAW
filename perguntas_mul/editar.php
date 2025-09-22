<?php
$linha;

if (isset($_POST["editar"])) {
    if (!file_exists("perguntas_mul.txt")) {
        die("Arquivo não existe");
    }

    $file = fopen("perguntas_mul.txt", "r") or die("Erro ao abrir arquivo");
    $arq = fgets($file);

    while ($linha = fgetcsv($file, 0, ";")) {
        $d = "";
        if ($linha[0] == $_POST["id"]) {
            $d = $_POST["id"] . ";" . $_POST["pergunta"] . ";" . $_POST["a"] . ";" . $_POST["b"] . ";" . $_POST["c"] . ";" . $_POST["d"] . ";" . $_POST["e"] . ";" . "\n";
        } else {
            $d = $linha[0] . ";" . $linha[1] . ";" . $linha[2] . "\n";
        }

        $arq = $arq . $d;
    }
    fclose($file);

    $file = fopen("perguntas_mul.txt", "w") or die("Erro ao abrir arquivo");
    fwrite($file, $arq);
    fclose($file);

    header('Location: http://localhost:80/alunos/perguntas_mul/listar.php');
} else if (isset($_POST['id'])) {
    if (!file_exists("perguntas_mul.txt")) {
        die("Arquivo não existe");
    }

    $file = fopen("perguntas_mul.txt", "r") or die("Erro ao abrir arquivo");
    $arq = fgets($file);

    while ($linha = fgetcsv($file, 0, ";")) {
        if ($linha[0] == $_POST["id"]) {
            break;
        }
    }
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
    <title>Editar Pergunta de multipla escolha</title>
</head>

<body class="flex flex-col p-5">
    <form action="./editar.php" method="post" class="flex flex-col w-lg mx-auto border p-5 gap-3">
        <label class="!w-full">
            <p>Pergunta:</p>
            <input type="text" name="pergunta" class="w-full border" required value="<?php echo $linha[1]; ?>">

        </label>

        <label class="!w-full">
            <p>A:</p>
            <input type="text" name="a" class="w-full border" required value="<?php echo $linha[2]; ?>">
        </label>

        <label class="!w-full">
            <p>B:</p>
            <input type="text" name="b" class="w-full border" required value="<?php echo $linha[3]; ?>">
        </label>

        <label class="!w-full">
            <p>C:</p>
            <input type="text" name="c" class="w-full border" required value="<?php echo $linha[4]; ?>">
        </label>

        <label class="!w-full">
            <p>D:</p>
            <input type="text" name="d" class="w-full border" required value="<?php echo $linha[5]; ?>">
        </label>

        <label class="!w-full">
            <p>E:</p>
            <input type="text" name="e" class="w-full border" required value="<?php echo $linha[6]; ?>">
        </label>

        <input type="hidden" name="id" value="<?php echo $_POST["id"]; ?>">
        <input type="hidden" name="editar" value="<?php echo $_POST["id"]; ?>">

        <input type="submit" class=" uk-btn uk-btn-primary" value="Enviar">
    </form>
</body>

</html>
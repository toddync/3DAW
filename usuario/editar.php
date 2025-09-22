<?php
$id;
$nome;
$email;

if (isset($_POST["editar"])) {
    if (!file_exists("usuarios.txt")) {
        die("Arquivo não existe");
    }

    $file = fopen("usuarios.txt", "r") or die("Erro ao abrir arquivo");
    $arq = fgets($file);

    while ($linha = fgetcsv($file, 0, ";")) {
        $d = "";
        if ($linha[0] == $_POST["id"]) {
            $d = $_POST["id"] . ";" . $_POST["nome"] . ";" . $_POST["email"] . "\n";
        } else {
            $d = $linha[0] . ";" . $linha[1] . ";" . $linha[2] . "\n";
        }

        $arq = $arq . $d;
    }
    fclose($file);

    $file = fopen("usuarios.txt", "w") or die("Erro ao abrir arquivo");
    fwrite($file, $arq);
    fclose($file);

    header('Location: http://localhost:80/alunos/usuario/listar.php');
} else if (isset($_POST['id'])) {
    if (!file_exists("usuarios.txt")) {
        die("Arquivo não existe");
    }

    $file = fopen("usuarios.txt", "r") or die("Erro ao abrir arquivo");
    $arq = fgets($file);

    while ($linha = fgetcsv($file, 0, ";")) {
        if ($linha[0] == $_POST["id"]) {
            $id = $linha[0];
            $nome = $linha[1];
            $email = $linha[2];
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
    <title>Editar Usuario</title>
</head>

<body class="flex flex-col p-5">
    <form action="./editar.php" method="post" class="flex flex-col w-lg mx-auto border p-5 gap-3">
        <label class="!w-full">
            <p>Nome:</p>
            <input type="text" name="nome" class="w-full border" required value="<?php echo $nome; ?>">
        </label>

        <label class="!w-full">
            <p>Email:</p>
            <input type="email" name="email" class="w-full border" required value="<?php echo $email; ?>">
        </label>

        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <input type="hidden" name="editar" value="<?php echo $id; ?>">

        <input type="submit" class=" uk-btn uk-btn-primary" value="Enviar">
    </form>
</body>

</html>
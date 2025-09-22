<?php 
if (isset($_POST["id"])) {
    if (!file_exists("perguntas_texto.txt")) {
        die("Arquivo não existe");
    }

    $file = fopen("perguntas_texto.txt", "r") or die("Erro ao abrir arquivo");
    $arq = fgets($file);

    while ($linha = fgetcsv($file, 0, ";")) {
        $d = "";
        if ($linha[0] == $_POST["id"]) {
            continue;
        } else {
            $d = $linha[0] . ";" . $linha[1] . ";" . $linha[2] . "\n";
        }

        $arq = $arq . $d;
    }
    fclose($file);

    $file = fopen("perguntas_texto.txt", "w") or die("Erro ao abrir arquivo");
    fwrite($file, $arq);
    fclose($file);

    header('Location: http://localhost:80/alunos/perguntas/listar.php');
}
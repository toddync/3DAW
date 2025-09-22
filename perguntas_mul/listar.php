<?php
$file;
if (!file_exists("perguntas_mul.txt")) {
    $file = fopen("perguntas_mul.txt", "w") or die("Erro ao criar arquivo");
    fputcsv($file, ["id", "pergunta", "a", "b", "c", "d", "e"], ";");
    fclose($file);
}

$file = fopen("perguntas_mul.txt", "r") or die("Erro ao abrir arquivo");
fgets($file);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/franken-ui@2.1.0/dist/css/core.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/franken-ui@2.1.0/dist/js/core.iife.js" type="module"></script>
    <script src="https://cdn.jsdelivr.net/npm/franken-ui@2.1.0/dist/js/icon.iife.js" type="module"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title>Lista de perguntas de texto</title>
</head>

<body class="flex flex-col p-5">
    <form action="./criar.php">
        <input type="submit" class=" uk-btn uk-btn-primary" value="Adicionar pergunta">
    </form>

    <?php
    while ($linha = fgetcsv($file, 0, ";")) {
        ?>
        <div class="uk-card uk-card-body max-w-fit flex flex-col mx-auto">
            <h3 class="uk-card-title w-fit mx-auto"><?php echo $linha[1]; ?></h3>
            <div class="flex flex-col gap-2 m-2">
                <p>
                    A) <?php echo $linha[2]; ?>
                </p>
                <p>
                    B) <?php echo $linha[3]; ?>
                </p>
                <p>
                    C) <?php echo $linha[4]; ?>
                </p>
                <p>
                    D) <?php echo $linha[5]; ?>
                </p>
                <p>
                    E) <?php echo $linha[6]; ?>
                </p>
            </div>
            <div class="flex gap-3">
                <form action="./editar.php" method="post" class="mx-auto">
                    <input type="hidden" name="id" value="<?php echo $linha[0]; ?>">
                    <input type="submit" class=" uk-btn uk-btn-primary" value="Editar">
                </form>

                <form action="./deletar.php" method="post" class="mx-auto">
                    <input type="hidden" name="id" value="<?php echo $linha[0]; ?>">
                    <input type="submit" class=" uk-btn uk-btn-destructive" value="Deletar">
                </form>
            </div>
        </div>
        <?php
    }
    ?>
</body>

</html>

<?php fclose($file); ?>
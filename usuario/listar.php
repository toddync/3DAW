<?php
if (!file_exists("usuarios.txt")) {
    $file = fopen("usuarios.txt", "w");
    fputcsv($file, ["id", "nome"], ";");
    fclose($file);
}

$file = fopen("usuarios.txt", "r");
fgets($file);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/franken-ui@2.1.0/dist/css/core.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/franken-ui@2.1.0/dist/js/core.iife.js" type="module"></script>
    <script src="https://cdn.jsdelivr.net/npm/franken-ui@2.1.0/dist/js/icon.iife.js" type="module"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title>Lista de Usuarios</title>
</head>

<body>
    <form action="./criar.php">
        <input type="submit" class=" uk-btn uk-btn-primary" value="Adicionar usuario">
    </form>
    <table class="uk-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($linha = fgetcsv($file, 0, ";")) {
                ?>
                <tr>
                    <td><?php echo $linha[0]; ?></td>
                    <td><?php echo $linha[1]; ?></td>
                    <td><?php echo $linha[2]; ?></td>
                    <td>
                        <div class="flex gap-3">
                            <form action="./editar.php" method="post">
                                <input type="hidden" name="id" value="<?php echo $linha[0]; ?>">
                                <input type="submit" class=" uk-btn uk-btn-primary" value="Editar">
                            </form>

                            <form action="./deletar.php" method="post">
                                <input type="hidden" name="id" value="<?php echo $linha[0]; ?>">
                                <input type="submit" class=" uk-btn uk-btn-destructive" value="Deletar">
                            </form>
                        </div>
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
</body>

</html>
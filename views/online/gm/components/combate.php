<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Combate</title>
    <style>
        .tablero {
            display: flex;
            gap: 40px;
            margin: 20px auto;
            width: max-content;
        }
        .zona {
            display: grid;
            grid-template-columns: repeat(3, 50px);
            grid-template-rows: repeat(3, 50px);
            gap: 5px;
        }
        .celda {
            width: 50px;
            height: 50px;
            background: #e0e0e0;
            border: 1px solid #888;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2em;
        }
    </style>
</head>
<body>
    <div class="tablero">
        <div class="zona">
            <?php
            for ($fila = 0; $fila < 3; $fila++) {
                for ($col = 0; $col < 3; $col++) {
                    echo '<div class="celda"></div>';
                }
            }
            ?>
        </div>
        <div class="zona">
            <?php
            for ($fila = 0; $fila < 3; $fila++) {
                for ($col = 0; $col < 3; $col++) {
                    echo '<div class="celda"></div>';
                }
            }
            ?>
        </div>
    </div>
</body>
</html>
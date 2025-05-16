<?php
// Mostrar errores por si ocurre algo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conexión a MySQL
$host = "localhost";
$user = "root";
$pass = "";
$db = "quiz_interactivo";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Consulta de puntos
$sql = "SELECT nombre, puntos FROM usuarios ORDER BY puntos DESC";
$resultado = $conn->query($sql);

// Para calcular la barra, necesitamos el máximo de puntos para hacer proporción
$max_puntos = 0;
if ($resultado->num_rows > 0) {
    $usuarios = [];
    while ($fila = $resultado->fetch_assoc()) {
        $usuarios[] = $fila;
        if ($fila['puntos'] > $max_puntos) {
            $max_puntos = $fila['puntos'];
        }
    }
} else {
    $usuarios = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tabla de Puntos con Copas</title>
    <meta http-equiv="refresh" content="5">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(to right, #e0f7fa, #80deea);
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .container {
            background-color: white;
            padding: 30px 40px;
            border-radius: 16px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 90%;
        }
        h2 {
            text-align: center;
            color: #00796b;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 1.1rem;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ccc;
            vertical-align: middle;
        }
        th {
            background-color: #26a69a;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f1f1f1;
        }
        .barra-container {
            background: #ddd;
            border-radius: 10px;
            overflow: hidden;
            height: 24px;
            max-width: 100%;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.2);
        }
        .barra {
            height: 100%;
            background: linear-gradient(90deg, #26a69a, #00796b);
            border-radius: 10px 0 0 10px;
            width: 0;
            color: white;
            text-align: right;
            padding-right: 8px;
            line-height: 24px;
            font-weight: 700;
            transition: width 1s ease;
            white-space: nowrap;
        }
        .nombre {
            font-weight: 600;
            color: #004d40;
            width: 35%;
            padding-right: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1.1rem;
        }
        /* Copas */
        .copa {
            font-size: 1.6rem;
            user-select: none;
        }
        .oro {
            color: #FFD700; /* oro */
            text-shadow: 0 0 5px #ffea00;
        }
        .plata {
            color: #C0C0C0; /* plata */
            text-shadow: 0 0 5px #e0e0e0;
        }
        .bronce {
            color: #cd7f32; /* bronce */
            text-shadow: 0 0 5px #b06d29;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Tabla de Puntos</h2>
        <table>
            <tr>
                <th>Participante</th>
                <th>Puntos</th>
            </tr>
            <?php if (count($usuarios) > 0): ?>
                <?php foreach ($usuarios as $index => $usuario): 
                    $porcentaje = $max_puntos > 0 ? ($usuario['puntos'] / $max_puntos) * 100 : 0;
                    // Definir copa para los primeros 3
                    $copa = "";
                    if ($index === 0) $copa = '<span class="copa oro">🥇</span>';
                    elseif ($index === 1) $copa = '<span class="copa plata">🥈</span>';
                    elseif ($index === 2) $copa = '<span class="copa bronce">🥉</span>';
                ?>
                <tr>
                    <td class="nombre"><?= $copa ?><?= htmlspecialchars($usuario['nombre']) ?></td>
                    <td>
                        <div class="barra-container" aria-label="<?= htmlspecialchars($usuario['nombre']) ?> tiene <?= $usuario['puntos'] ?> puntos">
                            <div class="barra" style="width: <?= $porcentaje ?>%;">
                                <?= $usuario['puntos'] ?>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="2" style="text-align:center;">No hay participantes aún</td></tr>
            <?php endif; ?>
        </table>
    </div>

    <script>
        window.onload = () => {
            const barras = document.querySelectorAll('.barra');
            barras.forEach(barra => {
                const width = barra.style.width;
                barra.style.width = '0';
                setTimeout(() => {
                    barra.style.width = width;
                }, 100);
            });
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>

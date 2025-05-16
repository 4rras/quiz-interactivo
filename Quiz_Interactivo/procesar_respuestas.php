<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$db = "quiz_interactivo";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Verificar que el usuario esté en sesión
if (!isset($_SESSION['nombre'])) {
    die("Usuario no autenticado.");
}
$usuario = $_SESSION['nombre'];

// Recibir datos enviados desde el formulario
$pregunta_id = intval($_POST['pregunta_id'] ?? 0);
$respuesta_usuario = $_POST['respuesta'] ?? '';

if ($pregunta_id <= 0 || empty($respuesta_usuario)) {
    die("Datos incompletos.");
}

// Obtener la respuesta correcta de la pregunta
$stmt = $conn->prepare("SELECT respuesta_correcta FROM preguntas WHERE id = ?");
$stmt->bind_param("i", $pregunta_id);
$stmt->execute();
$stmt->bind_result($respuesta_correcta);
if (!$stmt->fetch()) {
    die("Pregunta no encontrada.");
}
$stmt->close();

// Comparar la respuesta enviada con la correcta (case insensitive)
if (strcasecmp(trim($respuesta_usuario), trim($respuesta_correcta)) === 0) {
    // Sumar puntos (ejemplo 10 por cada correcta)
    $puntos_ganados = 10;

    // Obtener puntos actuales del usuario
    $stmt = $conn->prepare("SELECT puntos FROM usuarios WHERE nombre = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $stmt->bind_result($puntos_actuales);
    if ($stmt->fetch()) {
        $nuevos_puntos = $puntos_actuales + $puntos_ganados;
        $stmt->close();

        // Actualizar puntos en la base de datos
        $stmt = $conn->prepare("UPDATE usuarios SET puntos = ? WHERE nombre = ?");
        $stmt->bind_param("is", $nuevos_puntos, $usuario);
        $stmt->execute();
        $stmt->close();
    } else {
        $stmt->close();
        die("Usuario no encontrado.");
    }
}

$conn->close();

// Redirigir a la siguiente pregunta (aumenta 1 el id de pregunta)
header("Location: quiz.php?pregunta=" . ($pregunta_id + 1));
exit;
?>

<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

include 'conexion.php';

$usuario_id = $_SESSION['usuario_id'];
$pregunta_id = $_POST['pregunta_id'] ?? null;
$respuesta_usuario = $_POST['respuesta'] ?? null;

if (!$pregunta_id || !$respuesta_usuario) {
    echo json_encode(['error' => 'Datos incompletos']);
    exit;
}

$stmt = $conn->prepare("SELECT respuesta_correcta FROM preguntas_biblicas WHERE id = ?");
$stmt->bind_param("i", $pregunta_id);
$stmt->execute();
$stmt->bind_result($respuesta_correcta);
$stmt->fetch();
$stmt->close();

$puntos_a_sumar = 0;
if ($respuesta_usuario === $respuesta_correcta) {
    $puntos_a_sumar = 1;
}

$stmt = $conn->prepare("UPDATE usuarios SET puntos = puntos + ? WHERE id = ?");
$stmt->bind_param("ii", $puntos_a_sumar, $usuario_id);
$stmt->execute();
$stmt->close();

echo json_encode(['success' => true, 'puntos_sumados' => $puntos_a_sumar]);
?>

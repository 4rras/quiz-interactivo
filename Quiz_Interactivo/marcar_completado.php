<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['status' => 'error', 'mensaje' => 'Usuario no autenticado']);
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Actualiza completado a 1 para ese usuario
$sql_update = "UPDATE usuarios SET completado = 1 WHERE id = ?";
$stmt = $conn->prepare($sql_update);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['status' => 'ok', 'mensaje' => 'Usuario marcado como completado']);
} else {
    echo json_encode(['status' => 'error', 'mensaje' => 'No se pudo actualizar']);
}

$stmt->close();
$conn->close();
?>

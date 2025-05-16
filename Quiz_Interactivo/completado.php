<?php
session_start();
include 'conexion.php';

if (isset($_SESSION['usuario_id'])) {
    $id = $_SESSION['usuario_id'];

    $sql = "UPDATE jugadores SET completado = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
}
?>

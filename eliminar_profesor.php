<?php
include 'conexion.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM profesores WHERE id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
}

header("Location: profesores_index.php");
exit;
?>

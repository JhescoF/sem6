<?php
include 'conexion.php'; 


if (!isset($_GET['id'])) {
    die("Error: ID no especificado.");
}

$id = $_GET['id'];


$sql = "SELECT p.*
        FROM profesores p
        WHERE p.id = :id";

$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);
$profesor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profesor) {
    die("Error: Profesor no encontrado.");
}


$sqlCursosProfesor = "SELECT id_curso FROM profesor_curso WHERE id_profesor = :id";
$stmtCursosProf = $pdo->prepare($sqlCursosProfesor);
$stmtCursosProf->execute(['id' => $id]);
$cursosProfesor = $stmtCursosProf->fetchAll(PDO::FETCH_COLUMN);


$sqlCursos = "SELECT * FROM cursos";
$stmtCursos = $pdo->query($sqlCursos);
$cursos = $stmtCursos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Profesor</title>
      <link rel="stylesheet" href="estilos_editar.css">
</head>
<body>
    <h1>Editar Profesor</h1>
    <form action="update_profesor.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $profesor['id']; ?>">

        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?php echo $profesor['nombre']; ?>" required><br>

        <label>Apellido:</label>
        <input type="text" name="apellido" value="<?php echo $profesor['apellido']; ?>" required><br>

        <label>DNI:</label>
        <input type="text" name="dni" value="<?php echo $profesor['dni']; ?>" required><br>

        <label>Dirección:</label>
        <input type="text" name="direccion" value="<?php echo $profesor['direccion']; ?>"><br>

        <label>Años de experiencia:</label>
        <input type="number" name="experiencia_años" value="<?php echo $profesor['experiencia_años']; ?>"><br>

        <label>Cursos:</label>
<div class="cursos-container">
    <?php foreach ($cursos as $curso): ?>
        <?php $checked = in_array($curso['id'], $cursosProfesor) ? "checked" : ""; ?>
        <div class="curso-item">
            <input type="checkbox" id="curso_<?php echo $curso['id']; ?>" 
                   name="cursos[]" value="<?php echo $curso['id']; ?>" <?php echo $checked; ?>>
            <label for="curso_<?php echo $curso['id']; ?>">
                <?php echo $curso['nombre']; ?>
            </label>
        </div>
    <?php endforeach; ?>
</div>


        <br>
        <button type="submit">Guardar cambios</button>
    </form>
</body>
</html>

<?php
include("conexion.php");

// Si envió el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $dni = $_POST['dni'];
    $direccion = $_POST['direccion'];
    $experiencia = $_POST['experiencia_años'];
    $cursos = isset($_POST['cursos']) ? $_POST['cursos'] : [];

    try {
        // Iniciar transacción
        $pdo->beginTransaction();

        // Insertar profesor
        $sql = "INSERT INTO profesores (nombre, apellido, dni, direccion, experiencia_años) 
                VALUES (:nombre, :apellido, :dni, :direccion, :experiencia)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'nombre' => $nombre,
            'apellido' => $apellido,
            'dni' => $dni,
            'direccion' => $direccion,
            'experiencia' => $experiencia
        ]);
        $id_profesor = $pdo->lastInsertId();

        // Insertar cursos en la tabla intermedia
        if (!empty($cursos)) {
            $sql_curso = "INSERT INTO profesor_curso (id_profesor, id_curso) VALUES (:id_profesor, :id_curso)";
            $stmtCurso = $pdo->prepare($sql_curso);

            foreach ($cursos as $curso_id) {
                $stmtCurso->execute([
                    'id_profesor' => $id_profesor,
                    'id_curso' => $curso_id
                ]);
            }
        }

        // Confirmar cambios
        $pdo->commit();

        // Redirigir
        header("Location: profesores_index.php");
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error al agregar profesor: " . $e->getMessage());
    }
}

// Obtener cursos disponibles
$sqlCursos = "SELECT * FROM cursos";
$stmtCursos = $pdo->query($sqlCursos);
$cursos = $stmtCursos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Agregar Profesor</title>
  <link rel="stylesheet" href="estilos_agregar.css">

</head>
<body>
  <h1>Agregar Profesor</h1>
  <form method="POST">
    <label>Nombre:</label>
    <input type="text" name="nombre" required><br><br>

    <label>Apellido:</label>
    <input type="text" name="apellido" required><br><br>

    <label>DNI:</label>
    <input type="text" name="dni" required><br><br>

    <label>Dirección:</label>
    <input type="text" name="direccion"><br><br>

    <label>Años de experiencia:</label>
    <input type="number" name="experiencia_años"><br><br>

   <label>Cursos:</label>
<div class="cursos-container">
  <?php foreach ($cursos as $curso): ?>
    <div class="curso-item">
      <input type="checkbox" id="curso_<?php echo $curso['id']; ?>" name="cursos[]" value="<?php echo $curso['id']; ?>">
      <label for="curso_<?php echo $curso['id']; ?>">
        <?php echo $curso['nombre']; ?>
      </label>
    </div>
  <?php endforeach; ?>
</div>


    <br>
    <button type="submit">Guardar</button>
  </form>

  <br>
  <a href="profesores_index.php">⬅ Volver</a>
</body>
</html>

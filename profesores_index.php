<?php
// Conexión
$conexion = new mysqli("localhost", "root", "", "profesores_crud");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// ----- Paginación -----
$registros_por_pagina = 5;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$inicio = ($pagina > 1) ? ($pagina * $registros_por_pagina - $registros_por_pagina) : 0;

// ----- Buscador -----
$busqueda = "";
$where = "";
if (!empty($_GET['buscar'])) {
    $busqueda = $conexion->real_escape_string($_GET['buscar']);
    $where = "WHERE p.nombre LIKE '%$busqueda%' 
              OR p.apellido LIKE '%$busqueda%' 
              OR p.dni LIKE '%$busqueda%' 
              OR c.nombre LIKE '%$busqueda%'";
}

// ----- Total registros -----
$sql_total = "SELECT COUNT(DISTINCT p.id) AS total
              FROM profesores p
              LEFT JOIN profesor_curso pc ON p.id = pc.id_profesor
              LEFT JOIN cursos c ON pc.id_curso = c.id
              $where";
$resultado_total = $conexion->query($sql_total);
$fila_total = $resultado_total->fetch_assoc();
$total_registros = $fila_total['total'];
$total_paginas = ceil($total_registros / $registros_por_pagina);

// ----- Consulta principal -----
$sql = "SELECT p.id, p.nombre, p.apellido, p.dni, GROUP_CONCAT(c.nombre SEPARATOR ', ') AS cursos
        FROM profesores p
        LEFT JOIN profesor_curso pc ON p.id = pc.id_profesor
        LEFT JOIN cursos c ON pc.id_curso = c.id
        $where
        GROUP BY p.id
        LIMIT $inicio, $registros_por_pagina";

$resultado = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Profesores</title>
  <link rel="stylesheet" href="profesores_index.css">
</head>
<body>
  <header>
    <h1>Gestión de Profesores</h1>
  </header>

  <!-- 🔍 Buscador -->
  <form method="GET" action="profesores_index.php" style="margin-bottom:15px; text-align:center;">
    <input type="text" name="buscar" value="<?php echo $busqueda; ?>" placeholder="Buscar por nombre, DNI o curso">
    <button type="submit">Buscar</button>
  </form>


  <div class="top-bar" style="text-align:center; margin-bottom:10px;">
    <a href="agregar_profesor.php" class="btn-agregar">+ Agregar Registro</a>
  </div>

  <main>
    <?php if ($resultado->num_rows > 0) { ?>
      <table border="1" cellspacing="0" cellpadding="8" style="margin:auto; width:90%; text-align:center;">
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>DNI</th>
          <th>Cursos</th>
          <th>Acciones</th>
        </tr>
        <?php while ($fila = $resultado->fetch_assoc()) { ?>
          <tr>
            <td><?php echo $fila['id']; ?></td>
            <td><?php echo $fila['nombre'] . " " . $fila['apellido']; ?></td>
            <td><?php echo $fila['dni']; ?></td>
            <td><?php echo $fila['cursos'] ?: 'Sin curso'; ?></td>
            <td>
              <a href="editar_profesor.php?id=<?php echo $fila['id']; ?>" class="btn btn-editar">Editar</a>
              <a href="eliminar_profesor.php?id=<?php echo $fila['id']; ?>" class="btn btn-eliminar" onclick="return confirm('¿Seguro que deseas eliminar este profesor?')">Eliminar</a>
            </td>
          </tr>
        <?php } ?>
      </table>

      <!-- 📌 Paginación -->
      <div style="margin-top:15px; text-align:center;">
        <?php for ($i = 1; $i <= $total_paginas; $i++) { ?>
          <a href="profesores_index.php?pagina=<?php echo $i; ?>&buscar=<?php echo $busqueda; ?>"
             style="margin:0 5px; <?php echo ($i == $pagina) ? 'font-weight:bold; text-decoration:underline;' : ''; ?>">
             <?php echo $i; ?>
          </a>
        <?php } ?>
      </div>
    <?php } else { ?>
      <p style="text-align:center; font-size:18px; color:#777;">No hay registros de profesores.</p>
    <?php } ?>
    <?php $conexion->close(); ?>
  </main>
</body>
</html>

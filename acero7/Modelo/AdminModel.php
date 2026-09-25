<?php
/**
 * AdminModel.php
 * ---------------------------------------------------------------
 * Modelo del panel de administración de ACERO GYM.
 *
 * En lugar de escribir un ABM a mano para cada tabla, este modelo lee la
 * estructura REAL de la base (SHOW FULL COLUMNS) y arma los formularios y
 * listados automáticamente. Así, si la base tiene alguna columna distinta a
 * la esperada, el panel sigue funcionando igual.
 *
 * Lo único que se configura a mano es:
 *   - qué tablas se administran        -> adminEntidades()
 *   - qué columnas son claves foráneas -> clave 'fk' de cada entidad
 *   - etiquetas lindas para mostrar    -> clave 'labels'
 *
 * Todas las consultas usan sentencias preparadas. Los nombres de tablas y
 * columnas nunca vienen del usuario: se validan contra la estructura real de
 * la base antes de usarse (ver adminIdentificadorSeguro()).
 */

/* =============================================================
   1. Configuración de las entidades administrables
   ============================================================= */

function adminEntidades(): array
{
    return [
        'usuarios' => [
            'tabla'    => 'usuarios',
            'titulo'   => 'Usuarios',
            'singular' => 'usuario',
            'desc'     => 'Cuentas que pueden iniciar sesión en el sistema.',
            'password' => ['clave'],
            'labels'   => [
                'nombre_usuario' => 'Nombre de usuario',
                'FK_id_rol'      => 'Rol',
                'Fk_id_cliente'  => 'Cliente asociado',
                'Fk_id_empleado' => 'Empleado asociado',
                'clave'          => 'Contraseña',
            ],
            'listado'  => ['id_usuarios', 'nombre_usuario', 'email', 'FK_id_rol'],
            'buscar'   => ['nombre_usuario', 'email'],
        ],

        'clientes' => [
            'tabla'    => 'clientes',
            'titulo'   => 'Clientes',
            'singular' => 'cliente',
            'desc'     => 'Socios del gimnasio y sus datos físicos.',
            'labels'   => ['DNI' => 'DNI', 'altura' => 'Altura (m)', 'peso' => 'Peso (kg)'],
            'listado'  => ['id_clientes', 'Nombre', 'apellido', 'DNI', 'genero'],
            'buscar'   => ['Nombre', 'apellido', 'DNI'],
        ],

        'empleados' => [
            'tabla'    => 'empleados',
            'titulo'   => 'Empleados',
            'singular' => 'empleado',
            'desc'     => 'Personal del gimnasio.',
            'buscar'   => ['nombre', 'Nombre', 'apellido', 'DNI'],
        ],

        'productos' => [
            'tabla'    => 'Productos',
            'titulo'   => 'Productos',
            'singular' => 'producto',
            'desc'     => 'Catálogo de la tienda: precios, stock e imágenes.',
            'labels'   => [
                'Fk_id_categoria' => 'Categoría',
                'Fk_id_marca'     => 'Marca',
                'Fk_id_proveedor' => 'Proveedor',
                'precio_compra'   => 'Precio de compra',
                'precio_venta'    => 'Precio de venta',
                'Stock_min'       => 'Stock mínimo',
                'stock_mac'       => 'Stock máximo',
                'stock_actual'    => 'Stock actual',
            ],
            'listado'  => ['id_producto', 'nombre', 'Fk_id_categoria', 'precio_venta', 'stock_actual'],
            'buscar'   => ['nombre', 'descripcion'],
            'imagen'   => 'imagen', // columna donde se guarda el archivo subido
        ],

        'categorias' => [
            'tabla'    => 'Categoria',
            'titulo'   => 'Categorías',
            'singular' => 'categoría',
            'desc'     => 'Categorías con las que se agrupan los productos.',
            'buscar'   => ['nombre_categoria'],
        ],

        'marcas' => [
            'tabla'    => 'Marca',
            'titulo'   => 'Marcas',
            'singular' => 'marca',
            'desc'     => 'Marcas de los productos de la tienda.',
            'buscar'   => ['nombre'],
        ],

        'proveedores' => [
            'tabla'    => 'Proveedor',
            'titulo'   => 'Proveedores',
            'singular' => 'proveedor',
            'desc'     => 'Proveedores que abastecen la tienda.',
            'buscar'   => ['nombre'],
        ],

        'membresias' => [
            'tabla'    => 'Membresias',
            'titulo'   => 'Membresías',
            'singular' => 'membresía',
            'desc'     => 'Planes que se le venden a los socios.',
            'buscar'   => ['membresia'],
        ],

        'clases' => [
            'tabla'    => 'Clases',
            'titulo'   => 'Clases',
            'singular' => 'clase',
            'desc'     => 'Clases y actividades que dicta el gimnasio.',
            'buscar'   => ['clase'],
        ],

        'ventas' => [
            'tabla'      => 'ventas_productos',
            'titulo'     => 'Ventas',
            'singular'   => 'venta',
            'desc'       => 'Ventas de la tienda online. Se pueden consultar y eliminar.',
            'sin_crear'  => true,
            'sin_editar' => true,
        ],

        'pagos' => [
            'tabla'    => 'Pagos',
            'titulo'   => 'Pagos',
            'singular' => 'pago',
            'desc'     => 'Pagos de membresías registrados en el sistema.',
        ],
    ];
}

/**
 * Columnas FK conocidas -> tabla a la que apuntan.
 * Se comparan siempre en minúsculas, así no importa si la columna se llama
 * "Fk_id_categoria" o "FK_id_Categoria".
 */
function adminMapaClavesForaneas(): array
{
    return [
        'fk_id_rol'        => 'Roles',
        'fk_id_cliente'    => 'clientes',
        'fk_id_clientes'   => 'clientes',
        'fk_id_empleado'   => 'empleados',
        'fk_id_usuario'    => 'usuarios',
        'fk_id_usuarios'   => 'usuarios',
        'fk_id_categoria'  => 'Categoria',
        'fk_id_marca'      => 'Marca',
        'fk_id_proveedor'  => 'Proveedor',
        'fk_id_clase'      => 'Clases',
        'fk_id_membresia'  => 'Membresias',
        'fk_id_producto'   => 'Productos',
        'fk_id_medio_pago' => 'Medio_de_pago',
    ];
}

/** Columnas candidatas para mostrar como "nombre" de un registro relacionado. */
function adminColumnasEtiqueta(): array
{
    return [
        'nombre_usuario', 'nombre_categoria', 'nombre', 'Nombre', 'membresia',
        'clase', 'Rol', 'rol', 'descripcion', 'titulo', 'razon_social',
    ];
}

function adminEntidad(string $clave): ?array
{
    $entidades = adminEntidades();
    if (!isset($entidades[$clave])) {
        return null;
    }
    $cfg = $entidades[$clave];
    $cfg['clave'] = $clave;
    return $cfg;
}

/* =============================================================
   2. Lectura de la estructura real de la base
   ============================================================= */

/** Valida un identificador (tabla/columna) antes de interpolarlo en el SQL. */
function adminIdentificadorSeguro(string $identificador): string
{
    if (!preg_match('/^[A-Za-z0-9_]+$/', $identificador)) {
        throw new InvalidArgumentException('Identificador inválido: ' . $identificador);
    }
    return '`' . $identificador . '`';
}

/**
 * Devuelve el nombre real de la tabla (respetando mayúsculas del servidor).
 * Si la tabla no existe, devuelve null en lugar de romper la página.
 */
function adminResolverTabla(PDO $pdo, string $tabla): ?string
{
    static $cache = null;

    if ($cache === null) {
        $cache = [];
        foreach ($pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_NUM) as $fila) {
            $cache[strtolower($fila[0])] = $fila[0];
        }
    }

    return $cache[strtolower($tabla)] ?? null;
}

/** Metadatos de cada columna de una tabla. */
function adminColumnas(PDO $pdo, string $tabla): array
{
    static $cache = [];
    if (isset($cache[$tabla])) {
        return $cache[$tabla];
    }

    $real = adminResolverTabla($pdo, $tabla);
    if ($real === null) {
        return $cache[$tabla] = [];
    }

    $columnas = [];
    $filas = $pdo->query('SHOW FULL COLUMNS FROM ' . adminIdentificadorSeguro($real))->fetchAll();

    foreach ($filas as $fila) {
        $tipoSql = strtolower($fila['Type']);
        $columnas[$fila['Field']] = [
            'nombre'     => $fila['Field'],
            'tipo_sql'   => $tipoSql,
            'tipo'       => adminTipoDeCampo($tipoSql),
            'opciones'   => adminOpcionesEnum($tipoSql),
            'maxlargo'   => adminLargoMaximo($tipoSql),
            'nulo'       => strtoupper($fila['Null']) === 'YES',
            'default'    => $fila['Default'],
            'pk'         => strtoupper($fila['Key']) === 'PRI',
            'auto'       => strpos(strtolower($fila['Extra']), 'auto_increment') !== false,
        ];
    }

    return $cache[$tabla] = $columnas;
}

function adminTipoDeCampo(string $tipoSql): string
{
    if (strpos($tipoSql, 'enum') === 0)                                  return 'enum';
    if (preg_match('/^(tinyint|smallint|mediumint|int|bigint)/', $tipoSql)) return 'entero';
    if (preg_match('/^(decimal|numeric|float|double)/', $tipoSql))       return 'decimal';
    if ($tipoSql === 'date')                                             return 'fecha';
    if (preg_match('/^(datetime|timestamp)/', $tipoSql))                 return 'fechahora';
    if (strpos($tipoSql, 'time') === 0)                                  return 'hora';
    if (preg_match('/(text|blob)/', $tipoSql))                           return 'texto_largo';
    return 'texto';
}

function adminOpcionesEnum(string $tipoSql): array
{
    if (strpos($tipoSql, 'enum') !== 0) {
        return [];
    }
    preg_match_all("/'((?:[^']|'')*)'/", $tipoSql, $coincidencias);
    return array_map(static fn($v) => str_replace("''", "'", $v), $coincidencias[1]);
}

function adminLargoMaximo(string $tipoSql): ?int
{
    if (preg_match('/^(var)?char\((\d+)\)/', $tipoSql, $m)) {
        return (int)$m[2];
    }
    return null;
}

/** Nombre de la clave primaria de la tabla. */
function adminClavePrimaria(PDO $pdo, array $cfg): ?string
{
    foreach (adminColumnas($pdo, $cfg['tabla']) as $col) {
        if ($col['pk']) {
            return $col['nombre'];
        }
    }
    return null;
}

/* =============================================================
   3. Campos del formulario
   ============================================================= */

/**
 * Devuelve la lista de campos editables de la entidad, ya enriquecidos con
 * etiqueta, tipo de input y (si corresponde) las opciones de su clave foránea.
 */
function adminCampos(PDO $pdo, array $cfg): array
{
    $mapaFk  = adminMapaClavesForaneas();
    $labels  = $cfg['labels'] ?? [];
    $claves  = $cfg['password'] ?? [];
    $ocultos = $cfg['ocultos'] ?? [];
    $campos  = [];

    foreach (adminColumnas($pdo, $cfg['tabla']) as $nombre => $col) {
        if ($col['auto'] || in_array($nombre, $ocultos, true)) {
            continue; // los IDs autoincrementales no se editan
        }

        $campo = $col;
        $campo['label']     = $labels[$nombre] ?? adminEtiquetaBonita($nombre);
        $campo['password']  = in_array($nombre, $claves, true);
        $campo['requerido'] = !$col['nulo'] && $col['default'] === null && !$campo['password'];
        $campo['fk']        = null;

        $tablaFk = $cfg['fk'][$nombre]['tabla'] ?? ($mapaFk[strtolower($nombre)] ?? null);
        if ($tablaFk !== null && adminResolverTabla($pdo, $tablaFk) !== null) {
            $campo['fk'] = [
                'tabla'    => $tablaFk,
                'opciones' => adminOpcionesRelacion($pdo, $tablaFk),
            ];
        }

        // La imagen del producto se sube con un <input type="file">, no a mano.
        if (($cfg['imagen'] ?? null) === $nombre) {
            $campo['tipo']      = 'archivo';
            $campo['requerido'] = false;
        }

        $campos[$nombre] = $campo;
    }

    return $campos;
}

/**
 * Largo de un texto. Usa mbstring si está disponible (acentos y ñ) y si no,
 * cae en strlen() para que el panel funcione igual.
 */
function adminLargoTexto(string $texto): int
{
    return function_exists('mb_strlen') ? mb_strlen($texto, 'UTF-8') : strlen($texto);
}

/** Recorta un texto agregando puntos suspensivos. */
function adminRecortar(string $texto, int $limite): string
{
    if (adminLargoTexto($texto) <= $limite) {
        return $texto;
    }
    $corte = function_exists('mb_substr')
        ? mb_substr($texto, 0, $limite, 'UTF-8')
        : substr($texto, 0, $limite);
    return $corte . '…';
}

function adminEtiquetaBonita(string $columna): string
{
    $texto = preg_replace('/^(fk_|pk_)/i', '', $columna);
    $texto = preg_replace('/^id_/i', '', $texto);
    $texto = str_replace('_', ' ', $texto);
    return ucfirst(trim($texto));
}

/**
 * Opciones (id => texto) para un <select> de clave foránea.
 * Se limita a 500 filas para no cargar de más en tablas grandes.
 */
function adminOpcionesRelacion(PDO $pdo, string $tabla): array
{
    static $cache = [];
    if (isset($cache[$tabla])) {
        return $cache[$tabla];
    }

    $real = adminResolverTabla($pdo, $tabla);
    if ($real === null) {
        return $cache[$tabla] = [];
    }

    $columnas = adminColumnas($pdo, $tabla);
    $pk = null;
    foreach ($columnas as $col) {
        if ($col['pk']) { $pk = $col['nombre']; break; }
    }
    if ($pk === null) {
        return $cache[$tabla] = [];
    }

    // Se buscan hasta dos columnas de texto para armar la etiqueta
    // (por ejemplo: "Nombre" + "apellido").
    $etiquetas = [];
    foreach (adminColumnasEtiqueta() as $candidata) {
        foreach ($columnas as $col) {
            if (strcasecmp($col['nombre'], $candidata) === 0 && !in_array($col['nombre'], $etiquetas, true)) {
                $etiquetas[] = $col['nombre'];
            }
        }
        if (count($etiquetas) >= 1) break;
    }
    foreach ($columnas as $col) {
        if (count($etiquetas) >= 2) break;
        if (strcasecmp($col['nombre'], 'apellido') === 0 && !in_array($col['nombre'], $etiquetas, true)) {
            $etiquetas[] = $col['nombre'];
        }
    }

    $select = adminIdentificadorSeguro($pk);
    foreach ($etiquetas as $etiqueta) {
        $select .= ', ' . adminIdentificadorSeguro($etiqueta);
    }

    $filas = $pdo->query(
        'SELECT ' . $select . ' FROM ' . adminIdentificadorSeguro($real) .
        ' ORDER BY ' . adminIdentificadorSeguro($etiquetas[0] ?? $pk) . ' ASC LIMIT 500'
    )->fetchAll();

    $opciones = [];
    foreach ($filas as $fila) {
        $partes = [];
        foreach ($etiquetas as $etiqueta) {
            if (trim((string)$fila[$etiqueta]) !== '') {
                $partes[] = $fila[$etiqueta];
            }
        }
        $texto = $partes ? implode(' ', $partes) : ('#' . $fila[$pk]);
        $opciones[(string)$fila[$pk]] = $texto . ' (#' . $fila[$pk] . ')';
    }

    return $cache[$tabla] = $opciones;
}

/* =============================================================
   4. Listado, búsqueda y paginado
   ============================================================= */

function adminColumnasListado(PDO $pdo, array $cfg): array
{
    $columnas = adminColumnas($pdo, $cfg['tabla']);

    // Si la configuración define un listado, se usan solo las columnas que existan.
    if (!empty($cfg['listado'])) {
        $elegidas = array_values(array_filter($cfg['listado'], static fn($c) => isset($columnas[$c])));
        if ($elegidas) {
            return $elegidas;
        }
    }

    // Si no, se muestran las primeras 6 columnas, salteando contraseñas.
    $claves = $cfg['password'] ?? [];
    $elegidas = [];
    foreach ($columnas as $nombre => $col) {
        if (in_array($nombre, $claves, true) || $col['tipo'] === 'texto_largo') {
            continue;
        }
        $elegidas[] = $nombre;
        if (count($elegidas) >= 6) break;
    }
    return $elegidas;
}

function adminListar(PDO $pdo, array $cfg, string $busqueda = '', int $pagina = 1, int $porPagina = 20): array
{
    $tabla = adminResolverTabla($pdo, $cfg['tabla']);
    if ($tabla === null) {
        return ['filas' => [], 'total' => 0, 'pagina' => 1, 'paginas' => 1, 'falta_tabla' => true];
    }

    $columnas = adminColumnas($pdo, $cfg['tabla']);
    $pk = adminClavePrimaria($pdo, $cfg);

    $where  = '';
    $params = [];
    $busqueda = trim($busqueda);

    if ($busqueda !== '') {
        $camposBusqueda = array_values(array_filter(
            $cfg['buscar'] ?? array_keys($columnas),
            static fn($c) => isset($columnas[$c]) && in_array($columnas[$c]['tipo'], ['texto', 'texto_largo'], true)
        ));
        if ($camposBusqueda) {
            $condiciones = [];
            foreach ($camposBusqueda as $i => $campo) {
                $condiciones[] = adminIdentificadorSeguro($campo) . ' LIKE :b' . $i;
                $params[':b' . $i] = '%' . $busqueda . '%';
            }
            $where = ' WHERE (' . implode(' OR ', $condiciones) . ')';
        }
    }

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM ' . adminIdentificadorSeguro($tabla) . $where);
    $stmt->execute($params);
    $total = (int)$stmt->fetchColumn();

    $paginas = max(1, (int)ceil($total / $porPagina));
    $pagina  = min(max(1, $pagina), $paginas);
    $offset  = ($pagina - 1) * $porPagina;

    $orden = $pk ? ' ORDER BY ' . adminIdentificadorSeguro($pk) . ' DESC' : '';
    $sql = 'SELECT * FROM ' . adminIdentificadorSeguro($tabla) . $where . $orden .
           ' LIMIT ' . (int)$porPagina . ' OFFSET ' . (int)$offset;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return [
        'filas'   => $stmt->fetchAll(),
        'total'   => $total,
        'pagina'  => $pagina,
        'paginas' => $paginas,
    ];
}

function adminObtener(PDO $pdo, array $cfg, $id): ?array
{
    $tabla = adminResolverTabla($pdo, $cfg['tabla']);
    $pk    = adminClavePrimaria($pdo, $cfg);
    if ($tabla === null || $pk === null) {
        return null;
    }

    $stmt = $pdo->prepare(
        'SELECT * FROM ' . adminIdentificadorSeguro($tabla) .
        ' WHERE ' . adminIdentificadorSeguro($pk) . ' = :id LIMIT 1'
    );
    $stmt->execute([':id' => $id]);
    $fila = $stmt->fetch();

    return $fila ?: null;
}

/* =============================================================
   5. Alta, modificación y baja
   ============================================================= */

/** Normaliza un valor del formulario según el tipo real de la columna. */
function adminNormalizarValor(array $campo, $valor)
{
    if ($valor === null) {
        return null;
    }
    $valor = is_string($valor) ? trim($valor) : $valor;

    if ($valor === '') {
        return $campo['nulo'] ? null : ($campo['tipo'] === 'entero' || $campo['tipo'] === 'decimal' ? 0 : '');
    }

    switch ($campo['tipo']) {
        case 'entero':    return (int)$valor;
        case 'decimal':   return (float)str_replace(',', '.', $valor);
        case 'fechahora': return str_replace('T', ' ', $valor);
        default:          return $valor;
    }
}

/** Valida los datos del formulario. Devuelve un array de errores (vacío si está todo bien). */
function adminValidar(array $campos, array $datos, bool $esEdicion): array
{
    $errores = [];

    foreach ($campos as $nombre => $campo) {
        if ($campo['tipo'] === 'archivo') {
            continue;
        }

        $valor = $datos[$nombre] ?? '';
        $valor = is_string($valor) ? trim($valor) : $valor;

        // En edición, una contraseña vacía significa "no cambiarla".
        if ($campo['password'] && $esEdicion && $valor === '') {
            continue;
        }
        if ($campo['password'] && !$esEdicion && strlen($valor) < 4) {
            $errores[] = 'La contraseña debe tener al menos 4 caracteres.';
            continue;
        }

        if ($campo['requerido'] && $valor === '') {
            $errores[] = 'El campo "' . $campo['label'] . '" es obligatorio.';
            continue;
        }
        if ($valor === '') {
            continue;
        }

        if ($campo['tipo'] === 'entero' && !preg_match('/^-?\d+$/', (string)$valor)) {
            $errores[] = 'El campo "' . $campo['label'] . '" debe ser un número entero.';
        }
        if ($campo['tipo'] === 'decimal' && !is_numeric(str_replace(',', '.', (string)$valor))) {
            $errores[] = 'El campo "' . $campo['label'] . '" debe ser un número.';
        }
        if ($campo['maxlargo'] && adminLargoTexto((string)$valor) > $campo['maxlargo']) {
            $errores[] = 'El campo "' . $campo['label'] . '" no puede superar los ' . $campo['maxlargo'] . ' caracteres.';
        }
        if ($campo['opciones'] && !in_array((string)$valor, $campo['opciones'], true)) {
            $errores[] = 'El valor elegido para "' . $campo['label'] . '" no es válido.';
        }
        if (strcasecmp($nombre, 'email') === 0 && !filter_var($valor, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El email no tiene un formato válido.';
        }
    }

    return $errores;
}

/**
 * Inserta o actualiza un registro.
 * Devuelve el ID del registro guardado.
 */
function adminGuardar(PDO $pdo, array $cfg, array $campos, array $datos, $id = null)
{
    $tabla     = adminResolverTabla($pdo, $cfg['tabla']);
    $pk        = adminClavePrimaria($pdo, $cfg);
    $esEdicion = $id !== null && $id !== '';

    $valores = [];
    foreach ($campos as $nombre => $campo) {
        if ($campo['tipo'] === 'archivo') {
            continue; // se resuelve aparte, al subir el archivo
        }
        if (!array_key_exists($nombre, $datos)) {
            continue;
        }

        $valor = $datos[$nombre];

        if ($campo['password']) {
            if (trim((string)$valor) === '') {
                continue; // no se toca la contraseña actual
            }
            $valores[$nombre] = password_hash($valor, PASSWORD_BCRYPT);
            continue;
        }

        $valores[$nombre] = adminNormalizarValor($campo, $valor);
    }

    if (!$valores) {
        throw new RuntimeException('No se recibió ningún dato para guardar.');
    }

    if ($esEdicion) {
        $asignaciones = [];
        $params = [];
        foreach ($valores as $columna => $valor) {
            $asignaciones[] = adminIdentificadorSeguro($columna) . ' = :v_' . $columna;
            $params[':v_' . $columna] = $valor;
        }
        $params[':pk'] = $id;

        $sql = 'UPDATE ' . adminIdentificadorSeguro($tabla) .
               ' SET ' . implode(', ', $asignaciones) .
               ' WHERE ' . adminIdentificadorSeguro($pk) . ' = :pk';
        $pdo->prepare($sql)->execute($params);

        return $id;
    }

    $columnas = array_map('adminIdentificadorSeguro', array_keys($valores));
    $marcas   = [];
    $params   = [];
    foreach ($valores as $columna => $valor) {
        $marcas[] = ':v_' . $columna;
        $params[':v_' . $columna] = $valor;
    }

    $sql = 'INSERT INTO ' . adminIdentificadorSeguro($tabla) .
           ' (' . implode(', ', $columnas) . ') VALUES (' . implode(', ', $marcas) . ')';
    $pdo->prepare($sql)->execute($params);

    return $pdo->lastInsertId();
}

function adminEliminar(PDO $pdo, array $cfg, $id): void
{
    $tabla = adminResolverTabla($pdo, $cfg['tabla']);
    $pk    = adminClavePrimaria($pdo, $cfg);
    if ($tabla === null || $pk === null) {
        throw new RuntimeException('No se pudo identificar el registro a eliminar.');
    }

    $stmt = $pdo->prepare(
        'DELETE FROM ' . adminIdentificadorSeguro($tabla) .
        ' WHERE ' . adminIdentificadorSeguro($pk) . ' = :id'
    );
    $stmt->execute([':id' => $id]);
}

/* =============================================================
   6. Imágenes de producto
   ============================================================= */

/**
 * Guarda la imagen subida como "{id}.{extension}" dentro de uploads/productos/,
 * que es la misma convención que ya usaba el proyecto.
 */
function adminGuardarImagen(PDO $pdo, array $cfg, $id, array $archivo): ?string
{
    if (empty($archivo['name']) || $archivo['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $permitidas = ['jpg', 'jpeg', 'png', 'webp'];
    $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $permitidas, true)) {
        throw new RuntimeException('Formato de imagen no permitido. Usá JPG, PNG o WEBP.');
    }
    if ($archivo['size'] > 3 * 1024 * 1024) {
        throw new RuntimeException('La imagen no puede pesar más de 3 MB.');
    }

    $carpeta = __DIR__ . '/../uploads/productos/';
    if (!is_dir($carpeta)) {
        mkdir($carpeta, 0775, true);
    }

    $nombreArchivo = $id . '.' . $ext;
    if (!move_uploaded_file($archivo['tmp_name'], $carpeta . $nombreArchivo)) {
        throw new RuntimeException('No se pudo guardar la imagen en el servidor.');
    }

    $tabla = adminResolverTabla($pdo, $cfg['tabla']);
    $pk    = adminClavePrimaria($pdo, $cfg);
    $pdo->prepare(
        'UPDATE ' . adminIdentificadorSeguro($tabla) .
        ' SET ' . adminIdentificadorSeguro($cfg['imagen']) . ' = :img' .
        ' WHERE ' . adminIdentificadorSeguro($pk) . ' = :id'
    )->execute([':img' => $nombreArchivo, ':id' => $id]);

    return $nombreArchivo;
}

/* =============================================================
   7. Estadísticas del tablero
   ============================================================= */

function adminContar(PDO $pdo, string $tabla): ?int
{
    $real = adminResolverTabla($pdo, $tabla);
    if ($real === null) {
        return null;
    }
    try {
        return (int)$pdo->query('SELECT COUNT(*) FROM ' . adminIdentificadorSeguro($real))->fetchColumn();
    } catch (PDOException $e) {
        return null;
    }
}

function adminSumar(PDO $pdo, string $tabla, string $columna): float
{
    $real = adminResolverTabla($pdo, $tabla);
    if ($real === null || !isset(adminColumnas($pdo, $tabla)[$columna])) {
        return 0.0;
    }
    try {
        return (float)$pdo->query(
            'SELECT COALESCE(SUM(' . adminIdentificadorSeguro($columna) . '),0) FROM ' . adminIdentificadorSeguro($real)
        )->fetchColumn();
    } catch (PDOException $e) {
        return 0.0;
    }
}

function adminEstadisticas(PDO $pdo): array
{
    return [
        'usuarios'   => adminContar($pdo, 'usuarios'),
        'clientes'   => adminContar($pdo, 'clientes'),
        'empleados'  => adminContar($pdo, 'empleados'),
        'productos'  => adminContar($pdo, 'Productos'),
        'membresias' => adminContar($pdo, 'Membresias'),
        'ventas'     => adminContar($pdo, 'ventas_productos'),
        'ingresos'   => adminSumar($pdo, 'Pagos_productos', 'total') + adminSumar($pdo, 'Pagos', 'Monto'),
    ];
}

/** Productos cuyo stock actual quedó por debajo del mínimo. */
function adminProductosStockBajo(PDO $pdo, int $limite = 5): array
{
    $columnas = adminColumnas($pdo, 'Productos');
    if (!isset($columnas['stock_actual'])) {
        return [];
    }

    $condicion = isset($columnas['Stock_min'])
        ? 'stock_actual <= Stock_min'
        : 'stock_actual <= 5';

    try {
        return $pdo->query(
            'SELECT id_producto, nombre, stock_actual' .
            (isset($columnas['Stock_min']) ? ', Stock_min' : '') .
            ' FROM ' . adminIdentificadorSeguro(adminResolverTabla($pdo, 'Productos')) .
            ' WHERE ' . $condicion . ' ORDER BY stock_actual ASC LIMIT ' . (int)$limite
        )->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

/** Últimas ventas registradas (para el tablero). */
function adminUltimasVentas(PDO $pdo, int $limite = 5): array
{
    if (adminResolverTabla($pdo, 'ventas_productos') === null) {
        return [];
    }
    try {
        return $pdo->query(
            "SELECT v.id_venta_producto, v.fecha_venta, u.nombre_usuario,
                    COALESCE((SELECT pp.total FROM Pagos_productos pp
                              WHERE pp.Fk_id_venta_producto = v.id_venta_producto
                              ORDER BY pp.id_Pagos_productos DESC LIMIT 1), 0) AS total
             FROM ventas_productos v
             LEFT JOIN usuarios u ON u.id_usuarios = v.Fk_id_usuario
             ORDER BY v.fecha_venta DESC, v.id_venta_producto DESC
             LIMIT " . (int)$limite
        )->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

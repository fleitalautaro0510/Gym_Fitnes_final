<?php
require_once __DIR__ . '/../Modelo/AdminModel.php';

/**
 * AdminControlador
 * ---------------------------------------------------------------
 * Panel de administración (rol 1). Permite listar, crear, editar y eliminar
 * los registros de todas las tablas configuradas en AdminModel::adminEntidades().
 *
 * Rutas (index.php?action=...):
 *   admin_dashboard  -> tablero con estadísticas
 *   admin_listar     -> listado de una entidad     (&ent=productos)
 *   admin_nuevo      -> formulario de alta         (&ent=productos)
 *   admin_editar     -> formulario de edición      (&ent=productos&id=5)
 *   admin_guardar    -> procesa el alta/edición    (POST)
 *   admin_eliminar   -> borra un registro          (POST)
 *   acceso_denegado  -> aviso de permisos
 */
class AdminControlador
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /* ---------------------------------------------------------
       Seguridad
       --------------------------------------------------------- */

    /** Corta la ejecución si el usuario no es administrador (rol 1). */
    private function requiereAdmin(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        if ((int)($_SESSION['user_rol'] ?? 0) !== 1) {
            header('Location: index.php?action=acceso_denegado');
            exit;
        }
    }

    /** Token anti-CSRF para los formularios que modifican datos. */
    public static function token(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['admin_token'])) {
            $_SESSION['admin_token'] = bin2hex(random_bytes(16));
        }
        return $_SESSION['admin_token'];
    }

    private function verificarToken(): void
    {
        $enviado = $_POST['token'] ?? '';
        if (!hash_equals(self::token(), $enviado)) {
            $this->mensaje('error', 'La sesión del formulario expiró. Probá de nuevo.');
            header('Location: index.php?action=admin_dashboard');
            exit;
        }
    }

    /** Guarda un mensaje para mostrarlo después de una redirección. */
    private function mensaje(string $tipo, string $texto): void
    {
        $_SESSION['admin_flash'] = ['tipo' => $tipo, 'texto' => $texto];
    }

    public static function tomarMensaje(): ?array
    {
        if (empty($_SESSION['admin_flash'])) {
            return null;
        }
        $flash = $_SESSION['admin_flash'];
        unset($_SESSION['admin_flash']);
        return $flash;
    }

    /** Devuelve la configuración de la entidad pedida por GET/POST. */
    private function entidadPedida(): array
    {
        $clave = $_GET['ent'] ?? $_POST['ent'] ?? '';
        $cfg   = adminEntidad((string)$clave);

        if ($cfg === null) {
            $this->mensaje('error', 'La sección solicitada no existe.');
            header('Location: index.php?action=admin_dashboard');
            exit;
        }
        return $cfg;
    }

    /* ---------------------------------------------------------
       Vistas
       --------------------------------------------------------- */

    /** Tablero principal. */
    public function panel(): void
    {
        $this->requiereAdmin();

        $entidades    = adminEntidades();
        $stats        = adminEstadisticas($this->pdo);
        $stockBajo    = adminProductosStockBajo($this->pdo);
        $ultimasVentas = adminUltimasVentas($this->pdo);
        $flash        = self::tomarMensaje();
        $pdo          = $this->pdo;

        require __DIR__ . '/../Vistas/admin/dashboard.php';
    }

    /** Listado de registros de una entidad, con búsqueda y paginado. */
    public function listar(): void
    {
        $this->requiereAdmin();

        $cfg       = $this->entidadPedida();
        $busqueda  = trim((string)($_GET['q'] ?? ''));
        $pagina    = max(1, (int)($_GET['pagina'] ?? 1));
        $resultado = adminListar($this->pdo, $cfg, $busqueda, $pagina);

        $columnas   = adminColumnasListado($this->pdo, $cfg);
        $campos     = adminCampos($this->pdo, $cfg);
        $pk         = adminClavePrimaria($this->pdo, $cfg);
        $entidades  = adminEntidades();
        $flash      = self::tomarMensaje();
        $token      = self::token();
        $pdo        = $this->pdo;

        require __DIR__ . '/../Vistas/admin/lista.php';
    }

    /** Formulario de alta. */
    public function nuevo(): void
    {
        $this->requiereAdmin();

        $cfg = $this->entidadPedida();
        if (!empty($cfg['sin_crear'])) {
            $this->mensaje('error', 'Esta sección no permite crear registros nuevos.');
            header('Location: index.php?action=admin_listar&ent=' . urlencode($cfg['clave']));
            exit;
        }

        $campos    = adminCampos($this->pdo, $cfg);
        $registro  = $_SESSION['admin_form_datos'] ?? [];
        $errores   = $_SESSION['admin_form_errores'] ?? [];
        unset($_SESSION['admin_form_datos'], $_SESSION['admin_form_errores']);

        $esEdicion = false;
        $entidades = adminEntidades();
        $token     = self::token();
        $pdo       = $this->pdo;

        require __DIR__ . '/../Vistas/admin/formulario.php';
    }

    /** Formulario de edición. */
    public function editar(): void
    {
        $this->requiereAdmin();

        $cfg = $this->entidadPedida();
        if (!empty($cfg['sin_editar'])) {
            $this->mensaje('error', 'Esta sección es de solo lectura.');
            header('Location: index.php?action=admin_listar&ent=' . urlencode($cfg['clave']));
            exit;
        }

        $id       = $_GET['id'] ?? null;
        $registro = $id !== null ? adminObtener($this->pdo, $cfg, $id) : null;

        if ($registro === null) {
            $this->mensaje('error', 'No se encontró el registro que querés editar.');
            header('Location: index.php?action=admin_listar&ent=' . urlencode($cfg['clave']));
            exit;
        }

        // Si venimos de un intento fallido de guardado, se conservan los datos escritos.
        if (!empty($_SESSION['admin_form_datos'])) {
            $registro = array_merge($registro, $_SESSION['admin_form_datos']);
        }
        $errores = $_SESSION['admin_form_errores'] ?? [];
        unset($_SESSION['admin_form_datos'], $_SESSION['admin_form_errores']);

        $campos    = adminCampos($this->pdo, $cfg);
        $esEdicion = true;
        $entidades = adminEntidades();
        $token     = self::token();
        $pdo       = $this->pdo;

        require __DIR__ . '/../Vistas/admin/formulario.php';
    }

    /* ---------------------------------------------------------
       Acciones que modifican datos
       --------------------------------------------------------- */

    /** Procesa el alta o la edición. */
    public function guardar(): void
    {
        $this->requiereAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=admin_dashboard');
            exit;
        }
        $this->verificarToken();

        $cfg       = $this->entidadPedida();
        $id        = ($_POST['__id'] ?? '') !== '' ? $_POST['__id'] : null;
        $esEdicion = $id !== null;
        $campos    = adminCampos($this->pdo, $cfg);

        if ($esEdicion && !empty($cfg['sin_editar'])) {
            $this->mensaje('error', 'Esta sección es de solo lectura.');
            header('Location: index.php?action=admin_listar&ent=' . urlencode($cfg['clave']));
            exit;
        }
        if (!$esEdicion && !empty($cfg['sin_crear'])) {
            $this->mensaje('error', 'Esta sección no permite crear registros nuevos.');
            header('Location: index.php?action=admin_listar&ent=' . urlencode($cfg['clave']));
            exit;
        }

        // Solo se toman del POST los campos que realmente existen en la tabla.
        $datos = [];
        foreach ($campos as $nombre => $campo) {
            if (array_key_exists($nombre, $_POST)) {
                $datos[$nombre] = $_POST[$nombre];
            } elseif ($campo['tipo'] === 'entero' && $campo['tipo_sql'] === 'tinyint(1)') {
                $datos[$nombre] = 0; // checkbox no marcado
            }
        }

        $errores = adminValidar($campos, $datos, $esEdicion);

        if ($errores) {
            $_SESSION['admin_form_datos']   = $datos;
            $_SESSION['admin_form_errores'] = $errores;
            $destino = $esEdicion
                ? 'index.php?action=admin_editar&ent=' . urlencode($cfg['clave']) . '&id=' . urlencode((string)$id)
                : 'index.php?action=admin_nuevo&ent=' . urlencode($cfg['clave']);
            header('Location: ' . $destino);
            exit;
        }

        try {
            $nuevoId = adminGuardar($this->pdo, $cfg, $campos, $datos, $id);

            if (!empty($cfg['imagen']) && !empty($_FILES['__imagen']['name'])) {
                adminGuardarImagen($this->pdo, $cfg, $nuevoId, $_FILES['__imagen']);
            }

            $this->mensaje('ok', $esEdicion
                ? ucfirst($cfg['singular']) . ' actualizado correctamente.'
                : ucfirst($cfg['singular']) . ' creado correctamente.');
        } catch (PDOException $e) {
            $_SESSION['admin_form_datos']   = $datos;
            $_SESSION['admin_form_errores'] = [$this->mensajeDeError($e)];
            $destino = $esEdicion
                ? 'index.php?action=admin_editar&ent=' . urlencode($cfg['clave']) . '&id=' . urlencode((string)$id)
                : 'index.php?action=admin_nuevo&ent=' . urlencode($cfg['clave']);
            header('Location: ' . $destino);
            exit;
        } catch (RuntimeException $e) {
            $this->mensaje('error', $e->getMessage());
        }

        header('Location: index.php?action=admin_listar&ent=' . urlencode($cfg['clave']));
        exit;
    }

    /** Elimina un registro. */
    public function eliminar(): void
    {
        $this->requiereAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=admin_dashboard');
            exit;
        }
        $this->verificarToken();

        $cfg = $this->entidadPedida();
        $id  = $_POST['__id'] ?? null;

        if ($id === null || $id === '') {
            $this->mensaje('error', 'No se indicó qué registro eliminar.');
            header('Location: index.php?action=admin_listar&ent=' . urlencode($cfg['clave']));
            exit;
        }

        // Un admin no puede borrarse a sí mismo (quedaría sin poder entrar).
        if ($cfg['clave'] === 'usuarios' && (string)$id === (string)$_SESSION['user_id']) {
            $this->mensaje('error', 'No podés eliminar tu propio usuario mientras estás conectado.');
            header('Location: index.php?action=admin_listar&ent=usuarios');
            exit;
        }

        try {
            adminEliminar($this->pdo, $cfg, $id);
            $this->mensaje('ok', ucfirst($cfg['singular']) . ' eliminado correctamente.');
        } catch (PDOException $e) {
            $this->mensaje('error', $this->mensajeDeError($e));
        } catch (RuntimeException $e) {
            $this->mensaje('error', $e->getMessage());
        }

        header('Location: index.php?action=admin_listar&ent=' . urlencode($cfg['clave']));
        exit;
    }

    /** Traduce los errores de MySQL a algo entendible. */
    private function mensajeDeError(PDOException $e): string
    {
        $codigo = $e->errorInfo[1] ?? 0;

        switch ($codigo) {
            case 1451:
                return 'No se puede eliminar: el registro está siendo usado por otra tabla ' .
                       '(por ejemplo, un producto que ya figura en una venta). Eliminá primero esas relaciones.';
            case 1452:
                return 'El registro relacionado que elegiste no existe. Revisá los campos de tipo "relación".';
            case 1062:
                return 'Ya existe un registro con ese valor único (por ejemplo, un usuario, email o DNI repetido).';
            case 1406:
                return 'Alguno de los textos ingresados es más largo de lo que permite la base de datos.';
            default:
                return 'Error de base de datos: ' . $e->getMessage();
        }
    }

    /** Pantalla de acceso denegado. */
    public function accesoDenegado(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        http_response_code(403);
        require __DIR__ . '/../Vistas/admin/acceso_denegado.php';
    }
}

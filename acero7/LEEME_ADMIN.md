# Panel de administración — ACERO GYM

Módulo de administración agregado al proyecto. Permite **listar, crear, editar y eliminar**
los registros de todo el sistema desde una sola interfaz, con el mismo estilo visual del sitio.

---

## Cómo entrar

1. Copiá la carpeta `acero4` en `htdocs` (XAMPP) como ya la tenías.
2. Asegurate de que la base `mydb` esté importada y que la tabla `Roles` tenga el rol
   **1 = Administrador**.
3. Entrá a `http://localhost/acero4/index.php?action=login` con un usuario cuyo `FK_id_rol` sea `1`.
4. Al iniciar sesión te lleva solo al panel. También aparece el botón **"Panel admin"**
   en el menú del sitio público.

Si no tenés un usuario administrador, podés crear uno desde phpMyAdmin. La contraseña se
guarda encriptada con `password_hash()`, así que conviene crear el usuario desde el mismo
panel (o generar el hash con PHP) en lugar de escribirla en texto plano.

---

## Qué se puede administrar

| Sección | Tabla | Alta | Edición | Baja |
|---|---|:--:|:--:|:--:|
| Usuarios | `usuarios` | ✔ | ✔ | ✔ |
| Clientes | `clientes` | ✔ | ✔ | ✔ |
| Empleados | `empleados` | ✔ | ✔ | ✔ |
| Productos | `Productos` | ✔ | ✔ | ✔ |
| Categorías | `Categoria` | ✔ | ✔ | ✔ |
| Marcas | `Marca` | ✔ | ✔ | ✔ |
| Proveedores | `Proveedor` | ✔ | ✔ | ✔ |
| Membresías | `Membresias` | ✔ | ✔ | ✔ |
| Clases | `Clases` | ✔ | ✔ | ✔ |
| Ventas | `ventas_productos` | — | — | ✔ |
| Pagos | `Pagos` | ✔ | ✔ | ✔ |

Cada sección incluye buscador y paginado (20 registros por página).

---

## Archivos agregados

```
admin.css                              Estilos del panel
LEEME_ADMIN.md                         Este archivo
Modelo/AdminModel.php                  Lógica de datos (CRUD genérico)
Controlador/AdminControlador.php       Rutas, permisos y validaciones
Controlador/verificar_admin.php        Protección para páginas sueltas
Vistas/admin/dashboard.php             Tablero con estadísticas
Vistas/admin/lista.php                 Listado con editar / eliminar
Vistas/admin/formulario.php            Alta y edición
Vistas/admin/acceso_denegado.php       Pantalla de error 403
Vistas/admin/parciales/header_admin.php
Vistas/admin/parciales/footer_admin.php
```

## Archivos modificados

- **`index.php`** — se agregaron las rutas del panel. (Tu `AuthControlador` ya redirigía a
  `admin_dashboard` al loguear un rol 1, pero esa ruta no existía en el router, por eso
  el administrador terminaba siempre de vuelta en el login.)
- **`Vistas/parciales/header_publico.php`** — botón "Panel admin" para el rol 1.
- **`Vistas/productos.php`**, **`Vistas/producto_form.php`**,
  **`Controlador/guardar_producto.php`**, **`Controlador/eliminar_producto.php`** —
  estas cuatro páginas estaban accesibles **sin iniciar sesión**: cualquiera que escribiera
  la URL en el navegador podía editar o borrar productos. Ahora exigen rol de administrador.

---

## Rutas disponibles

| URL | Qué hace |
|---|---|
| `index.php?action=admin_dashboard` | Tablero con estadísticas |
| `index.php?action=admin_listar&ent=productos` | Listado de una sección |
| `index.php?action=admin_nuevo&ent=productos` | Formulario de alta |
| `index.php?action=admin_editar&ent=productos&id=5` | Formulario de edición |
| `index.php?action=admin_guardar` | Procesa el alta/edición (POST) |
| `index.php?action=admin_eliminar` | Borra un registro (POST) |
| `index.php?action=acceso_denegado` | Aviso de permisos insuficientes |

---

## Cómo está hecho (para explicarlo en la defensa del proyecto)

El panel **no** tiene un ABM escrito a mano por cada tabla. En su lugar, `AdminModel.php`
consulta la estructura real de la base con `SHOW FULL COLUMNS` y con eso arma solo:

- los **campos del formulario** y su tipo de input (número, fecha, lista desplegable, textarea…),
- qué campos son **obligatorios** (según `NOT NULL` y si tienen valor por defecto),
- las **listas desplegables de claves foráneas**, mostrando el nombre del registro
  relacionado en lugar del número de ID,
- las **columnas del listado**.

La ventaja es que si mañana agregás una columna a una tabla, aparece sola en el panel sin
tocar código. Lo único que se configura a mano es qué tablas se administran y sus etiquetas,
en la función `adminEntidades()` al principio del archivo.

### Seguridad

- Todas las consultas usan **sentencias preparadas** (PDO) con parámetros.
- Los nombres de tablas y columnas nunca se toman del usuario: se validan contra la
  estructura real de la base antes de usarse en el SQL.
- Todas las páginas verifican **sesión iniciada + rol 1**.
- Los formularios que modifican datos llevan **token anti-CSRF**.
- Las contraseñas se guardan con `password_hash()`. Al editar un usuario, si dejás el campo
  contraseña vacío, se mantiene la que ya tenía.
- Un administrador **no puede eliminar su propio usuario** mientras está conectado.
- Todo lo que se muestra pasa por `htmlspecialchars()` (evita inyección de HTML/JS).

### Errores de base de datos traducidos

En lugar del mensaje técnico de MySQL, el panel muestra explicaciones claras:

| Código | Mensaje que ve el usuario |
|---|---|
| 1451 | No se puede eliminar: el registro está siendo usado por otra tabla… |
| 1452 | El registro relacionado que elegiste no existe… |
| 1062 | Ya existe un registro con ese valor único (usuario, email o DNI repetido) |
| 1406 | Alguno de los textos es más largo de lo que permite la base |

---

## Notas

- **Imágenes de productos:** se suben desde el formulario y se guardan en
  `uploads/productos/` con el nombre `{id}.jpg` (o `.png` / `.webp`), que es la misma
  convención que ya usaba el proyecto. Máximo 3 MB.
- **Nombres de tablas:** el panel busca cada tabla sin distinguir mayúsculas de minúsculas,
  así que funciona igual si en tu base figuran como `Usuarios` o `usuarios`. Si una tabla no
  existe, esa sección avisa con un cartel en vez de romperse.
- **Rol 3 (empleados):** por ahora `empleado_dashboard` los manda a la tienda. Si querés que
  tengan un panel propio con permisos reducidos, se puede reutilizar este mismo módulo
  limitando las secciones visibles.

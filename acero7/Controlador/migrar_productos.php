<?php
/**
 * migrar_productos.php
 * ---------------------------------------------------------------------
 * Script de UNA SOLA VEZ. Se ejecuta abriendo esta URL en el navegador:
 *
 *      http://localhost/acero3/Controlador/migrar_productos.php
 *
 * Hace dos cosas, y las dos son seguras de ejecutar más de una vez
 * (no duplica nada si lo volvés a correr):
 *
 *   1) Agrega a la tabla `Productos` las columnas que le faltaban para
 *      poder mostrar descripción, características e imagen:
 *        - descripcion     TEXT
 *        - caracteristicas TEXT   (una característica por línea)
 *        - imagen          VARCHAR(255)
 *
 *   2) Crea (si no existen) las categorías de la tienda y carga ~20
 *      productos por categoría: una categoría de CONSUMOS para
 *      entrenamiento (suplementos/nutrición) y otras categorías con
 *      productos/equipamiento para entrenar.
 *
 * Una vez que corriste este script y viste el mensaje de OK, podés
 * borrarlo o dejarlo (si lo volvés a abrir no rompe nada, simplemente
 * no vuelve a insertar lo que ya existe).
 * ---------------------------------------------------------------------
 */

require_once __DIR__ . '/conexion.php';

header('Content-Type: text/html; charset=utf-8');
echo "<!doctype html><html lang='es'><head><meta charset='utf-8'>
<title>Migración de productos | Acero Gym</title>
<style>
body{font-family:system-ui,Arial,sans-serif;background:#0a0a0c;color:#f4f3ef;padding:32px;line-height:1.6}
h1{color:#ffb703} h2{color:#ffb703;margin-top:32px}
.ok{color:#7CFC9A} .skip{color:#9a9a9e} .err{color:#ff6b6b}
code{background:#1b1b1f;padding:2px 6px;border-radius:3px}
</style></head><body>";
echo "<h1>Migración y carga de productos — Acero Gym</h1>";

/* =====================================================================
   PASO 1: agregar columnas nuevas a Productos (si no existen)
   ===================================================================== */
echo "<h2>1) Columnas de la tabla Productos</h2><ul>";

function columnaExiste(PDO $pdo, string $tabla, string $columna): bool
{
    $stmt = $pdo->prepare(
        "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :tabla AND COLUMN_NAME = :columna"
    );
    $stmt->execute([':tabla' => $tabla, ':columna' => $columna]);
    return (int)$stmt->fetchColumn() > 0;
}

function agregarColumnaSiNoExiste(PDO $pdo, string $tabla, string $columna, string $definicion): void
{
    if (columnaExiste($pdo, $tabla, $columna)) {
        echo "<li class='skip'>La columna <code>{$columna}</code> ya existe. Se omite.</li>";
        return;
    }
    $pdo->exec("ALTER TABLE `{$tabla}` ADD COLUMN `{$columna}` {$definicion}");
    echo "<li class='ok'>Columna <code>{$columna}</code> agregada correctamente.</li>";
}

agregarColumnaSiNoExiste($pdo, 'Productos', 'descripcion', 'VARCHAR(500) NULL AFTER nombre');
agregarColumnaSiNoExiste($pdo, 'Productos', 'caracteristicas', 'TEXT NULL AFTER descripcion');
agregarColumnaSiNoExiste($pdo, 'Productos', 'imagen', 'VARCHAR(255) NULL AFTER caracteristicas');
echo "</ul>";

/* =====================================================================
   PASO 2: categorías y marcas
   ===================================================================== */
function idCategoria(PDO $pdo, string $nombre, string $descripcion): int
{
    $stmt = $pdo->prepare("SELECT id_categoria FROM Categoria WHERE nombre_categoria = :n");
    $stmt->execute([':n' => $nombre]);
    $id = $stmt->fetchColumn();
    if ($id) return (int)$id;

    $cols = 'nombre_categoria';
    $vals = ':n';
    $params = [':n' => $nombre];
    if (columnaExiste($pdo, 'Categoria', 'descripcion')) {
        $cols .= ', descripcion';
        $vals .= ', :d';
        $params[':d'] = $descripcion;
    }
    $pdo->prepare("INSERT INTO Categoria ({$cols}) VALUES ({$vals})")->execute($params);
    return (int)$pdo->lastInsertId();
}

function idMarca(PDO $pdo, string $nombre): ?int
{
    if ($nombre === '') return null;
    $stmt = $pdo->prepare("SELECT id_Marca FROM Marca WHERE nombre = :n");
    $stmt->execute([':n' => $nombre]);
    $id = $stmt->fetchColumn();
    if ($id) return (int)$id;
    $pdo->prepare("INSERT INTO Marca (nombre) VALUES (:n)")->execute([':n' => $nombre]);
    return (int)$pdo->lastInsertId();
}

echo "<h2>2) Categorías</h2><ul>";
$idConsumos     = idCategoria($pdo, 'Suplementos y Nutrición', 'Todo lo que consumís antes, durante y después de entrenar: proteínas, creatina, pre-entrenos, bebidas e hidratación.');
$idAccesorios   = idCategoria($pdo, 'Accesorios de Entrenamiento', 'Guantes, cinturones, straps, bandas y demás accesorios para mejorar cada entrenamiento.');
$idEquipamiento = idCategoria($pdo, 'Equipamiento de Fitness', 'Mancuernas, kettlebells, discos y equipamiento para entrenar en el gimnasio o en casa.');
$idIndumentaria = idCategoria($pdo, 'Indumentaria Deportiva', 'Remeras, calzas, buzos y calzado pensados para moverte con comodidad.');
foreach ([
    'Suplementos y Nutrición' => $idConsumos,
    'Accesorios de Entrenamiento' => $idAccesorios,
    'Equipamiento de Fitness' => $idEquipamiento,
    'Indumentaria Deportiva' => $idIndumentaria,
] as $nombre => $id) {
    echo "<li class='ok'>{$nombre} → id_categoria = {$id}</li>";
}
echo "</ul>";

/* =====================================================================
   PASO 3: productos (~20 por categoría)
   Formato de cada fila:
   [nombre, marca, precio_compra, precio_venta, stock_actual, stock_min, stock_max, descripcion, [caracteristicas...]]
   ===================================================================== */

$productos = [];

// ---------- CONSUMOS PARA ENTRENAMIENTO (Suplementos y Nutrición) ----------
$productos[$idConsumos] = [
    ['Proteína Whey Concentrada 1kg', 'Acero Nutrition', 9000, 15990, 40, 5, 80,
        'Proteína de suero concentrada ideal para acompañar la recuperación muscular después de entrenar.',
        ['24g de proteína por porción', 'Mezcla instantánea, sin grumos', 'Sabor chocolate', 'Rinde 33 porciones']],
    ['Proteína Whey Aislada 900g', 'Acero Nutrition', 11500, 18990, 30, 5, 60,
        'Proteína aislada de rápida absorción, con muy bajo contenido de grasas y lactosa.',
        ['27g de proteína por porción', 'Bajo en grasas y carbohidratos', 'Ideal post-entrenamiento', 'Sabor vainilla']],
    ['Proteína Vegana de Guisante 900g', 'GreenFit', 10500, 17490, 20, 3, 40,
        'Proteína 100% vegetal a base de guisante, apta para dietas veganas y vegetarianas.',
        ['22g de proteína por porción', '100% de origen vegetal', 'Sin lactosa ni gluten', 'Sabor cacao natural']],
    ['Creatina Monohidratada 300g', 'Acero Nutrition', 5200, 8990, 50, 10, 100,
        'Creatina monohidratada micronizada para ganar fuerza y potencia en el entrenamiento.',
        ['5g de creatina pura por dosis', 'Micronizada, se disuelve fácil', 'Sin sabor, apta para mezclar', 'Rinde 60 dosis']],
    ['Creatina Monohidratada 500g', 'Acero Nutrition', 7800, 12990, 35, 8, 70,
        'Presentación grande de creatina monohidratada para varios meses de suplementación.',
        ['5g de creatina pura por dosis', 'Rinde 100 dosis', 'Aumenta el rendimiento en esfuerzos cortos', 'Sin sabor']],
    ['BCAA 2:1:1 en polvo 300g', 'ProFit', 6200, 10490, 30, 5, 60,
        'Aminoácidos ramificados en proporción 2:1:1 para reducir el catabolismo muscular.',
        ['Relación 2:1:1 de leucina, isoleucina y valina', 'Ayuda a la recuperación muscular', 'Sabor frutos rojos', 'Apto para tomar durante el entreno']],
    ['Glutamina 300g', 'ProFit', 5900, 9990, 25, 5, 50,
        'Glutamina pura en polvo, útil para la recuperación y el sistema inmune del deportista.',
        ['5g de glutamina por dosis', 'Favorece la recuperación muscular', 'Sin sabor', 'Rinde 60 dosis']],
    ['Pre-entreno Explosivo 300g', 'Acero Nutrition', 8200, 13990, 25, 5, 50,
        'Fórmula pre-entrenamiento con cafeína para más energía, foco y rendimiento.',
        ['Contiene cafeína y beta-alanina', 'Aumenta la energía y el foco', 'Sabor sandía', 'No recomendado antes de dormir']],
    ['Pre-entreno sin cafeína 300g', 'Acero Nutrition', 8000, 13490, 20, 5, 40,
        'Pre-entrenamiento formulado sin cafeína, ideal para entrenar de noche.',
        ['Libre de cafeína y estimulantes', 'Mejora el bombeo muscular (pump)', 'Sabor manzana verde', 'Apto para entrenos nocturnos']],
    ['Quemador de grasa L-Carnitina líquida', 'ProFit', 4200, 7490, 30, 5, 60,
        'L-Carnitina líquida que ayuda a movilizar las grasas como fuente de energía.',
        ['1500mg de L-Carnitina por dosis', 'Formato líquido de fácil consumo', 'Sabor cítrico', 'Botella de 500ml']],
    ['Barras de proteína caja x12', 'Acero Nutrition', 7000, 11990, 40, 8, 80,
        'Caja con 12 barras de proteína, perfectas para un snack rápido y nutritivo.',
        ['20g de proteína por barra', 'Bajas en azúcar', 'Sabor chocolate y maní', 'Fáciles de llevar al gimnasio']],
    ['Barras energéticas caja x12', 'GreenFit', 6000, 10490, 35, 8, 70,
        'Barras energéticas a base de cereales y frutos secos para antes de entrenar.',
        ['Aporte extra de carbohidratos', 'Con avena, miel y frutos secos', 'Sin conservantes artificiales', 'Caja de 12 unidades']],
    ['Bebida isotónica en polvo 1kg', 'HidraSport', 4500, 7990, 30, 5, 60,
        'Bebida isotónica en polvo para reponer electrolitos durante entrenamientos intensos.',
        ['Repone sodio, potasio y magnesio', 'Rinde hasta 20 litros', 'Sabor naranja', 'Ideal para climas cálidos']],
    ['Bebida isotónica lista para tomar 500ml', 'HidraSport', 900, 1690, 80, 15, 150,
        'Bebida isotónica lista para tomar, práctica para llevar al entrenamiento.',
        ['Hidratación inmediata', 'Sabor limón', 'Sin necesidad de preparar', 'Botella de 500ml']],
    ['Multivitamínico deportivo x60 comp.', 'ProFit', 5200, 8990, 25, 5, 50,
        'Complejo multivitamínico pensado para las mayores demandas del deportista.',
        ['Vitaminas A, C, D, E y complejo B', 'Un comprimido al día', 'Frasco de 60 comprimidos', 'Apoya el sistema inmune']],
    ['Omega 3 x90 cápsulas', 'ProFit', 4800, 8490, 25, 5, 50,
        'Ácidos grasos Omega 3 que ayudan a la salud cardiovascular y articular.',
        ['1000mg de aceite de pescado por cápsula', 'Alto contenido de EPA y DHA', 'Frasco de 90 cápsulas', 'Uso diario']],
    ['Colágeno hidrolizado 300g', 'GreenFit', 6500, 10990, 20, 5, 40,
        'Colágeno hidrolizado en polvo para cuidar articulaciones, piel y tendones.',
        ['10g de colágeno por dosis', 'Con vitamina C agregada', 'Sabor neutro', 'Rinde 30 dosis']],
    ['Maltodextrina 1kg', 'HidraSport', 3800, 6490, 25, 5, 50,
        'Carbohidrato de rápida absorción para reponer energía en entrenamientos largos.',
        ['Carbohidrato de alto índice glucémico', 'Ideal para deportes de resistencia', 'Sin sabor', 'Se disuelve fácil en agua']],
    ['Ganador de masa muscular (mass gainer) 3kg', 'Acero Nutrition', 12500, 19990, 15, 3, 30,
        'Suplemento hipercalórico para quienes buscan aumentar masa muscular y peso.',
        ['Combina proteínas y carbohidratos', '600 kcal por porción', 'Sabor chocolate', 'Rinde 20 porciones']],
    ['Shaker mezclador 600ml', 'Acero Nutrition', 1200, 2490, 60, 10, 120,
        'Vaso mezclador con rejilla anti-grumos, ideal para preparar tus suplementos.',
        ['Capacidad 600ml', 'Rejilla mezcladora incluida', 'Libre de BPA', 'Varios colores disponibles']],
];

// ---------- ACCESORIOS DE ENTRENAMIENTO ----------
$productos[$idAccesorios] = [
    ['Guantes de entrenamiento', 'Acero Gear', 3200, 5990, 40, 8, 80,
        'Guantes acolchados que protegen las manos durante el levantamiento de pesas.',
        ['Palma acolchada antideslizante', 'Ajuste con velcro en la muñeca', 'Transpirables', 'Disponibles en varios talles']],
    ['Cinturón de levantamiento de cuero', 'Acero Gear', 8500, 14990, 20, 4, 40,
        'Cinturón de cuero genuino para dar estabilidad a la zona lumbar en levantamientos pesados.',
        ['Cuero genuino de alta resistencia', 'Hebilla doble prong', 'Ancho de 10cm', 'Disponible en talles S a XL']],
    ['Cinturón de neopreno', 'Acero Gear', 4200, 7490, 30, 6, 60,
        'Cinturón liviano de neopreno con cierre de velcro, ideal para entrenamientos funcionales.',
        ['Material flexible y liviano', 'Cierre de velcro ajustable', 'Buena sujeción lumbar', 'Fácil de colocar y quitar']],
    ['Muñequeras de compresión (par)', 'Acero Gear', 1800, 3290, 50, 10, 100,
        'Par de muñequeras que brindan sujeción y compresión durante el entrenamiento.',
        ['Tejido elástico transpirable', 'Reduce el riesgo de lesiones', 'Talle único ajustable', 'Se venden en par']],
    ['Rodilleras de neopreno (par)', 'Acero Gear', 3600, 6490, 30, 6, 60,
        'Rodilleras de neopreno que dan soporte en sentadillas y ejercicios de piernas.',
        ['Neopreno de 5mm de espesor', 'Sujeción firme sin restar movilidad', 'Se venden en par', 'Talles S, M, L, XL']],
    ['Straps de agarre para pesas', 'Acero Gear', 1500, 2790, 45, 8, 90,
        'Correas de agarre que ayudan a sostener más peso en ejercicios de tirón.',
        ['Algodón resistente reforzado', 'Mejora el agarre en peso muerto y remo', 'Se venden en par', 'Ajuste regulable']],
    ['Correas de tobillo para polea', 'Acero Gear', 2200, 3990, 25, 5, 50,
        'Correas acolchadas para ejercicios de aductores y glúteos en polea baja.',
        ['Acolchado interior antideslizante', 'Argolla de acero reforzada', 'Se venden en par', 'Ajuste con velcro']],
    ['Cuerda para saltar (soga)', 'Acero Gear', 1400, 2590, 40, 8, 80,
        'Soga de salto con rulemanes para entrenamientos de cardio y coordinación.',
        ['Cable de acero forrado en PVC', 'Mangos ergonómicos', 'Largo ajustable', 'Rulemanes para giro suave']],
    ['Banda elástica de resistencia liviana', 'FlexBand', 900, 1690, 60, 10, 120,
        'Banda elástica ideal para calentamiento, movilidad y rehabilitación.',
        ['Resistencia liviana', 'Látex resistente', 'Uso en calentamiento y estiramiento', '1,5 metros de largo']],
    ['Banda elástica de resistencia fuerte', 'FlexBand', 1100, 2090, 55, 10, 110,
        'Banda elástica de alta resistencia para entrenamiento de fuerza y movilidad.',
        ['Resistencia alta', 'Ideal para sentadillas con banda', 'Látex de alta durabilidad', '1,5 metros de largo']],
    ['Set de bandas elásticas (5 niveles)', 'FlexBand', 3200, 5990, 30, 5, 60,
        'Set con 5 bandas de distinta resistencia para progresar en tu entrenamiento.',
        ['5 niveles de resistencia', 'Incluye bolsa de transporte', 'Ideal para viajar o entrenar en casa', 'Colores diferenciados por resistencia']],
    ['Faja lumbar de entrenamiento', 'Acero Gear', 3800, 6990, 25, 5, 50,
        'Faja de sujeción lumbar recomendada para trabajos de fuerza y cargas pesadas.',
        ['Doble cierre de velcro', 'Refuerzo lumbar rígido', 'Talles S a XL', 'Transpirable']],
    ['Grip de agarre (fat gripz)', 'Acero Gear', 2600, 4790, 30, 5, 60,
        'Aumentan el diámetro de la barra para trabajar la fuerza de agarre y antebrazo.',
        ['Se colocan sobre la barra', 'Mejoran la fuerza de agarre', 'Goma de alta densidad', 'Se venden en par']],
    ['Toalla deportiva de microfibra', 'Acero Gear', 1500, 2790, 50, 10, 100,
        'Toalla liviana de secado rápido, ideal para llevar al gimnasio.',
        ['Microfibra de secado rápido', 'Liviana y compacta', 'Incluye funda de transporte', '80x35cm']],
    ['Botella deportiva 1L', 'Acero Gear', 1600, 2990, 60, 10, 120,
        'Botella resistente con marcas de nivel para controlar tu hidratación.',
        ['Capacidad 1 litro', 'Libre de BPA', 'Marcas de nivel de agua', 'Pico anti-derrame']],
    ['Bolso deportivo de gimnasio', 'Acero Gear', 5200, 8990, 25, 5, 50,
        'Bolso espacioso con compartimento para calzado, ideal para ir al gimnasio.',
        ['Compartimento separado para calzado', 'Bolsillo para botella', 'Material resistente al agua', 'Capacidad 30 litros']],
    ['Candado para mancuernas (par)', 'Acero Gear', 1200, 2290, 40, 8, 80,
        'Traba de seguridad para fijar los discos en la barra durante el entrenamiento.',
        ['Cierre rápido y seguro', 'Compatible con barra olímpica', 'Se venden en par', 'Resistente al uso intensivo']],
    ['Cinta adhesiva para dedos (tape)', 'Acero Gear', 900, 1690, 45, 10, 90,
        'Cinta deportiva para proteger los dedos en ejercicios de barra y anillas.',
        ['Alta adherencia', 'Protege la piel de las manos', 'Fácil de cortar', 'Rollo de 10 metros']],
    ['Guantes con muñequera integrada', 'Acero Gear', 3800, 6990, 30, 6, 60,
        'Guantes con soporte de muñeca extendido para mayor estabilidad en press.',
        ['Muñequera integrada de sujeción', 'Palma acolchada', 'Cierre ajustable con velcro', 'Talles S a XL']],
    ['Mochila porta shaker y accesorios', 'Acero Gear', 4600, 8290, 20, 4, 40,
        'Mochila deportiva con compartimentos pensados para llevar tu shaker y suplementos.',
        ['Compartimento térmico para shaker', 'Bolsillo para accesorios chicos', 'Correas acolchadas', 'Resistente al agua']],
];

// ---------- EQUIPAMIENTO DE FITNESS ----------
$productos[$idEquipamiento] = [
    ['Mancuernas de goma 1kg (par)', 'Acero Gear', 2200, 3990, 40, 8, 80,
        'Par de mancuernas de goma, ideales para rutinas de tonificación y rehabilitación.',
        ['Recubrimiento de goma antideslizante', 'Peso 1kg cada una', 'No dañan el piso', 'Empuñadura ergonómica']],
    ['Mancuernas de goma 2kg (par)', 'Acero Gear', 3200, 5490, 35, 6, 70,
        'Mancuernas de goma de 2kg, perfectas para entrenamientos funcionales.',
        ['Recubrimiento de goma antideslizante', 'Peso 2kg cada una', 'Empuñadura antideslizante', 'Resistentes a impactos']],
    ['Mancuernas de goma 5kg (par)', 'Acero Gear', 6200, 9990, 25, 5, 50,
        'Mancuernas de goma de 5kg para entrenamiento de fuerza en gimnasio o casa.',
        ['Recubrimiento de goma antideslizante', 'Peso 5kg cada una', 'No dañan el piso', 'Alta durabilidad']],
    ['Kettlebell 8kg', 'IronForce', 5200, 8990, 20, 4, 40,
        'Pesa rusa de 8kg para entrenamientos de fuerza funcional y potencia.',
        ['Cuerpo de hierro fundido', 'Base plana para mayor estabilidad', 'Asa ergonómica antideslizante', 'Peso: 8kg']],
    ['Kettlebell 12kg', 'IronForce', 7200, 11990, 18, 4, 36,
        'Pesa rusa de 12kg, ideal para progresar en fuerza y resistencia muscular.',
        ['Cuerpo de hierro fundido', 'Base plana antivuelco', 'Asa ancha ergonómica', 'Peso: 12kg']],
    ['Kettlebell 16kg', 'IronForce', 9200, 14990, 15, 3, 30,
        'Pesa rusa de 16kg para entrenamientos avanzados de fuerza y acondicionamiento.',
        ['Cuerpo de hierro fundido', 'Recubrimiento de goma en la base', 'Asa antideslizante', 'Peso: 16kg']],
    ['Barra Z para bíceps', 'IronForce', 8200, 13990, 12, 3, 24,
        'Barra curva especial para ejercicios de bíceps y tríceps, cuida las muñecas.',
        ['Diseño ergonómico en zigzag', 'Reduce la tensión en la muñeca', 'Acero cromado', 'Compatible con discos estándar']],
    ['Disco olímpico de goma 5kg', 'IronForce', 4200, 6990, 30, 6, 60,
        'Disco de goma con inserto de acero, compatible con barras olímpicas.',
        ['Recubrimiento de goma', 'Diámetro olímpico 50mm', 'Peso: 5kg', 'Reduce el ruido al apoyarlo']],
    ['Disco olímpico de goma 10kg', 'IronForce', 7200, 11990, 25, 5, 50,
        'Disco de goma de 10kg, resistente para entrenamientos de fuerza intensivos.',
        ['Recubrimiento de goma', 'Diámetro olímpico 50mm', 'Peso: 10kg', 'Inserto de acero reforzado']],
    ['Colchoneta de yoga/pilates', 'Acero Gear', 3200, 5990, 40, 8, 80,
        'Colchoneta antideslizante para yoga, pilates y ejercicios de suelo.',
        ['Espesor de 6mm', 'Superficie antideslizante', 'Incluye correa de transporte', 'Medidas: 173x61cm']],
    ['Step aeróbico ajustable', 'Acero Gear', 5200, 8990, 20, 4, 40,
        'Plataforma de step con altura ajustable para clases de aeróbico y tonificación.',
        ['Altura ajustable en 3 niveles', 'Superficie antideslizante', 'Soporta hasta 150kg', 'Fácil de guardar']],
    ['Balón medicinal 4kg', 'IronForce', 4200, 7490, 25, 5, 50,
        'Balón medicinal para ejercicios de potencia, core y lanzamientos.',
        ['Peso: 4kg', 'Superficie de goma texturizada', 'Alta resistencia a impactos', 'Diámetro estándar 34cm']],
    ['TRX / bandas de suspensión', 'FlexBand', 6200, 10990, 20, 4, 40,
        'Sistema de entrenamiento en suspensión para trabajar todo el cuerpo con tu propio peso.',
        ['Correas de nylon reforzado', 'Incluye anclaje para puerta', 'Ajuste de longitud rápido', 'Soporta hasta 180kg']],
    ['Rueda abdominal', 'Acero Gear', 1800, 3290, 40, 8, 80,
        'Rueda para ejercicios de core, ideal para fortalecer abdominales y espalda.',
        ['Rueda de goma antideslizante', 'Mangos ergonómicos', 'Incluye rodillera de apoyo', 'Estructura reforzada']],
    ['Barra de dominadas para marco de puerta', 'IronForce', 4200, 7490, 20, 4, 40,
        'Barra de dominadas que se instala sin necesidad de taladrar, ideal para casa.',
        ['Instalación sin herramientas', 'Ajustable a distintos anchos de puerta', 'Soporta hasta 100kg', 'Agarres múltiples']],
    ['Foam roller (rodillo de espuma)', 'Acero Gear', 2600, 4790, 35, 6, 70,
        'Rodillo de espuma para masajes de liberación miofascial y recuperación muscular.',
        ['Espuma de alta densidad', 'Superficie texturizada', 'Largo 33cm', 'Ideal después de entrenar']],
    ['Cuerdas de battle rope 9m', 'IronForce', 8200, 13990, 12, 3, 24,
        'Cuerdas ondulatorias para entrenamientos de alta intensidad y potencia.',
        ['Largo: 9 metros', 'Diámetro 38mm', 'Fundas protectoras en los extremos', 'Ideal para HIIT']],
    ['Plataforma pliométrica (cajón de salto)', 'IronForce', 9200, 15990, 10, 2, 20,
        'Cajón de salto de 3 alturas para ejercicios de pliometría y potencia.',
        ['3 alturas en un solo cajón', 'Estructura de madera reforzada', 'Superficie antideslizante', 'Soporta hasta 150kg']],
    ['Banco plegable de entrenamiento', 'IronForce', 12500, 19990, 10, 2, 20,
        'Banco plegable ajustable, ideal para press y ejercicios con mancuernas.',
        ['Respaldo ajustable en varios ángulos', 'Estructura plegable, fácil de guardar', 'Tapizado acolchado', 'Soporta hasta 200kg']],
    ['Set de conos de agilidad x10', 'Acero Gear', 2200, 3990, 30, 5, 60,
        'Set de 10 conos para ejercicios de agilidad, velocidad y coordinación.',
        ['Incluye 10 conos apilables', 'Colores variados', 'Livianos y resistentes', 'Ideal para entrenamiento funcional']],
];

// ---------- INDUMENTARIA DEPORTIVA ----------
$productos[$idIndumentaria] = [
    ['Remera dry-fit hombre', 'Acero Wear', 3200, 5990, 50, 10, 100,
        'Remera deportiva de tela dry-fit que absorbe el sudor y mantiene el cuerpo seco.',
        ['Tela dry-fit transpirable', 'Corte deportivo regular', 'Costuras reforzadas', 'Talles S a XXL']],
    ['Remera dry-fit mujer', 'Acero Wear', 3200, 5990, 50, 10, 100,
        'Remera deportiva femenina de secado rápido, ideal para cualquier entrenamiento.',
        ['Tela dry-fit transpirable', 'Corte entallado', 'Liviana y elástica', 'Talles S a XL']],
    ['Musculosa deportiva hombre', 'Acero Wear', 2800, 4990, 45, 8, 90,
        'Musculosa liviana con tela transpirable, ideal para entrenamientos de alta intensidad.',
        ['Tela liviana transpirable', 'Corte holgado', 'Sisa amplia para libertad de movimiento', 'Talles S a XXL']],
    ['Top deportivo mujer', 'Acero Wear', 2600, 4790, 45, 8, 90,
        'Top deportivo con sujeción media, cómodo para entrenamientos de intensidad variada.',
        ['Sujeción media', 'Tela elástica de secado rápido', 'Sin costuras irritantes', 'Talles S a XL']],
    ['Calza deportiva mujer', 'Acero Wear', 4200, 7490, 40, 8, 80,
        'Calza de tela elástica con compresión suave, ideal para entrenar con libertad de movimiento.',
        ['Tela elástica con compresión suave', 'Cintura alta', 'No transparenta en sentadillas', 'Talles S a XL']],
    ['Short deportivo hombre', 'Acero Wear', 3200, 5790, 45, 8, 90,
        'Short liviano y transpirable, ideal para entrenamientos funcionales y running.',
        ['Tela liviana de secado rápido', 'Bolsillo interno con cierre', 'Cintura elástica ajustable', 'Talles S a XXL']],
    ['Calza corta (biker) mujer', 'Acero Wear', 3600, 6490, 40, 8, 80,
        'Calza corta tipo biker, ideal para entrenamientos de alta intensidad.',
        ['Tela elástica con compresión', 'Cintura alta', 'Bolsillo lateral', 'Talles S a XL']],
    ['Buzo deportivo con capucha', 'Acero Wear', 6200, 10990, 30, 6, 60,
        'Buzo con capucha, cómodo para el calentamiento previo o para después de entrenar.',
        ['Tela frisada interior', 'Capucha ajustable', 'Bolsillo canguro', 'Talles S a XXL']],
    ['Campera cortavientos', 'Acero Wear', 7200, 12990, 25, 5, 50,
        'Campera liviana cortavientos, ideal para entrenar al aire libre.',
        ['Resistente al viento y salpicaduras', 'Liviana y plegable', 'Cierre frontal completo', 'Talles S a XXL']],
    ['Zapatillas running', 'Acero Run', 12000, 19990, 25, 5, 50,
        'Zapatillas livianas pensadas para correr, con buena amortiguación.',
        ['Amortiguación en talón y planta', 'Suela de goma antideslizante', 'Malla transpirable', 'Talles del 36 al 45']],
    ['Zapatillas training/cross', 'Acero Run', 13000, 21990, 25, 5, 50,
        'Zapatillas versátiles para entrenamiento funcional, pesas y clases grupales.',
        ['Base estable para levantamientos', 'Suela con buen agarre', 'Refuerzo lateral', 'Talles del 36 al 45']],
    ['Medias deportivas (pack x3)', 'Acero Wear', 1400, 2590, 60, 10, 120,
        'Pack de 3 pares de medias deportivas con tela transpirable.',
        ['Tela con control de humedad', 'Refuerzo en talón y punta', 'Pack de 3 pares', 'Talle único adulto']],
    ['Gorra deportiva', 'Acero Wear', 1800, 3290, 45, 8, 90,
        'Gorra liviana con visera curva, ideal para entrenar al aire libre.',
        ['Tela transpirable', 'Cierre trasero ajustable', 'Visera curva', 'Talle único']],
    ['Cintillo para cabeza', 'Acero Wear', 900, 1690, 50, 10, 100,
        'Cintillo elástico que absorbe el sudor y mantiene el cabello en su lugar.',
        ['Tela elástica absorbente', 'No aprieta la cabeza', 'Liviano', 'Talle único']],
    ['Guantes térmicos para correr', 'Acero Run', 2200, 3990, 30, 6, 60,
        'Guantes térmicos livianos, ideales para entrenar al aire libre en invierno.',
        ['Tela térmica interior', 'Compatibles con pantallas táctiles', 'Ajuste ceñido', 'Talles S a L']],
    ['Pantalón deportivo largo hombre', 'Acero Wear', 5200, 8990, 35, 6, 70,
        'Pantalón deportivo largo, cómodo para entrenar o para el día a día.',
        ['Tela elástica liviana', 'Cintura con cordón ajustable', 'Bolsillos laterales', 'Talles S a XXL']],
    ['Conjunto deportivo (top + calza) mujer', 'Acero Wear', 6800, 11990, 25, 5, 50,
        'Conjunto deportivo combinado de top y calza, pensado para entrenar con estilo.',
        ['Tela elástica de secado rápido', 'Conjunto combinado top + calza', 'Cintura alta en la calza', 'Talles S a XL']],
    ['Bermuda deportiva hombre', 'Acero Wear', 3600, 6290, 35, 6, 70,
        'Bermuda deportiva cómoda para entrenar en días de calor.',
        ['Tela liviana transpirable', 'Largo a la rodilla', 'Bolsillos laterales', 'Talles S a XXL']],
    ['Chaleco deportivo', 'Acero Wear', 4200, 7490, 25, 5, 50,
        'Chaleco deportivo liviano, ideal para las estaciones de entretiempo.',
        ['Tela cortavientos liviana', 'Sin mangas para libertad de movimiento', 'Cierre frontal', 'Talles S a XXL']],
    ['Mallas térmicas para invierno', 'Acero Wear', 4800, 8490, 25, 5, 50,
        'Mallas con interior térmico, ideales para entrenar al aire libre en invierno.',
        ['Interior térmico afelpado', 'Compresión suave', 'Cintura alta', 'Talles S a XL']],
];

/* =====================================================================
   PASO 4: insertar productos (si no existe uno con el mismo nombre)
   ===================================================================== */
echo "<h2>3) Productos</h2>";

$stmtExiste = $pdo->prepare("SELECT id_producto FROM Productos WHERE nombre = :nombre");
$stmtInsert = $pdo->prepare(
    "INSERT INTO Productos
        (nombre, descripcion, caracteristicas, Fk_id_categoria, Fk_id_marca, Fk_id_proveedor,
         precio, precio_compra, precio_venta, stock_actual, Stock_min, stock_mac)
     VALUES
        (:nombre, :descripcion, :caracteristicas, :categoria, :marca, NULL,
         :precio, :precio_compra, :precio_venta, :stock_actual, :stock_min, :stock_max)"
);

$insertados = 0;
$omitidos = 0;

foreach ($productos as $idCategoriaActual => $listaProductos) {
    echo "<h3>Categoría id {$idCategoriaActual}</h3><ul>";
    foreach ($listaProductos as $p) {
        [$nombre, $marca, $precioCompra, $precioVenta, $stockActual, $stockMin, $stockMax, $descripcion, $caracteristicas] = $p;

        $stmtExiste->execute([':nombre' => $nombre]);
        if ($stmtExiste->fetchColumn()) {
            echo "<li class='skip'>{$nombre} — ya existe, se omite.</li>";
            $omitidos++;
            continue;
        }

        $idMarcaActual = idMarca($pdo, $marca);
        $stmtInsert->execute([
            ':nombre' => $nombre,
            ':descripcion' => $descripcion,
            ':caracteristicas' => implode("\n", $caracteristicas),
            ':categoria' => $idCategoriaActual,
            ':marca' => $idMarcaActual,
            ':precio' => $precioVenta,
            ':precio_compra' => $precioCompra,
            ':precio_venta' => $precioVenta,
            ':stock_actual' => $stockActual,
            ':stock_min' => $stockMin,
            ':stock_max' => $stockMax,
        ]);
        echo "<li class='ok'>{$nombre} — insertado.</li>";
        $insertados++;
    }
    echo "</ul>";
}

echo "<h2>Resumen</h2>";
echo "<p class='ok'>Productos insertados: {$insertados}</p>";
echo "<p class='skip'>Productos que ya existían (omitidos): {$omitidos}</p>";
echo "<p>Listo. Ya podés ir a <a style='color:#ffb703' href='../productos.php'>la tienda</a> para verlos.</p>";
echo "<p style='color:#9a9a9e'>Tip: para poner una imagen a un producto, tomá su ID desde el panel de administración y guardá un archivo llamado <code>ID.jpg</code> (o .png / .webp) dentro de la carpeta <code>acero3/uploads/productos/</code> directamente desde Visual Studio Code. Se va a mostrar automáticamente, sin tocar la base de datos.</p>";

echo "</body></html>";

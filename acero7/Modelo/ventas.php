<?php
function obtenerVentasUsuario(PDO $pdo,int $usuarioId):array{
 $sql="SELECT v.id_venta_producto AS id_venta,v.fecha_venta,v.Fk_id_usuario,
              COALESCE((SELECT pp.total FROM Pagos_productos pp WHERE pp.Fk_id_venta_producto=v.id_venta_producto ORDER BY pp.id_Pagos_productos DESC LIMIT 1),'0') AS total,
              COALESCE((SELECT mp.nombre FROM Pagos_productos pp JOIN Medio_de_pago mp ON mp.id_medio_de_pago=pp.Fk_id_medio_pago WHERE pp.Fk_id_venta_producto=v.id_venta_producto ORDER BY pp.id_Pagos_productos DESC LIMIT 1),'') AS metodo_pago
       FROM ventas_productos v WHERE v.Fk_id_usuario=:id ORDER BY v.fecha_venta DESC,v.id_venta_producto DESC";
 $s=$pdo->prepare($sql);$s->execute([':id'=>$usuarioId]);return $s->fetchAll();
}
function obtenerDetalleVenta(PDO $pdo,int $ventaId):array{
 $s=$pdo->prepare("SELECT d.*,p.nombre AS producto_nombre FROM Detalle_venta_producto d LEFT JOIN Productos p ON p.id_producto=d.fk_id_producto WHERE d.Fk_id_ventas_producto=:id ORDER BY d.id_detalle_venta_producto");$s->execute([':id'=>$ventaId]);return $s->fetchAll();
}
function obtenerInscripcionesUsuario(PDO $pdo,int $usuarioId):array{
 $s=$pdo->prepare("SELECT i.*,m.membresia,m.duracion,di.fecha_inicio,di.fecha_fin,di.sub_total FROM Inscripcion i JOIN Detalle_inscripcion di ON di.Fk_id_inscripcion=i.id_inscripcion JOIN Membresias m ON m.id_membresia=di.Fk_id_membresia WHERE i.Fk_id_usuario=:id ORDER BY i.fecha_inscripcion DESC,i.id_inscripcion DESC");$s->execute([':id'=>$usuarioId]);return $s->fetchAll();
}

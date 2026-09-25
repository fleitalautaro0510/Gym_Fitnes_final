<?php
session_start();
require_once __DIR__ . '/conexion.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ../carrito.php'); exit; }
$carrito=$_SESSION['carrito']??[]; if(!$carrito){header('Location: ../carrito.php?error=carrito_vacio');exit;}
$metodo=$_POST['metodo_pago']??'efectivo';
$permitidos=['efectivo'=>'Efectivo','transferencia'=>'Transferencia','tarjeta'=>'Tarjeta'];
if(!isset($permitidos[$metodo]))$metodo='efectivo';
$usuarioId=isset($_SESSION['user_id'])?(int)$_SESSION['user_id']:null;
if(!$usuarioId){header('Location: ../index.php?action=login&error=debe_iniciar_sesion');exit;}
try{
 $pdo->beginTransaction();
 $stmt=$pdo->prepare("SELECT id_medio_de_pago FROM Medio_de_pago WHERE LOWER(nombre)=LOWER(:nombre) LIMIT 1");$stmt->execute([':nombre'=>$permitidos[$metodo]]);$medio=$stmt->fetch();
 if(!$medio)throw new RuntimeException('No existe el medio de pago en la base de datos. Importá database_compatibilidad.sql.');
 $medioId=(int)$medio['id_medio_de_pago'];
 $estado=$pdo->query("SELECT id_estado_pago FROM Estado_pago ORDER BY id_estado_pago LIMIT 1")->fetch();
 if(!$estado)throw new RuntimeException('No existe un estado de pago. Importá database_compatibilidad.sql.');
 $estadoId=(int)$estado['id_estado_pago'];
 $numero='AC-'.date('YmdHis').'-'.random_int(100,999);
 $totalProductos=0;$totalPlanes=0;
 foreach($carrito as $item){$sub=(float)$item['precio']*(int)$item['cantidad'];if($item['tipo']==='producto')$totalProductos+=$sub;else$totalPlanes+=$sub;}
 $ventasProductoId=null;
 if($totalProductos>0){
   $s=$pdo->prepare("INSERT INTO ventas_productos (fecha_venta,Fk_id_usuario) VALUES (NOW(),:u)");$s->execute([':u'=>$usuarioId]);$ventasProductoId=(int)$pdo->lastInsertId();
 }
 foreach($carrito as $item){
   $cantidad=(int)$item['cantidad'];$precio=(float)$item['precio'];$sub=$cantidad*$precio;
   if($item['tipo']==='producto'){
      $id=(int)$item['id'];
      $lock=$pdo->prepare("SELECT stock_actual,nombre,precio_venta,precio FROM Productos WHERE id_producto=:id FOR UPDATE");$lock->execute([':id'=>$id]);$prod=$lock->fetch();
      if(!$prod || (int)$prod['stock_actual']<$cantidad)throw new RuntimeException('El producto "'.($prod['nombre']??$item['nombre']).'" no tiene stock suficiente.');
      $u=$pdo->prepare("UPDATE Productos SET stock_actual=stock_actual-:c WHERE id_producto=:id");$u->execute([':c'=>$cantidad,':id'=>$id]);
      $d=$pdo->prepare("INSERT INTO Detalle_venta_producto (Fk_id_usuario,Fk_id_ventas_producto,fk_id_producto,amount,cantidad,precio,sub_total) VALUES (:u,:v,:p,:a,:c,:pr,:s)");$d->execute([':u'=>$usuarioId,':v'=>$ventasProductoId,':p'=>$id,':a'=>$cantidad,':c'=>$cantidad,':pr'=>$precio,':s'=>$sub]);
   } else {
      $idM=(int)$item['id'];
      $m=$pdo->prepare("SELECT id_membresia,membresia,duracion,precio FROM Membresias WHERE id_membresia=:id");$m->execute([':id'=>$idM]);$mem=$m->fetch();if(!$mem)throw new RuntimeException('La membresía seleccionada no existe.');
      $ins=$pdo->prepare("INSERT INTO Inscripcion (Fk_id_usuario,fecha_inscripcion,hora_inscripcion,total) VALUES (:u,CURDATE(),CURTIME(),:t)");$ins->execute([':u'=>$usuarioId,':t'=>$sub]);$insId=(int)$pdo->lastInsertId();
      $inicio=date('Y-m-d');$dur=(string)$mem['duracion'];$fin=$inicio;if(preg_match('/(\d+)/',$dur,$mm)){ $n=(int)$mm[1]; $fin=date('Y-m-d',strtotime($inicio.' +'.$n.' '.(stripos($dur,'año')!==false?'year':(stripos($dur,'mes')!==false?'month':'day')))); }
      $di=$pdo->prepare("INSERT INTO Detalle_inscripcion (Fk_id_inscripcion,Fk_id_membresia,fecha_inicio,fecha_fin,sub_total) VALUES (:i,:m,:fi,:ff,:s)");$di->execute([':i'=>$insId,':m'=>$idM,':fi'=>$inicio,':ff'=>$fin,':s'=>$sub]);
      $pg=$pdo->prepare("INSERT INTO Pagos (Fk_id_inscripcion,Fk_id_medio_pago,Monto,fecha_pago,Fk_id_estado_pago) VALUES (:i,:m,:t,NOW(),:e)");$pg->execute([':i'=>$insId,':m'=>$medioId,':t'=>$sub,':e'=>$estadoId]);
   }
 }
 if($ventasProductoId){$pg=$pdo->prepare("INSERT INTO Pagos_productos (Fk_id_venta_producto,Fk_id_medio_pago,fecha_pago,total,Fk_id_estado_pago) VALUES (:v,:m,NOW(),:t,:e)");$pg->execute([':v'=>$ventasProductoId,':m'=>$medioId,':t'=>(string)$totalProductos,':e'=>$estadoId]);}
 $pdo->commit();
 $_SESSION['ultimo_numero_venta']=$numero;$_SESSION['ultimo_total_venta']=$totalProductos+$totalPlanes;$_SESSION['carrito']=[];
 header('Location: ../carrito.php?pedido=ok&venta='.urlencode($numero));exit;
}catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();header('Location: ../checkout.php?error='.urlencode($e->getMessage()));exit;}

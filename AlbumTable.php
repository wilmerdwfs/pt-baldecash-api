<?php
/*
 * STANDAR DE NISSI CONSULTAS
 * 
 */
namespace Principal\Model;
 
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Platform\PlatformInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;

use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mail\Transport\SmtpOptions;

use Principal\Model\LogFunc;

/// INDICE

// Update general
// Consulta general
// Consulta general publica
// Consulta mostrar borrado o no 

// ESPECIAL: Datos de acceso a la opcion actual 

// Lista de roles
// Rol usuario
// rol de un usuario 

class AlbumTable extends AbstractTableGateway
{
   protected $table  = 't_nivelasp';
   protected $table2 = 't_etapas_con';
   protected $table3 = 't_etapas_con';

   public $salarioMinimo;   
   
   public $dbAdapter;
    
   public function __construct(Adapter $adapter)
   {
        $this->adapter = $adapter;
        $this->initialize();
        // Parametros generales
   }
   
   // Update general
   public function modGeneral($con)
   {
      $result=$this->adapter->query($con,Adapter::QUERY_MODE_EXECUTE);

   }

   // Update general con id 
   public function modGeneralId($con)
   {
      $result=$this->adapter->query($con,Adapter::QUERY_MODE_EXECUTE);
      $id = $this->adapter->getDriver()->getLastGeneratedValue(); 
      return $id;
   }  
   // Consulta general
   public function getGeneral($con)
   {
      $result=$this->adapter->query($con,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }
   // Consulta general publica
   public function getGeneralP($con)
   {
      $result=$this->adapter->query($con,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();
      return $datos;
   }   
   // Consulta general 1
   public function getGeneral1($con)
   {
      $result=$this->adapter->query($con,Adapter::QUERY_MODE_EXECUTE);
      //$datos=$result->toArray();
      $datos = $result->current();
      return $datos;
   }   
   // Configuraciones generales
   public function getConfiguraG($con)
   {
      $result=$this->adapter->query("select * from c_general ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();
      return $datos;
   }                                           
   // Listado de cabeceras
   public function getCabInf($con)
   {
      $result=$this->adapter->query("select * from i_cabecera ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }                                              
   // Listado de pies de documentos
   public function getPieInf($con)
   {
      $result=$this->adapter->query("select * from i_pie ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }                                                    
   // Consulta mostrar borrado o no
   public function getBregistro($tabla,$campo,$id)
   {
      $result=$this->adapter->query("select id as bloquear from ".$tabla." where ".$campo." =".$id,Adapter::QUERY_MODE_EXECUTE);
      //$datos=$result->toArray();
      $datos = $result->current();
      return $datos;
   }   
   //consulta de la master usuarios
   public function getUserVendedor($con)
   {
      $result=$this->adapter->query("select a.aprobarPedidos, a.idVen, b.cedula
                                         from cythrmr0_master_fv.users a
                                         left join c_vendedores b on b.id = a.idVen 
                                          where a.id > 0 ".$con,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();
      return $datos;
   }
  //consulta de la master usuarios
   public function getUserConductores($con)
   {
      $result=$this->adapter->query("select a.aprobarPedidos, a.idCond, b.cedula
                                         from cythrmr0_master_fv.users a
                                         left join l_conductores b on b.id = a.idCond 
                                          where a.id > 0 ".$con,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();
      return $datos;
   }  
   // ESPECIAL: Datos de acceso a la opcion actual  
   public function getPermisos($lin)
   {
      $t = new LogFunc($this->adapter);
      $dt = $t->getDatLog();      

      if ($dt['admin']==1)
      {
         $con = "select 1 as nuevo, 1 as modificar, 1 as eliminar, 1 as aprobar, 0 as vista,
            0 as idGrupNom 
          from c_mu2 limit 1";      
      }
      else // Usuario no administrador
      {
         $con = "select b.nuevo, b.modificar, b.eliminar, b.aprobar, b.vista,
               b.idGrupNom 
                 from c_mu3 a 
                   inner join c_roles_o b on b.idM3 = a.id 
                   inner join c_roles c on c.id = b.idRol 
                   inner join users d on d.idRol = c.id 
                   where d.id=".$dt['idUsu']." 
                   and concat('/',a.modelo , '/' , a.controlador, '/' ,  a.vista)='$lin'";        
      }
      $result=$this->adapter->query($con,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();
      return $datos;
   }
   // ESPECIAL: Datos de acceso a la opcion actual  
   public function getPermisosAcceso($lin)
   {
      $t = new LogFunc($this->adapter);
      $dt = $t->getDatLog();      

      if ($dt['admin']==1)
      {
         return 1; 
      }
      else // Usuario no administrador
      {
         $con = "select count(a.id) as num 
                 from c_mu3 a 
                   inner join c_roles_o b on b.idM3 = a.id 
                   inner join c_roles c on c.id = b.idRol 
                   inner join users d on d.idRol = c.id 
                   where d.id=".$dt['idUsu']." 
                   and concat(a.modelo , '/' , a.controlador, '/' ,  a.vista)='$lin'";
         $result=$this->adapter->query($con,Adapter::QUERY_MODE_EXECUTE);
         $datos = $result->current();
         return $datos['num'];                           
      }
   }   
                
    // CONSULTAS FIJAS *-----------------------------------------------****

   // Lista de roles
   public function getRoles($con)
   {
      $result=$this->adapter->query("select * from c_roles where estado=0 ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                                          
   // Consulta de mnues 
   public function getMenuRoles($id, $idRol)
   {
      $result=$this->adapter->query("select a.id, a.nombre, case when b.idM3 is null then 0 else b.id end as idM3,
                                         case when b.nuevo is null then 0 else b.nuevo end as nuevo,
                                         case when b.modificar is null then 0 else b.modificar end as modificar,
                                         case when b.eliminar is null then 0 else b.eliminar end as eliminar, 
                                         case when b.aprobar is null then 0 else b.aprobar end as aprobar, 
                                         case when b.vista is null then 0 else b.vista end as vista, 
                            c.idM1 , a.idM2, d.idM  , case when c.grupoNom > 0
                                  then b.idGrupNom else -9 end as idGrup # Manejo de grupo de nomina   
                                         from c_mu3 a
                                         inner join c_mu2 c on c.id = a.idM2 
                                         inner join c_mu1 d on d.id = c.idM1 
                                         left join c_roles_o b on b.idM3 = a.id and b.idRol = ".$idRol." 
                                         where  a.idM2 = ".$id ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                              
   // Rol usuario
   public function getRolUsu($usu)
   {
      $result=$this->adapter->query("select * 
        from users where usr_name='".$usu."'" ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();
      return $datos;
   }                                             
   // Permisos especiales por usuario 
   public function getUsuEspe($id)
   {
      $result=$this->adapter->query("select * 
        from users where id=".$id ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();
      return $datos;
   }                                                

   // Reportes del contructor 
   public function getConReporCon($id)
   {
      $result=$this->adapter->query("select case when b.salida =2 then b.id*-1 else b.id end as id, b.nombre
              from c_mu3 a 
                inner join i_constructor b on b.idOm = a.id 
              where a.id = ".$id ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }

   // Campos para mostrar en el reporte
   public function getCamposReport($id)
   {
      $result=$this->adapter->query("select c.id, c.alias 
                               from c_mu3 a 
                                      inner join i_constructor b on b.idOm = a.id 
                                      inner join i_constructor_ca c on c.idCon = b.id 
                                 where b.id = ".$id ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                                                           
              
   // Listado de articulos 
   public function getArticulos($con)
   {
      $result=$this->adapter->query("select * 
             from i_articulos a where a.estado = 0 ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }                                                   

   // Existenci de articulos por ubidacion
   public function getArticulosExistUbi($id)
   {
      $result=$this->adapter->query("select a.idMat, b.nombre, a.*  
               from i_bodegas_mat_ubi a
                  inner join i_ubicacion b on b.id = a.idUbi 
               where a.idMat =".$id ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }                                                   

   // Existenci de articulos por lotes
   public function getArticulosExistLot($id)
   {
      $result=$this->adapter->query("select a.idMat, d.nombre as nomEmp, e.nombre as nomCcos, c.nombre as nomBod, b.nombre, a.*  
               from i_bodegas_mat_lote a
                  inner join i_lotes b on b.id = a.idLot 
                  inner join i_bodegas c on c.id = a.idBod 
                  inner join c_empresas d on d.id = c.idEmp 
                  inner join c_centro_costos e on e.id = c.idCcos  
               where a.idMat = ".$id." 
                group by a.id  
                order by a.fechaF asc " ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }                                                      
   // Existenci de articulos por lotes y empresas
   public function getArticulosExistLotEmp($id, $idCot)
   {
      $result=$this->adapter->query("select a.idMat, d.nombre as nomEmp, e.nombre as nomCcos, c.nombre as nomBod, b.nombre, a.* 
               from i_bodegas_mat_lote a
                  inner join i_lotes b on b.id = a.idLot 
                  inner join i_bodegas c on c.id = a.idBod 
                  inner join c_cotiza_c f on f.id = ".$idCot." 
                  inner join c_empresas d on d.id = c.idEmp and d.id = f.idEmp 
                  inner join c_centro_costos e on e.id = c.idCcos                    
                  inner join c_terceros_emp g on g.idTer = f.idCli and g.idEmp = d.id  
               where a.idMat = ".$id." and a.existen > 0 
                group by a.id  
                order by a.fechaF asc " ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }                                                         
   // Existenci de articulos 
   public function getArticulosExist($id)
   {
      $result=$this->adapter->query("select a.*, case when ( select count(aa.id) from i_bodegas_mat_lote aa where aa.idMat = a.idMat ) > 0 then 
 0 else count(a.id) end as num,
    ifnull( ( select bb.porc
                             from c_promocion_d aa 
                               inner join c_promocion bb on bb.id = aa.idProm 
                            where aa.idMat = a.idMat order by aa.id desc limit 1 ) , 0 )  as porcProm , 

                         ifnull( ( select bb.cantidad 
                             from c_promocion_d aa 
                               inner join c_promocion bb on bb.id = aa.idProm 
                            where aa.idMat = a.idMat ) , 0 ) as cantProm,              

                         ifnull( ( select bb.multi  
                             from c_promocion_d aa 
                               inner join c_promocion bb on bb.id = aa.idProm 
                            where aa.idMat = a.idMat ) , 0 ) as cantAdi 

                       from i_bodegas_mat a 
                           where a.idMat =".$id ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();
      return $datos;
   }
   // Buscar articulos que no son controlados
   public function getArticulosBusNoControlados($con, $id)
   {
      $result=$this->adapter->query("select a.id, a.codigo, a.nombre, a.precio1,c.existen, b.nombre as nomMarca, e.nombre as nomEmp ,
        h.nombre as nomPre,     
                        ifnull( ( select aa.id from c_cotiza_d aa where aa.idDoc = ".$id." and aa.idMat = a.id limit 1),0 ) as idIcot ,
                        ifnull( ( select aa.descFinan from c_cotiza_c aa where aa.id = ".$id." ),0 ) as descFinan,

                      ifnull(( select aa.precioEsp   
                             from c_promocion_d aa 
                               inner join c_promocion bb on bb.id = aa.idProm 
                            where bb.tipo = 0 and aa.idMat = a.id AND aa.estado = 0  and DATE_FORMAT( now() , '%Y-%m-%d') >= bb.fechaI 
                               and DATE_FORMAT( now() , '%Y-%m-%d') <= bb.fechaF limit 1),0 ) as precioEsp, 

                         ( select count(aa.id) 
                             from c_promocion_d aa 
                               inner join c_promocion bb on bb.id = aa.idProm 
                            where aa.idMat = a.id AND aa.estado = 0 and DATE_FORMAT( now() , '%Y-%m-%d') >= bb.fechaI 
                               and DATE_FORMAT( now() , '%Y-%m-%d') <= bb.fechaF limit 1 ) as promo ,
                       # Promociones normales 
                        ifnull( ( select aa.porc 
                             from c_promocion_d aa 
                               inner join c_promocion bb on bb.id = aa.idProm 
                            where bb.tipo = 0 and aa.idMat = a.id AND aa.estado = 0 and DATE_FORMAT( now() , '%Y-%m-%d') >= bb.fechaI 
                               and DATE_FORMAT( now() , '%Y-%m-%d') <= bb.fechaF limit 1 ),0) as porc,        

                         ( select case when aa.cantidad = 0 then ' ' else concat( 'Por cada ', aa.cantidad, ' gratis ' , aa.multi ) end 
                             from c_promocion_d aa 
                               inner join c_promocion bb on bb.id = aa.idProm 
                            where bb.tipo = 0 and aa.idMat = a.id and DATE_FORMAT( now() , '%Y-%m-%d') >= bb.fechaI 
                               and DATE_FORMAT( now() , '%Y-%m-%d') <= bb.fechaF 
                              limit 1 ) as lleve, 

                        ifnull(( select aa.cantidad  
                             from c_promocion_d aa 
                               inner join c_promocion bb on bb.id = aa.idProm 
                            where bb.tipo = 0 and aa.idMat = a.id AND aa.estado = 0 and DATE_FORMAT( now() , '%Y-%m-%d') >= bb.fechaI 
                               and DATE_FORMAT( now() , '%Y-%m-%d') <= bb.fechaF limit 1),0 ) as cantProm , 
                            
                        ifnull(( select aa.multi  
                             from c_promocion_d aa 
                               inner join c_promocion bb on bb.id = aa.idProm 
                            where bb.tipo = 0 and aa.idMat = a.id AND aa.estado = 0  and DATE_FORMAT( now() , '%Y-%m-%d') >= bb.fechaI 
                               and DATE_FORMAT( now() , '%Y-%m-%d') <= bb.fechaF limit 1),0 ) as cantAdi , 
                          # Teleferecias ----------------     
                        ifnull( ( select aa.porc 
                             from c_promocion_d aa 
                               inner join c_promocion bb on bb.id = aa.idProm 
                            where bb.tipo = 1 and aa.idMat = a.id AND aa.estado = 0 and DATE_FORMAT( now() , '%Y-%m-%d') >= bb.fechaI 
                               and DATE_FORMAT( now() , '%Y-%m-%d') <= bb.fechaF limit 1 ),0) as porcTele,

                      ifnull(( select aa.precioEsp   
                             from c_promocion_d aa 
                               inner join c_promocion bb on bb.id = aa.idProm 
                            where bb.tipo = 1 and aa.idMat = a.id AND aa.estado = 0  and DATE_FORMAT( now() , '%Y-%m-%d') >= bb.fechaI 
                               and DATE_FORMAT( now() , '%Y-%m-%d') <= bb.fechaF limit 1),0 ) as precioEspTele ,                                           
                         ( select case when aa.cantidad = 0 then ' ' else concat( 'Por cada ', aa.cantidad, ' gratis ' , aa.multi ) end 
                             from c_promocion_d aa 
                               inner join c_promocion bb on bb.id = aa.idProm 
                            where bb.tipo = 1 and aa.idMat = a.id and DATE_FORMAT( now() , '%Y-%m-%d') >= bb.fechaI 
                               and DATE_FORMAT( now() , '%Y-%m-%d') <= bb.fechaF 
                              limit 1 ) as lleveTele,
                                                       ifnull(( select aa.cantidad  
                             from c_promocion_d aa 
                               inner join c_promocion bb on bb.id = aa.idProm 
                            where bb.tipo = 1 and aa.idMat = a.id AND aa.estado = 0 and DATE_FORMAT( now() , '%Y-%m-%d') >= bb.fechaI 
                               and DATE_FORMAT( now() , '%Y-%m-%d') <= bb.fechaF limit 1),0 ) as cantPromTele , 
                            
                        ifnull(( select aa.multi  
                             from c_promocion_d aa 
                               inner join c_promocion bb on bb.id = aa.idProm 
                            where bb.tipo = 1 and aa.idMat = a.id AND aa.estado = 0  and DATE_FORMAT( now() , '%Y-%m-%d') >= bb.fechaI 
                               and DATE_FORMAT( now() , '%Y-%m-%d') <= bb.fechaF limit 1),0 ) as cantAdiTele, a.unidad , i.nombre as nomEmpa,
                  (select aa.precioUnidad from i_articulos_lista_pre aa where aa.idMat = a.id and aa.precioUnidad >0 limit 1) as precioUnidad , j.iva                            

                        from i_articulos a 
                           inner join i_marcas b on b.id = a.idMarca 
                           inner join i_bodegas_mat c on c.idMat = a.id 
                           left join c_empresas e on e.id = a.idEmp  
                           left join i_presentacion h on h.id = a.idPre 
                           left join i_unidades_empaques i on i.id = a.idUnEmp 
                           left join c_tarifas j on j.id = a.idIva 
                          where a.estado = 0  AND a.descon = 0 and ".$con. " 
                            group by a.id 
                            order by a.codigo, a.nombre 
                           " ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }                        

   // Buscar articulos que son controlados
   public function getArticulosBusControlados($idSitio,$conCla,$buscar,$cont )
   {
      $result=$this->adapter->query("select a.id, a.codigo, a.nombre, a.precio1, c.topMax ,
                         ( select count(aa.id) 
                             from c_promocion_d aa 
                               inner join c_promocion bb on bb.id = aa.idProm 
                       where aa.idMat = a.id ) as promo ,e.nombre as nomEmp  
                                from i_articulos a 
                                 left join i_articulos_cla b on b.idMat = a.id 
                                 left join c_terceros_sitio_e c on c.idCla = b.idCla and c.id = ".$idSitio." 
                                 left join i_clasificacion d on d.id = c.idCla and d.controlado=".$cont." ".$conCla." 
                                 left join c_empresas e on e.id = a.idEmp 
                          where (a.nombre like '%".$buscar."%' or a.codigo like '%".$buscar."%') " ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }                                                                                                             
   // Listado de lineas 
   public function getLineas($con)
   {
      $result=$this->adapter->query("select * 
             from i_lineas a where a.estado = 0 ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   } 
   //detalle de promociones 
   public function getPromocionesDeta($con)
   {
      $result=$this->adapter->query("select a.idProm, d.id as idArt, d.codigo, d.nombre, d.precio1 as existen, 
      fu_lista_precio( a.idMat, 1 , 1 ) as precioLista, 
          b.id, b.nombre as nomProm, b.porc, b.cantidad, b.multi, e.nombre as nomMarca ,a.precioEsp as precioEspDes, a.porc as porcDes ,a.cantidad as cantidadDes , a.multi as multiDes , c.existen as salActual   
                             from c_promocion_d a 
                               inner join c_promocion b on b.id = a.idProm 
                               left join i_bodegas_mat_lote c on c.idMat = a.idMat  
                               inner join i_articulos d on d.id = a.idMat 
                               inner join i_marcas e on e.id = d.idMarca  ".$con."
                            order by d.nombre,b.id " ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }                                                    
   //Listado de clasificaciones 
   public function getClasificaciones($con)
   {
      $result=$this->adapter->query("select * 
             from i_clasificacion a where a.estado = 0 ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }                                                   
   // Listado de articulos y lineas
   public function getArtiLin($con)
   {
      $result=$this->adapter->query("select a.nombre, b.imagen1, a.id ,
         ( select sum(aa.existen) from i_bodegas_mat aa where aa.idMat = a.id  ) as existen    
               from i_articulos a 
                  inner join i_articulos_img b on b.idMat = a.id 
                  inner join i_articulos_lin c on c.idMat = a.id 
                where a.estado = 0 ".$con."   
               order by a.nombre" ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }                                                   

   // Listado de bodegas
   public function getBodegas($con)
   {
      $result=$this->adapter->query("select * 
             from i_bodegas a where a.estado = 0 ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }                                                   
   // Listado de lotes
   public function getLotes($con)
   {
      $result=$this->adapter->query("select * 
             from i_lotes a where a.estado = 0 ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }                                                   
   // Listado de ubicaciones
   public function getUbicaciones($con)
   {
      $result=$this->adapter->query("select * 
             from i_ubicacion a where a.estado = 0 ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }                                                      
   // Listado de presentaciones
   public function getPresentaciones($con)
   {
      $result=$this->adapter->query("select * 
             from i_presentacion a where a.estado = 0 ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }                                                   
   // Listado de presentaciones de articulos
   public function getPresentacionesArti($id)
   {
      $result=$this->adapter->query("select b.id, b.nombre  
                 from i_articulos_pre a 
                      inner join i_presentacion b on b.id = a.idPres 
                 where a.idMat = ".$id ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }                                                   
   // Listado de precios de articulos
   public function getPreciosArti($id, $lista)
   {
      if ($lista==0)
          $lista = 1; 

      $result=$this->adapter->query("select a.precio".$lista." as precio,costo  
              from i_articulos a 
               where a.id = ".$id ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();
      return $datos;
   }                                                      
   // Listado de ubicacions de articulos
   public function getUbicacionesArti($id)
   {
      $result=$this->adapter->query("select b.id, b.nombre, d.tipUni   
                 from i_lineas_ubi a 
                      inner join i_articulos_lin c on c.idLin = a.idLin
                      inner join i_articulos d on d.id = c.idMat  
                      inner join i_ubicacion b on b.id = a.idUbi                       
                 where d.id = ".$id ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }                                                      
   // Listado de lotes de articulos
   public function getLotesArti($id)
   {
      $result=$this->adapter->query("select b.id, b.nombre, d.tipUni   
                 from i_lineas_lotes a 
                      inner join i_articulos_lin c on c.idLin = a.idLin
                      inner join i_articulos d on d.id = c.idMat  
                      inner join i_lotes b on b.id = a.idLot                        
                 where d.id = ".$id ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }                                                         
   // Listado de vendedores
   public function getVendedores($con)
   {
      $result=$this->adapter->query("select * 
             from c_vendedores a where a.estado = 0 ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }                
   //Listado de terceros
   public function getTerceros($con, $tipo )
   {
      $result=$this->adapter->query("select * 
             from c_terceros a 
                where   (a.tipo = ".$tipo.") and a.estado = 0".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   } 
   //Listado de terceros
   public function getTercerosC($con, $tipo )
   {
      $con2 = '';
      if ($tipo>0)
          $con2 = "a.tipo in (".$tipo.",3) and "; 

      $result=$this->adapter->query("select a.*, b.factElec, (select count(c.id) from c_terceros_con c where c.idTer = a.id ) as numC  
             from c_terceros a 
                  left join c_terceros_asp_fis b on b.idTer = a.id
             where ".$con2." a.estado = 0 ".$con." order by a.nombre",Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }    
   //Listado de tipo visitas
   public function getTipVisita($con)
   {
      $result=$this->adapter->query("select a.* 
             from c_tip_visita a ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }   
   //Listado de concepto de visita motivo
   public function getConVisita($con)
   {
      $result=$this->adapter->query("select a.* 
             from c_con_visita a ".$con,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }             
   //Listado de terceros dat
   public function getTercerosDat($con, $tipo )
   {
      $result=$this->adapter->query("select * 
             from c_terceros a 
                where a.tipo = ".$tipo." and a.estado = 0 ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();
      return $datos;
   }                   
   // Listado de terceros por vendedores
   public function getTercerosVen($id )
   {
      $result=$this->adapter->query("select a.* 
             from c_terceros a 
                  inner join c_vendedores_ter b on b.idTer = a.id
                where b.idVen = ".$id." and a.estado = 0
              order by a.nombre  " ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }                
   // Listado de sitiosde entregas vende
   public function getTercerosSit($id )
   {
      $result=$this->adapter->query("select b.*, c.nombre as nomCla  
             from c_terceros a 
                  inner join c_terceros_sitio_e b on b.idTer = a.id
                  inner join i_clasificacion c on c.id = b.idCla
                where b.idTer = ".$id." and a.estado = 0" ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }                      
   // Listado de terceros por vendedores
   public function getVendedoresCli($id )
   {
      $result=$this->adapter->query("select a.* 
             from c_vendedores a 
                  inner join c_vendedores_ter b on b.idVen = a.id
                where b.idTer = ".$id." and a.estado = 0" ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }                      
   // Tipos de cotizaciones
   public function getTiposCotiza($con)
   {
      $result=$this->adapter->query("select * 
             from c_tip_cotiza a where a.estado = 0 ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }                                                   
   // Cotizaciones cabecera
   public function getCotizaC($id)
   {
      $result=$this->adapter->query("select a.*, b.nombre as nomCli, c.nombre as nomVen , d.nombre as nomTcot, e.nombre as nomCli, f.nombre as nomVen,  
  (  select count(aa.id) from c_cotiza_d aa where aa.idDoc = a.id) as num,
     a.vlrTotal as diasSinApro,  
 datediff( now(), a.fecDes ) as diasSinDes , g.valor as califica,
     g.enviaPedido ,g.validaCupoC,g.vlrEstado ,g.idUsuApr ,b.cupoCred   
             from c_cotiza_c a 
                 inner join c_terceros b on b.id = a.idCli 
                 inner join c_vendedores c on c.id = a.idVen 
                 inner join c_tip_cotiza d on d.id = a.tipo 
                 inner join c_terceros e on e.id = a.idCli 
                 inner join c_vendedores f on f.id = a.idVen 
                 left join c_califica g on g.id = b.idCal 
           where a.cotiza = 0 and a.id = ".$id." 
             order by a.id desc" ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();
      return $datos;
   }                                                   
   // Cotizaciones cabecera
   public function getCotizaClibre($id)
   {
      $result=$this->adapter->query("select a.*, b.nombre as nomCli, c.nombre as nomVen , d.nombre as nomTcot, e.nombre as nomCli, f.nombre as nomVen,  
  (  select count(aa.id) from c_cotiza_d aa where aa.idDoc = a.id) as num,
     a.vlrTotal as diasSinApro,  
 datediff( now(), a.fecDes ) as diasSinDes , g.valor as califica,
     g.enviaPedido          
             from c_cotiza_c a 
                 inner join c_terceros b on b.id = a.idCli 
                 inner join c_vendedores c on c.id = a.idVen 
                 inner join c_tip_cotiza d on d.id = a.tipo 
                 inner join c_terceros e on e.id = a.idCli 
                 inner join c_vendedores f on f.id = a.idVen 
                 left join c_califica g on g.id = b.idCal 
           where a.id = ".$id." 
             order by a.id desc" ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();
      return $datos;
   }                                                      
   // Cotizaciones cabecera
   public function getCotiza($con)
   {
      $result=$this->adapter->query("select a.*,b.id as idCli, b.nombre as nomCli, c.nombre as nomVen , d.nombre as nomTcot,  
  (  select count(aa.id) from c_cotiza_d aa where aa.idDoc = a.id) as num,
 datediff( now(), a.fecDoc ) as diasSinApro,  
 datediff( now(), a.fecDes ) as diasSinDes, e.usuario as nomUsuA,a.comen,f.nombre AS formaPago         
             from c_cotiza_c a 
                 inner join c_terceros b on b.id = a.idCli 
                 inner join c_vendedores c on c.id = a.idVen 
                 left join c_tip_cotiza d on d.id = a.tipo 
                 left join users e on e.id = a.idUsuA2  
                 left  join c_forma_pago f on  f.id = a.idFor
           where a.cotiza = 0 and a.id > 0".$con." 
             order by a.id desc" ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }                                                   
   // Cotizaciones cabecera 2
   public function getCotizaLibre($con)
   {
      $result=$this->adapter->query("select a.*,b.id as idCli, b.nombre as nomCli, c.nombre as nomVen , d.nombre as nomTcot,  
  (  select count(aa.id) from c_cotiza_d aa where aa.idDoc = a.id) as num,
 datediff( now(), a.fecDoc ) as diasSinApro,  
 datediff( now(), a.fecDes ) as diasSinDes, e.usuario as nomUsuA,a.comen,f.nombre AS formaPago         
             from c_cotiza_c a 
                 inner join c_terceros b on b.id = a.idCli 
                 inner join c_vendedores c on c.id = a.idVen 
                 left join c_tip_cotiza d on d.id = a.tipo 
                 left join users e on e.id = a.idUsuA2  
                left  join c_forma_pago f on  f.id = a.idFor
           where a.id > 0 and a.anulado = 0 ".$con." 
             order by a.id desc" ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }                                                      
   // Cotizaciones detalle
   public function getCotizaD($con)
   {
      $result=$this->adapter->query("select a.*, b.codigo, b.nombre as nomArt,
        case b.tipUni when 0 then 'UNIDAD' 
                when 3 then 'KILOGRAMOS' end as unidad, d.nombre as nomLot, e.nombre as nomBod,
           f.nombre as nomCcos, g.nombre as nomEmp ,h.fecDoc, i.iva , j.nombre as nomPres     
             from c_cotiza_d a 
               inner join i_articulos b on b.id = a.idMat          
               inner join  c_cotiza_c h on h.id = a.idDoc 
               left join c_cotiza_d_lot c on c.idIdoc = a.id
               left join i_lotes d on d.id = c.idLote
               left join i_bodegas e on e.id = c.idBod 
               left join c_centro_costos f on f.id = e.idCcos 
               left join c_empresas g on g.id = b.idEmp                
               left join c_tarifas i on i.id = b.idIva 
               left join i_presentacion j on j.id = a.idPres  
         where a.id > 0 ".$con." order by b.nombre, f.nombre" ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }                                                      

   // autorizaciones de despacho
   public function getAutoriza($con)
   {
      $result=$this->adapter->query("select a.*, b.nombre as nomCli, c.nombre as nomVen , 
  ( select count(aa.id) from c_cotiza_d aa where aa.idDoc = a.id ) as num,
  ( select sum(aa.cantidad * aa.valor) from c_cotiza_d aa where aa.idDoc = a.id ) as vlrTotal,
  ( select case when sum(dd.cantidad) is null then 0 else sum(dd.cantidad) end from c_cotiza_d_a dd where dd.idDoc = a.id ) as autorizado,       
  ( select case when sum(dd.cantidad) is null then 0 else sum(dd.cantidad) end from c_traslados_d dd where dd.idCot = a.id ) as descargado 
             from c_cotiza_c a 
                 inner join c_terceros b on b.id = a.idCli 
                 inner join c_vendedores c on c.id = a.idVen 
           where a.id > 0 
            and ( select case when sum(dd.cantidad) - sum(dd.trasladado)  is null then 0 else sum(dd.cantidad) - sum(dd.trasladado) end 
                         from c_cotiza_d_a dd where dd.idDoc = a.id ) > 0
             order by a.id desc" ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }                      

   // Compras cabecera
   public function getCompras($con)
   {
      $result=$this->adapter->query("select a.*, b.nombre as nomPro, 
  (  select count(aa.id) from c_compras_d aa where aa.idDoc = a.id) as num,
  (  select sum(aa.cantidad * aa.valor) from c_compras_d aa where aa.idDoc = a.id) as vlrTotal     
             from c_compras a 
                 inner join c_terceros b on b.id = a.idPro 
           where a.id > 0 ".$con." 
             order by a.id desc" ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }                                                                             

   // Compras detalles
   public function getComprasD($con)
   {
      $result=$this->adapter->query("select a.*, b.codigo, b.nombre as nomArt  
             from c_compras_d a 
               inner join i_articulos b on b.id = a.idMat      
             where a.id > 0 ".$con." order by a.id desc" ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }                                                                             

   // FACTURAS CARTERA
   public function getCarteraResumid($id)
   {
      $result=$this->adapter->query("select sum(a.vlrTotal) as total , sum(a.vlrAbono) as abonado , sum(a.vlrTotal - a.vlrAbono) as saldo ,
count( a.id ) as numFact , 
 ( select aa.fecha from c_facturas aa where aa.vlrTotal != aa.vlrAbono and aa.idCli = a.idCli order by a.fecha desc limit 1 ) as fechaV,
 ( select datediff( now(), aa.fecha)  from c_facturas aa where aa.vlrTotal != aa.vlrAbono and aa.idCli = a.idCli order by a.fecha desc limit 1 ) as diasV,
 ( select aa.fecha from c_facturas aa where aa.vlrTotal != aa.vlrAbono and aa.idCli = a.idCli order by a.fecha limit 1 ) as fechaN ,     
 ( select datediff( now(), aa.fecha) from c_facturas aa where aa.vlrTotal != aa.vlrAbono and aa.idCli = a.idCli order by a.fecha limit 1 ) as diasN ,
 ( select aa.fecDoc from c_facturas_abonos aa where aa.idCli = a.idCli order by a.fecDoc desc limit 1 ) as fechaAbono ,
  ( select aa.vlrAbono from c_facturas_abonos aa where aa.idCli = a.idCli order by a.fecDoc desc limit 1 ) as vlrAbono, b.cupoCred     
from c_facturas a 
    inner join c_terceros b on b.id = a.idCli   
  where a.vlrTotal != a.vlrAbono and a.idCli  = ".$id ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();
      return $datos;
   }

   public function getCarteraResumRapId($con)
   {
      $result=$this->adapter->query(" 
      select a.numFacTip as  numFactura, a.fecGenFac AS fecDoc, 
         a.fecVenFac AS fecVen, a.saldo as vlrTotal,
            DATEDIFF(a.fecVenFac, NOW() )  AS dias,  
         a.vlrBruto,a.idFac,
      a.vlrDescuento, a.vlrIva, 
       a.descFinan,a.vlrImp,a.diasTrans,a.nomCli,
       a.dirCli,a.telCli,a.diasTrans,a.idCli, a.cupoCred 
        FROM  v_cartera_rap   a 
        where a.idFac > 0 ".$con,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();
      return $datos;
   }

   // FACTURAS CARTERA GENERAL
   public function getCarteraResumidG($con)
   {
      $result=$this->adapter->query("select sum(a.vlrTotal) as total , sum(a.vlrAbono) as abonado , sum(a.vlrTotal - a.vlrAbono) as saldo ,
count( a.id ) as numFact , 
 ( select aa.fecha from c_facturas aa where aa.vlrTotal != aa.vlrAbono and aa.idCli = a.idCli order by a.fecha desc limit 1 ) as fechaV,
 ( select datediff( now(), aa.fecha)  from c_facturas aa where aa.vlrTotal != aa.vlrAbono and aa.idCli = a.idCli order by a.fecha desc limit 1 ) as diasV,
 ( select aa.fecha from c_facturas aa where aa.vlrTotal != aa.vlrAbono and aa.idCli = a.idCli order by a.fecha limit 1 ) as fechaN ,     
 ( select datediff( now(), aa.fecha) from c_facturas aa where aa.vlrTotal != aa.vlrAbono and aa.idCli = a.idCli order by a.fecha limit 1 ) as diasN ,
 ( select aa.fecDoc from c_facturas_abonos aa where aa.idCli = a.idCli order by a.fecDoc desc limit 1 ) as fechaAbono ,
  ( select aa.vlrAbono from c_facturas_abonos aa where aa.idCli = a.idCli order by a.fecDoc desc limit 1 ) as vlrAbono, b.cupoCred ,a.idCli    
from c_facturas a 
    inner join c_terceros b on b.id = a.idCli   
  where a.vlrTotal != a.vlrAbono ".$con."  group by a.idCli",Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }                                                        

   // FACTURAS DETALLADAS EN CARTERA
   public function getCarteraDeta($id)
   {
      $result=$this->adapter->query("select a.tipFactura, a.numFactura, a.id,a.fecDoc,a.fecVen,a.vlrTotal,a.vlrAbono, datediff( now() , a.fecDoc) as dias from c_facturas a 
             where a.vlrTotal != a.vlrAbono and a.idCli = ".$id." order by a.fecDoc " ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }
   // FACTURAS DETALLADAS EN CARTERA GENERAL
   public function getCarteraDetaG($con)
   {
      $result=$this->adapter->query("select a.tipFactura, a.numFactura, a.id,a.fecDoc,a.fecVen,a.vlrTotal,a.vlrAbono, datediff( now() , a.fecDoc) as dias,a.idCli from c_facturas a 
             where a.vlrTotal != a.vlrAbono ".$con." order by a.fecDoc " ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }
   //FACTURAS DETALLADAS EN CARTERA POR VENEDOR
   public function getCarteraDetaVend($id, $idVen)
   {
      $result=$this->adapter->query("select a.tipFactura, a.numFactura, a.id,a.fecDoc,a.fecVen,a.vlrTotal,a.vlrAbono, datediff( now() , a.fecDoc) as dias from c_facturas a 
               inner join c_vendedores_ter b on b.idTer = a.idCli and b.idVen = ".$idVen."  
             where a.vlrTotal != a.vlrAbono and a.idCli = ".$id." order by a.fecDoc " ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }
   //vista de cartera hecha por perdro castro modificada 06/06/2020 
   public function getCarteraDetaVendVista($con)
   {
      $result=$this->adapter->query("select a.numFacTip as  numFactura, a.fecGenFac AS fecDoc, 
         a.fecVenFac AS fecVen, a.saldo as vlrTotal,
            DATEDIFF(a.fecVenFac, NOW() )  AS dias,  a.vlrAbonoProv AS vlrAbono,
         a.vlrBruto,a.idFac,
      a.vlrDescuento, a.vlrIva,  a.descFinan,a.vlrImp,a.diasTrans,a.nomCli,a.dirCli,a.telCli,a.diasTrans, a.comen
        from v_cartera   a 
        where a.idFac > 0 and a.saldoProv!=0 ".$con."
        order by a.fecVenFac " ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }
   public function getCarteraDetaVendVistaRap($con)
   {
      $result=$this->adapter->query("select a.numFacTip as  numFactura, a.fecGenFac AS fecDoc, 
         a.fecVenFac AS fecVen, a.saldo as vlrTotal,
            DATEDIFF(a.fecVenFac, NOW() )  AS dias,  
         a.vlrBruto,a.idFac,
      a.vlrDescuento, a.vlrIva, 
       a.descFinan,a.vlrImp,a.diasTrans,a.nomCli,
       a.dirCli,a.telCli,a.diasTrans,a.idCli
        FROM  v_cartera_rap   a 
        where a.idFac > 0 ".$con."
        order by a.fecVenFac  " ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }
   public function getFacturas($con)
   {
      $result=$this->adapter->query("select a.tipFactura , a.numFactura, a.id, a.fecDoc , 
        a.fecVen, a.fecVen,a.vlrTotal - fu_valor_abono_prov(a.numFactura) AS saldo,
            DATEDIFF(a.fecVen, NOW() )  AS dias,  a.vlrAbono,
         a.vlrBruto,
      a.vlrDesc, a.vlrIva, a.vlrReteIva, a.vlrReteFt, a.vlrReteIca, a.descFinan  
        from c_facturas   a 
        inner join c_terceros    b ON b.id = a.idCli
        inner join c_vendedores  c ON c.id = a.idVen
        where  a.id > 0 ".$con."
        order BY a.fecDoc " ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();
      return $datos;
   }
  // Facturas
   public function getFacturasC($con)
   {
      $result=$this->adapter->query("select a.*, b.nombre as nomCli, b.id as idCli,c.nombre as nomVen , b.nit  
             from c_facturas a 
                 inner join c_terceros b on b.id = a.idCli 
                 inner join c_vendedores c on c.id = a.idVen 
           where a.id > 0 ".$con." 
             order by a.id desc" ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }
   //destalle carteras causasdas
   public function getCarteraCausadas($con)
   {
      $result=$this->adapter->query("select a.idDoc,a.idAbon, a.fecGenFac,concat(a.tipFactura,'/',a.numFactura) AS numFactura
              , a.fecGenFac as fecDoc,a.fecVenFac AS fecVen, a.diasTrans as dias, a.valAbonoFac
                   from v_abonos_d a where a.idDoc > 0 ".$con,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }  
   //ABONOS SIN FACTURAS
   public function getBonosSinFact($id, $idVen,$cont,$con)
   {
      $result=$this->adapter->query("select   a.*
            from c_facturas_abonos a 
               inner join c_vendedores_ter b on b.idTer = a.idCli and b.idVen = ".$idVen."
               ".$cont."  
             where  a.idCli =  ".$id.$con." order by a.fecDoc" ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }                                                         
   // Resumido cliente
   public function getFactCli($id)
   {
      if ($id==0)
          $id = -999;    
      $result=$this->adapter->query("select sum(a.vlrTotal) as valor, count(a.id) as numFact ,
( select aa.fecDoc 
       from c_facturas aa where aa.idCli = a.idCli order by a.fecha desc limit 1 ) as fechaUcomp 
          from c_facturas a 
            where year(a.fecDoc)=2018 and a.idCli=".$id ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();
      return $datos;
   }                                                               

   // Listado de zonas
   public function getZonas($con)
   {
      $result=$this->adapter->query("select * 
             from c_zonas a where a.estado = 0 ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }                       
   // Despachos
   public function getDespachos($con)
   {
      $result=$this->adapter->query("select a.id , a.nombre, (select count(aa.id) from c_cotiza_desp aa where aa.idDesp = a.id ) as numDesp  
             from i_despachos a 
        where a.estado = 0".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }                                                                                

   // Totales cotizaciones 
   public function getCotizaTotal($id)
   {
      $result=$this->adapter->query("select round( sum(a.cantidad * a.valor) ,0 ) as vlrBruto ,
      case when c.manIva = 1 then 0 else 
       round( sum( ( ( d.iva / 100 ) * ( a.valor - ( ( (a.descuCom / 100 ) * ( a.valor - ( (a.descuFin / 100 ) * a.valor ) ) ) + ( a.descuComA ) )  ) ) * a.cantidad ),0 ) end as vlrIva , 
             
       round( sum( ( (a.descuCom / 100 ) * a.valor) * a.cantidad ) ,0 ) as vlrDesc, 
       
       sum(a.descuComA  * a.cantidad ) as vlrDescAdi ,
       
       round( sum( ( (a.descuFin / 100 ) * a.valor) * a.cantidad ) ,0 ) as vlrDescCli, c.comen         
       
                  from c_cotiza_d a 
                    inner join i_articulos b on b.id = a.idMat 
                    inner join c_cotiza_c c on c.id = a.idDoc 
                    inner join c_tarifas d on d.id = b.idIva 
           where a.idDoc =".$id ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();
      return $datos;
   }                                                           
   // Totales compras 
   public function getComprasTotal($id)
   {
      $result=$this->adapter->query("select sum(a.cantidad * a.valor) as vlrBruto ,
           sum( ( (a.iva / 100 ) * ( a.valor - ( (a.descuCom / 100 ) * a.valor) ) ) * a.cantidad ) as vlrIva , 
           sum( ( (a.descuCom / 100 ) * a.valor) * a.cantidad ) as vlrDesc ,
      ( select sum(aa.largo * aa.cantidad) from c_compras_d_lot aa where aa.idIdoc = a.id ) as medidaLot            
                  from c_compras_d a 
           where a.idDoc = ".$id ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();
      return $datos;
   }                                                              
   // Composicion del reporte
   public function getFilCont()
   {
      $result=$this->adapter->query("select * from i_constructor_f order by id " ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                                                 
   // Composicion del reporte
   public function getConRepor($con, $id)
   {
      $result = $this->adapter->query("select b.id, d.id as idCon, b.nombre, case when d.tipo is null then '' else d.tipo end as tipo, 
case when d.etiqueta is null then '' else d.etiqueta end as etiqueta, case when d.names is null then '' else d.names end as names, 
           case when d.funcion is null then '' else d.funcion end as funcion, b.consulta , b.salida , d.consulta as conFun         
                               from c_mu3 a 
                                      inner join i_constructor b on b.idOm = a.id 
                                      left join i_constructor_ele c on c.idCon = b.id 
                                      left join i_constructor_f d on d.id = c.tipo 
                                 where b.id = ".$id."  
                                      order by d.tipo desc" ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                                              
   // Menu 3
   public function getMenRepor($con)
   {
      $result=$this->adapter->query("select a.*, b.nombre as nomC2, 
           c.nombre as nomC3, d.nombre as nomC4  
           from c_mu3 a 
           inner join c_mu2 b on b.id = a.idM2
           inner join c_mu1 c on c.id = b.idM1
           inner join c_mu d on d.id = c.idM 
           where a.repor = 1 
           order by d.nombre, c.nombre, b.nombre, a.nombre".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                                                 
   // Elementos de un reporte
   public function getEleRep($con)
   {
      $result=$this->adapter->query("select * from i_constructor_ele where id>0 ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                                              

   // Ventas de vendedores resumida 
   public function getVendRes($con)
   {
      $result=$this->adapter->query("select a.idVen, b.nombre as nomVen,
        sum(a.vlrTotal) as vlrTotal , count(a.id) as num ,
       a.fecha as fechaI, a.fecha as fechaF      
from c_cotiza_c a 
  inner join c_vendedores b on b.id = a.idVen 
  inner join c_terceros c on c.id = a.idCli 
where a.estado in(0, 1, 2 ) and  a.longitud != 0 and a.latitud != 0 ".$con." 
group by a.idVen   
order by b.nombre, c.nombre, a.fecha" ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }                                                   
   // Ventas de vendedores detallada
   public function getVendDet($con)
   {
      $result=$this->adapter->query("select a.idVen, b.nombre as nomVen, c.nombre as nomCli, a.fecha, a.latitud, a.longitud, a.vlrTotal, a.estado     
from c_cotiza_c a 
  inner join c_vendedores b on b.id = a.idVen 
  inner join c_terceros c on c.id = a.idCli 
where a.estado in( 0, 1, 2 ) and a.longitud != 0 and a.latitud != 0 ".$con." 
group by b.nombre, c.nombre, a.id 
order by b.nombre, c.nombre, a.fecha asc " ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }                                                      

   //Datos de cotizacion, clase y tope
   public function getCotDetCla($id)
   {
      $result=$this->adapter->query("select a.controlado,a.idCli,a.idSitio,b.idCla,
                                   case when b.topMax is null then 0 else b.topMax 
                                    end as topMax , c.nombre as nomCla      
                                 from c_cotiza_c a 
                                   left join c_terceros_sitio_e b on b.id = a.idSitio   
                                   left join i_clasificacion c on c.id = b.idCla 
                                 where a.id=".$id ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();
      return $datos;
   }                                                     

   //Centros de costos
   public function getCentroCostos($id)
   {
      $result=$this->adapter->query("select * 
                                 from c_centro_costos a 
                                    order by a.nombre" ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }                                                         

   //Lista de precio de cliente
   public function getCotizaTerLista($id)
   {
      $result=$this->adapter->query("select b.lista   
              from c_cotiza_c a 
                    inner join c_terceros_list b on b.idTer = a.idCli
              where a.id = ".$id ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();
      return $datos;
   }

   //Lista de recaudo
   public function getRecaudos($con)
   {
      $result=$this->adapter->query("select a.*, c.nombre as nomTer, d.nombre as nomVen    
              from c_facturas_abonos a 
                inner join c_facturas b on b.id = a.idFact 
                inner join c_terceros c on c.id = b.idCli 
                inner join c_vendedores d on d.id = b.idVen 
              where a.id > 0 ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }
   
   //Vista de abonos 
   public function getAbonosVista($con,$cond)
   {
      $result=$this->adapter->query($con." from v_abonos a ".$cond,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }
   //Lista de recaudo
   public function getRecaudosDeta($con)
   {
      $result=$this->adapter->query("select a.*, ( select count(aa.id) from c_facturas_abonos_d aa where aa.idDoc = a.id ) as num,
d.nombre as nomTer ,e.nombre as nomVen, f.nombre as formPago, a.comen,
(select aa.nombre 
                 from c_recibos_prov_tran aa where aa.idDoc = a.id and aa.nombre !='' limit 1 ) as nomCli
              from c_recibos a 
                    left join  c_recibos_d b on b.idDoc = a.id
                    left join c_facturas c on c.id = b.idFact 
                    left join c_terceros d on d.id = a.idCli 
                    inner join c_vendedores e on e.id = a.idVen 
                    left join c_tip_forma_pago f on  f.id = a.idFor 
                     ".$con." order by a.id desc ",Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }

   // Lista de laboratorios
   public function getMarcas($con)
   {
      $result=$this->adapter->query("select * from i_marcas ".$con." order by nombre" ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                                                                       
   // Lista de empresas
   public function getEmpresas($con)
   {
      $result=$this->adapter->query("select * from c_empresas where estado = 0 ".$con." order by nombre" ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }  
  // Lista de empresas por cliente
   public function getEmpresasTer($con)
   {
      $result=$this->adapter->query("select a.* from c_terceros_emp a ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                                                                           
   // Formas de pago
   public function getFormasPagos($con)
   {
      $result=$this->adapter->query("select *
                        from c_forma_pago ".$con." 
                           " ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   } 
  // Formas de pago
   public function getFormasPagosId($id)
   {
      $result=$this->adapter->query("select *
                        from c_forma_pago where id=".$id." 
                           " ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->current();
      return $datos;
   } 
   // tipo Formas de pago
   public function getTipFormasPagos($con)
   {
      $result=$this->adapter->query("select *
                        from c_tip_forma_pago ".$con." 
                           " ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   } 
  //Sectores 
   public function getSectores($con)
   {
      $result=$this->adapter->query("select * from c_sectores where estado = 0 ".$con,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }  
   //Terceros sitios
   public function getSitiosTercerosId($id)
   {
      $result=$this->adapter->query("select a.*, b.nombre as barrio from c_terceros_sitio_e a
                                                      inner join c_sectores b on b.id = a.idSector
                                                 where a.estado = 0 and idTer=".$id,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }    

   // Listado de cciudades
   public function getCiudades($con)
   {
      $result=$this->adapter->query("select * 
                 from crm_ciudades order by nombre" ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }                                                               
   // Listado de pais
   public function getPais($con)
   {
      $result=$this->adapter->query("select * 
                 from crm_pais " ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }  
   //Clasificacion con terceros opcion
   public function getClasificaTercerosOrigen($con)
   {
      $result=$this->adapter->query("select b.id,b.nombre, a.id as idTcla,a.nombre as nomTcla 
                                                  from  c_tip_clasifica a 
                                                     inner join i_clasificacion b on a.id = b.idTcla 
                                                  where a.origen = ".$con.' order by a.nombre ',Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   } 
   // Clasificaciones terceros 
   public function getClasificaTerceros($id)
   {
      $result=$this->adapter->query("select * from c_terceros_cla a  inner join  c_tip_clasifica b   
              where idTer = ".$id ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }  

   //Lista detalle  
   public function getListaPrecioDetalle($con)
   {
      $result=$this->adapter->query("select a.id as idArticulo, a.codigo as codArticulo, a.nombre as nomArticulo, c.codigo as codLista,c.nombre as nomLista, d.nombre as nomMarca, d.porc as porcMar,
        c.nombre as nomLis,b.precio,b.porc as porcLisPre, c.porc,c.tipCalculo,e.nombre as nomFab,c.deci,f.existen as salActual, h.nombre AS nomProm , 
                                        g.porc AS porcD , g.precioEsp AS precioEspD, g.cantidad AS cantidadD , g.multi AS multiD
                                                              
                                                from  i_articulos a 
                                                   inner join i_articulos_lista_pre b  on b.idMat = a.id 
                                                   inner join  i_listas_precios  c on c.id = b.idLista 
                                                   left join i_marcas d on d.id = a.idMarca
                                                   left join c_terceros e on e.id = d.idTer 
                                                   left join i_bodegas_mat f on f.idMat = a.id
                                                   left join c_promocion_d g ON g.idMat = a.id
                                                   left join c_promocion  h ON h.id = g.idProm 

                                                where  c.id >0  ".$con." and a.estado = 0
                                                group by a.id order by a.nombre",Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }   
   //Lista detalle  
   public function getLista($con)
   {
      $result=$this->adapter->query("select  * from i_listas_precios where estado = 0 and origen=".$con,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }    
   //Tipo de devoluciones 
   public function getTipDevoluciones($con)
   {
      $result=$this->adapter->query("select * from c_tip_devol where  estado =0 ".$con,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   } 
   //Lista de precios
   public function getLisprecios($con)
   {
      $result=$this->adapter->query("select a.*, case when a.origen = 0 then 'Proveedor'
                                                      when a.origen = 1 then 'Cliente' 
                                                 end as origenLista 
                                                 from i_listas_precios a   where a.estado = 0 ".$con." order by a.origen",Adapter::QUERY_MODE_EXECUTE);
   }     
   //Lista de precios
   public function getListaPrecios($con)
   {
      $result=$this->adapter->query("select * from i_listas_precios where  estado =0 ".$con,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   } 
   //Ventas de vendedores
   public function getVendResGeneral($con)
   {
      $result=$this->adapter->query("select a.id,a.fecDoc, sum(a.vlrBruto) - sum(a.vlrDescuento) as vlrTotal, b.nombre as nomCli, a.estado,d.usuario, a.fecApr,b.direccion,c.nombre as nomVen,c.id as idVen,count(a.id) as num ,a.fecha as fechaI, a.fecha as fechaF,e.nombre AS formaPago,a.comen, a.valorFlete as 'valorFlete'
                                    from  c_cotiza_c a 
                                          inner join c_terceros b on b.id = a.idCli 
                                          inner join c_vendedores c on c.id = a.idVen
                                          left  join users d on d.id = a.idUsuA
                                          left  join c_forma_pago e on  e.id = a.idFor
                                    where a.estado > 0 ".$con." 
                                    order by a.fecDoc desc",Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   } 
   //Ventas de vendedores
   public function getAbonos($con)
   {
      $result=$this->adapter->query("select a.* from c_facturas_abonos a ".$con,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->current();
      return $datos;
   } 
   //Listado de visitas
   public function getVisitas($con)
   {
      $result=$this->adapter->query("select a.comen, a.fecha, b.nombre AS nomTvis, c.nombre AS nomConVis,d.nombre,
        e.nombre as nomVen
                   from  c_visitas a 
                         inner join c_tip_visita b ON b.id = a.idTvis 
                         inner join c_con_visita c ON c.id = a.idCvis
                         inner join c_terceros   d ON d.id = a.idCli
                         inner  join c_vendedores e on e.id = a.idVen ".$con,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }  
   //Lista de retenciones con tipos general
   public function getRetencionesTipos($con)
   {
      $result=$this->adapter->query("select a.*, b.nombre as nomTrete,
                                            case 
                                                when a.operacion = 0 then 'Suma' 
                                                else 'Resta'  
                                            end as tipoOper, c.nombre as planCuen
                                            from c_impuestos a 
                                                 left join c_tip_impuestos b on b.id = a.idTimp
                                                 left join con_plan_cuentas c on c.id = a.idCta
                                            ".$con.' order by b.nombre',Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   } 
   //Lista de consignaciones
   public function getConsignaciones($con)
   {
      $result=$this->adapter->query("select a.*, b.nombre as nomBanco 
                                                 from c_consignaciones a
                                                      inner join  con_bancos b on b.id = a.idBan
                                                      left join c_facturas_abonos c on c.idConsignac = a.id

                                            ".$con,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }  
   //Lista de bancos
   public function getBancos($con)
   {
      $result=$this->adapter->query("select a.* from con_bancos a
                                            where a.id > 0 and estado = 0 ".$con,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }   
   //detalle de facturas en despachos
   public function getDespachosFacturas($con)
   {
      $result=$this->adapter->query("select concat(i.prefijo ,'/', a.numDoc) AS numFactura, a.id , a.fecDoc,  a.fecVen, 
            a.vlrTotal ,a.vlrAbono,  a.idCli,g.nombre as nomCli,g.direccion as dirCli, g.direccion as dirEntrega, g.telefonos telCli,h.nombre  nomVendedor, h.id as idVen,b.id as idDocD, c.estado as estadoDoc,d.nombre as nomTrans, f.nombre as nomCon, e.nombre as nomVeh,c.id as idDoc, c.fecDoc as fecDespa,e.placa, 
          ( select count(aa.id) from l_despachos_d aa where aa.idDoc = c.id ) as num , a.vlrBruto ,b.idAbono as idAbonoD,b.idDoc as idAbono,
        a.vlrTotal, fu_valor_abono_prov (a.numFactura) AS vlrAbonoP, 
        fu_valor_abono_def (a.numFactura) AS vlrAbonoD,b.programada,
        ( select count(aa.id) from l_despachos_d aa where aa.idDoc = c.id and aa.estado = 0 ) as num,
        ( select count(aa.id) from l_despachos_d aa where aa.idDoc = c.id  and aa.estado = 1 ) as numR,
         ( select count(aa.id) from l_despachos_d aa where aa.idDoc = c.id  ) as numT,
       b.estado,c.ordenEntrega,c.fecDoc AS fecDocDes, j.id as idRecibo, j.vlrAbono , c.id as idDespa
    from c_facturas  a 
              inner join l_despachos_d b on b.idFact = a.id 
              inner join l_despachos   c on c.id = b.idDoc
              inner join c_transportadoras d on d.id = c.idTrans
              inner join l_vehiculos  e   on e.id = c.idVeh
              inner join l_conductores f on f.id = c.idCon
              inner join c_terceros    g on g.id = a.idCli
              inner join c_vendedores  h on h.id = a.idVen
              inner join c_tip_doc     i on i.id = a.idTipDoc
              inner join c_recibos     j on j.id = b.idAbono
        WHERE  b.id !=0 ".$con." and b.estado = 0 ",Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }  
   //detalle de transportadora vehiculos y conductores
   public function  getDespachosFacturasId($id)
   {
      $result=$this->adapter->query("select a.*,
    ( select count(aa.id) from l_despachos_d aa where aa.idDoc = a.id ) as num , g.usuario,b.idFact , b.numfactura 
   from          l_despachos  a 
   inner join    l_despachos_d   b on b.idDoc = a.id
   inner join    c_transportadoras c on c.id = a.idTrans
   inner join    l_vehiculos  d   on d.id = a.idVeh
   inner join    l_conductores f on f.id = a.idCon
   inner join    users g on g.id = a.idUsu
   ".$id,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();
      return $datos;
   } 
    //motivos de no entregas id
   public function getMotiNoEntregasId($id)
   {
      $result=$this->adapter->query("select a.* from l_motivos_no_entregas a where a.estado = 0 and a.id  = ".$id,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();
      return $datos;
   }  
   //motivos de no entregas general
   public function getMotiNoEntregas($con)
   {
      $result=$this->adapter->query("select a.* from l_motivos_no_entregas a where a.estado = 0 ".$con,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }  
   //abonos
   public function getAbonosD($con)
   {
      $result=$this->adapter->query("select a.*,b.idFact AS idFactD, b.vlrAbono AS vlrAbonoD, b.numFactura AS numFacturaD, b.tipFactura from c_facturas_abonos a 
      inner join c_facturas_abonos_d b on b.idDoc = a.id  ".$con." group by b.id",Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->current();
      return $datos;
   }   
  
   //consulta para el estado de pedidos
   public function getPedidos($con)
   {
      $result=$this->adapter->query("select count(id) as num ,id   
                   from c_cotiza_c  ".$con,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->current();
      return $datos;
   }  
   //totales por empresa
   public function getTolPedidoEmp($id)
   {
      $result=$this->adapter->query(" select  sum(b.valor * b.cantidad) as vlrDescCli, e.nombre as nomEmp      
       
                  from c_cotiza_c a 
                    inner join c_cotiza_d b on b.idDoc= a.id 
                    inner join i_articulos c on c.id = b.idMat
                    left join c_empresas e on e.id = c.idEmp
           where a.id=".$id." group by e.id",Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   } 
   // Lista de impuestos
   public function getImpuestosDat($id)
   {
      $result=$this->adapter->query("select a.* 
        from c_impuestos a 
        where a.id = ".$id ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->current();
      return $datos;
   } 
   // Lista de impuestos de facturas
   public function getImpuestosFact($id)
   {
      $result=$this->adapter->query("select a.* , b.valor
        from c_impuestos a inner join c_facturas_imp b on b.idImp = a.id
        where b.idDoc = ".$id ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }
   //plan recaudos
   public function getPlanRecaudos($con)
   {
      $result=$this->adapter->query("select a.*, f.nombre nomTer, f.nit, e.numFactura, e.fecVen,f.direccion,g.nombre as nomCiu
             from c_facturas_comp_crm a
                 inner join t_plan_recaudos b on b.id = a.idPlanRec
                 inner join c_coordinadores_cartera c on c.id = b.idCoor
                 inner join c_vendedores_coor  d on d.idCoor  = c.id
                 inner join c_facturas         e on e.id = a.idFac
                 inner join c_terceros         f on f.id = e.idCli
                 inner join crm_ciudades       g on g.id = f.idCiu
             where  a.id > 0 and a.estado = 0 ".$con,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   } 
   //compañias de la master
   public function getCompaniasMaster($con)
   {
      $result=$this->adapter->query("select c.id,c.nombre  
                          from users a 
                            inner  join  users_comp b on b.idUsu = a.id
                            inner  join  companias  c on c.id = b.idComp
                          where a.usr_name = '".$con."'",Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   } 
   //factura detalle lotes
   public function getFacturasNotasCred($con)
   {
      $result=$this->adapter->query("select a.*, c.nombre as nomCli, d.nombre as nomCiu, c.direccion, e.usuario, (select count(aa.id) from  c_notas_creditos_fac_d aa where aa.idDoc = a.id and aa.cantidad!=0) as num, b.numDoc as numFac, c.nit
             from c_notas_creditos_fac a 
                  inner join c_facturas b on b.id = a.idFac
                  inner join c_terceros c on c.id = b.idCli 
                  inner join crm_ciudades d on d.id = c.idCiu
                  inner join users e on e.id = a.idUsu   ".$con,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }
   //Tipo de documentos
   public function getTipDoc($con)
   {
      $result=$this->adapter->query("select a.* from c_tip_doc  a
                                             where a.id > 0 and a.estado = 0 ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }
   //motivos de entrega devoluciones
   public function getMotivosEntregasDevol($con)
   {
      $result=$this->adapter->query("select a.* from c_mot_devol a
                          where a.id > 0 ".$con,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }
   //notas creditos c_factura detalle
   public function getFacturasNotasCredId($con)
   {
      $result=$this->adapter->query("select a.* from c_notas_creditos_fac a where a.id > 0 ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();
      return $datos;
   }
   // Lista de impuestos
   public function getImpuestos($con)
   {
      $result=$this->adapter->query(" select a.*, b.nombre as nomTipImp,
                                            case 
                                                when a.operacion = 0 then 'Suma' 
                                                else 'Resta'  
                                            end as operacion, c.nombre as planCuen
                                            from c_impuestos a 
                                                 inner join c_tip_impuestos b on b.id = a.idTimp
                                                 inner join con_plan_cuentas c on c.id = a.idCta 
                                            where a.estado = 0  ".$con,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }   
   //notas creditos facturas detalle
   public function getFacturasNotasCredDeta($con)
   {
      $result=$this->adapter->query("select a.idFac,  b.idMat as idArt, c.nombre as nomArt,  b.codLot, b.canFac,  b.iva, b.cantidad,b.id, b.valor,fu_inf_art_fac_dev_cant(a.idFac, b.idMat) AS cantDev,
        fu_inf_art_fac_dev_doc(a.idFac, b.idMat) AS notasCre
  FROM c_notas_creditos_fac a
       inner join c_notas_creditos_fac_d b on b.idDoc = a.id
       inner join i_articulos c on c.id = b.idMat
             where a.id > 0 ".$con,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }  
   // Totales notas creditos   
   public function getNotasCreditosTotal($id)
   {
      $result=$this->adapter->query("select round( sum(a.cantidad * a.valor) ,0 ) as vlrBruto ,  round( sum(a.cantidad * a.valor) *( a.iva/100 ) ) as vlrIva
           from c_notas_creditos_fac_d a 
                    inner join i_articulos b on b.id = a.idMat 
           where a.idDoc = ".$id ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();
      return $datos;
   }
   //notas credtitos de facturas devolucion
   public function getNotasCreditosFacImpC($con)
   {
      $result=$this->adapter->query("select a.*, c.nombre as nomImp,c.nombre 
          from c_notas_creditos_fac_imp a 
             inner join c_notas_creditos_fac b on b.id = a.idDoc 
              inner join c_impuestos c on c.id = a.idImp ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }  
   //Tipo de documento id
   public function getTipDocId($id)
   {
      $result=$this->adapter->query("select a.*   
                                      from c_tip_doc  a
                                    where a.id = ".$id,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();
      return $datos;
   } 
   //presupuesto
   public function getPres($con)
   {
      $result=$this->adapter->query("select a.*
       from c_presupuestos a WHERE a.id > 0  ".$con."
" ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }
   //presupuesto
   public function getPresDeta($con)
   {
      $result=$this->adapter->query("select a.presVen, a.ejecutado,(a.presVen - a.ejecutado  ) AS penFac ,
 ROUND(((a.presVen - a.ejecutado  ) / a.presVen ) * 100,00) AS porPenFac,
a.presCob, a.recaudado,(a.presCob - a.recaudado  ) AS penCob ,
ROUND(((a.presCob - a.recaudado  ) / a.presCob ) * 100,00) AS porPenCob,
fu_inf_ven_meses_fac(a.idVenPres,YEAR(a.fecIniPres), MONTH(a.fecIniPres) -3,MONTH(a.fecIniPres) -1) as ventaUltTri,
fu_inf_ven_meses_fac(a.idVenPres,YEAR(a.fecIniPres), MONTH(a.fecIniPres) -1,MONTH(a.fecIniPres) -1) as ventaUltMes
          ,  
fu_inf_ven_meses_rec(a.idVenPres,YEAR(a.fecIniPres), MONTH(a.fecIniPres) -3,MONTH(a.fecIniPres) -1) as recaUltTri
          ,  
fu_inf_ven_meses_rec(a.idVenPres,YEAR(a.fecIniPres), MONTH(a.fecIniPres) -1,MONTH(a.fecIniPres) -1) as recaUltMes
  from v_presupuestos a WHERE a.idVenPres > 0  ".$con."
  AND YEAR( a.fecIniPres) = YEAR(NOW()) AND MONTH(a.fecIniPres) = MONTH(NOW()) " ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }
   //recibos totales
   public function getRecibosTotalId($id)
   {
      $result=$this->adapter->query("select a.*, b.nombre as nomProv ,
        SUM(c.vlrAbono) as vlrBruto,
                   0 as vlrIva 
             from c_recibos a 
                 inner join c_terceros b on b.id = a.idCli 
                 inner join c_recibos_d c on c.idDoc = a.id   
        where a.id = ".$id ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();
      return $datos;
   } 
   public function getRecibosImp($con)
   {
      $result=$this->adapter->query("select a.*,b.idD, b.valor
          from c_impuestos a 
                      inner join c_recibos_imp  b on b.idImp = a.id 
          where a.id > 0 ".$con,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }
   public function  getCodIntCla($con)
   {
     $result=$this->adapter->query("select a.* from i_codigo_inteligente_cla a WHERE  a.id > 0 ".$con,Adapter::QUERY_MODE_EXECUTE);
     $datos = $result->toArray();
     return $datos;
  }
  public function  getCodInt($con)
  {
    $result=$this->adapter->query("select a.*, b.nombre as nomCla
                                     from i_codigo_inteligente a 
                                          inner join  i_codigo_inteligente_cla  b on b.id = a.idCodIntCla
                                     WHERE  a.id > 0 ".$con,Adapter::QUERY_MODE_EXECUTE);
   $datos = $result->toArray();
   return $datos;
  }

   public function  getMarcasUsu($con)
   {
      $result=$this->adapter->query("select a.*
                     from  i_marcas  a 
                        inner join c_usu_marcas b on b.idMarca = a.id
                     where a.id > 0 ".$con,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos; 
   }

}    




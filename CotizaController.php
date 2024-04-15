<?php
/** STANDAR MAESTROS NISSI  */
// (C): Cambiar en el controlador 
namespace comercial\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Adapter\Adapter;
use Zend\Form\Annotation\AnnotationBuilder;

use comercial\Model\Entity\Cotiza;     // (C)
use comercial\Model\Entity\CotizaI;     // Centros de costos invitados

use Principal\Form\Formulario;      // Componentes generales de todos los formularios
use Principal\Model\ValFormulario;  // Validaciones de entradas de datos
use Principal\Model\AlbumTable;     // Libreria de datos
use Principal\Model\EspFunc;
use Principal\Model\GraficosTable;     // Libreria de datos graficos
use Principal\Model\Funciones; // Traer datos de session activa y datos del pc 
use Principal\Model\LogFunc; // Traer datos de session activa y datos del pc 
use Zend\Session\Container;

class CotizaController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }
    private $lin  = "/comercial/cotiza/list"; // Variable lin de acceso  0 (C)
    private $tlis = "Listado de pedidos"; // Titulo listado
    private $tfor = "Documento de pedido"; // Titulo formulario
    private $ttab = "IT, CODIGO, DESCRIPCION, CANTIDAD, VALOR, % DESC Comercial, $ +DESC Comercial, $ DESC Financiero, IVA, TOTAL, EDITAR ,ELIMINAR"; // Titulo de las columnas de la tabla
    // Listado de registros ********************************************************************************************
    public function listAction()
    {        
      $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
      $t = new LogFunc($this->dbAdapter);
      $dt = $t->getDatLog();      
      $this->dbAdapter = $this->getServiceLocator()->get($dt['db']);// Conector base de datos cliente

      $g = new GraficosTable($this->dbAdapter);
      $d = new AlbumTable($this->dbAdapter);
      $form = new Formulario("form");
      $datGen = $d->getConfiguraG("");
      $modVisitaVen = $datGen['modVisitaVen'];  

      $datUsu = $d->getGeneral1('select aprobarPedidos, idVen, 
                                   digitador, lower(b.nombre) as nombre     
                                     from cythrmr0_master_fv.users a
                                         left join c_vendedores b on b.id = a.idVen   
                           where a.id='.$dt['idUsu']);  
      $nomVen = $datUsu['nombre'];
      $idVen = $datUsu['idVen'];

      if ($datUsu['idVen']>0)  
         $con = ' and a.idVen = '.$datUsu['idVen'];

      if ($datUsu['digitador']==1)
          $con = $con.' and a.idUsu='.$dt['idUsu'];

      if ($datUsu['aprobarPedidos']==1)
          $con = $con.' and a.estado=1';
      
      $datos = $d->getVendedores("");
      $arreglo[0]= "TODOS";
      foreach ($datos as $dat)
      {
        $idc=$dat['id'];$nom=$dat['codigo'].' - '.$dat['nombre'];
        $arreglo[$idc]= $nom;
      }      
      if ($arreglo != '') 
          $form->get("idVen")->setValueOptions($arreglo);              

      $arreglo=""; 
      $datos = $d->getTerceros("",0);
      $arreglo[0]= "TODOS";
      foreach ($datos as $dat)
      {
        $idc=$dat['id'];$nom=$dat['nit'].' - '.$dat['nombre'];
        $arreglo[$idc]= $nom;
      }      
      if ($arreglo != '') 
          $form->get("idCli")->setValueOptions($arreglo);              

      $arreglo= "";
      $arreglo[1]= "EN ESPERA";
      $arreglo[2]= "APROBADOS";
      $form->get("estado")->setValueOptions($arreglo);              

      // fitros 
      if($this->getRequest()->isPost()) // Actulizar datos
      {
          $request = $this->getRequest();
          $data = $this->request->getPost();
          $con = " ";
          if ($data->idCli > 0)
          {
             $con = " and a.idCli = ".($data->idCli);
             $form->get("idCli")->setAttribute("value",$data->idCli);
          }
          if ($data->idVen > 0)
          { 
             $con = $con." and a.idVen = ".($data->idVen);
             $form->get("idVen")->setAttribute("value",$data->idVen);
          }             
          if ($data->estado > 0)
          {
             $con = $con." and a.estado = ".($data->estado);
             $form->get("estado")->setAttribute("value",$data->estado);
          }                          
          $con = $con." and SUBSTRING_INDEX(SUBSTRING_INDEX(a.fecha, ' ', 1), ' ', -1) 
                      between '".$data->fechaIni."' and '".$data->fechaFin."'";
          $form->get("fechaIni")->setAttribute("value",$data->fechaIni);
          $form->get("fechaFin")->setAttribute("value",$data->fechaFin);
      }         

      //Buscar si hay pedido activo
      $dat = $d->getGeneral1('select count(id) as num ,id   
                                     from c_cotiza_c  
                                       where a.cotiza =0 and  estado=0 and idVen='.$idVen);  
      $activo = $dat['num'];
 
      $valores=array
      (
            "form"      => $form,
            "titulo"    =>  $this->tlis.' '.$nomVen,
            "daPer"     =>  $d->getPermisos($this->lin), // Permisos de usuarios
            "datos"     =>  $d->getCotiza($con),            
            "datCliT"    => $g->getVenCliTot($datUsu['idVen']),
            "datCotT"    => $g->getVenCotTot($datUsu['idVen'], 2),
            "datCotE"    => $g->getVenCotTot($datUsu['idVen'], 1),
            "datCotTv"   => $g->getVenCotTot($datUsu['idVen'], '1,2'),            
            "ttablas"   =>"PEDIDO,CLIENTE,VENDEDOR,ITEMS,ESTADO",
            "lin"       =>  $this->lin,        
            'url'     => $this->getRequest()->getBaseUrl(),    
            "flashMessages" => $this->flashMessenger()->getMessages(), // Mensaje de guardado    
            "aprobarPedidos" => $datUsu['aprobarPedidos'],
            'modVisitaVen' => $modVisitaVen,            
            "idVen"   => $datUsu['idVen'],
            "activo"  => $activo ,
            "id" => $dat['id']       
        );                
        return new ViewModel($valores);
        
    } // Fin listar registros 
    
 
   // Editar y nuevos datos *********************************************************************************************
   public function listaAction() 
   { 
      $form = new Formulario("form");
      //  valores iniciales formulario   (C)
      $id = (int) $this->params()->fromRoute('id', 0);
      $form->get("id")->setAttribute("value",$id);                       

      $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
      $t = new LogFunc($this->dbAdapter);
      $dt = $t->getDatLog();      
      $this->dbAdapter = $this->getServiceLocator()->get($dt['db']);// Conector base de datos cliente

      $d = New AlbumTable($this->dbAdapter);      
      $g = new GraficosTable($this->dbAdapter);
      $dat = $d->getGeneral1("select buscBitaAjax from c_general");
      $buscBitaAjax = $dat['buscBitaAjax'];
      $datUsu = $d->getGeneral1('select aprobarPedidos, idVen, digitador, admin   
                                     from cythrmr0_master_fv.users where id='.$dt['idUsu']);  
      $admin = $datUsu['admin'];
      $digitador = $datUsu['digitador'];
      $aprobarPedidos = $datUsu['aprobarPedidos'];
      $idVen = $datUsu['idVen'];
      //Buscar si hay pedido activo
      $dat = $d->getGeneral1('select count(id) as num, id    
                                     from c_cotiza_c  
                                       where cotiza=0 and estado in (0) AND  app = 1  and idVen='.$idVen);  
      $activo = $dat['num'];
      $num = '';
      if ($dat['num']>0)
      {  
        $num = ' No '.$dat['id'];
        $id = $dat['id'];
        $form->get("id")->setAttribute("value",$id);                       
      }
      // Clientes
      $arreglo='';
      $con = '';
      $tipo = 0 ;
      if ( ($digitador==1) or ($admin==1) or ($aprobarPedidos==1) )
         $datos = $d->getTerceros($con, $tipo);
      else   
         $datos = $d->getTercerosVen($datUsu['idVen']);
      foreach ($datos as $dat)
      {
        $idc=$dat['id'];$nom=$dat['nit'].' - '.$dat['nombre'];
        $arreglo[$idc]= $nom;
      }      
      if ($arreglo != '') 
          $form->get("idCli")->setValueOptions($arreglo);              

      // Articulos
      if ($buscBitaAjax==0)
      {  
        $arreglo='';
        $datos = $d->getArticulos('');
        foreach ($datos as $dat)
        {
           $idc=$dat['id'];$nom = $dat['codigo'].' '.$dat['nombre'];
           $arreglo[$idc]= $nom;
        }      
        if ($arreglo != '') 
          $form->get("idMat")->setValueOptions($arreglo);              
      }
      $date   = new \DateTime(); 
      $fecSis = $date->format('Y-m-d');        
      $form->get("fecDoc")->setAttribute("value",$fecSis);

      $datCot = $d->getGeneral1('select * from c_cotiza_c where id='.$id); 
      $idCli = $datCot['idCli'];
      $estado = $datCot['estado'];
      // Estado
      //$daPer = $d->getPermisos($this->lin); // Permisos de esta opcion
      if ($estado==0)
      {  
         if ($id == 0)
         {
            $val=array
            (
               "0"  => 'Revisión',
            );    
         }else{ 
              $val=array
              (
                 "0"  => 'Revisión',
                 "1"  => 'Solicitar aprobación'              
              );    
         }              
      }else{

         if ($aprobarPedidos==1)
         {
           $val=array
           (
              "1"  => 'Esperando aprobación',          
              "2"  => 'Aprobar pedido',
              "5"  => 'Cancelar pedido',              
              "4"  => 'Devolver pedido al vendedor'                          
           );                          
         }else{ 
           $val=array
           (
              "1"  => 'Esperando aprobación',          
           );       
         }                     
      }  

      $form->get("estado")->setValueOptions($val);
      $arreglo = '';
      $datos = $d->getTiposCotiza(""); // Permisos de esta opcion
      foreach ($datos as $dat)
      {
         $idc = $dat['id']; $nom = $dat['nombre'];
         $arreglo[$idc] = $nom;
      }
      if ($arreglo != '') 
          $form->get("tipo")->setValueOptions($arreglo);          

      $arreglo = '';
      $datos = $d->getGeneral("select * from cythrmr0_master_fv.users where firmDigita=1 ");
      foreach ($datos as $dat)
      {
         $idc = $dat['id']; $nom = $dat['usuario'];
         $arreglo[$idc] = $nom;
      }
      if ($arreglo != '') 
          $form->get("idUsu")->setValueOptions($arreglo);          


      $valores=array
      (
           "titulo"  => $this->tfor.$num,
           "form"    => $form,
           "datCot"  => $d->getGeneral1('select a.*, b.nit, b.nombre as nomTer , c.direccion, c.barrio  
                                 from c_cotiza_c a 
                                   inner join c_terceros b on b.id = a.idCli 
                                   left join c_terceros_sitio_e c on c.idTer = b.id and c.id = a.idSitio  
                                 where a.id = '.$id), 
           "datVen"  => $d->getGeneral1('select count(a.id) as num, a.idVen ,b.nombre  
                                           from cythrmr0_master_fv.users a 
                                             inner join c_vendedores b on b.id = a.idVen 
                                        where a.id = '.$dt['idUsu']), 
           'url'     => $this->getRequest()->getBaseUrl(),
           'id'      => $id,
           "datCont" => $g->getPedidos($idCli),
           "datFact" => $d->getFactCli($idCli),
           'digitador' =>$digitador,
           'aprobarPedidos' => $aprobarPedidos,
           'buscBitaAjax' => $buscBitaAjax,
           'estado' => $estado,
           'activo' => $activo,
           "lin"     => $this->lin
      );       
      // ------------------------ Fin valores del formulario 
       if ($id > 0) // Cuando ya hay un registro asociado
       {
            $datos = $d->getCotiza(" and a.id = ".$id);
            // Valores guardados
            foreach ($datos as $dato) 
            {
               $form->get("estado")->setAttribute("value",$dato['estado']);
               $form->get("tipo")->setAttribute("value",$dato['tipo']);
               $form->get("idVen")->setAttribute("value",$dato['idVen']);
               $form->get("fecDoc")->setAttribute("value",$dato['fecDoc']);
               $form->get("idCli")->setAttribute("value",$dato['idCli']);
            } 
       }            
      $view = new ViewModel($valores);        
      $this->layout('layout/layoutTurnos'); 
      return $view;                
   } // Fin actualizar datos 

   // Eliminar 
   public function listeAction() 
   {
      $id = (int) $this->params()->fromRoute('id', 0);
      if ($id > 0)
         {
            $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
            $t = new LogFunc($this->dbAdapter);
            $dt = $t->getDatLog();      
            $this->dbAdapter = $this->getServiceLocator()->get($dt['db']);// Conector base de datos cliente            

            $u = new Cotiza($this->dbAdapter);  // ---------------------------------------------------------- 5 FUNCION DENTRO DEL MODELO (C)         
            $d = new AlbumTable($this->dbAdapter); // ---------------------------------------------------------- 4 FUNCION DENTRO DEL MODELO (C)                      
            $u->delRegistro($id);
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().$this->lin);
          }          
   }       
   // Guardar detalle 
   public function listdAction() 
   {
        $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
        $t = new LogFunc($this->dbAdapter);
        $dt = $t->getDatLog();      
        $this->dbAdapter = $this->getServiceLocator()->get($dt['db']);// Conector base de datos cliente
        $t = new LogFunc($this->dbAdapter);
        $dt = $t->getDatLog(); 
        $form = new Formulario("form");
        $d = new AlbumTable($this->dbAdapter); 
        $f = new Funciones($this->dbAdapter);

        if($this->getRequest()->isPost()) // Actulizar datos
        {
           $request = $this->getRequest();
           if ($request->isPost()) 
           {
              $data = $this->request->getPost();
              $id = $data->id;
              // INICIO DE TRANSACCIONES
              $connection = null;
              try 
              {
                  $connection = $this->dbAdapter->getDriver()->getConnection();
                  $connection->beginTransaction();
                  if ($data->tipo==1)
                  {  
                     //$dat = $d->getGeneral1("select * from i_articulos where id =".$data->idMat); 
                     $precio     = (float) $data->precio;
                     $descuento  = (float) $data->descuento;
                     $descuentoF = (float) $data->descuentoF;
                     $descuentoA = (float) $data->descuentoA;
                     $descuComCli = (float) $data->descuComCli;
                     $cantProm = (float) $data->cantProm;
                     $cantAdi = (float) $data->cantAdi;// Promociones
                     $porcProm = (float) $data->porProm;
                     $porcTele = (float) $data->porcTele;
                     $preEspe = (float) $data->preEspe;
                     $preEspeTele = (float) $data->preEspeTele;
                     $cantAdiTele = (float) $data->cantAdiTele;// Promociones teleferia
                     $cantPromTele = (float) $data->cantPromTele;
                     $cantidad = (float) $data->cantidad;
                     //$idPres     = (int) $data->idPres;
                     $idPres    = (int) $data->idPres;
                     $promocion = 0;
                     $cantProm  = 0;

                     if ( ($cantAdi>0) or ($porcProm>0) )// Manejo de promociones
                     { 
                       $promocion = 1;
                       $numero = $data->cantidad;
                       if ($numero%$data->cantProm==0)// Buca funcion de primos 
                       {
                          $cantProm = ($numero/$data->cantProm)*$cantAdi;
                       }else{
                          $numero = $numero - 1;
                          if ( $numero >= $data->cantProm )  
                             $cantProm = ($numero/$data->cantProm)*$cantAdi;
                       }
                       $cantProm = (int) $cantProm; 
                     }// Fin promocion POR X LLEVAS TANTO 

                     if ( ($cantAdiTele>0) or ($porcPromTele>0) )// Manejo de promociones teleferias
                     { 
                       $promocion = 1;
                       $numero = $data->cantidad;
                       if ($numero%$data->cantPromTele==0)// Buca funcion de primos 
                       {
                          $cantProm = ($numero/$data->cantPromTele)*$cantAdiTele;
                       }else{
                          $numero = $numero - 1;
                          if ( $numero >= $data->cantPromTele )  
                             $cantProm = ($numero/$data->cantPromTele)*$cantAdiTele;
                       }
                       $cantProm = (int) $cantProm; 
                     }// Fin promocion POR X LLEVAS TANTO teleferias 

                     $valorNormal = 0;
                     if ($preEspe>0)// manejo de precio especial 
                     { 
                       $promocion = 1;                     
                       $valorNormal = $precio;
                       $precio = $preEspe; // Cambiamos de precio para registrar precio original 
                     }  
                     if ($preEspeTele>0)// manejo de precio especial teleferia
                     { 
                       $promocion = 1;                     
                       $valorNormal = $precio;
                       $precio = $preEspeTele; // Cambiamos de precio para registrar precio original 
                     }                                            
                     // Sumo todos los descuentos , 
                     // teleferias , pormociones y de articulos 
                     $descuento = $descuento + $porcProm + $porcTele;
                     $dat = $d->getGeneral1("select b.iva  
                                               from i_articulos a 
                                                 inner join c_tarifas b on b.id = a.idIva  
                                             where a.id =".$data->idMat);
                     $iva = $dat['iva'];
                     $id = $d->modGeneralId("insert into c_cotiza_d (idDoc, idMat, idPres, cantidad, cantProm, descuComCli, descuCom, descuComA, descuFin, valor, iva, idUsu, promocion, valorNormal, descuTele, descuProm,mayorValor) 
                     values(".$data->id.",".$data->idMat.", ".$idPres.",".$cantidad.",".$cantProm.", ".$descuComCli.",".$descuento.", ".$descuentoA.", ".$descuentoF.",".$precio.", ".$iva.", ".$dt['idUsu'].", ".$promocion.", ".$valorNormal.",".$porcTele.", ".$porcProm.",".$data->mayorValor.")"  );
                     $idI = $id;
                     // Guardar lote
                     if ($data->idLot>0)
                     {
                        $dat = $d->getGeneral1("select idLot,idBod 
                                        from i_bodegas_mat_lote where id=".$data->idLot);

                        $d->modGeneralId("insert into c_cotiza_d_lot (idDoc,idIdoc,idMat,idLote,idBod, idUsu) 
                     values(".$data->id.",".$id.",".$data->idMat.",
                        ".$dat['idLot'].",".$dat['idBod'].",".$dt['idUsu'].")");
                     } 
                  }  
                  // Totalizar pedido
                  $f->getTotales($data->id);

                  $connection->commit();
                  $this->flashMessenger()->addMessage('');
              }// Fin try casth   
              catch (\Exception $e) 
              {
                 if ($connection instanceof \Zend\Db\Adapter\Driver\ConnectionInterface) {
                     $connection->rollback();
                       echo $e;
                 } 
              /* Other error handling */
              }// FIN TRANSACCION                                                             
               // $d->modGeneral("update c_cotiza_d 
                 //    set tipo = ".$data->tipo.",
                   //      idVen = ".$data->idVen.", estado=".$data->estado.",
                  //       fecDoc='".$data->fecha."'  
                  //    where id = ".$data->id);
           }// Fin guardado datos
           $id = $data->id;
        }

      $valores=array
      (
         "id"      => $id,        
         "form"    => $form,
         "datos"   => $d->getCotizaD(" and a.idDoc =".$id),
         "datCon"  => $d->getConfiguraG(''),
         "datTot"  => $d->getCotizaTotal($id),             
         'url'     => $this->getRequest()->getBaseUrl(),
         "ttablas" => $this->ttab,         
         "cantidad" => $data->cantidad,         
         "descuento" => $descuento,
         "descuentoA" => $descuentoA,
         "precio"  => $precio,         
         "cantProm" => $cantProm,
         "ttablas" => $this->ttab,         
         "idMat"   => $data->idMat,
         "idI"   => $idI,
         "iva"   => $iva,
         "lin"     => $this->lin         
      );                
      $view = new ViewModel($valores);        
      $this->layout('layout/blancoC'); 
      return $view;                
   }       
   // Agregar cabecera   
   public function listcAction()
   {
        $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
        $t = new LogFunc($this->dbAdapter);
        $dt = $t->getDatLog();      
        $this->dbAdapter = $this->getServiceLocator()->get($dt['db']);// Conector base de datos cliente
        $t = new LogFunc($this->dbAdapter);
        $dt = $t->getDatLog();  
        $d = new AlbumTable($this->dbAdapter); 

        if($this->getRequest()->isPost()) // Actulizar datos
        {
           $request = $this->getRequest();
           if ($request->isPost()) 
           {
              $data = $this->request->getPost();

              // INICIO DE TRANSACCIONES
              $connection = null;
              try 
              {
                    $connection = $this->dbAdapter->getDriver()->getConnection();
                    $connection->beginTransaction();                              
                    $id = $data->id;
                    $radio    = (double) $data->radio;
                    $latitud  = (double) $data->latitud;
                    $longitud = (double) $data->longitud;
                    $control = (double) $data->control;
                    $sitioEntrega= (int) $data->sitioEntrega;

                    $idUsuF = 0;
                    if (isset($data->idUsuF))
                        $idUsuF = $data->idUsuF;

                    if ($id==0)
                    {                      
                       $idVen = (int) $data->idVen; 
                       $iva = 0;
                       if (isset($data->iva))
                          $iva = (int) $data->iva;

                       $numOrd = '';
                       if (isset($data->numOrd))
                         $numOrd = $data->numOrd;

                       $fecEnt = '';
                       if (isset($data->fecEnt))
                         $fecEnt = $data->fecEnt;

                       $idBod = 0;
                       if (isset($data->idBod))
                          $idBod = (int) $data->idBod;

                       $diasPlazos = 0;
                       if (isset($data->diasPlazos))
                          $diasPlazos = (int) $data->diasPlazos;

                       $descFinan = 0;
                       if (isset($data->descFinan))
                          $descFinan = (float) $data->descFinan;                        
                       $id = $d->modGeneralId("insert into c_cotiza_c 
                              (idVen, idCli, idFor, dolar, idUsu, radio, latitud, longitud, controlado, idSitio,idUsuF, fecDoc, numOrd, dirAdi, fecEnt, manIva, idCcos, idBod, diasPlazo, descFinan, valorFlete,app ) 
                           values(".$idVen.",".$data->idCli.",".$data->idFor.",".$data->dolar.",".$dt['idUsu'].",".$radio.",".$latitud.",
                           ".$longitud.", ".$control.",".$sitioEntrega.",".$idUsuF.",'".$dt['fecSis']."','".$numOrd."', 
                           '".$dirAdi."', '".$fecEnt."', ".$iva."
                           , ".$data->idCcos.", ".$idBod.", ".$diasPlazos.", ".$descFinan.",".$data->valorFlete.",1 )");
                    }  
                    else
                    {  
                       if ($data->estado == 0)
                       {                      
                          $d->modGeneral("update c_cotiza_c 
                             set idVen = ".$data->idVen.", 
                                 idCli = ".$data->idCli." 
                           where id = ".$data->id);
                       }   
                       if ($data->estado == 4)
                       {                      
                          $comen = "-Pedido devuelto";
                          $d->modGeneral("update c_cotiza_c 
                             set estado=0  , comenD = '".$comen."'      
                           where id = ".$data->id);
                       }                          
                       if ($data->estado == 1)
                       {
                          $d->modGeneral("update c_cotiza_c 
                              set idUsuA = ".$dt['idUsu'].", 
                                  fecApr = '".$dt['fecSis']."',
                                  estado = ".$data->estado."    
                        where id = ".$data->id);

                       } 
                       if ($data->estado == 2)
                       {
                          $d->modGeneral("update c_cotiza_c 
                              set idUsuA = ".$dt['idUsu'].", 
                                  fecApr = '".$dt['fecSis']."',
                                  estado = ".$data->estado."    
                        where id = ".$data->id);

                       }                        
                    }    
                    $connection->commit();
                    $this->flashMessenger()->addMessage('');
              }// Fin try casth   
              catch (\Exception $e) 
              {
                 if ($connection instanceof \Zend\Db\Adapter\Driver\ConnectionInterface) {
                     $connection->rollback();
                       echo $e;
                 } 
              /* Other error handling */
              }// FIN TRANSACCION                                                          

           }// Fin guardado datos
        }

      $valores=array
      (
         "id"      =>  $id,        
         'url'     => $this->getRequest()->getBaseUrl(),
         "estado"  => $data->estado,
         "lin"     => $this->lin         
      );                
      $view = new ViewModel($valores);        
      $this->layout('layout/blancoC'); 
      return $view;                
        
   } // Fin guardar cabecera

   // Buscar datos de cartera del cliente
   public function listdatcliAction() 
   {
       $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
       $t = new LogFunc($this->dbAdapter);
       $dt = $t->getDatLog();      
       $this->dbAdapter = $this->getServiceLocator()->get($dt['db']);// Conector base de datos cliente

        $d = new AlbumTable($this->dbAdapter); 
        $form = new Formulario("form");

        if($this->getRequest()->isPost()) // Actulizar datos
        {
           $request = $this->getRequest();
           if ($request->isPost()) 
           {
              $data = $this->request->getPost();
              $id = $data->id;
            }// Fin guardado datos
        }

      $dat = $d->getGeneral1("select * from c_general");
      $controlado = $dat['controlado'];      
      $dolar      = $dat['dolar'];      
      $manIva     = $dat['manIva'];      
      $manOrden   = $dat['manOrden'];      
      $manEntr2   = $dat['manEntr2'];            
      $manFent    = $dat['manFent'];                  
      $bodegaPedido = $dat['bodegaPedido'];                  
      $manEmp = $dat['manEmp'];                  


      $arreglo = array("0" => "PEDIDO SIN PRODUCTOS CONTROLADOS",
                       "1" => "PEDIDO DE PRODUCTOS CONTROLADOS");
      $form->get("tipoC")->setValueOptions($arreglo);              
      // BUscar datos del cliente 
      $dat = $d->getGeneral1("select idTer, idEmp from c_terceros_emp where id=".$id);
      $id = $dat['idTer'];
      $idEmp = $dat['idEmp'];      
      $arreglo = '';
      $datos = $d->getCentroCostos("");
      foreach ($datos as $dat)
      {
        $idc=$dat['id'];$nom = $dat['codigo'].' - '.$dat['nombre'];
        $arreglo[$idc]= $nom;
      }      
      if ($arreglo != '') 
          $form->get("idCcos")->setValueOptions($arreglo);              

      $arreglo = '';
      $datos = $d->getBodegas("");
      foreach ($datos as $dat)
      {
        $idc=$dat['id'];$nom = $dat['nombre'];
        $arreglo[$idc]= $nom;
      }      
      if ($arreglo != '') 
          $form->get("idBod")->setValueOptions($arreglo);                     

      $arreglo = '';
      $datos = $d->getFormasPagos("");
      foreach ($datos as $dat)
      {
        $idc=$dat['id'];$nom = $dat['nombre'];
        $arreglo[$idc]= $nom;
      }      
      if ($arreglo != '') 
          $form->get("idFor")->setValueOptions($arreglo);                                       
        
      $valores=array
      (
         "id"      => $id,       
         "idCot"   => $data->idCot, 
         'url'     => $this->getRequest()->getBaseUrl(),
         "ttablas" => $this->ttab,         
         "datCli"  => $d->getVendedoresCli($id),
         "datos"   => $d->getCarteraResumRapId(" and a.idCli=".$id),
         "datCar"  => $d->getCarteraDetaVendVistaRap(" and a.idCli=".$id." group by a.numFactura "),
         "datTer"  => $d->getTerceros(" and a.id = ".$id, 0),         
         "datTerD" => $d->getTercerosDat(" and a.id = ".$id, 0),         
         "datCot"  => $d->getCotizaC($data->idCot), 
         "datSit"  => $d->getTercerosSit($id),                           
         "form"    => $form,
         "lin"     => $this->lin,       
         "idCli"   => $id,
         "idEmp"   => $idEmp,
         "controlado" => $controlado,         
         "dolar" => $dolar,         
         "manIva" => $manIva,
         "manOrden" => $manOrden,
         "manEntr2" => $manEntr2,
         "maVen"    => $data->maVen,
         "maEmp"    => $manEmp,
         "manFent"  => $manFent,
         "bodegaPedido"  => $bodegaPedido
      );                
      $view = new ViewModel($valores);        
      $this->layout('layout/blancoC'); 
      return $view;                
   }       

   // Buscar datos de materiales
   public function listbusAction() 
   {
        $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
        $t = new LogFunc($this->dbAdapter);
        $dt = $t->getDatLog();      
        $this->dbAdapter = $this->getServiceLocator()->get($dt['db']);// Conector base de datos cliente

        $form = new Formulario("form");        
        $d = new AlbumTable($this->dbAdapter); 
        $datConfig = $d->getConfiguraG(''); 
        if($this->getRequest()->isPost()) // Actulizar datos
        {
           $request = $this->getRequest();
           if ($request->isPost()) 
           {
              $data = $this->request->getPost();
              $buscar = $data->buscar;

              $nombre = $data->nombre;

           }// Fin guardado datos
        }
           // echo 'ddssss'; 
      if ($buscar == '')  
          $buscar = 'XXXXXXXXXXX';
      //echo $buscar.'<br />';  
      $dat = $d->getGeneral1("select controlado,manLote,manDesCom,manDesComP,manDesFin,busEmp, filtroPres  
                                  from c_general");
      $controlado = $dat['controlado']; 
      $manLote = $dat['manLote']; 
      $manDesCom = $dat['manDesCom']; 
      $manDesComP = $dat['manDesComP']; 
      $manDesFin = $dat['manDesFin']; 
      $filtroPres = $dat['filtroPres']; 
      // Si la empresa no trabaja productos controlados  
      $busEmp = ""; 
      if ($dat['busEmp']==1) 
      {
          $busEmp = " and a.idEmp=".$data->idEmp;
      }     
      if ($controlado==0)
      {  

          $buscar = strtoupper(ltrim(rtrim($buscar)));

          $con = " ( MATCH(a.codigo, a.nombre) AGAINST ('+".str_replace(" ", "* +", $buscar)."*' IN BOOLEAN MODE) )".$busEmp;
//echo $con;
        
          if ($data->idMar>0)
          {  
            if ($buscar!='XXXXXXXXXXX')              
                $con = $con.' and b.id = '.$data->idMar ;             
            else 
                $con = ' b.id = '.$data->idMar ;                               
          }    
          $idBod = 0;
          if ($filtroPres==0){

             $datos = $d->getGeneral("CALL pr_busq_art_venta_fv('".$buscar."',".$data->idMar.",".$data->id.", ".$idBod.")");
          }
          else              
          {  
             $conLike = "( a.nombre like '%".$buscar."%' or a.codigo like '%".$buscar."%')" ;
             $idPres = 0;
             //echo $conLike;
             $datos = $d->getGeneral('CALL pr_busq_art_venta_copy( "'.$conLike.'" ,'.$data->idMar.','.$idBod.',0,'.$data->idPres.' )');         
          } 
      }else{
        // Si la cotizacion trabaja solo controlados
        $dat = $d->getCotDetCla($data->id);
        $conCla='';
        if ($dat['idCla']>0)
           $conCla = "b.idCla = ".$dat['idCla']." and ";
        // se buscar si se manejo como contrlada o no 
        if ($dat['controlado']==0)
        {
          if ($dat['idCla']>0)
             $conCla = " and b.idCla = ".$dat['idCla'];          
          $datos = $d->getArticulosBusNoControlados($dat['idSitio'],$con,$buscar,0); 
        }else{
          $datos = $d->getArticulosBusNoControlados($dat['idSitio'],$con,$buscar,1);                                          
        } 
      }   
      if ($manLote==0)
        $titulo = "ARTICULO";
      else
        $titulo = "ARTICULO,OK"; 
        
      $valores=array
      (
         "form"    => $form,
         "datos"   => $datos,
         'url'     => $this->getRequest()->getBaseUrl(),
         "lin"     => $this->lin,   
         "datCon"  => $d->getConfiguraG(''),
         "topMax"  => $dat['topMax'],    
         "nomCla"  => $dat['nomCla'],
         "manLote" => $manLote,
         "manDesComP" => $manDesComP,
         "manDesCom" => $manDesCom,         
         "manDesFin" => $manDesFin,
         "ttablas"   =>$titulo,
         "verMayorValMat" => $datConfig['verMayorValMat'],
         "verExisMat"     => $datConfig['verExisMat'],
      );                
      $view = new ViewModel($valores);        
      $this->layout('layout/blancoC'); 
      return $view;                
   }       
   // Seleccionar busquedas
   public function listbusselAction() 
   {
        $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
        $t = new LogFunc($this->dbAdapter);
        $dt = $t->getDatLog();      
        $this->dbAdapter = $this->getServiceLocator()->get($dt['db']);// Conector base de datos cliente        
        $d = new AlbumTable($this->dbAdapter); 

        if($this->getRequest()->isPost()) // Actulizar datos
        {
           $request = $this->getRequest();
           if ($request->isPost()) 
           {
              $data = $this->request->getPost();
              $id = $data->id;

           }// Fin guardado datos
        }
      $valores=array
      (
         "maVen"   => $data->buscar,
         "datMat"   => $d->getGeneral("select a.id, a.codigo, a.nombre
                                from i_articulos a  
                          where a.id =".$id),
         "datos"   => $d->getArticulosExist($id),
         "datPre"  => $d->getPresentacionesArti($id), 
         "datPrec" => $d->getPreciosArti($id,0), 
      );                
      $view = new ViewModel($valores);        
      $this->layout('layout/blancoC'); 
      return $view;                
   }          
   // Buscar datos de de matriales
   public function listdatartAction() 
   {
        $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
        $t = new LogFunc($this->dbAdapter);
        $dt = $t->getDatLog();      
        $this->dbAdapter = $this->getServiceLocator()->get($dt['db']);// Conector base de datos cliente        
        $d = new AlbumTable($this->dbAdapter); 
        $form = new Formulario("form");

        if($this->getRequest()->isPost()) // Actulizar datos
        {
           $request = $this->getRequest();
           if ($request->isPost()) 
           {
              $data = $this->request->getPost();
              $id = $data->id;
           }// Fin guardado datos
        }
      $dat = $d->getGeneral1("select costoEditable,manDesCom,manDesFin,manLote,manUbi,controlExis,listaPreciosCliente      
                                  from c_general");
      $costoEditable = $dat['costoEditable'];
      $manDesCom = $dat['manDesCom'];
      $manDesFin = $dat['manDesFin']; 
      $manLote = $dat['manLote'];
      $manUbi = $dat['manUbi'];       
      $controlExis = $dat['controlExis'];    
      $listaPreciosCliente = $dat['listaPreciosCliente'];       

      $dat = $d->getGeneral1("select b.descCom  
              from c_cotiza_c a 
                    inner join c_terceros b on b.id = a.idCli
              where a.id = ".$data->idCot);
      // Lista de precio asociada al cliente
      $datL = $d->getCotizaTerLista($data->idCot);      
      $lista = $datL['lista'];
      $valores=array
      (
         "id"      =>  $id,       
         'url'     => $this->getRequest()->getBaseUrl(),
         "ttablas" => $this->ttab,        
         "datPre"  => $d->getPresentacionesArti($id), 
         "datPrec" => $d->getPreciosArti($id, $lista), 
         "datos"   => $d->getArticulosExist($id),
         "datUbi"  => $d->getArticulosExistUbi($id),
         "datLot"  => $d->getArticulosExistLotEmp($id, $data->idCot),
         "form"    => $form,
         "costoEditable" => $costoEditable,
         "manDesCom" => $manDesCom,
         "manDesFin" => $manDesFin,      
         "manLote" => $manLote,
         "manUbi"  => $manUbi,  
         "descCom" => $dat['descCom'],      
         "controlExis" => $controlExis,              
         "listaPreciosCliente" => $listaPreciosCliente,
         "lin"     => $this->lin         
      );                
      $view = new ViewModel($valores);        
      $this->layout('layout/blancoC'); 
      return $view;                
   }       

   // Buscar datos de lote
   public function listdatlotAction() 
   {
        $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
        $t = new LogFunc($this->dbAdapter);
        $dt = $t->getDatLog();      
        $this->dbAdapter = $this->getServiceLocator()->get($dt['db']);// Conector base de datos cliente        
        $d = new AlbumTable($this->dbAdapter); 
        $form = new Formulario("form");

        if($this->getRequest()->isPost()) // Actulizar datos
        {
           $request = $this->getRequest();
           if ($request->isPost()) 
           {
              $data = $this->request->getPost();
              $id = $data->id;


           }// Fin guardado datos
        }
      // BUscar datos del cliente 
      $dat = $d->getGeneral1("select existen from i_bodegas_mat_lote where id=".$id);
      $existen = $dat['existen'];
      $valores=array
      (
         "id"      => $id,       
         "existen" => $existen, 
      );                
      $view = new ViewModel($valores);        
      $this->layout('layout/blancoC'); 
      return $view;                
   }       

   // Clientes asociados a vendedores
   public function listclAction()
   {
        $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
        $t = new LogFunc($this->dbAdapter);
        $dt = $t->getDatLog();      
        $this->dbAdapter = $this->getServiceLocator()->get($dt['db']);// Conector base de datos cliente        
        $d = new AlbumTable($this->dbAdapter); 

        if($this->getRequest()->isPost()) // Actulizar datos
        {
           $request = $this->getRequest();
           if ($request->isPost()) 
           {
              $data = $this->request->getPost();             

           }// Fin guardado datos
        }

      $valores=array
      (
         "datos"   =>  $d->getGeneral("select b.id, b.nit, b.nombre  
                                         from c_vendedores_cli a 
                                            inner join c_terceros b on b.id = a.idTer 
                                       where a.idVen =".$data->id),        
         'url'     => $this->getRequest()->getBaseUrl(),
         "lin"     => $this->lin         
      );                
      $view = new ViewModel($valores);        
      $this->layout('layout/blancoC'); 
      return $view;                
        
   } // Fin guardar cabecera
   // Editar registros 
   public function listedAction()
   {     
      $form  = new Formulario("form");
      //  valores iniciales formulario   (C)
      $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
      $t = new LogFunc($this->dbAdapter);
      $dt = $t->getDatLog();      
      $this->dbAdapter = $this->getServiceLocator()->get($dt['db']);// Conector base de datos cliente      
      $d = new AlbumTable($this->dbAdapter);
      $f = new Funciones($this->dbAdapter);            
      // --      
      if($this->getRequest()->isPost()) // Si es por busqueda
      {
          $request = $this->getRequest();
          $data = $this->request->getPost();
          if ($request->isPost()) 
          {
             $orden    = (int) $data->orden;
             $ver      = (int) $data->ver;
             $descu    = (float) $data->descu;
             $descuFin = (float) $data->descuFin;
              // INICIO DE TRANSACCIONES
              $connection = null;
              try 
              {
                  $connection = $this->dbAdapter->getDriver()->getConnection();
                  $connection->beginTransaction();

                  $d->modGeneral("update c_cotiza_d  
                      set cantidad  = ".$data->canti.", 
                          descuCom  = ".$data->descu.",
                          descuComA = ".$data->descuA.",
                          descuFin  = ".$data->descuFin." 
                      where id = ".$data->id);  
                  // Totalizar pedido
                  $f->getTotales($data->id);
                  $connection->commit();
                  $this->flashMessenger()->addMessage('');
              }// Fin try casth   
              catch (\Exception $e) 
              {
                 if ($connection instanceof \Zend\Db\Adapter\Driver\ConnectionInterface) {
                     $connection->rollback();
                       echo $e;
                 } 
              /* Other error handling */
              }// FIN TRANSACCION                                                                 
          }
      }
      $view = new ViewModel();        
      $this->layout("layout/blancoC");
      return $view;            
    }
   // Anular o elimnar pedido completo
   public function listanuAction()
   {     
      $form  = new Formulario("form");
      $id = (int) $this->params()->fromRoute('id', 0);
      //  valores iniciales formulario   (C)
      $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
      $t = new LogFunc($this->dbAdapter);
      $dt = $t->getDatLog();      
      $this->dbAdapter = $this->getServiceLocator()->get($dt['db']);// Conector base de datos cliente      
      $d=new AlbumTable($this->dbAdapter);
      $f = new Funciones($this->dbAdapter);            
      // --      
          // INICIO DE TRANSACCIONES
              $connection = null;
              try 
              {
                $connection = $this->dbAdapter->getDriver()->getConnection();
                $connection->beginTransaction();            
                $dat = $d->getGeneral1("select count(id) as num 
                          from c_cotiza_d where idDoc = ".$id);
                if ($dat['num']>0)
                    $d->modGeneral("update c_cotiza_c set anulado = 1, idUsuAnu = ".$dt['idUsu'].", fecAnu = '".$dt['fecSis']."'  
                                        where id = ".$id);                
                else   
                    $d->modGeneral("delete from c_cotiza_c where id = ".$id);

                $connection->commit();
                return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().$this->lin);                
                $this->flashMessenger()->addMessage('');
              }// Fin try casth   
              catch (\Exception $e) 
              {
                 if ($connection instanceof \Zend\Db\Adapter\Driver\ConnectionInterface) {
                     $connection->rollback();
                       echo $e;
                 } 
              /* Other error handling */
              }// FIN TRANSACCION                                             
      $view = new ViewModel();        
      $this->layout("layout/blancoC");
      return $view;            
    }
   // Eliminar registro 
   public function listidAction()
   {     
      $form  = new Formulario("form");
      $id = (int) $this->params()->fromRoute('id', 0);
      //  valores iniciales formulario   (C)
      $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
      $t = new LogFunc($this->dbAdapter);
      $dt = $t->getDatLog();      
      $this->dbAdapter = $this->getServiceLocator()->get($dt['db']);// Conector base de datos cliente      
      $d=new AlbumTable($this->dbAdapter);
      $f = new Funciones($this->dbAdapter);            
      // --      
              // INICIO DE TRANSACCIONES
              $connection = null;
              try 
              {
                $connection = $this->dbAdapter->getDriver()->getConnection();
                $connection->beginTransaction();            
                $dat = $d->getGeneral1("select idDoc 
                          from c_cotiza_d where id = ".$id);

                $d->modGeneral("delete from c_cotiza_d_a where idIdoc = ".$id);               
                $d->modGeneral("delete from c_cotiza_d_lot where idIdoc = ".$id);
                $d->modGeneral("delete from c_cotiza_d where id = ".$id);

                // Totalizar pedido
                $f->getTotales($dat['idDoc']);

                $connection->commit();
                return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().$this->lin.'v/'.$dat['idDoc']);                
                $this->flashMessenger()->addMessage('');
              }// Fin try casth   
              catch (\Exception $e) 
              {
                 if ($connection instanceof \Zend\Db\Adapter\Driver\ConnectionInterface) {
                     $connection->rollback();
                       echo $e;
                 } 
              /* Other error handling */
              }// FIN TRANSACCION                                             

      $view = new ViewModel();        
      $this->layout("layout/blancoC");
      return $view;            
    }    
   // Editar descuento 
   public function listdescAction()
   {     
      $form  = new Formulario("form");
      //  valores iniciales formulario   (C)
      $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
      $t = new LogFunc($this->dbAdapter);
      $dt = $t->getDatLog();      
      $this->dbAdapter = $this->getServiceLocator()->get($dt['db']);// Conector base de datos cliente      
      $d=new AlbumTable($this->dbAdapter);
      // --      
      if($this->getRequest()->isPost()) // Si es por busqueda
      {
          $request = $this->getRequest();
          $data = $this->request->getPost();
          if ($request->isPost()) 
          {

             $diasFinan = (float) $data->diasFinan;
             $descFinan = (float) $data->descFinan;
             $diasPlazo = (int) $data->diasPlazo;

             if ($descFinan>0)
             {    
                $d->modGeneral("update c_cotiza_c  
                      set diasFinan=".$diasFinan.", 
                          descFinan=".$descFinan.",
                          diasPlazo=".$diasPlazo.",
                          comen='".$data->comen."',  
                          idUsuDesc = ".$dt['idUsu']."  
                      where id = ".$data->id);  
             }else{
                $d->modGeneral("update c_cotiza_c  
                      set comen='".$data->comen."',
                          diasPlazo=".$diasPlazo."  
                        where id = ".$data->id);                 
             }   
          }
      }
      $view = new ViewModel();        
      $this->layout("layout/blancoC");
      return $view;            
    }
    // Buscar datos de materiales
    public function listbuscliAction() 
    {
        $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
        $t = new LogFunc($this->dbAdapter);
        $dt = $t->getDatLog();      
        $this->dbAdapter = $this->getServiceLocator()->get($dt['db']);// Conector base de datos cliente        

        $form = new Formulario("form");        
        $d = new AlbumTable($this->dbAdapter); 

        if($this->getRequest()->isPost()) // Actulizar datos
        {
           $request = $this->getRequest();
           if ($request->isPost()) 
           {
              $data = $this->request->getPost();
              $buscar = $data->buscar;

           }// Fin guardado datos
        }

      if ($buscar == '')  
          $buscar = 'XXXXXXXXXXX';

      $buscar = strtoupper(ltrim(rtrim($buscar)));

      //$con = " ( MATCH(a.nit, a.nombre, a.nomComer, a.cedRepr, a.nomRepr, a.apeRepr) AGAINST ('+".str_replace(" ", "* +", $buscar)."*' IN BOOLEAN MODE) )";

      $con = " ( a.nit like '%".$buscar."%' or 
                 a.nombre like '%".$buscar."%' or 
                 a.nomComer like '%".$buscar."%' or 
                 a.cedRepr like '%".$buscar."%' or 
                 a.nomRepr like '%".$buscar."%' or 
                 a.apeRepr like '%".$buscar."%' )";

      $idVen = 0;  
      if ( $dt['idVen'] > 0 )
          $con = $con.' and f.id =  '.$dt['idVen'];

      $valores=array
      (
         "form"    => $form,
         "datos"   => $d->getGeneral("select b.id,a.id as idCli, a.nit, a.direccion, a.telefonos, a.email, a.nombre, c.nombre as nomEmp, e.nombre as nomGrupo,f.nombre as nomVen,a.emailFe                     
                          from c_terceros a 
                                   left join c_terceros_emp b on b.idTer = a.id 
                                   left join c_empresas c on c.id = b.idEmp 
                                   left join c_grupo e on e.id = a.idGrupo 
                                   left join c_vendedores_ter d on d.idTer = a.id 
                                   left join c_vendedores f on f.id = d.idVen 
                          where a.id > 0 and ".$con),
         'url'     => $this->getRequest()->getBaseUrl(),
         "lin"     => $this->lin,       
         "ttablas"   =>"CLIENTE,OK",
      );                
      $view = new ViewModel($valores);        
      $this->layout('layout/blancoC'); 
      return $view;                
   }       
   // Seleccionar busquedas clientes
   public function listbusselcliAction() 
   {
      $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
      $t = new LogFunc($this->dbAdapter);
      $dt = $t->getDatLog();      
      $this->dbAdapter = $this->getServiceLocator()->get($dt['db']);// Conector base de datos cliente        
        $d = new AlbumTable($this->dbAdapter); 

        if($this->getRequest()->isPost()) // Actulizar datos
        {
           $request = $this->getRequest();
           if ($request->isPost()) 
           {
              $data = $this->request->getPost();
              $id = $data->id;

           }// Fin guardado datos
        }
      $valores=array
      (
         "maVen"   => $data->buscar,
         "datMat"   => $d->getGeneral("select a.id, a.codigo, a.nombre
                                from i_articulos a  
                          where a.id =".$id),
         "datos"   => $d->getArticulosExist($id),
         "datPre"  => $d->getPresentacionesArti($id), 
         "datPrec" => $d->getPreciosArti($id,0), 
      );                
      $view = new ViewModel($valores);        
      $this->layout('layout/blancoC'); 
      return $view;                
   }              
   // Detalle del pedido
   public function listadAction() 
   { 
      $form = new Formulario("form");
      //  valores iniciales formulario   (C)
      $id = (int) $this->params()->fromRoute('id', 0);
      $form->get("id")->setAttribute("value",$id);                       

      $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
      $t = new LogFunc($this->dbAdapter);
      $dt = $t->getDatLog();      
      $this->dbAdapter = $this->getServiceLocator()->get($dt['db']);// Conector base de datos cliente      
      $d = New AlbumTable($this->dbAdapter);      
      $g = new GraficosTable($this->dbAdapter);
      $t = new LogFunc($this->dbAdapter);
      $dat = $d->getGeneral1("select buscBitaAjax,controlExis,manLote,busEmp,codInt,filtroPres 
                                   from c_general");

      $busEmp = (int) $dat['busEmp'];
      $buscBitaAjax = $dat['buscBitaAjax'];
      $controlExis = $dat['controlExis'];       
      $manLote = $dat['manLote'];       
      $filtroPres = $dat['filtroPres']; 

      $datUsu = $d->getGeneral1('select aprobarPedidos, idVen, digitador, admin   
                                     from cythrmr0_master_fv.users where id='.$dt['idUsu']);  
      $admin = $datUsu['admin'];
      $digitador = $datUsu['digitador'];
      $aprobarPedidos = $datUsu['aprobarPedidos'];

      $arreglo = array();  
      $datos = $d->getPresentaciones(" ");
      foreach ($datos as $dat)
      {
             $idc = $dat['id']; $nom = $dat['nombre'];
             $arreglo[$idc]= $nom;
      }    
      $form->get("idPres")->setValueOptions($arreglo);                          
      

      //empresas
      $arreglo='';
      $datos = $d->getEmpresas('');
      foreach ($datos as $dat)
      {
           $idc=$dat['id'];$nom =$dat['nombre'];
           $arreglo[$idc]= $nom;
      }      
      if ($arreglo != '') 
          $form->get("idEmp")->setValueOptions($arreglo); 

      // Clientes
      $arreglo='';
      $con = '';
      $tipo = 0 ;
      if ( ($digitador==1) or ($admin==1) or ($aprobarPedidos==1) )
         $datos = $d->getTerceros($con, $tipo);
      else   
         $datos = $d->getTercerosVen($datUsu['idVen']);
      foreach ($datos as $dat)
      {
        $idc=$dat['id'];$nom=$dat['nit'].' - '.$dat['nombre'];
        $arreglo[$idc]= $nom;
      }      
      if ($arreglo != '') 
          $form->get("idCli")->setValueOptions($arreglo);              

      // Articulos
      $arreglo = '';  
      $arreglo[0] = 'TODOS';
      $datos = $d->getMarcas(" ");
      foreach ($datos as $dat)
      {
            $idc = $dat['id']; $nom = $dat['nombre'];
            $arreglo[$idc]= $nom;
      }      
      if ($arreglo != '') 
          $form->get("idMat")->setValueOptions($arreglo);  

      if ($dt['db']=='db4') 
      {
 
         $datos = $d->getMarcasUsu(" and b.idUsu=".$dt['idUsu']);
     
         if (!empty($datos)) {
           
            $arreglo = '';  
            foreach ($datos as $dat)
            {
               $idc = $dat['id']; $nom = $dat['nombre'];
               $arreglo[$idc]= $nom;
            }      
            if ($arreglo != '') 
              $form->get("idMat")->setValueOptions($arreglo);  
        }  
        
      }else{

        if ($buscBitaAjax==0)
        {  
           $arreglo='';
           $datos = $d->getArticulos('');
           foreach ($datos as $dat)
           {
              $idc=$dat['id'];$nom = $dat['codigo'].' '.$dat['nombre'];
              $arreglo[$idc]= $nom;
           }      
           if ($arreglo != '') 
             $form->get("idMat")->setValueOptions($arreglo);              
        }

      }
      
      $date   = new \DateTime(); 
      $fecSis = $date->format('Y-m-d');        
      $form->get("fecDoc")->setAttribute("value",$fecSis);

      $datCot = $d->getGeneral1('select * from c_cotiza_c where id='.$id); 
      $idCli = $datCot['idCli'];
      $estado = $datCot['estado'];
      // Estado
      //$daPer = $d->getPermisos($this->lin); // Permisos de esta opcion
      if ($estado==0)
      {  
         if ($id == 0)
         {
            $val=array
            (
               "0"  => 'Revisión',
            );    
         }else{ 
              $val=array
              (
                 "0"  => 'Revisión',
                 "1"  => 'Solicitar aprobación'              
              );    
         }              
      }else{

         if ($aprobarPedidos==1)
         {
           $val=array
           (
              "1"  => 'Esperando aprobación',          
              "2"  => 'Aprobar pedido',
              "3"  => 'Cancelar pedido',              
              "4"  => 'Devolver pedido al vendedor'                          
           );                          
         }else{ 
           $val=array
           (
              "1"  => 'Esperando aprobación',          
           );       
         }                     
      }  

      $form->get("estado")->setValueOptions($val);
      $arreglo = '';
      $datos = $d->getTiposCotiza(""); // Permisos de esta opcion
      foreach ($datos as $dat)
      {
         $idc = $dat['id']; $nom = $dat['nombre'];
         $arreglo[$idc] = $nom;
      }
      if ($arreglo != '') 
          $form->get("tipo")->setValueOptions($arreglo);          

      $arreglo = '';
      $datos = $d->getGeneral("select * from cythrmr0_master_fv.users where firmDigita=1 ");
      foreach ($datos as $dat)
      {
         $idc = $dat['id']; $nom = $dat['usuario'];
         $arreglo[$idc] = $nom;
      }
      if ($arreglo != '') 
          $form->get("idUsu")->setValueOptions($arreglo);          

      $arreglo = '';  
      $arreglo[0] = 'TODOS';
      $datos = $d->getMarcas("");
      foreach ($datos as $dat)
      {
        $idc = $dat['id']; $nom = $dat['nombre'];
        $arreglo[$idc]= $nom;
      }      
      if ($arreglo != '') 
          $form->get("tipo")->setValueOptions($arreglo);              

      $valores=array
      (
           "titulo"  => $this->tfor,
           "form"    => $form,
           "datCot"  => $d->getGeneral1('select * from c_cotiza_c where id='.$id), 
           "datVen"  => $d->getGeneral1('select count(a.id) as num, a.idVen ,b.nombre  
                                           from cythrmr0_master_fv.users a 
                                             inner join c_vendedores b on b.id = a.idVen 
                                        where a.id = '.$dt['idUsu']), 
           'url'     => $this->getRequest()->getBaseUrl(),
           'id'      => $id,
           "datCont" => $g->getPedidos($idCli),
           "datFact" => $d->getFactCli($idCli),
           "datDcot" => $d->getCotDetCla($id), 
           'datCodIntCla'=> $d->getCodIntCla(" order by a.orden"), 
           'datCodInt'   => $d->getCodInt("  order by b.orden"), 
           'digitador' =>$digitador,
           'aprobarPedidos' => $aprobarPedidos,
           'buscBitaAjax' => $buscBitaAjax,
           'controlExis' => $controlExis,
           'estado' => $estado,
           'manLote' => $manLote,
           'busEmp' => $busEmp,
           "lin"     => $this->lin,
           "codInt"  => $codInt,   
      );       
      // ------------------------ Fin valores del formulario 
       if ($id > 0) // Cuando ya hay un registro asociado
       {
            $datos = $d->getCotiza(" and a.id = ".$id);
            // Valores guardados
            foreach ($datos as $dato) 
            {
               $form->get("estado")->setAttribute("value",$dato['estado']);
               $form->get("tipo")->setAttribute("value",$dato['tipo']);
               $form->get("idVen")->setAttribute("value",$dato['idVen']);
               $form->get("fecDoc")->setAttribute("value",$dato['fecDoc']);
               $form->get("idCli")->setAttribute("value",$dato['idCli']);
            } 
       }            
      $view = new ViewModel($valores);        
      $this->layout('layout/layoutTurnos'); 
      return $view;                
   } // Fin actualizar datos    

   // ver detalle 
   public function listvAction() 
   {
      $id = (int) $this->params()->fromRoute('id', 0);
      
      $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
      $t = new LogFunc($this->dbAdapter);
      $dt = $t->getDatLog();      
      $this->dbAdapter = $this->getServiceLocator()->get($dt['db']);// Conector base de datos cliente        
      $form = new Formulario("form");
      $d = new AlbumTable($this->dbAdapter); 
      $f = new Funciones($this->dbAdapter);
      
      $arreglo = '';
      $datos = $d->getFormasPagos("");
      foreach ($datos as $dat)
      {
        $idc=$dat['id'];$nom = $dat['nombre'];
        $arreglo[$idc]= $nom;
      }      
      if ($arreglo != '') 
          $form->get("idFor")->setValueOptions($arreglo);

      $datUsu = $d->getGeneral1('select aprobarPedidoApp 
                                     from cythrmr0_master_fv.users a
                                         left join c_vendedores b on b.id = a.idVen   
                           where a.id='.$dt['idUsu']);  
      $aprobaPedido = $datUsu['aprobarPedidoApp'];
      $f->getTotales($id);// Calcular totales 
      //MANDAR LA FORMA DE PAGO

      $datCot    = $d->getCotizaC($id);
      $datForPag  = $d->getFormasPagosId($datCot['idFor']);
      
      $valores=array
      (
         "titulo"  => "Detalle del pedido",
         "id"      => $id,        
         "form"    => $form,
         "datCot"  => $d->getCotizaC($id),
         "datCon"  => $d->getConfiguraG(''),
         "datos"   => $d->getCotizaD(" and a.idDoc =".$id),
         "datTot"  => $d->getCotizaTotal($id),         
         "datCan"  => $d->getGeneral1("select sum(a.cantidad) as num 
                                from c_cotiza_d a where a.idDoc = ".$id),
         "datTotEmp"  => $d->getTolPedidoEmp($id),
         'url'     => $this->getRequest()->getBaseUrl(),
         "ttablas" => $this->ttab,         
         "aprobaPedido" => $aprobaPedido,
         "formaPago"    => $datForPag['nombre'],
         "lin"     => $this->lin         
      );                
      $view = new ViewModel($valores);        

      return $view;                
   }       
   //gestionar pedido 
   public function listgpAction() 
   {
      $id = (int) $this->params()->fromRoute('id', 0);
      
      $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
      $t = new LogFunc($this->dbAdapter);
      $dt = $t->getDatLog();      
      $this->dbAdapter = $this->getServiceLocator()->get($dt['db']);// Conector base de datos cliente        
      $form = new Formulario("form");
      $d    = new AlbumTable($this->dbAdapter); 
      $f    = new Funciones($this->dbAdapter);
      
      $arreglo = '';
      $datos = $d->getFormasPagos("");
      foreach ($datos as $dat)
      {
        $idc=$dat['id'];$nom = $dat['nombre'];
        $arreglo[$idc]= $nom;
      }      
      if ($arreglo != '') 
          $form->get("idFor")->setValueOptions($arreglo);

      $datUsu = $d->getGeneral1('select aprobarPedidoApp 
                                     from cythrmr0_master_fv.users a
                                         left join c_vendedores b on b.id = a.idVen   
                           where a.id='.$dt['idUsu']);  
      $aprobaPedido = $datUsu['aprobarPedidoApp'];
      $f->getTotales($id);// Calcular totales 
      //MANDAR LA FORMA DE PAGO

      $datCot    = $d->getCotizaC($id);
      $datForPag  = $d->getFormasPagosId($datCot['idFor']);
      
      $valores=array
      (
         "titulo"  => "Detalle del pedido",
         "id"      => $id,        
         "form"    => $form,
         "datCot"  => $d->getCotizaC($id),
         "datos"   => $d->getCotizaD(" and a.idDoc =".$id),
         "datTot"  => $d->getCotizaTotal($id),         
         "datCan"  => $d->getGeneral1("select sum(a.cantidad) as num 
                                from c_cotiza_d a where a.idDoc = ".$id),
         "datTotEmp"  => $d->getTolPedidoEmp($id),
         'url'     => $this->getRequest()->getBaseUrl(),
         "ttablas" => $this->ttab,         
         "aprobaPedido" => $aprobaPedido,
         "formaPago"    => $datForPag['nombre'],
         "lin"     => $this->lin         
      );                
      $view = new ViewModel($valores);        

      return $view;                
   }       
   // Enviar pedido
   public function listepAction()
   {
        $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
        $t = new LogFunc($this->dbAdapter);
        $dt = $t->getDatLog();      
        $this->dbAdapter = $this->getServiceLocator()->get($dt['db']);// Conector base de datos cliente        
        $t = new LogFunc($this->dbAdapter);
        $dt = $t->getDatLog(); 
        $d = new AlbumTable($this->dbAdapter); 

        if($this->getRequest()->isPost()) // Actulizar datos
        {
           $request = $this->getRequest();
           if ($request->isPost()) 
           {
              $data = $this->request->getPost();

              // INICIO DE TRANSACCIONES
              $connection = null;
              try 
              {
                  $connection = $this->dbAdapter->getDriver()->getConnection();
                  $connection->beginTransaction();                            
                  $id = $data->id;
                  $dat = $d->getCotizaC($data->id);
                  $datNomUsu = $d->getUsuEspe($dt['idUsu']);
                  $comen = ' ...... /  El '.$dt['fecSis'].' - '.$datNomUsu['usuario'].' dijo : '.$data->comenReg;
                
                  $estado = $dat['vlrEstado'];
                  $idUsu  = $dt['idUsu'];
                  //si es dos se asigna el usuario que diga en la tabla de califica/ modificado---2/06/2020 
                  if ($dat['vlrEstado']==2)
                     $idUsu = $dat['idUsuApr'];
                          
                  if ($dat['validaCupoC']==1) 
                  {
                      $datUsa = $d->getGeneral1("select fu_cartera_cli(".$dat['idCli'].") as usado");
                      $usado =  $datUsa['usado'];
                      $valor =  $dat['cupoCred'] - $usado - $dat['vlrTotal'];
                 
                      if ($valor>0) {
                          $estado = 2;
                          $idUsu = $dat['idUsuApr'];
                          $d->modGeneral("update c_cotiza_c 
                            set idUsuA = ".$idUsu.", 
                                fecApr = '".$dt['fecSis']."',
                                comen = '".ltrim($dat['comen'].$comen)."',
                                estado = ".$estado.",
                                clienteEstrella = ".$estado.",
                                idUsuA2 = ".$dt['idUsu'].", 
                                fecApr2 = '".$dt['fecSis']."',
                                valorFlete = ".$data->flete."  
                            where id = ".$data->id);
                      }else{
                          $d->modGeneral("update c_cotiza_c 
                          set idUsuA = ".$idUsu.", 
                              fecApr = '".$dt['fecSis']."',
                              clienteEstrella = ".$estado.",
                              comen = '".ltrim($dat['comen'].$comen)."',
                              estado = ".$estado." ,
                              valorFlete = ".$data->flete." 
                          where id = ".$data->id); 
                      }
                  }else{

                      if ( $estado == 0 ) // Si el cliente tiene calificacion 0, debe ser enviado ene stado 1 
                          $estado = 1;

                      $d->modGeneral("update c_cotiza_c 
                        set idUsuA = ".$idUsu.", 
                            fecApr = '".$dt['fecSis']."',
                            clienteEstrella = ".$estado.",
                            comen = '".ltrim($dat['comen'].$comen)."',
                            estado = ".$estado." ,
                            valorFlete = ".$data->flete." 

                        where id = ".$data->id); 
                  }   
                  $connection->commit();
                  $this->flashMessenger()->addMessage('');
              }// Fin try casth   
              catch (\Exception $e) 
              {
                 if ($connection instanceof \Zend\Db\Adapter\Driver\ConnectionInterface) {
                     $connection->rollback();
                       echo $e;
                 } 
              /* Other error handling */
              }// FIN TRANSACCION                                                   
          }// Fin guardado datos
        }
        $valores=array
        (
         "id"      =>  $id,        
         'url'     => $this->getRequest()->getBaseUrl(),
         "estado"  => $data->estado,
         "lin"     => $this->lin         
       );                
       $view = new ViewModel($valores);        
       $this->layout('layout/blancoC'); 
       return $view;                
   } // Fin enviar pedido
   //aprobar pedido
   public function listaprobarpedAction()
   {
        $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
        $t = new LogFunc($this->dbAdapter);
        $dt = $t->getDatLog();      
        $this->dbAdapter = $this->getServiceLocator()->get($dt['db']);// Conector base de datos cliente        
        $t = new LogFunc($this->dbAdapter);
        $dt = $t->getDatLog(); 
        $d = new AlbumTable($this->dbAdapter); 

        if($this->getRequest()->isPost()) // Actulizar datos
        {
           $request = $this->getRequest();
           if ($request->isPost()) 
           {
              $data = $this->request->getPost();

              // INICIO DE TRANSACCIONES
              $connection = null;
              try 
              {
                  $connection = $this->dbAdapter->getDriver()->getConnection();
                  $connection->beginTransaction();                            
                  $id = $data->id;
                  $d->modGeneral("update c_cotiza_c 
                              set idUsuA = ".$dt['idUsu'].", 
                                  fecApr = '".$dt['fecSis']."',
                                  estado = 2 
                            where id = ".$data->id);                         
                      
                  $connection->commit();
                  $this->flashMessenger()->addMessage('');
              }// Fin try casth   
              catch (\Exception $e) 
              {
                 if ($connection instanceof \Zend\Db\Adapter\Driver\ConnectionInterface) {
                     $connection->rollback();
                       echo $e;
                 } 
              /* Other error handling */
              }// FIN TRANSACCION                                                   
          }// Fin guardado datos
        }
        $valores=array
        (
         "id"      =>  $id,        
         'url'     => $this->getRequest()->getBaseUrl(),
         "estado"  => $data->estado,
         "lin"     => $this->lin         
       );                
       $view = new ViewModel($valores);        
       $this->layout('layout/blancoC'); 
       return $view;                
        
   } // Fin enviar pedido

   // Eliminar pedido
   public function listcpAction()
   {
        $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
        $t = new LogFunc($this->dbAdapter);
        $dt = $t->getDatLog();      
        $this->dbAdapter = $this->getServiceLocator()->get($dt['db']);// Conector base de datos cliente        
        $d = new AlbumTable($this->dbAdapter); 
        $inactivar = 1;
        if($this->getRequest()->isPost()) // Actulizar datos
        {
           $request = $this->getRequest();
           if ($request->isPost()) 
           {
              $data = $this->request->getPost();
              // INICIO DE TRANSACCIONES
              $connection = null;
              try 
              {
                    $connection = $this->dbAdapter->getDriver()->getConnection();
                    $connection->beginTransaction();                              
                    $id = $data->id;
                    if ($data->inactivar!=null)//ESTO ES PARA DEVOLVER Y CANCELAR
                    {
                       $d->modGeneral("update c_cotiza_c 
                                      set estado = ".$data->inactivar." 
                                         where id=".$data->id); 
                       $inactivar = $data->inactivar;
                    } 
                    else//NO APROBAR PEDIDO
                    {
                       $d->modGeneral("update c_cotiza_c 
                                      set estado = 5
                                         where id=".$data->id);
                    }
                    $connection->commit();
                    $this->flashMessenger()->addMessage('');

              }// Fin try casth   
              catch (\Exception $e) 
              {
                 if ($connection instanceof \Zend\Db\Adapter\Driver\ConnectionInterface) {
                     $connection->rollback();
                       echo $e;
                 } 
              /* Other error handling */
              }// FIN TRANSACCION                                                          

           }// Fin guardado datos
        }
        $valores=array
        (
         "id"         =>  $id,        
         'url'        => $this->getRequest()->getBaseUrl(),
         "estado"     => $data->estado,
         "inactivar"  => $inactivar,
         "lin"     => $this->lin         
       );                
       $view = new ViewModel($valores);        
       $this->layout('layout/blancoC'); 
       return $view;                
    } // Fin eliminar pedido   

   // ver informe de ventas
   public function listvdAction() 
   {
      $id = (int) $this->params()->fromRoute('id', 0);
      $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
      $t = new LogFunc($this->dbAdapter);
      $dt = $t->getDatLog();      
      $this->dbAdapter = $this->getServiceLocator()->get($dt['db']);// Conector base de datos cliente        
      $form = new Formulario("form");
      $d = new AlbumTable($this->dbAdapter); 
      $f = new Funciones($this->dbAdapter);

      $date   = new \DateTime(); 
      $fecSis = $date->format('Y-m-d');        

      $form->get("fechaIni")->setAttribute("value",$fecSis);
      $form->get("fechaFin")->setAttribute("value",$fecSis);

      $arreglo[0] = "TODOS";
      $arreglo[1] = "EN ESPERA";
      $arreglo[2] = "APROBADOS";
      $arreglo[3] = "EN DESPACHO";
      $arreglo[7] = "FACTURADOS";
      $form->get("tipoC")->setValueOptions($arreglo);     
     

      $arreglo = '';  
      $arreglo[0] = 'Seleccione...';
      $datos = $d->getTercerosVen($dt['idVen']);
      foreach ($datos as $dat)
      {
          $idc = $dat['id']; $nom = $dat['nombre'];
          $arreglo[$idc]= $nom;
      } 
      $form->get("idCli")->setValueOptions($arreglo);           
      $t = new LogFunc($this->dbAdapter);
      $dt = $t->getDatLog();  
      $valores=array
      (
         "titulo"  => "Detalle del pedido",
         "id"      => $id,        
         "form"    => $form,
         "datos"   => $d->getCotizaD(" and a.idDoc =".$id),
         "datTot"  => $d->getCotizaTotal($id),         
         'url'     => $this->getRequest()->getBaseUrl(),
         "ttablas" => $this->ttab,         
         "lin"     => $this->lin,
         "admin"   => $dt['admin']      
      );                
      $view = new ViewModel($valores);        

      return $view;                
   }  // Ventas        

   // Informe de ventas
   public function listvdrAction() 
   {
      $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
      $t = new LogFunc($this->dbAdapter);
      $dt = $t->getDatLog();      
      $this->dbAdapter = $this->getServiceLocator()->get($dt['db']);// Conector base de datos cliente
      $form = new Formulario("form");        
      $d = new AlbumTable($this->dbAdapter); 
      if($this->getRequest()->isPost()) // Actulizar datos
      {
           $request = $this->getRequest();
           if ($request->isPost()) 
           {
              $data = $this->request->getPost();
              $idCli ="";
              $tipo ="";
              if ($data->idCli!=0)
              {
                 $idCli =" and a.idCli = ".$data->idCli;
              }
              if ($data->tipo!=0)
              {
                 $tipo =" and a.estado = ".$data->tipo;
              }
              $datUsu = $d->getGeneral1('select aprobarPedidos, aprobarPedidoApp,idVen, digitador, lower(b.nombre) as nombre     
                                                from cythrmr0_master_fv.users a
                                                      left join c_vendedores b on b.id = a.idVen   
                                                where a.id='.$dt['idUsu']);  

               

              $aprobarPedido = $datUsu['aprobarPedidoApp'];
           
              $idVen = $datUsu['idVen'];
              $fechaI = $data->fechaI;
              $fechaF = $data->fechaF;
              $con = $idCli.$tipo." and convert(a.fecDoc, DATE)  between '".$fechaI."' and '".$fechaF."' and c.id = ".$idVen." group by a.id";
              $nomCli = '';
              if ( $dt['admin']==1) {//cuando es admin ignora todas las restricciones
                   if ( $data->nomCli!='') {//para los pedidos 
                     $nomCli   = " and  (  b.nombre like '%".$data->nomCli."%' or  b.nit like '%".$data->nomCli."%')";
                   }
                   
                   $con = $idCli.$tipo." and convert(a.fecDoc, DATE)  between '".$fechaI."' and '".$fechaF."'  ".$nomCli." group by a.id";

                   if ( $data->nomCli!='') {//para el totalizado
                     $nomCli   = " and  (  c.nombre like '%".$data->nomCli."%' or  c.nit like '%".$data->nomCli."%')";
                   }
              }
              $consulta = "select sum(a.vlrTotal) as vlrTotal 
                                  from c_cotiza_c a 
                                      inner join c_vendedores b on b.id = a.idVen
                                      inner join c_terceros c on c.id = a.idCli 
                                  where  convert(a.fecDoc, DATE) between  '".$fechaI."' and '".$fechaF."' and b.id = ".$idVen.$idCli.$tipo.$nomCli.'  group by a.id';
            }// Fin guardado datos
      }
      // Si la empresa no trabaja productos controlados  
      $valores=array
      (
         "form"    => $form,
         "datos"   => $d->getGeneral1($consulta),//totalizado
         "datDet"  => $d->getVendResGeneral($con), 
         'url'     => $this->getRequest()->getBaseUrl(),
         "lin"     => $this->lin,
         "aprobarPedido"  => $aprobarPedido,
         "ttablas"       =>"CODIGO,ARTICULO,OK",
      );                
      $view = new ViewModel($valores);        
      $this->layout('layout/blancoC'); 
      return $view;                
   }  
    // Informe de ventas
   public function listvdrsnAction() 
   {
      $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
      $t = new LogFunc($this->dbAdapter);
      $dt = $t->getDatLog();      
      $this->dbAdapter = $this->getServiceLocator()->get($dt['db']);// Conector base de datos cliente
      $form = new Formulario("form");        
      $d = new AlbumTable($this->dbAdapter); 
      if($this->getRequest()->isPost()) // Actulizar datos
      {
           $request = $this->getRequest();
           if ($request->isPost()) 
           {
              $data = $this->request->getPost();
              $idCli ="";
              $tipo ="";
              if ($data->idCli!=0)
              {
                 $idCli =" and a.idCli = ".$data->idCli;
              }
             
              $datUsu = $d->getGeneral1('select aprobarPedidoApp,idVen,  lower(b.nombre) as nombre     
                                                from cythrmr0_master_fv.users a
                                                      left join c_vendedores b on b.id = a.idVen   
                                                where a.id='.$dt['idUsu']);  

               
              $aprobarPedido = $datUsu['aprobarPedidoApp'];
              $fechaI = $data->fechaI;
              $fechaF = $data->fechaF;
              $consulta = "select sum(a.vlrTotal) as vlrTotal 
                                  from c_cotiza_c a 
                                      inner join c_vendedores b on b.id = a.idVen
                                      inner join c_terceros c on c.id = a.idCli 
                                  where  convert(a.fecDoc, DATE) between  '".$fechaI."' and '".$fechaF."' ".$idCli." and a.estado not in (9) AND
                       
                       fu_ped_saldo_neg(a.id) = 'SI'  group by a.id";
              $con = $idCli." and convert(a.fecDoc, DATE)  between '".$fechaI."' and '".$fechaF."' and a.estado not in (9,0) AND fu_ped_saldo_neg(a.id) = 'SI' group by a.id";
            
           }// Fin guardado datos
      }
      // Si la empresa no trabaja productos controlados  
      $valores=array
      (
         "form"    => $form,
         "datos"   => $d->getGeneral1($consulta),
         "datDet"  => $d->getVendResGeneral($con), 
         'url'     => $this->getRequest()->getBaseUrl(),
         "lin"     => $this->lin,
         "aprobarPedido"  => $aprobarPedido,
         "ttablas"       =>"CODIGO,ARTICULO,OK",
      );                
      $view = new ViewModel($valores);        
      $this->layout('layout/blancoC'); 
      return $view;                
   }                  

   // GUardar obervacion
   public function listvgAction()
   {
        $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
        $t = new LogFunc($this->dbAdapter);
        $dt = $t->getDatLog();      
        $this->dbAdapter = $this->getServiceLocator()->get($dt['db']);// Conector base de datos cliente
        $d = new AlbumTable($this->dbAdapter); 

        if($this->getRequest()->isPost()) // Actulizar datos
        {
           $request = $this->getRequest();
           if ($request->isPost()) 
           {
              $data = $this->request->getPost();

              // INICIO DE TRANSACCIONES
              $connection = null;
              try 
              {
                    $connection = $this->dbAdapter->getDriver()->getConnection();
                    $connection->beginTransaction();                              
                    $id = $data->id;

                    $d->modGeneral("update c_cotiza_c 
                                      set comen = '".$data->comenReg."' 
                                         where id=".$data->id);      

                    $connection->commit();
                    $this->flashMessenger()->addMessage('');
              }// Fin try casth   
              catch (\Exception $e) 
              {
                 if ($connection instanceof \Zend\Db\Adapter\Driver\ConnectionInterface) {
                     $connection->rollback();
                       echo $e;
                 } 
              /* Other error handling */
              }// FIN TRANSACCION                                                          

           }// Fin guardado datos
        }

      $valores=array
      (
         "id"      =>  $id,        
         'url'     => $this->getRequest()->getBaseUrl(),
         "lin"     => $this->lin         
      );                
      $view = new ViewModel($valores);        
      $this->layout('layout/blancoC'); 
      return $view;                
        
   } // Fin eliminar pedido   
   //ver promociones
   public function listpAction() 
   {
     $id = (int) $this->params()->fromRoute('id', 0);
      $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
      $t = new LogFunc($this->dbAdapter);
      $dt = $t->getDatLog();      
      $this->dbAdapter = $this->getServiceLocator()->get($dt['db']);// Conector base de datos cliente        
      $form = new Formulario("form");
      $d = new AlbumTable($this->dbAdapter); 
      $f = new Funciones($this->dbAdapter);
      $date   = new \DateTime(); 
      $fecSis = $date->format('Y-m-d'); 

      $valores=array
      (
         "titulo"  => "Artículos en promoción",
         "id"      => $id,        
         "form"    => $form,
         "datos"   => $d->getPromocionesDeta(" where  b.fechaF >= "."'".$fecSis."' and a.estado = 0"),
         'url'     => $this->getRequest()->getBaseUrl(),
         "ttablas" => $this->ttab,         
         "lin"     => $this->lin         
      );                
      $view = new ViewModel($valores);        

      return $view;                
   } 
  //ver lista de precio
  public function listlpAction() 
  {
      $id = (int) $this->params()->fromRoute('id', 0);
      $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
      $t = new LogFunc($this->dbAdapter);
      $dt = $t->getDatLog();      
      $this->dbAdapter = $this->getServiceLocator()->get($dt['db']);// Conector base de datos cliente        
      $form = new Formulario("form");
      $d = new AlbumTable($this->dbAdapter); 
      $f = new Funciones($this->dbAdapter);

      $datos = $d->getListaPrecios('');
      foreach ($datos as $dat)
      {
         $idc = $dat['id']; $nom = $dat['nombre'];
         $arreglo[$idc] = $nom;
      }
      if ($arreglo != '') 
          $form->get("idLista")->setValueOptions($arreglo);

      $arreglo = ''; 
      $arreglo[0]= 'TODAS'; 
      $datos = $d->getMarcas("");
      foreach ($datos as $dat)
      {
        $idc = $dat['id']; $nom = $dat['nombre'];
        $arreglo[$idc]= $nom;
      }      
      if ($arreglo != '') 
          $form->get("idMarca")->setValueOptions($arreglo);  

      $valores=array
      (
         "titulo"  => "Listas de precios",
         "id"      => $id,        
         "form"    => $form,
         'url'     => $this->getRequest()->getBaseUrl(),
         "ttablas" => $this->ttab,         
         "lin"     => $this->lin         
      );                
      $view = new ViewModel($valores);        

      return $view;                
   }
   //buscar lista de precio 
   public function listblAction()
   { 
      $form = new Formulario("form");
      //  valores iniciales formulario   (C)
      $id = $this->params()->fromRoute('id', 0);
      $form->get("id")->setAttribute("value",$id);         
      $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
      $t = new LogFunc($this->dbAdapter);
      $dt = $t->getDatLog();      
      $this->dbAdapter = $this->getServiceLocator()->get($dt['db']);// Conector base de datos clienteº;
      $d = new AlbumTable($this->dbAdapter);
      if($this->getRequest()->isPost()) // Actualizar 
      {
        $t  = new LogFunc($this->dbAdapter);
        $dt = $t->getDatLog();
        $request = $this->getRequest();
        $sesion=new Container('sesion');
       // echo $sesion->empAct;
        if ($request->isPost()) 
        {
          
          $data = $this->request->getPost();
          $con ='';
          $marca ='';
          if ($data->criterio!='') 
          {
             $con = " and  ( MATCH(a.codigo, a.nombre) AGAINST ('+".str_replace(" ", "* +",$data->criterio)."*' IN BOOLEAN MODE) )";
          }
        
          if ($data->idMarca!=0) 
          {
             $marca = " and d.id = ".$data->idMarca;
          }
        }
        $valores=array
        (
            "datos"   => $d->getListaPrecioDetalle(" and c.id = ".$data->idLista.$con.$marca),
            "id"      => $data->id,
            "form"    => $form,
            "ttablas" => "",
            "lin"     => $this->lin
        );             
              
      }      
      $view = new ViewModel($valores);        
      $this->layout('layout/blancoC'); // Layout del login
      return $view;        
   }       

   // Eliminar items de pedido ajas 
   public function listidaAction()
   {     
      $form  = new Formulario("form");
      //  valores iniciales formulario   (C)
      $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
      $t = new LogFunc($this->dbAdapter);
      $dt = $t->getDatLog();      
      $this->dbAdapter = $this->getServiceLocator()->get($dt['db']);// Conector base de datos cliente      
      $d=new AlbumTable($this->dbAdapter);
      $f = new Funciones($this->dbAdapter);            
      // --      
      if($this->getRequest()->isPost()) // Si es por busqueda
      {
          $request = $this->getRequest();
          $data = $this->request->getPost();
          $id = $data->id;

          // INICIO DE TRANSACCIONES
          $connection = null;
          try 
          {
                $connection = $this->dbAdapter->getDriver()->getConnection();
                $connection->beginTransaction();            
                $dat = $d->getGeneral1("select idDoc 
                          from c_cotiza_d where id = ".$id);

                $d->modGeneral("delete from c_cotiza_d_a where idIdoc = ".$id);               
                $d->modGeneral("delete from c_cotiza_d_lot where idIdoc = ".$id);
                $d->modGeneral("delete from c_cotiza_d where id = ".$id);

                // Totalizar pedido
                $f->getTotales($dat['idDoc']);

                $connection->commit();
                $this->flashMessenger()->addMessage('');
              }// Fin try casth   
              catch (\Exception $e) 
              {
                 if ($connection instanceof \Zend\Db\Adapter\Driver\ConnectionInterface) {
                     $connection->rollback();
                       echo $e;
                 } 
              /* Other error handling */
              }// FIN TRANSACCION                                             
      }
      $valores=array
      (
        "id"      => $id,
        "idMat"   => $data->idMat,
      );                   
      $view = new ViewModel($valores);        
      $this->layout("layout/blancoC");
      return $view;            
    }

   // Aprobar pedidos
   public function listapAction() 
   {
        $id = (int) $this->params()->fromRoute('id', 0);
        $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
        $t = new LogFunc($this->dbAdapter);
        $dt = $t->getDatLog();      
        $this->dbAdapter = $this->getServiceLocator()->get($dt['db']);// Conector base de datos cliente   
        $t = new LogFunc($this->dbAdapter);
        $dt = $t->getDatLog();     
        $form = new Formulario("form");
        $d = new AlbumTable($this->dbAdapter); 
        $f = new Funciones($this->dbAdapter);
        $t = new LogFunc($this->dbAdapter);
        $dt = $t->getDatLog(); 
        $datUsu = $d->getGeneral1('select idVen, aprobarPedidoApp ,transportador 
                                     from users where id='.$dt['idUsu']);  
      

        $date   = new \DateTime(); 
        $fecSis = $date->format('Y-m-d');        
        $valores=array
        (
          "titulo"  => "Pedidos pendientes por aprobación",
          "id"      => $id,        
          "form"    => $form,
          "datos"   => $d->getCotiza(' and a.estado in (1)'),
          'url'     => $this->getRequest()->getBaseUrl(),
          "ttablas" => "Pedido, ok",         
          "lin"     => $this->lin         
        );                
        $view = new ViewModel($valores);        
        return $view;                
   }  // Ventas        


   // Aprobar pedido
   public function listaprAction()
   {
        $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
        $t = new LogFunc($this->dbAdapter);
        $dt = $t->getDatLog();      
        $this->dbAdapter = $this->getServiceLocator()->get($dt['db']);// Conector base de datos cliente   
        $t = new LogFunc($this->dbAdapter);
        $dt = $t->getDatLog();       
        $d = new AlbumTable($this->dbAdapter); 

        if($this->getRequest()->isPost()) // Actulizar datos
        {
           $request = $this->getRequest();
           if ($request->isPost()) 
           {
              $data = $this->request->getPost();
              // INICIO DE TRANSACCIONES
              $connection = null;
              try 
              {
                    $connection = $this->dbAdapter->getDriver()->getConnection();
                    $connection->beginTransaction();                              
                    $id = $data->id;

                          $d->modGeneral("update c_cotiza_c 
                              set idUsuA = ".$dt['idUsu'].", 
                                  fecApr = '".$dt['fecSis']."',
                                  estado = 2,
                                  idUsuA2 = ".$dt['idUsu'].", 
                                  fecApr2 = '".$dt['fecSis']."' 
                            where id = ".$data->id);

                    $connection->commit();
                    $this->flashMessenger()->addMessage('');
              }// Fin try casth   
              catch (\Exception $e) 
              {
                 if ($connection instanceof \Zend\Db\Adapter\Driver\ConnectionInterface) {
                     $connection->rollback();
                       echo $e;
                 } 
              /* Other error handling */
              }// FIN TRANSACCION                                              

           }// Fin guardado datos
        }
      $valores=array
      (
         "id"      =>  $id,        
         'url'     => $this->getRequest()->getBaseUrl(),
         "lin"     => $this->lin         
      );                
      $view = new ViewModel($valores);        
      $this->layout('layout/blancoC'); 
      return $view;                
        
   } // Fin enviar pedido
   // Buscar datos de materiales
   public function listbusclicAction() 
   {
        $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
        $t = new LogFunc($this->dbAdapter);
        $dt = $t->getDatLog();      
        $this->dbAdapter = $this->getServiceLocator()->get($dt['db']);// Conector base de datos cliente        
        $form = new Formulario("form");        
        $d = new AlbumTable($this->dbAdapter)
        ; 
        if($this->getRequest()->isPost()) // Actulizar datos
        {
           $request = $this->getRequest();
           if ($request->isPost()) 
           {
              $data = $this->request->getPost();
              $id = $data->id;
           }// Fin guardado datos
        }
        $valores=array
       (
         "form"    => $form,
         "datos"   => $d->getCarteraDeta($id." group by a.numFactura "),
         "datosR"  => $d->getCarteraResumid($id),
         'url'     => $this->getRequest()->getBaseUrl(),
         "lin"     => $this->lin,       
         "ttablas"   =>"CLIENTE,OK",
       );                
       $view = new ViewModel($valores);        
       $this->layout('layout/blancoC'); 
       return $view;                
  }  

  //pedido
  public function listdatpedAction() 
  {
      $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
      $t = new LogFunc($this->dbAdapter);
      $dt = $t->getDatLog();      
      $this->dbAdapter = $this->getServiceLocator()->get($dt['db']);// Conector base de datos cliente
      $form = new Formulario("form");        
      $d = new AlbumTable($this->dbAdapter); 
      if($this->getRequest()->isPost()) // Actulizar datos
      {
           $request = $this->getRequest();
           if ($request->isPost()) 
           {
              $data = $this->request->getPost();
              $id = $data->idPed;
           }// Fin guardado datos
      }
      // Si la empresa no trabaja productos controlados  
      $valores=array
      (
         "form"    => $form,
         "datos"   => $d->getCotizaD(" and a.idDoc =".$id),
         "datTot"  => $d->getCotizaTotal($id),
         'url'     => $this->getRequest()->getBaseUrl(),
         "lin"     => $this->lin,   
         "ttablas"   =>"CODIGO,ARTICULO,OK",
      );                
      $view = new ViewModel($valores);        
      $this->layout('layout/blancoC'); 
      return $view;                
   }    
   // Actualizar email
   public function listactemailAction()
   {
        $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
        $t = new LogFunc($this->dbAdapter);
        $dt = $t->getDatLog();      
        $this->dbAdapter = $this->getServiceLocator()->get($dt['db']);// Conector base de datos cliente        
        $d = new AlbumTable($this->dbAdapter); 

        if($this->getRequest()->isPost()) // Actulizar datos
        {
           $request = $this->getRequest();
           if ($request->isPost()) 
           {
              $data = $this->request->getPost();
              // INICIO DE TRANSACCIONES
              $connection = null;
              try 
              {
                $connection = $this->dbAdapter->getDriver()->getConnection();
                $connection->beginTransaction();                              
                $id = $data->id;
                $d->modGeneral("update c_terceros 
                                set emailFe = '".$data->email."',
                                  actualizaCli = 1
                                where id = ".$data->id);

                $connection->commit();
                $this->flashMessenger()->addMessage('');
              }// Fin try casth   
              catch (\Exception $e) 
              {
                 if ($connection instanceof \Zend\Db\Adapter\Driver\ConnectionInterface) {
                     $connection->rollback();
                       echo $e;
                 } 
              /* Other error handling */
              }// FIN TRANSACCION                                              

           }// Fin guardado datos
        }
      $valores=array
      (
         "id"      =>  $id,        
         'url'     => $this->getRequest()->getBaseUrl(),
         "lin"     => $this->lin         
      );                
      $view = new ViewModel($valores);        
      $this->layout('layout/blancoC'); 
      return $view;                
        
   } // Fin enviar pedido   

   // pedidos saldo negativos
   public function listpsnAction() 
   {
      $id = (int) $this->params()->fromRoute('id', 0);
      $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
      $t = new LogFunc($this->dbAdapter);
      $dt = $t->getDatLog();      
      $this->dbAdapter = $this->getServiceLocator()->get($dt['db']);// Conector base de datos cliente        
      $form = new Formulario("form");
      $d = new AlbumTable($this->dbAdapter); 
      $f = new Funciones($this->dbAdapter);

      $date   = new \DateTime(); 
      $fecSis = $date->format('Y-m-d');        

      $form->get("fechaIni")->setAttribute("value",$fecSis);
      $form->get("fechaFin")->setAttribute("value",$fecSis);

      $arreglo[0] = "TODOS";
      $arreglo[1] = "EN ESPERA";
      $arreglo[2] = "APROBADOS";
      $arreglo[3] = "EN DESPACHO";
      $arreglo[7] = "FACTURADOS";
      $form->get("tipoC")->setValueOptions($arreglo);     

      $arreglo = '';  
      $arreglo[0] = 'Seleccione...';
      $datos = $d->getTerceros(" and a.id!=1 ",' 1 or a.tipo = 3 ');
      foreach ($datos as $dat)
      {
          $idc = $dat['id']; $nom = $dat['nombre'];
          $arreglo[$idc]= $nom;
      } 
      $form->get("idCli")->setValueOptions($arreglo);           

      $valores=array
      (
         "titulo"  => "Detalle del pedido",
         "id"      => $id,        
         "form"    => $form,
         "datos"   => $d->getCotizaD(" and a.idDoc =".$id),
         "datTot"  => $d->getCotizaTotal($id),         
         'url'     => $this->getRequest()->getBaseUrl(),
         "ttablas" => $this->ttab,         
         "lin"     => $this->lin         
      );                
      $view = new ViewModel($valores);        

      return $view;                
   }  // Ventas  
   // Actualizar email
   public function listfleteAction()
   {
        $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
        $t = new LogFunc($this->dbAdapter);
        $dt = $t->getDatLog();      
        $this->dbAdapter = $this->getServiceLocator()->get($dt['db']);// Conector base de datos cliente        
        $d = new AlbumTable($this->dbAdapter); 

        if($this->getRequest()->isPost()) // Actulizar datos
        {
           $request = $this->getRequest();
           if ($request->isPost()) 
           {
              $data = $this->request->getPost();
              // INICIO DE TRANSACCIONES
              $connection = null;
              try 
              {
                $connection = $this->dbAdapter->getDriver()->getConnection();
                $connection->beginTransaction();                              
                $d->modGeneral("update c_cotiza_c
                                   set valorFlete = ".$data->flete."
                                where id = ".$data->id);
                $connection->commit();
                $this->flashMessenger()->addMessage('');
              }// Fin try casth   
              catch (\Exception $e) 
              {
                 if ($connection instanceof \Zend\Db\Adapter\Driver\ConnectionInterface) {
                     $connection->rollback();
                       echo $e;
                 } 
              /* Other error handling */
              }// FIN TRANSACCION                                              

           }// Fin guardado datos
        }
      $valores=array
      (
         "id"      =>  $id,        
         'url'     => $this->getRequest()->getBaseUrl(),
         "lin"     => $this->lin         
      );                
      $view = new ViewModel($valores);        
      $this->layout('layout/blancoC'); 
      return $view;                
        
   } // Fin enviar pedido     
}

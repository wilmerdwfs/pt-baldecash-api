<?php
/**
 * Local Configuration Override
 *
 * This configuration override file is for overriding environment-specific and
 * security-sensitive configuration information. Copy this file without the
 * .dist extension at the end and populate values as needed.
 *
 * @NOTE: This file is ignored from Git by default with the .gitignore included
 * in ZendSkeletonApplication. This is a good practice, as it prevents sensitive
 * credentials from accidentally being committed into version control.
 */
return array(
    'service_manager'=>array(
        'factories'=>array(
            'Zend\Db\Adapter'=>'Zend\Db\Adapter\AdapterServiceFactory',
        ),
    ),
    'db'=>array(
        'username'=>'cythrmr0',
        'password'=>'Tecno2023*',
        'driver'=>'Pdo',
        'dsn'=>'mysql:dbname=cythrmr0_master_fv;host:localhost',
        'driver_options'=>array(
            PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES \'utf8\''
        ),
        'adapters' => array(
           'db1' => array( // Rumatex
              'username'=>'cythrmr0',
              'password'=>'Tecno2023*',
              'driver'=>'Pdo',
              'dsn'=>'mysql:dbname=cythrmr0_rumatex;host:localhost',
              'driver_options'=>array(
            PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES \'utf8\''
           ),
        ),// Fin adapatador Rumatex
           'db2' => array( // Rockdsa
              'username'=>'cythrmr0',
              'password'=>'Tecno2023*',
              'driver'=>'Pdo',
              'dsn'=>'mysql:dbname=cythrmr0_venta;host:localhost',
              'driver_options'=>array(
            PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES \'utf8\''
           ),
        ),// Fin adapatador Rockdsa   
           'db3' => array( // Pruebas 
              'username'=>'cythrmr0',
              'password'=>'Tecno2023*',
              'driver'=>'Pdo',
              'dsn'=>'mysql:dbname=cythrmr0_pruebas_fv;host:localhost',
              'driver_options'=>array(
            PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES \'utf8\''
           ),
        ),// Fin adapatador pruebas            
           'db4' => array( // JN 
              'username'=>'cythrmr0',
              'password'=>'Tecno2023*',
              'driver'=>'Pdo',
              'dsn'=>'mysql:dbname=cythrmr0_distribuciones_jn;host:localhost',
              'driver_options'=>array(
            PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES \'utf8\''
           ),
        ),// Fin adapatador JN                 
           'db5' => array( // alfonsoeme 
              'username'=>'cythrmr0',
              'password'=>'Tecno2023*',
              'driver'=>'Pdo',
              'dsn'=>'mysql:dbname=cythrmr0_alfonsoeme;host:localhost',
              'driver_options'=>array(
            PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES \'utf8\''
           ),
        ),// Fin adapatador alfonsoeme 


        'db7' => array( // cajacopi pruebas
              'username'=>'cythrmr0',
              'password'=>'Tecno2023*',
              'driver'=>'Pdo',
              'dsn'=>'mysql:dbname=cythrmr0_pruebas_fv;host:localhost',
              'driver_options'=>array(
            PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES \'utf8\''
           ),
        ),// Fin adapatador pruebas    
        
        'db8' => array( // cajacopi 
              'username' => 'hrm',
              'password' => 'hrm2015',
              'driver'=>'Pdo',
              'dsn'=>'mysql:dbname=hrm_cajacopi;host:localhost',
              'driver_options'=>array(
            PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES \'utf8\''
           ),
        ),// Fin adapatador pruebas   

        'db9' => array( // jn 2 segunda empresa
              'username'=>'cythrmr0',
              'password'=>'Tecno2023*',
              'driver'=>'Pdo',
              'dsn'=>'mysql:dbname=cythrmr0_distribuciones_jn2;host:localhost',
              'driver_options'=>array(
            PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES \'utf8\''
           ),
        ),// Fin adapatador                   
    ),     
  ),   
);

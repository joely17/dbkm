<?php
/**
 * Dailyscript - Web | App | Media
 *
 *
 *
 * @category
 * @package     Models
 * @subpackage
 * @author      Iván D. Meléndez (ivan.melendez@dailyscript.com.co)
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co) 
 */

class Sucursal extends ActiveRecord {
    
    /**
     * Constante para definir el id de la oficina principal
     */
    const OFICINA_PRINCIPAL = 1;

    /**
     * Método para definir las relaciones y validaciones
     */
    protected function initialize() {
        $this->belongs_to('empresa');
        $this->belongs_to('ciudad');
        $this->has_many('usuario');

        $this->validates_presence_of('sucursal', 'message: Ingresa el nombre de la sucursal');        
        $this->validates_presence_of('direccion', 'message: Ingresa la dirección de la sucursal.');
        $this->validates_presence_of('ciudad_id', 'message: Indica la ciudad de ubicación de la sucursal.');
                
    }  
    
    /**
     * Método para ver la información de una sucursal
     * @param int|string $id
     * @return Sucursal
     */
    public function getInformacionSucursal($id, $isSlug=false) {
        $id = ($isSlug) ? Filter::get($id, 'string') : Filter::get($id, 'numeric');
        $columnas = 'sucursal.*, empresa.nombre, empresa.siglas, empresa.representante_legal, ciudad.ciudad';
        $join = 'INNER JOIN empresa ON empresa.id = sucursal.empresa_id INNER JOIN ciudad ON ciudad.id = sucursal.ciudad_id';
        $condicion = ($isSlug) ? "sucursal.slug = '$id'" : "sucursal.id = '$id'";
        return $this->find_first("columns: $columnas", "join: $join", "conditions: $condicion");
    } 
    
    /**
     * Método que devuelve las sucursales
     * @param string $order
     * @param int $page 
     * @return ActiveRecord
     */
    public function getListadoSucursal($order='order.sucursal.asc', $page='', $empresa=null) {
        $empresa = Filter::get($empresa, 'int');
        
        $columns = 'sucursal.*, empresa.siglas, ciudad.ciudad';
        $join = 'INNER JOIN empresa ON empresa.id = sucursal.empresa_id INNER JOIN ciudad ON ciudad.id = sucursal.ciudad_id';        
        $conditions = (empty($empresa)) ? 'sucursal.id > 0' : "empresa.id = '$empresa'";
        
        $order = $this->get_order($order, 'sucursal', array('sucursal'=>array('ASC'=>'sucursal.sucursal ASC, ciudad.ciudad ASC, empresa.siglas ASC',
                                                                              'DESC'=>'sucursal.sucursal DESC, ciudad.ciudad ASC, empresa.siglas ASC'),
                                                            'ciudad'=>array('ASC'=>'ciudad.ciudad ASC, sucursal.direccion ASC, sucursal.sucursal ASC, empresa.siglas ASC',
                                                                              'DESC'=>'ciudad.ciudad DESC, sucursal.direccion ASC, sucursal.sucursal ASC, empresa.siglas ASC')
                                                            ));                        
        if($page) {                
            return $this->paginated("columns: $columns", "join: $join", "conditions: $conditions", "order: $order", "page: $page");
        } else {
            return $this->find("columns: $columns", "join: $join", "conditions: $conditions", "order: $order", "page: $page");            
        }
    }

}
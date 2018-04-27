<?php
/*
 * This file is part of the UCL-Serco
 *
 * Copyright (C) 2018 Université de Louvain-la-Neuve (UCL-TICE)
 *
 * Written by
 *        Erin Dupuis   (erin.dupuis@uclouvain.be)
 *        Arnaud Willame (arnaud.willame@uclouvain.be)
 *        Domenico Palumbo (dominique.palumbo@uclouvain.be)
 *
 * This software is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 3 of
 * the License, or (at your option) any later version.
 *
 * This software is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software Foundation,
 * Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301  USA
 */

require_once dirname(__FILE__).'/class.DBConnection.php';

/**
 * Collection_Type Class is use to manage Collection types
 *
 * @author Arnaud Willame (arnaud.willame@uclouvain.be)
 */
class Collection_Type
{
    public $id ;
    public $ref ;
    public $name;
    public $description;
    public $idUser;
    private $defaultColl;
    private $collections;
    private $fields;
    
    public function __construct($ref) {
            $db = new DBConnection();  
//            get information about collection type based on ref
            $return = $db->getDbResult('Collection_Type',array('ref'=>$ref));
            
            $this->id =$return[0]['id'];
            $this->ref =$return[0]['ref'];
            $this->name =$return[0]['name'];
            $this->description =$return[0]['description'];
            $this->defaultColl =$return[0]['defaultColl'];
            $this->idUser =$return[0]['xid_user'];
            
            //get collections linked to this collection type
            $this->collections = $db->getDbResult('Collection',array('xid_CollectionType'=>$this->id));            
            //getfields linked to the collection type        
            $this->fields = $db->getDbResult('Fields',array('xid_CollectionType'=>$this->id),"*","priority");   
    }    
    
    function getCollections(){
        return $this->collections;
    }    
        
    function getFields(){
        return $this->fields;
    }
        
    public function getDefaultColl(){
        return $this->defaultColl;
    }  
}
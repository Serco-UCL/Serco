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

require_once dirname(__FILE__).'/class.Collection_Type.php';

/**
 * Collection Class is use to manage Collection
 *
 * @author Arnaud Willame (arnaud.willame@uclouvain.be)
 */
class Collection extends Collection_Type
{
    private $id_coll ;
    private $ref_coll ;
    private $name_coll;
    private $description_coll;
    private $engine_coll;
    private $xid_CollectionType;
    private $last_update;
    private $return;
    
    public function __construct($ref,$coll){
        parent::__construct($ref);  
            $db = new DBConnection();  
            //get collection information based on ref and collection type
            $return = $db->getDbResult('Collection',array('ref'=>$coll));
            $this->return = $return;
            $this->id_coll =$return[0]['id'];
            $this->ref_coll =$return[0]['ref'];
            $this->name_coll =$return[0]['name'];
            $this->description_coll =$return[0]['description'];
            $this->engine_coll =$return[0]['engine'];
            $this->xid_CollectionType =$return[0]['xid_CollectionType'];
            $this->last_update =$return[0]['last_update'];  
    }
    
    function getNameCol(){
        return $this->name_coll;
    }
    
    function getEngineCol(){
        return $this->engine_coll;
    }
}
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

require_once dirname(__FILE__).'/class.Request.php';
require_once dirname(__FILE__).'/class.DBConnection.php';

/**
 * Info Class is used to give information about the request possibles
 *
 * @author Arnaud Willame (arnaud.willame@uclouvain.be)
 */
class Info extends Request{
    private $collectionTypes ;
    
    public function __construct($input){
        parent::__construct($input);
            //begin the counter
            $this->beginTime = microtime(true);      
            $db = new DBConnection();  
//            get collection types
            $this->collectionTypes = $db->getDbResult('Collection_Type');
//            hide fields we dont want to display
            $this->collectionTypes=$this->fieldsToHide($this->collectionTypes,array('id','xid_user'));     
//            get total number of rows in the table
            $this->total = $db->getNbTableRows('Collection_Type');  
//            get all collections and fields based on collection type
            for($i=0;$i<count($this->collectionTypes );$i++){                
                $results['Collection_Type'][$i]=$this->collectionTypes[$i]; 
                $coll_type=new Collection_Type($this->collectionTypes[$i]['ref']);
                $collections =$coll_type->getCollections();
                $collections=$this->fieldsToHide($collections,array('id','xid_CollectionType'));
                
                for($i2=0;$i2<count($collections);$i2++){
                    $results['Collection_Type'][$i]['collections'][$i2]=$collections[$i2];
                }                
                $fields = $coll_type->getFields();
                $fields=$this->fieldsToHide($fields,array('id','xid_CollectionType'));
                
                for($i3=0;$i3<count($fields);$i3++){
                    $results['Collection_Type'][$i]['fields'][$i3]=$fields[$i3];
                }
            }  
            
        $this->docs=$results;
        $this->nbFound=count($this->docs);       
        
        $this->output['responseHeader']['params']=$this->getParams();      
        $this->output['responseHeader']['status']=$this->getStatus();

        $this->output['response']['total']=$this->getTotal();
        $this->output['response']['nbFound']=$this->getNbFound();
        $this->output['response']['docs']=$this->getDocs();

        $this->output['responseHeader']['QTime']= microtime(true) - $this->getBeginTime();        
        $this->output=$this->applyFormat($this->output);

    }
    
    public function getCollectionTypes(){
        return $this->collectionTypes;
    }    
    public function getDocs(){
        return $this->docs;
    }
    public function getTotal(){
        return $this->total;
    }
    public function getNbFound(){
        return $this->nbFound;
    }
    public function getOutput(){
        return $this->output;
    }
    public function getBeginTime(){
        return $this->beginTime;
    }    

}
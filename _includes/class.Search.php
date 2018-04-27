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
 * Search class is used to manage search request
 *
 * @author Arnaud Willame (arnaud.willame@uclouvain.be)
 */
class Search extends Request{    
    public function __construct($input) {       
        parent::__construct($input);
        $this->beginTime = microtime(true);      
        $db = new DBConnection();  
        //Set tableName
        $DBTableName= $this->getCollectionType()."_".$this->getCollection();
        //get number of rows
        $this->total = $db->getNbTableRows($DBTableName);   
        $coll = new Collection_Type($this->getCollectionType());
        $fields = $coll->getFields();
        
        for($i=0;$i<count($fields);$i++){
            $fiedsLabel[$i]=$fields[$i]['libelle'];
            
            if($fields[$i]['searchable']=="1")                
                $queryTable[$fields[$i]['libelle']]=$this->getQuery();
        }              
        $select = implode (", ", $fiedsLabel);
        
        
        //get nb return of request without limit et offset
        $this->totalQueryReturned= $db->getNbreturn($DBTableName,$queryTable);
        //get request response
        $return = $db->getDbResult($DBTableName,$queryTable,$select,$fields[0]['libelle'],$this->getOrder(),$this->getOffset(), $this->getLimit());        
        $return=$this->fieldsToHide($return,array('id'));       
        $this->docs = $return;       
        $this->nbFound=count($this->docs);        
        $this->output['responseHeader']['params']=$this->getParams();      
        $this->output['responseHeader']['status']=$this->getStatus();        
        $this->output['response']['total']=$this->getTotal();
        $this->output['response']['nbFound']=$this->getNbFound();
        $this->output['response']['totalQueryReturned']=$this->getTotalQueryReturned();
        $this->output['response']['offset']=$this->getOffset();
        $this->output['response']['docs']=$this->getDocs();
        $this->output['responseHeader']['QTime']= microtime(true) - $this->getBeginTime();
        $this->output=$this->applyFormat($this->output);
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
    public function getTotalQueryReturned(){
        return $this->totalQueryReturned;
    }
    public function getDoc(){
        return $this->doc;
    }
    public function getQTime(){
        return $this->QTime;
    }
    public function getError(){
        return $this->error;
    }    
    public function getOutput(){
        return $this->output;
    }
    public function getBeginTime(){
        return $this->beginTime;
    }
}
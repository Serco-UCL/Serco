<?php
/*
 * This file is part of the UCL-Serco
 *
 * Copyright (C) 2018 UniversitÃ© de Louvain-la-Neuve (UCL-TICE)
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
    private $elastic;
    private $db;

    public function __construct($input) {
        parent::__construct($input);
        $this->beginTime = microtime(true);
        $db = new DBConnection();
        // $elastic = new Elastic();
        $this->setElastic(new Elastic());

        $coll = new Collection_Type($this->getCollectionType());
        $fields = $coll->getFields();
        $collection=new Collection($this->getCollectionType(),$this->getCollection());
        $engine=$collection->getEngineCol();

        if($engine=='db'){
            //prepare data
            $DBTableName= $this->getCollectionType()."_".$this->getCollection();
            for($i=0;$i<count($fields);$i++){
                $fiedsLabel[$i]=$fields[$i]['libelle'];

                if($fields[$i]['searchable']=="1")
                $queryTable[$fields[$i]['libelle']]=$this->getQuery();
            }
            $select = implode (", ", $fiedsLabel);
            //get number of rows
            $this->total = $db->getNbTableRows($DBTableName);
            //get nb return of request without limit et offset
            $this->totalQueryReturned= $db->getNbreturn($DBTableName,$queryTable);
            //get request response
            $return = $db->getDbResult($DBTableName,$queryTable,$select,$fields[0]['libelle'],$this->getOrder(),$this->getOffset(), $this->getLimit());
        }
        else if($engine=='elastic'){
            $index=strtolower($this->getCollection());
            //Get count of avaiable response in the collection
            $this->total = $this->getElastic()->getTotalAnswer($index)['count'];
            //Parse query to match with conf asked
            $query=$this->getParsedQuery();
            //check if limit is correct and <10000 (elastic limitation)
            $this->setLimit($this->getMaxLimit());
            //get elastic output
            $response=$this->getAnswer($query);
            //Get only the result of search in an array
            $return=$this->getResponseArray($response);
            //Get count of results
            $this->totalQueryReturned= $this->getTotalQueryReturnedFromResponse($response);
        }
        $this->populateResponseForSerco($return);
        $this->output=$this->applyFormat($this->output);
    }

    public function getAnswer($query){
        $index=strtolower($this->getCollection());

        //if integration mode == query
        if($this->getIm() == '' || $this->getIm() == "q" || $this->getIm() == "query"){
            $json= '{"query": {"query_string" : {"fields" : ["description", "code"], "query" : "'.$query.'"}}, "size": '.$this->getLimit().',"from": '.$this->getOffset().' }';
            $url='http://serco.sipr.ucl.ac.be:9200/'.$index.'/_search';
            print_r($json);
        }
        elseif ($this->getIm() == "s" || $this->getIm() == "suggest"){
            $json='{"suggest": { "text" : "'.$this->getQuery().'", "simple_phrase" : { "phrase" : { "field" : "description.trigram","size" : '.$this->getLimit().', "max_errors": 20, "direct_generator" : [ { "field" : "description.trigram",  "suggest_mode" : "always" } ], "highlight": { "pre_tag": "<em>", "post_tag": "</em>" }} }}}';
            $url='http://serco.sipr.ucl.ac.be:9200/'.$index.'/_search';
        }
        elseif( $this->getIm() == "sq" || $this->getIm() == "qs"){
            //query request
            $json= '{"query": {"query_string" : {"fields" : ["description", "code"], "query" : "'.$query.'"}}, "size": '.$this->getLimit().',"from": '.$this->getOffset().' ,';
            //suggest request
            $json.='"suggest": { "text" : "'.$this->getQuery().'", "simple_phrase" : { "phrase" : { "field" : "description.trigram","size" : '.$this->getLimit().', "max_errors": 20, "direct_generator" : [ { "field" : "description.trigram",  "suggest_mode" : "always" } ], "highlight": { "pre_tag": "<em>", "post_tag": "</em>" }} }}}';
            $url='http://serco.sipr.ucl.ac.be:9200/'.$index.'/_search';
        }

        return json_decode( $this->getElastic()->getResultSearch($url,$json),true);
        // $elastic->getResultsuggest
    }

    public function getResponseArray($response){
        //if integration mode == suggest
        if ($this->getIm() == "s" || $this->getIm() == "suggest" || $this->getIm() == "sq" || $this->getIm() == "qs"){
            $result=$response['suggest']['simple_phrase'][0]['options'];
            for($i=0;$i<count($result);$i++){
                $tbreturn['suggest'][$i]=$result[$i]['text'];
            }
        }
        //if integration mode == query
        if($this->getIm() == '' || $this->getIm() == "q" || $this->getIm() == "query" || $this->getIm() == "sq" || $this->getIm() == "qs"){
            $result=$response['hits']['hits'];
            for($i=0;$i<count($result);$i++){
                $tbreturn['query'][$i]=$result[$i]['_source'];
            }
        }
        return $tbreturn;
    }

    public function getTotalQueryReturnedFromResponse($response){
        return $response['hits']['total'];
    }

    public function populateResponseForSerco($return){
        $return = $this->fieldsToHide($return,array('id'));
        //display for bootstrap table
        if($this->getBootstrapTable()=='true'){
            if (strpos($return,'"response":')>0)
                $return = substr($return,strpos($return,'"response":')+11);

            $return = str_replace('"docs":','"rows":',$return);
            if(strpos($return,'"totalQueryReturned":')>0) {
              $return = str_replace('"total":','"allRecords":',$return);
              $return = substr(str_replace('"totalQueryReturned":','"total":',$return),0, -1);
            }
            $this->output['rows']=$return;
            $this->output['allRecords']=$this->getTotal();
            $this->output['total']=$this->getTotalQueryReturned();
            $this->output['offset']=$this->getOffset();
        //complete display
        }else {
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
        }
    }

    public function getMaxLimit(){
        $limit=$this->getLimit();
        if($limit>$this->total)
        $limit=$this->total;

        if($limit>10000)
        $limit=10000;

        return $limit;
    }
    public function getParsedQuery(){
        //search in the collection
        $query=$this->getQuery();
        $keysTable= explode(" ", $query);
        $query="*".$keysTable[0]."*";
        for($i=1;$i<count($keysTable);$i++){
            $query.=" *".$keysTable[$i]."*";
        }
        return $query;
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
    public function getElastic(){
        return $this->elastic;
    }
    public function setElastic($elastic){
        $this->elastic=$elastic;
    }

}

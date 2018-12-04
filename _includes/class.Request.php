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
 * Request class is used to manage request and initialise values
 *
 * @author Arnaud Willame (arnaud.willame@uclouvain.be)
 */
class Request{
    private $params;
    private $collectionType;
    private $collection;
    private $status;
    private $error;
    private $QTime;
    private $total;
    private $totalQueryReturned;
    private $nbFound;
    private $offset;
    private $limit;
    private $query;
    private $im; // interogation mode ( suggest || query || both)
    private $docs;
    private $order;
    private $bootstrapTable;
    private $format;
    private $lang;
    private $output;
    private $beginTime;
    private $debug;

    public function __construct($input) {
        global $config;
        if(isset($input['debug']))
        $debug=new Debug($config['debug'],$input);


        $this->params = $input;
        $this->status = 1;
        //    init params default value
        if(isset($input['related'])){
//            if collection not specified, collection = default collection of the collection type
            $related=explode(":",$input['related']);
            if(count($related)>=2){
                $this->collectionType=$related[0];
                $this->collection=$related[1];
                for($i=2;$i<count($related);$i++){
                    $this->collection.=":".$related[$i];
                }
            }
            elseif(count($related)==1){
                $this->collectionType=$related[0];
                $coll=new Collection_Type($related[0]);
                $this->collection=$coll->getDefaultColl();
            }
            else
                $this->error="Collection type not specified";
        }
        $this->lang = (isset($input['lang']) && ($input['lang'] == 'fr' || $input['lang'] == 'en')) ? $input['lang'] : $config['defautLocale'];
        $this->query= (isset($input['query'])) ? $input['query'] : '';
        $this->offset = intval((isset($input['offset'])) ? $input['offset'] : '0');
        $this->limit = intval((isset($input['limit'])) ? $input['limit'] : '99999999999999999999');
        $this->format = (isset($input['format'])) ? $input['format'] : 'json';
        $this->order = ((isset($input['order']) && ( $input['order']=='ASC' || $input['order']=='DESC' )) ? $input['order'] : 'ASC');
        $this->bootstrapTable = ((isset($input['bootstrapTable']) ) ? 'true' : 'false');
        $this->im = ( ( ( isset($input['im']) ) && $input['im']!="" )  ? $input['im'] : '');

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
    public function getOffset(){
        return $this->offset;
    }
    public function getStatus(){
        return $this->status;
    }
    public function getQTime(){
        return $this->QTime;
    }
    public function getParams(){
        return $this->params;
    }
    public function getCollectionType(){
        return $this->collectionType;
    }
    public function getCollection(){
        return $this->collection;
    }
    public function getError(){
        return $this->error;
    }
    public function getLimit(){
        return $this->limit;
    }
    public function getQuery(){
        return $this->query;
    }
    public function getOrder(){
        return $this->order;
    }

    public function getBootstrapTable(){
        return $this->bootstrapTable;
    }
    public function getFormat(){
        return $this->format;
    }
    public function getLang(){
        return $this->lang;
    }
    public function getOutput(){
        return $this->output;
    }
    public function getBeginTime(){
        return $this->beginTime;
    }
    public function getIm(){
        return $this->im;
    }
    public function setLimit($limit){
        $this->limit=$limit;
    }
    public function getDebug(){
        return $this->debug;
    }
    public function setDebug($debug){
        $this->debug=$debug;
    }


    /**
     * return an array without the specified args
     *
     * @param Array $array array to parse
     * @param String $args value to unset from the array
     *
     * @return Array
     */
    public function fieldsToHide($array, $args){
        foreach ($args as &$value) {
            for($i=0;$i<count($array);$i++){
                if(isset($array[$i][$value])){
                    unset($array[$i][$value]);
                }
            }
        }
        return $array;
    }

    /**
     * return the array in the format asked in the request or JSON if not specified
     *
     * @param Array $output array to format
     *
     * @return Array
     */
    public function applyFormat($output){

        if($this->getFormat() == 'elastic'){
            $output = $this->elastic_encode($output);
        }
        else if($this->getFormat() == 'xml'){
            $output = $this->xml_encode($output);
        }else if($this->getFormat() == 'html'){
            $output = $this->html_encode($output);
        }else
           $output = json_encode($output);
        return $output;
    }

    /**
    * Encode the array in html
    *
    * @param Array $mixed Array to encode
    *
    * @return Array
    */
    public function html_encode($mixed){
        return '<pre>' . var_export($mixed, true) . '</pre>';
    }
    /**
     * Encode the array in xml
     *
     * @param Array $mixed Array to encode
     *
     * @return Array
     */
    public function xml_encode($mixed, $domElement = null, $DOMDocument = null)
    {
        if (is_null($DOMDocument)) {
            $DOMDocument = new \DOMDocument;
            $DOMDocument->formatOutput = true;
            $this->xml_encode($mixed, $DOMDocument, $DOMDocument);
            return $DOMDocument->saveXML();
        } else {
            if (is_object($mixed)) {
                $mixed = get_object_vars($mixed);
            }
            if (is_array($mixed)) {
                foreach ($mixed as $index => $mixedElement) {
                    if (is_int($index)) {
                        if ($index === 0) {
                            $node = $domElement;
                        } else {
                            $node = $DOMDocument->createElement($domElement->tagName);
                            $domElement->parentNode->appendChild($node);
                        }
                    } else {
                        $plural = $DOMDocument->createElement($index);
                        $domElement->appendChild($plural);
                        $node = $plural;
                        // Added filter for properties that end with 's': is_array($mixedElement).
                        // Those are only converted to an array if they contain an array.
                        if (!(rtrim($index, 's') === $index) && is_array($mixedElement)) {
                            $singular = $DOMDocument->createElement(rtrim($index, 's'));
                            $plural->appendChild($singular);
                            $node = $singular;
                        }
                    }
                    $this->xml_encode($mixedElement, $node, $DOMDocument);
                }
            } else {
                $mixed = is_bool($mixed) ? ($mixed ? 'true' : 'false') : $mixed;
                $domElement->appendChild($DOMDocument->createTextNode($mixed));
            }
        }
    }
    public function elastic_encode($output){
        $index=strtolower($output['responseHeader']['params']['related']);
        $index1=explode(':',$index);
        $datas=$output['response']['docs'];
        $output="";
        for($i=0;$i<count($datas);$i++){
            $output.='{"index":{"_index": "'.$index1[0].'_'.$index1[1].'s","_type":"_doc","_id":'.$i.'}}'.PHP_EOL;
            $output.=json_encode($datas[$i]).PHP_EOL;
        }
        return ($output);
    }


}

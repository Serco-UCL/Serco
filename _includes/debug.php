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
 /**
  *
  */
 class Debug
 {
     private $debugOptions;
     private $debugTime;

     function __construct($debug=false,$input)
     {
         global $config;
         if($debug && isset($input['debug']) && $input['debug'])
            $this->setDebugOptions($debug);
     }

     public function getDebugOptions(){
         return $this->debugOptions;
     }
     public function setDebugOptions($debugOptions){
         $this->debugOptions=$debugOptions;
     }

     public function display($type,$text,$param=array()){
         global $config;
         if( $config[$type]){
            if($this->getDebugOptions()){
                echo($text);
                if(!empty($param)){
                    print_r($param);
                }
            }
        }
     }
     public function getDebugTime(){
         return $this->debugTime;
     }
     public function setDebugTime($debugTime){
         $this->debugTime=$debugTime;
     }
 }

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

/**
 * Doc Class is use to display documentation file
 *
 * @author Arnaud Willame (arnaud.willame@uclouvain.be)
 */
class Doc
{
    private $output;
    
    public function __construct($format,$lang) {
        if(file_exists(dirname(__FILE__).'/../_templates/'.$lang.'/man.xhtml')){
            $response=file_get_contents(dirname(__FILE__).'/../_templates/'.$lang.'/man.xhtml');

            if($format == 'xml'){
                $this->output = xml_encode($response);
            }else 
                $this->output = $response;
        }    
    }
    
    public function getOutput(){
        return $this->output;
    }
}
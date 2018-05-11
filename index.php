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


    foreach (glob("./_includes/*.php") as $filename)
    {
        require_once $filename;
    }
    $config = include('./_config/config.php');

    $input = array_merge($_GET, $_POST);
    if(isset($input['lang']) && ($input['lang']=='en' || $input['lang']=='fr' ))
       $locale=$input['lang'];
    else 
        $locale=$config['defautLocale'];
    
    if(!isset($input['action']) || $input['action']=='')
        $input['action']='rest';
    
    switch ($input['action']){
        case "rest" :            
            require_once './pages/'.'rest.php';
            break;
        case "contact" :            
            require_once './pages/'.'contact.php';
            break;
            break;
        case "include" :            
            require_once './pages/'.'include.php';
            break;

    }
        
function get_text($locale,$ref){ 
    if(file_exists('./_templates/'.$locale.'/translation.php')){
        include './_templates/'.$locale.'/translation.php';
        return $string[$ref];  
    }
}
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
 * LOOK man page for information about how to use the rest interface by adding the arg "doc" in youre rest query 
 * 
 * This page return the result of the rest call
 */
$input = array_merge($_GET, $_POST);
$request= new Request($input);
if(isset($input['doc'])){
    $doc = new Doc($request->getFormat(),$request->getLang());
    $response = $doc->getOutput();
}
elseif(isset($input['info'])) { 
    $info= new Info($input);
    $response=$info->getOutput();
} 
else {     // get result asked        
    $search= new Search($input);
    $response=$search->getOutput();
}

echo $response;

    

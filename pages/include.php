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

    //    init params default value    
    if(isset($input['related'])){
        $related=explode(":",$input['related']);
        if(count($related)>=2){
            $CollectionType=$related[0];
            $collection=$related[1];
        }
        elseif(count($related)==1){
            $CollectionType=$related[0];
            $coll=new Collection_Type($related[0]);
            $collection=$coll->getDefaultColl();
        }
        else
            $response['responseHeader']['error']="Collection type not specified";
    }  
    else $error=true;
    
    $lang = (isset($input['lang']) && ($input['lang'] == 'fr' || $input['lang'] == 'en')) ? $input['lang'] : $config['defautLocale'];

    $maxline = intval((isset($input['maxline'])) ? $input['maxline'] : 20);
    $order = ((isset($input['order']) && ( $input['order']=='ASC' || $input['order']=='DESC' )) ? $input['order'] : 'ASC');   

    $response = file_get_contents('https://'.$_SERVER[HTTP_HOST].'/'.$_SERVER['PHP_SELF'].'?related='.$CollectionType.':'.$collection.'&format=json&bootstrapTable&query=',False,$cxContext);

    $coll=new Collection($CollectionType,$collection);
    $fields = $coll->getFields();
    
    $obj=json_decode($response);
    $id= rand(10000,99999);
    $displayline = $maxline;
    $tableHeight  = (95+$displayline*20);
    $myForm = '';
    $myForm.="<html>";
    $myForm.= include('./pages/header.php');   
    $myForm .=  '<div id="body_include" class="">';
    $myForm .=  '<div id="head_include">';
    $myForm .=  '<p class="h3">'.$coll->getNameCol().'</p>';
    $myForm .=  '<div style="background-color:transparent;">';
    $myForm .=  '<form>';
    $myForm .=  '  <div class="input-group" style="min-width:180px;max-width:250px;">';
    $myForm .=  ' <input type="text" class="form-control" style="height:24px;" id="search_data_'.$id.'">';
    $myForm .=  '  <span class="input-group-btn">';
    $myForm .=  '       <button class="btn btn-default btn-xs" type="button" style="background-color:#00214e;color:white;" onclick="search'.$CollectionType.'_'.$id.'();">Rechercher</button>';
    $myForm .=  '  </span>';
    $myForm .=  '</div>';
    $myForm .=  '</form>';
    $myForm .=  '</div>';
    $myForm .=  '</div>';
    $myForm .=  '<div id="part2_include" class="">';
    $myForm .=  '<div class="">';
    $myForm .= display_table($id, $obj, $maxline, $CollectionType, $tableHeight,$collection,$fields);
    $myForm .=  '</div>';
    $myForm .=  '</div>';
    $myForm .=  '<script>';
    $myForm .=  '  $(document).on("keypress", "input", function(e) {';	
    $myForm .=  '    if(e.keyCode == 13 && e.target.type !== "submit") {';	
    $myForm .=  '      var btid = $(this).attr("id") + "_btn";';
    $myForm .=  '      $("#"+btid).click();';	
    $myForm .=  '      e.preventDefault();';	
    $myForm .=  '      return $(e.target).blur().focus();';	
    $myForm .=  '    }';	
    $myForm .=  '  });';	
    $myForm .=  '  $(".no-records-found").hide();';	
    $myForm .=  '</script>';

   echo $myForm;


        
    function display_table($id, $obj, $maxline, $type, $tableHeight,$collection,$fields) {
        $myTableForm = "";
            $myTableForm .= '<div id="div_table_'.$id.'" class="table-responsive" style="">';
            $myTableForm .= '  <table id="icd_table_'.$id.'" class="table table-sm table-striped table-bordered" style="overflow-y: auto;width:100%;" data-toggle="table" data-height="'.$tableHeight.'" data-side-pagination="server" data-pagination="true"   data-pagination-h-align="left" data-page-list="[]" data-row-style="rowStyle" data-page-size='.$maxline.'>';
            $myTableForm .= '    <thead><tr>';
        foreach($obj->rows[0] as $key => $value) {
          if($key != 'id') $myTableForm .= '      <th data-field="'.$key.'">'.ucfirst($key).'</th>';
        }
        $myTableForm .= '    </tr></thead>';
        $myTableForm .= '	';
        $myTableForm .= '  </table>';
        $myTableForm .= '</div>';
        $myTableForm .= '</div>';


        $myTableForm .= '<script>';
        $myTableForm .= '
                            $("#search_data_'.$id.'").keyup(function(e){
                                if(e.keyCode == 13)
                                {
                                    search'.$type.'_'.$id.'();
                                }
                            });';


        $myTableForm .= '  var table_'.$id.' = $("#icd_table_'.$id.'");';
        $myTableForm .= '  table_'.$id.'.bootstrapTable({formatNoMatches: function () {return "Aucun resultats trouve ";},formatShowingRows: function (pageFrom, pageTo, totalRows) {return "showing "+pageFrom+" to "+pageTo+" of "+totalRows+" record";}});';

        $myTableForm .= '  function search'.$type.'_'.$id.'() {';
        $myTableForm .= '    var param = $("#search_data_'.$id.'").val();';
        $myTableForm .= '    myUrl = "../'.$_SERVER['PHP_SELF'].'?related='.$type.':'.$collection.'&format=json&bootstrapTable&query="+param;';
        $myTableForm .= '    table_'.$id.'.bootstrapTable("refresh",{url: myUrl});';
        $myTableForm .= '    table_'.$id.'.bootstrapTable("selectPage", 1);';
        $myTableForm .= '  }';
        $myTableForm .= '  function rowStyle(row, index) {';
        $myTableForm .= '  	return {';
        $myTableForm .= '  		classes: "",';
        $myTableForm .= '  		css: {"font-size": "12px;padding:1px;"}';
        $myTableForm .= '   };';
        $myTableForm .= '  }';	
        $myTableForm .= '</script>';
        $myTableForm .= '</body></html>';
        return $myTableForm;
  } 

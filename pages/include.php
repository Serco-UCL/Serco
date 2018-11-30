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
    
    $error=false;
    if(isset($input['related']) ){
        $related=explode(":",$input['related']);
        $CollectionType=$related[0];
        $col=new Collection_Type($CollectionType);
        if(!isset($col->name))
            $error=true;
    }
    else 
        $error=true;

    //    init params default value    
    if(isset($related) && !$error){
        
        if(count($related)>=2){
            $collection=$related[0];
            $collection=$related[1];    
            for($i=2;$i<count($related);$i++){
                $collection.=":".$related[$i];    
            }
        }
        elseif(count($related)==1){
            $coll=new Collection_Type($CollectionType);
            $collection=$coll->getDefaultColl();
        }
        
        

        $lang = (isset($input['lang']) && ($input['lang'] == 'fr' || $input['lang'] == 'en')) ? $input['lang'] : $config['defautLocale'];

        $maxline = intval((isset($input['maxline'])) ? $input['maxline'] : 20);
        $order = ((isset($input['order']) && ( $input['order']=='ASC' || $input['order']=='DESC' )) ? $input['order'] : 'ASC');   

        $response = file_get_contents('https://'.$_SERVER[HTTP_HOST].'/'.$_SERVER['PHP_SELF'].'?related='.$CollectionType.':'.$collection.'&format=json&bootstrapTable&query=',False,$cxContext);

        $coll=new Collection($CollectionType,$collection);

        $obj=json_decode($response);
        $displayline = $maxline;
        $tableHeight  = (95+$displayline*20);
        $myForm = '';
        $myForm.="<html>";
        $myForm.= include('./pages/header.php');   
        $myForm.= '<style type="text/css">
                    .container{
                        width:90%;
                        margin:5em auto;
                    }
                    .autocomplete-items{
                         height: 0px;
                    }
                    .item{

                        padding: 3px;
                        padding-left: 10px;
                        cursor: pointer;
                        background-color: #fff;
                        border: 1px solid #d4d4d4;
                        max-width: 250px;
                        background-color: white;
                    }

                    .item:hover{
                        background-color: #d4d4d4;
                    }

                    </style>';   
        $myForm .=  '<div id="body_include" class="">';
        $myForm .=  '<div id="head_include">';
        $myForm .=  '<p class="h3">'.$coll->getNameCol().'</p>';
        $myForm .=  '<div style="background-color:transparent;">';
        $myForm .=  '<form>';
        $myForm .=  '  <div class="input-group" style="min-width:180px;max-width:250px;">';
        $myForm .=  ' <input type="text" class="form-control" style="height:24px;" id="search_data" onfocus="setFocus(1)" onblur="setFocus(0)">';
        $myForm .=  '  <span class="input-group-btn">';
        $myForm .=  '       <button class="btn btn-default btn-xs" type="button" style="background-color:#00214e;color:white;" onclick="search'.$CollectionType.'();">Rechercher</button>';
        $myForm .=  '  </span>';
        $myForm .=  '</div>';
        $myForm .=  '<div id="result" class="autocomplete-items" ></div>';
        $myForm .=  '</form>';
        $myForm .=  '</div>';
        $myForm .=  '</div>';
        $myForm .=  '<div id="part2_include" class="">';
        $myForm .=  '<div class="">';
        $myForm .= display_table($obj, $maxline, $CollectionType, $tableHeight,$collection);
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
       ?>
     <script type="text/javascript">
         
         $('body').click(function(){
//            $('#result').hide();
//            alert('hide');
        })

    function replaceString(obj){
        $("#search_data").val(obj.id);
//        getSuggest();
        $('#result').hide();

    };
        
    var focus=0;
    var results='';
    function setFocus(n){
        focus=n;
//        getSuggestelastic();
        if(focus==0)
            $('#result').hide()
    }
    
    function getSuggestelastic(){
//        $('#result').show();
		var search=$('#search_data');
        var text=search.val();     

        //feed for words like suggestion
        var data ='related=<?php echo($CollectionType.":".$collection); ?>&query='+text;
        //feed for autocompletion suggestion
//        var feed='{"suggest": {"my-suggest-1" : { "prefix" : "*'+text+'*", "completion" : { "field" : "description.completion" }}}}';
        


       url="http://serco.sipr.ucl.ac.be/serviceTest/";
       
       //word suggest
//       url="http://serco.sipr.ucl.ac.be:9200/icd10:fulls/_search";
       //auto completion
//        url="http://serco.sipr.ucl.ac.be:9200/icd10s2/_search";
      
       if(text.length > 2){
            $.ajax({
                url:url,
                type:"GET",
                dataType:"json",
                data : data,
                success: function(data){
                    var size=10;
                    if(data['response']['docs']== null){
                        size=0;
                        results+='<div></div>';
                    }
                    else if(data['response']['docs'].length > 0 && text!=""){
                        if(data['response']['docs'].length<10 )
                            size=data['response']['docs'].length;

                        for (i = 0; i < size; i++) { 
                            res=""
                            $.each(data['response']['docs'][i],
                               function(index, value, ){
                                   res+=value+" ";
                                   written=value;
                                   console.log(index);
                                   console.log(value);
                               }
                           );

                            results+='<div id="'+written+'" class="item ui-menu-item" onclick="replaceString(this)">';
                            results+=res;
                            results+='</div>';
                        
                        }
                    }
                    $('#result').html(results);
                    $('#result').show();

                    results='';
                    if (focus==1) setTimeout(function(){getSuggestelastic()}, 1000);
                    
                },
            })
        }
	}
    
    
//	function getSuggest(){
////        $('#result').show();
//		var search=$('#search_data');
//        var text=search.val();     
//
//        //feed for words like suggestion
//        var feed='{"suggest": { "my-suggest-1" : { "text" : "*'+text+'*", "term" : {  "field" : "description", "min_word_length": 1 } },"my-suggest-2" : { "text" : "*'+text+'*", "term" : {  "field" : "code", "min_word_length": 1 } }}}';
//        //feed for autocompletion suggestion
//        var feed='{"suggest": {"my-suggest-1" : { "prefix" : "*'+text+'*", "completion" : { "field" : "description.completion" }}}}';
//        
//
//
//       url="http://serco.sipr.ucl.ac.be:9200/icd10:fulls/_search";
//       
//       //word suggest
////       url="http://serco.sipr.ucl.ac.be:9200/icd10:fulls/_search";
//       //auto completion
//        url="http://serco.sipr.ucl.ac.be:9200/icd10s2/_search";
//      
//       
//        $.ajax({
//            url:url,
//            type:"POST",
////            data:data,
//            contentType:"application/json; charset=utf-8",
//            dataType:"json",
//            data : feed,
//            success: function(data){
//                console.log(data);
//                if (data['suggest']['my-suggest-1'].length >0 && text!=""){
//                    $.each(data['suggest']['my-suggest-1'][0]['options'],
//                        function(index, value){//
//                            results+='<div id="'+value.text+'" class="item ui-menu-item" onclick="replaceString(this)">';
//                            results+=value.text;
//                            results+='</div>';
//                        }
//                    );
//                }
////                multiple 
////                if (data['suggest']['my-suggest-2'].length >0  && text!="" ){
////
////                    $.each(data['suggest']['my-suggest-2'][0]['options'],
////                        function(index, value){
////                            console.log(value.text);
////
////                             results+='<div class="item ui-menu-item"  onclick="replaceString(this)>';
////                            results+=value.text;
////                            results+='</div>';
////                        }
////                    );
////                }
//                $('#result').html(results);
//                $('#result').show();
//
//                results='';
////                $( "#search_data" ).change(function() {
//////                    alert( "Handler for .change() called." );
////                        getSuggest();
////                  });
//                if (focus==1) setTimeout(function(){getSuggest()}, 300);
//            },
//          })
//	}

          
	</script>  

<?php
    }    
    else echo  'Service non ou mal configuré !'.
                '<br><br>Veuillez utiliser le format d\'URL suivant :'.
                '<br>https://'.$_SERVER[HTTP_HOST].'/'.$_SERVER['PHP_SELF'].'?action=include&related=TypeColelction:Collection'.
                '<br><br> Paramètres :'.
                '<br>- related [(TypeColelction obligatoire)] : Type de recherche à effectuer'.
                '<br><br>Exemple d\'utilisation : <a href="https://'.$_SERVER[HTTP_HOST].'/'.$_SERVER['PHP_SELF'].'?action=include&related=ICD10:FULL">https://'.$_SERVER[HTTP_HOST].'/'.$_SERVER['PHP_SELF'].'?action=include&related=ICD10:FULL</a>';


        
    function display_table($obj, $maxline, $type, $tableHeight,$collection) {
        var_dump($collection);
        
        
        
        $myTableForm = "";
            $myTableForm .= '<div id="div_table" class="table-responsive" style="">';
            $myTableForm .= '  <table id="icd_table" class="table table-sm table-striped table-bordered" style="overflow-y: auto;width:100%;" data-toggle="table" data-height="'.$tableHeight.'" data-side-pagination="server" data-pagination="true"   data-pagination-h-align="left" data-page-list="[]" data-row-style="rowStyle" data-page-size='.$maxline.'>';
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
                            $("#search_data").keyup(function(e){
                                getSuggestelastic();
                                if(e.keyCode == 13)
                                {
                                    search'.$type.'();
                                }
                            });';


        $myTableForm .= '  var table = $("#icd_table");';
        $myTableForm .= '  table.bootstrapTable({formatNoMatches: function () {return "Aucun resultats trouve ";},formatShowingRows: function (pageFrom, pageTo, totalRows) {return "showing "+pageFrom+" to "+pageTo+" of "+totalRows+" record";}});';

        $myTableForm .= '  function search'.$type.'() {';
        $myTableForm .= '    var param = $("#search_data").val();';
        $myTableForm .= '    myUrl = "../'.$_SERVER['PHP_SELF'].'?related='.$type.':'.$collection.'&format=json&bootstrapTable&query="+param;';
//        echo '    myUrl = "../'.$_SERVER['PHP_SELF'].'?related='.$type.':'.$collection.'&format=json&bootstrapTable&query="+param;';
        $myTableForm .= '    table.bootstrapTable("refresh",{url: myUrl});';
        $myTableForm .= '    table.bootstrapTable("selectPage", 1);';
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

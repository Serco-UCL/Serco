<?php
  echo" 
    <h3>INPUT </h3>
    <h4>list of usable params:</h4>
  
    <ul>
        <li>related : {collectionType:collection?}</li>
        <li>query : query of the search</li>
        <li>offset : offset where the search begin</li>
        <li>limit : maximum rows number</li>
        <li> order : ASC | DESC</li>
        <li>format : xml | json | html</li> 
 
    </ul>

  <h4>if contain 'info'</h4>
  <ul>
    <li>info : display the list of avaiable SearchType</li>
  </ul>
  
  <h3>OUPUT </h3>
 ";
 
  
   
    echo "<h4>Query output</h4>". displayArray(false);
    echo "<h4> Information output </h4>". displayArray(true);
    
//    echo displayArray(true);
    
    function displayArray($info){
    $response=array();
    $response['responseHeader']['params']=array('param1'=>'value1','param2'=>'value2');      
    $response['responseHeader']['status']="{1 | -1}";
    $response['responseHeader']['error']="String()";
    $response['responseHeader']['QTime']= "float()";
    $response['response']['total']="int()";
    $response['response']['totalQueryReturned']="int()";
    $response['response']['nbFound']="int()";
    $response['response']['offset']="int()";
    if($info)
        $response['response']['docs']=array("Collection_Type"=>array(0=>array('row1'=>'value1','row2'=>'value2','collections'=>array(0=>array('row1'=>'value1','row2'=>'value2')),'fields'=>array(0=>array('row1'=>'value1','row2'=>'value2')))));
    else 
        $response['response']['docs']=array(0=>array('row1'=>'value1','row22'=>'value2'));

    return '<pre>' . var_export($response, true) . '</pre>';

    }
   
?>
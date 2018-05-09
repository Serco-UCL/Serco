<?php
$return='<?xml version="1.0" encoding="utf-8"?>

<!DOCTYPE html
   PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" >

<html xmlns="http://www.w3.org/1999/xhtml">';
//used to get captcha value
session_start(); 
$return.= include('./pages/header.php');    

$obj=get_data('http://serco.sipr.ucl.ac.be'.$_SERVER['PHP_SELF'].'?info');

$return.='       
    <body>  
        <div class="container py-5">
        <p class="h2  text-center">'.get_text($locale,"contactform").'</p>';

if (isset($input['requestType']) && isset($input['request']) && isset($input['email']) && isset($input['name']) && isset($input['captcha']) && $input['captcha']==$_SESSION['captcha'] ){   
    switch ($input['requestType']) {
    case 0:
        $subject=get_text($locale,"simplecontact");
        $message=get_text($locale,"simplecontact")." : \n\r";
        break;
    case 1:
        $subject=get_text($locale,"asknewcoll");
        $message=get_text($locale,"asknewcoll")." : \n\r  ".get_text($locale,"label")." : ".$input['colLib']." \n\r ".get_text($locale,"description")." : ".$input['colDesc']."\n\r ";
        break;
    case 2:
        $subject=get_text($locale,"askfollowcoll");
        $message=get_text($locale,"askfollowcoll")." :  ".json_encode($input['collection'])." \n\r";
        break;
    case 3:
        $subject=get_text($locale,"warnerrorcoll");
        $message=get_text($locale,"warnerrorcoll")." : ".json_encode($input['collection'])." \n\r"; 
        break;
    case 4:
        $subject=get_text($locale,"askdelinfopers");
        $message=get_text($locale,"askdelinfopers")." \n\r";      
        break;    
    }
   
    $message.=get_text($locale,"askingmess").$input['name']." ( ".$input['email']." ) : \n\r".$input['request'];
    $to = $config['MailContact'];
    //Send mail
    mail($to, $subject, $message);   
    //add message in database
    $db = new DBConnection();  
    $status=$db->setMessage($input["name"],$input["email"],$to,$subject,$message);  
         
    if($status){
    $return .= '  <div class="alert alert-success">
                     <strong>'.get_text($locale,"success").'</strong> '.get_text($locale,"successsend").'
                </div>';
    }
    else {
    $return .= '<div class="alert alert-danger">
                    <strong>'.get_text($locale,"error").'</strong> '.get_text($locale,"erroccured").'
                </div>';
    }
}

else{
    //Alert message if error
    if(isset($input['requestType']) || isset($input['request']) || isset($input['email']) || isset($input['name']) || isset($input['captcha'])){
        $return .= '<div class="alert alert-danger">
                <strong>'.get_text($locale,"error").'</strong> '.get_text($locale,"erroccured").'
            </div>';
    }
    $requestType=(isset($input['requestType'])) ? $input['requestType']:'1';
    $request=(isset($input['request'])) ? $input['request']:'';
    $email=(isset($input['email'])) ? $input['email']:'';
    $name=(isset($input['name'])) ? $input['name']:'';
    $colLib=(isset($input['colLib'])) ? $input['colLib']:'';
    $colDesc=(isset($input['colDesc'])) ? $input['colDesc']:'';
    $collections=(isset($input['collection'])) ? $input['collection']:array();

    $obj2=json_decode($obj,true);
    $response=$obj2['response']['docs']['Collection_Type'];
    for($i=0;$i<count($response);$i++){
        $collectionTypes[$i]['name']=$response[$i]['name'];
        for($i2=0;$i2<count($response[$i]['collections']);$i2++){
            $collectionTypes[$i]['collections'][$i2]=$response[$i]['collections'][$i2]['name'];
        }
    }
    $return.='
        <form  method="post">
            <div class="well">
              <div class="form-group">
                <label for="name">'.get_text($locale,"completename").'</label>
                <input type="text" class="form-control" id="name" name="name" value="'.$name.'"  placeholder="'.get_text($locale,'entercompletename').'" required>
              </div>
              <div class="form-group">
                <label for="email">'.get_text($locale,"email").'</label>
                <input required type="email" class="form-control" id="email" name="email" value="'.$email.'"   aria-describedby="emailHelp" placeholder="'.get_text($locale,"enteremail").'">
                <!-- <small id="emailHelp" class="form-text text-muted">We\'ll never share your email with anyone else.</small>-->
              </div>
              <small id="emailHelp" class="form-text text-muted">'.get_text($locale,"privateDataUse").'</small>
            </div>
            
            <div class="well">           
                <div id="msgType" class="form-group">
                  <label for="Select1">'.get_text($locale,"contacttype").'</label>
                  <select name="requestType" class="form-control" value="'.$requestType.'"   id="Select1">                      
                    <option id="sel1" value="1" >'.get_text($locale,"asknewcoll").'</option>
                    <option id="sel2" value="2" >'.get_text($locale,"askfollowcoll").'</option>
                    <option id="sel3" value="3" >'.get_text($locale,"warnerrorcoll").'</option>
                    <option id="sel4" value="4" >'.get_text($locale,"askdelinfopers").'</option>
                    <option id="sel0" value="0" >'.get_text($locale,"simplecontact").'</option>
                  </select>
                </div>
                
                <div id="collInfos" >
                    <div class="form-group">
                        <label for="colLib">'.get_text($locale,"colLib").'</label>
                        <input type="text" class="form-control" id="colLib" name="colLib" value="'.$colLib.'"  placeholder="'.get_text($locale,'entercolLib').'" required>
                    </div>
                    <div class="form-group">
                        <label for="colDesc">'.get_text($locale,"colDesc").'</label>
                        <input type="text" class="form-control" id="colDesc" name="colDesc" value="'.$colDesc.'"  placeholder="'.get_text($locale,'entercolDesc').'" required>
                    </div>
                </div>


                <div id="linkedCol" class="form-group"  style="display: none">
                  <label for="Select2">'.get_text($locale,"linkedcoll").'</label>
                  <select multiple="multiple" name="collection[]" class="form-control" value="'.$collection.'"  id="Select2">
                   ';
                    for($i=0;$i<count($response);$i++){
                        $return.='<option id="ref_'.$response[$i]["ref"].'" class="optionGroup" value="'.$response[$i]['ref'].'" >'.$response[$i]['name'].'</option>';
                        for($i2=0;$i2<count($response[$i]['collections']);$i2++){
                             $return.='<option id="ref_'.$response[$i]['ref'].'_'.$response[$i]['collections'][$i2]['ref'].'" class="optionChild" value="'.$response[$i]['ref'].'_'.$response[$i]['collections'][$i2]['ref'].'">'.$response[$i]['collections'][$i2]['name'].'</option>';
                        }
                        $return.='</optgroup>';
                    }
                    $return.='</select>
                </div>
                <div id="msg" class="form-group">
                  <label for="request">'.get_text($locale,"message").'</label>
                  <textarea class="form-control" name ="request" id="request" rows="3" required>'.$request.'</textarea>
                </div>
            </div>';
                    
            $return.='
            <div class="well">
                <div class="form-group">
                    <label for="name">'.get_text($locale,"captcha").'</label>
                    <p><img id="imgCaptcha" src="./pages/captcha.php">
                    <button type="button" id="regen" class="btn btn-info">'.get_text($locale,"reloadCaptcha").'</button> </p>
                    <input type="text" class="form-control" id="captcha" name="captcha" required>
                  </div>
            </div>

            <input type="hidden" name="action" value="contact">
            <div class="form-group">
                <button type="submit" class="btn btn-primary">'.get_text($locale,"submit").'</button>
            </div>
        </form>
    ';
    $return.="<script>
        $('#linkedCol').hide();
        $(document).ready( function() { 
//            put the value selected if exist
            $('#sel'+ $requestType +'').attr('selected', 'selected');";    
            for($i=0;$i<count($collections);$i++){
                 $return.=" $('#ref_'+'".$collections[$i]."').attr('selected', 'selected');";
            };        
            $return.="
//            put the values selected if exists
            if($('#Select1').val() != '1' && $('#Select1').val() != '4' && $('#Select1').val() != '0'){ 
                $('#linkedCol').show();
            }

            //Hide/show collections info
            $('#Select1').change(function(){
               if($(this).val() == '1'){ 
                   $('#collInfos').show();
               }
               else {
                $('#collInfos').hide();
               }
            });


            //Hide/show collections
            $('#Select1').change(function(){
               if($(this).val() == '1' || $('#Select1').val() == '4' || $('#Select1').val() == '0'){ 
                   $('#linkedCol').hide();
               }
               else {
                $('#linkedCol').show();
               }
            });
        });
        
        $('#regen').click(function(event){
            $('#imgCaptcha').attr('src', $('#imgCaptcha').attr('src')+'#');
          });

//        onclick(regen)
//            imgCaptcha.reload
    </script>";
}
$return.="</div></body></html>";
echo $return;
            
function get_data($url) {
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}



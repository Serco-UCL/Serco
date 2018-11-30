<?php // use Elasticsearch\ClientBuilder;
//require_once dirname(__FILE__).'/elasticsearch/vendor/autoload.php';
//    
//    $hosts = [
//        'http://serco.sipr.ucl.ac.be:9200'
//    ];
//        $client = ClientBuilder::create()->build();
//        $client = ClientBuilder::create()
//                    ->setHosts($hosts)
//                    ->build();

    
//$params = [
//    'index' => 'icd10s',
//    'type' => 'icd10',
//    'id' => '1',
//    'body' => ['code' n=> 'A001']
//];
//
//$response = $client->index($params);
//print_r($response);
//    

//$params2 = [
//    'index' => 'icd10:revisites',
//    'type' => 'icd10:revisite',
//    'body' => [
//        'query' => [
//            'match' => [
//                'code' => 'A020'
//            ]
//        ]
//    ]
//];
//$response2 = $client->index($params2);
//print_r($response2);

        
        
//$url="serco.sipr.ucl.ac.be:9200/icd10:revisites/_search?q=*a*";        
//                
//$ch = curl_init();
//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//curl_setopt($ch, CURLOPT_URL,$url);
//$result=curl_exec($ch);
//curl_close($ch);
//
//// Will dump a beauty json :3
//$response=json_decode($result, true);
//$result=$response;
////$result=$response['hits']['hits'][0]['_source'];
////$result=$response['took']['hits'];
//
//
//var_dump(($result));
//echo($result);
    //get all records
//   $url="serco.sipr.ucl.ac.be:9200/icd10:revisites/_search";        
////   search
//   $url="serco.sipr.ucl.ac.be:9200/icd10:revisites/_search?q=*:*a*&size=1000&from=0";        
//
//    $ch = curl_init();
//    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//    curl_setopt($ch, CURLOPT_URL,$url);
//    $result=curl_exec($ch);
//    curl_close($ch);
//
//    // Will dump a beauty json :3
//    $response=json_decode($result, true);
////    $result=$response;
////    $result=$response['hits']['hits'][0]['_source'];
//    $result=$response['hits']['hits'];
//    //$result=$response['took']['hits'];
//    for($i=0;$i<count($result);$i++){
//        $tbreturn[$i]=$result[$i]['_source'];
//    }
    
//    var_dump($response);
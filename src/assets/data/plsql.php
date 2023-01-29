<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Authorization');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS');
header('Content-type: application/json');
require_once('class.oci.php');

$headers=getallheaders();

$data = file_get_contents("php://input");
$data = json_decode($data, TRUE);

if (isset($data['sql'])) {
   if ($data['sql']!="") {
      
   }
}

$X=new OracleDB();
$X->startParams();
$X->addParams(":name","BOX");
$X->addParams(":tname",'',"OUT",180,SQLT_CHR);

$params=array();
$p=array();
$p['name']="name";
$p['val']="BOX";
$p['inout']="IN";
$p['len']=-1;
array_push($params,$p);

//$a=$X->sql("SELECT TNAME FROM TAB WHERE TNAME LIKE '%'||:name||'%'",$params);
$a=$X->sql("SELECT TNAME INTO :tname FROM TAB WHERE TNAME LIKE '%'||:name||'%' AND ROWNUM = 1");
//$a=$X->sql("SELECT TNAME FROM TAB INTO :tname WHERE TNAME LIKE '%'||:name||'%'");
 	
//$X->start_params();
//$X->add_params("filter","FPS");
//$a=$X->get_ref_cursor('TEST_REF');

$o=json_encode($a, JSON_HEX_TAG |
        JSON_HEX_APOS |
        JSON_HEX_QUOT |
        JSON_HEX_AMP |
        JSON_UNESCAPED_UNICODE);
echo $o;
?>

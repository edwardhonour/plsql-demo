<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Authorization');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS');
header('Content-type: application/json');
require_once('class.OracleDB.php');
$uid=$_COOKIE['uid'];

$X=new OracleDB();

function getEmployeeData($empno) {

    $sql="SELECT ENAME, DEPTNO FROM EMP WHERE EMPNO = " . $empno . ";
    $d=$this->X->sql($sql);
    if (sizeof($d)>0) {
        return $d[0];
    } else {
        $f=array();
        $f['ENAME']="Employee Not Found";
        $f['DEPTNO']="";
        return $f;
    }
}

$data = file_get_contents("php://input");
$data = json_decode($data, TRUE);
$output=array();
if (!isset($data['q'])) $data['q']="user";
$aa=explode("/",$data['q']);
if (isset($aa[1])) {
     $data['q']=$aa[1];
     if (isset($aa[2])) {
         $data['id']=$aa[2];
         }
     if (isset($aa[3])) {
         $data['id2']=$aa[3];
         }
         if (isset($aa[4])) {
         $data['id3']=$aa[4];
         }
}
$output=array();

   switch ($data['q']) {
        case 'get-criteria-category':
             $output=$A->getCriteriaCategory($data);
             break;
        case 'get-filter-option':
             $output=$A->getFilterOptions($data);
             break;
        case 'do-count':
             $output=$A->doCount($data);
             break;
        case 'select-criteria-option':
             $output=$A->selectCriteriaOption($data);
             break;
        case 'select-filter-option':
             $output=$A->selectCriteriaFilter($data);
             break;
        case 'select-criteria-column':
             $output=$A->selectCriteriaColumn($data);
             break;
        case 'delete-criteria-option':
             $output=$A->deleteCriteriaOption($data);
             break;
        case 'delete-criteria-filter':
             $output=$A->deleteCriteriaFilter($data);
             break;
        case 'delete-criteria-column':
             $output=$A->deleteCriteriaColumn($data);
             break;
        case 'count-results':
             $output=$A->countResults($data);
             break;
        case 'start-new-report':
             $output=$A->startNewReport($data);
             break;
        case 'filter':
             $output=$A->getFilterPage($data);
             break;
        case 'columns':
             $output=$A->getColumnPage($data);
             break;
        case 'report':
             $output=$A->doReport($data);
             break;
        case 'perform-criteria-search':
             $output=$A->criteriaSearch($data);
             break;
        case 'post-edit-da-option':
             $output=$A->postEditDAOption($data);
             break;
        case 'options':
             $output=$A->getOptionsList($data);
             break;
        default:
            $output=$A->getHomePage($data);
            break;
        }

$o=json_encode($output, JSON_HEX_TAG |
        JSON_HEX_APOS |
        JSON_HEX_QUOT |
        JSON_HEX_AMP |
        JSON_UNESCAPED_UNICODE);

echo $o;
?>

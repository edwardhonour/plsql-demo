<?php

//---------------------------------------------------------------------
// Main API Router for this angular directory.
// Author: Â Edward Honour
// Date: 07/18/2021
//---------------------------------------------------------------------

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Authorization');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS');
header('Content-type: application/json');

require_once('class.OracleDB.php');

$uid=$_COOKIE['uid'];

$X=new OracleDB();
$db=$X->connectACN();

// Require and initialize the class libraries necessary for this module. Code
// specific for your application goes in here.

//=======================================================================================
// APPLICATION SPECIFIC CODE BELOW - CONNECT STRING CODE ABOVE
//=======================================================================================

class FORMS {

    public $X;
    public $json;
    public $arr;
    function __construct() {
         $this->X=new OracleDB();
    }
    function getUser($data) {
            if (!isset($data['uid'])) $data['uid']="55009";
            if ($data['uid']=="") $data['uid']="55009";
            $sql="select * from FPS_USER WHERE USER_ID = " . $data['uid'];
            $user=$this->X->sql($sql);
            $u=array();
            if (sizeof($user)==0) {
                    $u['forced_off']=1;
            } else {
                    $u=$user[0];
                    $u['forced_off']=0;
            }
            return $u;
    }

    function isJSON($string){
       return is_string($string) && is_array(json_decode($string, true)) ? true : false;
    }

    function makeResponse($data) {
        if (!isset($data['formData'])) $data['formData']=array();
        if (!isset($data['displayData'])) $data['displayData']=array();
        if (!isset($data['exceptions'])) $data['exceptions']=array();
        
        if (!isset($data['exceptions']['lastError'])) $data['exceptions']['lastError']=array();
        if (!isset($data['exceptions']['lastError']['code'])) $data['exceptions']['lastError']['code']=0;
        if (!isset($data['exceptions']['lastError']['message'])) $data['exceptions']['lastError']['message']="";
        if (!isset($data['exceptions']['lastDebug'])) $data['exceptions']['lastDebug']=array();
        if (!isset($data['exceptions']['lastDebug']['sql'])) $data['exceptions']['lastDebug']['sql']="";
        if (!isset($data['exceptions']['lastDebug']['binds'])) $data['exceptions']['lastDebug']['binds']=array();
        if (!isset($data['exceptions']['NO_DATA_FOUND'])) $data['exceptions']['NO_DATA_FOUND']=false;
        if (!isset($data['exceptions']['TOO_MANY_ROWS'])) $data['exceptions']['TOO_MANY_ROWS']=false;
        if (!isset($data['exceptions']['VALUE_ERROR'])) $data['exceptions']['VALUE_ERROR']=false;
        if (!isset($data['exceptions']['ZERO_DIVIDE'])) $data['exceptions']['ZERO_DIVIDE']=false;

        if (!isset($data['results'])) $data['results']=array();
        if (!isset($data['parameters'])) $data['parameters']=array();
    }

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

    function deleteCriteriaColumn($data) {
          $sql="delete from RWH_USER_COLUMN_SELECTION WHERE ID = " . $data['data']['ID'];
          $this->X->execute($sql);
         //-- REorder...
         $sql="select * from RWH_USER_COLUMN_SELECTION WHERE RNUM = " . $rnum . " ORDER BY COLUMN_ORDER";
         $templates=$this->X->sql($sql);
         $i=0;
         foreach($templates as $t) {
             $i++;
             $sql="UPDATE RWH_USER_COLUMN_SELECTION SET OPTION_ORDER = " . $i . " WHERE ID = " . $t['ID'];
             $this->X->execute($sql);
         }
         $output=$this->getHomePage($data);
         return $output;
    }
/* */
    function deleteCriteriaOption($data) {
         $data['id']=$data['data']['WEIGHT_ID'];
         $rnum=$data['data']['RNUM'];
         $sql="delete from RWH_USER_OPTION_SELECTION WHERE ID = " . $data['data']['ID'];
         $this->X->execute($sql);
         //-- REorder...
         $sql="select * from RWH_USER_OPTION_SELECTION WHERE RNUM = " . $rnum . " AND OPTION_TYPE = 'OPTION' ORDER BY OPTION_ORDER";
         $templates=$this->X->sql($sql);
         $i=0;
         foreach($templates as $t) {
             $i++;
             $sql="UPDATE RWH_USER_OPTION_SELECTION SET OPTION_ORDER = " . $i . " WHERE ID = " . $t['ID'];
             $this->X->execute($sql);
         }
         $output=$this->getHomePage($data);
         return $output;
    }

   function deleteCriteriaFilter($data) {
         $data['id']=$data['data']['WEIGHT_ID'];
         $rnum=$data['data']['RNUM'];
         $sql="delete from RWH_USER_OPTION_SELECTION WHERE ID = " . $data['data']['ID'];
         $this->X->execute($sql);
         //-- REorder...
         $sql="select * from RWH_USER_OPTION_SELECTION WHERE RNUM = " . $rnum . " AND OPTION_TYPE <> 'OPTION' ORDER BY OPTION_ORDER";
         $templates=$this->X->sql($sql);
         $i=0;
         foreach($templates as $t) {
             $i++;
             $sql="UPDATE RWH_USER_OPTION_SELECTION SET OPTION_ORDER = " . $i . " WHERE ID = " . $t['ID'];
             $this->X->execute($sql);
         }
         $output=$this->getHomePage($data);
         return $output;
    }

    function getCriteriaCategory($data) {
         $data['id']=$data['data']['CAT_ID'];
         $output=$this->getHomePage($data);
         return $output;
    }

    function getUserSelections($data) {

            $rnum=$data['rnum'];

            $sql="select * from RWH_USER_OPTION_SELECTION WHERE RNUM = " . $rnum . " AND OPTION_TYPE = 'OPTION' ORDER BY OPTION_ORDER";
            $list=$this->X->sql($sql);
            return $list;

    }
/* */
    function getFilterSelections($data) {

            $rnum=$data['rnum'];
            $sql="select * from RWH_USER_OPTION_SELECTION WHERE RNUM = " . $rnum . " AND OPTION_TYPE <> 'OPTION' ORDER BY OPTION_ORDER";
            $templates=$this->X->sql($sql);
            return $templates;

    }

    function getColumnSelections($data) {

            $rnum=$data['rnum'];
            $sql="select * from RWH_USER_COLUMN_SELECTION WHERE RNUM = " . $rnum . " ORDER BY COLUMN_ORDER";
            $templates=$this->X->sql($sql);
            return $templates;

    }

    function getCriteriaCategories($data) {
            $sql="select CAT_ID, LONG_NAME, TITLE FROM RWH_CRITERIA_CATEGORIES WHERE SCORING_TYPE = 'PMI' ORDER BY CAT_ID ";
            $templates=$this->X->sql($sql);
            return $templates;
    }

    function getFilterCategories($data) {
            $sql="select CAT_ID, LONG_NAME, TITLE FROM RWH_CRITERIA_CATEGORIES WHERE SCORING_TYPE = 'FLT' ORDER BY CAT_ID ";
            $templates=$this->X->sql($sql);
            return $templates;
    }

    function getCategoryOptions($id) {
        $sql="SELECT * FROM RWH_DA_OPTIONS WHERE WEIGHT_ID = '" . $id. "' AND OPTION_TYPE = 'OPTION' ORDER BY OPTION_ORDER";
        $options=$this->X->sql($sql);
        return $options;
    }

    function getFilterOptions($data) {
        $output=$this->getHomePage($data);
        $sql="SELECT * FROM RWH_DA_OPTIONS WHERE OPTION_TYPE = '" . $data['data']['WEIGHT_ID'] . "' ORDER BY OPTION_ORDER";
        $options=$this->X->sql($sql);
        $output['options']=$options;
        return $output;
    }

    function getSectionTitle($id) {

            $sql="SELECT * FROM RWH_CRITERIA_CATEGORIES WHERE CAT_ID = '" . $id . "' AND SCORING_TYPE = 'PMI'";
            $t=$this->X->sql($sql);
            if (sizeof($t)>0) {
                    $out=$t[0]['LONG_NAME'];
            } else {
                    $out="Please Select a Category";
            }
            return $out;

    }

    function getFilterTitle($id) {
            $sql="SELECT * FROM RWH_CRITERIA_CATEGORIES WHERE CAT_ID = '" . $id . "' AND SCORING_TYPE = 'FLT'";
            $t=$this->X->sql($sql);
            if (sizeof($t)>0) {
                    $out=$t[0]['LONG_NAME'];
            } else {
                    $out="Please Select a Category";
            }
            return $out;
    }


function makeOptionData($id,$rnum,$username) {
          $optionData=array();
          $optionData['WEIGHT_ID']=$id;
          $optionData['CAT_ID']=$id;
          $optionData['RNUM']=$rnum;
          $optionData['USERNAME']=$username;
          $optionData['NOT_FLAG']="";
          $optionData['OPTIONID']="";
          $optionData['OPTION_VALUE']="";
          $optionData['OPTION_TYPE']="";
          $optionData['OPTION_SOURCE']="";
          $optionData['OPTION_EXCLUDE']="";
          return $optionData;
}

function makeFilterData($id,$rnum) {
          $filterData=array();
          $filterData['WEIGHT_ID']=$id;
          $filterData['ID']="";
          $filterData['RNUM']=$rnum;
          $filterData['OPTION_ORDER']="";
          $filterData['OPTION_TYPE']="";
          $filterData['OPTION_DESC']="";
          $filterData['OPTION_EXCLUDE']="";
          $filterData['OPTION_ID']="";
          $filterData['CUSTOM1']="";
          $filterData['CUSTOM2']="";
          return $filterData;
}

function makeColumnData($id,$rnum) { 
          $columnData=array();
          $columnData['RNUM']=$rnum;
          $columnData['COLUMN_DSC']="";
          $columnData['COLUMN_ID']="";
          $columnData['COLUMN_ORDER']="";
          $columnData['SCORING_TYPE']="";
          return $columnData;
}

function makeSearchData($id,$rnum) {
          $searchData=array();
          $searchData['SEARCH']="";
          return $searchData;
}

function makeFormData($id,$rnum) {
          $formData=array();
          $formData['ID']="";
          $formData['CAT_ID']="";
          return $formData;
}
/* */
    function getHomePage($data) {

          $output=array();
          $user=$this->getUser($data);
          if (isset($data['id'])) { $id=$data['id']; } else { $id=0; }
          if (isset($data['rnum'])) $rnum=$data['rnum']; else $rnum='';
         
          if ($rnum=='') {
             $rnum=$this->getRNum();
             $data['rnum']=$rnum;
          }

          if ($user['forced_off']==1) {
             $output=array();
             $output['user']=$user;
             return $output;
          } else {

                $output=array();
                $output['user']=$user;

    
            if ($id!=""&&$id!=0) {
                $output['section_title']=$this->getSectionTitle($id);
                $options=$this->getCategoryOptions($id);
            } else {
                $output['section_title']="Please Select a Category";
                $options=array();
            }

            $output['rnum']=$rnum;
            $output['options']=$options;

            $output['categories']=$this->getCriteriaCategories($data);
            $output['option_selection']=$this->getUserSelections($data);
            $output['filter_selection']=$this->getFilterSelections($data);
            $output['column_selection']=$this->getColumnSelections($data);

            }


          $output['searchData']=$this->makeSearchData($id,$rnum);
          $output['optionData']=$this->makeOptionData($id,$rnum,$output['user']['USER_NAME']);
          $output['filterData']=$this->makeFilterData($id,$rnum);
          $output['columnData']=$this->makeColumnData($id,$rnum);
          $output['formData']=$this->makeFormData($id,$rnum);
          $output['postForm']=$this->makeFormData($id,$rnum);
          return $output;
    }

/* */
    function getFilterPage($data) {

          $output=array();
          $user=$this->getUser($data);
          if (isset($data['id'])) { $id=$data['id']; } else { $id=0; }
          if (isset($data['rnum'])) { $rnum=$data['rnum']; } else { $rnum=''; }
          if ($rnum=='') {
             $rnum=$this->getRNum();
             $data['rnum']=$rnum;
          }

          if ($user['forced_off']==1) {
             $output=array();
             $output['user']=$user;
             return $output;
          } else {

            $output=array();
            $output['user']=$user;

            if ($id!=""&&$id!=0) {
                $output['section_title']=$this->getFilterTitle($id);
                $options=$this->getFilterOptions($id);
            } else {
                $output['section_title']="Please Select a Category";
                $options=array();
            }

            $output['rnum']=$rnum;
            $output['options']=$options;

            $output['categories']=$this->getFilterCategories($data);
            $output['option_selection']=$this->getUserSelections($data);
            $output['filter_selection']=$this->getFilterSelections($data);
            $output['column_selection']=$this->getColumnSelections($data);

            }

          $output['optionData']=$this->makeOptionData($id,$rnum,$output['user']['USER_NAME']);
          $output['filterData']=$this->makeFilterData($id,$rnum);
          $output['columnData']=$this->makeColumnData($id,$rnum);
	  $output['formData']=$this->makeFormData($id,$rnum);
          $output['postForm']=$this->makeFormData($id,$rnum);
          return $output;
    }

    function getColumnTitle($id) {

    }

    function getColumnOptions($id) {

    }

    function getColumnCategories($id) {

    }

function makeFrom($data) {

    $rnum=$data['rnum'];

    $sql=" FROM ";
    $sql.=" RWH_DIM_FACILITY F WHERE F.ACTIVE_FLAG = 'Y' AND F.FPS_RESPONSIBLE = 'Y' ";
    $sql.=" AND BUILDING_NBR NOT IN (SELECT BUILDING_NBR FROM FPS_EXCLUDED_BUILDINGS) ";

    $s="SELECT OPTION_ID FROM RWH_USER_OPTION_SELECTION WHERE RNUM = ". $rnum . " AND OPTION_SOURCE = 'SETS' ";
    $s.=" ORDER BY OPTION_ORDER ";
    $t=$this->X->sql($s);
    if (sizeof($t)>0) {
        $a="";
        $c=0;
        $oper="";
        foreach($t as $u) {
        if ($u['OPTION_ID']=='1'||$u['OPTION_ID']=='3'||$u['OPTION_ID']=='4'||$u['OPTION_ID']=='5') {
           if ($u['OPTION_ID']=='1') $oper.=" ( ";       
           if ($u['OPTION_ID']=='2') $oper.=" ) "; 
           if ($u['OPTION_ID']=='3') $oper.=" INTERSECT ";
           if ($u['OPTION_ID']=='4') $oper.=" UNION ";
           if ($u['OPTION_ID']=='5') $oper.=" MINUS ";//
           $a.= $oper;
       } else {
           $a.= $oper . " (SELECT FACILITY_ID FROM RWH_FACILITY_OPTIONS WHERE OPTION_ID = " . $u['OPTION_ID'] . ")";
           $oper=" INTERSECT ";
       }
       $c++;
       }
       $sql .= " AND F.FACILITY_ID IN (" . $a . ")";
    }

    return $sql;

}

function doCount($data) {

    $rnum=$data['rnum'];

    $sql="SELECT to_char(count(DISTINCT F.BUILDING_NBR),'99,999') as C ";
    $sql.=$this->makeFrom($data);

    $ora=new OracleDB();
    $db=$ora->connectACN();
    $data = $db->query($sql);
    $result = $data->fetchAll(PDO::FETCH_ASSOC);
    if ($result) {
             $C=$result[0]['C'];
             $C=str_replace(" ","",$C);
             $C=$C . " matches";
          } else{
             $C="Expression Error";
    }
    $output=array();
    $output['count']=$C;
    return $output;
}

function doReport($data) {


    $rnum=$data['rnum'];

    $output=$this->getHomePage($data);
    $sql="SELECT FACILITY_ID, BUILDING_NBR, FACILITY_NAME, REGION_ID, DISTRICT_ID, CITY_NAME, STATE_ABBR, ";
    $sql.=" DECODE(OWNERSHIP_ID,200004,'Owned',200029,'Leased',300029,'N','') AS OWNERSHIP_ID, FSL "; 
    $sql.=$this->makeFrom($data);

    $h=$this->X->sql($sql);
    $output['list']=$h;
    return $output;

}

  function getColumnPage($data) {

          $output=array();
          $user=$this->getUser($data);
          if (isset($data['id'])) { $id=$data['id']; } else { $id=0; }
          if (isset($data['rnum'])) { $rnum=$data['rnum']; } else { $rnum=''; }
          if ($rnum=='') {
             $rnum=$this->getRNum();
             $data['rnum']=$rnum;
          }

          if ($user['forced_off']==1) {
             $output=array();
             $output['user']=$user;
             return $output;
          } else {

            $output=array();
            $output['user']=$user;

            if ($id!=""&&$id!=0) {
                $output['section_title']=$this->getColumnTitle($id);
                $options=$this->getColumnOptions($id);
            } else {
                $output['section_title']="Please Select a Category";
                $options=array();
            }

            $output['rnum']=$rnum;
            $output['options']=$options;

            $output['categories']=$this->getColumnCategories($data);
            $output['option_selection']=$this->getUserSelections($data);
            $output['filter_selection']=$this->getFilterSelections($data);
            $output['column_selection']=$this->getColumnSelections($data);

            }
          $output['optionData']=$this->makeOptionData($id,$rnum,$output['user']['USER_NAME']);
          $output['filterData']=$this->makeFilterData($id,$rnum);
          $output['columnData']=$this->makeColumnData($id,$rnum);
	  $output['formData']=$this->makeFormData($id,$rnum);
          $output['postForm']=$this->makeFormData($id,$rnum);
          return $output;
    }

    function getSQL($data) {
       $sql=$data['sql'];
       $list=$this->X->sql($sql);
       $output=array();
       $output['list']=$list;
       return $output;
    }

    function getOptionsList($data) {
       $output=$this->getHomePage($data);
       $sql="SELECT * FROM RWH_DA_OPTIONS ORDER BY WEIGHT_ID, OPTION_ORDER";
       $list=$this->X->sql($sql);
       $output['list']=$list;
       $formData=array();
       $formData['ID']="";
       $formData['WEIGHT_ID']="";
       $formData['TITLE']="";
       $formData['SHORT_NAME']="";
       $formData['OPTIONID']="";
       $formData['OPTION_DSC']="";
       $formData['OPTION_TYPE']="";
       $formData['OPTION_SOURCE']="";
       $formData['OPTION_ORDER']="";
       $formData['DISPLAY_LEVEL']="";
       $output['formData']=$formData;
       return $output;
    }

    function selectCriteriaFilter($data) {
       print_r($data);
    }

    function selectCriteriaColumn($data) {
       print_r($data);
    }

    function criteriaSearch($data) {
        $output=$this->getHomePage($data);
        $sql="SELECT * FROM RWH_DA_OPTIONS WHERE UPPER(OPTION_DSC) LIKE '%" . strtoupper($data['data']['SEARCH']) . "%' ORDER BY OPTION_ORDER";
        $options=$this->X->sql($sql);
        $output['section_title']="Search Results";
        $output['options']=$options;
        return $output;
    }

    function postEditDAOption($data) {
          $post=$data['data'];
          $this->X->post($post);
          $output=array();
          $output['error_code']=0;
          return $output;
    }

}

//---
// BEGIN
//---

$A=new DA();
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

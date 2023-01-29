<?php
require_once('../../../MIST/oracle_oci.inc.php');
class OracleDB
{

    public $results;

	function connect() {
                //--
                //-- Basic connect without context security.
                //--
		$db = new oracleOCI();
		return $db;
	}

	function connectACN() {
                //--
                //-- Basic connect with context security.
                //--
		$db = new oracleOCI();
		$db->connectACN();
		return $db;
	}

	function sql($s) {
                //--
                //-- Connect, run a sql query, and return the dataset. 
                //--
		$db = new oracleOCI();
		$db->connectACN();
		$stmt = $db->prepare($s); 
		$stmt->execute();
		$a = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $a;
	}

        function get($s) {
                //--
                //-- Connect, run a sql query, and put the dataset into $this->results. 
                //--
		$db = new oracleOCI();
		$db->connectACN();
		$stmt = $db->prepare($s); 
		$stmt->execute();
		$this->results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return sizeof($this->results);
        }

        function require($s,$condition='',$msg='') {
                //--
                //-- Run a sql query and return $msg if condition is not met.
                //-- if condition is blank it defaults to sizeof dataset > 0.
                //-- Output dataset is placed in $this->results.
                //--
		$db = new oracleOCI();
		$db->connectACN();
		$stmt = $db->prepare($s); 
		$stmt->execute();
		$this->results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ($condition=='') {
                      if (sizeof($this->results)>0) {
                            return true;
                      } else {
                            return false;
                            if ($msg!='') {
                                die($msg);
                            }
                      }
                }
        }

	function sqlC($s) {
		$db = new oracleOCI();
		$db->connectACN();
		$stmt = $db->prepare($s); 
		$stmt->execute();
		$a = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $a[0]['C'];
	}

	function execute($s) {
		$db = new oracleOCI();
		$db->connectACN();
		$stmt = $db->prepare($s); 
		$stmt->execute();
	}

	function post($post) {
		$db = new oracleOCI();
		$db->connectACN();

		//-----------------------------------------------------
		// use $post['primary_key'] to define the PK
		// use $post['sequence_name'] to define the sequenece.
		//-----------------------------------------------------

                $id="";
                if (!isset($post['primary_key'])) $post['primary_key']="ID";
                if (isset($post[$post['primary_key']])) {
                         $id=$post[$post['primary_key']];
                } else {
                      $id="";
                }

		$seq_name="MEANINGLESS_KEY_SEQ";

		if ($id=="") $id="0";
		if ($id==""||$id=="0") {
			$sql = "SELECT MEANINGLESS_KEY_SEQ.NEXTVAL AS C FROM DUAL";
			$data = $db->query($sql);
			$result = $data->fetch(PDO::FETCH_ASSOC);
			$id=$result['C'];
			$sql = "INSERT INTO " . $post['table_name'] .  " (" . $post['primary_key'] . ") VALUES (" . $id . ")";
			$s=str_replace("XXX","YYY",$sql);
			$st = $db->prepare($s);
			$st->execute();
		}

		foreach ($post as $name => $value) {
			if ($name!="action"&&$name!=$post['primary_key']&&$name!='table_name') {
			   	    $sql = "UPDATE ". $post['table_name'] . " SET " . $name . " = ? WHERE " . $post['primary_key'] . " = ?";
			            $stmt = $db->prepare($sql);
			            $stmt->bindParam(1, $value);
			            $stmt->bindParam(2, $id);
			            $stmt->execute();
				}
			}
		return($id);
	}

	function postDT($post) {
		$db = new oracleOCI();
		$db->connectACN();

		//-----------------------------------------------------
		// use $post['primary_key'] to define the PK
		// use $post['sequence_name'] to define the sequenece.
		//-----------------------------------------------------


		if (isset($post[$post['primary_key']])) {
			$id=$post[$post['primary_key']];
		} else {
			$id="0";
		}

		if (isset($post['sequence_name'])) {
			$seq_name=$post['sequence_name'];
		} else {
			$seq_name="MEANINGLESS_KEY_SEQ";
		}

		if ($id=="") $id="0";
		if ($id==""||$id=="0") {
			$sql = "SELECT " . $seq_name . ".NEXTVAL AS C FROM DUAL";
			$data = $db->query($sql);
			$result = $data->fetch(PDO::FETCH_ASSOC);
			$id=$result['C'];
			$sql = "INSERT INTO " . $post['table_name'] .  " (" . $post['primary_key'] . ") VALUES (" . $id . ")";
			$s=str_replace("XXX","YYY",$sql);
			$st = $db->prepare($s);
			$st->execute();
		}

		foreach ($post as $name => $value) {
			if ($name!="action"&&$name!=$post['primary_key']&&$name!='table_name') {
			     if (substr($name,-5)=="_DATE") {
			   	    $sql = "UPDATE ". $post['table_name'] . " SET " . $name . " = TO_DATE(?,'MM/DD/YYYY') WHERE " . $post['primary_key'] . " = ?";
			            $stmt = $db->prepare($sql);
			            $stmt->bindParam(1, $value);
			            $stmt->bindParam(2, $id);
			            $stmt->execute();
				} else {
			   	    $sql = "UPDATE ". $post['table_name'] . " SET " . $name . " = ? WHERE " . $post['primary_key'] . " = ?";
			            $stmt = $db->prepare($sql);
			            $stmt->bindParam(1, $value);
			            $stmt->bindParam(2, $id);
			            $stmt->execute();
				}
				}
			}
		return($id);
	}

	function postDoc($id,$table_name,$template,$data) {
		//--connect
		$db = new oracleOCI();
		$db->connectACN();

		//--get user's segment
		$uid=$_COOKIE['uid'];
		$sql = "SELECT SEGMENT_ID AS C FROM TBL_USER WHERE ID = '" . $uid . "'";
		$data = $db->query($sql);
		$result = $data->fetch(PDO::FETCH_ASSOC);
		$segment_id=$result['C'];

		//--if ID==0 Get a sequence number.
		if ($id=="0") {
			$sql = "SELECT MEANINGLESS_KEY_SEQ.NEXTVAL AS C FROM DUAL";
			$data = $db->query($sql);
			$result = $data->fetch(PDO::FETCH_ASSOC);
			$id=$result['C'];
		}

		//-- Find out if record exists.
		$sql="SELECT COUNT(*) AS C FROM " . $table_name . " WHERE ID = '" . $id . "'";
		$data = $db->query($sql);
		$result = $data->fetch(PDO::FETCH_ASSOC);
		$count=$result['C'];

		//-- if record does not exist, create it.
		if ($count==0) {
			$sql = "INSERT INTO " . $table_name .  " (ID, SEGMENT_ID) VALUES ('" . $id . "','" . $segment_id . "')";
			$s=str_replace("XXX","YYY",$sql);
			$st = $db->prepare($s);
			$st->execute();	
			$document_json=$template;
		} else {
			$sql="SELECT DOCUMENT FROM " . $table_name . " WHERE ID = '" . $id . "'";
			$data = $db->query($sql);
			$result = $data->fetch(PDO::FETCH_ASSOC);
			$document_json=$result['DOCUMENT'];
		}

		$document=json_decode($document_json,true);

		foreach ($post as $name => $value) {
			if ($name!="ID"&&$name!="SEGMENT_ID") {
				$document[$name]=$value;
			}
		}
		$document_json = json_encode($document);
		$sql = "UPDATE ". $post['table_name'] . " SET DOCUMENT = ? WHERE ID = ?";
	        $stmt = $db->prepare($sql);
        	$stmt->bindParam(1, $value);
            	$stmt->bindParam(2, $id);
            	$stmt->execute();
		return($id);
	}


	function sqlObject($s) {
		$db = new oracleOCI();
		$db->connectACN();
		$uid=$_COOKIE['uid'];

		$sql = "ALTER SESSION SET NLS_DATE_FORMAT = 'MM/DD/YYYY'";
		$stmt = $db->prepare($sql); 
		$stmt->execute();

		$sql = "SELECT USER_ID, ROLE, REGION_ID FROM FPS_USER WHERE USER_ID = " . $uid;
		$stmt = $db->prepare($sql); 
		$stmt->execute();
		$user = $stmt->fetch(PDO::FETCH_ASSOC);

		$sql = "SELECT PRIV_ID FROM FPS_USER_PRIVS WHERE USER_ID = " . $uid;
		$stmt = $db->prepare($sql); 
		$stmt->execute();
		$privs = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$user['PRIVS']=$privs;
		$output=array();
		$output['user']=$user;

		$stmt = $db->prepare($s); 
		$stmt->execute();
		$a = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$output['data']=$a;
		return $output;
	}

	function sqlJSON($s) {
		$a=$this->sqlObject($s);
		return(str_replace('null','""',json_encode($a)));
	}

	function packageObject($query) {
		$db = new oracleOCI();
		$db->connectACN();
		$uid=$_COOKIE['uid'];

		$sql = "ALTER SESSION SET NLS_DATE_FORMAT = 'MM/DD/YYYY'";
		$stmt = $db->prepare($sql); 
		$stmt->execute();

		$sql = "SELECT USER_ID, ROLE, REGION_ID FROM FPS_USER WHERE USER_ID = " . $uid;
		$stmt = $db->prepare($sql); 
		$stmt->execute();
		$user = $stmt->fetch(PDO::FETCH_ASSOC);

		$sql = "SELECT PRIV_ID FROM FPS_USER_PRIVS WHERE USER_ID = " . $uid;
		$stmt = $db->prepare($sql); 
		$stmt->execute();
		$privs = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$user['PRIVS']=$privs;
		$output=array();
		$output['user']=$user;

		$sql = "SELECT SQL, PARAM_COUNT FROM FPS_SQL WHERE QUERY_ID = " . $query['id'];
		$stmt = $db->prepare($sql);
		$stmt->execute();
		$d = $stmt->fetch(PDO::FETCH_ASSOC);
		$s = $d['SQL'];
		$pc = $d['PARAM_COUNT'];
		$stmt = $db->prepare($s); 
		for($x=1;$x<=$pc;$x++) {
			$o='param' . $x;
		        $stmt->bindParam($x, $query[$o]);
		}

		$stmt->execute();
		$a = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$output['data']=$a;
		return $output;
	}

	function packageJSON($s) {
		$a=$this->packageObject($s);
		return(str_replace('null','""',json_encode($a)));
	}

	function paramCount($s) {
		$db = new oracleOCI();
		$db->connectACN();
		$sql = "SELECT PARAM_COUNT FROM FPS_SQL WHERE QUERY_ID = " . $s;
		$stmt = $db->prepare($sql); 
		$stmt->execute();
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
		return($data['PARAM_COUNT']);
	}
}

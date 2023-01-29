<?php

/* -- PUT THIS SOMEWHERE ELSE -- */

define('SCHEMA', 'FPSPROD');
define('PASSWORD', 'FPSotof123!!');
define('DATABASE', 'ORAFPS');
define('CHARSET', 'UTF8');
define('CLIENT_INFO', 'ACN');

class OracleDB {

        protected $params=array();

	function connect() {
		$db = new oracleOCI();
		return $db;
	}

	function setContext($context='') {
		$db = new oracleOCI();
		$db->setContext($context);
		return $db;
	}

        function startParams() {
               $this->params=array();
        }

        function addParams($name, $value, $inout='IN', $len=-1, $dtype=SQLT_CHR) {
               $tmp=array();
               $tmp['name']=$name;
               $tmp['val']=$value;
               $tmp['inout']=$inout;
               $tmp['len']=$len;
               $tmp['dtype']=$dtype;
               array_push($this->params,$tmp);
        }

        function plsql($proc,$params,$type='REFCURSOR') {
                 //-- NULL (no return value/procedure)
                 //-- VARCHAR2 (function returns string)
                 //-- NUMBER (function returns string)
                 //-- REFCURSOR (functionszZ returns sys_refcursor)
        }

	function sql($s,$params=array()) {
                //--
                //-- Connect, run a sql query, and return the dataset. 
                //--
		$db = new oracleOCI();
		$db->setContext();
       		$param_count=substr_count($s,':');
	        if ($param_count==0) {
                        //-- Query with no parameters
			$stmt = $db->prepare($s); 
			$stmt->execute();
      			$results=array();
	    		if (!isset($db->lastError['code'])) {
                             $results['results']=$stmt->fetchAll(PDO::FETCH_ASSOC);
       				if (sizeof($results['results'])==0) {
                                        $results['lastError']=array();
                                        $results['lastDebug']=array();
         				$results['lastError']['code']=1403;
         				$results['lastError']['message']="ORA-01403: no data found";
				        $results['lastDebug']['sql']=$s;
                                        $results['lastDebug']['binds']=array();	
       				} else {
					$results['lastError']=$db->lastError;
           				$results['lastDebug']=$db->lastDebug;
                                }
                        }            		
	        	return $results;
	        } else {
                       if (sizeof($params)==0) $params=$this->params;
		       $plsql=0;
                       if (substr_count(strtoupper($s),'INTO')>0) $plsql=0;;          
                       foreach($params as $p) { if ($p['inout']!="IN") $plsql=1; }

                       if ($plsql==1) {
				$t=strtoupper($s);
					 if (strpos($t,"BEGIN")) {
                            			if (strpos($t,"BEGIN")!=0) $s.="BEGIN ".$s." END;";
					} else {
						 $t="BEGIN " . $s; 
						if (substr($t,-1)==';')
                                                	{ $u=$t . " END;"; }
						else { $u=$t . "; END;"; }
 						 $s=$u;
					}
                       }   
			
  		       $stmt = $db->prepare($s); 
		       $msg_out="";
                       foreach($params as $p) {
				if ($p['inout']=="IN") {
                                       $stmt->bindParam($p['name'], $p['val']);
                                } else {
                                        $plsql=1;
					$stmt->bindParam($p['name'], $msg_out, $p['len'], SQLT_CHR);
                                }
                        }
			$stmt->execute();
      			$results=array();
                        if ($plsql==0) {
		    		if (!isset($db->lastError['code'])) {
                                   $results['results']=$stmt->fetchAll(PDO::FETCH_ASSOC);
       				   if (sizeof($results['results'])==0) {
                                        $results['lastError']=array();
                                        $results['lastDebug']=array();
         				$results['lastError']['code']=1403;
         				$results['lastError']['message']="ORA-01403: no data found";
				        $results['lastDebug']['sql']=$s;
                                        $results['lastDebug']['binds']=array();	
       				   } else {
					$results['lastError']=$db->lastError;
           				$results['lastDebug']=$db->lastDebug;
                                   }
                                }
                        } else {
                            if (!isset($db->lastError['code'])) {
                                $results['lastError']=array();
                                $results['lastDebug']=array(); 
                                $results['results']=$msg_out;  
                            } else {
				$results['lastError']=$db->lastError;
           			$results['lastDebug']=$db->lastDebug;
				$results['results']="";
                            }
                        }    		
	        	return $results;
                      
                        
		}
	}

	function post($post) {

		$db = new oracleOCI();
		$db->setContext();

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

	function execute($s) {
		$db = new oracleOCI();
		$db->setContext();
		$stmt = $db->prepare($s); 
		$stmt->execute();
                $results=array();
                $results['results']=array();
                $results['lastError']=$db->lastError;
                $results['lastDebug']=$db->lastDebug;
	}
}


class OracleOCI
{
  public $debug = true;
  protected $stid = null;
  protected $conn = null;
  protected $sql; //stored sql for debug
  protected $prefetch = 100;
  protected $pagination = false;
  protected $page = 1;
  protected $perPage = 30;
  protected $rowId = null;
  protected $bindValues = array(); //stored values binded for debug
  protected $parameters = array();
  public $lastError = array();
  public $lastDebug = array();

  function __construct($module="", $cid="") {
    $this->lastError=array();
    $this->lastDebut=array();
    $this->conn = @oci_pconnect(SCHEMA, PASSWORD, DATABASE, CHARSET);
    if (!$this->conn) {
      $m = oci_error();
      throw new \Exception('Cannot connect to database: ' . $m['message']);
    }
    // Record the "name" of the web user, the client info and the module.
    // These are used for end-to-end tracing in the DB.
    oci_set_client_info($this->conn, CLIENT_INFO);
    if($module!="")
      oci_set_module_name($this->conn, $module);
    if($cid != "")
      oci_set_client_identifier($this->conn, $cid);
  }

  function start_params() {
      $this->parameters=array();
  }
  
  function add_params($name, $value) {
       $p=array();
       $p['name']=$name;   
       $p['value']=$value;
       array_push($this->parameters,$p);
  }

  function get_sql($sql, $data) {
       $stmt = $sql;
       // SELECT TNAME INTO :form.TNAME FROM TAB WHERE ROWNUM = 1;
       $param_count=substr_count(':',$stmt);
       if ($param_count==0) {
             $this->stid=oci_parse($this->conn,$stmt);
	$this->lastError=array();
        $this->lastDebug=array();
        $success = @oci_execute($this->stid);
        if($success === FALSE)
        {
            $this->lastError=$this->getError();
            $this->lastDebug=$this->getDebug();
            $result=array();
            $result['result']=array();
            $result['lastError']=$this->lastError;
            $result['lastDebug']=$this->lastDebug;
	    return $result;
        } else { 
	    
	}
      
  }

  function get_scalar_function($fn, $params, $type='SQLR_CHR') {

        $rv='';

        $stmt="BEGIN :return_value := " . $fn . "(";
        $pcount=0;
        foreach($params as $p) {
           if ($pcount>0) $stmt.=",";
           $stmt.=":".$p['name']; 
           $pcount++;
        }
        $stmt .= "); END;";

        $stmt=$this->strip_special_characters($stmt);
        $this->stid=oci_parse($this->conn,$stmt);

        foreach($params as $p) oci_bind_by_name($this->stid,':'.$p['name'],$p['value']);
        oci_bind_by_name($this->stid,':return_value',$rv);

	$this->lastError=array();
        $this->lastDebug=array();
        $success = @oci_execute($this->stid);
        if($success === FALSE)
        {
            $this->lastError=$this->getError();
            $this->lastDebug=$this->getDebug();
            $result=array();
            $result['result']=array();
            $result['lastError']=$this->lastError;
            $result['lastDebug']=$this->lastDebug;
	    return $result;
        } else {
              $success=@oci_execute($rc);
              $this->lastError=$this->getError();
              $this->lastDebug=$this->getDebug();
              $result=array();
              $result['result']=array();
              $result['lastError']=$this->lastError;
              $result['lastDebug']=$this->lastDebug;
              if (isset($result['lastError']['code'])) {
                   return $result;
              } else {
                   $output=array();
                   oci_fetch_all($rc,$output,0,-1,OCI_ASSOC);	
                   $result=array();
                   $result['result']=$output;
                   $result['lastError']=$this->lastError;
                   $result['lastDebug']=$this->lastDebug;
	           return $result;
              }
        }
  }

  function get_ref_cursor($fn, $params=array()) {

//        if (sizeof($params)==0) $params=$this->parameters;

        $stmt="BEGIN :return_cursor := " . $fn . "(";
        $pcount=0;
        foreach($params as $p) {
           if ($pcount>0) $stmt.=",";
           $stmt.=":".$p['name']; 
           $pcount++;
        }
        $stmt .= "); END;";

        $stmt=$this->strip_special_characters($stmt);
        $this->stid=oci_parse($this->conn,$stmt);
        $rc=oci_new_cursor($this->conn);
        foreach($params as $p) oci_bind_by_name($this->stid,':'.$p['name'],$p['value']);
        oci_bind_by_name($this->stid,':return_cursor',$rc,-1,OCI_B_CURSOR);

        $this->lastError=array();
        $this->lastDebug=array();
        $success = @oci_execute($this->stid);
        if($success === FALSE)
        {
            $this->lastError=$this->getError();
            $this->lastDebug=$this->getDebug();
            $result=array();
            $result['result']=array();
            $result['lastError']=$this->lastError;
            $result['lastDebug']=$this->lastDebug;
	    return $result;
        } else {
              $success=@oci_execute($rc);
              $this->lastError=$this->getError();
              $this->lastDebug=$this->getDebug();
              $result=array();
              $result['result']=array();
              $result['lastError']=$this->lastError;
              $result['lastDebug']=$this->lastDebug;
              if (isset($result['lastError']['code'])) {
                   return $result;
              } else {
                   $output=array();
                   oci_fetch_all($rc,$output,0,-1,OCI_ASSOC);	
                   $result=array();
                   $result['result']=$output;
                   $result['lastError']=$this->lastError;
                   $result['lastDebug']=$this->lastDebug;
	           return $result;
              }
	   }
        }
   }
  function __destruct() {
    if ($this->stid)
    oci_free_statement($this->stid);
    if ($this->conn)
    oci_close($this->conn);
  }
  public function getNewDescriptor($type)
  {
    return oci_new_descriptor($this->conn, $type);
  }
  public function commit()
  {
    oci_commit($this->conn);
  }
  public function rollback()
  {
    oci_rollback($this->conn);
  }
  public function connect()
  {
    //do nothing
    return $this;
  }
  public function paginate($page=1, $perPage=30)
  {
    $this->pagination = true;
    $this->page = $page;
    $this->perPage = $perPage;
  }

  public function strip_special_characters($str)
  {
    $out = "";
    for ($i = 0;$i < strlen($str);$i++)
      if ((ord($str[$i]) != 9) && (ord($str[$i]) != 10) &&
          (ord($str[$i]) != 13))
        $out .= $str[$i];
 
  // Return character only strings.
  return $out; }


  public function setContext($context='')
  {
    //execute context
    $this->prepare("BEGIN FPS\$CONTEXT.FPS_NEW_CONTEXT(:uid,'" . $_SERVER['SERVER_NAME'] . "');  END;");
	if (isset($_COOKIE['uid'])) {
	    $this->bindValue(":uid",$_COOKIE['uid']);
	} else {
		$u="55001";
	        $this->bindValue(":uid",$u);
	}
    $this->execute();
    return $this;
  }

  public function getError()
  {
    //return error data if any
    return oci_error($this->stid);
  }
  public function errorInfo()
  {
    return $this->getError();
  }
  public function getDebug()
  {
    $debug = array("sql"=>$this->sql);
    $debug['binds'] = print_r($this->bindValues, true);
    return $debug;
  }
  public function prepare($sql)
  {
    $this->bindValues = array();
    $this->sql = $sql;
    if($this->pagination)
    {
      $this->sql = 'SELECT * FROM (SELECT a.*, ROWNUM AS rnum FROM (' . $sql . ') a WHERE ROWNUM <= :sq_last) WHERE :sq_first <= RNUM';
    }
    if(strpos($this->sql,'?')!==false)
    {
      //there are positional parameters
      //replace all ? params with :pos# with a 1 index
      $number = substr_count($this->sql, '?');
      $i = $number;
      for($i;$i>0;$i--)
      {
        $this->sql = substr_replace($this->sql,":pos".$i,strrpos($this->sql,'?'),1);
      }
    }
    $this->stid = oci_parse($this->conn, $this->sql);
    if($this->stid === FALSE)
    {
      echo "parse error";
      //error
      print_r($this->errorInfo());
    }
    if ($this->prefetch >= 0) {
      oci_set_prefetch($this->stid, $this->prefetch);
    }
    return $this;
  }
  public function bindValue($name,&$val, $length = -1, $type = SQLT_CHR)
  {
    if(is_integer($name))
    {
      $name=":pos".$name;
    }
    $this->bindValues[$name] = $val;
    if(is_array($val))
    {
      oci_bind_array_by_name($this->stid, $name, $val, count($val), $length,$type);
    } else {
      oci_bind_by_name($this->stid, $name, $val, $length, $type);
    }
  }
  public function bindParam($name,&$val, $length = -1, $type = SQLT_CHR)
  {
    $this->bindValue($name,$val,$length, $type);
  }

  public function query($sql)
  {
    $this->prepare($sql);
    $this->execute();
    return $this;
  }
  /**
  * Executes the sql passed in with prepare
  * @return bool If the execution was successful
  */

  public function execute($transaction = OCI_COMMIT_ON_SUCCESS, $actionTrace = null)
  {
    if($actionTrace != null) { oci_set_action($this->conn, $actionTrace); }
    if($this->pagination)
    {
      //bind our start and end
      $this->bindValue(":sq_first",($this->page-1)*$this->perPage);
      $this->bindValue(":sq_last",($this->page)*$this->perPage -1);
    }
    if(strpos($this->sql, 'ROWID')!==false)
    {
      $this->rowid = oci_new_descriptor($this->conn, OCI_D_ROWID);
      oci_define_by_name($this->stid, "ROWID", $this->rowid, OCI_D_ROWID);
    } else if (strpos($this->sql, 'ROW_ID')!==false)
    {
      $this->rowid = oci_new_descriptor($this->conn, OCI_D_ROWID);
      oci_define_by_name($this->stid, "ROW_ID", $this->rowid, OCI_D_ROWID);
    } else {
      $this->rowid = null;
    }

    $this->lastError = array();
    $this->lastDebug = array();
    $success = @oci_execute($this->stid, $transaction);
    if($success === FALSE)
    {
      $this->lastError=$this->getError();
      $this->lastDebug=$this->getDebug();
    }
    return $success;
  }
  public function fetch($type = null)
  {
    if($type == PDO::FETCH_ASSOC)
    {
        return oci_fetch_array($this->stid, OCI_ASSOC+OCI_RETURN_NULLS+OCI_RETURN_LOBS);
    }
    return oci_fetch_array($this->stid, OCI_NUM+OCI_RETURN_NULLS+OCI_RETURN_LOBS);
  }

  public function fetchJSON($type = null)
  {
    $o=array();
    $tmp = $this->fetch($type);
    $o['results']=json_encode(str_replace("null",'""',$tmp), JSON_HEX_TAG |
        JSON_HEX_APOS |
        JSON_HEX_QUOT |
        JSON_HEX_AMP |
        JSON_UNESCAPED_UNICODE);
    $o['lastError']=$this->lastError;
    $o['lastDebug']=$this->lastDebug;
    return $o;        
  }


  public function fetchAll($type = null)
  {
    $flags = OCI_FETCHSTATEMENT_BY_ROW+OCI_RETURN_NULLS+OCI_RETURN_LOBS;
    if($type == PDO::FETCH_ASSOC)
    {
      $flags += OCI_ASSOC;
    } else {
      $flags += OCI_NUM;
    }
    $res = array();
    @oci_fetch_all($this->stid, $res, 0, -1, $flags);
  /*  while ( $row = oci_fetch_array($this->stid,OCI_ASSOC+OCI_RETURN_NULLS+OCI_RETURN_LOBS) ) {
        // Call the load() method to get the contents of the LOB
        //print $row['ROW_ID']->load()."\n";
        if(!$this->rowid!==null)
        echo $this->rowid."\n";
        $res[] = $row;
    }*/
    /*if(count($res)>0)
    {
      $checkForLob = $res[0];
      $lobKeys = array();
      $hasLob = false;
      foreach($checkForLob as $key=>$val)
      {
        if(is_a($val,"OCI-Lob"))
        {
          $hasLob = true;
          $lobKeys[] = $key;
        }
      }
      if($hasLob)
      {
        //load all lobs

        $i = 0;
        $len = count($res);
        print_r($lobKeys);
        for($i;$i<$len;$i++)
        {
          foreach($lobKeys as $key)
          {
            echo $key;
            print_r($res[$i]);
            var_dump($res[$i][$key]);
            if(is_object($res[$i][$key]))
            {
              $res[$i][$key] = $res[$i][$key]->load();
            }
          }
        }
      }
    }*/
    //$this->stid = null;  // free the statement resource
    return($res);
  }

  public function fetchAllJSON($type = null)
  {
    $o=array();
    $tmp = $this->fetchAll($type);
    $o['results']=$tmp;
    $o['lastError']=$this->lastError;
    $o['lastDebug']=$this->lastDebug;
    return $o;        
  }

  public function sql($s,$params=array()) {
      $stmt=$this->prepare($s);
      $stmt->execute($s);
      $results=array();
//      if (!isset($this->lastError['code'])) {
           $results['results']=$stmt->fetchAll(PDO::FETCH_ASSOC);
           $results['lastError']=$this->lastError;
           $results['lastDebug']=$this->lastDebug;
//      } else {
//           $results['results']=array();
//           $results['lastError']=$this->lastError;
//           $results['lastDebug']=$this->lastDebug;
//      }
      return $results;
  }

}

?>

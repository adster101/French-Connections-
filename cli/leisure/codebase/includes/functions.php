<?PHP  
	function showme($msg){
		echo "<pre>".print_r($msg,true)."</pre>";
	}
	
	function loadAccoLayoutLayers($housecode, &$ret, $lang = 'nl', $fromparrentid = NULL, $parentsequencenumber = NULL){
		$fpid = ($fromparrentid == NULL) ? "IS NULL" : " = ".$fromparrentid;
		$psid = ($parentsequencenumber == NULL) ? "IS NULL" : " = ".$parentsequencenumber;
		
		$sql = "SELECT al.layoutid, al.sequencenumber, al.numberofitems, al.parentid, al.parentsequencenumber, rlst.description
				FROM jsonrpc.acco_layout al
				LEFT JOIN jsonrpc.reference_layout_subtypes rlst ON rlst.id = al.layoutid AND rlst.lang = '".$lang."'
				WHERE al.housecode = '".$housecode."' 
				AND al.parentid ".$fpid." AND al.parentsequencenumber ".$psid."
				ORDER BY al.layoutid ASC, 
				al.sequencenumber ASC";
				
		$qry = getData($sql);
		
		if($qry->num_rows > 0){
			while($res = $qry->fetch_object()){
				$tmp = array();
				//$tmp["layoutid"] = $res->layoutid;
				//$tmp["sequencenumber"] = $res->sequencenumber;
				$tmp["numberofitems"] = $res->numberofitems;
				$tmp["description"] = $res->description;
				//$tmp["detaildescr"] = $res->detaildescr;
				//$tmp["parentid"] = $res->parentid;
				//$tmp["parentsequencenumber"] = $res->parentsequencenumber;
				
				$tmp["contents"] = array();
				$tmp["details"] = array();
				
				$and_parentid = ($res->parentid == NULL) ? "IS NULL" : " = ".$res->parentid;
				$and_parentsequencenumber = ($res->parentsequencenumber == NULL) ? "IS NULL" : " = ".$res->parentsequencenumber;
				
				$sql_det = "SELECT description FROM jsonrpc.acco_layout_details ald LEFT JOIN jsonrpc.reference_layout_details rld ON ald.detailnumber = rld.id AND rld.lang = '".$lang."' 
						WHERE ald.housecode = '".$housecode."'  AND ald.sequencenumber = ".$res->sequencenumber." AND ald.layoutid = ".$res->layoutid." AND ald.parentid ".$and_parentid." AND parentsequencenumber ".$and_parentsequencenumber.";";
				$qry_det = getData($sql_det);
				while($res_det = $qry_det->fetch_object()){
					array_push($tmp["details"], $res_det->description);
				}
				loadAccoLayoutLayers($housecode, &$tmp["contents"], $lang, $res->layoutid, $res->sequencenumber);
				
				array_push($ret,$tmp);
			}
		}
	}
	
	function loadAccoLayoutLayersString($housecode, $lang = 'nl', $fromparrentid = NULL, $parentsequencenumber = NULL){
		$layout = array();
		loadAccoLayoutLayers($housecode, $layout, $lang, $fromparrentid, $parentsequencenumber); 
		
		$return = parseAccoLayoutContents($layout, true);
		return $return;
	}
	
	function parseAccoLayoutContents($contents, $nocount = false){
		$ret = "";
		if(sizeof($contents)>0){
			$ret .= "<ul>";
			foreach($contents as $cont){
				$ret .= "<li>";
				if(!$nocount) $ret .= $cont['numberofitems'].'x ';
				$ret .= $cont['description'];
				if(sizeof($cont['details']) > 0){
					$ret .= " (".implode(', ',$cont['details']).")";
				}
				if(sizeof($cont['contents']) > 0){
					$ret .= parseAccoLayoutContents($cont['contents']);
				}
				$ret .= "</li>";
			}
			$ret .= "</ul>";
		}
		return $ret;		
	}
	
	function getApersMaxpers($apers = "") {
		$arr = array(1 => 5, 2 => 6, 3 => 7, 4 => 8, 5 => 9, 6 => 12, 7 => 13, 8 => 14, 9 => 15, 10 => 20, 11 => 21, 12 => 22, 13 => 23, 14 => 24, 15 => 25, 16 => 26, 17 => 27, 18 => 28, 19 => 29, 20 => 500, 30 => 500, 40 => 500, 50 => 500);
		if (empty($apers)) return $arr;
		elseif (isset($arr[$apers])) return $arr[$apers];
		else return 500;
	}
?>
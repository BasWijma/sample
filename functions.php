<?php
function MySQL_Query($sql) {
	
	include('config.php');
	
	$conn = new mysqli($servername, $username, $password, $dbname);
	if($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	if ($conn->query($sql) === TRUE) {
		echo "Record updated successfully";
	} else {
		echo "Error updating record: " . $conn->error;
	}
	
	$conn->close();	
}

function Select_Data($sql) {
	
	
	include('config.php');
	
	$conn = new mysqli($servername, $username, $password, $dbname);
	if($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}

	$result = $conn->query($sql);
	$array = array();	
	
	if(mysqli_num_rows($result) > 0) {
		while($row = mysqli_fetch_array($result)) {
			array_push($array,$row);
		}
	}
	
	return $array;
}

function FetchData() {
	
	include('config.php');
	
	$conn = new mysqli($servername, $username, $password, $dbname);
	if($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	
	$sql = "SELECT * FROM anc";
	$result = $conn->query($sql);
	
	if(mysqli_num_rows($result) > 0) {
		$data = array();	
		while($row = mysqli_fetch_array($result)) {
			$data[$row["id"]] = array(	$row["first_name"],
										$row["last_name"],
										$row["date_of_birth"],
										$row["place_of_birth"],
										$row["date_of_death"],
										$row["place_of_death"],
										$row["father_id"],
										$row["mother_id"],
										$row["children"],
										$row["additional_info"],
										$row["sources"] );
		}
	}
	
	return $data;
	
}

function AncData($id) {
	
	include('config.php');
	
	$conn = new mysqli($servername, $username, $password, $dbname);
	if($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	
	$sql = "SELECT * FROM anc WHERE id = '".$id."'";
	$result = $conn->query($sql);
	
	if(mysqli_num_rows($result) > 0) {
		while($row = mysqli_fetch_array($result)) {
			$data = array(	$row["first_name"],
							$row["last_name"],
							$row["date_of_birth"],
							$row["place_of_birth"],
							$row["date_of_death"],
							$row["place_of_death"],
							$row["father_id"],
							$row["mother_id"],
							$row["children"],
							$row["additional_info"],
							$row["sources"] );
		}
	}
	
	//Build array $anc
	$anc = array();
	//1 - 19 Personal data																						  (NEW) MYSQL DB : ancestors
	$anc[1] = $data["first_name"];
	//20 - 29 Father data
	
	//30 - 39 Mother Data
	
	//50 - 99 Children Data				$amc[50] = array(Date of Birth, Date of Death, First name, Last name, Other Parent, etc);		MYSQL DB : cousins
	
	//100 - 199 Sibling Data			$anc[100] = array(Marriage,
	
	//100 - 199 Marriage Data			$anc[100] = array(Date,Partner,etc);											MYSQL DB : marriage
	
	//200 - 299 Profession Data																							MYSQL DB : professions
	
	
	//Sources 1000 - !																									MYSQL DB : sources
		//eg. Birth Date Source = $anc[1020] - $anc[1029]
	
	
	return $data;
	
}

function ConvertDate($date) {
	//Date = 9 characters
	//[1] = Op(1)/Voor(2)/Na(3)/Omstreeks(4)
	//[2]+[3] = Day
	//[4]+[5] = Maand
	//[6]-[9] = Jaar
	
	if(strlen($date) == 9) {	
	
		$pre = substr($date,0,1);
		$day = substr($date,1,2)*1;
		$month = substr($date,3,2)*1;
		$year = substr($date,5,4)*1;
		
		switch($pre) {
			case 1: $pre = "op"; break;
			case 2: $pre = "voor"; break;
			case 3: $pre = "na"; break;
			case 4:
			default: $pre = "omstreeks"; break;
		}
		
		switch($month) {
			case 1: $month_name = "januari"; break;
			case 2: $month_name = "februari"; break;
			case 3: $month_name = "maart"; break;
			case 4: $month_name = "april"; break;
			case 5: $month_name = "mei"; break;
			case 6: $month_name = "juni"; break;
			case 7: $month_name = "juli"; break;
			case 8: $month_name = "augustus"; break;
			case 9: $month_name = "september"; break;
			case 10: $month_name = "oktober"; break;
			case 11: $month_name = "november"; break;
			case 12: $month_name = "december"; break;
			default: $month_name = ""; break;
		}	
		
		$string = "geboren ".$pre;
		if($day > 0) { $string .= " ".$day; }
		if($month_name != "") { $string .= " ".$month_name; }
		if($year > 0) { $string .= " ".$year; }
			
		$return = array($pre,$day,$month_name,$year,$string);
		
	} else {
		
		$return = array(0,0,"",0,"");	

	}
	
	return $return;
	
}

function SaveData($new_data) {
	
	$sql = "UPDATE anc 
			SET 	first_name = '".$new_data['first_name']."',
					last_name = '".$new_data['last_name']."',
					date_of_birth = '".$new_data['date_of_birth']."',
					place_of_birth = '".$new_data['place_of_birth']."',
					date_of_death = '".$new_data['date_of_death']."',
					place_of_death = '".$new_data['place_of_death']."',
					sources = '".$new_data['sources']."'
			WHERE id = '".$new_data['id']."'";

	MySQL_Query($sql);
}

function NewAnc($id) {
	
	$sql = "INSERT INTO anc (	id, 
								first_name, 
								last_name, 
								date_of_birth, 
								place_of_birth, 
								date_of_death, 
								place_of_death, 
								father_id, 
								mother_id, 
								children, 
								additional_info, 
								sources					)
								
			VALUES (			".$id.",
								'',
								'',
								'',
								'',
								'',
								'',
								'0',
								'0',
								'',
								'',
								'')";

	MySQL_Query($sql);
	
	if($id % 2 == 0) { 
		$parent = "father_id";
		$child = $id/2;
	} else { 
		$parent = "mother_id";
		$child = ($id-1)/2;	
	}
	$sql = "UPDATE anc SET ".$parent." = ".$id." WHERE id = ".$child;
	MySQL_Query($sql);
	
	header("Location: edit_anc.php?x=".$id);
	
}


function Edit_Date($date) {
	// < = Voor
	// > = Na
	// ± = Omstreeks
	$voor = "";
	$na = "";
	$omstreeks = "";
	if(strlen($date > 8)) {
		if(substr($date,0,1) == "<") { $pre = "<"; $voor = "selected"; }
		elseif(substr($date,0,1) == ">") { $pre = ">"; $na = "selected"; }
		elseif(substr($date,0,1) == "±") { $pre = "±"; $omstreeks = "selected"; }
		else { $pre = ""; }
		$date = substr($date,-8);
	} else {
		$pre = "";
	}
	
	$day   = substr($date,0,2)*1;
	if(!is_numeric($day)) { $day = 0; }
	$month = substr($date,2,2)*1;
	if(!is_numeric($month)) { $month = 0; }
	$year  = substr($date,4,4)*1;
	if(!is_numeric($year)) { $year = 0; }
	
	$form_field = "<select name='pre'>
						<option value=''><option>
						<option value='<' ".$voor.">Voor</option>
						<option value='>' ".$na.">Na</option>
						<option value='±' ".$omstreeks.">Omstreeks</option>
					</select>
					<select name='day'>";
	$counter = 0;
	while($counter <= 31) {
		if($day == $counter) { $selected = "selected"; } else { $selected = ""; }
		$form_field .= "<option value='".$counter."' ".$selected.">".$counter."</option>";
		$counter++;
	}
	$form_field .= "</select>";
	
	return $form_field;
}



function Date_Array($date) {
	
	if(strlen($date) == 9) {
	
		$pre = substr($date,0,1);
		$day = substr($date,1,2)*1;
		$month = substr($date,3,2)*1;
		$year = substr($date,5,4)*1;
		
		$return = array($pre,$day,$month,$year);
	
	} else {
		
		$return = array(0,0,0,0);
			
	}
	
	return $return;
		
}

function DateToString($event,$pre,$day,$month,$year) {

	$string = "";

	if($day > 0 || $month > 0 || $year > 00) {
		
		switch($event) {
			case 1: default: $string .= ", geboren "; break;
			case 2: $string .= ", overleden "; break;
		}
		
		switch($pre) {
			case 1: $string .= "op "; break;
			case 2: $string .= "voor "; break;
			case 3: $string .= "na "; break;
			case 4: default: $string .= "omstreeks "; break;		
		}
		
		if(is_numeric($day) && $day > 0) {
			$string .= $day." ";
		}
		
		switch($month) {
			case 1: $string .= "januari "; break;
			case 2: $string .= "februari "; break;
			case 3: $string .= "maart "; break;
			case 4: $string .= "april "; break;
			case 5: $string .= "mei "; break;
			case 6: $string .= "juni "; break;
			case 7: $string .= "juli "; break;
			case 8: $string .= "augustus "; break;
			case 9: $string .= "september "; break;
			case 10: $string .= "oktober "; break;
			case 11: $string .= "november "; break;
			case 12: $string .= "december "; break;
			default: $string .= ""; break;
		}
		
		if(is_numeric($year) && $year > 0) {
			$string .= $year;
		}
		
	}
	
	return $string;
	
}

function ParentData($id) {
	
	global $data;
	$string = "";
	
	$pa = $data[$id][6];
	$ma = $data[$id][7];
	
	if($id % 2 == 0 || $id == 1) { $gn = "zn."; } else { $gn = "dr."; }
	
	if($pa > 0) {
		
		$pa_name = $data[$pa][0]." ".$data[$pa][1];
		$string .= ", ".$gn." van <a href='#".$pa."'>".$pa_name."</a>";
		
	}
	
	if($ma > 0) {
		
		if($pa == 0) { $string .= ", ".$gn." van "; } else { $string .= " en "; }
		
		$ma_name = $data[$ma][0]." ".$data[$ma][1];
		$string .= "<a href='#".$ma."'>".$ma_name."</a>";
		
	}
	
	return $string;
	
}

function ChildrenData($id) {

	global $data;
	$string = "";
	
	$sql = "SELECT id FROM anc WHERE father_id = '".$id."' OR mother_id = '".$id."'";
	$result = Select_Data($sql);
	
	if(!empty($result)) {
		
		
		
		$string .= "<br />Kind: ";
		
		foreach($result as $key => $value) {
		
			$child_id = $value["id"];
			$child_name = $data[$child_id][0]." ".$data[$child_id][1];
			$string .= "<a href='#".$child_id."'>".$child_name."</a><br />";
			
		}
		
	}
	
	return $string;

}


function DateConversion($raw) {
	
	$string = "";
	if($raw > 0 || strlen($raw) == 9) {
		
		$pre = substr($raw,0,1);
		$day = substr($raw,1,2)*1;
		$month = substr($raw,3,2)*1;
		$year = substr($raw,5,4)*1;
		
		switch($pre) {
			case 4: $string .= "ca. "; break;
		}
		
		if($day > 0) { $string .= $day; }
		
		if($month > 0) {
			switch($month) {
				case 1: $string .= " januari"; break;
				case 2: $string .= " februari"; break;
				case 3: $string .= " maart"; break;
				case 4: $string .= " april"; break;
				case 5: $string .= " mei"; break;
				case 6: $string .= " juni"; break;
				case 7: $string .= " juli"; break;
				case 8: $string .= " augustus"; break;
				case 9: $string .= " september"; break;
				case 10: $string .= " oktober"; break;
				case 11: $string .= " november"; break;
				case 12: $string .= " december"; break;
				default: $string .= ""; break;
			}
		}
		
		if($year > 0) { $string .= " ".$year; }		
		
	}
	
	return $string;

}


function CheckPrivate($raw,$years) {

	$privacy = true;

	if($raw > 0 || strlen($raw) == 9) {
		
		if(!isset($years)) { $years = 0; }
		
		$day = substr($raw,1,2)*1;
		$month = substr($raw,3,2)*1;
		$year = substr($raw,5,4)*1;

		if($year < (date("Y")-$years)) { $privacy = false; }
		elseif($year == (date("Y")-$years)) {
			if($month < date("n")) { $privacy = false; }
			elseif($month == date("n")) {
				if($day < date("j")) { $privacy = false; }
			}
		}
		
	}
	
	return $privacy;
	
}









?>
<?php
// Defines global constants here
define('REGISTER_SUCCESS', 'Thank you for your registration. We are now redirecting you to the home page.');
define('REGISTER_ERROR', 'Your registration cannot be processed this time, please try again a little bit later.');
define('CONTACT_MSG', 'We appreciate that you\'ve taken the time to write us. We\'ll get back to you very soon. Please come back and see us often.');
define('EDIT_MSG', 'Your profile has been saved successfully. You will be redirected to the home page shortly.');
define('EDIT_MSG1', 'Your request cannot be processed this time, please make sure you enter a valid password.');
define('DEREGISTER_MSG','Your account has been removed from the site. Welcome to come back any time.');
define('DEREGISTER_MSG1','Your request cannot be processed this time, please try again a little bit later.');
define('RECOVER_MSG', 'Your temporary password is ');
define('RECOVER_MSG1', '. You can change it in your profile panel once logged in.');
define('RECOVER_MSG2', 'Your username or email is incorrect');
define('NULL_VALUE', 'PHP Datatype NVARCHAR() compatable with SQLSRV and PDO_SQLSRV when passing as reference in stored procedure. When using the PDO_SQLSRV driver, you can specify the PHP data type when retrieving data from the server with PDOStatement::bindColumn and PDOStatement::bindParam. See PDOStatement::bindColumn and PDOStatement::bindParam for more information.');
define('SOURCE_COMP_WT','Source Emission by Compositions (Without Transportation) - tonne CO2e');
define('SOURCE_COMP_TO','Source Emission by Compositions (Transportation Only) - tonne CO2e');
define('DEST_COMP','Emissions by Composition (tonne CO2e)');
define('TOTAL_WASTE','Total Waste, Transportation, Electricity');
//$arrWASTE = array("Total Waste", "Transportation", "Electricity");

//show message without auto redirecting
function showMessage($msg) {
	echo "<link rel='stylesheet' href='style/form.css'>";
	echo "<div class='msgbox'>".RECOVER_MSG."<span class='msgstrong'>".$msg."</span>".RECOVER_MSG1;
	echo "<br><br><span class='msgcenter'><a href='/login'>Home</a></span></div>";
}

//show message with auto redirecting
//type 0 - go home page, 1- go back immediately, 2 - go back with messages
function showMessageRedirect($msg, $type) {
	echo "<link rel='stylesheet' href='style/form.css'>";
	echo "<div class='msgbox'>".$msg;
	if($type == 0) {
		echo "<br><br><span class='msgcenter'><a href='index.php'>Home</a></span></div>";
		echo "<meta http-equiv='refresh' content='4; url=index.php' />";
	}
	elseif($type == 1){
		echo "<br><br><span class='msgcenter'><a href='javascript:history.go(-1)'>Back</a></span></div>";
		header("location:javascript://history.go(-1)");
	}
	else {
		echo "<br><br><span class='msgcenter'><a href='javascript:history.go(-1)'>Back</a></span></div>";
		header("refresh:4; location:javascript://history.go(-1)");
	}
}

/**
 * Find the position of the Xth occurrence of a substring in a string
 * @param $haystack
 * @param $needle
 * @param $number integer > 0
 * @return int
 */
function strposX($haystack, $needle, $number){
    if($number == '1'){
        return strpos($haystack, $needle);
    }elseif($number > '1'){
        return strpos($haystack, $needle, strposX($haystack, $needle, $number - 1) + strlen($needle));
    }else{
        return error_log('Error: Value for parameter $number is out of range');
    }
}

// extract data from a string
// index - the index of the substring to be extracted
// flag - 0: take whole substring, 1: take composition part, 2: take waste part
function explodeData($data, $index, $flag) {
	$result = null;
	$array = explode(";", $data);
	if(sizeof($array) > $index) {
		$result = $array[$index];
		if ($flag != 0) {
			$pos = strposX($result, ',', 5);
			$result = ($flag == 1 ? substr($result, 1, $pos - 1) : substr($result, $pos+1));
		}
	}
	return $result;
}

// extract data from a string
// index - the index of the substring to be extracted
function splitData($data, $index) {
	$result = null;
	$array = explode(",", $data);
	if(sizeof($array) > $index) {
		$result = $array[$index];
	}
	return $result;
}

//pull out source by composition data
// label - composition name, eg. Organic
// data - tonnage. eg. 3135, 0, 0, 92, 1283, 114, 10
// type - type. eg. bar
function scData($label, $data, $type) {
	$result = null;
	$first = 1;
	$arr = explode(";", $data);
	for ($i=0;$i<sizeof($arr);$i++) {
		$lbl = splitData($label, $i);
		if ($i == sizeof($arr) - 1) {
			$lbl = "Total";
		}
		$tmp = "{'label':'".$lbl."','data':[".explodeData($data, $i, 0)
				."],'type':'".$type."'}";
		if ($first == 1) {
			$result = $tmp;
			$first = 0;
		}
		else {
			$result = $result.','.$tmp;
		}
	}
	return $result;
}

//pull out destination by composition data
// label - composition name, eg. Organic
// data - tonnage. eg. 1283, 553, 85, 49, 58
// id - destination id-1
function dcData($label, $data, $id) {
	$arr = explodeData($data, 0, 1);
	$result = "{'labels':[".$label."],'data':[".explodeData($data, $id, 1)."]},"
			 ."{'labels':[".TOTAL_WASTE."],'data':[".explodeData($data, $id, 2)."]}";
	return $result;
}

?>
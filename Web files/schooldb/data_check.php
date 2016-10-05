<?php
/*
 * File to hold reqular expression checks
 */

$validName = "^[a-zA-Z\s]*$";
$validUsername = "^[a-zA-Z0-9]*$";
$validId = "^[a-zA-Z0-9]+$";
$validDate = "^[0-9]{4}-(0[1-9]|1[0-2])-[0-3][0-9]$";
$validPhone = "^\d{10}$";
$validNumber = "^[0-9]*$";
$validFloat = "^\d+(\.|\,)\d{2}$";
$validClassNumber = "^[a-zA-Z0-9]*$/";
$validNote = "^[a-zA-Z0-9]{1,500}$";

function isNameValid($name) {
	if (preg_match('/' . $validName . '/', $name)) {
		return true;
	} else {
		return false;
	}
}
function isUsernameValid($name) {
	if (preg_match('/' . $validUsername . '/', $name)) {
		return true;
	} else {
		return false;
	}
}
function isIdNumberValid($id) {
	if (preg_match('/' . $validId . '/', $id)) {
		return true;
	} else {
		return false;
	}
}
function isDateValid($date) {
	if (preg_match('/' . $validDate . '/', $date)) {
		return true;
	} else {
		return false;
	}
}
function isPhoneNumberValid($number) {
	if (preg_match("/" . $validPhone . "/", $number)) {
		return true;
	} else {
		return false;
	}
}
function isNumberValid($number) {
	if (preg_match("/". $validNumber . "/", $number)) {
		return true;
	} else {
		return false;
	}
}
function isFloatValid($number) { // float or int
	if (preg_match("/" . $validFloat . "/", $number) || preg_match("/^[0-9]*$/", $number)) {
		return true;
	} else {
		return false;
	}
}
function isClassNumberValid($number) {
	if (preg_match("/" . $validClassNumber . "/", $number)) {
		return true;
	} else {
		return false;
	}
}
function isNoteValid($note) {
  if (preg_match('/' . $validNote . '/', $note)) {
    return true;
  } else {
    return false;
  }
}
//For preparing SQL statements
function mysqli_prepared_query($link, $sql, $typeDef = FALSE, $params = FALSE){
  if($stmt = mysqli_prepare($link, $sql)){
    if(count($params) == count($params,1)){
      $params = array($params);
      $multiQuery = FALSE;
    } else {
      $multiQuery = TRUE;
    } 
   
    if($typeDef){
      $bindParams = array();   
      $bindParamsReferences = array();
      $bindParams = array_pad($bindParams,(count($params,1)-count($params))/count($params),"");        
      foreach($bindParams as $key => $value){
        $bindParamsReferences[$key] = &$bindParams[$key]; 
      }
      array_unshift($bindParamsReferences,$typeDef);
      $bindParamsMethod = new ReflectionMethod('mysqli_stmt', 'bind_param');
      $bindParamsMethod->invokeArgs($stmt,$bindParamsReferences);
    }
   
    $result = array();
    foreach($params as $queryKey => $query){
      foreach($bindParams as $paramKey => $value){
        $bindParams[$paramKey] = $query[$paramKey];
      }
      $queryResult = array();
      if(mysqli_stmt_execute($stmt)){
        $resultMetaData = mysqli_stmt_result_metadata($stmt);
        if($resultMetaData){                                                                              
          $stmtRow = array();  
          $rowReferences = array();
          while ($field = mysqli_fetch_field($resultMetaData)) {
            $rowReferences[] = &$stmtRow[$field->name];
          }                               
          mysqli_free_result($resultMetaData);
          $bindResultMethod = new ReflectionMethod('mysqli_stmt', 'bind_result');
          $bindResultMethod->invokeArgs($stmt, $rowReferences);
          while(mysqli_stmt_fetch($stmt)){
            $row = array();
            foreach($stmtRow as $key => $value){
              $row[$key] = $value;          
            }
            $queryResult[] = $row;
          }
          mysqli_stmt_free_result($stmt);
        } else {
          $queryResult[] = mysqli_stmt_affected_rows($stmt);
        }
      } else {
        $queryResult[] = FALSE;
      }
      $result[$queryKey] = $queryResult;
    }
    mysqli_stmt_close($stmt);  
  } else {
    $result = FALSE;
  }
 
  if($multiQuery){
    return $result;
  } else {
    return $result[0];
  }
} 
?>
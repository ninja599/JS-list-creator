<?php
/*
- Receives data from JS AJAX and sends sanitized data to Listmapper Object.
- Receives returns from object and updates $_SESSION
- Echos sanitized userinput back to JS.
*/

include 'config.php';
$listmapper = new Listmapper(new Dbh(), $userid);

if (isset($_SESSION['list']) && $_SESSION['list'] != null) {
	$mode = 'update';
	$listid = $_SESSION['list']->getId();
} else {
	$mode = 'create';
}

function sanitize($string) {
	$string = mb_strtolower(htmlspecialchars(trim($string)));
	$arr = explode(' ',trim($string));
	$arr[0] = mb_convert_case($arr[0], MB_CASE_TITLE, 'UTF-8');
	$result = implode(" ", $arr);
	return $result;
}

// Adding item recieves JSON with item name and list position.
// $listname = true when adding item and naming list simultaniously
function decode_item($json, $listname = false) {
		$data = json_decode($json, TRUE);
	if ($listname == false) {
		$data['method'] = 'item';
		$data['name'] = sanitize($data['name']);
	}
	return $data;
}

// Create list
if ($mode == 'create') {
  if(isset($_POST['name']) || 
     isset($_POST['item']) || 
     isset($_POST['groceryitem']) ||
     isset($_POST['groceryname'])) { 
    
    // With list name
    if (isset($_POST['name'])) {
      $input = sanitize($_POST['name']);
      $data = array (
        'method' => 'name',
        'name' => $input,
        'type' => 'todo'
      );
    }
    
    // With list item
    if (isset($_POST['item'])) {
      $data = decode_item($_POST['item']);
    }
    
    // Grocery list by renaming list
    if (isset($_POST['groceryname'])) {
      $input = sanitize($_POST['groceryname']);
      $data = array (
        'method' => 'name',
        'name' => $input,
        'type' => 'grocery'
      );
    }
    
    // With item when listType == 'grocery'
    if (isset($_POST['groceryitem'])) {
      $data = decode_item($_POST['groceryitem']);
      $data['method'] = 'item';
      $data['listType'] = 'grocery';
    }
    
    $_SESSION['list'] = $listmapper->create_list($data, $datetime); 
    echo $data['name'];
  }
}

// Update list
if ($mode == 'update') {
	// Editing list name
	if (isset($_POST['name'])) {
		$input = sanitize($_POST['name']);
		$_SESSION['list'] = $listmapper->edit_list_name($_SESSION['list'], $input);
		echo $input;
	}	
  
	// Adding item to list
	if (isset($_POST['item'])) {
		$data = decode_item($_POST['item']);
		$listmapper->add_item($_SESSION['list'], $data);
		echo $data['name'];
	}
  
  // Renaming existing grocery list
  if(isset($_POST['groceryname'])) {
    $input = sanitize($_POST['groceryname']);
		$_SESSION['list'] = $listmapper->edit_list_name($_SESSION['list'], $input);
    echo $input;
  }
  
	// Adding item to grocery list
	if (isset($_POST['groceryitem'])) {
		$data = decode_item($_POST['groceryitem']);
		$listmapper->add_item($_SESSION['list'], $data);
		echo $data['name'];
	}
}

// Moving item around
if (isset($_POST['move'])) { 
	$_SESSION['list'] = $listmapper->move_item($_SESSION['list'], json_decode($_POST['move'], true));
}

// Checking item
if (isset($_POST['checkbox'])) {
	$_SESSION['list'] = $listmapper->check_item($_SESSION['list'], json_decode($_POST['checkbox'], true));
}

// Deleting list or item
if (isset($_POST['delitem'])) {
	$_SESSION['list'] = $listmapper->remove_item($_SESSION['list'], $_POST['delitem']);
}

if (isset($_POST['dellist'])) {
  if($_POST['dellist'] == 'session') {
    $listmapper->del_list($_SESSION['list']);
    unset($_SESSION['list']);
  }
  else if(is_integer((int)$_POST['dellist'])) {
    $listmapper->del_list($listmapper->get_saved_list($_POST['dellist']));
  }
}

// Suggestions
if (isset($_POST['suggest'])) {
  $array = $listmapper->suggest_items($_POST['suggest']);
  echo json_encode($array);
}

if (isset($_POST['delsuggestion'])) {
  $listmapper->del_suggestion($_POST['delsuggestion']);
}
?>

<?php

class Checklist {
	private $id = null, $userid, $name, $type, $datetime, $inDB = false;
	private $items = [];
	
	public function __construct ($data = []) {
		if (!empty($data)) {
			$this->setValues($data);
		}
	}
	
	public function setValues($data = []) {
		if (isset($data['id'])) {
			$this->id = $data['id'];
			$this->inDB = true;
		}
		if (isset($data['userid'])) {
			$this->userid = $data['userid'];
		}
		if (isset($data['name'])) {
			$this->name = $data['name'];
		}
		if (isset($data['type'])) {
			$this->type = $data['type'];
		}
		if (isset($data['datetime'])) {
			$this->datetime = $data['datetime'];
		}
		if (isset($data['items'])) {
			if (isset($data['items'][0])) {
				$this->items = $data['items'];
			} else {
				$this->items[] = $data['items'];
			}
		}
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getUserId() {
		return $this->userid;
	}
	
	public function getName() {
		return $this->name;
	}	
  
	public function getListType() {
		return $this->type;
	}  
	public function getListDate() {
		return $this->datetime;
	}
	
	public function getItems() {
		return $this->items;
	}
	
	public function checkItem($itemposition, $action) {
			$this->items[$itemposition]['checked'] = $action;
      
		return $this->items[$itemposition]['id'];
	}
	
	public function removeItem($itemposition) {
		array_splice($this->items, $itemposition, 1);
		foreach ($this->items as $k => $v) {
			$this->items[$k]['position'] = $k;
		}
		return $this->items;
	}
	
	public function moveItem($itemposition, $direction) {
    $out = array_splice($this->items, $itemposition, 1);
		if ($direction === 'up') {
      array_splice($this->items, $itemposition-1, 0, $out);
      // return 'up';;
		} else if ($direction === 'down') {
			array_splice($this->items, $itemposition+1, 0, $out);
      // return 'down';
		} else if (is_int($direction)) {
      array_splice($this->items, $direction, 0, $out);
      // return 'drag';
    }
    $len = count($this->items);
    for ($i = 0; $i < $len; $i++) {
      $this->items[$i]['position'] = $i;
    }
		return $this->items;
	}
}
?>
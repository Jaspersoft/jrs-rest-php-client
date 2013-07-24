<?php
namespace Jaspersoft\Dto\Report;

class InputControl {

	public $uri;
	public $id;
	public $value;
	public $error;
	public $options = array();

	public function __construct($uri = null, $id = null, $value = null, $error = null) {
		$this->uri = (!empty($uri)) ? strval($uri) : null;
		$this->id = (!empty($id)) ? strval($id) : null;
		$this->value = (!empty($value)) ? strval($value) : null;
		$this->error = (!empty($error)) ? strval($error) : null;
	}

	public static function createFromJSON($json) {
		$data_array = json_decode($json, true);
		$result = array();
		foreach($data_array['inputControlState'] as $k) {
			$temp = new self($k['uri'], $k['id'], $k['value'], $k['error']);
			if (!empty($k['options'])) {
				foreach ($k['options'] as $o) {
					$temp->addOption($o['label'], $o['value'], $o['selected']);
				}
			}
			$result[] = $temp;
		}
		return $result;
	}

	public function addOption($label, $value, $selected) {
		$temp = array('label' => strval($label), 'value' => strval($value), 'selected' => $selected);
		if($selected == 1) { $temp['selected'] = 'true'; } else { $temp['selected'] = 'false'; }
		$this->options[] = $temp;
	}

	public function getOptions() {
		return $this->options;
	}

	public function getUri() {
		return $this->uri;
	}

	public function getId() {
		return $this->id;
	}

}

?>
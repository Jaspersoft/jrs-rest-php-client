<?php
namespace Jasper;

class ReportOptions {

	public $uri;
	public $id;
	public $label;

	public function __construct($uri = null, $id = null, $label = null) {
		$this->uri = (!empty($uri)) ? strval($uri) : null;
		$this->id = (!empty($id)) ? strval($id) : null;
		$this->label = (!empty($label)) ? strval($label) : null;
	}

	public static function createFromJSON($json) {
		$data_array = json_decode($json, true);
		$result = array();
		foreach ($data_array['reportOptionsSummary'] as $k) {
			$result[] = new self($k['uri'], $k['id'], $k['label']);
		}
		return $result;
	}

	public function getUri() {
		return $this->uri;
	}

	public function getId() {
		return $this->id;
	}

	public function getLabel() {
		return $this->label;
	}

	public function setUri($uri) {
		$this->uri = $uri;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function setLabel($label) {
		$this->label = $label;
	}

}

?>
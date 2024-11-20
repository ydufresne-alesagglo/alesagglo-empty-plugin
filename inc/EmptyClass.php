<?php
namespace AlesAgglo\AlesAggloEmptyPlugin;
class EmptyClass {
	private $name;

	public function __construct( $name = 'World' ) {
		$this->name = $name;
	}

	public function hello() {
	return "Hello {$this->name} !";
	}
}
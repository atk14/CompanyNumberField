<?php
class TcCompanyNumberField extends TcBase {

	function test(){
		$this->field = new CompanyNumberField(array());

		$this->assertValid("1234567");
		$this->assertValid("12345678");
		$this->assertValid("123456789");
		$this->assertValid("123456789");

		$err = $this->assertInvalid("XYZ");
	}
}

<?php
class TcCompanyNumberField extends TcBase {

	function test(){
		$this->field = new CompanyNumberField(array());

		$this->assertValid("1234567");
		$this->assertValid("12345678");
		$this->assertValid("123456789");
		$this->assertValid("123456789");

		$err = $this->assertInvalid("XYZ");
		$this->assertEquals("Please enter a valid company registration number",$err);

		// CZ

		$value = $this->assertValid("12345678");
		$this->assertValid("12345678",$value);
		$this->assertTrue($this->field->is_valid_for("CZ",$value,$err));
		$this->assertEquals("12345678",$value);

		$value = $this->assertValid("123456");
		$this->assertValid("123456",$value);
		$this->assertTrue($this->field->is_valid_for("CZ",$value,$err));
		$this->assertEquals("00123456",$value);

		$value = $this->assertValid("123456789");
		$this->assertValid("123456789",$value);
		$this->assertFalse($this->field->is_valid_for("CZ",$value,$err));
		$this->assertValid("12345689",$value);
		$this->assertEquals("Enter the company number as eight digits",$err);

		// LV

		$value = $this->assertValid("LV 12345678901");
		$this->assertEquals("LV 12345678901",$value);
		$this->assertTrue($this->field->is_valid_for("LV",$value,$err));
		$this->assertEquals("LV12345678901",$value);

		$value = $this->assertValid("lv 23456789012");
		$this->assertEquals("LV 23456789012",$value);
		$this->assertTrue($this->field->is_valid_for("LV",$value,$err));
		$this->assertEquals("LV23456789012",$value);

		$value = $this->assertValid("12345678");
		$this->assertEquals("12345678",$value);
		$this->assertFalse($this->field->is_valid_for("LV",$value,$err));
		$this->assertEquals("12345678",$value);
		$this->assertEquals("Enter the company number as LV and eleven digits",$err);

		// CompanyNumberField configured for one specific country

		$this->field = new CompanyNumberField(array("country" => "CZ"));

		$value = $this->assertValid("12345678");
		$this->assertValid("12345678",$value);

		$value = $this->assertValid("123456");
		$this->assertValid("00123456",$value);

		$err = $this->assertInvalid("123456789");
		$this->assertEquals("Enter the company number as eight digits",$err);
	}
}

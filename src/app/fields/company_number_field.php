<?php
/**
 *
 * Usage
 *
 *	$field = new CompanyNumberField([]);
 *	// after form cleaning, a complementary method is_valid_for() can be called for a specific country...
 *	$field->is_valid_for("SK"); // or
 *	$field->is_valid_for("SK",$company_number); // or
 *	$field->is_valid_for("SK",$company_number,$err);
 *
 *	$field = new CompanyNumberField(["country" => "CZ"]); // accepts company numbers of the one country only
 */
class CompanyNumberField extends RegexField {
	static $Patterns;
	static $OutputFilters;

	function __construct($options = array()){
		$options += array(
			"country" => null, // e.g. "CZ"
			"null_empty_output" => true,
			"error_messages" => array(
				"invalid" => _("Please enter a valid company registration number"),
			),
			"format_hints" => array(
				"CZ" => _("Enter the company number as NNNNNNNN (8 digits)"),
				"SK" => _("Enter the company number as NNNNNNNN (8 digits)"),
			),
		);

		$this->country = $options["country"];
		$this->format_hints = $options["format_hints"];

		$_patterns = array();
		foreach(self::$Patterns as $key => $pattern){
			$_patterns[] = "(?<$key>$pattern)";
		}
		parent::__construct("/^(".join("|",$_patterns).")$/",$options);
	}

	function clean($value){
		$value = trim($value);
		$value = strtoupper($value);
		$value = preg_replace('/\s+/',' ',$value);

		list($err,$value) = parent::clean($value);
		if(!$value || !is_null($err)){
			return array($err,$value);
		}
		if($this->country && !$this->is_valid_for($this->country,$value,$err)){
			$value = null;
		}
		$this->cleaned_value = $value;
		return array($err,$value);
	}

	/**
	 *
	 *	$fied->is_valid_for("CZ"); // true or false
	 *
	 *	// automatic value filtering is considered
	 *	$company_number = "12345";
	 *	$fied->is_valid_for("CZ",$company_number); // true
	 *	echo $company_number; // "123 45"
	 *
	 * Typical usage in a controller
	 *
	 *	if($this->request->post() && ($d = $this->form->validate($this->params))){
	 *		if(!$this->form->fields["company_number"]->is_valid_for($d["country"],$d["company_number"],$err_msg)){
	 *			$this->set_error("company_number",$err_msg);
	 *			return;
	 *		}
	 *		$user = User::CreateNewRecord($d);
	 *		// ...
	 *	}
	 */
	function is_valid_for($country,&$company_number = null,&$err_message = null){
		$format_hints = $this->format_hints;

		if(is_null($company_number)){
			$company_number = $this->cleaned_value;
		}

		$err_message = null;

		if(isset(self::$Patterns[$country])){
			$patern = self::$Patterns[$country];

			if(!preg_match("/^$patern$/",$company_number)){
				// trying to check the code again but without white spaces
				$_company_number = preg_replace('/\s*/','',$company_number);
				if($_company_number!==$company_number && ($this->is_valid_for($country,$_company_number,$err_message))){
					$company_number = $_company_number;
				}else{
					$err_message = isset($format_hints[$country]) ? $format_hints[$country] : $this->messages["invalid"];
					return false;
				}
			}
		}else{
			trigger_error("CompanyNumberField: matching pattern missing for $country");
		}

		if(isset(self::$OutputFilters[$country])){
			$pattern = '/' . self::$Patterns[$country] . '/';
			$replace = self::$OutputFilters[$country];
			if(is_callable($replace)) {
				preg_match($pattern, $company_number, $matches);
				$company_number = $replace($matches);
			} else {
				$company_number = preg_replace($pattern,$replace,$company_number);
			}
		}

		return true;
	}
}

// https://cs.wikipedia.org/wiki/Identifika%C4%8Dn%C3%AD_%C4%8D%C3%ADslo_osoby
CompanyNumberField::$Patterns = array(
		"CZ" => '\d{8}',
		"SK" => '\d{8}',
		"SI" => '\d{7}',
		"PL" => '\d{9}',
		"LT" => '\d{9}',
		"LV" => 'LV\d{11}',
);

CompanyNumberField::$OutputFilters = array(
);



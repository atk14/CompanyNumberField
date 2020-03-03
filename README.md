CompanyNumberField
==================

CompanyNumberField is a field for entering company registration numbers ("IČ" or "IČO" in Czech) into forms in ATK14 applications.

Installation
------------

Just use the Composer:

    cd path/to/your/atk14/project/
    composer require atk14/company-number-field

Optionally you can symlink the CompanyNumberField file into your project:

    ln -s ../../vendor/atk14/company-number-field/src/app/fields/company_number_field.php app/fields/company_number_field.php

Usage in an ATK14 application
-----------------------------

CompanyNumberField has method is_valid_for() for re-validation in context of the selected country. 

In a form:

    <?php
    // file: app/forms/users/create_new_form.php
    class CreateNewForm extends ApplicationForm {

      function set_up(){
        // ...
        $this->add_field("company_number", new CompanyNumberField([
          "label" => "Company registration number",
        ]));
        $this->add_field("country",new ChoiceField([
          "label" => "Country",
          "choices" => [
            "CZ" => "Czech Republic",
            "SK" => "Slovakia",
            "AT" => "Austria",
            "PL" => "Poland",
            // ...
          ],
        ]));
      }
    }

In a controller:

    <?php
    // file: app/controllers/users_controller.php
    class UsersController extends ApplicationController {

      function create_new(){
        // ...
        if($this->request->post() && ($d = $this->form->validate($this->params))){
          // postal code re-validation for the selected country
          if(!$this->form->fields["company_number"]->is_valid_for($d["country"],$d["company_number"],$err)){
            $this->form->set_error("company_number",$err);
            return;
          }

          $user = User::CreateNewRecord($d);
          // ...
        }
      }
    }

It's possible to set up CompanyNumberField only to accept postal codes from one specific country. Re-validation is not necessary in this case.

    <?php
    // file: app/forms/users/create_new_form.php
    class CreateNewForm extends ApplicationForm {

      function set_up(){
        // ...
        $this->add_field("company_number", new CompanyNumberField([
          "label" => "Company registration number",
          "country" => "CZ"
        ]));
      }
    }

Error message for invalid company_number code or format hints can be specified

    <?php
    // file: app/forms/users/create_new_form.php
    class CreateNewForm extends ApplicationForm {

      function set_up(){
        // ...
        $this->add_field("company_number", new CompanyNumberField([
          "error_messages" => [
            "invalid" => _("Invalid company number"),
          ],
          "format_hints" => [
            "CZ" => _("Please use format NNNNNNNN (8 digits)"),
            "LT" => _("Please use format NNNNNNNNN (9 digits)"),
          ],
        ]));
      }
    }

Testing
-------

    composer update --dev
    cd test
    ../vendor/bin/run_unit_tests

License
-------

CompanyNumberField is free software distributed [under the terms of the MIT license](http://www.opensource.org/licenses/mit-license)

[//]: # ( vim: set ts=2 et: )

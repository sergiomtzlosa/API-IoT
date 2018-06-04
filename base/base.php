<?php

include('../utils/config.php');
include('../utils/utils.php');
include('../utils/http_codes.php');
include('../utils/http_methods.php');
include('../utils/messages.php');

date_default_timezone_set('Europe/Madrid');

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
//error_reporting(E_ALL | E_STRICT);

class BaseObject {

  protected function die_json($error_str, $http_code) {

    http_response_code($http_code);

    $err_obj = Utils::show_json_error($error_str, $http_code);

    die(json_encode($err_obj));
  }

  protected function check_field_is_empty($field, $desc_field) {

    if (Utils::is_empty($field)) {

      Utils::die_json("Field \"" . $desc_field . "\" is empty", HTTP_CODES::$HTTP_GENERIC_ERROR);
    }
  }

  protected function check_field_is_set($field, $desc_field) {

    if (!Utils::is_set($field)) {

      Utils::die_json("Field \"" . $desc_field . "\" is not set", HTTP_CODES::$HTTP_GENERIC_ERROR);
    }
  }

  protected function new_hash($item) {

    $salted = $item . Config::$SALT_WORD;

    return strval(hash('sha256', $salted));
  }
}

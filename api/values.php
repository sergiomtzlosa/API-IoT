<?php

include('../base/value_management.php');

$content_type = $_SERVER["CONTENT_TYPE"];
Utils::check_content_type($content_type);

$token = $_SERVER["HTTP_TOKEN"];
Utils::check_token($token);

$request_method = strtoupper($_SERVER['REQUEST_METHOD']);

if (Utils::check_rest_operation($request_method, HTTP_METHODS::$HTTP_POST)) {

    $content = trim(file_get_contents("php://input"));
    $decoded = json_decode($content, true);

    if (!is_array($decoded) or $decoded == NULL) {

        Utils::die_json('Received content contained invalid JSON', HTTP_CODES::$HTTP_GENERIC_ERROR);
    }

    $values_management = new ValueManagement();

    $values_management->insert_new_document($decoded, $token);

} else if (Utils::check_rest_operation($request_method, HTTP_METHODS::$HTTP_PUT)) {

  $content = trim(file_get_contents("php://input"));
  $decoded = json_decode($content, true);

  if (!is_array($decoded) or $decoded == NULL) {

      Utils::die_json('Received content contained invalid JSON', HTTP_CODES::$HTTP_GENERIC_ERROR);
  }

  $values_management = new ValueManagement();

  $row = $decoded["docs"];

  $date_from = $decoded["date_from"];

  if ($date_from == null) {

    $date_from = "";
  }

  $date_to = $decoded["date_to"];

  if ($date_to == null) {

    $date_to = "";
  }

  $values_management->find_documents($row, $date_from, $date_to, $token);

} else {

  $this->die_json(Messages::$INTERNAL_ERROR, HTTP_CODES::$HTTP_GENERIC_ERROR);
}

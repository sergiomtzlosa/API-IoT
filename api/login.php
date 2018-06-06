<?php

include('../base/login_management.php');

$content_type = $_SERVER["CONTENT_TYPE"];
Utils::check_content_type($content_type);

$request_method = strtoupper($_SERVER['REQUEST_METHOD']);

if (Utils::check_rest_operation($request_method, HTTP_METHODS::$HTTP_POST)) {

      $content = trim(file_get_contents("php://input"));
      $decoded = json_decode($content, true);

      if (!is_array($decoded) or $decoded == NULL) {

          Utils::die_json(Messages::$JSON_CONTENT_NOT_VALID, HTTP_CODES::$HTTP_GENERIC_ERROR);
      }

      $username = $decoded['username'];
      $password = $decoded['password'];

      $login_management = new LoginManagement();

      if (!$login_management->enabled_user($token)) {

          Utils::die_json(Messages::$USER_DISABLED, HTTP_CODES::$HTTP_GENERIC_ERROR);
      }

      $login_management->login_user($username, $password);

} else {

  $this->die_json(Messages::$INTERNAL_ERROR, HTTP_CODES::$HTTP_GENERIC_ERROR);
}

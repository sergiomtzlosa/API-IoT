<?php

include('../base/user_management.php');

$content_type = $_SERVER["CONTENT_TYPE"];
Utils::check_content_type($content_type);

$token = $_SERVER["HTTP_TOKEN"];
Utils::check_token($token);

$request_method = strtoupper($_SERVER['REQUEST_METHOD']);

if (Utils::check_rest_operation($request_method, HTTP_METHODS::$HTTP_POST) ||
    Utils::check_rest_operation($request_method, HTTP_METHODS::$HTTP_PUT)) {

      $content = trim(file_get_contents("php://input"));
      $decoded = json_decode($content, true);

      if (!is_array($decoded) or $decoded == NULL) {

          Utils::die_json('Received content contained invalid JSON', HTTP_CODES::$HTTP_GENERIC_ERROR);
      }

      $username = $decoded['username'];
      $password = $decoded['password'];
      $name = $decoded['name'];
      $surname = $decoded['surname'];
      $description = $decoded['description'];

      $user_management = new UserManagement();

      if (Utils::check_rest_operation($request_method, HTTP_METHODS::$HTTP_PUT)) {

        $user_id = $decoded['user_id'];

        $user_management->update_user($username, $password, $name, $surname, $description, $user_id);

      } else {

        $is_admin = $decoded['admin'];

        $user_management->insert_new_user($username, $password, $name, $surname, $description, $is_admin, $token);
      }

} else if (Utils::check_rest_operation($request_method, HTTP_METHODS::$HTTP_DELETE)) {

  $content = trim(file_get_contents("php://input"));
  $decoded = json_decode($content, true);

  if (!is_array($decoded) or $decoded == NULL) {

      Utils::die_json('Received content contained invalid JSON', HTTP_CODES::$HTTP_GENERIC_ERROR);
  }

  $user_id = $decoded['user_id'];

  $user_management = new UserManagement();

  $user_management->delete_user($user_id, $token);

} else if (Utils::check_rest_operation($request_method, HTTP_METHODS::$HTTP_GET)) {

  $user_id = $_GET['user_id'];
  
  $user_management = new UserManagement();

  $user_management->select_user($user_id, $token);

} else {

  $this->die_json(Messages::$INTERNAL_ERROR, HTTP_CODES::$HTTP_GENERIC_ERROR);
}

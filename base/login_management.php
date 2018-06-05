<?php

include_once('base.php');

class LoginManagement extends BaseObject {

  public function test_echo() {

    return "It works!!";
  }

  public function expired_token($token) {

    return $this->is_expired_token($token);
  }

  public function login_user($username, $password) {

    $this->check_field_is_set($username, "username");
    $this->check_field_is_set($password, "password");

    $this->check_field_is_empty($username, "username");
    $this->check_field_is_empty($password, "password");

    $db_connection = $this->open_db_connection();

    $hash_password = $this->new_hash($password);

    //call procedure login_user_actions
    $query = "CALL login_user_actions('" . $username. "', '" . $hash_password . "');";

    $result = mysqli_query($db_connection, $query);

    $row_final = mysqli_fetch_assoc($result);

    if (empty($row_final)) {

      Utils::die_json(Messages::$DATA_NOT_FOUND, HTTP_CODES::$HTTP_OK);

    } else {

      Utils::show_success_custom_json($row_final, HTTP_CODES::$HTTP_OK);
    }

    $this->close_db_connection($db_connection);
  }
}

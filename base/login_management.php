<?php

include_once('base.php');

class LoginManagement extends BaseObject {

  public function test_echo() {

    return "It works!!";
  }

  function open_db_connection() {

    $db_host = Config::$MARIADB_HOST;
    $db_user = Config::$MARIADB_USER;
    $db_password = Config::$MARIADB_PASSWORD;
    $db_used = Config::$MARIADB_DATABASE;

    // en /etc/php.ini habilitar extension=php_mysqli.dll
    $db_link = mysqli_connect($db_host, $db_user, $db_password, $db_used);

    //var_dump($db_link);

    if (mysqli_connect_errno()) {

      $this->die_json(Messages::$INTERNAL_ERROR, HTTP_CODES::$HTTP_GENERIC_ERROR);
    }

    $db_link->set_charset('latin1');

    mysqli_select_db($db_link, $db_used);

    return $db_link;
  }

  function close_db_connection($db_object) {

    if (!is_null($db_object)) {

      mysqli_close($db_object);
    }
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

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

    return strval(hash_hmac('sha256', $salted, false));
  }

  protected function open_db_connection() {

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

  protected function close_db_connection($db_object) {

    if (!is_null($db_object)) {

      mysqli_close($db_object);
    }
  }

  protected function is_expired_token($token) {

    $sql = "SELECT expired FROM sensors_tokens WHERE token = '" . $token . "' LIMIT 1";

    $db_connection = $this->open_db_connection();

    $result = $db_connection->query($sql);
    $rows = $result->num_rows;

    $expired = false;

    if ($rows > 0) {

        while($row = $result->fetch_assoc()) {

            $check_expired = strval($row["expired"]);

            if ($check_expired == "1") {

              $expired = true;
              break;
            }
        }
    }

    $this->close_db_connection($db_connection);

    return $expired;
  }
}

<?php

include('base.php');

class UserManagement extends BaseObject {

  public function test_echo() {

    return "It works!!";
  }

  public function expired_token($token) {

    return $this->is_expired_token($token);
  }

  public function insert_new_user($username, $password, $name, $surname, $description, $is_admin, $token) {

    $this->check_field_is_set($username, "username");
    $this->check_field_is_set($password, "password");
    $this->check_field_is_set($name, "name");
    $this->check_field_is_set($surname, "surname");
    $this->check_field_is_set($description, "description");

    $this->check_field_is_empty($username, "username");
    $this->check_field_is_empty($password, "password");
    $this->check_field_is_empty($name, "name");
    $this->check_field_is_empty($surname, "surname");
    $this->check_field_is_empty($description, "description");

    $db_connection = $this->open_db_connection();

    // Check if token is admin, only admin can insert
    if (!$this->is_token_admin($db_connection, $token)) {

      Utils::die_json(Messages::$CANNOT_INSERT, HTTP_CODES::$HTTP_GENERIC_ERROR);
    }

    // Check if username or password EXISTS
    if ($this->username_password_exists($db_connection, $username, $password)) {

      Utils::die_json(Messages::$USERNAME_PASSWORD_EXISTS, HTTP_CODES::$HTTP_GENERIC_ERROR);
    }

    $admin = false;

    if ($this->is_token_admin($db_connection, $token)) {

      if (!Utils::is_empty($is_admin)) {

        if (strval($is_admin) == "1") {

          $admin = true;
        }
      }
    }

    $temp_date = date('Y-m-d H:i:s');

    $hash_password = $this->new_hash($password);

    $query = "INSERT INTO sensors_users (username, password, name, surname, description, ts_last_update, is_admin) ";
    $query .= "VALUES ('$username', '$hash_password', '$name', '$surname', '$description', '$temp_date', '$admin')";

    $stmt = $db_connection->prepare($query);

    $stmt->execute();

    $num = mysqli_affected_rows($db_connection);

    $result_query = false;
    $new_row_id = '';
    $last_row_inserted = [];

    if ($num > 0) {

      $result_query = true;

      $new_row_id = $db_connection->insert_id;

      $last_row_inserted = $this->get_last_inserted_row($db_connection, Config::$MARIADB_TABLE, $new_row_id);
    }

    $stmt->close();

    $this->close_db_connection($db_connection);

    if ($result_query) {

      $object = [];
      $object["user_id"] = strval($new_row_id);

      if (count($last_row_inserted) > 0) {

        $object["token"] = $last_row_inserted["token"];
      }

      Utils::show_success_custom_json($object, HTTP_CODES::$HTTP_CREATED);

    } else {

      Utils::die_json(Messages::$CANNOT_INSERT, HTTP_CODES::$HTTP_GENERIC_ERROR);
    }
  }

  public function update_user($username, $password, $name, $surname, $description, $user_id) {

    $this->check_field_is_set($user_id, "user_id");
    $this->check_field_is_set($username, "username");
    $this->check_field_is_set($password, "password");
    $this->check_field_is_set($name, "name");
    $this->check_field_is_set($surname, "surname");
    $this->check_field_is_set($description, "description");

    $this->check_field_is_empty($user_id, "user_id");
    $this->check_field_is_empty($username, "username");
    $this->check_field_is_empty($password, "password");
    $this->check_field_is_empty($name, "name");
    $this->check_field_is_empty($surname, "surname");
    $this->check_field_is_empty($description, "description");

    $db_connection = $this->open_db_connection();

    $temp_date = date('Y-m-d H:i:s');
    $hash_password = $this->new_hash($password);

    $query = "UPDATE sensors_users AS s1 ";
    $query .= "SET s1.username = '" . $username . "', s1.password = '" . $hash_password . "', s1.name = '" . $name . "', s1.surname = '" . $surname . "', s1.description = '" . $description . "', s1.ts_last_update ='" . $temp_date . "' ";
    $query .= "WHERE  s1.user_id = " . intval($user_id);

    $result = mysqli_query($db_connection, $query);

    if ($result) {

      http_response_code(HTTP_CODES::$HTTP_UPDATED);
    } else {

      http_response_code(HTTP_CODES::$HTTP_GENERIC_ERROR);
    }

    $this->close_db_connection($db_connection);
  }

  public function delete_user($user_id, $token) {

    $this->check_field_is_set($user_id, "user_id");

    $this->check_field_is_empty($user_id, "user_id");

    $db_connection = $this->open_db_connection();

    // Check if token is admin, only admin can delete
    if (!$this->is_token_admin($db_connection, $token)) {

      Utils::die_json(Messages::$CANNOT_DELETE, HTTP_CODES::$HTTP_GENERIC_ERROR);
    }

    $query = "DELETE FROM sensors_users WHERE user_id = ". $user_id;

    $result = mysqli_query($db_connection, $query);

    if ($result) {

      http_response_code(HTTP_CODES::$HTTP_DELETED);
    } else {

      http_response_code(HTTP_CODES::$HTTP_GENERIC_ERROR);
    }

    $this->close_db_connection($db_connection);
  }

  public function select_user($user_id, $token) {

    $this->check_field_is_set($user_id, "user_id");

    $this->check_field_is_empty($user_id, "user_id");

    $db_connection = $this->open_db_connection();

    $is_admin = ($this->is_token_admin($db_connection, $token));

    $sql = "SELECT username, password, name, surname, description, token ";

    if ($is_admin) {

      $sql = "SELECT * ";
    }

    $sql .= "FROM sensors_users AS s1 INNER JOIN sensors_tokens AS s2 ";
    $sql .= "ON s1.user_id = s2.token_user_id WHERE s1.user_id = ". $user_id . " LIMIT 1";

    $result = $db_connection->query($sql);

    $row_final = [];

    if ($result->num_rows > 0) {

        while($row = $result->fetch_assoc()) {

            $row_final = $row;
            break;
        }
    }

    if (empty($row_final)) {

      Utils::die_json(Messages::$DATA_NOT_FOUND, HTTP_CODES::$HTTP_OK);

    } else {

      Utils::show_success_custom_json($row_final, HTTP_CODES::$HTTP_OK);
    }

    $this->close_db_connection($db_connection);
  }

  function is_token_admin($db_connection, $token) {

    $sql = "SELECT is_admin FROM sensors_users AS s1 INNER JOIN sensors_tokens AS s2 ON s1.user_id = s2.token_user_id WHERE s2.token = '" . $token . "' LIMIT 1";

    $result = $db_connection->query($sql);
    $rows = $result->num_rows;

    $return_admin = false;

    if ($rows > 0) {

        while($row = $result->fetch_assoc()) {

            $check_admin = strval($row["is_admin"]);

            if ($check_admin == "1") {

              $return_admin = true;
              break;
            }
        }
    }

    return false;
  }

  function username_password_exists($db_connection, $username, $password) {

    $hash_password = $this->new_hash($password);

    $sql = "SELECT username, password FROM sensors_users WHERE username LIKE '%" . $username . "%' OR password LIKE '%" . $hash_password . "%' LIMIT 1";

    $result = $db_connection->query($sql);
    $rows = $result->num_rows;

    if ($rows > 0) {

        return true;
    }

    return false;
  }

  function get_last_inserted_row($db_connection, $table, $row_id) {

    $sql = "SELECT user_id, username, password, name, surname, description, token FROM sensors_users s1 INNER JOIN sensors_tokens s2 ";
    $sql .= "ON s1.user_id = s2.token_user_id WHERE s1.user_id =" . $row_id . " LIMIT 1";

    $result = $db_connection->query($sql);
    $rows = $result->num_rows;

    $full_row = [];

    if ($rows > 0) {

        while($row = $result->fetch_assoc()) {

            $full_row["user_id"] = strval($row["user_id"]);
            $full_row["username"] = $row["username"];
            $full_row["password"] = $row["password"];
            $full_row["name"] = $row["name"];
            $full_row["surname"] = $row["surname"];
            $full_row["description"] = $row["description"];
            $full_row["token"] = $row["token"];
        }
    }

    return $full_row;
  }
}

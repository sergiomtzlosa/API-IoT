<?php

include_once('base.php');

class ValueManagement extends BaseObject {

  public function test_echo() {

    return "It works!!";
  }

  public function expired_token($token) {

    return $this->is_expired_token($token);
  }

  public function enabled_user($token) {

    return $this->is_user_enabled($token);
  }

  public function open_db_mongo_connection() {

    $db_host = Config::$MONGODB_HOST;
    $db_port = Config::$MONGODB_PORT;
    $db_user = Config::$MONGODB_USER;
    $db_password = Config::$MONGODB_PASSWORD;

    // https://stackoverflow.com/questions/34486808/installing-the-php-7-mongodb-client-driver
    // Install pecl and set extension in /etc/php.ini
    // sudo pecl install mongodb
    // extension="/usr/local/php5-7.1.13-20180201-134129/lib/php/extensions/no-debug-non-zts-20160303/mongodb.so"
    $connection_string = "mongodb://" . $db_user . ":" . $db_password . "@" . $db_host . ":" . $db_port;

    $db_link = new MongoDB\Driver\Manager($connection_string);

    if (!$db_link) {

        die('Cannot connect to MongoDB');
    }

    return $db_link;
  }

  public function close_db_mongo_connection($db_object) {

    if (!is_null($db_object)) {

      //$db_object->close();
    }
  }

  public function insert_new_document($document, $token) {

    $bulk = new MongoDB\Driver\BulkWrite;
    $bulk->insert($document);

    $manager = $this->open_db_mongo_connection();

    $db_used = Config::$MONGODB_DATABASE;
    $collection_used = Config::$MONGODB_COLLECTION;

    try {

      $result = $manager->executeBulkWrite($db_used . '.' . $collection_used, $bulk);

      $inserted = $result->getInsertedCount();

      if ($inserted > 0) {

        $row_final = [];
        $row_final["docs_inserted"] = strval($inserted);

        Utils::show_success_custom_json($row_final, HTTP_CODES::$HTTP_OK);

      } else {

        Utils::die_json(Messages::$CANNOT_INSERT, HTTP_CODES::$HTTP_GENERIC_ERROR);
      }
    } catch (MongoDB\Driver\Exception\BulkWriteException $e) {

        Utils::die_json(Messages::$DATA_NOT_FOUND, HTTP_CODES::$HTTP_OK);
    }
  }

  public function find_documents($rows, $date_from, $date_to, $token) {

    $this->check_field_is_set($rows, "rows");

    $this->check_field_is_empty($rows, "rows");

    $manager = $this->open_db_mongo_connection();

    $db_used = Config::$MONGODB_DATABASE;
    $collection_used = Config::$MONGODB_COLLECTION;

    $value_rows = intval($rows);

    if ($value_rows == 0) {

      $value_rows = 10;
    }

    $options = ['limit' => $value_rows];

    $filter = [];

    if (($date_from != "") && ($date_to != "")) {

      $filter = ['timestamp' => ['&gte' => $date_from, '&lte' => $date_to]];
    }

    $query = new MongoDB\Driver\Query($filter, $options);
    $cursor = $manager->executeQuery($db_used . '.' . $collection_used, $query);
    $array_results = $cursor->toArray();

    if (count($array_results) > 0) {

      Utils::show_success_custom_json($array_results, HTTP_CODES::$HTTP_OK);

    } else {

      Utils::die_json(Messages::$DATA_NOT_FOUND, HTTP_CODES::$HTTP_OK);
    }
  }
}

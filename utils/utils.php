<?php

class Utils {

  public static function is_set($variable) {

    if (!isset($variable)) {

        return false;
    }

    return true;
  }

  public static function is_empty($variable) {

    if (!empty($variable)) {

      return false;
    }

    return true;
  }

  public static function show_json_error($errorString, $response_code) {

    $error = [];
    $error["code"] = strval($response_code);
    $error["message"] = $errorString;

    return $error;
  }

  public static function check_rest_operation($request_method, $operation) {

    return (strcasecmp($request_method, $operation) == 0);
  }

  public static function check_content_type($content_type) {

    $contentType = isset($content_type) ? trim($content_type) : '';

    if (strcasecmp(strtolower($contentType), 'application/json') != 0) {

        Utils::die_json(Messages::$CONTENT_TYPE_NOT_VALID, HTTP_CODE::$HTTP_GENERIC_ERROR);
    }
  }

  public static function check_token($token) {

    if (!Utils::is_set($token)) {

      Utils::die_json(Messages::$TOKEN_NOT_SET, HTTP_CODES::$HTTP_GENERIC_ERROR);
    }

    if (Utils::is_empty($token)) {

      Utils::die_json(Messages::$TOKEN_EMPTY, HTTP_CODES::$HTTP_GENERIC_ERROR);
    }
  }

  public static function die_json($error_str, $http_code) {

    http_response_code($http_code);

    $err_obj = Utils::show_json_error($error_str, $http_code);

    die(json_encode($err_obj));
  }

  public static function show_success_json($error_str, $http_code) {

    http_response_code($http_code);

    $err_obj = Utils::show_json_error($error_str, $http_code);

    echo json_encode($err_obj);
  }

  public static function show_success_custom_json($obj_array, $http_code) {

    http_response_code($http_code);

    echo json_encode($obj_array);
  }
}

<?php

class Config {

  public static $MONGODB_HOST = 'mongo';
  public static $MONGODB_PORT = '27017';
  public static $MONGODB_USER = 'root';
  public static $MONGODB_PASSWORD = 'root';
  public static $MONGODB_DATABASE = 'sensors';
  public static $MONGODB_COLLECTION = 'sensors_values';

  public static $MARIADB_HOST = 'database';
  public static $MARIADB_USER = 'root';
  public static $MARIADB_PASSWORD = 'root';
  public static $MARIADB_DATABASE = 'sensors';
  public static $MARIADB_TABLE = 'sensors_users';

  public static $SALT_WORD = "785f4ee2dac1463030e7ec1795f05c41";
}

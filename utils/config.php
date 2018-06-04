<?php

class Config {

  public static $MONGODB_HOST = 'YOUR_MONGODB_IP';
  public static $MONGODB_PORT = 'YOUR_MONGODB_PORT';
  public static $MONGODB_USER = 'YOUR_MONGODB_USER';
  public static $MONGODB_PASSWORD = 'YOUR_MONGODB_PASSWROD';
  public static $MONGODB_DATABASE = 'sensors';
  public static $MONGODB_COLLECTION = 'sensors_values';

  public static $MARIADB_HOST = 'YOUR_MYSQL_IP';
  public static $MARIADB_USER = 'YOUR_MYSQL_USER';
  public static $MARIADB_PASSWORD = 'YOUR_MYSQL_PASSWROD';
  public static $MARIADB_DATABASE = 'sensors';
  public static $MARIADB_TABLE = 'sensors_users';

  public static $SALT_WORD = "785f4ee2dac1463030e7ec1795f05c41";
}

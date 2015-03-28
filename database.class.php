<?php

  /**
  *
  * This database class has a clean and common methods that works with various
  * types of database (such as: msSQL, mySQL, mySQLi and postgres) and PDO. It
  * has cache system integrated along side making it very effective and powerful
  * database class..
  *
  * @version:       2.9.14
  * @package        Stvdi
  * @author:        Jencube Team
  * @license:       http://opensource.org/licenses/gpl-license.php 
  *                 GNU General Public License (GPL)
  * @copyright:     Copyright (c) 2013 - 2015 Jencube
  * @twitter:       @deusex0 & @One_Oracle
  * @filesource     includes/libraries/database.class.php
  *
  **/


  class Database {

    /**
    *
    * Database Type
    *
    * @access protected
    * @var string
    *
    **/
    protected $dbType;

    /**
    *
    * Database Host
    *
    * @access protected
    * @var string
    *
    **/
    protected $dbHost;

    /**
    *
    * Database Name
    *
    * @access protected
    * @var string
    *
    **/
    protected $dbName;

    /**
    *
    * Database User
    *
    * @access protected
    * @var string
    *
    **/
    protected $dbUsername;

    /**
    *
    * Database Password
    *
    * @access protected
    * @var string
    *
    **/
    protected $dbPassword;

    /**
    *
    * Database Port
    *
    * @access protected
    * @var integer
    *
    **/
    protected $dbPort = 0;

    /**
    *
    * To make a remote connection
    *
    * @access private
    * @var bool
    *
    **/
    private $remote = FALSE;

    /**
    *
    * Database table columns charset
    *
    * @access public
    * @var string
    *
    **/
    var $dbCharset = 'utf8';

    /**
    *
    * Database table columns collate
    *
    * @access public
    * @var string
    *
    **/
    var $dbCollate = 'utf8_general_ci';

    /**
    *
    * To show database errors
    *
    * @access private
    * @var bool
    *
    **/
    private $displayError = FALSE;

    /**
    *
    * SQL query
    *
    * @access private
    * @var string
    *
    **/
    private $sqlQuery = NULL;

    /**
    *
    * Query results
    *
    * @access protected
    * @var mixed
    *
    **/
    protected $query = 0;

    /**
    *
    * Query fetch data
    *
    * @access protected
    * @var mixed
    *
    **/
    protected $queryResult = 0;

    /**
    *
    * Query time
    *
    * @access public
    * @var integer
    *
    **/
    public $queryTime = 0;

    /**
    *
    * Query count
    *
    * @access private
    * @var integer
    *
    **/
    var $numQueries = 0;

    /**
    *
    * Previous query row count
    *
    * @access public
    * @var integer
    *
    **/
    var $numRows = 0;

    /**
    *
    * PNumber of column in query result
    *
    * @access public
    * @var integer
    *
    **/
    var $numFields = 0;

    /**
    *
    * Count of affected rows by previous query
    *
    * @access public
    * @var integer
    *
    **/
    public $affectedRows = 0;

    /**
    *
    * AUTO_INCREMENT generated ID by the previous query
    *
    * @access public
    * @var integer
    *
    **/
    var $insertID;

    /**
    *
    * Database connection link
    *
    * @access protected
    * @var integer
    *
    **/
    protected $conn;

    /**
    *
    * Use PHP Data Objects
    *
    * @access private
    * @var bool
    *
    **/
    private $PDO = FALSE;

    /**
    *
    * The last error during query
    *
    * @access protected
    * @var array
    *
    **/
    protected $errorMsg = array();

    /**
    *
    * To suppress/show errors
    *
    * @access protected
    * @var bool
    *
    **/
    protected $suppressErrors = FALSE;

    /**
    *
    * Database activities log
    *
    * @access public
    * @var string
    *
    **/
    var $logData = '';

    /**
    *
    * Log data directory
    *
    * @access private
    * @var string
    *
    **/
    private $logDir;

    /**
    *
    * Cache results
    *
    * @access private
    * @var bool
    *
    **/
    private $cache = TRUE;

    /**
    *
    * Cache encode
    *
    * @access private
    * @var string
    *
    **/
    private $cacheEncode = 'jsone';

    /**
    *
    * Cache decode
    *
    * @access private
    * @var string
    *
    **/
    private $cacheDecode = 'jsond';

    /**
    *
    * Cache filename & directory
    *
    * @access private
    * @var string
    *
    **/
    private $cacheFile;

    /**
    *
    * Cache file directory
    *
    * @access private
    * @var string
    *
    **/
    private $cacheDir;

    /**
    *
    * Cache name
    *
    * @access public
    * @var string
    *
    **/
    var $cacheName = NULL;

    /**
    *
    * Cache Unique ID
    *
    * @access private
    * @var string
    *
    **/
    private $cacheID = NULL;

    /**
    *
    * Cache age, the period of time cache is allow
    * to exists. Default is 5min
    *
    * @access public
    * @var integer
    *
    **/
    var $cacheAge = 300;

    /**
    *
    * Cache file last modified time.
    *
    * @access private
    * @var integer
    *
    **/
    private $cacheMod;

    /**
    *
    * debugging
    *
    * @access private
    * @var bool
    *
    **/
    private $debug = TRUE;

    /**
    *
    * debugging
    *
    * @access public
    * @var array
    *
    **/
    var $querydebug = array();

    /**
    *
    * debugging start time
    *
    * @access public
    * @var integer
    *
    **/
    var $startTime = 0;

    /**
    *
    * Class constructor initialization to set the class
    * properties and connection to the database
    *
    * @access public
    * @param array
    *
    **/
    public function __construct( $config = array() ) {
      $this->logData = $this->log_string( "Database() Initialized" );
      $this->dbType = ( isset( $config['db_type'] ) ) ? $config['db_type'] : 'mysqli';
      $this->dbHost = ( isset( $config['db_host'] ) ) ? $config['db_host'] : 'localhost';
      $this->dbName = ( isset( $config['db_name'] ) ) ? $config['db_name'] : '';
      $this->dbUsername = ( isset( $config['db_user'] ) ) ? $config['db_user'] : 'root';
      $this->dbPassword = ( isset( $config['db_pass'] ) ) ? $config['db_pass'] : '';
      $this->dbCharset = ( isset( $config['db_charset'] ) ) ? $config['db_charset'] : $this->dbCharset;
      $this->dbCollate = ( isset( $config['db_collation'] ) ) ? $config['db_collation'] : $this->dbCollate;
      $this->remote = ( isset( $config['remote'] ) ) ? $config['remote'] : $this->remote;
      $this->dbPort = ( isset( $config['db_port'] ) ) ? $config['db_port'] : '3360';
      $this->PDO = ( isset( $config['PDO'] ) ) ? $config['PDO'] : $this->PDO;
      $this->cache = ( isset( $config['cache'] ) ) ? $config['cache'] : $this->cache;
      $this->cacheDir = ( isset( $config['cache_path'] ) ) ? $config['cache_path'] : $this->cacheDir;
      $this->cacheEncode = ( isset( $config['cache_encode'] ) ) ? $config['cache_encode'] : $this->cacheEncode;
      $this->cacheDecode = ( isset( $config['cache_decode'] ) ) ? $config['cache_decode'] : $this->cacheDecode;
      $this->debug = ( isset( $config['debug'] ) ) ? $config['debug'] : $this->debug;

      if ( ! $this->is_connected() ) {
        $this->connect();
      }
    }

    /**
    *
    * Connecting and selecting database
    *
    * @access public
    *
    **/
    public function connect() {
      $this->logData .= $this->log_string( "connect() called" );

      if ( ! $this->PDO ) {

        switch ( $this->dbType ) {
          case 'mysql':
            if ( !$this->remote ) {
              $this->conn = @mysql_connect( $this->dbHost, $this->dbUsername, $this->dbPassword );
            } else {
              $this->conn = @mysql_connect( $this->dbHost . ':' . $this->port, $this->dbUsername, $this->dbPassword );
            }

            if ( !$this->conn ) {
              $this->logData .= $this->log_string( "mysql_connect() failed", "error" );
            }
            break;
          case 'mysqli':
            if ( !$this->remote ) {
              $this->conn = @mysqli_connect( $this->dbHost, $this->dbUsername, $this->dbPassword );
            } else {
              $this->conn = @mysqli_connect( $this->dbHost . ':' . $this->port, $this->dbUsername, $this->dbPassword );
            }

            if ( !$this->conn ) {
              $this->logData .= $this->log_string( "mysqli_connect() failed", "error" );
            }
            break;
          case 'mssql':
            if ( !$this->remote ) {
              $this->conn = @mssql_connect( $this->dbHost, $this->dbUsername, $this->dbPassword );
            } else {
              $this->conn = @mssql_connect( $this->dbHost . ':' . $this->port, $this->dbUsername, $this->dbPassword );
            }

            if ( !$this->conn ) {
              $this->logData .= $this->log_string( "mssql_connect() failed", "error" );
            }
            break;
          case 'postgres':
            if ( !$this->remote ) {
              $this->conn = @pg_connect("host=$this->dbHost dbname=$this->dbName user=$this->dbUsername password=$this->dbPassword");
            } else {
              $this->conn = @pg_connect("host=$this->dbHost port=$this->port dbname=$this->dbName user=$this->dbUsername password=$this->dbPassword");
            }

            if ( ! $this->conn ) {
              $this->logData .= $this->log_string( "pg_connect() failed", "error" );
            }
            break;
        }

        if ( ! $this->conn ) {
          $this->logData .= $this->log_string( "Unable to connect to database: " . $this->sql_error(), "error" );
          $this->errorMsg[] = "Unable to connect to database: <em>" . $this->sql_error() . "</em>";
          return FALSE;
        }

        if ( ! empty( $this->dbName ) ) {
          $this->logData .= $this->log_string( "Database name not supplied.", "error" );
          $this->errorMsg[] = "Please provide a database name.";
          return FALSE;
        }

        $this->select_db( $this->dbName, $this->conn );

      } else {

        try {

          if ( ! $this->remote ) {
            $this->conn = new PDO( "{$this->dbType}:dbname={$this->dbName};host={$this->dbHost};" . $this->dbCharset, $this->dbUsername, $this->dbPassword );
          } else {
            $this->conn = new PDO( "{$this->dbType}:dbname={$this->dbName};host={$this->dbHost};port={$this->port};" . $this->dbCharset, $this->dbUsername, $this->dbPassword );
          }
          $this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);

        } catch( PDOExpection $e ) {
          $this->logData .= $this->log_string( "PDO() failed", "error" );
          $this->logData .= $this->log_string( "Unable to connect to database: " . $e->getMessage(), "error" );
          $this->errorMsg[] = "Unable to connect to database: <em>" . $e->getMessage() . "</em>";
          return FALSE;
        }

      }
      return TRUE;

    }

    /**
    *
    * Close/kill the database connection and query results
    *
    * @access public
    * @return bool
    *
    **/
    public function disconnect( $stmt = NULL ) {
      $this->logData .= $this->log_string( "disconnect() called" );

      if ( !$this->is_connected() ) {
        $this->logData .= $this->log_string( "disconnect(): Database connection does not exist", "error" );
        $this->errorMsg[] = "Database connection does not exist";
        return FALSE;
      }

      if ( !$this->PDO ) {

        switch( $this->dbType ) {
          case "mysql":
            return @mysql_close();
          break;
          case "mysqli":
            return @mysqli_close( $this->conn );
          break;
          case "mssql":
            return @mssql_close();
          break;
          case "postgres":
            return @pg_close( $this->conn );
          break;
        }

        if ( is_resource( $this->query ) )
          $this->free_memory( $this->query );

      } else {

        return ( is_null( $stmt ) ) ? $this->conn->closeCursor() : $stmt->closeCursor();

      }

      $this->conn = NULL;
      $this->query = NULL;
      $this->stmt = NULL;
      $this->affectedRows = 0;
      $this->numFields = 0;
      $this->numRows = 0;
      $this->errorMsg = NULL;
      $this->numQueries = 0;
    }

    /**
    *
    * Free result memory
    *
    * @access private
    * @return bool
    * @param resource $result -> result identifier
    *
    **/
    private function free_memory( $result = NULL ) {
      $this->logData .= $this->log_string( "free_memory() called" );

      if ( is_null( $result ) ) {
        $result = $this->query;
      }

      switch( $this->dbType ){
        case "mysql":
          return @mysql_free_result( $result );
        break;
        case "mysqli":
          return @mysqli_free_result( $result );
        break;
        case "mssql":
          return @mssql_free_result( $result );
        break;
        case "postgres":
          return @pg_free_result( $result );
        break;
      }
    }

    /**
    *
    * Return database error message
    *
    * @access private
    * @return string
    *
    **/
    private function sql_error( $stmt = NULL ) {
      $errormsg = "";

      if ( ! $this->PDO ) {
        switch($this->dbType){
          case "mysql":
            $errormsg = @mysql_error();
          break;
          case "mysqli":
            $errormsg = @mysqli_error( $this->conn );
          break;
          case "mssql":
            $errormsg = @mssql_get_last_message();
          break;
          case "postgres":
            $errormsg = @pg_last_error( $this->conn );
          break;
        }
      } else {
        $error = ( is_null( $stmt ) ) ? $this->conn->errorInfo() : $stmt->errorInfo();
        $errormsg = $error[2];
      }

      return $errormsg;
    }

    /**
    *
    * Returns the numerical value of the error message
    * from previous MySQL operation
    *
    * @access private
    * @return integer
    *
    **/
    private function sql_errno( $stmt = NULL ) {
      $errorno = "";

      if ( ! $this->PDO ) {
        switch( $this->dbType ) {
          case "mysql":
            $errorno = @mysql_errno( $this->conn ) ;
          break;
          case "mysqli":
            $errorno = @mysqli_errno( $this->conn );
          break;
        }

      } else {
        $errorno = ( is_null( $stmt ) ) ? $this->conn->errorCode() : $stmt->errorCode();
      }
      return $errorno;
    }

    /**
    *
    * Select database
    *
    * @access public
    * @return bool
    * @param string $db -> Database name
    * @param resource $conn -> link indentifier
    *
    **/
    public function select_db( $db = NULL, $conn = NULL ) {
      $this->logData .= $this->log_string( "select_db() called" );

      if ( ! is_null( $db ) )
        $this->dbName = $db;

      if ( ! is_null( $conn ) )
        $this->conn = $conn;

      switch( $this->dbType ) {
        case "mysql":
          if ( !@mysql_select_db( $this->dbName, $this->conn ) ) {
            $this->logData .= $this->log_string( "Could not select database named " . $db, "error" );
            $this->logData .= $this->log_string( $this->sql_error(), "error" );
            $this->errorMsg[] = "Could not select database: <em>" . $this->sql_error() . "</em>";
            return FALSE;
          }
        break;
        case "mysqli":
          if ( !@mysqli_select_db( $this->conn, $this->dbName ) ) {
            $this->logData .= $this->log_string( "Could not select database named " . $db, "error" );
            $this->logData .= $this->log_string( $this->sql_error(), "error" );
            $this->errorMsg[] = "Could not select database: <em>" . $this->sql_error() . "</em>";
            return FALSE;
          }
        break;
        case "mssql":
          if ( !@mssql_select_db( $this->dbName, $this->conn ) ) {
            $this->logData .= $this->log_string( "Could not select database named " . $db, "error" );
            $this->logData .= $this->log_string( $this->sql_error(), "error" );
            $this->errorMsg[] = "Could not select database: <em>" . $this->sql_error() . "</em>";
            return FALSE;
          }
        break;
      }

      $this->set_charset( $this->conn );
      return TRUE;

    }

    /**
    *
    * Check if there is a database connection
    *
    * @access public
    * @return bool
    *
    **/
    public function is_connected() {
      return ( $this->conn ) ? TRUE : FALSE;
    }

    /**
    *
    * Perform a database query
    *
    * @access public
    * @return bool|Integer
    * @param string $SQL -> Database query
    *
    **/
    public function query( $SQL = NULL ) {
      $this->logData .= $this->log_string( "query() called" );
      $this->logData .= $this->log_string( $SQL, "query" );

      if ( ! $this->is_connected() ) {
        $this->logData .= $this->log_string( "No database connection made.", "error" );
        $this->errorMsg[] = "No database connection made.";
        return FALSE;

      }

      $this->sqlQuery = ( ! is_null( $SQL ) ) ? $SQL : $this->sqlQuery;

      if ( ! $this->PDO ) {

        switch ( $this->dbType ) {
          case "mysql":
            $this->query = @mysql_query( $this->sqlQuery, $this->conn );
          break;
          case "mysqli":
            $this->query = @mysqli_query( $this->conn, $this->sqlQuery );
          break;
          case "mssql":
            $this->query = @mssql_query( $this->sqlQuery, $this->conn );
          break;
          case "postgres":
            $this->query = @pg_query( $this->conn, $this->sqlQuery );
          break;
        }

      } else {

        $this->query = $this->conn->query( $this->sqlQuery );

      }

      $this->numQueries++;
      $this->num_rows();
      unset ( $SQL );

      if ( ! $this->query ) {

        $sqlError = $this->sql_error();
        $this->logData .= $this->log_string( "Query execution failed.", "error" );
        $this->logData .= $this->log_string( "Could not run query: " . $sqlError, "error" );
        $this->errorMsg[] = "Could not run query: <em>" . $sqlError . "</em>";
        return FALSE;

      } else {

        return $this->query;

      }

    }

    /**
    *
    * Return random result
    * Contributed by akinas.com => mysql_random_row
    *
    * @access public
    * @return string
    * @param array $data -> Query details
    * @usage rand_query(
    *           array(
    *                 tablename => 'NOT NULL',
    *                 fieldid => 'id',
    *                 fields => '*',
    *                 limit => '0, 30',
    *                 where => '`name` = 'james' AND `privilege` = 'user'',
    *                 type => '0'
    *           )
    * )
    *
    **/
    public function rand_query( $data ) {
      $this->logData .= $this->log_string( "rand_query() called" );
      $this->logData .= $this->log_string( print_r( $data ), "query" );

      if ( ! $this->is_connected() ) {

        $this->logData .= $this->log_string( "No database connection made.", "error" );
        $this->errorMsg[] = "No database connection made.";
        return FALSE;

      }

      switch ( $data['type'] ) {
        case '0':
          // 4th Slowest 100% of time to execute
          $where = ( isset( $data['where'] ) && !empty( $data['where'] ) )? "WHERE " . $data['where'] : "";
          $randQuery = $this->query("
            SELECT " . $data['fields'] . "
            FROM `" . $data['tablename'] . "`
            " . $where . "
            ORDER BY RAND()
            LIMIT " . $data['limit'] . "
            ");
          break;
        case '1':
          // 3rd - 79% of time to execute
          // Continuous change, no repetition
          $where = ( isset( $data['where'] ) && !empty( $data['where'] ) )? "WHERE " . $data['where'] : "";
          $andWhere = ( !empty( $data['where'] ) )? "AND " . $where : "";
          $queryRange = $this->query("
            SELECT MAX( `" . $data['fieldid'] . "` ) AS maxid, MIN( `" . $data['fieldid'] . "` ) AS minid
            FROM `" . $data['tablename'] . "`
            " . $where . "
            ");
          $fetchRange = $this->fetch_object( $queryRange );
          $randID = mt_rand( $fetchRange->minid, $fetchRange->maxid );
          $randQuery = $this->query("
            SELECT " . $data['fields'] . "
            FROM `" . $data['tablename'] . "`
            WHERE `" . $data['fieldid'] . "` >= " . $randID . " " . $andWhere . "
            LIMIT " . $data['limit'] . "
            ");
          break;
        case '2':
          // 2nd - 16% of time to execute
          // Repetition
          $where = ( isset( $data['where'] ) && !empty( $data['where'] ) )? "WHERE " . $where : "";
          $andWhere = ( !empty( $data['where'] ) )? "AND " . $where : "";
          $randQuery = $this->query("
            SELECT " . $data['fields'] . "
            FROM `" . $data['tablename'] . "`
            WHERE `" . $data['fieldid'] . "` >= ( SELECT FLOOR( MAX( " . $data['fieldid'] . " ) * RAND() )
              FROM `" . $data['tablename'] . "` " . $where . " )
            " . $andWhere . "
            ORDER BY " . $data['fieldid'] . "
            LIMIT " . $data['limit'] . "
            ");
          break;
        case '3':
          // 1st - Fastest; 13% of time to execute
          // COntinuous change, no repetition
          $where = ( !empty( $data['where'] ) )? "WHERE " . $data['where'] : "";
          $offsetResult = $this->query("
            SELECT FLOOR( RAND() * COUNT(*) ) AS offset
            FROM `" . $data['tablename'] . "`
            " . $where . "
            ");
          $offsetFetch = $this->fetch_object( $offsetResult );
          $offset = $offsetFetch->offset;
          $randQuery = $this->query("
            SELECT " . $data['fields'] . "
            FROM `" . $data['tablename'] . "`
            " . $where . "
            LIMIT " . $offset . ', ' . $data['limit'] . "
            ");
          break;
      }
      return $randQuery;
    }

    /**
    *
    * Fetch options
    *
    * @access private
    * @return mixed
    * @param string $type -> Fetch type ie: array|object|row|assoc
    *
    **/
    private function fetch_option( $type = 'object' ) {
      $this->logData .= $this->log_string( "fetch_option() called" );

      switch( $type ) {
        case "object":
          return $this->fetch_object();
        break;
        case "array":
          return $this->fetch_array();
        break;
        case "row":
          return $this->fetch_row();
        break;
        case "assoc":
          return $this->fetch_assoc();
        break;
      }

      return FALSE;
    }

    /**
    *
    * Perform a database query
    *
    * @access public
    * @return mixed
    * @param string $SQL -> Database SQL query
    *
    **/
    public function fetch( $SQL = NULL, $type = 'object' ) {
      $this->logData .= $this->log_string( "fetch() called" );

      if ( ! is_null ( $SQL ) )
        $this->sqlQuery = $SQL;

      if ( $this->cache == TRUE && $this->verify_cache() ) {
        return $this->get_cache();
      }

      return $this->fetch_option( $type );

    }

    /**
    *
    * Perform a database query
    *
    * @access public
    * @return mixed
    * @param string $SQL -> Database SQL query
    *
    **/
    public function fetch_all( $SQL = NULL ) {
      $this->logData .= $this->log_string( "fetch_all() called" );

      if ( ! is_null( $SQL ) )
        $this->sqlQuery = $SQL;

      $this->logData .= $this->log_string( $this->sqlQuery, "query" );

      $cacheName = ( is_null( $this->cacheName ) ) ? md5( $this->cacheID . $this->sqlQuery ) : md5( $this->cacheName . '-' . $this->cacheID . $this->sqlQuery );
      $this->cacheFile = $this->cacheDir . $cacheName . '.cache';

      $data = array();

      if ( $this->cache == TRUE && $this->verify_cache() ) {

        return $this->get_cache();

      } else {

        $this->start_timer();

        if ( ! $this->PDO ) {

          while ( $obj = $this->fetch_object() ) {
            $data[] = $obj;
          }
          $this->disconnect();

        } else {

          $this->query = $this->conn->query( $this->sqlQuery );

          if ( ! $this->query ) {
            $sqlError = $this->sql_error();
            $this->logData .= $this->log_string( "Query execution failed.", "error" );
            $this->logData .= $this->log_string( "Could not run query: " . $sqlError, "error" );
            $this->errorMsg[] = "Could not run query: <em>" . $sqlError . "</em>";
            return FALSE;
          }

          $data = $this->query->fetchAll( PDO::FETCH_OBJ );
          $this->disconnect( $this->query );
        }

        $this->queryResult = $data;
        $this->num_rows();
        $timeTaken = $this->stop_timer();

        // Cache
        if ( $this->cache ) {
          $this->set_cache();
        }

        // debug
        if ( $this->debug ) {
          $this->debug_data( $timeTaken, $this->sqlQuery );
        }

      }

      unset( $SQL );
      return $this->queryResult;

    }

    /**
    *
    * Get column information from a result and return as an object
    *
    * @access public
    * @return mixed
    * @param string $SQL -> Database SQL query
    * @param integer $colNumber -> Column Number
    *
    **/
    public function fetch_field( $SQL = NULL, $colNumber = 0 ) {
      $this->logData .= $this->log_string( "fetch_field() called" );

      if ( ! is_null( $SQL ) )
        $this->sqlQuery = $SQL;

      if ( ! ctype_digit( strval( $colNumber ) ) ) {
        $this->logData .= $this->log_string( "Invalid column number.", "error" );
        $this->errorMsg[] = "Invalid column number";
        return FALSE;
      }

      $this->logData .= $this->log_string( $this->sqlQuery, "query" );

      $cacheName = ( is_null( $this->cacheName ) ) ? md5( $this->cacheID . $this->sqlQuery ) : md5( $this->cacheName . '-' . $this->cacheID . $this->sqlQuery );
      $this->cacheFile = $this->cacheDir . $cacheName . '.cache';

      if ( $this->cache == TRUE && $this->verify_cache() ) {

        return $this->get_cache();

      } else {

        $this->start_timer();

        if ( $row = $this->fetch_row() ) {
          $data = $row[$colNumber];
        }

        $this->queryResult = $data;
        $timeTaken = $this->stop_timer();

        // Cache
        if ( $this->cache ) {
          $this->set_cache();
        }

        // debug
        if ( $this->debug ) {
          $this->debug_data( $timeTaken, $this->sqlQuery );
        }

      }

      unset( $SQL, $colNumber );
      return $this->queryResult;
    }

    /**
    *
    * Fetch a result row as an associative array
    *
    * @access public
    * @return bool|array
    * @param string $SQL -> Database SQL query
    *
    **/
    public function fetch_assoc( $SQL = NULL ) {
      $this->logData .= $this->log_string( "fetch_assoc() called" );

      if ( ! is_null( $SQL ) )
        $this->sqlQuery = $SQL;

      $this->logData .= $this->log_string( $this->sqlQuery, "error" );
      $this->queryResult = NULL;

      $cacheName = ( is_null( $this->cacheName ) ) ? md5( $this->cacheID . $this->sqlQuery ) : md5( $this->cacheName . '-' . $this->cacheID . $this->sqlQuery );
      $this->cacheFile = $this->cacheDir . $cacheName . '.cache';

      if ( $this->cache == TRUE && $this->verify_cache() ) {

        return $this->get_cache();

      } else {

        $this->start_timer();

        if ( ! $this->PDO ) {

          switch( $this->dbType ) {
            case "mysql":
              $this->queryResult = @mysql_fetch_assoc( $this->query );
            break;
            case "mysqli":
              $this->queryResult = @mysqli_fetch_assoc( $this->query );
            break;
            case "mssql":
              $this->queryResult = @mssql_fetch_assoc( $this->query );
            break;
            case "postgres":
              $this->queryResult = @pg_fetch_assoc( $this->query );
            break;
          }
          $this->disconnect();

        } else {

          $this->query = $this->conn->query( $this->sqlQuery );

          if ( ! $this->query ) {
            $sqlError = $this->sql_error();
            $this->logData .= $this->log_string( "Query execution failed.", "error" );
            $this->logData .= $this->log_string( "Could not run query: " . $sqlError, "error" );
            $this->errorMsg[] = "Could not run query: <em>" . $sqlError . "</em>";
            return FALSE;
          }

          $this->queryResult = $this->query->fetch( PDO::FETCH_ASSOC );
          $this->disconnect( $this->query );

        }

        $this->num_fields();
        $timeTaken = $this->stop_timer();

        // Cache
        if ( $this->cache ) {
          $this->set_cache();
        }

        // debug
        if ( $this->debug ) {
          $this->debug_data( $timeTaken, $this->sqlQuery );
        }

        return $this->queryResult;

      }

    }

    /**
    *
    * Fetch a result row as an object
    *
    * @access public
    * @return bool|object
    * @param string $SQL -> Database SQL query
    *
    **/
    public function fetch_object( $SQL = NULL ) {
      $this->logData .= $this->log_string( "fetch_object() called" );

      if ( ! is_null( $SQL ) )
        $this->sqlQuery = $SQL;

      $this->logData .= $this->log_string( $this->sqlQuery, "query" );
      $this->queryResult = NULL;

      $cacheName = ( is_null( $this->cacheName ) ) ? md5( $this->cacheID . $this->sqlQuery ) : md5( $this->cacheName . '-' . $this->cacheID . $this->sqlQuery );
      $this->cacheFile = $this->cacheDir . $cacheName . '.cache';

      if ( $this->cache == TRUE && $this->verify_cache() ) {

        return $this->get_cache();

      } else {

        $this->start_timer();

        if ( ! $this->PDO ) {

          switch( $this->dbType ) {
            case "mysql":
              $this->queryResult = @mysql_fetch_object( $this->query );
            break;
            case "mysqli":
              $this->queryResult = @mysqli_fetch_object( $this->query );
            break;
            case "mssql":
              $this->queryResult = @mssql_fetch_object( $this->query );
            break;
            case "postgres":
              $this->queryResult = @pg_fetch_object( $this->query );
            break;
          }
          $this->disconnect();

        } else {

          $this->query = $this->conn->query( $this->sqlQuery );

          if ( ! $this->query ) {
            $sqlError = $this->sql_error();
            $this->logData .= $this->log_string( "Query execution failed.", "error" );
            $this->logData .= $this->log_string( "Could not run query: " . $sqlError, "error" );
            $this->errorMsg[] = "Could not run query: <em>" . $sqlError . "</em>";
            return FALSE;
          }

          $this->queryResult = $this->query->fetch( PDO::FETCH_OBJ );
          $this->disconnect( $this->query );

        }

        $this->num_fields();
        $timeTaken = $this->stop_timer();

        // Cache
        if ( $this->cache ) {
          $this->set_cache();
        }

        // debug
        if ( $this->debug ) {
          $this->debug_data( $timeTaken, $this->sqlQuery );
        }

        return $this->queryResult;

      }

    }

    /**
    *
    * Fetch a result row as an associative array, a numeric array, or both
    *
    * @access public
    * @return bool|array
    * @param string $SQL -> Database SQL query
    *
    **/
    public function fetch_array( $SQL = NULL ) {
      $this->logData .= $this->log_string( "fetch_array() called" );

      if ( ! is_null( $SQL ) )
        $this->sqlQuery = $SQL;

      $this->logData .= $this->log_string( $this->sqlQuery, "query" );
      $this->queryResult = NULL;

      $cacheName = ( is_null( $this->cacheName ) ) ? md5( $this->cacheID . $this->sqlQuery ) : md5( $this->cacheName . '-' . $this->cacheID . $this->sqlQuery );
      $this->cacheFile = $this->cacheDir . $cacheName . '.cache';

      if ( $this->cache == TRUE && $this->verify_cache() ) {

        return $this->get_cache();

      } else {

        $this->start_timer();

        if ( ! $this->PDO ) {

          switch( $this->dbType ) {
            case "mysql":
              $this->queryResult = @mysql_fetch_array( $this->query );
            break;
            case "mysqli":
              $this->queryResult = @mysqli_fetch_array( $this->query );
            break;
            case "mssql":
              $this->queryResult = @mssql_fetch_array( $this->query );
            break;
            case "postgres":
              $this->queryResult = @pg_fetch_array( $this->query );
            break;
          }
          $this->disconnect();

        } else {

          $this->query = $this->conn->query( $this->sqlQuery );

          if ( ! $this->query ) {
            $sqlError = $this->sql_error();
            $this->logData .= $this->log_string( "Query execution failed.", "error" );
            $this->logData .= $this->log_string( "Could not run query: " . $sqlError, "error" );
            $this->errorMsg[] = "Could not run query: <em>" . $sqlError . "</em>";
            return FALSE;
          }

          $this->queryResult = $this->query->fetch( PDO::FETCH_BOTH );
          $this->disconnect( $this->query );

        }

        $this->num_fields();
        $timeTaken = $this->stop_timer();

        // Cache
        if ( $this->cache ) {
          $this->set_cache();
        }

        // debug
        if ( $this->debug ) {
          $this->debug_data( $timeTaken, $this->sqlQuery );
        }

        return $this->queryResult;

      }

    }

    /**
    *
    * Get number of rows in result
    *
    * @access public
    * @return bool|array
    * @param string $SQL -> Database SQL query
    *
    **/
    public function fetch_row( $SQL = NULL ) {
      $this->logData .= $this->log_string( "fetch_row() called" );

      $cacheName = ( is_null( $this->cacheName ) ) ? md5( $this->cacheID . $this->sqlQuery ) : md5( $this->cacheName . '-' . $this->cacheID . $this->sqlQuery );
      $this->cacheFile = $this->cacheDir . $cacheName . '.cache';

      if ( ! is_null( $SQL ) )
        $this->sqlQuery = $SQL;

      $this->logData .= $this->log_string( $this->sqlQuery, "query" );
      $this->queryResult = NULL;

      if ( $this->cache == TRUE && $this->verify_cache() ) {

        return $this->get_cache();

      } else {

        $this->start_timer();

        if ( ! $this->PDO ) {

          switch( $this->dbType ) {
            case "mysql":
              $this->queryResult = @mysql_fetch_row( $this->query );
            break;
            case "mysqli":
              $this->queryResult = @mysqli_fetch_row( $this->query );
            break;
            case "mssql":
              $this->queryResult = @mssql_fetch_row( $this->query );
            break;
            case "postgres":
              $this->queryResult = @pg_fetch_row( $this->query );
            break;
          }
          $this->disconnect();

        } else {

          $this->query = $this->conn->query( $this->sqlQuery );

          if ( ! $this->query ) {
            $sqlError = $this->sql_error();
            $this->logData .= $this->log_string( "Query execution failed.", "error" );
            $this->logData .= $this->log_string( "Could not run query: " . $sqlError, "error" );
            $this->errorMsg[] = "Could not run query: <em>" . $sqlError . "</em>";
            return FALSE;
          }

          $this->queryResult = $this->query->fetch( PDO::FETCH_NUM );
          $this->disconnect( $this->query );
        }

        $this->num_fields();
        $timeTaken = $this->stop_timer();

        // Cache
        if ( $this->cache ) {
          $this->set_cache();
        }

        // debug
        if ( $this->debug ) {
          $this->debug_data( $timeTaken, $this->sqlQuery );
        }

        return $this->queryResult;

      }

    }

    /**
    *
    * Get number of rows in query result
    *
    * @access public
    * @return bool|Integer
    * @param resource $query -> Query result
    *
    **/
    public function num_rows( $queryResult = NULL ) {
      $this->logData .= $this->log_string( "num_rows() called" );

      if ( ! is_null( $queryResult ) )
        $this->query = $queryResult;

      if ( ! $this->is_connected() ) {

        $this->logData .= $this->log_string( "No database connection made.", "error" );
        $this->errorMsg[] = "No database connection made.";
        return FALSE;

      }

      if ( ! $this->PDO ) {

        switch ( $this->dbType ) {
          case "mysql":
            $this->numRows = @mysql_num_rows( $this->query );
            break;
          case "mysqli":
            $this->numRows = @mysqli_num_rows( $this->query );
          break;
          case "mssql":
            $this->numRows = @mssql_num_rows( $this->query );
            break;
          case "postgres":
            $this->numRows = @pg_num_rows( $this->query );
            break;
        }

      } else {

        if ( ! is_null( $queryResult ) ) {
          $this->queryResult = $this->query->fetchAll( PDO::FETCH_OBJ );
        }
        $this->numRows = count( $this->queryResult );

      }

      if ( ! $this->numRows ) {
        $sqlError = $this->sql_error();
        $this->logData .= $this->log_string( "Query execution failed.", "error" );
        $this->logData .= $this->log_string( "Could not run query: " . $sqlError, "error" );
        $this->errorMsg[] = "Could not run query: <em>" . $sqlError . "</em>";
        return FALSE;
      }

      return $this->numRows;
    }

    /**
    *
    * Return number of fields in query result
    *
    * @access public
    * @return bool|Integer
    * @param object $stmt -> PDO statement object
    *
    **/
    public function num_fields( $stmt = NULL ) {
      $this->logData .= $this->log_string( "num_fields() called" );

      if ( ! $this->is_connected() ) {

        $this->logData .= $this->log_string( "No database connection made.", "error" );
        $this->errorMsg[] = "No database connection made.";
        return FALSE;

      }

      if ( ! $this->PDO ) {

        switch ( $this->dbType ) {
          case "mysql":
            $this->numFields = @mysql_num_fields( $this->query );
            break;
          case "mysqli":
            $this->numFields = @mysqli_num_fields( $this->query );
          break;
          case "mssql":
            $this->numFields = @mssql_num_fields( $this->query );
            break;
          case "postgres":
            $this->numFields = @pg_num_fields( $this->query );
            break;
        }

      } else {

        $this->numFields = ( is_null( $stmt ) ) ? $this->query->columnCount() : $stmt->columnCount();

      }

      if ( ! $this->numFields ) {
        $sqlError = $this->sql_error();
        $this->logData .= $this->log_string( "Query execution failed.", "error" );
        $this->logData .= $this->log_string( "Could not run query: " . $sqlError, "error" );
        $this->errorMsg[] = "Could not run query: <em>" . $sqlError . "</em>";
        return FALSE;
      }

      return $this->numFields;
    }

    /**
    *
    * Return the ID generated in the last query
    *
    * @access public
    * @return bool|Integer
    *
    **/
    public function insert_id() {
      $this->logData .= $this->log_string( "insert_id() called." );

      if ( ! $this->is_connected() ) {

        $this->logData .= $this->log_string( "No database connection made.", "error" );
        $this->errorMsg[] = "No database connection made.";
        return FALSE;

      }

      if ( ! $this->PDO ) {

        switch ( $this->dbType ) {
          case "mysql":
            $this->insertID = @mysql_insert_id();
            break;
          case "mysqli":
            $this->insertID = @mysqli_insert_id( $this->query );
          break;
          case "mssql":
            $this->insertID = @mssql_result( $this->query, 0, 0 );
            break;
          case "postgres":
            $this->insertID = @pg_last_oid( $this->query );
            break;
        }

      } else {

        $this->insertID = $this->conn->lastInsertId();

      }

      if ( ! $this->insertID ) {
        $sqlError = $this->sql_error();
        $this->logData .= $this->log_string( "Query execution failed.", "error" );
        $this->logData .= $this->log_string( "Failed to retrieve identity value: " . $sqlError, "error" );
        $this->errorMsg[] = "Failed to retrieve identity value: <em>" . $sqlError . "</em>";
        return FALSE;
      }

      return $this->insertID;
    }

    /**
    *
    * Return number of affected rows in previous MySQL operation
    *
    * @access public
    * @return bool|Integer
    * @param object $stmt -> PDO statement object
    *
    **/
    public function affected_rows( $stmt = NULL ) {
      $this->logData .= $this->log_string( "affected_row() called." );

      if ( ! $this->is_connected() ) {

        $this->logData .= $this->log_string( "No database connection made.", "error" );
        $this->errorMsg[] = "No database connection made.";
        return FALSE;

      }

      if ( ! $this->PDO ) {

        switch ( $this->dbType ) {
          case "mysql":
            $this->affectedRows = @mysql_affected_rows( $this->conn );
            break;
          case "mysqli":
            $this->affectedRows = @mysqli_affected_rows( $this->conn );
          break;
          case "mssql":
            $this->affectedRows = @mssql_rows_affected( $this->conn);
            break;
          case "postgres":
            $this->affectedRows = @pg_affected_rows( $this->conn );
            break;
        }

      } else {

        $this->affectedRows = ( is_null( $stmt ) ) ? $this->query->rowCount() : $stmt->rowCount();

      }

      return $this->affectedRows;
    }

    /**
    *
    * Insert new records into a database table
    *
    * @access public
    * @return bool|Integer
    *
    **/
    public function insert( $tableName, $insertData, $group = TRUE ) {
      $this->logData .= $this->log_string( "insert() called" );

      if ( ! $this->is_connected() ) {

        $this->logData .= $this->log_string( "No database connection made.", "error" );
        $this->errorMsg[] = "No database connection made.";
        return FALSE;

      }

      $where = '';

      if ( ! $this->PDO ) {

        $this->sqlQuery = "INSERT INTO `" . $tableName . "` ";

        if ( !$group ) {
          $tableField = '';
          $fieldValue = array();
          foreach ( $insertData as $field => $value ) {
            $tableField .= ', `' . $this->escape( $field ) . '`';
            $fieldValue[] = ', `' . $this->escape( $value ) . '`';
          }

          $this->sqlQuery .= '( ' . trim( substr( $tableField, 2 ) ) . ' ) ';
          $this->sqlQuery .= 'VALUES ( \'' . implode( '\', \'', $fieldValue ) . '\' )';
        } else {
          $data = array();
          foreach ( $updateData as $column => $value ) {
            $data[] = "`" . $this->escape( $this->clean_input( $column ) ) . "` = '" . $this->escape( $this->clean_input( $value ) ) . "'";
          }
          $this->sqlQuery .= implode( ', ', $data );
        }

        $this->logData .= $this->log_string( $this->sqlQuery, "query" );
        if ( ! $this->query( $this->sqlQuery ) ) {
          return FALSE;
        }
        $this->affected_rows();

      } else {

        if ( !is_array( $insertData ) && !is_null( $insertData ) ) {
          $this->logData .= $this->log_string( "PDO identifier must be an array.", "error" );
          $this->errorMsg[] = "PDO identifier must be an array.";
          return FALSE;
        }

        $this->sqlQuery = "INSERT INTO " . $tableName;
        $this->sqlQuery .= ' ( ' . implode( ', ', array_keys( $insertData ) ) . ' )';
        $this->sqlQuery .= ' VALUES ( :' . implode( ', :', array_keys( $insertData ) ) . ' )';

        $this->logData .= $this->log_string( $this->sqlQuery, "query" );

        $stmt = $this->conn->prepare( $this->sqlQuery );

        foreach ( $insertData as $field => $value ) {
          $bindField = ':' . $field;
          $stmt->bindParam( $bindField, $insertData[$field] );
        }

        if ( ! $stmt->execute() ) {
          $error = $this->sql_error( $stmt );
          $this->errorMsg[] = $error;
          $this->logData .= $this->log_string( $error, "error" );
          return FALSE;
        }
        $this->affected_rows( $stmt );
        // $this->conn->lastInsertId( 'student_id');

      }
      unset( $tableName, $insertData );
      return TRUE;

    }

    /**
    *
    * Update record in a database table
    *
    * @access public
    * @return bool|Integer
    * @param string $tableName -> Database table name
    * @param array $updateData -> fieldname => fieldvalue
    * @param string|array $identifier -> Query identifier, array for PDO
    *
    **/
    public function update( $tableName, $updateData, $identifier = NULL ) {
      $this->logData .= $this->log_string( 'update() called.' );

      if ( ! $this->is_connected() ) {

        $this->logData .= $this->log_string( 'No database connection made.', 'error' );
        $this->errorMsg[] = "No database connection made.";
        return FALSE;

      }

      if ( ! is_array( $updateData ) && ! is_null( $updateData ) ) {
        $this->logData .= $this->log_string( 'Update data must be an array.', 'error' );
        $this->errorMsg[] = "Update data must be an array.";
        return FALSE;
      }

      if ( is_null( $identifier ) ) {
        $this->logData .= $this->log_string( 'Specify an identifier.', 'error' );
        $this->errorMsg[] = "Specify an identifier.";
        return FALSE;
      }

      $where = '';

      if ( ! $this->PDO ) {

        if ( is_array( $identifier ) ) {
          $identifiers = array();
          foreach ( $identifier as $field => $value ) {
            $identifiers[] = " AND `" . $this->escape( $this->clean_input( $field ) ) . "` = '" . $this->escape( $this->clean_input( $value ) ) . "'";
          }

          $where = "WHERE " . substr( $identifiers, 4 );

        } else {
          if ( substr( strtoupper( trim( $identifier ) ), 0, 5 ) != 'WHERE' ) {
            $where = " WHERE ". $identifier;
          } else {
            $where = " " . trim( $identifier );
          }
        }

        $this->sqlQuery = "UPDATE `" . $tableName . "` SET ";

        $data = array();
        foreach ( $updateData as $column => $value ) {
          $data[] = "`" . $this->escape( $this->clean_input( $column ) ) . "` = '" . $this->escape( $this->clean_input( $value ) ) . "'";
        }
        $this->sqlQuery .= implode( ',', $data );

        $this->sqlQuery .= $where;

        $this->logData .= $this->log_string( $this->sqlQuery, 'query' );
        if ( $this->query( $this->sqlQuery ) ) {
          return TRUE;
        }
        $this->affected_rows();

      } else {

        if ( !is_array( $identifier ) && !is_null( $identifier ) ) {
          $this->logData .= $this->log_string( 'PDO identifier must be an array.', 'error' );
          $this->errorMsg[] = "PDO identifier must be an array.";
          return FALSE;
        }

        $this->sqlQuery = "UPDATE " . $tableName . " SET ";

        $updateFields = array();
        $variable = array();
        foreach ( $updateData as $field => $value ) {
          $fieldVar = ':' . $field;
          $updateFields[] = $field . " = " . $fieldVar;
        }
        $this->sqlQuery .= implode( ', ', $updateFields );

        foreach ( $identifier as $key => $value) {
          $where .= " AND {$key} = :{$key}";
          $variable[$key] = $this->escape( $value, FALSE );
        }
        $this->sqlQuery .= ' WHERE ' . substr( $where, 4 );

        $this->logData .= $this->log_string( $this->sqlQuery, 'query' );

        $stmt = $this->conn->prepare( $this->sqlQuery );
        foreach ( $updateData as $field => $value ) {
          $variable[$field] = $this->escape( $value, FALSE );
        }

        if ( ! $stmt->execute( $variable ) ) {
          $error = $this->sql_error( $stmt );
          $this->errorMsg[] = $error;
          $this->logData .= $this->log_string( $error, 'error' );
          return FALSE;
        }
        $this->affected_rows( $stmt );

      }
      unset( $tableName, $updateData, $identifier );
      return TRUE;
    }

    /**
    *
    * Delete record from database table
    *
    * @access public
    * @return bool|Integer
    * @param string $tableName -> Name of database table
    * @param array|string $identifier -> Query identifier
    *
    **/
    public function delete( $tableName, $identifier = NULL ) {
      $this->logData .= $this->log_string( "delete() called." );

      if ( ! $this->is_connected() ) {

        $this->logData .= $this->log_string( "No database connection made.", "error" );
        $this->errorMsg[] = "No database connection made.";
        return FALSE;

      }

      $where = '';

      if ( ! $this->PDO ) {

        if ( !empty( $identifier ) ) {
          if ( substr( strtoupper( trim( $identifier ) ), 0, 5 ) != 'WHERE' ) {
            $where = " WHERE ". $identifier;
          } else {
            $where = " " . trim( $identifier );
          }
        }
        $this->sqlQuery = "DELETE FROM " . $tableName . $where;
        $this->logData .= $this->log_string( $this->sqlQuery, "query" );
        if ( ! $this->query( $this->sqlQuery ) ) {
          return FALSE;
        }
        $this->affected_rows();
        return TRUE;

      } else {

        $this->sqlQuery = "DELETE FROM " . $tableName;

        if ( ! is_null( $identifier ) && ! is_array( $identifier ) ) {
          if ( substr( strtoupper( trim( $identifier ) ), 0, 5 ) != 'WHERE' ) {
            $where = " WHERE ". $identifier;
          } else {
            $where = " " . trim( $identifier );
          }
         $this->sqlQuery .= $where;
         $stmt = $this->conn->prepare( $this->sqlQuery );
       }

        if ( is_null( $identifier ) ) {
          $stmt = $this->conn->prepare( $this->sqlQuery );
        }

        if ( !is_null( $identifier ) && is_array( $identifier ) ) {
           foreach ( $identifier as $key => $value) {
            $where .= " AND {$key} = :{$key}";
            $variable[$key] = $this->escape( $value, FALSE );
          }
          $this->sqlQuery .= " WHERE " . substr( $where, 4 );
          $stmt = $this->conn->prepare( $this->sqlQuery );
          foreach ( $identifier as $field => $value ) {
            $bindField = ':' . $field;
            $stmt->bindParam( $bindField, $identifier[$field], PDO::PARAM_INT );
          }
        }
        $this->logData .= $this->log_string( $this->sqlQuery, 'query' );
        if ( ! $stmt->execute( $variable ) ) {
          $error = $this->sql_error( $stmt );
          $this->errorMsg[] = $error;
          $this->logData .= $this->log_string( $error, 'error' );
          return FALSE;
        }
        $this->affected_rows( $stmt );
        return TRUE;

      }

    }

    /**
    *
    * Sets the client character set
    *
    * @access public
    * @return bool
    * @param string $charset -> Character set name (optional)
    * @param string $collate -> Collation name (optional)
    * @param resource $conn -> Link identifier
    *
    **/
    public function set_charset( $conn = NULL, $charset = NULL, $collate = NULL ) {
      $this->logData .= "set_charset() called<br />";

      if ( ! $this->is_connected() ) {

        $this->logData .= "No database connection made.";
        $this->errorMsg[] = "No database connection made.";
        return FALSE;

      }

      if ( is_null( $charset ) )
        $charset = $this->dbCharset;

      if ( is_null( $collate ) )
        $collate = $this->dbCollate;

      if ( is_null( $conn ) )
        $conn = $this->conn;

      if ( ! $this->PDO ) {

        switch ( $this->dbType ) {
          case "mysql":
            $query = ( function_exists( 'mysql_set_charset' ) ) ? @mysql_set_charset( $charset, $conn ) : $this->query( 'SET NAMES ' . $charset, $conn );
            break;
          case "mysqli":
            $query = ( function_exists( 'mysqli_set_charset' ) ) ? @mysqli_set_charset( $charset, $conn ) : $this->query( 'SET NAMES ' . $charset, $conn );
          break;
          case "mssql":
            $query = @ini_set( 'mssql.charset', $charset );
            break;
          case "postgres":
            $query = ( function_exists( 'pg_set_client_encoding' ) ) ? @pg_set_client_encoding( $conn, $charset ) : $this->query( 'SET NAMES ' . $charset, $conn );
            break;
        }

        if ( ! empty( $collate ) )
          $this->query( 'COLLATE ' . $collate, $conn );

      } else {

        $query = $this->conn->exec( 'SET NAMES ' . $charset );

        if ( ! empty( $collate ) )
          $this->conn->exec( 'COLLATE ' . $collate );

      }

      if ( ! $query ) {
        $sqlError = $this->sql_error();
        $this->logData .= "Query execution failed.<br />";
        $this->logData .= "Could not run query: " . $sqlError . "<br />";
        $this->errorMsg[] = "Could not run query: <em>" . $sqlError . "</em><br />";
        return FALSE;
      }

      return TRUE;
    }

    /**
    *
    * Delete record from database table
    *
    * @access public
    * @return bool|Integer
    * @param string $tableName -> Name of database table
    * @param array|string $identifier -> Query identifier
    *
    **/
    private function log_string( $string, $type = 'log' ) {
      if ( is_null( $type) )
        $type = 'error';

      $dateTime = date( 'd/m/y h:i:s' );
      $logString = '';
      switch ( strtolower( $type ) ) {
        case 'error':
        case 'query':
          $logString = ucwords( $type) . ': <em>' . $string . '</em>';
          break;
        case 'log':
          $logString = $string;
          break;
        default:
          $logString = $string;
          break;
      }
      return $dateTime . ' ' .$logString . '<br />';

    }

    /**
    *
    * Create database table
    *
    * @access public
    * @return bool
    * @param string $SQL -> SQL code
    *
    **/
    public function create_table( $SQL ) {
      $this->logData .= "create_table() called.<br />";

      if ( empty( $SQL ) )
        return FALSE;

      $query = $this->query( $SQL );

      if ( $query ) {

        return TRUE;

      } else {

        $this->logData .= "Unable to create: <pre>" . $SQL . "</pre><br />";
        $this->errorMsg[] = "Unable to create: <pre>" . $SQL . "</pre>";
        return FALSE;

      }

    }

    /**
    *
    * Return list of table columns
    *
    * @access public
    * @return array
    * @param string $tablename -> Table name
    * @param string $colName -> Type of table details to return
    *
    **/
    public function table_details( $tableName, $colName = 'Field' ) {
      $this->logData .= "table_details() called.<br />";

      $columns = '';
      $query = $this->query( "SHOW COLUMNS FROM " . $tableName );
      while ( $row = $this->fetch_array( $query ) ) {

        $columns[] = $row->$colName;

      }

      return $columns;
    }

    /**
    *
    * Check if a table exists
    *
    * @access public
    * @return bool
    *
    **/
    public function table_exists( $tableName = NULL ) {
      $this->logData .= "table_exists() called.<br />";

      while ( $table = $this->show_tables() ) {

        if ( $table[0] == $tableName ) {

          return TRUE;

        } else {

          $this->logData .= $tableName . "don't exist<br />";
          $this->errorMsg[] = $tableName . "don't exist<br />";
          return FALSE;

        }

      }
      unset( $tableName );
    }

    /**
    *
    * Return a list of tables in the database
    *
    * @access public
    * @return bool|array
    *
    **/
    public function show_tables() {
      $this->logData .= "show_tables() called.<br />";

      $table = $this->query( "SHOW TABLES" );
      $fetch = $this->fetch_row( $table );
      if ( $fetch !== FALSE )
        return $fetch;
      else
        return FALSE;

      unset( $fetch );
    }

    /**
    *
    * De-fragment table that are frequently updated and/or deleted
    *
    * @access public
    * @return bool
    * @param string $tableName -> Table name
    *
    **/
    public function optimize( $tableName ) {
      $this->logData .= "optimize() called.<br />";

      return $this->query( "OPTIMIZE TABLE `". $this->escape( $tableName ) ."` " );
    }

    /**
    *
    * De-fragment tables that are frequently updated and/or deleted
    *
    * @access public
    * @return bool
    *
    **/
    public function optimize_all() {
      $this->logData .= "optimize_all() called.<br />";

      while ( $tables = $this->show_tables() ) {

        foreach ( $tables as $key => $tableName ) {

          if ( $this->optimize( $tableName ) ) {

            return TRUE;

          } else {

            return FALSE;
            $this->logData .= "Cannot optimize " . $tableName . " table<br />";
            $this->errorMsg[] = "Cannot optimize " . $tableName . " table";

          }

        }

      }

    }

    /**
    *
    * Import sql data file to database
    *
    * @access public
    * @return bool
    * @param string $file -> File directory
    *
    **/
    public function import_sql( $file ) {
      $this->logData .= "import_sql() called.<br />";

      if ( ! $this->is_connected() ) {

        $this->logData .= "No database connection made.";
        $this->errorMsg[] = "No database connection made.";
        return FALSE;

      }

      if ( ! file_exists( $file ) ) {

        $this->logData .= "The file " . $file . " does not exist.<br />";
        $this->errorMsg[] = "The file " . $file . " does not exist.<br />";
        return FALSE;

      }

      $content = @file_get_contents( $file );

      if ( ! $content ) {

        $this->logData .= "Unable to get the contents of the file  " . $file . ".<br />";
        $this->errorMsg[] = "Unable to get the contents of the file  " . $file . ".<br />";
        return FALSE;

      }

      $sql = explode( ';', $content );
      foreach ( $sql as $query ) {

        if ( !empty( $query ) ) {

          $result = $this->query( $query );

          if ( !$result ) {

            return FALSE;

          }

        }

      }

      return TRUE;
    }

    /**
    *
    * Backup database
    *
    * @access public
    * @param string $name -> Backup filename
    * @param string|array $tables -> Table(s) to backup
    * @author http://davidwalsh.name/backup-mysql-database-php
    *
    **/
    public function backup_db( $name = NULL, $tables = '*' ) {
      $this->logData .= "backup_db() called.<br />";

      // check if there is a database connection
      if ( !$this->is_connected() ) {
        $this->connect();
      }

      $backupContent = '';

      // get all of the tables
      if ( $tables == '*' )
      {
        $tables = array();
        $result = $this->show_tables();
        while ( $rowA = $this->fetch_row( $result ) )
        {
          $tables[] = $row[0];
        }
      }
      else
      {
        $tables = is_array( $tables ) ? $tables : explode( ',' , $tables );
      }

      // loop through tables
      foreach ( $tables as $table )
      {
        $result = $this->query( 'SELECT * FROM ' . $table );
        $numFields = $this->fetch_field( $result );
        $backupContent .= 'DROP TABLE ' . $table . ';';
        $rowB = $this->fetch_row(
          $this->query( 'SHOW CREATE TABLE ' . $table )
        );
        $backupContent .= '\n\n' . $rowB[1] . ';\n\n';

        for ( $i = 0; $i < $numFields; $i++ )
        {
          while ( $rowC = $this->fetch_row( $result ) )
          {
            $backupContent .= 'INSERT INTO ' . $table . ' VALUES (';
            for( $j = 0; $j < $numFields; $j++ )
            {
              $rowC[$j] = addslashes( $rowC[$j] );
              $rowC[$j] = ereg_replace( "\n", "\\n", $rowC[$j] );
              if ( isset( $row[$j] ) )
              {
                $backupContent .= '"' . $rowC[$j] . '"';
              }
              else
              {
                $backupContent .= '""';
              }

              if ( $j < ( $numFields - 1 ) ) {
                $backupContent .= ',';
              }
            }
            $backupContent .= ");\n";
          }
        }
        $backupContent .= "\n\n\n";
      }

      // Database file name
      if ( is_null( $name ) )
      {
        $backupName = 'backup-' . time() . '-' . ( md5( date( 'd-m-Y H:i:s' ) ) );
      }

      // Save file
      $handle = @fopen( $backupName . '.sql', 'w+' );
      @fwrite( $handle, $backupContent );
      @fclose( $handle );

    }

    /**
    *
    * Drop database/table
    *
    * @access public
    * @param string $type -> Drop type (table/db)
    * @param string $name -> Name of database/table
    *
    **/
    public function drop( $type = 'table', $name ) {
      $this->logData .= "drop() called<br />";

      // check if there is a database connection
      if ( !$this->is_connected() ) {
        $this->connect();
      }

      if ( $type == 'table' ) {
        $SQL = 'DROP TABLE IF EXISTS ' . $name;
      } else if ( $type == 'db' ) {
        $SQL = 'DROP DATABASE ' . $name;
      }

      $SQL = trim( $SQL );

      $this->logData .= "Query: " . $SQL . "<br />";

      if ( ! $this->PDO ) {

        if ( ! $this->query( $SQL ) ) {

          $sqlError = $this->sql_error();
          $this->logData .= "Query execution failed.<br />";
          $this->logData .= "Could not run query: " . $sqlError . "<br />";
          $this->errorMsg[] = "Could not run query: <em>" . $sqlError . "</em><br />";
          return FALSE;

        }

      } else {

        try {

          $this->conn->exec( $SQL );

        } catch( PDOExpection $e ) {

          $this->logData .= "Query execution failed.<br />";
          $this->logData .= "Could not run query: ".$e->getMessage(). "<br />";
          $this->errorMsg[] = "Could not run query: <em>" . $e->getMessage() . "</em><br />";
          return FALSE;

        }

      }

      unset ( $SQL, $type, $name );
      return TRUE;

    }

    /**
    *
    * Convert array|object to xml
    *
    * @access public
    * @return string
    * @param array|object $array -> Array/object of data to convert
    * @param array|string $xml -> Check if the XML Object exists
    *
    **/
    public function convert_to_xml( $array, $xml = FALSE ) {
      if ( ! ( is_array( $array ) || is_object( $array ) ) ) {
        return FALSE;
      }

      if ( $xml === FALSE ) {
        $xml = new SimpleXMLElement('<root/>');
        $this->logData .= $this->log_string( "convert_to_xml() called" );
      }

      foreach ( $array as $key => $value ) {
        if ( is_array( $value ) || is_object( $value ) ) {
          $this->convert_to_xml( $value, $xml->addChild( $key ) );
        } else {
          $xml->addChild( $key, $value );
        }
      }
      return $xml->asXML();
    }

    /**
    *
    * Encode/Decode data
    *
    * @access public
    * @return string|array|bool|object
    * @param string|arrray|object $value -> Data that need to be encoded/decoded
    * @param string $type -> Output type
    *
    **/
    public function output( $value, $type = 'jsone' ) {
      $this->logData .= $this->log_string( "output() called" );

      if ( is_null( $value ) )
        return FALSE;

      switch ( strtolower( $type ) ) {
        case 'jsonencode':
        case 'jsone':
          $outputData = json_encode( $value );
          break;
        case 'jsondecode':
        case 'jsond':
          $outputData = json_decode( $value );
          break;
        case 'serialize':
          $outputData = serialize( $value );
          break;
        case 'unserialize':
          $outputData = unserialize( $value );
          break;
        case "base64encode":
        case "base64e":
          $outputData = base64_encode( $value );
        break;
        case "base64decode":
        case "base64d":
          $outputData = base64_decode( $value );
        break;
        case "bin2hex":
        case "hexe":
          $outputData = bin2hex( $value );
        break;
        case "hex2bin":
        case "hexd":
          $outputData = @pack( "H*", $value );
        break;
        case "uuencode":
        case "uue":
          $outputData = convert_uuencode( $value );
        break;
        case "uudecode":
        case "uud":
          $outputData = convert_uudecode( $value );
        case 'xmlencode':
        case 'xmle':
          $output = Database::convert_to_xml( $value );
          return ( $output ) ? $output : $this->output( $value, 'jsone' );
          break;
        default:
          $outputData = json_encode( $value );
          break;
      }
      return $outputData;

    }

    /**
    *
    * Encode/Decode data
    *
    * @access public
    * @return string|array|bool|object
    * @param string|arrray|object $value -> Data that need to be encrypted
    * @param string $type -> Output type
    *
    **/
    public function _encrypt( $value, $type = 'md5' ) {
      $this->logData .= $this->log_string( "_encrypt() called" );

      if ( is_null( $value ) )
        return FALSE;

      switch( strtolower( $type ) ) {
        case "md5":
          $outputData = md5( $value );
        break;
        case "md5d":
          $outputData = md5( md5( $value ) );
        break;
        case "sha1":
          $outputData = sha1( $value );
        break;
        case "crypt":
          // encoding varies dependent on the OS
          $outputData = crypt( $value );
        break;
        default:
          $outputData = md5( $value );
        break;
      }
      return $outputData;

    }

    /**
    *
    * Start the debugging timer
    *
    * @access public
    * @return bool|true
    *
    **/
    public function start_timer() {
      $this->logData .= $this->log_string( "start_timer() called" );

      $this->startTime = microtime ( TRUE );
      return TRUE;
    }

    /**
    *
    * Stop the debugging time
    *
    * @access public
    * @return bool|true
    *
    **/
    public function stop_timer() {
      $this->logData .= $this->log_string( "stop_timer() called" );

      return ( microtime ( TRUE ) - $this->startTime );
    }

    public function search( $tableName, $query = NULL, $searchFields, $orderBy = NULL, $limit = NULL ) {

    }

    /**
    *
    * Set cache output
    *
    * @access public
    * @param $encode -> Cache encode type i.e jsonencode/jsone, serialize,
    *                   base64encode/base64e, bin2hex/hexe, uuencode/uue
    * @param $decode -> Cache decode type i.e jsondecode/jsond, unserialize,
    *                   base64decode/base64d, hex2bin/hexd, uudecode/uud
    *
    **/
    public function set_cache_output( $encode = NULL, $decode = NULL ) {
      $this->cacheEncode = ( ! is_null( $encode ) ) ? $encode : $this->cacheEncode;
      $this->cacheDecode = ( ! is_null( $decode ) ) ? $decode : $this->cacheDecode;
    }

    /**
    *
    * Set Unique ID to make cache even more unique to user
    *
    * @access public
    * @param $ID -> Cache unique id
    *
    **/
    public function set_cache_id( $ID = NULL ) {
      $this->logData .= $this->log_string( "set_cache_id() called" );
      $this->cacheID = trim( $ID );
    }

    /**
    *
    * Verify if cache existed and has expired
    *
    * @access private
    * @return bool|true
    *
    **/
    private function verify_cache() {
      $this->logData .= $this->log_string( "verify_cache() called" );

      if ( $this->cacheAge == "0" ) {

        $this->logData .= $this->log_string( "Cache age not set", "error" );
        return FALSE;

      } else {

        if ( $this->check_cache() ) {

          if ( date( 'Y-m-d', $this->cacheMod ) != date( 'Y-m-d', time() ) ) {

            $this->logData .= $this->log_string( "cache has expired", "error" );
            return FALSE;

          } else if ( ( time() - $this->cacheMod ) >= $this->cacheAge ) {

            $this->logData .= $this->log_string( "cache has expired", "error" );
            return FALSE;

          } else {

            return TRUE;

          }

        } else {

          return FALSE;

        }

      }

    }

    /**
    *
    * Check if cache exists
    *
    * @access public
    * @return bool
    *
    **/
    public function check_cache(){
      $this->logData .= $this->log_string( "check_cache() called" );

      if ( !file_exists( $this->cacheFile ) ) {

        $this->logData .= $this->log_string( $this->cacheFile . " does not exist", "error" );
        $this->errorMsg[] = "Cache File: <em>" . $this->cacheFile . " does not exist</em>";
        return FALSE;

      }

      // Set cache last modified time
      $this->cacheMod = filemtime( $this->cacheFile );
      return TRUE;
    }

    /**
    *
    * Store cache to file
    *
    * @access public
    * @return bool
    * @param string $result -> Database query result
    *
    **/
    public function set_cache( $result = NULL ) {
      $this->logData .= $this->log_string( "set_cache() called" );

      if ( ! is_null( $result) )
        $this->queryResult = $result;

      if ( ! $cacheFile = @fopen( $this->cacheFile, "w" ) ) {

        $this->logData .= $this->log_string( "Could not open cache: " . $this->cacheFile, "error" );
        $this->errorMsg[] = "Cache: <em>Could not open cache: " . $this->cacheFile . "</em>";
        return FALSE;

      }

      $output = $this->output( $this->queryResult );

      if ( ! fwrite( $cacheFile, $output ) ) {

        $this->logData .= $this->log_string( "Could not write to cache: " . $this->cacheFile, "error" );
        $this->errorMsg[] = "Cache: <em>Could not write to cache: " . $this->cacheFile . "</em>";
        fclose ( $cacheFile );
        return FALSE;

      }

      fclose( $cacheFile );
      return TRUE;
    }

    /**
    *
    * Retrieve cache data
    *
    * @access public
    * @return mixed
    * @param string $result -> Database query result
    *
    **/
    public function get_cache(){
      $this->logData .= $this->log_string( "get_cache() called" );
      $this->logData .= $this->log_string( $this->sqlQuery, "query" );

      $this->start_timer();

      if ( !$cacheFile = @file_get_contents( $this->cacheFile ) ) {

        $this->logData .= $this->log_string( "Could not read cache " . $this->cacheFile, "error" );
        $this->errorMsg[] = "Cache: <em>Could not read cache " . $this->cacheFile . "</em>";
        return FALSE;

      }

      if ( !$this->queryResult = json_decode( $cacheFile ) ) {

        $this->logData .= $this->log_string( "get_cache() failed", "error" );
        return FALSE;

      }

      $timeTaken = $this->stop_timer();
      $this->numRows = count( $this->queryResult );

      // debug
      if ( $this->debug ) {

        $this->debug_data( $timeTaken, $this->sqlQuery, 'cache' );

      }

      return $this->queryResult;
    }

    /**
    *
    * Store data for debugging
    *
    * @access private
    * @param integer $timeTaken -> Database query time
    * @param string $SQL -> Database query
    * @param string $queryType -> Database query type ( db or cache )
    *
    **/
    private function debug_data( $timeTaken, $SQL, $queryType = "db" ) {
      $this->logData .= $this->log_string( "debug_data() called" );

      $this->queryCount++;
      $time = number_format ( $timeTaken, 8 );
      $this->queryTime += $time;
      $this->querydebug[$this->queryCount] = array (
                                'Query' => $SQL,
                                'Time' => $time,
                                'Type' => $queryType
                              );
    }

    /**
    *
    * Escapes special characters in a string for use in an SQL statement
    *
    * @access public
    * @return string|bool
    * @param string|array $var -> The string that is to be escaped
    * @author http://css-tricks.com
    *
    **/
    function clean_input( $input ) {
      $this->logData .= $this->log_string( "clean_input() called" );

      $search = array(
        '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
        '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
        '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
        '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
      );

      $output = preg_replace( $search, '', $input );
      return $output;
    }

    /**
    *
    * Escapes special characters in a string for use in an SQL statement
    *
    * @access public
    * @deprecated 2.9.14
    * @return string
    * @param string $var -> The string that need filtering
    *
    **/
    public function filter( $var ) {
      $this->logData .= $this->log_string( "filter() called" );
      $unwantedString = '/[^\.\/\,\-\_\'\"\@\?\!\:\$\+ a-zA-Z0-9()]/';
      return preg_replace( $unwantedString, '', $var );
    }

    /**
    *
    * Escapes special characters in a string for use in an SQL statement
    *
    * @access public
    * @return string|bool
    * @param string|array $var -> The string that is to be escaped
    * @author http://css-tricks.com/snippets/php/sanitize-database-inputs/
    *
    **/
    public function escape( $input, $quote = TRUE ) {
      $this->logData .= $this->log_string( "escape() called" );

      // Make a connection to the database if not connected
      if ( ! $this->is_connected() ) {
        $this->connect();

      }

      if ( is_array( $input ) ) {

        foreach( $input as $key => $value ) {
          $output[$key] = $this->escape( $value );
        }

      } else {

        // this removes whitespace and related characters
        // from the beginning and end of the string
        $input = trim( $input );

        if ( get_magic_quotes_gpc() )
          $input = stripslashes( $input );


        $input = $this->clean_input( $input );

        if ( ! $this->PDO ) {

          switch ( $this->dbType ) {
            case "mysql":
              // If PHP version > 4.3.0
              if ( function_exists( "mysql_real_escape_string" ) ) {
                // Escape the MySQL string
                $string = @mysql_real_escape_string( $input );
              } else {
                $string = addslashes( $input );
              }
            break;
            case "mysqli":
              // If PHP version > 4.3.0
              if ( function_exists( "mysqli_real_escape_string" ) ) {
                // Escape the MySQLi string
                $string = @mysqli_real_escape_string( $input );
              } else {
                $string = addslashes( $input );
              }
            break;
            case "mssql":
              $string = @str_replace( "'", "''", $input );
            break;
            case "postgres":
              // If PHP version > 4.3.0
              if ( function_exists( "pg_escape_string" ) ) {
                // Escape the POSTGRES string
                $string = @pg_escape_string( $input );
              } else {
                $string = addslashes( $input );
              }
            break;
          }

        } else {

          $string = ( $quote ) ? $this->conn->quote( $input ) : $input;

        }

      }

      return $string;
    }

    /**
    *
    * Show database and SQL errors
    *
    * @access public
    * @return string|bool False if the showing of errors is disabled.
    *
    **/
    public function errors() {
      if ( $this->suppressErrors )
        return FALSE;

      foreach ( $this->errorMsg as $key => $value )
        return $value;

    }

  }

?>
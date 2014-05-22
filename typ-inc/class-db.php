<?php
/**
*
* MySQLi Database Class (ZerDB)
*
* @author Zerquix18
* @version 1.0
* @link http://github.com/zerquix18/zerdb
* @copyright Copyright (c) 2014, Zerquix18
*
**/

if( ! class_exists('mysqli') )
	exit("Class MySQLi doesn't exist'");

class zerdb {

	public $tablas = array(
			"usuarios" => array(
					"usuario", "clave", "email", "estado", "rango", "hash"
				),
			"sesiones" => array(
					"id", "hash", "ip", "fecha"
				),
			"trackers" => array(
					"personaje", "img", "imgbg", "fuente"
				),
			"config" => array(
					"titulo", "url", "robots", "extra"
				),
			"twitter" => array(
					"consumer_key", "consumer_secret", "access_token", "access_token_secret"
				),
			"log" => array(
					"accion", "fecha"
				),
			"tweets" => array(
					"nombre", "tweet"
				),
			"parametros" => array(
					"tracker", "posicion", "size", "angulo", "x", "y"
				)
		);

  /**
  *
  * Database username
  *
  * @since 1.0
  * @access private
  * @var string
  *
  **/
	private $dbhost;

  /**
  *
  * Database user
  *
  * @since 1.0
  * @access private
  * @var string
  *
  **/
	private $dbuser;

  /**
  *
  * Database password
  *
  * @since 1.0
  * @access private
  * @var string
  *
  **/

	private $dbpass;

  /**
  *
  * Database name
  *
  * @since 1.0
  * @access private
  * @var string
  *
  **/
	private $dbname;

  /**
  *
  * Database port
  *
  * @since 1.0
  * @access private
  * @var string
  *
  **/
	private $port;

  /**
  *
  * Affected rows of the last query OR the nums of rows selected in the last query.
  *
  * @since 1.0
  * @access public
  * @var int
  *
  **/
	public $nums = null;

  /**
  *
  * Is the connection done and right?
  *
  * @since 1.0
  * @access public
  * @var bool
  *
  **/
	public $ready = false;

  /**
  *
  * Access to MySQLi resource
  *
  * @since 1.0
  * @access public
  * @var object
  *
  **/
	public $mysqli;

  /**
  *
  * Last query you made or the current query you're making.
  *
  * @since 1.0
  * @access public
  * @var string|null
  *
  **/
	public $query;

  /**
  *
  * Last error
  *
  * @since 1.0
  * @access public
  * @var string
  *
  **/
	public $error;

  /**
  *
  * Last error number
  *
  * @since 1.0
  * @access public
  * @var string
  *
  **/
	public $errno;

  /**
  *
  * Database charset
  *
  * @since 1.0
  * @access public
  * @var string
  *
  **/
	public $charset;

  /**
  *
  * Last ID inserted (if there's one).
  *
  * @since 1.0
  * @access public
  * @var public
  *
  **/
	public $id;

  /**
  *
  * Class constructor.
  *
  * @param string $dbhost
  * @param string $dbuser
  * @param string $dbpass
  * @param string $dbname
  * @param string $charset
  * @param integer $port
  *
  **/
	public function __construct($dbhost, $dbuser, $dbpass, $dbname, $charset = null, $port = null ) {

		$this->dbhost = $dbhost;
		$this->dbuser = $dbuser;
		$this->dbpass = $dbpass;
		$this->dbname = $dbname;
		$this->charset = ! is_null($charset) ? $charset : 'utf8';
		$this->port = is_null($port) ? ini_get('mysqli.default_port') : $port;

		return $this->connect();
	}

  /**
  *
  * It connects to the database.
  *
  * @access private
  * @return bool true if everything is done and false if there was an error
  *
  **/
	private function connect() {
		$this->mysqli = new mysqli( $this->dbhost, $this->dbuser, $this->dbpass, $this->dbname, $this->port);
		if( $this->mysqli->connect_error ) {
			$this->error = $this->mysqli->connect_error;
			$this->errno = $this->mysqli->connect_errno;
			return false;
		}
		foreach($this->tablas as $a => $b)
				$this->$a = $a;

		$this->mysqli->set_charset( $this->charset );
		$this->ready = true;
		return true;
	}
  /**
  *
  * It closes the connection
  *
  * @return bool
  *
  **/
	public function close() {
		if( $this->ready )
			return $this->mysqli->close();
		return false;
	}

  /**
  *
  * It cleans some vars to use it again
  *
  * @return bool
  *
  **/
	public function flush() {
		$this->query = null;
		$this->error = null;
		$this->errno = null;
		$this->nums = null;
		return true;
	}

  /**
  *
  * It scapes the special characters in a string
  *
  * @access public
  * @param string $string
  * @return string
  *
  **/
	public function real_escape( $string ) {
    if( ! is_string( $string ) )
      return $string; // If it's integer or float there's nothing we have to do.
    $string = stripslashes($string); // if you added slashes...
    if( $this->ready )
      return $this->mysqli->real_escape_string( $string );
    else
      return addslashes( $string );
	}

  /**
  *
  * It makes a query.
  *
  * @param string $query
  * @param mixed $args
  * @return bool|object
  *
  **/
  public function query( $query = null, $args = '' ) {
    if( is_null($query) )
      return false;
    if( ! empty($args) && ! preg_match("/(\%)(s|d|f)|[\?]/", $query ) )
      return false;

    $this->flush();

      if( ! empty($args) ) {
      $args = func_get_args();
      array_shift($args);
      if( is_array($args[0]) )
        $args = $args[0];
      array_walk($args, array($this, "real_escape") );
      $rplc = array("%s", "%d", "%f", "'?'", "'%s'", "'%d'", "'%f'"); // deletes mistakes
      $query = str_replace($rplc, "?", $query); // replace all that by: ? :)
      if( preg_match_all("!\?!", $query) !== count($args) )
      return false;
    }

    if( ! empty($args) )
      foreach($args as $a) {
        $a = is_string($a) ? "'$a'" : $a; // if it's string it will pass it like an string eh... be careful.
        $query = preg_replace("/\?/", $a, $query, 1); // Replaces all ? by args in order... that's why the count have to be the same
      }

    $this->query = $query;

    return $this->execute();
  }

  /**
  *
  * It executes the query loaded in $this->query
  *
  * @return bool|object
  *
  **/
  public function execute() {
    if( empty($this->query ) )
      return false;
  	$q = $this->mysqli->query( $this->query );
  	if( !$q ) {
  		$this->error = $this->mysqli->error;
  		$this->errno = $this->mysqli->errno;
      return false;
  	}
  	$this->id = $this->mysqli->insert_id; // dw if it's null...
    $this->nums = $this->mysqli->affected_rows;
    if( preg_match('/(select)/i', $this->query) ) {
      $lol = new stdClass();
      $this->nums = $lol->nums = $q->num_rows;
      $lol->r = $q;
      if( $this->nums == 1):
        foreach($q->fetch_array() as $a => $b)
          $lol->$a = stripslashes($b);
        $q->data_seek(0);
        endif;
      return $lol;
    }
  	return $q;
  }

  /**
  *
  * Alias for $this->execute()
  *
  **/
  public function _() {
  	return $this->execute();
  }

  /**
  *
  * It selects something from the database
  *
  * @param string $table
  * @param string $data
  * @param array|null
  * @return bool|object
  *
  **/
  public function select( $table,  $data = "*", $where = null ) {
    if( ! $this->ready )
      return false;

  	$this->flush();
  	$this->query = "SELECT $data FROM $table";

    if( ! is_null($where) )
      $this->where( $where );

  	return $this;
  }

  /**
  *
  * It updates something in the database
  *
  * @param string $table
  * @param string|array $arg1
  * @param string $arg2
  * @return bool|object
  *
  **/
  function update( $table, $arg1, $arg2 = '') {
    if( ! $this->ready )
      return false;

    if( ! is_array($arg1) && count( func_get_args() ) !== 3 )
      return false;
    $this->flush();
    $this->query = "UPDATE {$table}";
    $set = array();
  if( empty($arg2) ) 
    foreach($arg1 as $a => $b)
      $set[] = "{$a} = '{$b}'";
  else
    $set = array("{$arg1} = '{$arg2}'");
    $this->query .= " SET " . implode(', ', $set);

    return $this;
  }

  /**
  *
  * It deletes something in the database
  *
  * @param string $table
  * @param null|array $arg2
  * @return object
  *
  **/
  public function delete( $table, $arg2 = null) {
    if( ! $this->ready )
      return false;

    $this->flush();
    $this->query .= "DELETE FROM {$table}";
    if( is_array($arg2) )
      $this->where( $arg2 );

    return $this;
  }

  /**
  *
  * It inserts something in the database
  *
  * @param string $table
  * @param array $t_data
  * @param array $data
  * @return bool
  *
  **/
  public function insert( $table, $data) {
    $data = array_slice( func_get_args(), 1 );
    return $this->insert_replace("INSERT", $table, $data );
  }
  /**
  *
  * It replaces something in the database
  *
  * @param string $table
  * @param array $t_data
  * @param array $data
  * @return bool
  *
  **/
  public function replace( $table, $data ) {
    $data = array_slice( func_get_args(), 1 );
    return $this->insert_replace("REPLACE", $table, $data );
  }
  /**
  *
  * Helper for insert and replace
  *
  **/
  private function insert_replace($action, $table, $data) {
    if( ! $this->ready )
      return false;
    if( empty($data) )
      return false;
    if( ! in_array( strtoupper($action), array("INSERT", "REPLACE") ) ) 
      return false;
    //then
  	$t_data = $this->tablas[$table];
    $this->query = "{$action} INTO {$table} (" . implode(', ', $t_data) . ") VALUES ";
    $v = array();
    foreach($data as $a)
        $v[] = "('" . implode("','", $a) . "')";

      $this->query .= implode(', ', $v);
      
      return $this->execute();
  }
  /**
  *
  * It alters some table in the database
  *
  * @param string $table
  * @param string $action
  * @param null|string|array $values
  * @return bool|object
  *
  **/
  function alter( $table, $action, $values = null) {
    $this->flush();
    $action = strtoupper($action);
    if( ! in_array($action, array('ADD', 'DROP', 'MODIFY') ) )
      return false;
    $action = ($check = in_array($action, array("DROP", "MODIFY") ) ) ? $action . " COLUMN" : $action;
    $this->query = "ALTER TABLE {$table} {$action} ";
    if("ADD" == $action || "MODIFY COLUMN" == $action)  {
      if( ! is_array($values) )
        return false;
      foreach($values as $a => $b) {
        $this->query .= "`{$a}` {$b}";

        break; // just one...
      }
     }elseif( "DROP COLUMN" == $action ) {
      if( ! is_string($values) )
        return false;
      $this->query .= $values;
    }
    return $this->execute();
  }
  /**
  *
  * It adds WHERE to the loaded query. 
  *
  * @param array|string $arg1
  * @param string $arg2
  * @param string $arg3
  * @return bool|object
  *
  **/
  public function where( $arg1, $arg2 = '', $and = "AND") {
    $args = func_get_args();
    if( ! is_array($arg1) && empty($arg2) )
      return false;
      $ops = array(">", "<", "!=", "=");
      if( in_array($arg2, $ops) )
        $op = $arg2;
      else
        $op = "=";

    if( is_array($arg1) ) {
      $wh = array();
      foreach($arg1 as $a => $b){
        $b = is_int($b) ? "{$b}" : "'{$b}'";
        $wh[] = "{$a} {$op} {$b}";
      }
      $this->query .= " WHERE " . implode(" {$and} ", $wh);
    }else{
      $this->query .= " WHERE {$args[0]} {$op} '{$args[1]}'";
    }

    return $this;
  }

  /**
  *
  * This is the same that where() but this adds "WHRE something != 'somethin'"
  *
  * @param array $where
  *
  **/
  public function wherenot( $where ) {
    return $this->where($where, "!=");
  }

  /**
  *
  * This is the same that where() but this adds "WHRE something > number"
  *
  * @param array $where
  *
  **/
  public function wheremt( $where ) {
    return $this->where($where, ">");
  }

  /**
  *
  * This is the same that where() but this adds "WHRE something < number"
  *
  * @param array $where
  *
  **/
  public function wherelt( $where ) {
    return $this->where($where, "<");
  }
  /** 
  *
  * LIKE statement
  *
  * @param string|array $arg1
  * @param string|bool $arg2
  * @param string $and
  * @param bool $not
  * @return bool|object
  *
  **/
  public function like($arg1, $arg2 = false, $and = "AND", $not = false) {
  if( ! is_array($arg1) && empty($arg2) )
    return false;
    $args = func_get_args();
    $like = array();
    $not = (!$not) ? '' : 'NOT ';
    if( is_array($arg1) ) {
      foreach($arg1 as $a => $b)
        $like[] = "$a {$not} LIKE %" . $b . "%";
    }else{
      $like[] = "{$args[0]} {$not}LIKE '%{$args[1]}%'";
    }
    $this->query .= " WHERE " . implode(' {$and} ', $like );

    return $this;
  }
  /**
  *
  * LIKE statement, but just starting ("WHERE something LIKE test%")
  *
  * @param string|array $arg1
  * @param string|bool $arg2
  * @param string $and
  * @param bool $not
  * @return bool|object
  *
  **/
  public function slike( $arg1, $arg2 = false, $and = "AND", $not = false) {
    if( ! is_array($arg1) && empty($arg2) )
      return false;
    $args = func_get_args();
    $like = array();
    $not = (!$not) ? '' : 'NOT ';
    if( is_array($arg1) ) {
      foreach($arg1 as $a => $b)
        $like[] = "$a $not LIKE %" . $b;
    }else{
      $like[] = "{$args[0]} {$not}LIKE '%{$args[1]}'";
    }
    $this->query .= " WHERE " . implode(' {$and} ', $like );
    return $this;
  }
  /**
  *
  * NOT like statement, it has the same use that $this->like()
  *
  **/
  public function notlike( $arg1, $arg2 = false, $and = "AND" ) {
    return $this->like($arg1, $arg2, $and, true );
  }
  /**
  *
  * NOT LIKE statement, it has the same use that $this->slike()
  *
  */
  public function notslike($arg1, $arg2 = false, $and = "AND") {
    return $this->slike($arg1, $arg2, $and, true);
  }
  /**
  *
  * LIMIT statement
  *
  * @param string|int $l1
  * @param string|int $l2
  * @return object
  *
  **/
  public function limit($l1, $l2 = '') {
    if( ! is_numeric($l1) )
      return false;
    $this->query .= " LIMIT {$l1}";
    if( is_numeric($l2) )
      $this->query .= ",{$l2}";

    return $this->execute();
  }
  /**
  *
  * It returns the Query
  *
  **/
  public function getQuery() {
    return $this->query;
  }
  /**
  *
  * It adds something to the current query
  *
  **/
  public function add( $add ) {
    $this->query .= " " . $add;
    return $this;
  }
  
  /** End class! **/
}
<?php

require_once 'crudController.php';

class dataTableController extends crudController {
  
    public function __construct(){
        parent::__construct();  
		$this->table = $this->data->table;
		$this->filtro = "";
		//$this->verifyTable($this->table);       
    }

    public function dataTableData(){
        // Table's primary key
        $primaryKey = 'id'.$this->table;		
		
		// Array of database columns which should be read and sent back to DataTables.
		// The `db` parameter represents the column name in the database, while the `dt`
		// parameter represents the DataTables column identifier. In this case object
		// parameter names
		$columns = array();

		$fields_table = $this->dataSchema($this->table);
		
		foreach ($fields_table as $value) {
			$field_name = $value->COLUMN_NAME;
			$field_type = $value->DATA_TYPE;
			array_push($columns,array( 'db' => $field_name, 'dt' => $field_name, 'type' => $field_type ),);			                
		}       
		//$this->returnData($this->data->dataTable,200);
		$dataTable = $this->simple((array) $this->data->dataTable, $this->table, $primaryKey, $columns);
		$this->returnData($dataTable,200);
       
    }

    /**
	 * Create the data output array for the DataTables rows
	 *
	 *  @param  array $columns Column information array
	 *  @param  array $data    Data from the SQL get
	 *  @return array          Formatted data in a row based format
	 */
	function data_output ( $columns, $data )
	{
		$out = array();	

		for ( $i=0, $ien=count($data) ; $i<$ien ; $i++ ) {
			$row = array();

			for ( $j=0, $jen=count($columns) ; $j<$jen ; $j++ ) {
				$column = $columns[$j];

				// Is there a formatter?
				if ( isset( $column['formatter'] ) ) {
					//$row[ $column['dt'] ] = $column['formatter']( $data[$i][ $column['db'] ], $data[$i] );
					$row[ $j ] = $column['formatter']( $data[$i][ $column['db'] ], $data[$i] );
				}
				else {
					//$row[ $column['dt'] ] = $data[$i][ $columns[$j]['db'] ];
					$row[ $j ] = $data[$i][ $columns[$j]['db'] ];
					
				}
			}

			$out[] = $row;
			
		}

		return $out;
	}

	/**
	 * Paging
	 *
	 * Construct the LIMIT clause for server-side processing SQL query
	 *
	 *  @param  array $request Data sent to server by DataTables
	 *  @param  array $columns Column information array
	 *  @return string SQL limit clause
	 */
	static function limit ( $request, $columns )
	{
		$limit = '';
		if ( isset($request['start']) && $request['length'] != -1 ) {
			$limit = "LIMIT ".intval($request['start']).", ".intval($request['length']);
		}
		return $limit;
	}

	/**
	 * Ordering
	 *
	 * Construct the ORDER BY clause for server-side processing SQL query
	 *
	 *  @param  array $request Data sent to server by DataTables
	 *  @param  array $columns Column information array
	 *  @return string SQL order by clause
	 */
	static function order ( $request, $columns )
	{
		$order = '';

		if ( isset($request['order']) && count($request['order']) ) {
			$orderBy = array();
			$dtColumns = self::pluck( $columns, 'dt' );

			for ( $i=0, $ien=count($request['order']) ; $i<$ien ; $i++ ) {
                // Convert the column index into the column data property
              
				$columnIdx = intval($request['order'][$i]->column);
				$requestColumn = (array) $request['columns'][$columnIdx];

				$columnIdx = array_search( $requestColumn['data'],  $dtColumns );
				/* if($columnIdx ){
					$column = $columns[ $columnIdx ];
				} */

				if ( $requestColumn['orderable'] == 'true' ) {
					$dir = $request['order'][$i]->dir === 'asc' ?
						'ASC' :
						'DESC';

					$orderBy[] = '`'.$columns[$columnIdx]['db'].'` '.$dir;
				}
			}

			$order = 'ORDER BY '.implode(', ', $orderBy);
		}

		return $order;
	}


	/**
	 * Searching / Filtering
	 *
	 * Construct the WHERE clause for server-side processing SQL query.
	 *
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here performance on large
	 * databases would be very poor
	 *
	 *  @param  array $request Data sent to server by DataTables
	 *  @param  array $columns Column information array
	 *  @param  array $bindings Array of values for PDO bindings, used in the
	 *    sql_exec() function
	 *  @return string SQL where clause
	 */
	function filter ( $request, $columns, &$bindings, $table )
	{
		$globalSearch = array();
		$columnSearch = array();
		$dtColumns = self::pluck( $columns, 'dt' );

		if ( isset($request['search']) && $request['search']->value != '' ) {
			$str = $request['search']->value;

			for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
				$requestColumn = (array)$request['columns'][$i];
				$columnIdx = array_search( $requestColumn['data'], $dtColumns );
				$column = $columns[ $i ];

				if ( $requestColumn['searchable'] == 'true' ) {
					$binding = self::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
					//$globalSearch[] = "`".$column['db']."` LIKE ".$binding;
					if(strpos($column['db'],"_id")!==false){      
						$campo2= "nombre";
						$tabla2 = str_replace("_id", "", $column['db']);
						$fields_table = $this->dataSchema($tabla2); 
            			foreach ($fields_table as $value) {
                			if($value->DATA_TYPE=="varchar"){
                    			$campo2 = $value->COLUMN_NAME;                             
                    			break;
                			}                
            			}           
            			$tabla2as = $tabla2."as";
						$globalSearch[] = $tabla2as.".".$campo2." LIKE '%$str%'";

					}
					elseif($column['type']== "DATE"){  
						if(strlen($str)==10){
							$str = date("Y-m-d", strtotime( $str));
						}
						$globalSearch[] = $table.".".$column['db']." LIKE '%$str%'";
					}
					else{
						$globalSearch[] = $table.".".$column['db']." LIKE '%$str%'";
					}
				}
			}
		}
		// Individual column filtering
		if ( isset( $request['columns'] ) ) {
			for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
				$requestColumn = (array) $request['columns'][$i];
				$columnIdx = array_search( $requestColumn['data'], $dtColumns );
				/* if($columnIdx ){
					$column = $columns[ $columnIdx ];
				} */
				$str = $requestColumn['search']->value;

				if ( $requestColumn['searchable'] == 'true' &&
				 $str != '' ) {
					$binding = self::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
					$columnSearch[] = "`".$column['db']."` LIKE '%$str%'";
				}
			}
		}

		// Combine the filters into a single string
		$where = '';

		if ( count( $globalSearch ) ) {
			$where = '('.implode(' OR ', $globalSearch).')';
		}

		if ( count( $columnSearch ) ) {
			$where = $where === '' ?
				implode(' AND ', $columnSearch) :
				$where .' AND '. implode(' AND ', $columnSearch);
		}

		if ( $where !== '' ) {
			$where = 'WHERE '.$where;
		}

		return $where;
	}


	/**
	 * Perform the SQL queries needed for an server-side processing requested,
	 * utilising the helper functions of this class, limit(), order() and
	 * filter() among others. The returned array is ready to be encoded as JSON
	 * in response to an SSP request, or can be modified if needed before
	 * sending back to the client.
	 *
	 *  @param  array $request Data sent to server by DataTables
	 *  @param  array|PDO $conn PDO connection resource or connection parameters array
	 *  @param  string $table SQL table to query
	 *  @param  string $primaryKey Primary key of the table
	 *  @param  array $columns Column information array
	 *  @return array          Server-side processing response array
	 */
	function simple ( $request, $table, $primaryKey, $columns )
	{
		$bindings = array();

		// Build the SQL query string from the request
		$limit = self::limit( $request, $columns );
		$order = self::order( $request, $columns );
		$where = self::filter( $request, $columns, $bindings, $table );
		// armar consulta cruzada para datatable
		if(!$where){
			$where = "WHERE 1=1 ".$this->filtro;
		}
		$sqlArmado = $this->armarConsulta($table,$where,$order,$limit, $columns);
		$data = $this->modularModel->sqlVariosReturn($sqlArmado,'A');
		//  
		$sqlArmadoCount = $this->armarConsultaCount($table,$where, $primaryKey, $columns);              
        $resFilterLength = $this->modularModel->sqlVariosReturn($sqlArmadoCount,'A');
        //$this->returnData($resFilterLength,200);
		$recordsFiltered = $resFilterLength[0]['cant'];
		
        $resTotalLength = $this->modularModel->sqlVariosReturn("SELECT COUNT(`{$primaryKey}`) as cant
        FROM   `$table`",'A');
		$recordsTotal = $resTotalLength[0]['cant'];
			
		/*
		 * Output
		 */
 
		return array(
			"draw"            => isset ( $request['draw'] ) ?
				intval( $request['draw'] ) :
				0,
			"recordsTotal"    => intval( $recordsTotal ),
			"recordsFiltered" => intval( $recordsFiltered ),			
			"data"            => self::data_output( $columns, $data )
		);
	}


	/**
	 * The difference between this method and the `simple` one, is that you can
	 * apply additional `where` conditions to the SQL queries. These can be in
	 * one of two forms:
	 *
	 * * 'Result condition' - This is applied to the result set, but not the
	 *   overall paging information query - i.e. it will not effect the number
	 *   of records that a user sees they can have access to. This should be
	 *   used when you want apply a filtering condition that the user has sent.
	 * * 'All condition' - This is applied to all queries that are made and
	 *   reduces the number of records that the user can access. This should be
	 *   used in conditions where you don't want the user to ever have access to
	 *   particular records (for example, restricting by a login id).
	 *
	 *  @param  array $request Data sent to server by DataTables
	 *  @param  array|PDO $conn PDO connection resource or connection parameters array
	 *  @param  string $table SQL table to query
	 *  @param  string $primaryKey Primary key of the table
	 *  @param  array $columns Column information array
	 *  @param  string $whereResult WHERE condition to apply to the result set
	 *  @param  string $whereAll WHERE condition to apply to all queries
	 *  @return array          Server-side processing response array
	 */
	function complex ( $request, $table, $primaryKey, $columns, $whereResult=null, $whereAll=null )
	{
		$bindings = array();
		$localWhereResult = array();
		$localWhereAll = array();
		$whereAllSql = '';

		// Build the SQL query string from the request
		$limit = self::limit( $request, $columns );
		$order = self::order( $request, $columns );
		$where = self::filter( $request, $columns, $bindings, $table );

		$whereResult = self::_flatten( $whereResult );
		$whereAll = self::_flatten( $whereAll );

		if ( $whereResult ) {
			$where = $where ?
				$where .' AND '.$whereResult :
				'WHERE '.$whereResult;
		}

		if ( $whereAll ) {
			$where = $where ?
				$where .' AND '.$whereAll :
				'WHERE '.$whereAll;

			$whereAllSql = 'WHERE '.$whereAll;
		}

		// armar consulta cruzada para datatable
		$sqlArmado = $this->armarConsulta($table,$where,$order,$limit, null);
		$data = $this->modularModel->sqlVariosReturn($sqlArmado,'A');
		//                
		// estaba $resFilterLength = count($data);
		$resFilterLength = $this->modularModel->sqlVariosReturn("SELECT COUNT(`{$primaryKey}`) as cant
        FROM   `$table`
        $where",'A');
        //$this->returnData($resFilterLength,200);
		$recordsFiltered = $resFilterLength[0]['cant'];
		
		/* $this->modularModel->sqlVariosReturn("SELECT COUNT(`{$primaryKey}`) as cant
        FROM   `$table`
        $where",'A'); */
        //$this->returnData($resFilterLength,200);
		// estaba $recordsFiltered = $resFilterLength;

		// Total data set length
	/* 	$resTotalLength = self::sql_exec( $db,
			"SELECT COUNT(`{$primaryKey}`)
			 FROM   `$table`"
        ); */
        $resTotalLength = $this->modularModel->sqlVariosReturn("SELECT COUNT(`{$primaryKey}`) as cant
        FROM   `$table`",'A');
		$recordsTotal = $resTotalLength[0]['cant'];

		/*
		 * Output
		 */
		return array(
			"draw"            => isset ( $request['draw'] ) ?
				intval( $request['draw'] ) :
				0,
			"recordsTotal"    => intval( $recordsTotal ),
			"recordsFiltered" => intval( $recordsFiltered ),
			"data"            => self::data_output( $columns, $data )
		);
	}


	/**
	 * Connect to the database
	 *
	 * @param  array $sql_details SQL server connection details array, with the
	 *   properties:
	 *     * host - host name
	 *     * db   - database name
	 *     * user - user name
	 *     * pass - user password
	 * @return resource Database connection handle
	 */
	static function sql_connect ( $sql_details )
	{
		try {
			$db = @new PDO(
				"mysql:host={$sql_details['host']};dbname={$sql_details['db']}",
				$sql_details['user'],
				$sql_details['pass'],
				array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION )
			);
		}
		catch (PDOException $e) {
			self::fatal(
				"An error occurred while connecting to the database. ".
				"The error reported by the server was: ".$e->getMessage()
			);
		}

		return $db;
	}





	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Internal methods
	 */

	/**
	 * Throw a fatal error.
	 *
	 * This writes out an error message in a JSON string which DataTables will
	 * see and show to the user in the browser.
	 *
	 * @param  string $msg Message to send to the client
	 */
	static function fatal ( $msg )
	{
		echo json_encode( array( 
			"error" => $msg
		) );

		exit(0);
	}

	/**
	 * Create a PDO binding key which can be used for escaping variables safely
	 * when executing a query with sql_exec()
	 *
	 * @param  array &$a    Array of bindings
	 * @param  *      $val  Value to bind
	 * @param  int    $type PDO field type
	 * @return string       Bound key to be used in the SQL where this parameter
	 *   would be used.
	 */
	static function bind ( &$a, $val, $type )
	{
		$key = ':binding_'.count( $a );

		$a[] = array(
			'key' => $key,
			'val' => $val,
			'type' => $type
		);

		return $key;
	}


	/**
	 * Pull a particular property from each assoc. array in a numeric array, 
	 * returning and array of the property values from each item.
	 *
	 *  @param  array  $a    Array to get data from
	 *  @param  string $prop Property to read
	 *  @return array        Array of property values
	 */
	static function pluck ( $a, $prop )
	{
		$out = array();

		for ( $i=0, $len=count($a) ; $i<$len ; $i++ ) {
			$out[] = $a[$i][$prop];
		}

		return $out;
	}


	/**
	 * Return a string from an array or a string
	 *
	 * @param  array|string $a Array to join
	 * @param  string $join Glue for the concatenation
	 * @return string Joined string
	 */
	static function _flatten ( $a, $join = ' AND ' )
	{
		if ( ! $a ) {
			return '';
		}
		else if ( $a && is_array($a) ) {
			return implode( $join, $a );
		}
		return $a;
	}

	private function dataSchema2($table){
        $base = BASE;
        $sql = "SELECT COLUMN_NAME,DATA_TYPE,CHARACTER_MAXIMUM_LENGTH,IS_NULLABLE 
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = '".$base."' AND TABLE_NAME = '$table'";            
        return $this->modularModel->sqlVariosReturn($sql,'O');
    }
      
    
	public function getDataTable(){

        $table = $this->table;        

        $fields_table = $this->dataSchema($table);                 
        if($fields_table!==false){
            //remove first element "id"
            $columns = array();
            // botones dataTable
            //$botones = $this->armar_botones($this->table, 0);	
            //$botones_cabecera = $this->armar_botones($this->table, 1);	            
			//
			$data = new stdClass();
			$settings = new stdClass();
			$delete = new stdClass();
			$add = new stdClass();
			$edit = new stdClass();
			$actions = new stdClass();
			$columns = new stdClass();
			$mode = new stdClass();

			$delete->confirmDelete = true;
		  	$delete->deleteButtonContent = 'Delete data';
			$delete->saveButtonContent = 'save';
			$delete->cancelButtonContent = 'cancel';
			$add->confirmCreate = false;
			$edit->confirmSave = false;
			$actions->position = 'left';
			$actions->add = false;
			$mode = 'external';
			$settings->delete = $delete;
			$settings->add = $add;
			$settings->edit = $edit;
			$settings->actions = $actions;
			$settings->mode = $mode;
			
            foreach ($fields_table as $value) {				
                $col = new stdClass();
				$text = str_replace("_", " ",$value->COLUMN_NAME);
				$text = str_replace("Codigo ", "",$text);
				$text = str_replace("SN ", "",$text);
				$text = strtoupper($text);
				$col->title = $text;
				$columns->{$value->COLUMN_NAME} = $col;                
            }
			$settings->columns = $columns;
            //
			$data = $this->makeTableData();
			//
            $this->response->settings = $settings;
            $this->response->data = $data;            
            $this->returnData($this->response,200);  
        }   
        else{
            $this->response->data = "Error";
            $this->returnData($this->response,400); 
        }    
    }
	
	public function getRegistersFromTable(){
		$tables = $this->modularModel->getRegistrosTabla($this->table,'','','N');
		if ($tables) {
			$this->response->status = "success";
			$this->response->tables = $tables;
			$this->returnData($this->response,200);
		}else{
			$this->response->status = "not found";
			$this->returnData($this->response,404);
		}
	}

	public function makeTableData(){
        // Table's primary key
        $primaryKey = 'id'.$this->table;		
				
		$columns = array();

		$fields_table = $this->dataSchema($this->table);
		
		foreach ($fields_table as $value) {
			$field_name = $value->COLUMN_NAME;
			$field_type = $value->DATA_TYPE;
			array_push($columns,array( 'db' => $field_name, 'dt' => $field_name, 'type' => $field_type ),);			                
		}       
		//$this->returnData($this->data->dataTable,200);
		$order = "";
		$limit = "";
		$where = "WHERE 1=1 ";
		
		$sqlArmado = $this->armarConsulta($this->table,$where,$order,$limit,$columns);
		$data = $this->modularModel->sqlVariosReturn($sqlArmado,'O');
		//		
		return $data;       
    }
	
}

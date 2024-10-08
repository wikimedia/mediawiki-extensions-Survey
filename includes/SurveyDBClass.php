<?php

use MediaWiki\MediaWikiServices;

/**
 * Abstract base class for representing objects that are stored in some DB table.
 *
 * @since 0.1
 *
 * @file SurveyDBClass.php
 * @ingroup Survey
 *
 * @license GPL-3.0-or-later
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
abstract class SurveyDBClass {
	/**
	 * The fields of the object.
	 * field name (w/o prefix) => value
	 *
	 * @since 0.1
	 * @var array
	 */
	protected $fields = [];

	/**
	 * Constructor.
	 *
	 * @since 0.1
	 *
	 * @param array|null $fields
	 * @param bool $loadDefaults
	 */
	public function __construct( $fields, $loadDefaults = false ) {
		$this->setField( static::getIDField(), null );

		if ( !is_array( $fields ) ) {
			$fields = [];
		}

		if ( $loadDefaults ) {
			$fields = array_merge( static::getDefaults(), $fields );
		}

		$this->setFields( $fields );
	}

	/**
	 * Returns an array with the fields and their types this object contains.
	 * This corresponds directly to the fields in the database, without prefix.
	 *
	 * field name => type
	 *
	 * Allowed types:
	 * * id
	 * * str
	 * * int
	 * * bool
	 * * array
	 *
	 * @since 0.1
	 *
	 * @return array
	 */
	protected static function getFieldTypes() {
		return [];
	}

	/**
	 * Returns an array with the fields and their descriptions.
	 *
	 * field name => field description
	 *
	 * @since 0.1
	 *
	 * @return array
	 */
	public static function getFieldDescriptions() {
		return [];
	}

	/**
	 * Returns the name of the database table objects of this type are stored in.
	 *
	 * @since 0.1
	 *
	 * @throws MWException
	 * @return string
	 */
	public static function getDBTable() {
		throw new MWException( 'Class did not implement getDBTable' );
	}

	/**
	 * Gets the db field prefix.
	 *
	 * @since 0.1
	 *
	 * @throws MWException
	 * @return string
	 */
	protected static function getFieldPrefix() {
		throw new MWException( 'Class did not implement getFieldPrefix' );
	}

	/**
	 * Returns the name of the id db field, without prefix.
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	protected static function getIDField() {
		return 'id';
	}

	/**
	 * Get a new instance of the class from a database result.
	 *
	 * @since 0.1
	 *
	 * @param array $result
	 *
	 * @return SurveyDBClass
	 */
	public static function newFromDBResult( $result ) {
		$result = (array)$result;
		$data = [];
		$idFieldLength = strlen( static::getFieldPrefix() );

		foreach ( $result as $name => $value ) {
			$data[substr( $name, $idFieldLength )] = $value;
		}

		return static::newFromArray( $data );
	}

	/**
	 * Get a new instance of the class from an array.
	 *
	 * @since 0.1
	 *
	 * @param array $data
	 * @param bool $loadDefaults
	 *
	 * @return SurveyDBClass
	 */
	public static function newFromArray( array $data, $loadDefaults = false ) {
		return new static( $data, $loadDefaults );
	}

	/**
	 * Selects the specified fields of the records matching the provided
	 * conditions. Field names get prefixed.
	 *
	 * @since 0.1
	 *
	 * @param array|null $fields
	 * @param array $conditions
	 * @param array $options
	 *
	 * @return array of self
	 */
	public static function select( $fields = null, array $conditions = [], array $options = [] ) {
		if ( $fields === null ) {
			$fields = array_keys( static::getFieldTypes() );
		}

		$result = static::rawSelect(
			static::getPrefixedFields( $fields ),
			static::getPrefixedValues( $conditions ),
			$options
		);

		$objects = [];

		foreach ( $result as $record ) {
			$objects[] = static::newFromDBResult( $record );
		}

		return $objects;
	}

	/**
	 * Selects the specified fields of the first matching record.
	 * Field names get prefixed.
	 *
	 * @since 0.1
	 *
	 * @param array|null $fields
	 * @param array $conditions
	 * @param array $options
	 *
	 * @return self|bool false
	 */
	public static function selectRow( $fields = null, array $conditions = [], array $options = [] ) {
		$options['LIMIT'] = 1;

		$objects = static::select( $fields, $conditions, $options );

		return count( $objects ) > 0 ? $objects[0] : false;
	}

	/**
	 * Returns if there is at least one record matching the provided conditions.
	 * Condition field names get prefixed.
	 *
	 * @since 0.1
	 *
	 * @param array $conditions
	 *
	 * @return bool
	 */
	public static function has( array $conditions = [] ) {
		return static::selectRow( [ static::getIDField() ], $conditions ) !== false;
	}

	/**
	 * Returns the amount of matching records.
	 * Condition field names get prefixed.
	 *
	 * @since 0.1
	 *
	 * @param array $conditions
	 * @param array $options
	 *
	 * @return int
	 */
	public static function count( array $conditions = [], array $options = [] ) {
		$res = static::rawSelect(
			[ 'COUNT(*) AS rowcount' ],
			static::getPrefixedValues( $conditions ),
			$options
		)->fetchObject();

		return $res->rowcount;
	}

	/**
	 * Selects the specified fields of the records matching the provided
	 * conditions. Field names do NOT get prefixed.
	 *
	 * @since 0.1
	 *
	 * @param array|null $fields
	 * @param array $conditions
	 * @param array $options
	 *
	 * @return ResultWrapper
	 */
	public static function rawSelect( $fields = null, array $conditions = [], array $options = [] ) {
		$dbr = MediaWikiServices::getInstance()->getDBLoadBalancer()->getConnection( DB_REPLICA );

		return $dbr->select(
			static::getDBTable(),
			$fields,
			count( $conditions ) == 0 ? '' : $conditions,
			'',
			$options
		);
	}

	/**
	 * @param array $values
	 * @param array $conditions
	 * @return bool
	 */
	public static function update( array $values, array $conditions = [] ) {
		$dbw = MediaWikiServices::getInstance()->getDBLoadBalancer()->getConnection( DB_PRIMARY );

		return $dbw->update(
			static::getDBTable(),
			static::getPrefixedValues( $values ),
			static::getPrefixedValues( $conditions )
		);
	}

	/**
	 * Writes the answer to the database, either updating it
	 * when it already exists, or inserting it when it doesn't.
	 *
	 * @since 0.1
	 *
	 * @return bool Success indicator
	 */
	public function writeToDB() {
		if ( $this->hasIdField() ) {
			return $this->updateInDB();
		} else {
			return $this->insertIntoDB();
		}
	}

	/**
	 * Updates the object in the database.
	 *
	 * @since 0.1
	 *
	 * @return bool Success indicator
	 */
	protected function updateInDB() {
		$dbw = MediaWikiServices::getInstance()->getDBLoadBalancer()->getConnection( DB_PRIMARY );

		return $dbw->update(
			$this->getDBTable(),
			$this->getWriteValues(),
			[ static::getFieldPrefix() . static::getIDField() => $this->getId() ]
		);
	}

	/**
	 * Inserts the object into the database.
	 *
	 * @since 0.1
	 *
	 * @return bool Success indicator
	 */
	protected function insertIntoDB() {
		$dbw = MediaWikiServices::getInstance()->getDBLoadBalancer()->getConnection( DB_PRIMARY );

		$result = $dbw->insert(
			static::getDBTable(),
			static::getWriteValues()
		);

		$this->setField( static::getIDField(), $dbw->insertId() );

		return $result;
	}

	/**
	 * Removes the object from the database.
	 *
	 * @since 0.1
	 *
	 * @return bool Success indicator
	 */
	public function removeFromDB() {
		$dbw = MediaWikiServices::getInstance()->getDBLoadBalancer()->getConnection( DB_PRIMARY );

		$sucecss = $dbw->delete(
			static::getDBTable(),
			[ static::getFieldPrefix() . static::getIDField() => $this->getId() ]
		);

		if ( $sucecss ) {
			$this->setField( static::getIDField(), null );
		}

		return $sucecss;
	}

	/**
	 * Return the names of the fields.
	 *
	 * @since 0.1
	 *
	 * @return array
	 */
	public function getFields() {
		return $this->fields;
	}

	/**
	 * Return the names of the fields.
	 *
	 * @since 0.1
	 *
	 * @return array
	 */
	public static function getFieldNames() {
		return array_keys( static::getFieldTypes() );
	}

	/**
	 * Return the names of the fields.
	 *
	 * @since 0.1
	 *
	 * @return array
	 */
	public function getSetFieldNames() {
		return array_keys( $this->fields );
	}

	/**
	 * Sets the value of a field.
	 * Strings can be provided for other types,
	 * so this method can be called from unserialization handlers.
	 *
	 * @since 0.1
	 *
	 * @param string $name
	 * @param mixed $value
	 *
	 * @throws MWException
	 */
	public function setField( $name, $value ) {
		$fields = static::getFieldTypes();

		if ( array_key_exists( $name, $fields ) ) {
			switch ( $fields[$name] ) {
				case 'int':
					$value = (int)$value;
					break;
				case 'bool':
					if ( is_string( $value ) ) {
						$value = $value !== '0';
					} elseif ( is_int( $value ) ) {
						$value = $value !== 0;
					}
					break;
				case 'array':
					if ( is_string( $value ) ) {
						$value = unserialize( $value );
					}
					break;
				case 'id':
					if ( is_string( $value ) ) {
						$value = (int)$value;
					}
					break;
			}

			$this->fields[$name] = $value;
		} else {
			throw new MWException( 'Attempted to set unknonw field ' . $name );
		}
	}

	/**
	 * Gets the value of a field.
	 *
	 * @since 0.1
	 *
	 * @param string $name
	 *
	 * @throws MWException
	 * @return mixed
	 */
	public function getField( $name ) {
		if ( $this->hasField( $name ) ) {
			return $this->fields[$name];
		} else {
			throw new MWException( 'Attempted to get not-set field ' . $name );
		}
	}

	/**
	 * Remove a field.
	 *
	 * @since 0.1
	 *
	 * @param string $name
	 */
	public function removeField( $name ) {
		unset( $this->fields[$name] );
	}

	/**
	 * Returns the objects database id.
	 *
	 * @since 0.1
	 *
	 * @return int|null
	 */
	public function getId() {
		return $this->getField( static::getIDField() );
	}

	/**
	 * Sets the objects database id.
	 *
	 * @since 0.1
	 *
	 * @param int|null $id
	 */
	public function setId( $id ) {
		$this->setField( static::getIDField(), $id );
	}

	/**
	 * Gets if a certain field is set.
	 *
	 * @since 0.1
	 *
	 * @param string $name
	 *
	 * @return bool
	 */
	public function hasField( $name ) {
		return array_key_exists( $name, $this->fields );
	}

	/**
	 * Gets if the object can take a certain field.
	 *
	 * @since 0.1
	 *
	 * @param string $name
	 *
	 * @return bool
	 */
	public static function canHasField( $name ) {
		return array_key_exists( $name, static::getFieldTypes() );
	}

	/**
	 * Gets if the id field is set.
	 *
	 * @since 0.1
	 *
	 * @return bool
	 */
	public function hasIdField() {
		return $this->hasField( static::getIDField() )
			&& $this->getField( static::getIDField() ) !== null;
	}

	/**
	 * Sets multiple fields.
	 *
	 * @since 0.1
	 *
	 * @param array $fields The fields to set
	 * @param bool $override Override already set fields with the provided values?
	 */
	public function setFields( array $fields, $override = true ) {
		foreach ( $fields as $name => $value ) {
			if ( $override || !$this->hasField( $name ) ) {
				$this->setField( $name, $value );
			}
		}
	}

	/**
	 * Gets the fields => values to write to the table.
	 *
	 * @since 0.1
	 *
	 * @return array
	 */
	protected function getWriteValues() {
		$values = [];

		foreach ( static::getFieldTypes() as $name => $type ) {
			if ( array_key_exists( $name, $this->fields ) ) {
				$value = $this->fields[$name];

				switch ( $type ) {
					case 'array':
						$value = serialize( (array)$value );
				}

				$values[static::getFieldPrefix() . $name] = $value;
			}
		}

		return $values;
	}

	/**
	 * Takes in a field or array of fields and returns an
	 * array with their prefixed versions, ready for db usage.
	 *
	 * @since 0.1
	 *
	 * @param array|string $fields
	 *
	 * @return array
	 */
	public static function getPrefixedFields( $fields ) {
		$fields = (array)$fields;

		foreach ( $fields as &$field ) {
			$field = static::getFieldPrefix() . $field;
		}

		return $fields;
	}

	/**
	 * Takes in a field and returns an it's prefixed version, ready for db usage.
	 *
	 * @since 0.1
	 *
	 * @param string $field
	 *
	 * @return string
	 */
	public static function getPrefixedField( $field ) {
		return static::getFieldPrefix() . $field;
	}

	/**
	 * Takes in an associative array with field names as keys and
	 * their values as value. The field names are prefixed with the
	 * db field prefix.
	 *
	 * @since 0.1
	 *
	 * @param array $values
	 *
	 * @return array
	 */
	public static function getPrefixedValues( array $values ) {
		$prefixedValues = [];

		foreach ( $values as $field => $value ) {
			$prefixedValues[static::getFieldPrefix() . $field] = $value;
		}

		return $prefixedValues;
	}

	/**
	 * Serializes the survey to an associative array which
	 * can then easily be converted into JSON or similar.
	 *
	 * @since 0.1
	 *
	 * @param array|null $fields
	 *
	 * @return array
	 */
	public function toArray( $fields = null ) {
		$data = [];
		$setFields = [];

		if ( !is_array( $fields ) ) {
			$setFields = $this->getSetFieldNames();
		} else {
			foreach ( $fields as $field ) {
				if ( $this->hasField( $field ) ) {
					$setFields[] = $field;
				}
			}
		}

		foreach ( $setFields as $field ) {
			$data[$field] = $this->getField( $field );
		}

		return $data;
	}

	/**
	 * @param bool $override
	 */
	public function loadDefaults( $override = true ) {
		$this->setFields( static::getDefaults(), $override );
	}

	/**
	 * Returns a list of default field values.
	 * field name => field value
	 *
	 * @since 0.1
	 *
	 * @return array
	 */
	public static function getDefaults() {
		return [];
	}

	/**
	 * Get API parameters for the fields supported by this object.
	 *
	 * @since 0.1
	 *
	 * @param bool $requireParams
	 *
	 * @return array
	 */
	public static function getAPIParams( $requireParams = true ) {
		$typeMap = [
			'id' => 'integer',
			'int' => 'integer',
			'str' => 'string',
			'bool' => 'integer',
			'array' => 'string'
		];

		$params = [];
		$defaults = static::getDefaults();

		foreach ( static::getFieldTypes() as $field => $type ) {
			if ( $field == static::getIDField() ) {
				continue;
			}

			$hasDefault = array_key_exists( $field, $defaults );

			$params[$field] = [
				ApiBase::PARAM_TYPE => $typeMap[$type],
				ApiBase::PARAM_REQUIRED => $requireParams && !$hasDefault
			];

			if ( $type == 'array' ) {
				$params[$field][ApiBase::PARAM_ISMULTI] = true;
			}

			if ( $hasDefault ) {
				$default = is_array( $defaults[$field] ) ? implode( '|', $defaults[$field] ) : $defaults[$field];
				$params[$field][ApiBase::PARAM_DFLT] = $default;
			}
		}

		return $params;
	}

	/**
	 * Takes an array of field name => field value and
	 * filters it on valid field names.
	 *
	 * @since 0.1
	 *
	 * @param array $conditions
	 * @param bool|int $id false
	 *
	 * @return array
	 */
	public static function getValidFields( array $conditions, $id = false ) {
		$validFields = [];

		$fields = static::getFieldTypes();

		foreach ( $conditions as $name => $value ) {
			if ( array_key_exists( $name, $fields ) ) {
				$validFields[$name] = $value;
			}
		}

		if ( $id !== false ) {
			$validParams[static::getIDField()] = $id;
		}

		return $validFields;
	}

}

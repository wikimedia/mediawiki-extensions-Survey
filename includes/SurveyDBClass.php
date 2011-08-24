<?php

/**
 * Abstract base class for representing objects that are stored in some DB table.
 * 
 * @since 0.1
 * 
 * @file SurveyDBClass.php
 * @ingroup Survey
 * 
 * @licence GNU GPL v3 or later
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
abstract class SurveyDBClass {
	
	/**
	 * Returns the name of the database table objects of this type are stored in.
	 * 
	 * @since 0.1
	 * 
	 * @return string
	 */
	protected abstract function getDBTable();
	
	/**
	 * Returns the name of the id field.
	 * 
	 * @since 0.1
	 * 
	 * @return string
	 */
	protected abstract function getIDField();
	
	/**
	 * Gets the fields => values to write to the database table. 
	 * 
	 * @since 0.1
	 * 
	 * @return array
	 */
	protected abstract function getWriteValues();
	
	/**
	 * The ID of the question DB record, or null if not inserted yet.
	 * 
	 * @since 0.1
	 * @var integer|null
	 */
	protected $id;
	
	/**
	 * Writes the answer to the database, either updating it
	 * when it already exists, or inserting it when it doesn't.
	 * 
	 * @since 0.1
	 * 
	 * @return boolean Success indicator
	 */
	public function writeToDB() {
		if ( is_null( $this->id ) ) {
			return $this->insertIntoDB();
		}
		else {
			return  $this->updateInDB();
		}
	}
	
	/**
	 * Updates the object in the database.
	 * 
	 * @since 0.1
	 * 
	 * @return boolean Success indicator
	 */
	protected function updateInDB() {
		$dbw = wfGetDB( DB_MASTER );
		
		return $dbw->update(
			$this->getDBTable(),
			$this->getWriteValues(),
			array( $this->getIDField() => $this->id )
		);
	}
	
	/**
	 * Inserts the object into the database.
	 * 
	 * @since 0.1
	 * 
	 * @return boolean Success indicator
	 */
	protected function insertIntoDB() {
		$dbw = wfGetDB( DB_MASTER );
		
		$result = $dbw->insert(
			$this->getDBTable(),
			$this->getWriteValues()
		);
		
		$this->id = $dbw->insertId();
		
		return $result;
	}
	
	/**
	 * Removes the object from the database.
	 * 
	 * @since 0.1
	 * 
	 * @return boolean Success indicator
	 */
	public function removeFromDB() {
		$dbw = wfGetDB( DB_MASTER );
		
		$sucecss = $dbw->delete(
			$this->getDBTable(),
			array( $this->getIDField() => $this->id )
		);
		
		if ( $sucecss ) {
			$this->id = null;
		}
		
		return $sucecss;
	}
	
	/**
	 * Returns the objects database id.
	 * 
	 * @since 0.1
	 * 
	 * @return integer|null
	 */
	public function getId() {
		return $this->id;
	}
	
}

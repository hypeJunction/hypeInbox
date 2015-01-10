<?php

namespace hypeJunction\Inbox;

use stdClass;

class AccessCollection {

	private $dbprefix;
	protected $members = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->dbprefix = elgg_get_config('dbprefix');
	}

	/**
	 * Create a new collection from data
	 * 
	 * @param mixed $data Initial data set
	 * @return AccessCollection
	 */
	public static function create($data) {
		$acl = new AccessCollection();
		return $acl->add($data);
	}

	/**
	 * Add member(s) to collection
	 *
	 * @param mixed $members A member or an array of members
	 * @return AccessCollection
	 */
	public function add($members = null) {
		array_push($this->members, $members);
		return $this;
	}

	/**
	 * Returns unqiue array of member guids added to collection
	 * @return array
	 */
	public function members() {
		$members = $this->group()->add($this->members)->guids();
		return array_unique($members);
	}

	/**
	 * Returns an access hash calculated based on sender and recipients
	 * Used for creating an access collection
	 * @return string
	 */
	public function calcAccessHash() {
		return sha1(implode(':', $this->members()));
	}

	/**
	 * Returns an access collection id suitable for saving entities in way
	 * accessible by all members
	 * @return int
	 */
	public function getCollectionId() {
		$acl_hash = $this->calcAccessHash();
		$collection_id = $this->getCollectionIdByName($acl_hash);
		if (!$collection_id) {
			return $this->createCollection($acl_hash, $this->members());
		}
		return $collection_id;
	}

	/**
	 * Get access collection by its name from database
	 * 
	 * @param string $name Collection name
	 * @return stdClass
	 */
	public function getCollectionIdByName($name) {
		$name = sanitize_string($name);
		$query = "SELECT * FROM {$this->dbprefix}access_collections
					WHERE name = '$name'";
		$collection = get_data_row($query);
		return ($collection) ?  $collection->id : 0;
	}

	/**
	 * Creates a new access collection and adds members
	 *
	 * @param string $name    Name of the collection
	 * @param array  $members Members to add to the collection
	 * @return int ID of the created collection
	 */
	public function createCollection($name, $members = array()) {
		$site = elgg_get_site_entity();
		$acl_id = create_access_collection($name, $site->guid);
		if (!empty($members)) {
			update_access_collection($acl_id, $members);
		}
		return $acl_id;
	}

	/**
	 * Returns a new entity group
	 * @return Group
	 */
	public function group() {
		return new Group;
	}

}

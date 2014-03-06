<?php
/**
 * izicms
 * 
 * LICENSE
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE Version 2 
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-2.0.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@izicms.com so we can send you a copy immediately.
 * 
 * @copyright	Copyright (c) 2009-2010 izicms Team (http://www.izicms.vn)
 * @license		http://www.gnu.org/licenses/gpl-2.0.txt GNU GENERAL PUBLIC LICENSE Version 2
 * @version 	$Id: Role.php 5455 2010-09-17 02:16:37Z leha $
 * @since		2.0.5
 */

class Core_Models_Dao_Pgsql_Role extends Izi_Model_Dao
	implements Core_Models_Interface_Role
{
	public function convert($entity)
	{
		return new Core_Models_Role($entity); 
	}
	
	public function getAclRoles()
	{
		$sql  = "SELECT * FROM " . $this->_prefix . "core_role AS r
				LEFT JOIN " . $this->_prefix . "core_role_inheritance AS i
				ON r.role_id = i.child_id
				ORDER BY role_id DESC, ordering DESC";
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return new Izi_Model_RecordSet($rows, $this);
	}
	
	public function getRoles()
	{
		$sql  = "SELECT * FROM " . $this->_prefix . "core_role
				ORDER BY role_id DESC";
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return new Izi_Model_RecordSet($rows, $this);
	}
	
	public function getById($id) 
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "core_role
						WHERE role_id = %s LIMIT 1",
						pg_escape_string($id));
		$rs  = pg_query($sql);
		$return = (0 == pg_num_rows($rs)) ? null : new Core_Models_Role(pg_fetch_object($rs));
		pg_free_result($rs);
		return $return;
	}
	
	public function add($role) 
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "core_role (name, description, locked)
						VALUES ('%s', '%s', '%s')
						RETURNING role_id",
						pg_escape_string($role->name),
						pg_escape_string($role->description),
						pg_escape_string($role->locked));
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		pg_free_result($rs);		
		return $row->role_id;
	}
	
	public function toggleLock($id) 
	{
		$sql = sprintf("UPDATE " . $this->_prefix . "core_role 
						SET locked = 1 - locked 
						WHERE role_id = %s", 
						pg_escape_string($id));
		$rs  = pg_query($sql);
		return pg_affected_rows($rs);
	}
	
	public function delete($id) 
	{
		return pg_delete($this->_conn, $this->_prefix . 'core_role', 
						array(
							'role_id' => $id,
						));	
	}
	
	public function getRolesIncludeUser()
	{
		$sql  = "SELECT r.*, u2.num_users
				FROM " . $this->_prefix . "core_role AS r
				LEFT JOIN
				(
					SELECT role_id, COUNT(*) AS num_users
					FROM " . $this->_prefix . "core_user AS u
					WHERE role_id IN (SELECT role_id FROM " . $this->_prefix . "core_role)
					GROUP BY role_id
				) AS u2
					ON r.role_id = u2.role_id";
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return new Izi_Model_RecordSet($rows, $this);
	}
	
	public function countUsers($roleId)
	{
		$sql = sprintf("SELECT COUNT(*) AS num_users FROM " . $this->_prefix . "core_user 
						WHERE role_id = %s LIMIT 1",
						pg_escape_string($roleId));
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		pg_free_result($rs);
		return $row->num_users;
	}
}

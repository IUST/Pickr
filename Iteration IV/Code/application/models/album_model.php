<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Album_Model extends CI_Model
{ // must have not the same name with it's controller name! :)
	private $table_name = 'album';
	private $user_album_table_name = 'user_album';
	private $picture_table_name = 'picture';
	private $picture_album_table_name = 'picture_album';

	function __construct() {
		parent::__construct();
	}

	public function is_album_exist($user_id, $album_name) {
		$albums_name = $this->get_all_album_name($user_id);
		$clear_albums_name = array();
		foreach ($albums_name as $name) { // clean names for URL
			$name = preg_replace("![^a-z0-9_]+!i", "-", strtolower($name));
			array_push($clear_albums_name, $name);
		}
		if (in_array($album_name, $clear_albums_name)) {
			return TRUE;
		}
		return FALSE;
	}

	public function get_album_information($user_id, $album_name) {
		$albums_info = array();
		$this->db->select('*');
		$query = $this->db->get($this->table_name);
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $album) {
		        if ($album_name == preg_replace("![^a-z0-9_]+!i", "-", strtolower($album->name))) {
		        	array_push($albums_info, array('id' => $album->id,
		        								   'name' => $album->name,
		        								   'description' => $album->description));
		        }
		    }
		}
		$final_album_info = array();
		$this->db->where('user_id', $user_id);
		$query = $this->db->get($this->user_album_table_name);
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				foreach ($albums_info as $album_info) {
					if ($album_info['id'] == $row->album_id) {
						array_push($final_album_info, $album_info);
						return $final_album_info;
					}
				}
			}
		}
		return $final_album_info;
	}

	public function delete_user_album($user_id, $album_name) {
		$albums_id = array();
		$this->db->select('*');
		$query = $this->db->get($this->table_name);
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $album) {
		        if ($album_name == preg_replace("![^a-z0-9_]+!i", "-", strtolower($album->name))) {
		        	array_push($albums_id, $album->id);
		        }
		    }
		}
		$final_album_id = 0;
		$all_albums_id = $this->get_all_album_id($user_id);
		foreach ($all_albums_id as $album_id) {
			if (in_array($album_id, $albums_id)) {
				$final_album_id = $album_id;
				break;
			}
		}
		$this->db->where('id', $final_album_id);
		$this->db->delete($this->table_name);
		return ($this->db->affected_rows() > 0) ? TRUE : FALSE;
	}

	public function rename_user_album($user_id, $old_album_name, $new_album_name) {
		$album_id = $this->get_album_id($user_id, $old_album_name);
		$this->db->set('name', $new_album_name);
		$this->db->where('id', $album_id);
		$this->db->update($this->table_name);
		return ($this->db->affected_rows() > 0) ? TRUE : FALSE;
	}

	// find album_id
	public function get_album_id($user_id, $album_name) {
		$all_album_id = $this->get_all_album_id($user_id);
		$album_id = 0;
		$this->db->where('name', $album_name);
		$query = $this->db->get($this->table_name);
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				if (in_array($row->id, $all_album_id)) {
					$album_id = $row->id;
					return $album_id;
				}
			}
		}
		return $album_id;
	}

	public function create_album($album_name, $user_id) {
		$albums_name = $this->get_all_album_name($user_id);
		// duplicated albums aborted!
		if (in_array($album_name, $albums_name)) {
			return FALSE;
		}
		$data['name'] = $album_name;
		$data['added'] = date('Y-m-d H:i:s');
		if ($this->db->insert($this->table_name, $data)) {
			$album_id = $this->db->insert_id();
			$this->db->set('user_id', $user_id);
			$this->db->set('album_id', $album_id);
			if ($this->db->insert($this->user_album_table_name)) {
				return TRUE;
			}
		}
		return FALSE;
	}

	private function get_all_album_id($user_id) {
		$albums_id = array();
		$this->db->where('user_id', $user_id);
		$query = $this->db->get($this->user_album_table_name);
		foreach ($query->result() as $row) {
			array_push($albums_id, $row->album_id);
		}
		return $albums_id;
	}

	// get all album_name
	public function get_all_album_name($user_id) {
		$this->db->where('user_id', $user_id);
		$query = $this->db->get($this->user_album_table_name);
		if ($query->num_rows() > 0) {
			return $this->get_album_name($query);
		}
		return NULL;
	}

	private function get_album_name($query) {
		$albums_name = array();
		foreach ($query->result() as $albums_id) {
	        $this->db->where('id', $albums_id->album_id);
			$query2 = $this->db->get($this->table_name);
			if ($query2->num_rows() > 0) {
				foreach ($query2->result() as $album) {
	                array_push($albums_name, $album->name);
	            }
			}
	    }
	    return $albums_name;
	}

	public function get_albums_detail($user_id) {
		$detail = array();
		$this->db->where('user_id', $user_id);
		$query = $this->db->get($this->user_album_table_name);
		foreach($query->result() as $row) {
			$this->db->where('id', $row->album_id);
			$query2 = $this->db->get($this->table_name);
			if ($query2->num_rows() > 0) {
				$album_name = $query2->row()->name;
				$this->db->where('album_id', $row->album_id);
				$temp = $this->db->get($this->picture_album_table_name);
				if ($temp->num_rows() > 0) {
					$this->db->where('id', $temp->row()->picture_id);
					$picture_address = $this->db->get($this->picture_table_name);
					if ($picture_address->num_rows() > 0) {
						$detail[$album_name] = $picture_address->row()->picture;
					}
				}
				else {
					$detail[$album_name] = base_url(IMAGES.'upload_picture.png');
				}
			}
		}
		return $detail;
	}
}
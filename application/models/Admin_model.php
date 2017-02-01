<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Class Admin_model
 *
 * Admin model bevat alle interactie met de databank.
 *
 * @Author Glenn Martens
 * @Reviewer Wasla Habib
 * @Link http://mbahcoding.com/php/codeigniter/codeigniter-server-side-ajax-crud-modal-validation.html Server-side-crud-validation
 */
class Admin_model extends CI_Model {
	/**
	 * Property voor de gebruikte database tabel.
	 * @var string $table
     */
	var $table = 'TB_Gebruikers';
	/**
	 * Property voor de gebruikte kolommen van de database.
	 * @var array $column
     */
	var $column = array('GB_Email', 'GB_Voornaam', 'GB_Achternaam', 'GB_Status', 'GB_Groep', 'GB_RegistratieDatum'); //set column field database for order and search
	/**
	 * Property voor de sorteervolgorde.
	 * @var array $order
     */
	var $order = array('GB_ID' => 'desc'); // default order

	/**
	 * Query die de data voor databank opvraagt.
	 *
	 * Query die de gegevens voor de datatable opvraagt, rekening houdend met de criteria die in het
	 * zoekveld opgegeven zijn.
     */
	private function _get_datatables_query()
	{
		$this->db->from($this->table);

		$i = 0;
	
		foreach ($this->column as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
			{
				if($i===0) // first loop
				{
					$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND. 
					$this->db->like($item, $_POST['search']['value']);
				}
				else
				{
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if(count($this->column) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$column[$i] = $item; // set column array variable to order processing
			$i++;
		}
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order))
		{
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	/**
	 * Query die de datatable vult.
	 *
	 * @return mixed
     */
	function get_datatables()
	{
		$this->_get_datatables_query();
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	/**
	 * Query die het aantal rijen ophaalt.
	 *
	 * @return mixed
     */
	function count_filtered()
	{
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	/**
	 * Query die ophaalt hoeveel rijen er zijn in de tabel.
	 * @return mixed
     */
	public function count_all()
	{
		$this->db->from($this->table);
		return $this->db->count_all_results();
	}

	/**
	 * gebruiker opvragen via zijn gebruiker id.
	 *
	 * @param $id
	 * @return mixed
     */
	public function get_by_id($id)
	{
		$this->db->from($this->table);
		$this->db->where('GB_ID',$id);
		$query = $this->db->get();

		return $query->row();
	}

	/**
	 * Opslaan van een nieuwe gebruiker in de databank.
	 *
	 * @param $data
	 * @return mixed
     */
	public function save($data)
	{
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	/**
	 * Updaten van de data van een gebruiker.
	 *
	 * @param $where
	 * @param $data
	 * @return mixed
     */
	public function update($where, $data)
	{
		$this->db->update($this->table, $data, $where);
		return $this->db->affected_rows();
	}

	/**
	 * Deleten van een gebruiker op basis van de gebruiker-id.
	 * @param $id
     */
	public function delete_by_id($id)
	{
		$this->db->where('GB_ID', $id); 
		$this->db->delete($this->table);
	}
}
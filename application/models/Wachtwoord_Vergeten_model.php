<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Wachtwoord_Vergeten_model
 * @Author Gregory Malomgre
 * @link https://ellislab.com/codeigniter/user-guide/database/results.html Database results codeigniter
 * @link https://ellislab.com/codeigniter/user-guide/database/active_record.html active record codeigniter
 * @Bron Curus SQL 1TIN
 */
class Wachtwoord_Vergeten_model extends CI_Model {

    //Namen van tabel in variabelen, handig indien aanpassingen aan db plaatsvinden
	/**
	 * Property $column_Email om te gebruiken bij queries.
	 * @var string $column_Email
     */
	private $column_Email = 'GB_Email';
	/**
	 * Property $column_ID om te gebruiken bij queries.
	 * @var string $column_ID
     */
	private $column_ID = 'GB_ID';
	/**
	 * Property $column_Password om te gebruiken bij queries.
	 * @var string $column_Password
     */
	private $column_Password = 'GB_Wachtwoord';
	/**
	 * Property $table_Name om te gebruiken bij queries.
	 * @var string $table_Name
     */
	private $table_Name = 'TB_Gebruikers';

	/**
	 * Email controleren van de gebruiker.
	 *
	 * Controleren of het opgegeven email-adres bestaat in de databank.
	 * @param $email
	 * @return null
     */
	public function get_email($email)
	{
		$this->db->select($this->column_Email);
		$this->db->from($this->table_Name);
		$this->db->where($this->column_Email, $email);

		$query = $this->db->get()->row_array();

		if (count($query) === 1)
		{
	   		return $query[$this->column_Email]; //terugsturen string pwd
		}
		else
		{
	    	return NULL; //indien email adres niet bestaat, controleerbaar in de controllers.
		}
    }

    //Wachtwoord opvragen, id en old_Password om te checken of zowel het id met bijbehorende wachtwoord bestaan anders geen data terug geven (NULL)
	/**
	 * Wachtwoord opvragen
	 *
	 * @param $id
	 * @param $password
	 * @return mixed
     */
	public function get_password($id, $password)
	{
		$this->db->select($this->column_Password);
		$this->db->from($this->table_Name);  //kan ook met get()?
		$this->db->where(array($this->column_ID => $id, $this->column_Password => $password));

		$query = $this->db->get()->row_array();

		if (count($query) === 1)
			{ 	//check of wachtwoord gevonden wordt, anders stuurt deze methode NULL terug, waarop getest kan worden in de controllers
	    		return $query[$this->column_Password]; //terugsturen string pwd
			}
    }

    //Pas het wachtwoord aan op basis van $id, enkel voor wachtwoord vergeten!
	/**
	 * Aanpassen wachtwoord
	 *
	 * Aanpassen van het wachtwoord van de gebruiker op basis van de gebruiker-id.
	 *
	 * @param $id
	 * @param $new_Password
     */
	public function set_password($id, $new_Password)
		{ 	//where $old_Password ter extra controle dat user echt de user is.
			$this->db->query('UPDATE ' . $this->table_Name . ' SET ' . $this->column_Password . ' = \'' . $new_Password . '\' WHERE ' . $this->column_ID . ' = ' . $id . ';');
    	}

	/**
	 * Get de ID van de gebruiker.
	 *
	 * Get de ID van de gebruiker op basis van het email-adres dat ingegeven werd door de gebruiker.
	 *
	 * @param $email
	 * @return mixed
     */
	public function get_id_from_email($email)
	{
		$this->db->select($this->column_ID);
		$this->db->select($this->column_Email);
		$this->db->from($this->table_Name);
		$this->db->where($this->column_Email,$email);
		$query = $this->db->get()->row_array();
		return $query['GB_ID'];

	}
}
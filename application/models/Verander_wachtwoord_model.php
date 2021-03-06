<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Verander_wachtwoord_model
 * @Author Gregory Malomgre
 * @Reviewer
 * @Link https://ellislab.com/codeigniter/user-guide/database/results.html database results
 * @Bron Cursus SQL 1TIN
 */
class Verander_wachtwoord_model extends CI_Model {

	/**
	 * Property voor de gebruikte database kolom.
	 * @var string $column_Email
     */
	private $column_Email = 'GB_Email';
	/**
	 * Property voor de gebruikte database kolom.
	 * @var string $column_ID
     */
	private $column_ID = 'GB_ID';
	/**
	 * Property voor de gebruikte database kolom.
	 * @var string $column_Password
     */
	private $column_Password = 'GB_Wachtwoord';
	/**
	 * Property voor de gebruikte database tabel.
	 * @var string $table_Name
     */
	private $table_Name = 'TB_Gebruikers';

	/**
	 * Email adres opvragen.
	 *
	 * Het email adres word opgevraagd via de ID van de gebruiker.
	 * @param $id
	 * @return null
     */
	public function get_email($id) {
	$this->db->select($this->column_Email);
	$this->db->from($this->table_Name);
	$this->db->where($this->column_ID, $id);

	$query = $this->db->get()->row_array();

	if (count($query) === 1) {
	    return $query[$this->column_Email]; //terugsturen string pwd
	} else {
	    return NULL; //indien email adres niet bestaat, controleerbaar in de controllers.
	}
    }

    //Wachtwoord opvragen, id en old_Password om te checken of zowel het id met bijbehorende wachtwoord bestaan anders geen data terug geven (NULL)
	/**
	 * Opvragen oud wachtwoord en linken met de gebruiker ID.
	 *
	 * @param $id
	 * @param $password
	 * @return mixed
     */
	public function get_password($id, $password) {
	$this->db->select($this->column_Password);
	$this->db->from($this->table_Name);  //kan ook met get()?
	$this->db->where(array($this->column_ID => $id, $this->column_Password => $password));

	$query = $this->db->get()->row_array();

	if (count($query) === 1) { //check of wachtwoord gevonden wordt, anders stuurt deze methode NULL terug, waarop getest kan worden in de controllers
	    return $query[$this->column_Password]; //terugsturen string pwd
	}
    }

	/**
	 * Aanpassen wachtwoord
	 *
	 * Het oude wachtwoord word aangepast naar het nieuwe wachtwoord op basis van zijn gebruikers-id.
	 * @param $id
	 * @param $new_Password
     */
	public function set_password($id, $new_Password) { //where $old_Password ter extra controle dat user echt de user is.
	$this->db->query('UPDATE ' . $this->table_Name . ' SET ' . $this->column_Password . ' = \'' . $new_Password . '\' WHERE ' . $this->column_ID . ' = ' . $id . ';');
    }

}

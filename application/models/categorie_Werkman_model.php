<?php

/**
 * Class categorie_Werkman_model
 * @Author Benjamin Dendas
 * @Reviewer Joris Meylaers
 * @UnitTest Wasla Habib
 * @Link http://mbahcoding.com/php/codeigniter/codeigniter-server-side-ajax-crud-modal-validation.html Server-crud-model-validation
 */
class categorie_Werkman_model extends CI_Model
{
    /**
     * Categoriën laden.
     *
     * Via een query vragen we alle categorieën op uit de databank en worden deze in een array
     * opgeslagen en vervolgens doorgestuurd naar de view.
     *
     * @return array
     */
    public function get_categorie()
    {
        $categorie = array();
        $this->db->select('CA_Naam');
        $this->db->select('CA_ID');
        $result = $this->db->get('tb_categorie')->result_array();
		$categorie["selectie"]="--Selecteer Categorie--";
        foreach($result as $r)
        {
            $categorie[$r["CA_Naam"]]=$r["CA_Naam"];
        }
        return $categorie;
    }

    /**
     * Werkmannen laden.
     *
     * Via een query vragen we alle actieve werkmannen op uit de databank en worden deze in een array
     * opgeslagen en vervolgens doorgestuurd naar de view.
     *
     * @return array
     */
    public function get_werknemer()
    {
        $werknemer = array();
        $this->db->select('GB_ID');
        $this->db->select('GB_Groep');
        $this->db->select('GB_Status');
        $this->db->select('GB_Voornaam');
        $this->db->select('GB_Achternaam');
        $this->db->where('GB_Groep','Werkman');
        $this->db->where('GB_Status','Actief');
        $result = $this->db->get('tb_gebruikers')->result_array();
        $werknemer["Niet-Toegewezen"]="--Selecteer --";
        foreach($result as $r)
        {
            $werknemer[$r["GB_ID"]]=$r["GB_Voornaam"]. ' ' .$r["GB_Achternaam"];
        }
        return $werknemer;
    }

    /**
     * Toevoegen categorie
     *
     * Categorie toe aan de databank.
     * @param $naam
     */
    public function toevoegen_categorie($naam)
    {
        $data = array(
            'CA_ID' => NULL,
            'CA_Naam' => $naam
        );
        $this->db->select('CA_ID');
        $this->db->select('CA_Naam');
        $this->db->insert('tb_categorie',$data);
    }


}
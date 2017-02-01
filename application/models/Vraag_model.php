<?php

/**
 * Class Vraag_model
 * @Author Benjamin Dendas
 * @Reviewer Joris Meylaers
 * @UnitTest Wasla Habib
 * @Link https://ellislab.com/codeigniter/user-guide/database/active_record.html active record codeigniter
 */
class Vraag_model extends CI_Model
{
    /**
     * Ophalen vragen.
     *
     * Query die 5 vragen uit de databank ophaalt en deze returned.
     *
     * @return mixed
     */
    public function return_vragen()
    {
        $query = $this->db->query('SELECT VR_Vraag FROM tb_vragen LIMIT 5');
        return $query->result();
    }

    /**
     * Insert antwoorden
     *
     * Insert de antwoorden van de gebruiker in de database.
     *
     * @param $email
     * @return mixed
     */
    public function antwoord_vragen($email)
    {
        $insert_antwoord_in_database = array(
            'AW_ID' => NULL,
            'AW_V1' => $this->input->post('v1a'),
            'AW_V2' => $this->input->post('v2a'),
			'AW_V3' => $this->input->post('v3a'),
			'AW_V4' => $this->input->post('v4a'),
			'AW_V5' => $this->input->post('v5a'),
            'AW_Gebruiker' => $email
        );
        $query = $this->db->insert('tb_antwoorden',$insert_antwoord_in_database);
        return $query;
    }


    /**
     * Updaten vragen vragenlijst.
     *
     * Methode update de vragen in de databank met wijzigingen die de gebruiker heeft uitgevoerd.
     *
     * @param $vragen
     */
    public function update_vragen($vragen)
    {
        for($i= 1;$i<=5;$i++)
        {
            $obj = array(
                'VR_Vraag' => $vragen[$i-1],
            );
            $this->db->where('VR_ID',$i);
            $this->db->update('TB_Vragen',$obj);
        }

    }




}
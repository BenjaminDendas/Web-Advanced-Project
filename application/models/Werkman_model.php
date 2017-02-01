<?php

/**
 * Class Werkman_model
 * @author Benjamin Dendas
 * @Reviewer Joris Meylaers
 * @UnitTest Wasla Habib
 * @link http://mbahcoding.com/php/codeigniter/codeigniter-server-side-ajax-crud-modal-validation.html server-side-crud-validation
 */
class Werkman_model extends CI_Model
{
    /**
     * Property voor de gebruikte tabel.
     * @var string $table
     */
    var $table = 'tb_tickets';
    /**
     * Property voor de gebruikte database kolommen.
     * @var array $column
     */
    var $column = array('TK_Onderwerp', 'TK_Beschrijving', 'TK_Status',
        'TK_Prioriteit', 'TK_GebruikerID', 'TK_Categorie', 'TK_SluitDatum', 'TK_WerkmanID'); //set column field database for order and search
    /**
     * Property voor de sorteer volgorde.
     * @var array $order
     */
    var $order = array('TK_ID' => 'asc'); // default order

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

                if(count($this->column) - 1 == $i)//last loop
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
     * Query die de datatables genereerd op basis van de meegegeven ID.
     *
     * @param $id
     * @return mixed
     */
    function get_datatables($id)
    {
        $this->_get_datatables_query();
        if($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $this->db->where('TK_WerkmanID',$id);
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Aantal tickets van één werknemer.
     *
     * Geeft het aantal tickets terug van één bepaalde werknemer.
     *
     * @param $id
     * @return mixed
     */
    function count_filtered($id)
    {
        $this->_get_datatables_query();
        $this->db->where('TK_WerkmanID',$id);
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
     * Ticket opvragen via zijn Ticket id.
     *
     * @param $id
     * @return mixed
     */
    public function get_by_id($id)
    {
        $this->db->from($this->table);
        $this->db->where('TK_ID',$id);
        $query = $this->db->get();

        return $query->row();
    }

    /**
     * Opslaan van een nieuw ticket in de databank.
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
     * Updaten van ticket-gegevens op basis van ticket-id.
     *
     * @param $id
     * @param $data
     * @return mixed
     */
    public function update($id, $data)
    {
        $this->db->set($data);
        $this->db->where('TK_ID', $id);
        $this->db->update($this->table);
        return $this->db->affected_rows();
    }

    /**
     * Verwijderen ticket.
     *
     * Verwijderen van een ticket op basis van zijn ticket-id.
     * @param $id
     */
    public function delete_by_id($id)
    {
        $this->db->where('TK_ID', $id);
        $this->db->delete($this->table);
    }
}
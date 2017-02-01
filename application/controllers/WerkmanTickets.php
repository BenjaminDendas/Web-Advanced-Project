<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class WerkmanTickets
 * @Author Benjamin Dendas
 * @Reviewer Joris Meylaers
 * @Link http://mbahcoding.com/php/codeigniter/codeigniter-server-side-ajax-crud-modal-validation.html server-side-crud-model-validation
 * @Link https://ellislab.com/codeigniter/user-guide/libraries/unit_testing.html Unit-testing
 */
class WerkmanTickets extends CI_Controller
{

    /**
     * Property voor de id van de gebruiker in bij te houden.
     * @var string $id
     */
    private $id = null; // User email

    /**
     * Get de id van de gebruiker.
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set de id van de gebruiker.
     * @param $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * WerkmanTickets constructor.
     *
     * Zet de waarde van ID naar de email van de ingelogde gebruiker.
     * Laad de Ticket_model en categorie_Werkman_model.
     */
    public function __construct()
    {
        parent::__construct();
        $user = $this->session->email;
        $this->setId($user);
        $this->load->model('Tickets_model');
        $this->load->model('categorie_Werkman_model','cwm');
    }

    /**
     * Controleerd tot toegang tot de pagina.
     *
     * Controleerd of de ingelogde gebruiker tot de werkman-groep behoord en of zijn account actief is.
     * Initialiseerd de dropdown van de categorieën.
     */
    public function index()
    {
        $data = $this->session->userdata;
        $data['dropdown'] = $this->cwm->get_categorie();
        if($this->session->groep === 'Werkman' AND $this->session->status === 'Actief')
        {
            $this->load->view('shared/header');
            $this->load->view('Werkman_Tickets',$data);
            $this->load->view('shared/footer');
        }
        else
        {
            redirect('Restricted');
        }
    }

    /**
     * Deze methode haalt de gegevens op en genereerd datatables die het daarna encodeerd in JSON-formaat.
     */
    public function ajax_list()
    {
        $list = $this->Tickets_model->get_datatables($this->id);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $ticket) {
            $no++;
            $row = array();
            $row[] = $ticket->TK_ID;
            $row[] = $ticket->TK_Onderwerp;
            $row[] = $ticket->TK_Beschrijving;
            $row[] = $ticket->TK_AanmaakDatum;
            $row[] = $ticket->TK_Status;
            $row[] = $ticket->TK_Categorie;

            //add html for action
            $row[] = '<a class="btn btn-sm btn-primary" title="Edit" onclick="edit_ticket('."'".$ticket->TK_ID."'".')"><i class="glyphicon glyphicon-pencil"></i> Bewerken</a>
                  <a class="btn btn-sm btn-danger" title="Delete" onclick="delete_ticket('."'".$ticket->TK_ID."'".')"><i class="glyphicon glyphicon-trash"></i> Verwijderen</a>';

            $data[] = $row;
        }

        $output = array(
            'draw' => $_POST['draw'],
            'recordsTotal' => $this->Tickets_model->count_all(),
            'recordsFiltered' => $this->Tickets_model->count_filtered($this->id),
            'data' => $data,
        );
        //output to json format
        echo json_encode($output);
    }

    /**
     * Veranderen van een ticket.
     *
     * Selecteer welk ticket u wilt veranderen door een ID mee te geven aan deze methode.
     * @param $id
     */
    public function ajax_edit($id)
    {
        $data = $this->Tickets_model->get_by_id($id);
        echo json_encode($data);
    }

    /**
     * Toevoegen van een ticket.
     */
    public function ajax_add()
    {
        $this->_validate();
        $data = array(
            'TK_Onderwerp' => $this->input->post('Onderwerp'),
            'TK_Beschrijving' => $this->input->post('Beschrijving'),
            'TK_Status' => 'Open',
            'TK_Prioriteit' => 'Laag',
            'TK_GebruikerID' => $this->session->email,
            'TK_Categorie' => $this->input->post('Categorie')
        );
        $this->Tickets_model->save($data);

        echo json_encode(array("status" => TRUE));
    }

    /**
     * Het updaten van de data van het ge-opende ticket.
     */
    public function ajax_update()
    {
        $this->_validate();
        $data = array(
            'TK_Onderwerp' => $this->input->post('Onderwerp'),
            'TK_Beschrijving' => $this->input->post('Beschrijving'),
            'TK_Categorie' => $this->input->post('Categorie')
        );
        $this->Tickets_model->update($this->input->post('id'), $data);

        echo json_encode(array('status' => TRUE));
    }

    /**
     * Verwijderen van een ticket
     *
     * Het verwijderen van een ticket gebeurd door het meegeven van het ticket-id.
     * @param $id
     */
    public function ajax_delete($id)
    {
        $this->Tickets_model->delete_by_id($id);

        echo json_encode(array('status' => TRUE));
    }

    /**
     * Validatie van de user-input.
     *
     * Controleren of de gebruiker alle verplichte velden heeft ingevuld.
     */
    private function _validate()
    {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        if(empty($this->input->post('Onderwerp')))
        {
            $data['inputerror'][] = 'Onderwerp';
            $data['error_string'][] = 'Gelieve een onderwerp in te geven';
            $data['status'] = FALSE;
        }

        if(empty($this->input->post('Beschrijving')))
        {
            $data['inputerror'][] = 'Beschrijving';
            $data['error_string'][] = 'Gelieve een beschrijving in te geven';
            $data['status'] = FALSE;
        }

        if(empty($this->input->post('Categorie')))
        {
            $data['inputerror'][] = 'Categorie';
            $data['error_string'][] = 'Gelieve een categorie in te geven';
            $data['status'] = FALSE;
        }

        if($data['status'] === FALSE)
        {
            echo json_encode($data);
            exit();
        }
    }





}
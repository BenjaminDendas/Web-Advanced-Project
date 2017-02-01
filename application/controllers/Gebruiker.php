<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Gebruiker
 * @Author Joris Meylaers
 * @Reviewer
 * @UnitTest Wasla Habib
 * @link http://mbahcoding.com/php/codeigniter/codeigniter-server-side-ajax-crud-modal-validation.html server-side-model-validation
 * @link https://ellislab.com/codeigniter/user-guide/libraries/unit_testing.html Unit-testing
 */
class Gebruiker extends CI_Controller
{
    /**
     * Property voor de ID van de gebruiker
     * @var string $id
     */
    private $id = null; // User email

    /**
     * Methode om de ID terug te geven
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Methode om de waarde van de property ID te veranderen.
     * @param $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Gebruiker constructor.
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
     * Controleerd of de ingelogde gebruiker tot de groep Gebruiker hoord en of het account actief is.
     *
     * Indien de ingelogde gebruiker niet tot de groep Gebruiker behoord of als zijn account niet actief is, zal hij
     * doorverwezen worden naar de Restricted-pagina.
     */
    public function index()
	{
        if($this->session->groep === 'Gebruiker' AND $this->session->status === 'Actief')
        {
            redirect('Members');
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
     * Verwijderen van een ticket op basis van de meegegeven id.
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

    /**
     * Unit-test voor de Gebruiker.
     *
     * Deze unit test, test of alle methoden binnen de controller de verwachte waarde teruggeven.
     */
    public function test()
	{
		// BEGIN test = Test de form_validation
		$test = $this->Tickets_model->valid = TRUE;
        $expected_res = array( 	array('TicketId'			=> 'TK_ID'),
                            	array('TicketOnderwerp'		=> 'TK_Onderwerp'),
                            	array('TicketBeschrijving'	=> 'TK_Beschrijving'),
                            	array('TicketAanmaakDatum'	=> 'TK_AanmaakDatum'),
                            	array('TicketPrioriteit'	=> 'TK_Prioriteit'),
                            	array('GebruikerId'			=> 'TK_GebruikerID'),
                            	array('TicketCatagorie'		=> 'TK_Categorie'),
                            	array('TicketSluitDatum'	=> 'TK_SluitDatum'));
   		$this->unit->run($test, $expected_res,'Test de lijst tickets' , ' De expected result is een array');
		// EINDE test
        
		// BEGIN test2 = Test de count_all en Coutn_filtered methoden
		$test2 = array( 'draw' 				=> 'draw',
                        'recordsTotal'		=> 'Tickets_model->count_all()',
                        'recordsFiltered' 	=> 'Tickets_model->count_filtered(id)',
                        'data' 				=> 'data' );
        $expected_res2 = FALSE;
        $this->unit->run($test2, $expected_res2, 'Test de count_all en Coutn_filtered methoden ' ,'Check ');
		// EINDE test2

		// BEGIN test3 = Test de methoden in edit functie
        $test3 = $this->Tickets_model->get_by_id('id') == TRUE;
        $expected_res3 = array();
        $this->unit->run($test3, $expected_res3,'Test de edit functie',' Edit functie  ');
		// EINDE test3

        // BEGIN test4 = Test de methoden in update functie
        $test4 =  array('TK_Onderwerp' 		=> 'Onderwerp',
                        'TK_Beschrijving' 	=> 'Beschrijving',
                        'TK_Categorie' 		=> array('cwm->get_categorie_naam'=>'Categorie'));
        $expected_res4 = TRUE; 
        $this->unit->run($test4, $expected_res4,'Test de edit functie',' Edit functie  ');
        // EINDE test4
		
        // BEGIN test5 = Test de delete functie
        $test5 = $this->Tickets_model->delete_by_id('id');
        $expected_res5 = FALSE;
        $this->unit->run($test5, $expected_res5,'Test in  delete functie',' check delete method ');
        // EINDE test5

        // BEGIN test6 = Test als groep niet gebruiker is redirect naar de restricted pagina
        $test6 = $this->session->groep == 'Gebruiker';
        $expected_res6 = 'Restricted';
        $this->unit->run($test6, $expected_res6,'Test of de groep is niet gebruikers',' Redirect naar de restricted pagina');
		// EINDE test6
		
        $this->load->view('testing');
    }
}
<?php
defined('BASEPATH') OR EXIT('No direct script access allowed');

/**
 * Class Dispatcher
 * @author Benjamin Dendas
 * @Reviewer Joris Meylaers
 * @UnitTest Wasla Habib
 * @link http://mbahcoding.com/php/codeigniter/codeigniter-server-side-ajax-crud-modal-validation.html server-side-model-validation
 * @link https://ellislab.com/codeigniter/user-guide/libraries/unit_testing.html Unit testing
 */
class Dispatcher extends CI_Controller
{
    /**
     * Dispatcher constructor.
     *
     * In de constructor worden de Dispatcher_model en categorie_werkman_model ingladen.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Dispatcher_model');
        $this->load->model('categorie_Werkman_model','cwm');
    }

    /**
     * Controle tot toegang.
     *
     * In deze methode word er gecontroleerd of de ingelogde gebruiker als groep Dispatcher heeft en of zijn account
     * Actief is. Indien dit niet get geval is zal de gebruiker doorverwezen worden naar de Restricted pagina.
     */
    public function index()
    {
        if($this->session->groep === 'Dispatcher' AND $this->session->status === 'Actief')
        {
            redirect('Members');
        }
        else
        {
			redirect('Restricted');
        }
    }

    /**
     * Ophalen van gegevens uit het dispatcher_model en genereren van de datatables.
     */
    public function ajax_list()
    {
        $list = $this->Dispatcher_model->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach($list as $ticket)
        {
            $no++;
            $row = array();
            $row[] = $ticket->TK_ID;
            $row[] = $ticket->TK_Onderwerp;
            $row[] = $ticket->TK_Beschrijving;
            $row[] = $ticket->TK_AanmaakDatum;
            $row[] = $ticket->TK_Status;
            $row[] = $ticket->TK_Prioriteit;
            $row[] = $ticket->TK_GebruikerID;
            $row[] = $ticket->TK_Categorie;
            $row[] = $ticket->TK_SluitDatum;
            $row[] = $ticket->TK_WerkmanID;

            // Add html voor de knopactie

            $row[] = '<a class="btn btn-sm btn-primary" title="Edit" onclick="edit_ticket('."'".$ticket->TK_ID."'".')"><i class="glyphicon glyphicon-pencil"></i> Bewerken</a>
                  <a class="btn btn-sm btn-danger" title="Hapus" onclick="delete_ticket('."'".$ticket->TK_ID."'".')"><i class="glyphicon glyphicon-trash"></i> Verwijderen</a>';

            $data[] = $row;
        }
        $output = array(
                        'draw' => $_POST['draw'],
                        'recordsTotal' => $this->Dispatcher_model->count_all(),
                        'recordsFiltered' => $this->Dispatcher_model->count_filtered(),
                        'data' => $data,
        );
        
		// echo output to JSON formaat
		echo json_encode($output);
    }

    /**
     * Aanpassen van tickets op basis van het ticketID.
     *
     * @param $id
     */
    public function ajax_edit($id)
    {
        $data = $this->Dispatcher_model->get_by_id($id);
		echo json_encode($data);
    }

    /**
     * Het updaten van de content van een ticket.
     */
    public function ajax_update()
    {
        if($this->input->post('status') === 'Gesloten')
        {
            $today = date("Y-m-d H:i:s");
            $this->_validate();
            $data = array(
                'TK_Status' => $this->input->post('status'),
                'TK_Prioriteit' => $this->input->post('prioriteit'),
                'TK_Categorie' => $this->input->post('Categorie'),
                'TK_Sluitdatum' => $today,
                'TK_WerkmanID' => $this->input->post('werknemers'),
            );
        }
        else{
            $this->_validate();
            $data = array(
                'TK_Status' => $this->input->post('status'),
                'TK_Prioriteit' => $this->input->post('prioriteit'),
                'TK_Categorie' => $this->input->post('Categorie'),
                'TK_WerkmanID' =>  $this->input->post('werknemers'),
            );
        }


		$this->Dispatcher_model->update(array('TK_ID' => $this->input->post('id')), $data);

		echo json_encode(array('status' => TRUE));
    }

    /**
     * Verwijderen van een ticket op basis van ID
     *
     * @param $id
     */
    public function ajax_delete($id)
    {
        $this->Dispatcher_model->delete_by_id($id);
        echo json_encode(array('status' => TRUE));
    }

    /**
     * Validatie of alle verplichte velden ingevuld zijn.
     *
     * Deze methode controleerd of er een correcte waarde voor status, prioriteit, categorie en warkman
     * ingegeven is.
     */
    public function _validate()
    {
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if($this->input->post('status') === 'SelecteerStatus')
		{
            $data['inputerror'][] = 'status';
            $data['error_string'][] = 'Status is verplicht!';
            $data['status'] = FALSE;
		}
		
		if($this->input->post('prioriteit') === 'SelecteerPrioriteit')
		{
			$data['inputerror'][] = 'prioriteit';
			$data['error_string'][] = 'Prioriteit is verplicht!';
			$data['status'] = FALSE;
		}

		if(empty($this->input->post('Categorie')))
		{
			$data['inputerror'][] = 'categorie';
			$data['error_string'][] = 'Categorie is verplicht!';
			$data['status'] = FALSE;
		}
        if($this->input->post('werkman') === 'Selecteer')
        {
            $data['inputerror'][] = 'werkman';
            $data['error_string'][] = 'Werkman toewijzen is verplicht!';
            $data['status'] = FALSE;
        }

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
    }

    /**
     * Unit-test om te controleren of de verwachte data gegenereerd word.
     */
    public function test ()
    {
 		// BEGIN test1 -> test of de gebruiker ingelogd is als dispatcher
        $test = $this->session->groep === 'Dispatcher';
        $expected_res = 'Restricted';
        $this->unit->run($test, $expected_res,'Check of de ingelode gebruiker een dispatcher is' ,'ingelogd als dispatcher');
        // Einde Test1

        // BEGIN test2 -> Check de get_werknemer method
        $test2 = $this->cwm->get_werknemer();
        $expected_res2 = FALSE; // true
        $this->unit->run($test2, $expected_res2,'Check de get_werknemer method' ,'Check de get_werknemer method en haal de informatie van werknemer waar groep dispatcher is');
        // Einde Test2

        // BEGIN test3 = count_all en Coutn_filtered methoden
        $test3 = array(
            "draw" 				=> "draw",
            "recordsTotal" 		=> '$this->Dispatcher_model->count_all()',
            "recordsFiltered" 	=> '$this->Dispatcher_model->count_filtered()',
            "data" 				=> "data");

        $expected_res3 = TRUE; // False
        $this->unit->run($test3, $expected_res3, 'Test de count_all en Coutn_filtered methoden ' ,'Check de methoden');
	    // EINDE test3

        // BEGIN test4 = Test get_by_id method
        $test4 = $this->Dispatcher_model->get_by_id('id') == 0;
        $expected_res4 = array();
        $this->unit->run($test4, $expected_res4,'Test de get_by_id method',' get_by_id method ');
        // EINDE test4

        // BEGIN test5 = Test de validatie
	  	$test5 = $data = array(
                               array('error_string[]' 	=> 'array()'),
                               array('inputerror[]' 	=> 'array()'),
                               array('status' 			=> TRUE));
		$expected_res5 = TRUE;
		$this->unit->run($test5, $expected_res5,'Test de validatie ' ,'_validate functie');
        // EINDE test4

        // BEGIN test5 = Test de delete functie
        $test5 = $this->Dispatcher_model->delete_by_id('id');
        $expected_res5 = TRUE;
        $this->unit->run($test5, $expected_res5,'Test in  delete functie',' check delete method ');
        // EINDE test5

        // BEGIN test6 = Test de update functie
        $test6  = array(
                      'TK_Status' =>'status',
                      'TK_Prioriteit' => 'prioriteit',
                      'TK_Categorie' => array('$this->cwm->get_categorie_naam'=>'Categorie'),
                      'TK_WerkmanID' => array(' $this->cwm->get_werknemer_naam'=>'werknemers'),
        );
        $expected_res6 = FALSE;
	    $this->unit->run($test6, $expected_res6,'Test de update functie',' update functie  ');
        // EINDE test6

        $this->load->view('testing');
    }

    /**
     * Unit-test voor het wergschrijven van data via de model
     *
     * Controleerd of de invoer identiek word weggeschreven naar de database via de Model.
     */
    public function testDispatcher_model()
	{
    	// BEGIN TEST1
    	$result1 = $this->Dispatcher_model->get_by_id(1);
    	$expected_res1 =array();// $this->Admin_model->db->get()
    	$this->unit->run($result1,$expected_res1,'test get_by_id');
    	// EINDE TEST1

    	// BEGIN TEST2 test update()
    	$data = array(
        		'TK_Status' => array('status'=>'actief'),
        		'TK_Prioriteit' => array('prioriteit'=>'hoog'),
    	);
    	$result2=$user[1]['TK_Prioriteit']='hoog';
    	//update user
    	$expected_res2=$user[1]['TK_Prioriteit']='laag';
    	$this->unit->run($result2,$expected_res2,'test update');
    	// EINDE TEST2

    	//BEGIN TEST3
    	$result3 = $this->Dispatcher_model->delete_by_id(1);
    	$expected_res3 =array();
    	$this->unit->run($result3,$expected_res3,'test delete_by_id');
    	// EINDE TEST3

    	//Report
    	$this->load->view('testing');
	}
}
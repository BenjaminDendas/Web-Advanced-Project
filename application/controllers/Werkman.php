<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Werkman
 *
 * Werkman klasse met methoden en properties m.b.t. de werkman.
 *
 * @Author Benjamin Dendas
 * @Reviewer Joris Meylaers
 * @UnitTest Wasla Habib
 * @Link http://mbahcoding.com/php/codeigniter/codeigniter-server-side-ajax-crud-modal-validation.html codeigniter-server-side-model
 * @link https://ellislab.com/codeigniter/user-guide/libraries/unit_testing.html Unit-testing
 */
class Werkman extends CI_Controller
{
    /**
     * ID van de werkman.
     * @var string $id
     */
    private $id = null;

    /**
     * Geeft de waarde van de ID property terug.
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Setten van een waarde voor de ID property
     * @param $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Werkman constructor.
     *
     * Zet de userID en laad de Werkman_model.
     */
    public function __construct()
    {
        parent::__construct();
        $user = $this->session->id;
        $this->setId($user);
        $this->load->model('Werkman_model','WModel');
    }

    /**
     * Controle op toegang tot de Werkman-Pagina.
     *
     * Controle of de ingelogde gebruiker tot de groep werkman behoord
     * en of de gebruiker zijn account actief is.
     */
    public function index()
	{
        if($this->session->groep === 'Werkman' AND $this->session->status === 'Actief')
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
        $list = $this->WModel->get_datatables($this->id);
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
            $row[] = $ticket->TK_Prioriteit;
            $row[] = $ticket->TK_GebruikerID;
            $row[] = $ticket->TK_Categorie;
            $row[] = $ticket->TK_SluitDatum;

            //add html for action
            $row[] = '<a class="btn btn-sm btn-primary" title="Edit" onclick="edit_ticket('."'".$ticket->TK_ID."'".')"><i class="glyphicon glyphicon-pencil"></i> Bewerken</a>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->WModel->count_all(),
            "recordsFiltered" => $this->WModel->count_filtered($this->id),
            "data" => $data,
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
        $data = $this->WModel->get_by_id($id);
        echo json_encode($data);
    }

    /**
     * Het updaten van de data van het ge-opende ticket.
     */
    public function ajax_update()
    {
        $this->_validate();
        $data = array(
            'TK_Status' => $this->input->post('status'),
        );
        $this->WModel->update($this->input->post('id'),$data);
		echo json_encode(array("status" => TRUE));
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

        if($this->input->post('status') === 'SelecteerStatus')
        {
            $data['inputerror'][] = 'status';
            $data['error_string'][] = 'Gelieve een status mee te geven.';
            $data['status'] = FALSE;
        }

        if($data['status'] === FALSE)
        {
			echo json_encode($data);
            exit();
        }
    }

    /**
     * Unit-test voor de Werkman.
     *
     * Deze unit test, test of alle methoden binnen de controller de verwachte waarde teruggeven.
     */
    public function test ()
   	{
        // BEGIN test1 -> test of de gebruiker  ingelogd is als dispatcher
        $test = $this->session->groep !== 'Werkman';
        $expected_res = 'Restricted';
        $this->unit->run($test, $expected_res,'Check of de ingelode gebruiker een werkman is' ,'ingelogd als werkman');
        // EINDE Test1


        // BEGIN test2 = count_all en Coutn_filtered methoden
        $test2 = array(
                   	'draw' 				=> 'draw',
                   	'recordsTotal' 		=> '$this->WModel->count_all()',
                   	'recordsFiltered' 	=> '$this->WModel->count_filtered($this->id)',
                   	'data' 				=> 'data');

        $expected_res2 = $test2;
        $this->unit->run($test2, $expected_res2, 'Test de count_all en Coutn_filtered methoden ' ,'Check de methoden');
        // EINDE test2


        // BEGIN test3 = Test get_by_id method
        $test3 = $this->WModel->get_by_id('id');
        $expected_res3 = !$test3 ;
        $this->unit->run($test3, $expected_res3,'Test de get_by_id method',' get_by_id method ');
        // EINDE test3


        // BEGIN test4 = Test de validatie
        $test4 = $data = array(
                      		array('error_string[]' 	=> 'array()'),
                      		array('inputerror[]' 	=> 'array()'),
                      		array('status' 			=> TRUE));
        $expected_res4 =  TRUE;
        $this->unit->run($test4, $expected_res4,'Test de validatie ' ,'_validate functie');
        // EINDE test4


        // BEGIN test6 = Test de update functie
        $test6 = array('TK_Status' =>'status',);
        $expected_res6 = TRUE;
        $this->unit->run($test6, $expected_res6,'Test de update functie',' update functie  ');
        // EINDE test6


        $this->load->view('testing');
    }

    /**
     * Unit-test voor het wergschrijven van data via de model
     *
     * Controleerd of de invoer identiek word weggeschreven naar de database via de Model.
     */
    public function testWerkman_model()
	{
		// BEGIN TEST1
    	$result1 = $this->Werkman_model->get_by_id(1);
    	$expected_res1 =array();
    	$this->unit->run($result1,$expected_res1,'test get_by_id');
	    // EINDE TEST1

    	// BEGIN TEST2 test update()
 	   	$data = array(
    		    'TK_Prioriteit' => array('prioriteit'=>'hoog'),);
	    $result2=$user[1]['TK_Prioriteit']='hoog';
	    // update user
	    $expected_res2=$user[1]['TK_Prioriteit']='laag';
	    $this->unit->run($result2,$expected_res2,'test update');
	    // EINDE TEST2

	    // BEGIN TEST3
	    $result3 = $this->WModel->delete_by_id(1);
	    $expected_res3 =array();
	    $this->unit->run($result3,$expected_res3,'test delete_by_id');
	    // EINDE TEST3

    	// BEGIN TEST4
   		$data = array(
        			'TK_Status' => array('status'=>'actief'),
        			'TK_Prioriteit' => array('prioriteit'=>'hoog'),
   		);
    	$result4 =$data;
    	$expected_res4 =array();

    	$this->unit->run($result4,$expected_res4,'test save');

    	// EINDE TEST4

    	//Report
    	$this->load->view('testing');

	}
}
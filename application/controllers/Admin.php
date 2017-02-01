<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Admin
 * @author Glenn Martens
 * @Reviewer Wasla Habib
 * @UnitTest Wasla Habib
 * @Beoordeel Moelans B.
 * @Link https://ellislab.com/codeigniter/user-guide/libraries/unit_testing.html Unit-testing
 * @Link http://mbahcoding.com/php/codeigniter/codeigniter-server-side-ajax-crud-modal-validation.html server-side-crud-validation
 */
class Admin extends CI_Controller {

	/**
	 * Admin constructor.
     */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('admin_model', 'admin');
		
		// $data array aanmakenn en initialiseren voor de validatie.
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;
	}

	/**
	 *  Index-methode voor de Admin-controller
	 *
	 * De index methode kijkt of de groep van de ingelogde gebruiker overeenkomt met administrator, en of dit account actief is.
	 * Indien dit niet het geval is, zal de gebruiker geredirect worden naar de Restricted-pagina.
	 *
     */
	public function index()
	{
		if($this->session->groep === 'Administrator' AND $this->session->status === 'Actief')
		{
			redirect('Members');
		}
		else
		{
			redirect('Restricted');
		}
	}

	/**
	 * Ajax_list methode van de Admin-controller
	 *
	 * Deze methode haalt de gegevens op en genereerd datatables die het daarna encodeerd in JSON-formaat.
     */
	public function ajax_list()
	{
		$list = $this->admin->get_datatables();
		$no = $_POST['start'];
		foreach ($list as $user) {
			$no++;
			$row = array();
			$row[] = $user->GB_Email;
			$row[] = $user->GB_Voornaam;
			$row[] = $user->GB_Achternaam;
			$row[] = $user->GB_Status;
			$row[] = $user->GB_Groep;
			$row[] = $user->GB_RegistratieDatum;

			//add html for action
			$row[] = '<a class="btn btn-sm btn-primary" title="Edit" onclick="edit_person('."'".$user->GB_ID."'".')"><i class="glyphicon glyphicon-pencil"></i> Bewerken</a>
				  <a class="btn btn-sm btn-danger" title="Hapus" onclick="delete_person('."'".$user->GB_ID."'".')"><i class="glyphicon glyphicon-trash"></i> Verwijderen</a>';
		
			$data[] = $row;
		}

		$output = array(
						'draw' => $_POST['draw'],
						'recordsTotal' => $this->admin->count_all(),
						'recordsFiltered' => $this->admin->count_filtered(),
						'data' => $data,
				);
				
		//output to json format
		echo json_encode($output);
	}

	/**
	 * Ajax_edit methode van de Admin-controller
	 *
	 * Deze methode maakt het mogelijk om de ticketen aan te passen, we geven hiervoor het ID van het ticket
	 * dat we willen aanpassen mee.
	 *
	 * @param $id
     */
	public function ajax_edit($id)
	{
		$data = $this->admin->get_by_id($id);
		
		echo json_encode($data);
	}

	/**
	 * ajax_add methode van de Admin-controller
	 *
	 * Deze methode maakt het mogelijk om gebruikers toe te voegen vanuit het administrator-paneel.
     */
	public function ajax_add()
	{
		$this->_validate_add_user();	
		$data = array(
				'GB_Email' => $this->input->post('email'),
				'GB_Wachtwoord' => sha1($this->input->post('wachtwoord')),
				'GB_Voornaam' => $this->input->post('voornaam'),
				'GB_Achternaam' => $this->input->post('achternaam'),
				'GB_Status' => $this->input->post('status'),
				'GB_Groep' => $this->input->post('groep'),
		);
		$this->admin->save($data);
		echo json_encode(array('status' => TRUE));
	}

	/**
	 * ajax_update methode van de Admin-controller
	 *
	 * Deze methode maakt het mogelijk om de informatie van gebruikers aan te passen vanuit het administrator-paneel.
     */
	public function ajax_update()
	{
		if($this->input->post('id') == 1)
		{
			$this->_validate_update_administrator();
			if(empty($this->input->post('wachtwoord')))
			{
				$data = array(
						'GB_Email' => $this->input->post('email'),
						'GB_Voornaam' => $this->input->post('voornaam'),
						'GB_Achternaam' => $this->input->post('achternaam'),
				);			
			} else {
				$this->_validate_update_administrator_wachtwoord();
				$data = array(
						'GB_Email' => $this->input->post('email'),
						'GB_Wachtwoord' => sha1($this->input->post('wachtwoord')),
						'GB_Voornaam' => $this->input->post('voornaam'),
						'GB_Achternaam' => $this->input->post('achternaam'),
				);				
			}
		} else {
			$this->_validate_update_user();
			if(empty($this->input->post('wachtwoord')))
			{
				$data = array(
						'GB_Email' => $this->input->post('email'),
						'GB_Voornaam' => $this->input->post('voornaam'),
						'GB_Achternaam' => $this->input->post('achternaam'),
						'GB_Status' => $this->input->post('status'),
						'GB_Groep' => $this->input->post('groep'),
				);			
			} else {
				$this->_validate_update_user_wachtwoord();
				$data = array(
						'GB_Email' => $this->input->post('email'),
						'GB_Wachtwoord' => sha1($this->input->post('wachtwoord')),
						'GB_Voornaam' => $this->input->post('voornaam'),
						'GB_Achternaam' => $this->input->post('achternaam'),
						'GB_Status' => $this->input->post('status'),
						'GB_Groep' => $this->input->post('groep'),
				);				
			}
		}
		$this->admin->update(array('GB_ID' => $this->input->post('id')), $data);
		
		echo json_encode(array('status' => TRUE));
	}

	/**
	 * ajax_delete methode van de Admin-controller
	 *
	 * Deze functie maakt het mogelijk om gebruikers te verwijderen, hiervoor geven we de id van de gebruiker die we
	 * willen verwijderen mee aan de methode.
	 *
	 * @param $id
     */
	public function ajax_delete($id)
	{
		$this->admin->delete_by_id($id);
		
		echo json_encode(array('status' => TRUE));
	}

	/**
	 * Methode die validatie-methoden voor het toevoegen van een gebruiker oproept.
	 *
	 * Deze validatie-methode voor het toevoegen van een gebruiker is een algemene methode
	 * die meerdere methodes met betrekking tot validatie laat uitvoeren.
     */
	private function _validate_add_user()
	{	
		$this->_validate_email();
		$this->_validate_wachtwoord();
		$this->_validate_wachtwoord_lengte();
		$this->_validate_voornaam_achternaam();
		$this->_validate_groep_status();										
	}

	/**
	 * Methode die validatie-methoden voor het updaten van een gebruiker oproept.
	 *
	 * Deze validatie-methode voor het updaten van een user is een algemene methode die meerdere methodes met
	 * betrekking tot validatie laat uitvoeren.
     */
	private function _validate_update_user()
	{
		$this->_validate_email();
		$this->_validate_voornaam_achternaam();
		$this->_validate_groep_status();					
	}

	/**
	 * methode die de validatie-methode voor het wachtwoord oproept.
     */
	private function _validate_update_user_wachtwoord()
	{
		$this->_validate_wachtwoord_lengte();					
	}

	/**
	 * Methode die de validatie voor email, voornaam en achternaam oproept.
     */
	private function _validate_update_administrator()
	{
		$this->_validate_email();
		$this->_validate_voornaam_achternaam();
	}

	/**
	 * Methode die de validatie voor email, wachtwoord, voornaam en achternaam oproept.
     */
	private function _validate_update_administrator_wachtwoord()
	{
		$this->_validate_email();
		$this->_validate_wachtwoord_lengte();
		$this->_validate_voornaam_achternaam();
	}

	/**
	 * Validatie-methode om de input op email te controleren.
	 *
	 * Deze methode controleerd of het ingegeven email-adres voldoet aan de vereisten.
	 * Na controle via een Regex zal er een error teruggegeven indien de validatie faalt.
     */
	private function _validate_email()
	{
		$data['status'] = TRUE;
		if(empty($this->input->post('email')))
		{
			$data['inputerror'][] = 'email';
			$data['error_string'][] = 'E-mail is verplicht!';
			$data['status'] = FALSE;
		}
		else if (preg_match('/^\S+@\S+\.\S+$/', $this->input->post('email')) == FALSE)
		{
			$data['inputerror'][] = 'email';
			$data['error_string'][] = 'E-mail patroon is niet juist!';
			$data['status'] = FALSE; 
		}
		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

	/**
	 * Validatie-methode om de input het wachtwoord te controleren.
	 *
	 * Deze methode controleerd of er een wachtwoord is ingegeven door de gebruiker,
	 * indien niet het geval zal er een error teruggegeven worden.
     */
	private function _validate_wachtwoord()
	{
		$data['status'] = TRUE;
		if(empty($this->input->post('wachtwoord')))
		{
			$data['inputerror'][] = 'wachtwoord';
			$data['error_string'][] = 'Wachtwoord is verplicht!';
			$data['status'] = FALSE;
		}
		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

	/**
	 * Validatie-methode om de lengte van het ingegeven wachtwoord te controleren.
	 *
	 * Deze methode controleerd of het ingegeven wachtwoord de minimumlengte van 8 karakters bereikt.
	 * Indien dit niet zo is, zal deze methode een error teruggeven.
     */
	private function _validate_wachtwoord_lengte()
	{
		$data['status'] = TRUE;
		if(strlen($this->input->post('wachtwoord')) < 8)
		{
			$data['inputerror'][] = 'wachtwoord';
			$data['error_string'][] = 'Wachtwoord moet minstens 8 karakters bevatten!';
			$data['status'] = FALSE;
		}
		else if(strlen($this->input->post('wachtwoord')) > 40)
		{
			$data['inputerror'][] = 'wachtwoord';
			$data['error_string'][] = 'Wachtwoord mag niet langer dan 40 karakters zijn!';
			$data['status'] = FALSE;
		}
		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

	/**
	 * Validatie-methode om de invoer van voornaam en achternaam te controleren
	 *
	 * Deze methode controleerd of de input van voornaam en achternaam zijn ingevuld.
	 * Indien dit niet zo is zal de methode een fout teruggeven.
     */
	private function _validate_voornaam_achternaam()
	{
		$data['status'] = TRUE;
		if(empty($this->input->post('voornaam')))
		{
			$data['inputerror'][] = 'voornaam';
			$data['error_string'][] = 'Voornaam is verplicht!';
			$data['status'] = FALSE;
		}
		if(empty($this->input->post('achternaam')))
		{
			$data['inputerror'][] = 'achternaam';
			$data['error_string'][] = 'Achternaam is verplicht!';
			$data['status'] = FALSE;
		}
		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

	/**
	 * Validate methode om de groep en status te controleren
	 *
	 * Deze methode controleerd of er bij het toevoegen van een gebruiker een status en een groep
	 * werd meegegeven, indien dit niet het ggeval is, zal er een fout terugggegeven worden.
     */
	private function _validate_groep_status()
	{
		$data['status'] = TRUE;
		if(empty($this->input->post('status')))
		{
			$data['inputerror'][] = 'status';
			$data['error_string'][] = 'Gelieve een status te selecteren!';
			$data['status'] = FALSE;
		}
		if(empty($this->input->post('groep')))
		{
			$data['inputerror'][] = 'groep';
			$data['error_string'][] = 'Gelieve een groep te selecteren!';
			$data['status'] = FALSE;
		}
		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

	/**
	 * Unit test voor de controller
	 *
	 * Deze methode test de functionaliteit van de controller, indien er iets fout loopt zal de unit-test falen.
     */
	public function test()
	{
		//BEGIN test1
		$test = $this->admin->valid = true;
		$expected_res = 1;
		$this->unit->run($test,$expected_res,'Check of admin is ingelogd','Passed als het true is anders kreeg je False ');
		// EINDE test1


		// BEGIN test2 = count_all en Coutn_filtered methoden
		$test2  =  array(
						"draw" 				=> "draw",
						"recordsTotal" 		=> '$this->admin->count_all()',
						"recordsFiltered" 	=> '$this->admin->count_filtered($this->id)',
						"data" 				=> "data");
					
		$expected_res2 = true; // False
		$this->unit->run($test2, $expected_res2, 'Test de count_all en Coutn_filtered methoden ' ,'Check de methoden');
		// EINDE test2


		// BEGIN test3 = Test get_by_id method
        $test3 = $this->admin->get_by_id('id') == true;
        $expected_res3 = array();
        $this->unit->run($test3, $expected_res3,'Test de get_by_id method',' get_by_id method ');
		// EINDE test3


		// BEGIN test4 = Test de validatie van  admin wachtwoord
		$test4 = $data = array(
                               array('error_string[]' 	=> 'array()'),
                               array('inputerror[]' 	=> 'array()'),
                               array('status' 			=> TRUE));
		$expected_res4 =  true;
		$this->unit->run($test4, $expected_res4,'Test de validatie van  admin wachtwoord','  _validate_update_administrator_wachtwoord() method');
        // EINDE test4


	   	// BEGIN test5 = Test de edit functie
        $test5 = $data = array(
                               array('error_string'	=> 'array()'),
                               array('inputerror' 		=> 'array()'),
                               array('status' 			=> 'TRUE'));
        $expected_res5 = false;
        $this->unit->run($test5, $expected_res5,'Test de edit functie',' Edit functie  ');
        // EINDE test5


        // BEGIN test6 = Test de update functie
        $test6  = array(
	    		    'GB_Email' => 'email',
    	            'GB_Wachtwoord' => 'wachtwoord',
                	'GB_Voornaam' => 'voornaam',
                 	'GB_Achternaam' => 'achternaam',
                 	'GB_Status' => 'status',
                 	'GB_Groep' =>'groep',
  		);
        $expected_res6 = true;
        $this->unit->run($test6, $expected_res6,'Test de update functie',' update functie  ');
        // EINDE test6

		$this->load->view('testing');
	}

	/**
	 * Unit-test voor het wergschrijven van data via de model
	 *
	 * Controleerd of de invoer identiek word weggeschreven naar de database via de Model.
     */
	public function testAdmin_model()
	{
   		//BEGIN TEST1
   		$result1 = $this->admin->get_by_id(1);
   		$expected_res1 =array();
   		$this->unit->run($result1,$expected_res1,'test get_by_id');
   		// EINDE TEST1

   		//BEGIN TEST2 test update()
   		$user= array('GB_Email'=>array('email' => 'waslahabib@yahoo.com'),
      				'GB_Wachtwoord' => array('password'=> 'schoolpassword1'),
      				'GB_Voornaam'   => array('voornaam'=>'wasla'),
      				'GB_Achternaam' => array('achternaam'=>'achternaam'),
      				'GB_Groep'     => 'Administrator',
      				'GB_Status'    => 'Actief');
   		$result2=$user[2]['GB_Voornaam']='wasla';
   		//update user
   		$expected_res2=$user[2]['GB_Voornaam']='khan';
   		$this->unit->run($result2,$expected_res2,'test update');
   		// Einde TEST2

   		//BEGIN TEST3
   		$result3 = $this->admin->delete_by_id(1);
   		$expected_res3 =array();// $this->Admin_model->db->get()
   		$this->unit->run($result3,$expected_res3,'test delete_by_id');
 	  	// EINDE TEST3

   		//BEGIN TEST4
    	$gebruiker=array('GB_Email'=>array('email' => 'test2@yahoo.com'),
      					'GB_Wachtwoord' => array('password'=> 'password2'),
      					'GB_Voornaam'   => array('voornaam'=>'test'),
      					'GB_Achternaam' => array('achternaam'=>'2'),
      					'GB_Groep'     => 'Gebruiker',
      					'GB_Status'    => 'niet-Actief');
   		$result4 =$gebruiker;
	   	$expected_res4 =array();

      	$this->unit->run($result4,$expected_res4,'test save');
	   	// EINDE TEST4

   		//Report
		$this->load->view('testing');
	}			
}
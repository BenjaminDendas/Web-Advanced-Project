<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Login
 * @Author Wasla Habib
 * @Reviewer Glenn Martens
 * @UnitTest Wasla Habib
 * @link https://ellislab.com/codeigniter/user-guide/libraries/unit_testing.html Unit-testing
 * @link https://www.youtube.com/watch?v=FmKm1gCgUoM Codeigniter login/registreer tutorial
 * @link http://www.sourcecodester.com/php/7290/user-registration-and-login-system-codeigniter.html gebruiker registratie/login systeem
 * @Link https://www.youtube.com/watch?v=T39lkofTq2M login/registreer systeem
 *
 */
class Login extends CI_Controller
{

	/**
	 * Login constructor.
	 *
	 * In deze constructor worden de gebruikers_model en Captcha_model ingeladen.
     */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('gebruikers_model');
		$this->load->model('Captcha_model', 'captcha');
	}

	/**
	 * Controle of er een ingelogde sessie actief is, indien dit het geval is word de gebruiker geredirect naar de Members pagina,
	 * anders zal de gebruiker naar de login-pagina verwezen worden.
     */
	public function index()
	{
		if ($this->session->is_logged_in === 1) {
			redirect('Members');
		} else {
			$data['cap'] = $this->captcha->createValues();
			$this->load->view('shared/header');
			$this->load->view('login', $data);
			$this->load->view('shared/footer');
		}
	}

	/**
	 * Validatie + Authenticatie inloggen.
	 *
	 * Vraagt de gebruiker zijn informatie op en controleerd deze met de invoer van de gebruiker. Indien de login credentials correct zijn, zoal er een sessie aangemaakt worden
	 * met de gebruiker zijn sessie-data en word hij naar de correcte pagina doorverwezen.
     */
	public function login_validatie()
	{
		// Gebruiker status, groep, voornaam, achternaam, id, ... opvragen om door te geven aan de user session.
		$temp = $this->gebruikers_model->get_user();
		$status = $temp['GB_Status'];
		$groep = $temp['GB_Groep'];
		$id = $temp['GB_ID'];
		$voornaam = $temp['GB_Voornaam'];
		$achternaam = $temp['GB_Achternaam'];

		$userCaptcha = trim($this->input->post('captcha'));
		$word = $this->session->userdata('captchaWord');

		// Aangepaste validatie regels.
		$this->form_validation->set_rules('email', 'Email', 'required|trim|callback_validate_credentials|valid_email|max_length[50]');
		$this->form_validation->set_rules('wachtwoord', 'Wachtwoord', 'required|sha1|trim|max_length[40]');
		$this->form_validation->set_rules('captcha', "Captcha", 'required');

		// Controleert de validatie.
		if ($userCaptcha === $word) {
			if ($this->form_validation->run()) {
				$this->session->unset_userdata('captchaWord');
				// Aanmaken van de 'user session' en redirecten naar de member controller.
				$data = array(
					'id' => $id,
					'email' => $this->input->post('email'),
					'voornaam' => $voornaam,
					'achternaam' => $achternaam,
					'status' => $status,
					'groep' => $groep,
					'is_logged_in' => 1
				);
				$this->session->set_userdata($data);
				redirect('members');
			} else {
				// Als de validatie faalt, wordt de login pagina opnieuw weergegeven.
				$data['cap'] = $this->captcha->createValues();
				$this->session->set_userdata('captchaWord', $data['cap']['word']);
				$this->load->view('shared/header');
				$this->load->view('login', $data);
				$this->load->view('shared/footer');
			}
		} else{
			// foutieve captcha
			$data['cap'] = $this->captcha->createValues();
			$data['message'] = 'Gelieve de captcha juist in te geven.';
			$this->session->set_userdata('captchaWord', $data['cap']['word']);
			$this->load->view('shared/header');
			$this->load->view('login', $data);
			$this->load->view('shared/footer');
		}
	}

	/**
	 * Controleren of er kan ingelogd worden met het opgegeven account.
	 * @return bool
     */
	public function validate_credentials()
	{
		if ($this->gebruikers_model->can_log_in()) {
			return true;
		} else {
			$this->form_validation->set_message('validate_credentials', 'Foutief e-mailadres of wachtwoord! Gelieve opnieuw te proberen!');
			return false;
		}
	}

	/**
	 * Unit-test voor het werken met userdata.
	 *
	 * Deze unit test, test of alle methoden binnen de controller de verwachte waarde teruggeven.
     */
	public function test()
	{
		// BEGIN test = Test of de user ingelogd is
		$test = $this->session->userdata('is_logged_in') == 1;
		$expected_res = 'OK ';
		$this->unit->run($test, $expected_res, 'Test of de user ingelogd is ', 'Geef de result passed als de gebruiker ingelogd is');
		// EINDE test

		// BEGIN test2 = Test de status
		$test2 = $this->session->userdata('status') == 'Actief';
		$expected_res2 = 'OK ';
		$this->unit->run($test2, $expected_res2, 'Test de status ', 'Geef de result passed , als de gebruiker actief is anders geef de result Failed');
		// EINDE test2

		// BEGIN test3 = Test op groep
		$test3 = $this->session->userdata('groep') == 'Administrator';
		$expected_res3 = ' ';
		$this->unit->run($test3, $expected_res3, 'Test de groep ', 'Geef de Result Passed als de groep Administrator is anders , geef de Result Failed');
		// EINDE test3

		// BEGIN test4 = Test the credentials
		$test4 = 'restricted';
		$expected_res4 = 'Error';
		$this->unit->run($test4, $expected_res4, 'Test the credentials ', 'Als de conditie niet jusit is , redirect naar de restricted pagina');
		// EINDE test4

		$this->load->view('testing');
	}

	/**
	 * Unit test voor het opvragen van data uit de gebruikers_model.
     */
	public function testGebruikers_model()
	{
		// BEGIN TEST1
		$result = $this->Gebruikers_model->can_log_in();
		$expected_res = $this->db->get('TB_Gebruikers') == FALSE;
		$this->unit->run($result[0], $expected_res, 'test can_log_in');
		// EINDE TEST1

		// BEGIN TEST2
		$result2 = $this->Gebruikers_model->get_user();
		$expected_res2 = $this->db->get('TB_Gebruikers') == TRUE;
		$this->unit->run($result2, $expected_res2, 'test get_user');
		// EINDE TEST2

		//Report
		$this->load->view('testing');
	}
}
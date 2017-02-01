<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class VeranderWachtwoord
 * @Author Glenn Martens
 * @Reviewer Joris Meylaers
 * @Reviewer Benjamin Dendas
 * @UnitTest Wasla Habib
 * @Link https://jtreminio.com/2013/03/unit-testing-tutorial-part-3-testing-protected-private-methods-coverage-reports-and-crap Unit-testing
 */
class VeranderWachtwoord extends CI_Controller {

	/**
	 * VeranderWachtwoord constructor.
	 *
	 * Laden van VeranderWachtwoord_model en Captcha_model.
     */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('VeranderWachtwoord_model');
		$this->load->model('Captcha_model','captcha');
	}

	/**
	 * Genereren van de captcha + tonen view.
     */
	public function index()
	{
		$data['cap'] = $this->captcha->createValues();
		$this->load->view('shared/header');
		$this->load->view('veranderWachtwoord',$data);
		$this->load->view('shared/footer');	
	}

	/**
	 * Veranderen van het wachtwoord van de ingelogde gebruiker.
     */
	public function verander_wachtwoord()
	{
		$gegevens = $this->session->all_userdata();
		$gebruiker_id = $gegevens['id'];

		$oud_wachtwoord = sha1($this->input->post('oud_wachtwoord'));
	    $nieuw_wachtwoord = sha1($this->input->post('nieuw_wachtwoord'));	

		// Aangepaste validatie regels.
		$this->form_validation->set_rules('oud_wachtwoord', 'oud_wachtwoord_label', 'required|trim');
		$this->form_validation->set_rules('nieuw_wachtwoord', 'nieuw_wachtwoord_label', 'required|trim|min_length[8]|max_length[40]');
		$this->form_validation->set_rules('bevestig_nieuw_wachtwoord', 'bevestig_nieuw_wachtwoord_label', 'required|trim|matches[nieuw_wachtwoord]|min_length[8]|max_length[40]');
		$this->form_validation->set_rules('captcha', "Captcha", 'required');

		// Aangepaste validatie 'error message'.
		$this->form_validation->set_message('min_length', 'Wachtwoord moet minimum 8 karakters bevatten!');
		$this->form_validation->set_message('max_length', 'Wachtwoord mag maximum 40 karakters bevatten!');
		$this->form_validation->set_message('matches', 'Nieuw wachtwoord en bevestig nieuw wachtwoord komen niet overeen!');

		$userCaptcha = trim($this->input->post('captcha'));

		$word = $this->session->userdata('captchaWord');

		if ($this->form_validation->run() && $userCaptcha === $word)
		{
			if ($oud_wachtwoord !== $nieuw_wachtwoord)
			{
				if ($oud_wachtwoord === $this->VeranderWachtwoord_model->getPwd($gebruiker_id, $oud_wachtwoord)) 
				{
					$this->VeranderWachtwoord_model->setPwd($gebruiker_id, $oud_wachtwoord, $nieuw_wachtwoord);
					$data['success'] = 'Wachtwoord Succesvol gewijzigd.';
					$this->session->unset_userdata('captchaWord');
					$data['cap'] = $this->captcha->createValues();
					$this->load->view('shared/header');
					$this->load->view('veranderWachtwoord', $data);
					$this->load->view('shared/footer');
				} else {
					$data['error'] = 'Ongeldige login: Uw originele wachtwoord komt niet overeen met het oude wachtwoord!';
					$data['cap'] = $this->captcha->createValues();
					$this->load->view('shared/header');
					$this->load->view('veranderWachtwoord', $data);
					$this->load->view('shared/footer');
				}
			}
		} else {
			$data['cap'] = $this->captcha->createValues();
			$this->load->view('shared/header');
			$this->load->view('veranderWachtwoord',$data);
			$this->load->view('shared/footer');
		}
	}

	/**
	 * Testen of het wachtwoord van de gebruiker is aangepast.
     */
	public function test()
	{
	   	// BEGIN TEST1 Verander_wachtwoord
	   	$conditie = $this->session->all_userdata();
	   	$id = $conditie['id'];
	   	$oud_wachtwoord = 'oudpassword';
	   	$nieuw_wachtwoord = 'nieuwpassword';
	   	$conditie_2 = $oud_wachtwoord !== $nieuw_wachtwoord;
	
	   	$result_1 = $id && $conditie_2;
	   	$expected_res1 =TRUE;
	   	$this->unit->run($result_1,$expected_res1,'test verander_wachtwoord');
	   	// EINDE TEST1

	 	//Report
	   	$this->load->view('testing');
	}

	/**
	 * Testen of het aangepaste wachtwoord van de gebruiker in de databank word opgeslagen.
     */
	public function testVeranderWachtwoord_model()
	{
      	$gegevens = $this->session->all_userdata();
      	$gebruiker_id = $gegevens['id'];
      	$gebruiker_password = 'oudwachtwoord';
      	$result= $this->VeranderWachtwoord_model->getPwd($gebruiker_id,$gebruiker_password);

	  	$password = '5185e8b8fd8a71fc80545e144f91faf2';
      	$expected_res = $this->VeranderWachtwoord_model->setPwd($gebruiker_id, $gebruiker_password,$password);
      	$this->unit->run($result,$expected_res,'test verander wachtwoord');

      	//Report
      	$this->load->view('testing');
   	}
}

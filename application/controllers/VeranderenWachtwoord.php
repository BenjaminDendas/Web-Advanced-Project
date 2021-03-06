<?php

include (__DIR__ . DIRECTORY_SEPARATOR . 'Wachtwoord_Constanten.php');

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class VeranderenWachtwoord
 * @Author Gregory Malomgre
 * @Reviewer Benjamin Dendas
 * @Bron Login.php - klasse
 * @Link https://ellislab.com/codeigniter/user-guide/libraries/form_validation.html form-validation
 */
class VeranderenWachtwoord extends CI_Controller {

	/**
	 * Property voor de gebruiker ID
	 * @var string $user_ID
     */
	private $user_ID;

	/**
	 * VeranderenWachtwoord constructor.
	 * Ophalen van de gebruiker-id uit de sessie.
     */
	public function __construct() {
	parent::__construct();

	if ($this->session->userdata('id') !== NULL) {
	    $this->user_ID = $this->session->userdata('id');
	} else {
	    redirect('login');
	}
    }

	/**
	 * Laden van de main-pagina.
     */
	public function index() {
	$data = array('controller' => $this->router->fetch_class(),
	    'password_Min_Length' => Wachtwoord_Constanten::PASSWORD_MIN_LENGTH,
	    'password_Max_Length' => Wachtwoord_Constanten::PASSWORD_MAX_LENGTH
	);

	$this->load->view('shared/header');
	$this->load->view('Veranderen_Wachtwoord', $data);
	$this->load->view('shared/footer', $data);
    }

	/**
	 * Form tonen + verwerken om het nieuw wachtwoord aan te passen.
     */
	public function generate_form() {
	$this->load->model('Verander_wachtwoord_model');

	$this->form_validation->set_rules('origineel_Wachtwoord', 'Origineel Wachtwoord', 'required|trim|min_length[' . Wachtwoord_Constanten::PASSWORD_MIN_LENGTH . ']|max_length[' . Wachtwoord_Constanten::PASSWORD_MAX_LENGTH . ']|sha1|callback_Validate_Previous_Password');
	$this->form_validation->set_rules('nieuw_Wachtwoord', 'Nieuw Wachtwoord', 'required|trim|min_length[' . Wachtwoord_Constanten::PASSWORD_MIN_LENGTH . ']|max_length[' . Wachtwoord_Constanten::PASSWORD_MAX_LENGTH . ']|sha1');
	$this->form_validation->set_rules('nieuw_Wachtwoord_Bevestig', 'Bevestig wachtwoord', 'required|trim|sha1|matches[nieuw_Wachtwoord]|sha1');

	$this->form_validation->set_message('Validate_Previous_Password', 'Ongeldig (oud) wachtwoord');
	$this->form_validation->set_message('min_length', 'Wachtwoord moet minimaal ' . Wachtwoord_Constanten::PASSWORD_MIN_LENGTH . ' karakters lang zijn');
	$this->form_validation->set_message('max_length', 'Wachtwoord is te lang, maximaal ' . Wachtwoord_Constanten::PASSWORD_MAX_LENGTH . ' karakters lang zijn');

	$data = array('password_Min_Length' => Wachtwoord_Constanten::PASSWORD_MIN_LENGTH, 'password_Max_Length' => Wachtwoord_Constanten::PASSWORD_MAX_LENGTH);

	//Tests indien user velden aanpast met chrome tools
	if ($this->form_validation->run()) {
	    $oud_Wachtwoord = $this->input->post('origineel_Wachtwoord');
	    $nieuw_Wachtwoord = $this->input->post('nieuw_Wachtwoord');

	    if ($oud_Wachtwoord !== $nieuw_Wachtwoord) {
		if ($this->Verander_wachtwoord_model->get_password($this->user_ID, $oud_Wachtwoord) === $oud_Wachtwoord) {
		    $this->Verander_wachtwoord_model->set_password($this->user_ID, $nieuw_Wachtwoord); //change pw
		    $data['success'] = 'Wachtwoord succesvol gewijzigd';
		}
	    }
	}

	$this->load->view('shared/header');
	$this->load->view('Veranderen_Wachtwoord', $data);
	$this->load->view('shared/footer');
    }

	/**
	 * Controleren vorig wachtwoord.
	 *
	 * @param $str
	 * @return bool
     */
	public function validate_previous_password($str) {
	if ($this->Verander_wachtwoord_model->get_password($this->user_ID, $str) === NULL) {
	    return FALSE;
	} else {
	    return TRUE;
	}
    }

	/**
	 * Bootstrap layout toepassen.
     */
	public function btstrp_index() {
	$this->load->model('Verander_wachtwoord_model');

	$data = $this->main_functionality(array('controller' => $this->router->fetch_class(),
	    'form_Url' => base_url($this->router->fetch_class() . '/'),
	    'default_Controller' => TRUE) //needed for view
	);

	if ($this->input->post('actie') === 'Verzend') {
	    //wanneer verzonden, check oude wachtwoord
	    $old_Password = sha1($this->input->post('origineel_Wachtwoord'));
	    $new_Password = sha1($this->input->post('nieuw_Wachtwoord'));
	    $new_Password_Validation = sha1($this->input->post('nieuw_Wachtwoord_Bevestig'));

	    if ($old_Password !== $new_Password) {
		if ($old_Password === $this->Verander_wachtwoord_model->get_password($this->user_ID, $old_Password)) {
		    if ($new_Password === $new_Password_Validation) {

			if (strlen($this->input->post('nieuw_Wachtwoord')) >= Wachtwoord_Constanten::PASSWORD_MIN_LENGTH) {
			    $this->Verander_wachtwoord_model->set_password($this->user_ID, $new_Password); //change pwd
			    $data['message'] = 'Wachtwoord Succesvol gewijzigd.';
			} else {
			    $data['message'] = 'Ongeldig nieuw wachtwoord, het wachtwoord moet minimaal \\\'' . Wachtwoord_Constanten::PASSWORD_MIN_LENGTH . '\\\' karakters lang zijn.';
			}
		    } else {
			$data['message'] = 'Het nieuwe wachtwoord en de bevestiging van dit wachtwoord komen niet overeen, gelieve opnieuw te proberen';
		    }
		} else {
		    $data['message'] = 'Ongeldige login: Uw originele wachtwoord komt niet overeen met het oude wachtwoord!';
		}
	    } else {
		$data['message'] = 'Oud en nieuw wachtwoord zijn hetzelfde!';
	    }
	}

	$this->session->set_userdata('message', $data['message']);
	redirect('OverzichtTickets');
    }

}

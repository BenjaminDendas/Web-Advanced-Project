<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class WachtwoordVergeten
 * @Author Gregory Malomgre
 * @Bron 1TIN pdf "FORMS# PHP Forms, Cookies, Sessions"
 * @Link https://ellislab.com/codeigniter/user-guide/libraries/email.html email library Codeigniter
 * @Link https://ellislab.com/codeigniter/user-guide/libraries/config.html config library Codeigniter
 */
class WachtwoordVergeten extends CI_Controller {

    /**
     * WachtwoordVergeten constructor.
     */
    public function __construct() {
	parent::__construct();
	$this->load->model('Wachtwoord_Vergeten_model', 'wvm');
    }

    /**
     * Laad de main pagina van Wachtwoord vergeten.
     */
    public function index() {
	$this->load->model('Captcha_model');

	$data = array(
	    'controller' => $this->router->fetch_class(),
	    'method' => $this->router->fetch_method(),
	    'min_Length' => 8,
	    'max_Length' => 40);

	$this->load->view('shared/header');

	if ($this->input->post('actie') === 'Verander') {
	    $email = $this->input->post('email');
	    $id = $this->wvm->get_id_from_email($email);
	    $db_Email = $this->wvm->get_email($email);

	    if (trim($this->input->post('captcha')) === $this->session->userdata('captchaWord')) {
		if ($db_Email !== NULL && $db_Email === $email) {
		    $random = strval(rand(0, 1000000000));
		    set_cookie('evaluate_cookie_id', $id, (time() + 600));
		    set_cookie('evaluate_cookie_session_ID', $random, (time() + 600));

		    //verstuur mail:
		    $mail_Status_Code = $this->Send_Mail('PXL-Ticketing: nieuw wachtwoord instructies', 'Nieuw wachtwoord aangevraagd, gelieve de volgende link te bezoeken om het veranderen van uw wachtwoord te bevestigen:<br/>' . '<a href="' . site_url($this->router->fetch_class() . '/update_password/' . $id . '/' . $random) . '">' . site_url($this->router->fetch_class() . '/update_password/' . $id . '/' . $random) . '</a><br/>of kopieer en plak voorgaande link in uw URL menu indien klikken niet ondersteund is door uw browser.', $db_Email);
		    if ($mail_Status_Code) {
			$data['message'] = 'Wachtwoord aanvraag verstuurd naar \\\'' . $db_Email . '\\\', u ontvangt zo meteen een e-mail met nieuwe login instructies.';
			$data['email'] = $email;
			$this->session->unset_userdata('captchaWord');
			$this->load->view('Wachtwoord_Vergeten_Success', $data);
		    } else {
			$data['captcha'] = $this->Captcha_model->createValues();
			$data['message'] = 'Fout tijdens versturen van e-mail. Waarschijnlijk is uw internetverbinding onderbroken, gelieve opnieuw te proberen.';
			$this->load->view('Wachtwoord_Vergeten', $data);
		    }
		} else {
		    $data['captcha'] = $this->Captcha_model->createValues();
		    $data['message'] = 'Ongeldige e-mail adres, probeer opnieuw of maak een account aan.';
		    $this->load->view('Wachtwoord_Vergeten', $data);
		}
	    } else {
		$data['captcha'] = $this->Captcha_model->createValues();
		$data['message'] = 'Captcha komt niet overeen met de captcha die u invoerde';
		$this->load->view('Wachtwoord_Vergeten', $data);
	    }
	} else {
	    $data['captcha'] = $this->Captcha_model->createValues();
	    $this->load->view('Wachtwoord_Vergeten', $data);
	}

	$this->load->view('shared/footer');
    }

    //Reactie op e-mail: indien validatie ok, geef mogelijkheid om wachtwoord aan te passen, anders een foutmelding of een 'die' in het geval dat een gehackte link kan gebruikt zijn
    /**
     * verander email functionaliteit
	 *
	 * Indien de gebruiker een link aangevraagd heeft om zijn wachtwoord te veranderen omdat hij het vergeten is zal
	 * deze methode de correcte pagina laden.
     */
    public function update_password() {
	$this->load->view('shared/header');
	if (func_num_args() === 2) {
	    $data = array(
		'args' => func_get_args(),
		'default_Controller' => FALSE,
		'controller' => $this->router->fetch_class(),
		'form_Url' => base_url($this->router->fetch_class() . '/' . $this->router->fetch_method() . '/' . func_get_arg(0) . '/' . func_get_arg(1)),
		'min_Length' => 8,
		'max_Length' => 40,
		'first_Run' => FALSE
	    );

	    $cookie_1 = get_cookie('evaluate_cookie_id');
	    $cookie_2 = get_cookie('evaluate_cookie_session_ID');

	    if ($cookie_1 !== NULL || $cookie_2 !== NULL) {
		if ($cookie_1 === $data['args'][0] && $cookie_2 === $data['args'][1]) {
		    if ($this->input->post('actie') === 'Verander') { //Filled in form
			$pass_1 = $this->input->post('password_1'); //id>wachtwoord
			$pass_2 = $this->input->post('password_2'); //sessie id

			if ($pass_1 === $pass_2) {
			    if (strlen($pass_1) >= 8) {
				$this->wvm->set_password($cookie_1, sha1($pass_1));
				delete_cookie('evaluate_cookie_id');
				delete_cookie('evaluate_cookie_session_ID');
				$data['message'] = 'Wachtwoord successvol gewijzigd';
				$this->load->view('Wachtwoord_Vergeten_Aanpassen_Success', $data);
			    } else {
				$data['message'] = 'Het nieuwe wachtwoord moet minimaal 8 karakters lang zijn.';
				$this->load->view('Wachtwoord_Vergeten_Aanpassen', $data);
			    }
			} else {
			    $data['message'] = 'Wachtwoorden komen niet overeen';
			    $this->load->view('Wachtwoord_Vergeten_Aanpassen', $data);
			}
		    } else {
			$this->load->view('Wachtwoord_Vergeten_Aanpassen', $data);
		    }
		} else {
		    $data['message'] = 'ongeldige link: dit valt voor wanneer uw login gegevens niet overeenkomen met onze server validatie test, gelieve een nieuwe e-mail aan te vragen en de instructies in de mail te volgen.';
		    $this->load->view('Wachtwoord_Vergeten', $data);
		}

		$data['controller'] = $this->router->fetch_class();

		$this->load->view('shared/footer');
		//opruimen oude cookies:
	    } else {
		die('ongeldige link, dit valt meestal voor indien u na 10 minuten probeert een "wachtwoord vergeten" link te bezoeken, vraag een nieuwe mail aan en volg de instructies opnieuw'); //ongeldige aanvraag, indien je hier komt heb je de cookies niet ge-set met de index() methode, dus ga ik ervanuit dat je probeert een account-link te gokken (brute-force protection)
	    }
	} else { //geen parameters in deze functie betekent een ongeldige (mogelijk oude mail), stuur in dit geval naar de index pagina.
	    $this->index();
	}
    }

    /**
	 * Versturen email.
	 *
	 * Om de gebruiker een mail te sturen word er gebruik gemaakt van de email libary van codeigniter.
	 *
     * @param $subject
     * @param $message
     * @param $recipient
     * @return mixed
     */
    private function send_Mail($subject, $message, $recipient) {
	$config['protocol'] = $this->config->item('protocol');
	$config['smtp_host'] = $this->config->item('smtp_host');
	$config['smtp_port'] = $this->config->item('smtp_port');
	$config['smtp_user'] = $this->config->item('smtp_user');
	$config['smtp_pass'] = $this->config->item('smtp_pass');
	$config['mailtype'] = $this->config->item('mailtype');
	$config['charset'] = $this->config->item('charset');
	$this->email->initialize($config);

	$adres = $this->config->item('smtp_user');
	$this->email->set_newline("\r\n"); //MOET met double quotes (""), enkele quotes ('') werken NIET!
	$this->email->from($adres, 'PXL Administratie');
	$this->email->to($recipient);
	$this->email->subject($subject);
	$this->email->message($message);
	$result = $this->email->send();
	return $result;
    }
}
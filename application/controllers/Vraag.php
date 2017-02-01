<?php

defined('BASEPATH') OR EXIT('No direct script access allowed');

/**
 * Class Vraag
 * @Author Benjamin Dendas
 * @Reviewer Joris Meylaers
 * @Link https://ellislab.com/codeigniter/user-guide/libraries/form_validation.html Form-validation
 */
class Vraag extends CI_Controller
{
	/**
	 * Vraag constructor.
	 *
	 * Laden van Vraag_model en Captcha_model.
     */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Vraag_model', 'Vraag');
		$this->load->model('Captcha_model','captcha');
	}

	/**
	 * Ophalen van de vragen en de view.
     */
	public function index()
	{
		$this->form_validation->set_rules('v1a', 'Rij 1', 'required');
		$this->form_validation->set_rules('v2a', 'Rij 2', 'required');
		$this->form_validation->set_rules('v3a', 'Rij 3', 'required');
		$this->form_validation->set_rules('v4a', 'Rij 4', 'required');
		$this->form_validation->set_rules('v5a', 'Rij 5', 'required');

		$data['results'] = $this->Vraag->return_vragen();
		$data['cap'] = $this->captcha->createValues();
		$this->load->view('shared/header');
		$this->load->view('survey', $data);
		$this->load->view('shared/footer');
	}

	/**
	 * Genereren van de vraag-view.
     */
	public function genereer_vraag_view()
	{
		// $vragen_array = array();
		$vraag_radio_button = array();
		$optie_array = Array();
		$optie_array[0] = 'Slecht';
		$optie_array[1] = 'Matig';
		$optie_array[2] = 'Gemiddeld';
		$optie_array[3] = 'Goed';
		$optie_array[4] = 'Zeer Goed';

		for ($item = 0; $item < 5; $item++) {
			$vraag_radio_button[$item] = form_radio('vraag' . $item, $optie_array[$item], FALSE);
		}
		foreach ($vraag_radio_button as $item) {
			print $item;
		}
	}

	/**
	 * Valideren of de verplichte velden ingevuld zijn.
     */
	public function indien_validatie_vragen()
	{
		$this->form_validation->set_rules('captcha', "Captcha", 'required');

		$userCaptcha = trim($this->input->post('captcha'));

		$word = $this->session->userdata('captchaWord');

		if ($this->form_validation->run() && $userCaptcha === $word) {
			if ($this->Vraag->antwoord_vragen($this->session->userdata('email'))) {
				$this->session->unset_userdata('captchaWord');
				$data['success'] = ' Hartelijk dank voor het invullen van de vragenlijst!';
				$this->load->view('shared/header');
				$this->load->view('survey_succeed',$data);
				$this->load->view('shared/footer');
			} else {
				$data['results'] = $this->Vraag->return_vragen();
				$data['cap'] = $this->captcha->createValues();
				$this->load->view('shared/header');
				$this->load->view('survey', $data);
				$this->load->view('shared/footer');
			}
		} else {
			$this->form_validation->set_message('userCaptcha', 'Gelieve de captcha juist in te geven.');
			$data['results'] = $this->Vraag->return_vragen();
			$data['cap'] = $this->captcha->createValues();
			$this->load->view('shared/header');
			$this->load->view('survey', $data);
			$this->load->view('shared/footer');
		}
	}

	/**
	 * Testen of iedere vraag een antwoord gekregen heeft en of de captcha werkt.
     */
	public function test()
	{
	   	// BEGIN TEST1 geneer vraag_view
	    $result_1  = Array(
    	               0 => 'Slecht',
          	           1 => 'Matig',
             	       2 =>  'Gemiddeld',
            	       3 =>  'Goed',
            	       4 =>  'Zeer Goed', );
   		$expected_res1 =FALSE;
   		$this->unit->run($result_1,$expected_res1,'test geneer vraag_view');
   		// EINDE TEST1

	    // BEGIN TEST2  indien validatie vragen ;
	    $conditie_1= $this->form_validation->run() && (strtoupper('$userCaptcha') === strtoupper('$word'))===1;
	    $conditie_2= $this->Vraag->antwoord_vragen($this->session->userdata('email'));
	    $result2 = $conditie_1 && $conditie_2;

        $expected_res2= '';
	    $this->unit->run($result2,$expected_res2,'test indien validatie_vragen ');
	    // EINDE TEST2

   	    //BEGIN TEST3 $this->Vraag_model->update_vragen();
   	    $value = array('VR_Vraag'=>'vragen');
        //$where = array('VR_ID'=>'id');
	    // $result = $this->Vraag_model->update_vragen($value);
	    $result3 = $conditie_1===0;
	    $expected_res3 =$this->form_validation->set_message('userCaptcha', 'Please enter correct words!');
	    $this->unit->run($result3,$expected_res3,'test indien validatie vragen ');
	    // EINDE TEST3

	    //Report
	    $this->load->view('testing');
	}

	/**
	 * Testen of de model de correcte waarden ophaalt en wegschrijft.
     */
	public function testVraag_model()
	{
   		// BEGIN TEST1
   		$result1 = $this->Vraag->return_vragen();
   		$expected_res1 =array();
   		$this->unit->run($result1,$expected_res1,'return vragen');
   		// EINDE TEST1

		// BEGIN TEST2  test antwoord vragen;
   		$result2= array( 'AW_ID' => NULL,
        				 'AW_V1' => 'v1a',
         				 'AW_V2' => 'v2a',
      					 'AW_V3' => 'v3a',
      					 'AW_V4' => 'v4a',
       					 'AW_V5' => 'v5a',
                         'AW_Gebruiker' => 'email');
   		$expected_res2=FALSE;
   		$this->unit->run($result2,$expected_res2,'test antwoord_vragen');
   		// EINDE TEST2

   		// BEGIN TEST3 $this->Vraag_model->update_vragen();
   		$value = array('VR_Vraag'=>'vragen');
   		//$where = array('VR_ID'=>'id');
		// $result = $this->Vraag_model->update_vragen($value);
   		$result3 = $value;
   		$expected_res3 =TRUE;
   		$this->unit->run($result3,$expected_res3,'test update_vragen');
   		// EINDE TEST3

   		//Report
   		$this->load->view('testing');
	}
}
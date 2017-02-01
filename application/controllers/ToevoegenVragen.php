<?php
defined('BASEPATH') OR EXIT('No direct script access allowed');

/**
 * Class ToevoegenVragen
 * @Author Benjamin Dendas
 * @Reviewer Joris Meylaers
 * @Link https://ellislab.com/codeigniter/user-guide/libraries/unit_testing.html unit-testing
 * @Link https://ellislab.com/codeigniter/user-guide/libraries/form_validation.html form-validation
 */
class ToevoegenVragen extends  CI_Controller
{
    /**
     * Private property $data
     * @var mixed $data
     */
    private $data = NULL;

    /**
     * ToevoegenVragen constructor.
     *
     * Laden van Vraag_model en Captcha_model.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Vraag_model','vraag');
        $this->load->model('Captcha_model','captcha');
    }

    /**
     * Controleren of de gebruiker toegang heeft.
     * Controleren of de ingelogde gebruiker tot de groep dispatcher hoort en of zijn status actief is.
     */
    public function index()
    {
        if($this->session->groep === 'Dispatcher' AND $this->session->status === 'Actief')
        {
            $this->data['vragen'] = $this->vraag->return_vragen();
            $this->data['cap'] = $this->captcha->createValues();
            $this->session->set_userdata('captchaWord', $this->data['cap']['word']);
            $this->form_validation->set_rules('vraag1', 'vraag 1', 'required');
            $this->form_validation->set_rules('vraag2', 'vraag 2', 'required');
            $this->form_validation->set_rules('vraag3', 'vraag 3', 'required');
            $this->form_validation->set_rules('vraag4', 'vraag 4', 'required');
            $this->form_validation->set_rules('vraag5', 'vraag 5', 'required');
            $this->load->view('shared/header');
            $this->load->view('toevoegen_vragen',$this->data);
            $this->load->view('shared/footer');
        }
        else
        {
            redirect('Restricted');
        }
    }

    /**
     * Aanpassen van de vragen van de vragenlijst.
     *
     * Deze functie overschrijft de huidige vragen in de databank met de nieuwe vragen die ingegeven zijn.
     */
    public function edit_vragen()
    {
        $this->form_validation->set_rules('captcha', "Captcha", 'required');

        $userCaptcha = trim($this->input->post('captcha'));

        $word = $this->session->userdata('captchaWord');

        if ($this->form_validation->run() && (strtoupper($userCaptcha) === strtoupper($word))) {
            $vragen = array();
            $vragen[0] = $this->input->post('vraag1');
            $vragen[1] = $this->input->post('vraag2');
            $vragen[2] = $this->input->post('vraag3');
            $vragen[3] = $this->input->post('vraag4');
            $vragen[4] = $this->input->post('vraag5');
            $this->vraag->update_vragen($vragen);
            $this->data['success'] = ' De survey vragenlijst is aangepast!';
            $this->index();
            }
             else {
                    $this->data['error'] = 'Gelieve de captcha correct in te vullen!';
                    $this->form_validation->set_message('userCaptcha', 'Please enter correct words!');
                    $this->data['cap'] = $this->captcha->createValues();
                    $this->session->set_userdata('captchaWord', $this->data['cap']['word']);
                    $this->data['vragen'] = $this->vraag->return_vragen();
                    $this->form_validation->set_rules('vraag1', 'vraag 1', 'required');
                    $this->form_validation->set_rules('vraag2', 'vraag 2', 'required');
                    $this->form_validation->set_rules('vraag3', 'vraag 3', 'required');
                    $this->form_validation->set_rules('vraag4', 'vraag 4', 'required');
                    $this->form_validation->set_rules('vraag5', 'vraag 5', 'required');
                    $this->load->view('shared/header');
                    $this->load->view('toevoegen_vragen',$this->data);
                    $this->load->view('shared/footer');
        }
    }

    /**
     * Unit-Test om te controleren of de juiste waarden doorgegeven worden.
     */
    public function test()
	{
    	//BEGIN Test1
    	$res_1 = $this->session->groep === 'Dispatcher' && $this->session->status === 'Actief';

    	$expected_res1 = TRUE;
    	$this->unit->run($res_1,$expected_res1,'test of grop Dispatcher is met status Actief  ');
    	// EINDE TEST1

	    // BEGIN Test2 edit_vragen
	    $conditie_1 =$this->session->userdata('captchaWord');
	    $res_2 = $this->form_validation->run() && (strtoupper('captcha') === strtoupper($conditie_1));
	    $expected_res2 = array(
    	                        0=>'vraag1',
    	                        1=>'vraag2',
    	                        2=>'vraag3',
    	                        3=>'vraag4',
    	                        4=>'vraag5'
    	                        );

	    $this->unit->run($res_1,$expected_res1,'test edit_vragen ');
	    // EINDE Test2

	    //Report
	    $this->load->view('testing');
	}
}
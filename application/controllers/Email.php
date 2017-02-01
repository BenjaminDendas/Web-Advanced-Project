<?php

defined('BASEPATH') OR EXIT('No direct script access allowed');

/**
 * Class Email
 * @author Benjamin Dendas
 * @Reviewer Glenn Martens
 * @UnitTest Wasla Habib
 * @link https://ellislab.com/codeigniter/user-guide/helpers/form_helper.html Form-Helper
 */
class Email extends CI_Controller
{
    /**
     * Email constructor.
     *
     * Laad de Captcha_model in.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Captcha_model','captcha');
    }

    /**
     * Controleren of de gebruiker toegang mag hebben tot deze pagina
     *
     * Deze index-methode controleerd of de ingelogde gebruiker tot de groep Administrator behoort en of zijn account actief is.
     * Indien dit niet het geval is zal hij geredirect worden naar de Restricted pagina.
     */
    public function index()
    {
        if($this->session->groep === 'Administrator' AND $this->session->status === 'Actief')
        {
            $data['cap'] = $this->captcha->createValues();
            $this->load->view('shared/header');
            $this->load->view('mail',$data);
            $this->load->view('shared/footer');
        }
        else
        {
            redirect('Restricted');
        }
    }

    /**
     * Configuratie van de email-instellingen.
     *
     * Deze methode schrijft de ingegeven email-instellingen weg naar de config file in de map config (config/config.php).
     */
    public function edit_email_info()
    {
		$data = array();
		
		// Aangepaste validatie regels.
		$this->form_validation->set_rules('email', 'SMTP_User', 'required|trim|valid_email|max_length[50]');
		$this->form_validation->set_rules('wachtwoord', 'SMTP_Pass', 'required|trim|max_length[40]');
		$this->form_validation->set_rules('port', 'SMTP_Port', 'required|trim');
        $this->form_validation->set_rules('captcha', "Captcha", 'required');

        $userCaptcha = trim($this->input->post('captcha'));

        $word = $this->session->userdata('captchaWord');

        // Controleert de validatie.
		if ($this->form_validation->run() && $userCaptcha === $word)
		{	
			$file = file_get_contents('application/config/config.php');
			
			$file = str_replace("\$config['smtp_host']" . ' = ' . "'" . $this->config->item('smtp_host') . "'", 
								"\$config['smtp_host']" . ' = ' . "'" . $this->input->post('hostname') . "'", $file);
			
			$file = str_replace("\$config['smtp_port']" . ' = ' . "'" . $this->config->item('smtp_port') . "'", 
								"\$config['smtp_port']" . ' = ' . "'" . $this->input->post('port') . "'", $file);
								
			$file = str_replace("\$config['smtp_user']" . ' = ' . "'" . $this->config->item('smtp_user') . "'", 
								"\$config['smtp_user']" . ' = ' . "'" . $this->input->post('email') . "'", $file);
								
			$file = str_replace("\$config['smtp_pass']" . ' = ' . "'" . $this->config->item('smtp_pass') . "'", 
								"\$config['smtp_pass']" . ' = ' . "'" . $this->input->post('wachtwoord') . "'", $file);
								
			file_put_contents('application/config/config.php', $file);
			
			$data['success'] = "E-mail instellingen aangepast";
            $this->session->unset_userdata('captchaWord');
            $data['cap'] = $this->captcha->createValues();
            $this->load->view('shared/header');
            $this->load->view('mail',$data);
            $this->load->view('shared/footer');
		} else {
            $this->form_validation->set_message('userCaptcha', 'Gelieve de captcha juist in te geven.');
            $data['cap'] = $this->captcha->createValues();
            $this->session->set_userdata('captchaWord', $data['cap']['word']);
            $this->load->view('shared/header');
            $this->load->view('mail', $data);
            $this->load->view('shared/footer');
		}
    }
}
<?php

defined('BASEPATH') OR EXIT('No direct script access allowed');

/**
 * Class Categorie_toevoegen
 * @author Benjamin Dendas
 * @Reviewer Joris Meylaers
 * @UnitTest Wasla Habib
 * @link http://stackoverflow.com/questions/30940361/codeigniter-checking-if-value-already-exists-in-database-with-result-num-row Value already exists
 */
class Categorie_toevoegen extends CI_Controller
{

    /**
     * Categorie_toevoegen constructor.
     *
     * In de constructor worden de models categorie_Werkman_model en Captcha_model ingeladen.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('categorie_Werkman_model','cwm');
        $this->load->model('Captcha_model','captcha');
    }

    /**
     * Index methode van de Categorie_toevoegen controller
     *
     * Deze methode conroleerd of de ingelogde gebruiker als groep Dispatcher heeft, indien dit niet het geval is zal de gebruiker
     * naar de restricted pagina verwezen worden.
     */
    public function index()
    {
        if($this->session->groep === 'Dispatcher')
        {
            $data['cap'] = $this->captcha->createValues();
            $this->load->view('shared/header');
            $this->load->view('toevoegen_categorie',$data);
            $this->load->view('shared/footer');
        }
        else
        {
            redirect('Restricted');
        }
    }

    /**
     * Voegt een categorie toe in de database.
     *
     * Deze methode controleerd of de ingegeven categorie al voorkomt in de database of niet, indien dit niet
     * het geval is zal de categorie toegevoegd worden.
     */
    public function toevoegen_categorie()
    {
        $categorie_naam = $this->input->post('categorienaam');
        $this->form_validation->set_rules('captcha', "Captcha", 'required');
        $userCaptcha = trim($this->input->post('captcha'));
        $word = $this->session->userdata('captchaWord');


        if($categorie_naam !== '' && $userCaptcha === $word)
        {
            $this->db->select('CA_Naam');
            $this->db->where('CA_Naam',$categorie_naam);
            $result = $this->db->get('TB_Categorie');
            if($result->num_rows() == 0)
            {
                $this->session->unset_userdata('captchaWord');
                $this->cwm->toevoegen_categorie($categorie_naam);
                $data['cap'] = $this->captcha->createValues();
                $data['success'] = $categorie_naam . ' is toegevoegd!';
                $this->load->view('shared/header');
                $this->load->view('toevoegen_categorie', $data);
                $this->load->view('shared/footer');
            }
            else
            {
                $data['cap'] = $this->captcha->createValues();
                $data['error'] = 'Categorie bestaat al!';
                $this->load->view('shared/header');
                $this->load->view('toevoegen_categorie', $data);
                $this->load->view('shared/footer');
            }
        }
        else
        {
            if($userCaptcha !== $word)
            {
                $data['cap'] = $this->captcha->createValues();
                $data['error'] = 'Gelieve de captcha correct in te geven.';
                $this->load->view('shared/header');
                $this->load->view('toevoegen_categorie', $data);
                $this->load->view('shared/footer');
            }
            else
            {
                $data['cap'] = $this->captcha->createValues();
                $data['error'] = 'Gelieve een categorie in te geven';
                $this->load->view('shared/header');
                $this->load->view('toevoegen_categorie', $data);
                $this->load->view('shared/footer');
            }

        }
    }
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Restricted
 * @Author Wasla Habib
 * @Reviewer Glenn Martens
 * @link https://www.youtube.com/watch?v=FmKm1gCgUoM Codeigniter login/registreer tutorial
 * @link http://www.sourcecodester.com/php/7290/user-registration-and-login-system-codeigniter.html gebruiker registratie/login systeem
 * @Link https://www.youtube.com/watch?v=T39lkofTq2M login/registreer systeem
 */
class Restricted extends CI_Controller {

	/**
	 * Doorverwijzing nar de Restricted pagina indien deze controller aangeroepen word, de gebruiker heeft geen toegang.
     */
	public function index()
	{
		$this->load->view('shared/header');
		$this->load->view('restricted');
		$this->load->view('shared/footer');
	}
}
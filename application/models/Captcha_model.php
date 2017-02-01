<?php
/**
 * Class Captcha_model
 * @Author Joris Meylaers
 * @Reviewer Benjamin Dendas
 * @Link http://tutsnare.com/how-to-use-captcha-in-codeigniter/ Captcha in codeigniter
 */
class Captcha_model extends CI_Model
{
    /**
     * Aanmaken van de captcha.
     *
     * Maakt de waarde van de captcha aan en zet deze in de userdata van de gebruiker.
     * @return string
     */
    public function createValues()
    {
        $vals = array(
            'img_path' => './assets/captcha/',
            'img_url' => base_url() . '/assets/captcha/',
            'font_path' => base_url() . '/system/fonts/texb.ttf',
            'img_width' => 240,
            'img_height' => 50,
            'expiration' => 7200,
            'img_id' => 'Captcha',
            'pool' => '0123456789',
            'word_length' => 3,
            'colors' => array(
                'background' => array(255, 255, 255),
                'border' => array(0, 0, 0),
                'text' => array(50, 50, 50),
                'grid' => array(245, 40, 40)
            )
        );
        $captcha = create_captcha($vals);
        $this->session->set_userdata('captchaWord', $captcha['word']);
        return $captcha;
    }
}
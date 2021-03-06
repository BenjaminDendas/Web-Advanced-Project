<!-- Author: Gregory Malomgre -->
<div class="container">
    <?= form_open('VeranderenWachtwoord/Generate_Form') ?>
    <div class="form-group">
	<?= /* inhoud form: */ form_label('Vul uw oud wachtwoord hier in') ?>
	<?= form_password(array('name' => 'origineel_Wachtwoord', 'class' => 'form-control', 'required' => 'required', 'minlength' => $password_Min_Length, 'maxlength' => $password_Max_Length)) ?>
    </div>
    <div class="form-group">
	<?= form_label('Vul uw nieuw wachtwoord in') ?>
	<?= form_password(array('name' => 'nieuw_Wachtwoord', 'class' => 'form-control', 'required' => 'required', 'minlength' => $password_Min_Length, 'malength' => $password_Max_Length)) ?>
    </div>
    <div class="form-group">
	<?= form_label('Bevestig uw nieuw wachtwoord') ?>
	<?= form_password(array('name' => 'nieuw_Wachtwoord_Bevestig', 'class' => 'form-control', 'required' => 'required', 'minlength' => $password_Min_Length, 'maxlength' => $password_Max_Length)) ?>
    </div>
    <div class="form-group">
	<?= form_submit(array('value' => 'Verander', 'class' => 'btn btn-success center-block')) ?>
    </div>
    <?= form_close() ?>

    <br/>
    Ga terug naar de hoofdpagina door <a href="<?= base_url() ?>">HIER</a> te klikken.
</div>

<script>
<?php if (strlen(validation_errors()) > 0) { ?>
        alert('Foutieve ingave (nieuw wachtwoord komt niet overeen met bevestiging of oud wachtwoord klopte niet).');
        /* alert('Volgende foutieve ingaves werden gedetecteerd: <?php $result = ''; foreach ($this->form_validation->error_array() as $key => $value) { $result = $result . $value . "  "; } echo trim($result); ?>'); //oude alert */
<?php } if (isset($success)) { ?>
        alert('<?= $success ?>');
<?php } ?>
</script>
<!-- Author: Gregory Malomgre -->
<div id="wrap">
    <div class="container">
		<div class="row">
	    	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<br>
			<div class="box box-center">
		    	<?php
		    	if (count($args) === 2) {
				echo form_open($form_Url);
				if (isset($message)) {
			    ?>
			    <!-- foutmelding: -->
			    <div class="alert alert-danger"><?= $message ?></div>
			    <!-- einde foutmelding. -->
				<?php } ?>

    		    <!-- 1ste veld (2): -->
    		    <div class="form-group">
    			<p><b>Voer uw nieuw wachtwoord in:</b></p>
			    <?= form_password(array('name' => 'password_1', 'class' => 'form-control', 'required' => 'required', 'minlength' => $min_Length, 'maxlength' => $max_Length)); ?>
    		    </div>
    		    <!-- 2de veld (2): -->
    		    <div class="form-group">
    			<p><b>Bevestig uw nieuw wachtwoord:</b></p>
			    <?= form_password(array('name' => 'password_2', 'class' => 'form-control', 'required' => 'required', 'minlength' => $min_Length, 'maxlength' => $max_Length)) ?>
    		    </div>
    		    <div class="form-group"><?= form_submit(array('name' => 'actie', 'value' => 'Verander', 'class' => 'btn btn-success center-block')) ?></div>
			<?= form_close() ?>
		    <?php } ?>
			</div>
        	<br>
			<br>
			<br>
	    	</div>
		</div>
    </div>
</div>
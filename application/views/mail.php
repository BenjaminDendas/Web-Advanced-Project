<!--
Author = Benjamin Dendas		Reviewer = Glenn Martens

BRONNEN:
https://ellislab.com/codeigniter/user-guide/helpers/form_helper.html
!-->

<div id="wrap">
	<div class="container">
   		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
       		<br>
            <div class="box box-center">
       			<h2>Verander email instellingen</h2>
       			<br>
       			<?php
          			echo form_open('Email/edit_email_info');
       				echo validation_errors('<div class="alert alert-danger">', '</div>');
       			?>
       			<?php if (isset($success)){ ?><div class="alert alert-success"><?= $success ?></div><?php }?>

	       		<!-- BEGIN SMTP-Hostname !-->
    	   		<div class="form-group">
       				<?php echo form_label('SMTP-Hostname' ,'hostname_label');?>
       				<?php
       					$data = array(
           							'name' 			=> 'hostname',
           							'class' 		=> 'form-control',
           							'placeholder' 	=> 'ssl://smtp.googlemail.com',
           							'required' 		=> 'required',
       					);
       					echo form_input($data);
       				?>
       			</div>
       			<!-- EINDE SMTP-Hostname !-->

     	  		<!-- BEGIN Poort-nummer !-->
     	  		<div class="form-group">
     	  			<?php echo form_label('Poort nummer','Poortnummer_label');?>
   					<?php
    	   				$data = array(
    	       						'name' 			=> 'port',
									'type'			=> 'number',
									'min'			=> '0',
									'max'			=> '65535',
    	       						'class' 		=> 'form-control',
    	       						'placeholder' 	=> '450',
       					);
       					echo form_input($data);
   			 		?>
       			</div>
       			<!-- EINDE Poort-nummer !-->

		        <!-- BEGIN Email-adres !-->
   			    <div class="form-group">
    	   			<?php echo form_label('Email-adres', 'email_label');?>
    	   			<?php
    	   				$data = array(
       		 						'name' 			=> 'email',
           							'class' 		=> 'form-control',
           							'placeholder' 	=> 'personeel@pxl.be',
       					);
       					echo form_input($data);
       				?>
       			</div>
       			<!-- EINDE Email-adres !-->
	
	   			<!-- BEGIN wachtwoord !-->
	       		<div class="form-group">
    	   			<?php echo form_label('email Wachtwoord', 'wachtwoord_label');?>
       				<?php
           				$data = array(
           	    					'name' 	=> 'wachtwoord',
           	    					'class' => 'form-control'
           				);
       					echo form_password($data);
       				?>
       			</div>
       			<!-- EINDE wachtwoord !-->

				<!-- BEGIN Captcha veld !-->
				<div class="row" style="text-align: center">
					<div class="col-sm-12">
						<div class="form-group">
							<?php echo $cap['image']; ?>
							<br>
							<input type="text" autocomplete="off" name="captcha" placeholder="Enter above text"/>
						</div>
					</div>
				</div>

				<!-- EINDE Captcha veld !-->

		        <!-- BEGIN Button Veld: !-->
   			    <div class="form-group">
    	       		<?php
    	       			$options = array(
    	           					'class' => 'btn btn-success center-block',
        	   			);
           				echo form_submit('mysubmit', 'Update', $options);
           			?>
       			</div>
       			<!-- EINDE Button Veld: !-->

		        <p><a href="<?php echo base_url()."members"?>" role="button" class="btn btn-default"><b> &#60;-- Terug </b></a></p>
	   		   	<?php echo form_close(); ?>
	        </div>
            <br><br><br>
	    </div>
	</div>
</div>
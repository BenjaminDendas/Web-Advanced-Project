<!--
Author = Glenn Martens		Reviewer = Joris Meylaers
BRON: /
!-->

<div id="wrap">
	<div class="container">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        	<br>
	       	<div class="box box-center">
	        	<h2>Verander Wachtwoord:</h2>
				<?php
					echo form_open('veranderWachtwoord/verander_wachtwoord');
					echo validation_errors('<div class="alert alert-danger">', '</div>');
				?>
        
	       		<?php if (isset($error)){ ?><div class="alert alert-danger"><?= $error ?></div><?php }?>
				<?php if (isset($success)){ ?><div class="alert alert-success"><?= $success ?></div><?php }?>
        
	        	<!-- BEGIN oud wachtwoord Veld: !-->
	        	<div class="form-group">   
					<?php echo form_label('Voer uw oud wachtwoord in:', 'oud_wachtwoord_label'); ?>
	    	   		<?php
			    		$data = array(
			    	    			'name'     		=> 'oud_wachtwoord',
									'type'			=> 'password',
	    			        		'class'       	=> 'form-control',
	    			        		'placeholder' 	=> 'Oude Wachtwoord',
									'required'    	=> 'required',
	    	    		      		'maxlength'   	=> '40',
	    				);
						echo form_input($data);
					?>
       			</div> 	
        		<!-- EINDE oud wachtwoord Veld: !-->
				
        		<!-- BEGIN nieuw wachtwoord Veld: !-->
        		<div class="form-group">   
					<?php echo form_label('Voer uw nieuw wachtwoord in:', 'nieuw_wachtwoord_label'); ?>
    	   			<?php
	    				$data = array(
	    			    	      'name'        => 'nieuw_wachtwoord',
								  'type'		=> 'password',
	    			        	  'class'       => 'form-control',
	    			        	  'placeholder' => 'Nieuwe Wachtwoord',
								  'required'    => 'required',
	    			        	  'maxlength'   => '40',
	    				);
						echo form_input($data);
					?>
       			</div> 	
        		<!-- EINDE nieuw wachtwoord Veld: !-->

            	<!-- BEGIN bevestig nieuw wachtwoord Veld: !-->
        		<div class="form-group">   
					<?php echo form_label('Bevestig uw nieuw wachtwoord:', 'bevestig_nieuw_wachtwoord_label'); ?>
    	   			<?php
	    				$data = array(
	    	    			      'name'        => 'bevestig_nieuw_wachtwoord',
								  'type'		=> 'password',
	    	        			  'class'       => 'form-control',
	    	        	     	  'placeholder' => 'Bevestig nieuw wachtwoord',
						 		  'required'    => 'required',
	    	        	          'maxlength'   => '40',
	    				);
						echo form_input($data);
					?>
       			</div> 	
        		<!-- EINDE bevestig nieuw wachtwoord Veld: !-->

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
						echo form_submit('mysubmit', 'Veranderen', $options); 
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
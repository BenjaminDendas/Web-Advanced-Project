<!--
Author = Benjamin Dendas	Reviewer = Joris Meylaers
BRON: http://mbahcoding.com/php/codeigniter/codeigniter-server-side-ajax-crud-modal-validation.html
!-->
<div id="wrap">
	<div class="container">
    	<div class="row">
       		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        	    <br>
       		 	<div class="box box-center">
            		<h2>Categorie toevoegen:</h2>
       		 		<br>
            		<?php
            			echo form_open('Categorie_toevoegen/toevoegen_categorie');
            			echo validation_errors('<div class="alert alert-danger">', '</div>');
            		?>

           			<?php if (isset($error)){ ?><div class="alert alert-danger"><?= $error ?></div><?php }?>
            		<?php if (isset($success)){ ?><div class="alert alert-success"><?= $success ?></div><?php }?>
            
           	 		<!-- BEGIN oud wachtwoord Veld: !-->
            		<div class="form-group">
                		<?php echo form_label('Voer een categorienaam in:', 'categorie_naam_label'); ?>
                		<?php
                			$data = array(
                    					'name'     		=> 'categorienaam',
                    					'type'			=> 'text',
                    					'class'       	=> 'form-control',
                 					    'placeholder' 	=> 'categorienaam',
                   						'required'    	=> 'required',
                    					'maxlength'   	=> '40',
                			);
               			 	echo form_input($data);
                		?>
            		</div>
            		<!-- EINDE categorie Veld: !-->

					<!-- BEGIN Captcha Veld !-->
					<div class="row" style="text-align: center">
						<div class="col-sm-12">
							<div class="form-group">
								<?php echo $cap['image']; ?>
								<br>
								<input type="text" name="captcha" placeholder="Enter above text"/>
							</div>
						</div>
					</div>
					<!-- EINDE Captcha Veld !-->

        	    	<!-- BEGIN Button Veld: !-->
        	    	<div class="form-group">
        	        	<?php
        	        		$options = array(
        	            					'class' => 'btn btn-success center-block',
        	        		);
        	        		echo form_submit('mysubmit', 'Toevoegen', $options);
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
</div>
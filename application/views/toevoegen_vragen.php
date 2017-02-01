<!--
Author = Benjamin Dendas		Reviewer = Joris Meylaers

BRONNEN:
https://ellislab.com/codeigniter/user-guide/helpers/form_helper.html
!-->

<div id="wrap">
	<div class="container">
		<div class="row">
    		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        		<br>
        		<div class="box box-center">
        			<h2>Update vragen:</h2>
       		 		<br>
        			<?php
        				echo form_open('ToevoegenVragen/edit_vragen');
        				echo validation_errors('<div class="alert alert-danger">', '</div>');
        			?>
	
			        <?php if (isset($success)){ ?><div class="alert alert-success"><?= $success ?></div><?php }?>
    			    <?php if (isset($error)){ ?><div class="alert alert-danger"><?= $error ?></div><?php }?>

			        <!-- BEGIN VRAAG 1 VELD !-->
	        		<div class="form-group">
	       				<?php echo form_label('Vraag 1', 'vraag1'); ?>
	        			<?php
	        				$data = array(
	            						'name'        => 'vraag1',
	            						'class'       => 'form-control',
	            						'placeholder' => 'Vraag 1',
	            						'required'    => 'required',
	            						'maxlength'   => '300',
	            						'value' => $vragen[0]->VR_Vraag
	        				);
	        				echo form_input($data);
	        			?>
	        		</div>
	        		<!-- EINDE VRAAG 1 VELD !-->
	
	        		<!-- BEGIN VRAAG 2 VELD !-->
	        		<div class="form-group">
	            		<?php echo form_label('Vraag 2', 'vraag2'); ?>
	            		<?php
	            			$data = array(
	                					'name'        => 'vraag2',
	                					'class'       => 'form-control',
	                					'placeholder' => 'Vraag 2',
	                					'required'    => 'required',
	                					'maxlength'   => '300',
	                					'value' => $vragen[1]->VR_Vraag
	           				);
	            			echo form_input($data);
	           			?>
	        		</div>
	        		<!-- EINDE VRAAG 2 VELD !-->

	        		<!-- BEGIN VRAAG 3 VELD !-->
	        		<div class="form-group">
	            		<?php echo form_label('Vraag 3', 'vraag3'); ?>
	            		<?php
	            			$data = array(
    	            					'name'        => 'vraag3',
 	        	       					'class'       => 'form-control',
 	    	           					'placeholder' => 'Vraag 3',
 		               					'required'    => 'required',
         		       					'maxlength'   => '300',
        	        					'value' => $vragen[2]->VR_Vraag
        	    			);
        	    			echo form_input($data);
        	    		?>
        			</div>
        			<!-- EINDE VRAAG 3 VELD !-->

	        		<!-- BEGIN VRAAG 4 VELD !-->
	        		<div class="form-group">
	            		<?php echo form_label('Vraag 4', 'vraag4'); ?>
	            		<?php
	            			$data = array(
	                					'name'        => 'vraag4',
	                					'class'       => 'form-control',
	                					'placeholder' => 'Vraag 4',
	                					'required'    => 'required',
	                					'maxlength'   => '300',
	                					'value' => $vragen[3]->VR_Vraag
	            			);
   	    	     			echo form_input($data);
   		         		?>
  	    	  		</div>
  		      		<!-- EINDE VRAAG 4 VELD !-->

	        		<!-- BEGIN VRAAG 5 VELD !-->
	        		<div class="form-group">
	            		<?php echo form_label('Vraag 5', 'vraag5'); ?>
	            		<?php
   		         			$data = array(
    	            					'name'        => 'vraag5',
    	            					'class'       => 'form-control',
    	            					'placeholder' => 'Vraag 5',
    	            					'required'    => 'required',
    	            					'maxlength'   => '300',
    	            					'value' => $vragen[4]->VR_Vraag
    	        			);
    	        			echo form_input($data);
    	        		?>
    	    		</div>
    	    		<!-- EINDE VRAAG 5 VELD !-->

					<!-- BEGIN CAPTCHA VELD !-->
	        		<div class="row text-center">
	            		<div class="col-sm-12">
	                		<div class="form-group">
	                    		<?php echo $cap['image']; ?>
	                    		<br>
	                    		<input type="text" autocomplete="off" name="captcha" required="required" placeholder="Enter above text"/>
	                		</div>
	            		</div>
	        		</div>
	                <!-- EINDE CAPTCHA VELD !-->
	
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
</div>
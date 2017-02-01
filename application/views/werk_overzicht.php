<!--
Author = Benjamin Dendas		Reviewer = Glenn Martens

BRON: http://mbahcoding.com/php/codeigniter/codeigniter-server-side-ajax-crud-modal-validation.html
!-->

<link href="<?php echo base_url()."assets/datatables/css/dataTables.bootstrap.css"?>" rel="stylesheet">
<link href="<?php echo base_url()."assets/datatables/css/responsive.dataTables.min.css"?>" rel="stylesheet">
<link href="<?php echo base_url()."assets/datatables/css/responsive.bootstrap.min.css"?>" rel="stylesheet">
<link href="<?php echo base_url()."assets/datatables/css/buttons.dataTables.min.css"?>" rel="stylesheet">

<div id="wrap">
	<div class="container">
		<div class="row">
    	    <br>
    	    <div class="box-datatables-header">
       			<div class="col-sm-8">
        		   	<span style="font-size: 25pt;"><b>Toegewezen Tickets:</b></span>
        		</div>
  				<div style="font-size: 25pt;" class="col-sm-4">
					<div class="dropdown" style="float:right;">
						<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
        	        		<?php print 'Welkom, ' . $voornaam . ' ' . $achternaam ?>
							<span class="caret"></span>
        	        	</button>
						<ul class="dropdown-menu">
							<li><a href="<?php echo base_url()."logout"?>">Uitloggen</a></li>
							<li><a href="<?php echo base_url()."veranderWachtwoord"?>">Wijzig wachtwoord</a></li>
							<li><a href="<?php echo base_url()."VeranderenWachtwoord"?>">Wijzig wachtwoord</a></li>
                            <li><a href="<?php echo base_url()."WerkmanTickets"?>">Mijn Tickets</a></li>
                            <li><a href="<?php echo base_url()."Werkman"?>">Toegewezen Tickets</a></li>
						</ul>
					</div>
        		</div>
    		</div>
		</div>
    	<div class="row">
    		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    	    	<br><br>
    	    	<button class="btn btn-default" onclick="reload_table()"><i class="glyphicon glyphicon-refresh"></i> Vernieuwen</button>
    	    	<br><br>
    	    	<table id="table" class="table table-striped table-bordered dt-responsive display nowrap datatable">
    	    	    <thead>
    	    		    <tr>
    	            		<th>ID</th>
    	            		<th>Onderwerp</th>
    	            		<th>Beschrijving</th>
    	            		<th>AanmaakDatum</th>
       		         		<th>Status</th>
        	        		<th>Prioriteit</th>
        	        		<th>Gebruiker</th>
        	        		<th>Categorie</th>
        	        		<th>SluitDatum</th>
        	        		<th>Action</th>
        	    		</tr>
        	    	</thead>
        	    	<tbody></tbody>
        	    	<tfoot>
        	    		<tr>
        	       			<th>ID</th>
        	        		<th>Onderwerp</th>
        	        		<th>Beschrijving</th>
        	        		<th>AanmaakDatum</th>
        	        		<th>Status</th>
        	        		<th>Prioriteit</th>
        	        		<th>Gebruiker</th>
        	        		<th>Categorie</th>
        	        		<th>SluitDatum</th>
        	        		<th>Action</th>
        	    		</tr>
        	    	</tfoot>
        		</table>
			</div>
	    </div>
	</div>
</div>

<script src="<?php echo base_url()."assets/jquery/jquery-2.2.2.min.js"?>"></script>
<script src="<?php echo base_url()."assets/bootstrap/js/bootstrap.min.js"?>"></script>
<script src="<?php echo base_url()."assets/datatables/js/jquery.dataTables.min.js"?>"></script>
<script src="<?php echo base_url()."assets/datatables/js/dataTables.bootstrap.js"?>"></script>
<script src="<?php echo base_url()."assets/datatables/js/dataTables.buttons.min.js"?>"></script>
<script src="<?php echo base_url()."assets/datatables/js/jszip.min.js"?>"></script>
<script src="<?php echo base_url()."assets/datatables/js/pdfmake.min.js"?>"></script>
<script src="<?php echo base_url()."assets/datatables/js/vfs_fonts.js"?>"></script>
<script src="<?php echo base_url()."assets/datatables/js/buttons.html5.min.js"?>"></script>
<script src="<?php echo base_url()."assets/datatables/js/buttons.bootstrap.min.js"?>"></script>
<script src="<?php echo base_url()."assets/datatables/js/buttons.print.min.js"?>"></script>
<script src="<?php echo base_url()."assets/datatables/js/dataTables.responsive.min.js"?>"></script>
<script src="<?php echo base_url()."assets/datatables/js/responsive.bootstrap.min.js"?>"></script>

<script type="text/javascript">

    var save_method; // Variabele voor de safe method string
    var table;

    $(document).ready(function() {

        // Datatables
        table = $('#table').DataTable({

            processing: true, // Feature control the processing indicator.
            serverSide: true, // Feature control DataTables' server-side processing mode.
            order: [], // Initial no order.
            responsive: true,
			
			// Taal instellingen
			language: {
        		url: "<?php echo base_url()."assets/datatables/json/Dutch.json"?>"
        	},					

            // Load data for the table's content from an Ajax source
            ajax: {
                url: "<?php echo site_url('Werkman/ajax_list')?>",
                type: "POST"
            },

            // Set column definition initialisation properties.
            columnDefs: [
                {
                    targets: [ -1 ], // Last column
                    orderable: false, // Set not orderable
                },
            ],

            // Instellingen voor de grote van de tabel.
            lengthMenu:
                [
                    [5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]
                ],

            // Maakt de structuur aan, tabel, knoppen, enz. op de juiste plaats.
            dom:
            "<'row'<'col-sm-5'Bl><'col-sm-7'f>>" +
            "<'row'<'col-sm-12't>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",

            // Maakt de knoppen aan met de nodige instellingen om data van de tabel te exporteren.
            // Copy, Excel, CSV, PDF
            buttons:
                [
                    {
                        extend: 'copy',
                        exportOptions: {
                            columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8 ]
                        }
                    },

                    {
                        extend: 'print',
                        exportOptions: {
                            columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8 ]
                        }
                    },

                    {
                        extend: 'excel',
                        exportOptions: {
                            columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8 ]
                        }
                    },

                    {
                        extend: 'csv',
                        exportOptions: {
                            columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8 ]
                        }
                    },

                    {
                        extend: 'pdf',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: {
                            columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8 ]

                        }
                    },
                ],
        });

        // set input/textarea/select event when change value, remove class error and remove text help block
        $("input").change(function(){
            $(this).parent().parent().removeClass('has-error');
            $(this).next().empty();
        });
        $("textarea").change(function(){
            $(this).parent().parent().removeClass('has-error');
            $(this).next().empty();
        });
        $("select").change(function(){
            $(this).parent().parent().removeClass('has-error');
            $(this).next().empty();
        });

    });

    function edit_ticket(id)
    {
        save_method = 'update';
        $('#form')[0].reset(); // Reset form on modals
        $('.form-group').removeClass('has-error'); // Clear error class
        $('.help-block').empty(); // Clear error string
        $('#upload').hide();

        // Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('Werkman/ajax_edit/')?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
                $('[name="id"]').val(data.TK_ID); // ID meegeven anders weet je niet welke je moet updaten.
                $('[name="status"]').val(data.TK_Status);
                $('.modal-title').text('Status wijzigen'); // Set title to Bootstrap modal title
                $('#modal_form').modal('show'); // Show bootstrap modal when complete loaded

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
            }
        });
    }

    function reload_table()
    {
        table.ajax.reload(null,false); // Reload datatable ajax
    }

    function save()
    {
        $('#btnSave').text('Bezig met opslaan...'); // Change button text
        $('#btnSave').attr('disabled',true); // Set button disable
        var url;
            url = "<?php echo site_url('Werkman/ajax_update')?>/";

        // Ajax adding data to database
        $.ajax({
            url : url,
            type: "POST",
            data: $('#form').serialize(),
            dataType: "JSON",
            success: function(data)
            {

                if(data.status) // If success close modal and reload ajax table
                {
                    $('#modal_form').modal('hide');
                    reload_table();
                }
                else
                {
                    for (var i = 0; i < data.inputerror.length; i++)
                    {
                        $('[name="'+data.inputerror[i]+'"]').parent().parent().addClass('has-error'); // Select parent twice to select div form-group class and add has-error class
                        $('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]); // Select span help-block class set text error string
                    }
                }
                $('#btnSave').text('Opslaan'); // Change button text
                $('#btnSave').attr('disabled',false); // Set button enable


            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error, Bewerken gegevens');
                $('#btnSave').text('Opslaan'); // Change button text
                $('#btnSave').attr('disabled',false); // Set button enable

            }
        });

    }

</script>

<div class="modal fade" id="modal_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Status wijzigen</h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="form" class="form-horizontal">
                    <input type="hidden" name="id"/>
                    <!-- BEGIN Status Veld: !-->
                    <div class="form-group">
                        <label class="control-label col-md-3">Status</label>
                        <div class="col-md-9">
                            <select name="status" class="form-control">
                                <option value="SelecteerStatus">--Selecteer Status--</option>
                                <option value="Open">Open</option>
                                <option value="Gesloten">Gesloten</option>
                                <option value="InBehandeling">In Behandeling</option>
                                <option value="Bevroren">Bevroren</option>
                            </select>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <!-- EINDE Status Veld: !-->
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Opslaan</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Annuleren</button>
            </div>
        </div>
    </div>
</div>
<br><br><br>
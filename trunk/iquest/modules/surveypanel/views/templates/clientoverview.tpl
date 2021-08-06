<div class="row">
	<div class="col-md-12 col-xs-12">
		<div class="x_panel">
			<div class="x_title">
            	<h2>iQuest | <small>Clientenoverzicht</small></h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
            <br />
            	<a class="btn btn-primary" href="{baseURL}addclient" role="button">Client toevoegen</a>
            	<br /><br />
            	<div class="alert alert-succes normal" role="alert">
 				<p id="formSuccesMessage">{sFormSucces}</p>
  				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
    				<span aria-hidden="true">&times;</span>
  				</button>
  			</div><br />
            <div class="alert alert-danger" role="alert">
 				<p id="formMessage">{sFormOutput}</p>
  				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
    				<span aria-hidden="true">&times;</span>
  				</button>
  			</div>
            	<table class="table table-striped">
  					<thead>
    					<tr>
					    	<th scope="col">ClientID</th>
					      	<th scope="col">Client naam</th>
					      	<th scope="col">E-mail</th>
					      	<th scope="col">Bedrijf</th>
					      	<th scope="col">Openstaand</th>
					      	<th scope="col">Bezig</th>
					     	<th scope="col">Gesloten</th>
					     	<th scope="col">Uitgenodigd op</th>
					     	<th scope="col"></th>
					     	<th scope="col"></th>
   						 </tr>
  					</thead>
  					<tbody>
    					{clients}
   					</tbody>
				</table>
            </div>
        </div>
	</div>
</div>



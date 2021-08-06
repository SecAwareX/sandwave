<div class="row">
	<div class="col-md-12 col-xs-12">
		<div class="x_panel">
			<div class="x_title">
            	<h2>iQuest | <small>Gebruikersoverzicht</small></h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
            <br />
            	<a class="btn btn-primary" href="{baseURL}adduser" role="button">Gebruiker toevoegen</a>
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
					    	<th scope="col">#</th>
					      	<th scope="col">Schermnaam</th>
					      	<th scope="col">Emailadres / Gebruikersnaam</th>
					      	<th scope="col">Wachtwoord</th>
					     	<th scope="col">Userlevel</th>
					     	<th scope="col">Blockt</th>
					     	<th scope="col">Sessions</th>
					     	<th scope="col">Laatste login</th>
					     	<th scope="col">&nbsp;</th>
					     	<th scope="col">&nbsp;</th>
   						 </tr>
  					</thead>
  					<tbody>
    					{users}
   					</tbody>
				</table>
            </div>
        </div>
	</div>
</div>



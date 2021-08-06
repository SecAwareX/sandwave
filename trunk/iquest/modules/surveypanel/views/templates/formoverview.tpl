<div class="row">
	<div class="col-md-12 col-xs-12">
		<div class="x_panel">
			<div class="x_title">
            	<h2>iQuest | <small>Vragenlijstenoverzicht</small></h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
            <br />
            	<a class="btn btn-primary" href="{baseURL}addform" role="button">Vragenlijst toevoegen</a>
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
					    	<th scope="col">Lijst ID</th>
					      	<th scope="col">Vragenlijstnaam</th>
					      	<th scope="col">Eigenaar</th>
					      	<th scope="col">Type</th>
					      	<th scope="col">Zichtbaar</th>
					      	<th scope="col"> </th>
					      	<th scope="col"> </th>
   						 </tr>
  					</thead>
  					<tbody>
    					{forms}
   					</tbody>
				</table>
            </div>
        </div>
	</div>
</div>



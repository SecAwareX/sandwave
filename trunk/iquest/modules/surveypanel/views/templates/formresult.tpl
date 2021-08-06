<div class="row">
	<div class="col-md-12 col-xs-12">
		<div class="x_panel">
			<div class="x_title">
            	<h2>iQuest | <small>Formulier : {formName}</small></h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
            <a href="{baseURL}showclient/{clientID}" class="btn btn-info" role="button" aria-pressed="true"><i class="fa fa-home"></i>&nbsp;&nbsp;Terug naar Dossier</a><br><br>
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
     			{clientData}
            </div>
        </div>
	</div>
</div>


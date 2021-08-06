<div class="row">
	<div class="col-md-12 col-xs-12">
		<div class="x_panel">
			<div class="x_title">
            	<h2>iQuest | <small>Scoregroep toevoegen aan {formname}</small></h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
             <a href="{baseURL}changeform/{formID}/#Scores" class="btn btn-info" role="button" aria-pressed="true"><i class="fa fa-home"></i>&nbsp;&nbsp;Terug naar Vragenlijst</a><br><br>
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
     			<form class="form-horizontal form-label-left input_mask" method="POST" action="{baseURL}changescoregroup/{formID}/{groupID}">
     				<div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
						<label class="control-label left">Score omschrijving</label>
						<input type="text" class="form-control id="scoreGroup" placeholder="Geef hier de groepnaam op" name="scoreGroup" value="{f_scoreGroup}" >
					</div>
					<div class="col-xs-6 form-group has-feedback">
						<label class="control-label left">Start van de vraag range</label>
						<input type="text" class="form-control id="groupStartRange" placeholder="Start van de range  laag" name="groupStartRange" value="{f_groupStartRange}" >
					</div>
					<div class="col-xs-6 form-group has-feedback">
						<label class="control-label left">Eind van de vraag range <small></label>
						<input type="text" class="form-control id="groupEndRange" placeholder="Eind van de range" name="groupEndRange" value="{f_groupEndRange}" >
					</div>
					
					<div class="clearfix"></div>
					
					<div id="optionFields"></div>
					<div class="col-md-3 col-sm-3 col-xs-6 form-group buttonGroup">
						<button type="submit" class="btn btn-primary" name="changescoregroup">Wijzig scoregroep</button>
					</div>
				</form>
            </div>
        </div>
	</div>
</div>


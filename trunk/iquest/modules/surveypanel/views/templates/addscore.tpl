<div class="row">
	<div class="col-md-12 col-xs-12">
		<div class="x_panel">
			<div class="x_title">
            	<h2>iQuest | <small>Score toevoegen aan {formname}</small></h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
            <a href="{baseURL}changeform/{formID}" class="btn btn-info" role="button" aria-pressed="true"><i class="fa fa-home"></i>&nbsp;&nbsp;Terug naar Vragenlijst</a><br><br>
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
     			<form class="form-horizontal form-label-left input_mask" method="POST" action="{baseURL}addscore/{formID}">
     				<div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
						<label class="control-label left">Score omschrijving</label>
						<input type="text" class="form-control id="scoreDesription" placeholder="Vul hier de score omschrijving toe" name="scoreDesription" value="{f_scoreDesription}" >
					</div>
					<div class="col-xs-6 form-group has-feedback">
						<label class="control-label left">Score laag</label>
						<input type="text" class="form-control id="scoreLow" placeholder="Score laag" name="scoreLow" value="{f_scoreLow}" >
					</div>
					<div class="col-xs-6 form-group has-feedback">
						<label class="control-label left">Score hoog <small>* Alleen verplicht bij Range tussen..</small></label>
						<input type="text" class="form-control id="scoreHigh" placeholder="Score hoog" name="scoreHigh" value="{f_scoreHigh}" >
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback">
						<label>Voorwaarde</label>
						<select class="form-control" id="comparison" name="comparison">
							<option value="0">Kies een voorwaarde</option>
							{options}
						</select>
					</div>
					<div class="clearfix"></div>
					
					<div id="optionFields"></div>
					<div class="col-md-3 col-sm-3 col-xs-6 form-group buttonGroup">
						<button type="submit" class="btn btn-primary" name="addscore">Voeg score toe</button>
					</div>
				</form>
            </div>
        </div>
	</div>
</div>


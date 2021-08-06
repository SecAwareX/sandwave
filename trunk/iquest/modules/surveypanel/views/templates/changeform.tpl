<div class="row">
	<div class="col-md-12 col-xs-12">
		<div class="x_panel">
			<div class="x_title">
            	<h2>iQuest | Stap1 <small>Vragenlijst wijzigen</small></h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
             <a href="{baseURL}forms" class="btn btn-info" role="button" aria-pressed="true"><i class="fa fa-home"></i>&nbsp;&nbsp;Terug naar Vragenlijsten</a><br><br>
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
	  			<form class="form-horizontal form-label-left input_mask" method="POST" action="{baseURL}changeform/{formID}">
					<h1>&nbsp;Soort vragenlijst</h1><br /><br />
					<div class="form-group">
						<label class="control-label left">&nbsp;&nbsp;&nbsp;Complexiteit (type lijst) </label>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<div id="complex" class="btn-group" data-toggle="buttons">
								<label class="btn btn-default {simpleActive}" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
									<input type="radio" name="complex" value="simple" {simplechecked}> &nbsp; Simpel &nbsp;
								</label>
								<label class="btn btn-default {mediumActive}" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
									<input type="radio" name="complex" value="medium" {mediumchecked}> Medium
								</label>
								<label class="btn btn-default {complexActive}" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
									<input type="radio" name="complex" value="complex" {complexchecked}> Complex
								</label>
							</div>
						</div>
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback">
					<label>Bedrijf</label>
						<select class="form-control" id="companys" name="companyIDSelected">
							<option value="0">Kies een bedrijf (indien van toepassing)</option>
							{companys}
						</select>
					</div>
				    <hr style="width: 100%; color: #337ab7; height: 1px; background-color:#337ab7;" />
				    <h1>&nbsp;Vragenlijst Naam &amp; Beschrijving</h1><br /><br />
					<div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback">
						<label class="control-label left">Vragenlijstnaam</label>
						<input type="text" class="form-control has-feedback-left" id="inputSuccess2" placeholder="Naam vragenlijst" name="f_listName" value="{f_listName}" >
						<span class="fa fa-user form-control-feedback left" aria-hidden="true"></span>
					</div><br />
				    <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback"">
						<br /><label for="exampleFormControlTextarea1">Beschrijving</label>
							  <textarea class="form-control rounded-0" id="exampleFormControlTextarea1" rows="10" name="f_decription" placeholder="Geef hier de inleidende beschrijving op van de vragenlijst indien van toepassing">{f_decription}</textarea>
					</div>
					 <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback"">
    					<br /><label for="exampleFormControlTextarea2">Score interpretatie <small>enkel bedoelt voor de arts</small></label>
    					<textarea class="form-control rounded-0" id="exampleFormControlTextarea2" rows="10" name="f_inter" placeholder="Geef hier de interpretatie op indien van toepassing">{f_inter}</textarea>
						</div>
				    <hr style="width: 100%; color: #337ab7; height: 1px; background-color:#337ab7;" />
					<br />
					<button type="submit" class="btn btn-primary" name="dochangeForm" {doAdd_dis}>Wijzig vragenlijst</button>
					<!--  <a class="btn btn-primary {step2_dis}" href="{baseURL}addQuestion/{FormID}" role="button">Voeg vragen toe</a>
					<a class="btn btn-primary {step2_dis}" href="{baseURL}addScore/{FormID}" role="button">Voeg scores toe</a> -->
				</form><br />
				<div class="col-sm-12 nav-tab-holder">
					<ul class="nav nav-tabs row" role="tablist">
						<li role="presentation" class="active col-sm-6 tabbutton"><a href="#Questions" aria-controls="Questions" role="tab" data-toggle="tab">Vragen</a></li>
						<li role="presentation" class="col-sm-6 tabbutton"><a href="#Scores" aria-controls="Scores" role="tab" data-toggle="tab">Scores</a></li>
						<li role="presentation" class="col-sm-6 tabbutton"><a href="#Preview" aria-controls="Preview" role="tab" data-toggle="tab">Preview</a></li>
					</ul>
				</div>
				<div id="currentTab" class="invisible">{currentTab}</div>
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane active" id="Questions">
						<div class="x_panel">
							<div class="x_title">
								<h2>Vragen | <small>{formname}</small></h2>
								<div class="clearfix"></div>
							</div>
							<div class="x_content">
							<div class="alert alert-succes normal" role="alert">
	 							<p id="formQuestionSuccesMessage">{sFormSucces}</p>
	  							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
	    							<span aria-hidden="true">&times;</span>
	  							</button>
	  						</div><br />
							<div class="alert alert-danger" role="alert">
	 							<p id="formQuestionMessage">{sFormOutput}</p>
	  								<button type="button" class="close" data-dismiss="alert" aria-label="Close">
	    							<span aria-hidden="true">&times;</span>
	  								</button>
	  						</div>
								<a class="btn btn-primary {step2_dis}" href="{baseURL}addquestion/{formID}" role="button">Voeg vragen toe</a>
								<table class="table table-striped">
			  					<thead>
			    					<tr>
								    	<th scope="col">VraagID</th>
								      	<th scope="col">Vraag</th>
								      	<th scope="col">Antwoordtype</th>
								     	<th scope="col"></th>
								     	<th scope="col"></th>
			   						 </tr>
			  					</thead>
			  					<tbody>
			    					{questions}
			   					</tbody>
							</table>
							</div>
						</div><!-- End tab panel -->
					</div>
					<div role="tabpanel" class="tab-pane" id="Scores">
						<div class="x_panel">
							<div class="x_title">
								<h2>Scores | <small>{formname}</small></h2>
								<div class="clearfix"></div>
							</div>
							<div class="x_content">
							<div class="alert alert-succes normal" role="alert">
	 							<p id="formScoreSuccesMessage">{sFormSucces}</p>
	  							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
	    							<span aria-hidden="true">&times;</span>
	  							</button>
	  						</div><br />
							<div class="alert alert-danger" role="alert">
	 							<p id="formScoreMessage">{sFormOutput}</p>
	  								<button type="button" class="close" data-dismiss="alert" aria-label="Close">
	    							<span aria-hidden="true">&times;</span>
	  								</button>
	  						</div>
							<a class="btn btn-primary {step2_dis}" href="{baseURL}addscore/{formID}" role="button">Voeg Score toe</a>
								<table class="table table-striped">
			  					<thead>
			    					<tr>
								    	<th scope="col">ScoreID</th>
								      	<th scope="col">Score</th>
								      	<th scope="col">Laag</th>
								     	<th scope="col">Hoog</th>
								     	<th scope="col">Voorwaarde</th>
			   						 </tr>
			  					</thead>
			  					<tbody>
			    					{scores}
			   					</tbody>
							</table>
							</div>
						</div><!-- End tab panel -->
					</div>
					<div role="tabpanel" class="tab-pane" id="Preview">
						<div class="x_panel">
							<div class="x_title">
								<h2>Preview | <small>Welkom</small></h2>
								<div class="clearfix"></div>
							</div>
							<div class="x_content">
							{formIntro}
	  			
	  						{surveyForm}
							</div>
						</div><!-- End tab panel -->
					</div>
				</div>
			</div><!-- End panel content -->
		</div><!-- End panel -->
	</div>
</div>


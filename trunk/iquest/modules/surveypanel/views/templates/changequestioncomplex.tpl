<div class="row">
	<div class="col-md-12 col-xs-12">
		<div class="x_panel">
			<div class="x_title">
            	<h2>iQuest | <small>Vraag wijzigen in {formname}</small></h2>
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
     			<form class="form-horizontal form-label-left input_mask" method="POST" action="{baseURL}changequestion/{formID}/{questionID}">
     				<div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback">
						<label>Kies een vraag groep</label>
						<select class="form-control" id="questionGroup" name="f_QuestionGroup">
							{questionGroups}
						</select>
					</div>
     				<div class="col-md-6 col-sm-12 col-xs-12 form-group has-feedback">
						<label class="control-label left">Nieuwe vraag groep</label>
						<input type="text" class="form-control id="inputSuccess2" placeholder="Geef een nieuwe groep naam op indien nodig" name="f_questionGroupNew" value="{f_questionGroupNew}" >
					</div>
     				<hr style="width: 100%; color: grey; height: 1px; background-color:grey;top:10px;position:relative;" />
     				<div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
						<label class="control-label left">Vraag / stelling</label>
						<input type="text" class="form-control id="inputSuccess2" placeholder="Vraag of stelling" name="f_question" value="{f_question}" >
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback">
						<label>Is een subvraag van</label>
						<select class="form-control" id="subQuestion" name="f_subQuestion">
							{parentQuestions}
						</select>
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback">
						<label>Type Antwoord</label>
						<select class="form-control" id="answerTypes" name="f_answerTypes">
							{questionTypes}
						</select>
					</div>
					<div class="col-xs-4 form-group has-feedback numberAnswers">
						<label class="control-label left">Aantal mogelijke antwoorden</label>
						<input type="text" class="form-control id="possibleAnswers" placeholder="Aantal antwoorden" name="f_possibleAnswers" value="{f_possibleAnswers}" >
					</div>
					<div class="clearfix"></div>
					<div class="col-md-3 col-sm-3 col-xs-6 form-group buttonGroup">
						<button type="submit" class="btn btn-primary" name="changequestion">Wijzig vraag</button>
					</div>
					<div class="clearfix"></div>
					<hr class="optionField" style="width: 100%; color: #337ab7; height: 1px; background-color:#337ab7;" />
					<h2 class="optionField">Opties</h2>
					<div class="col-md-3 col-sm-3 col-xs-6 form-group optionField">
						<label for="option" class="control-label">Keuze optie</label>
						<input type="text" class="form-control id="option" placeholder="Geef hier een antwoord optie op" name="f_option" value="" >
					</div>
					<div class="col-md-3 col-sm-3 col-xs-6 form-group optionField">
						<label for="option" class="control-label">Score</label>
						<input type="text" class="form-control id="option" placeholder="Geef hier de score / waarde van de optie van de" name="f_score" value="" >
					</div>
					<div class="col-md-3 col-sm-3 col-xs-6 form-group optionField buttonGroup">
						<div id="addOption" class="btn btn-primary" role="button">Voeg optie toe</div>
					</div><div class="clearfix"></div>
					<div id="optionFields">{optionFields}</div>
					
					
				</form>
            </div>
        </div>
	</div>
</div>


<div class="row">
	<div class="col-md-12 col-xs-12">
		<div class="x_panel">
			<div class="x_title">
            	<h2>iQuest | Stap1 <small>Vragenlijst toevoegen</small></h2>
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
     			 <form class="form-horizontal form-label-left input_mask" method="POST" action="{baseURL}{destination}">
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
                      <button type="submit" class="btn btn-primary" name="doAddForm" {doAdd_dis}>Voeg vragenlijst toe</button>
                      <button type="submit" class="btn btn-primary" name="dochangeForm" {change_dis}>Wijzig vragenlijst</button>
                      <a class="btn btn-primary {step2_dis}" href="{baseURL}changeform/{newFormID}#Questions" role="button">Door naar stap 2</a>
     			 </form>
            </div>
        </div>
	</div>
</div>


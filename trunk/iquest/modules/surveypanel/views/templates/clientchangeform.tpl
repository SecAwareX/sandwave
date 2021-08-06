<div class="row">
	<div class="col-md-12 col-xs-12">
		<div class="x_panel">
			<div class="x_title">
            	<h2>iQuest | <small>Client wijzigen</small></h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
            <a href="{baseURL}clients" class="btn btn-info" role="button" aria-pressed="true"><i class="fa fa-home"></i>&nbsp;&nbsp;Terug naar Clienten</a><br><br>
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
     			 <form class="form-horizontal form-label-left input_mask" method="POST" action="{baseURL}changeclient/{ClientID}">
     			  	<h1>&nbsp;Client gegevens</h1><br /><br />
     			  	<div class="form-group">
                  		<label class="control-label left">&nbsp;&nbsp;&nbsp;Geslacht</label>
                  			<div class="col-md-6 col-sm-6 col-xs-12">
                  				<div id="gender" class="btn-group" data-toggle="buttons">
	                  				<label class="btn btn-default {maleActive}" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
	                  					<input type="radio" name="gender" value="male" {malechecked}> &nbsp; Man &nbsp;
	                 				</label>
	                  				<label class="btn btn-default {femaleActive}" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
	                 			 		<input type="radio" name="gender" value="female" {femalechecked}> vrouw
	                  				</label>
                  				</div>
                  			</div>
                 		</div>
                 		<div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback">
                 			<label class="control-label left">Voornaam</label>
                        	<input type="text" class="form-control has-feedback-left" id="inputSuccess2" placeholder="voornaam" name="f_firstName" value="{f_firstName}" >
                        	<span class="fa fa-user form-control-feedback left" aria-hidden="true"></span>
                      </div>
                      <div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback">
                      <label class="control-label left">Achternaam</label>
                      	<input type="text" class="form-control" id="inputSuccess3" placeholder="achternaam" name="f_lastName" value="{f_lastName}">
                      	<span class="fa fa-user form-control-feedback right" aria-hidden="true"></span>
                      </div>
                      <div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback">
                      <label class="control-label left">Geboortedatum</label>
                        <input type="text" class="form-control has-feedback-left" id="inputSuccess4" placeholder="Geboortedatum | dd-mm-yyyy" name="f_dateOfBirth" value="{f_dateOfBirth}">
                        <span class="fa fa-calendar form-control-feedback left" aria-hidden="true"></span>
                      </div>

                      <div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback">
                      <label class="control-label left">Email</label>
                        <input type="text" class="form-control" id="inputSuccess5" placeholder="Email" name="f_email" value="{f_email}">
                        <span class="fa fa-envelope form-control-feedback right" aria-hidden="true"></span>
                      </div>
                      <div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback">
                       <label>Volgende afspraak</label>
                        <input type="text" class="date-picker form-control has-feedback-left" id="inputSuccess6" placeholder="Volgende afspraak" name="f_appointment" value="{f_appointment}">
                        <span class="fa fa-calendar form-control-feedback left" aria-hidden="true"></span>
                      </div>
                      
                      
                     <hr style="width: 100%; color: #337ab7; height: 1px; background-color:#337ab7;" />
                     
                      <h1>&nbsp;Bedrijfs formulieren</h1><br /><br />
                      <div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback">
                       <label>Bedrijf</label>
                        <select class="form-control" id="companys" name="companyIDSelected">
  							<option value="0">Kies een bedrijf</option>
  							{companys}
						</select>
						{companysQuestions}
                      </div>
                      
                       <hr style="width: 100%; color: #337ab7; height: 1px; background-color:#337ab7;" />
                      <h1>&nbsp;Formulieren</h1><br /><br />
                      {iquestForms}
                 		
                      <br />
                      <button type="submit" class="btn btn-primary" name="doChangeClient" {doAdd_dis}>Wijzig client</button>
     			 </form>
            </div>
        </div>
	</div>
</div>


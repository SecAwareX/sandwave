<div class="row">
	<div class="col-md-12 col-xs-12">
		<div class="x_panel">
			<div class="x_title">
            	<h2>iQuest | <small>Bedrijf toevoegen</small></h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
            <a href="{baseURL}companys" class="btn btn-info" role="button" aria-pressed="true"><i class="fa fa-home"></i>&nbsp;&nbsp;Terug naar Bedrijven</a><br><br>
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
       <form method="post" action="{baseURL}addcompany">
  <label for="inlineFormInputGroup">Bedrijfsnaam</label>
  <div class="input-group mb-2 mr-sm-2 mb-sm-0">
    <div class="input-group-addon"><i class="fa fa-building" aria-hidden="true"></i></div>
    <input type="text" class="form-control" id="inlineFormInputGroup" placeholder="Bedrijfsnaam" name="f_CompanyName" value="{Field_CompanyName}">
  </div>

   <button type="submit" class="btn btn-primary" name="doAddCompany" {doAdd_dis}>Voeg bedrijf toe</button>
  <a class="btn btn-primary {change_dis}" href="{baseURL}changecompany/{newCompanyID}" role="button">Bedrijf wijzigen</a>
</form>
            </div>
        </div>
	</div>
</div>
<div class="row">
	<div class="col-md-12 col-xs-12">
		<div class="x_panel">
			<div class="x_title">
            	<h2>iQuest | <small>Gebruiker wijzigen</small></h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
            <a href="{baseURL}users" class="btn btn-info" role="button" aria-pressed="true"><i class="fa fa-home"></i>&nbsp;&nbsp;Terug naar Gebruikers</a><br><br>
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
       <form method="post" action="{baseURL}changeuser/{userID}">
  <label for="inlineFormInputGroup">Schermnaam</label>
  <div class="input-group mb-2 mr-sm-2 mb-sm-0">
    <div class="input-group-addon"><i class="fa fa-desktop" aria-hidden="true"></i></div>
    <input type="text" class="form-control" id="inlineFormInputGroup" placeholder="Schermnaam" name="f_ScreenName" value="{Field_screenname}">
  </div>

  <label for="inlineFormInputGroup">Username</label>
  <div class="input-group mb-2 mr-sm-2 mb-sm-0">
    <div class="input-group-addon"><i class="fa fa-user"></i></div>
    <input type="text" class="form-control" id="inlineFormInputGroup" placeholder="Gebruikernaam / E-mailadres" name="f_UserName" value="{Field_username}">
  </div>
  
  <label for="inlineFormInputGroup">Wachtwoord</label>
  <div class="input-group mb-2 mr-sm-2 mb-sm-0">
    <div class="input-group-addon"><i class="fa fa-eye"></i></div>
    <input type="text" class="form-control" id="inlineFormInputGroup" placeholder="Wachtwoord" name="f_Pass" value="{Field_pass}" >
  </div>

  <div class="form-check mb-2 mr-sm-2 mb-sm-0">
    <label class="form-check-label">
      <input class="form-check-input" type="checkbox" name="doMail" {checked}> Mail de gewijzigde login gegevens aan de gebruiker
    </label>
  </div>

  <button type="submit" class="btn btn-primary" name="doChangeUser">Wijzig gebruiker</button>
</form>
            </div>
        </div>
	</div>
</div>
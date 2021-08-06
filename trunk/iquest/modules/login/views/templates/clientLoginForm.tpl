 <body class="login">
    <div>
      <a class="hiddenanchor" id="resetpass"></a>
      <a class="hiddenanchor" id="login"></a>

      <div class="login_wrapper">
        <div class="animate form login_form">
          <section class="login_content">
           <br />
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
            <form action="{baseURL}login" method="POST">
              <h1>iQuest Client login</h1>
              <div>
                <input type="text" class="form-control" placeholder="Email" required="" name="Username"/>
              </div>
              <div>
                <input type="password" class="form-control" placeholder="Password" required="" name="Pass"/>
              </div>
               <div class="buttonContainer">
               <input class="btn btn-primary loginBTN" type="submit" name="doLogin" value="Inloggen">
               <input class="btn btn-outline-secondary" type="reset" value="Reset"> 
             </div>

              <div class="clearfix"></div>

              <div class="separator">
                <p class="change_link">Wachtwoord vergeten?
                  <a href="#resetpass" class="to_register"> Wijzig wachtwoord </a>
                </p>

                <div class="clearfix"></div>
                <br />

                <div>
                  <h1><i class="fa fa-question-circle"></i> iQuest</h1>
                  <p>&copy 2018 All Rights Reserved. Mareis B.V.</p>
                </div>
              </div>
            </form>
          </section>
        </div>

        <div id="register" class="animate form registration_form">
          <section class="login_content">
          <br >
          <div class="alert alert-danger" role="alert">
 				<p id="formMessageReset">{sFormOutput}</p>
  				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
    				<span aria-hidden="true">&times;</span>
  				</button>
  			</div>
  			
            <form action="{baseURL}wachtwoordvergeten/#resetpass" method="POST">
              <h1>Wachtwoord vergeten</h1>
             <div>
                <input type="email" class="form-control" placeholder="Email" required="" name="email"/>
              </div>
              <div>
               <input class="btn btn-primary loginBTN" type="submit" name="doPassReset" value="Verstuur wachtwoord">
              </div>

              <div class="clearfix"></div>

              <div class="separator">
                <p class="change_link">iQuest?
                  <a href="#login" class="to_register"> Inloggen </a>
                </p>

                <div class="clearfix"></div>
                <br />

                <div>
                  <h1><i class="fa fa-question-circle"></i> iQuest</h1>
                  <p>&copy 2018 All Rights Reserved. Mareis B.V.</p>
                </div>
              </div>
            </form>
          </section>
        </div>
      </div>
    </div>
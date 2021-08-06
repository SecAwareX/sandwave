<body class="login">
    <div>
	<div class="login_wrapper">
        <div class="animate form login_form">
          <section class="login_content">
            <form action="" method="POST">
              <h1> Inloggen iQuest Dashboard </h1>
             	<div class="alert alert-danger" role="alert">
 					<p id="formMessage">{sFormOutput}</p>
  						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
    						<span aria-hidden="true">&times;</span>
  						</button></div>
  				
              <div>
                <input type="text" class="form-control" placeholder="Username" name="Username" required/>
              </div>
              <div>
                <input type="password" class="form-control" placeholder="Password" name="Pass" required/>
              </div>
              <div class="buttonContainer">
               <input class="btn btn-primary loginBTN" type="submit" name="doAdminLogin" value="Submit">
               <input class="btn btn-outline-secondary" type="reset" value="Reset"> 
             </div>

              <div class="clearfix"></div>

              <div class="separator">

                <div class="clearfix"></div>
                <br />

                <div>
                  <h1><i class="fa fa-question-circle"></i> iQuest</h1>
                  <p>&copy;2018 All Rights Reserved. Mareis B.V.</p>
                </div>
              </div>
            </form>
          </section>
        </div>
      </div>
    </div>
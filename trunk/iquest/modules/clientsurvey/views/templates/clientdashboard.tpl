<body class="nav-md">
    <div class="container body">
      <div class="main_container">
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
             <h1><a href="index.html" class="site_title"><i class="fa fa-question-circle"></i> <span>iQuest</span></a></h1>
            </div>

            <div class="clearfix"></div>

            <!-- menu profile quick info -->
            <div class="profile">
              <div class="profile_pic">
                <i class="fa fa-user" aria-hidden="true"></i>
              </div>
              <div class="profile_info">
                <span>Welkom,</span>
                <h2>{userName}</h2>
              </div>
            </div>
            <!-- /menu profile quick info -->

            <br />

            <!-- sidebar menu -->
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
              <div class="menu_section">
                <h3>Menu</h3>
                <ul class="nav side-menu">
                <li><a href="{baseURL}"><i class="fa fa-home"></i>Welkom </a></li>
                <li><a><i class="fa fa-edit"></i> Formulieren<span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      {formsNav}
                    </ul>
                  </li>
                  <li><a href="{baseURL}uitloggen"><i class="fa fa-sign-out" aria-hidden="true"></i>Uitloggen</a></li>
              </ul>
              </div>
              

            </div>
            <!-- /sidebar menu -->

            <!-- /menu footer buttons -->
            <div class="sidebar-footer hidden-small">
             
            </div>
            <!-- /menu footer buttons -->
          </div>
        </div>
		<div class="top_nav">
          <div class="nav_menu">
            <nav>
              <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
              </div>
            </nav>
          </div>
        </div>
		
       

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            {moduleContent}
          </div>
        </div>
        <!-- /page content -->

        <!-- footer content -->
        <footer>
          <div class="pull-right">
            iQuest - Mareis B.V. 
          </div>
          <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->
      </div>
    </div>

   
    
   
   
   
  
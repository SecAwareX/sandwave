<!--<div class="row top_tiles">
	<div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
    	<div class="tile-stats">
        	<div class="icon"><i class="fa fa-caret-square-o-right"></i></div>
            <div class="count">179</div>
            <h3>New iQuest invitations</h3>
            <p>Totaal aantal iQuest uitnodigingen</p>
         </div>
    </div>
    <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
    	<div class="tile-stats">
   			<div class="icon"><i class="fa fa-sort-amount-desc"></i></div>
    		<div class="count">179</div>
    		<h3>Accepted iQuest invitations</h3>
    		<p>Het totaal aantal geaccepteerde uitnodigingen</p>
    	</div>
    </div>
	<div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
		<div class="tile-stats">
			<div class="icon"><i class="fa fa-square-o"></i></div>
			<div class="count">179</div>
			<h3>Open iQuest surveys</h3>
			<p>Het aantal ingevulde lijsten</p>
		</div>
	</div>
	<div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
		<div class="tile-stats">
			<div class="icon"><i class="fa fa-check-square-o"></i></div>
			<div class="count">179</div>
			<h3>iQuest surveys filled in</h3>
			<p>Het aantal ingevulde lijsten</p>
		</div>
	</div>
</div>-->
<div class="row">
	<div class="col-md-12 col-xs-12">
		<div class="x_panel">
			<div class="x_title">
            	<h2>iQuest | <small>Client zoeken</small></h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
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
  			<form class="form-horizontal form-label-left input_mask" method="POST" action="https://iquest.mareis.nl/surveypanel/">
     				<div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
						<label class="control-label left">Zoek query</label>
						<input type="text" class="form-control id="inputSuccess2" placeholder="Zoek query" name="serachQuery" value="{f_serachQuery}" >
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback">
						<label>Zoek type</label>
						<select class="form-control" id="type" name="f_searchType">
							{searchTypes}
						</select>
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback">
						<label>Statussen</label>
						<select class="form-control" id="status" name="f_searchStatus">
							{searchStatus}
						</select>
					</div>
					<div class="col-md-3 col-sm-3 col-xs-6 form-group optionField buttonGroup">
						<!-- <div id="search" class="btn btn-primary" role="button">Filter / zoek</div>-->
						<input type="submit" id="search" class="btn btn-primary" role="button" value="Filter / zoek" />
					</div>
			</form>
  			<h2>Gefilterd op : <small>{filterName}</small></h2>
            	<table class="table table-striped">
  					<thead>
    					<tr>
					    	<th scope="col">ClientID</th>
					      	<th scope="col">Client naam</th>
					      	<th scope="col">E-mail</th>
					      	<th scope="col">Bedrijf</th>
					      	<th scope="col">Openstaand</th>
					      	<th scope="col">Bezig</th>
					     	<th scope="col">Gesloten</th>
					     	<th scope="col">Uitgenodigd op</th>
					     	<th scope="col"></th>
					     	<th scope="col"></th>
   						 </tr>
  					</thead>
  					<tbody>
    					{clients}
   					</tbody>
				</table>
            </div>
        </div>
	</div>
</div>

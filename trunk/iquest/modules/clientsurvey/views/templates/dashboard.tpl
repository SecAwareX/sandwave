<div id="dashboard">
	<div class="row">
		<div class="col-sm-12 nav-tab-holder">
			<ul class="nav nav-tabs row" role="tablist">
				<li role="presentation" class="active col-sm-6 tabbutton"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Welkom</a></li>
				<li role="presentation" class="col-sm-6 tabbutton"><a href="#forms" aria-controls="forms" role="tab" data-toggle="tab">Formulieren</a></li>
			</ul>
		</div>
	</div>
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="home">
			<div class="x_panel">
				<div class="x_title">
					<h2>iQuest | <small>Welkom</small></h2>
					<div class="clearfix"></div>
				</div>
				<div class="x_content">
				<p>Beste {userName},</p>
				<p>
				Bedankt dat je de moeite wilt nemen om de vragenlijst(en), die ik voor je heb klaargezet, in te vullen. 
				Ik wil je succes wensen bij het invullen. mochten er vragen zijn, mail me dan op <a href="mailto:wichgers@mareis.nl">wichgers@mareis.nl</a>.
				</p>
				<p>
				De antwoorden zullen vertrouwelijk worden behandeld. De uitkomst van de vragenlijst zal met jou besproken worden en advies zal volgen tijdens het volgende spreekuurcontact.
				</p>
				<p>
				met vriendelijke groet Maaike Wichgers, bedrijfsarts.
				</p>		
				</div>
			</div>
		</div>
		<div role="tabpanel" class="tab-pane " id="forms">
			<div class="x_panel">
				<div class="x_title">
					<h2>iQuest | <small>Welkom</small></h2>
					<div class="clearfix"></div>
				</div>
				<div class="x_content">
				 <div class="alert alert-danger" role="alert">
	 				<p id="formMessage">{sFormOutput}</p>
	  				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
	    				<span aria-hidden="true">&times;</span>
	  				</button>
	  			</div>
					<table class="table table-striped">
  					<thead>
    					<tr>
					    	<th scope="col">Formulier</th>
					      	<th scope="col">Toegevoegd op</th>
					      	<th scope="col">Tijd resterend</th>
					      	<th scope="col">status</th>
   						 </tr>
  					</thead>
  					<tbody>
    					{forms}
   					</tbody>
				</table>
				</div>
			</div>
		</div>
	</div>
</div>

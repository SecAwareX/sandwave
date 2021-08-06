$(document).ready(function(){
	
	$(".optionField").css("display","none");
	$(".numberAnswers").css("display","none");
	$(".answerOptionsDiv").css("display","none");
	
	//current tab after redirect from addQuestions / addscores etc
	var currentTab = $("#currentTab").text();
	if(currentTab == "Questions")
		{
		
		 $('html,body').animate({
	    	   scrollTop: $("#currentTab").offset().top
	    	});
		}else if(currentTab == "Scores")
			{
				$("#Questions").removeClass("active");
			    $( ".nav-tabs li:first" ).removeClass("active");
			    $("#Scores").addClass("active");
			    $('.nav-tabs li:nth-child(2)').addClass("active");
			    
			    $('html,body').animate({
			    	   scrollTop: $("#currentTab").offset().top
			    	});
			}else if(currentTab == "Preview")
				{
				
				}
	
	//Check what is the default type
	var iTypeID = $("#answerTypes").val();
	if(iTypeID == 3)
	{
		$(".optionField").css("display","block");
		$(".numberAnswers").css("display","block");
		$(".answerOptionsDiv").css("display","block");
	}
	
	//Answer Types
	$('#answerTypes').on('change', function() {
		
		var option = $(this).find(":selected").val() ;
		if(option == 3)
		{
			$(".optionField").css("display","block");
			$(".numberAnswers").css("display","block");
			$(".answerOptionsDiv").css("display","block");
		}else
			{
				
				$(".optionField").css("display","none");
				$("input[name=f_possibleAnswers]").val(1);
				$(".numberAnswers").css("display","none");
				$(".answerOptionsDiv").css("display","none");
			}
	   
	});
	//Get the current number of options
	var numberOfOptions = 1;
	$('#optionFields .optionWrapper').each(function() {
		
		var idString = $(this).attr('id');
		var ID = idString.split("_");
		var optionID = parseInt(ID[1])+1;
		if( optionID > numberOfOptions)
		{
			numberOfOptions = optionID;
		}
	});
	
	//Add option ->used in add question 
	$('#addOption').on('click', function() {
		
	  
		var optionField = '';
		optionField +=  '<div id="optionWrap_'+numberOfOptions+'" class="optionWrapper">';
	    optionField +=  '<div class="col-md-2 col-sm-2 col-xs-6 form-group optionField">';
	    optionField += '<label for="option" class="control-label">Optie</label>';
	    optionField += '<input type="text" class="form-control id="option'+numberOfOptions+'" placeholder="Keuze optie" name="optionField_'+numberOfOptions+'" value="" >';
	    optionField += '</div>';
	    optionField +=  '<div class="col-md-2 col-sm-2 col-xs-6 form-group optionField">';
	    optionField += '<label for="option" class="control-label">Score</label>';
	    optionField += '<input type="text" class="form-control id="optionScore_'+numberOfOptions+'" placeholder="Optie score" name="optionScoreField_'+numberOfOptions+'" value="" >';
	    optionField += '</div>';
	    optionField += '<div class="col-md-3 col-sm-3 col-xs-6 form-group optionField buttonGroup">';
	    optionField += '<div id="'+numberOfOptions+'" class="btn btn-danger deleteOption" role="button">Verwijder optie</div>';
	    optionField += '</div>';
	    optionField += '<div id="clear_'+numberOfOptions+'" class="clearfix"></div>';
	    optionField += '</div>';
	    
	   $('#optionFields').append(optionField);
	   $('input[name="optionField_'+numberOfOptions+'"]').val($('input[name="f_option"]').val());
	   $('input[name="f_option"]').val("");
	   $('input[name="optionScoreField_'+numberOfOptions+'"]').val($('input[name="f_score"]').val());
	   $('input[name="f_score"]').val("");
	   
	   numberOfOptions++;
	   
	   $('.deleteOption').on('click', function() {
			
		   var idToDelete = $(this).attr("id");
		   var optionWrapper = $( "#optionWrap_"+idToDelete );
		   $( "#optionWrap_"+idToDelete ).remove();
		   
		   if(numberOfOptions > 1)
		   {
			   numberOfOptions-2;
		   }
		});
	   
	   
	});
	
	//END --- Add option ->used in add question 
	//DELETE a option when this is already in the DB, used in changequestion
	$('.deleteOptionDB').on('click', function() {
		
		   var optionIDSplitted = $(this).attr("id").split("_");
			
			$.ajax({
				url : "https://iquest.mareis.nl/surveypanel/jqdeleteoption",
				type: "POST",
				data : "optionID="+optionIDSplitted[2],
			}).done(function(apiresponse){ 
				//alert(apiresponse);
				jsonResponse = jQuery.parseJSON(apiresponse);
				optionMessage(jsonResponse.APIStatus,jsonResponse.APIMessage);
				
				if(jsonResponse.APIStatus) {
					var idToDelete = optionIDSplitted[1];
					var optionWrapper = $( "#optionWrap_"+idToDelete );
					$( "#optionWrap_"+idToDelete ).remove();
					   
					if(numberOfOptions > 1)
					{
						numberOfOptions-2;
					}
				}
			});
			
		});
	//END --- DELETE a option when this is already in the DB, used in changequestion
	//ADDING a option in change question
	$('#addOptionDB').on('click', function() {
		
		var option = $('input[name="f_option"]').val();
		var optionScore = $('input[name="f_score"]').val();
		var questionID = $('input[name="questionID"]').val();
		
		$.ajax({
			url : "https://iquest.mareis.nl/surveypanel/jqaddoption",
			type: "POST",
			data : "option="+option+"&optionScore="+optionScore+"&questionID="+questionID,
		}).done(function(apiresponse){ 
			//alert(apiresponse);
			jsonResponse = jQuery.parseJSON(apiresponse);
			optionMessage(jsonResponse.APIStatus,jsonResponse.APIMessage);
			
			if(jsonResponse.APIStatus) {
				var optionField = '';
				optionField +=  '<div id="optionWrap_'+numberOfOptions+'" class="optionWrapper">';
			    optionField +=  '<div class="col-md-2 col-sm-2 col-xs-6 form-group optionField">';
			    optionField += '<label for="option" class="control-label">Optie</label>';
			    optionField += '<input type="text" class="form-control id="option'+numberOfOptions+'" placeholder="Keuze optie" name="optionField_'+numberOfOptions+'" value="" >';
			    optionField += '</div>';
			    optionField +=  '<div class="col-md-2 col-sm-2 col-xs-6 form-group optionField">';
			    optionField += '<label for="option" class="control-label">Score</label>';
			    optionField += '<input type="text" class="form-control id="optionScore_'+numberOfOptions+'" placeholder="Optie score" name="optionScoreField_'+numberOfOptions+'" value="" >';
			    optionField += '</div>';
			    optionField += '<div class="col-md-3 col-sm-3 col-xs-6 form-group optionField buttonGroup">';
			    optionField += '<div id="change_'+numberOfOptions+'_'+jsonResponse.newOptionID+'" class="btn btn-success changeOptionDBjs" role="button">wijzig optie</div>';
			    optionField += '<div id="delete_'+numberOfOptions+'_'+jsonResponse.newOptionID+'" class="btn btn-danger deleteOptionDBjs" role="button">Verwijder optie</div>';
			    optionField += '</div>';
			    optionField += '<div id="clear_'+numberOfOptions+'_'+jsonResponse.newOptionID+'" class="clearfix"></div>';
			    optionField += '</div>';
			    
			   $('#optionFields').append(optionField);
			   $('input[name="optionField_'+numberOfOptions+'"]').val($('input[name="f_option"]').val());
			   $('input[name="f_option"]').val("");
			   $('input[name="optionScoreField_'+numberOfOptions+'"]').val($('input[name="f_score"]').val());
			   $('input[name="f_score"]').val("");
			   
			   numberOfOptions++;
			   
			   $('.deleteOptionDBjs').on('click', function() {
					
				   var optionIDSplitted = $(this).attr("id").split("_");
					
					$.ajax({
						url : "https://iquest.mareis.nl/surveypanel/jqdeleteoption",
						type: "POST",
						data : "optionID="+optionIDSplitted[2],
					}).done(function(apiresponse){ 
						
						jsonResponse = jQuery.parseJSON(apiresponse);
						optionMessage(jsonResponse.APIStatus,jsonResponse.APIMessage);
						
						if(jsonResponse.APIStatus) {
							var idToDelete = optionIDSplitted[1];
							var optionWrapper = $( "#optionWrap_"+idToDelete );
							$( "#optionWrap_"+idToDelete ).remove();
							   
							if(numberOfOptions > 1)
							{
								numberOfOptions-2;
							}
						}
					});
					
				});
			   
			}
			//Change a option when we added it in changeQuestion
			$('.changeOptionDBjs').on('click', function() {
				
				var optionIDSplitted = $(this).attr("id").split("_");
				var option = $('input[name="optionField_'+optionIDSplitted[1]+'"]').val();
				var score = $('input[name="optionScoreField_'+optionIDSplitted[1]+'"]').val();
				
				$.ajax({
					url : "https://iquest.mareis.nl/surveypanel/jqchangeoption",
					type: "POST",
					data : "optionID="+optionIDSplitted[2]+"&option="+option+"&optionScore="+score,
				}).done(function(apiresponse){ 
					
					jsonResponse = jQuery.parseJSON(apiresponse);
					optionMessage(jsonResponse.APIStatus,jsonResponse.APIMessage);
					
				});
				
				
			});
			//END -- Change a option when we added it in changeQuestion
		});
		
		
	});
	//END -- ADDING a option in change question
	
	
	//Change a option when this already in the DB 
	$('.changeOptionDB').on('click', function() {
		
		
		var optionIDSplitted = $(this).attr("id").split("_");
		var option = $('input[name="optionField_'+optionIDSplitted[1]+'"]').val();
		var score = $('input[name="optionScoreField_'+optionIDSplitted[1]+'"]').val();
		
		$.ajax({
			url : "https://iquest.mareis.nl/surveypanel/jqchangeoption",
			type: "POST",
			data : "optionID="+optionIDSplitted[2]+"&option="+option+"&optionScore="+score,
		}).done(function(apiresponse){ 
			
			jsonResponse = jQuery.parseJSON(apiresponse);
			optionMessage(jsonResponse.APIStatus,jsonResponse.APIMessage);
			
		});
		
		
	});
	
});



function optionMessage(status,message)
{
	
	var messageClass = '';
	if(status) {
		messageClass = 'alert-succes';
	}else {
		messageClass = 'alert-warning';
	}
	
	var messageBox = '<div class="alert '+messageClass+' normal" role="alert">';
	messageBox += '<p>'+message+'</p>';
	messageBox += '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
	messageBox += '<span aria-hidden="true">&times;</span>';
	messageBox += '</button>';
	messageBox += '</div>';
	
	$("#optionMessageBox").html(messageBox);
}
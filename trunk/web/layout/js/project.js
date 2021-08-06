$(document).ready(function(){
	
	var sScreenMessage = $( "#formMessage" ).text();
	if(sScreenMessage == "")
	{
		$(".alert.alert-danger").css('display',"none");
	}
	
	var sScreenSuccesMessage = $( "#formSuccesMessage" ).text();
	if(sScreenSuccesMessage == "")
	{
		$(".alert.alert-succes.normal").css('display',"none");
	}
	
    
	var sInterText = $("#inter").html();
	$("#interBut").click(function() {
		
		if(sInterText !="") {
		var x=window.open('','','width=600, height=600');
		x.document.open();
		x.document.write(sInterText);
		x.document.close();
		}else {
			alert("Geen interpretatie ingevuld");
		}
	});
	
	
	
	//Geboortedatum
	$('input[name="f_dateOfBirth"]').daterangepicker({
	    "singleDatePicker": true,
	    "showDropdowns": true,
	    "locale": {
	        "format": "DD-MM-YYYY",
	        "separator": " - ",
	        "applyLabel": "Apply",
	        "cancelLabel": "Cancel",
	        "fromLabel": "From",
	        "toLabel": "To",
	        "customRangeLabel": "Custom",
	        "weekLabel": "W",
	        "daysOfWeek": [
	            "Zo",
	            "Ma",
	            "Di",
	            "Wo",
	            "Do",
	            "Vr",
	            "Za"
	        ],
	        "monthNames": [
	            "Januari",
	            "Februari",
	            "Maart",
	            "April",
	            "Mei",
	            "Juni",
	            "Juli",
	            "Augustus",
	            "September",
	            "Oktober",
	            "November",
	            "December"
	        ],
	        "firstDay": 1
	    },
	    "startDate": $('input[name="f_dateOfBirth"]').val(),
	    "endDate": "",
	    "drops": "up"
	}, function(start, end, label) {
	  console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
	});
	
	
	
	
	//Volgende afspraak f_appointment
	$('input[name="f_appointment"]').daterangepicker({
	    "singleDatePicker": true,
	    "locale": {
	        "format": "DD-MM-YYYY",
	        "separator": " - ",
	        "applyLabel": "Apply",
	        "cancelLabel": "Cancel",
	        "fromLabel": "From",
	        "toLabel": "To",
	        "customRangeLabel": "Custom",
	        "weekLabel": "W",
	        "daysOfWeek": [
	            "Zo",
	            "Ma",
	            "Di",
	            "Wo",
	            "Do",
	            "Vr",
	            "Za"
	        ],
	        "monthNames": [
	            "Januari",
	            "Februari",
	            "Maart",
	            "April",
	            "Mei",
	            "Juni",
	            "Juli",
	            "Augustus",
	            "September",
	            "Oktober",
	            "November",
	            "December"
	        ],
	        "firstDay": 1
	    },
	    "startDate": $('input[name="f_appointment"]').val(),
	    "endDate": "",
	    "drops": "up"
	}, function(start, end, label) {
	  console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
	});
	
	//alert($( "#companys" ).val());
	var iSelectedCompanyID = $( "#companys" ).val();
	if(iSelectedCompanyID != 0)
	{
		$("#CQuestions_"+iSelectedCompanyID).slideDown("fast");
	}

	
	$('#companys').on('change', function() {
		
		$(".companyQuestions").slideUp("fast");
	    var iCompanyID = $(this).find(":selected").val() ;
	    $("#CQuestions_"+iCompanyID).slideDown("fast");
	});
	
	
	//Tabs clientpanel
	var url  = $(location).attr('href');
	
	if(url.split("/").pop() == "#forms")
	{
		
		$("#home").removeClass("active");
		$( ".nav-tabs li:first" ).removeClass("active");
		$("#forms").addClass("active");
		$( ".nav-tabs li:last" ).addClass("active");
		$( "#formMessage" ).text("Het door u opgevraagde formulier bestaat niet of is niet toegangkelijk voor u.");
		$(".alert.alert-danger").css('display',"block");
		
	}
	
	//Client Forms
	var formStatus = $("#formStatus").text();
	var formType = $("#formType").text();
	
	if(formStatus == "init" && formType != "Simple" ) {
		$("#clientForm .form-group").css("display","none");
		$("#clientForm .form-group").first().css("display","block");
		
		
		if( formType == "Complex" )
		{
			$("#clientForm .questionGroupName").first().removeClass("hidden");
			var currentGroup = $("#clientForm .questionGroupName").attr("id");
			var aCurrentGRoupNr = currentGroup.split("_");
			var currentGroupNumber = parseInt(aCurrentGRoupNr[1]);
			var numberOfQuestions = parseInt($("#group_"+currentGroupNumber).text());
			var currentQuestion = 1;
		}
	
	}else if(formStatus == "init" && formType == "Simple" ) {
		$('#butSend').removeAttr("disabled");
	}else if(formStatus == "ready") {
		$("#clientForm .form-group").css("display","none");
		$("#questionsFilled").css("display","none");
		$("#butSend").css("display","none");
		$("#butreset").css("display","none");
	}else if(formStatus == "send") {
		$('#butSend').removeAttr("disabled");
	}
	
	var questionsTotal = parseInt($("#questionsTotal").text());
	var questionsFilled = [];
	var questionsWithSubs = [];
	var fieldNumber = 0; //only for showing the next field
	$("#clientForm input").focusin(function() {
		fieldNumber++;
		$("#clientForm").children('.form-group.main').eq(fieldNumber).css("display","block");
		
		});
	
	$("#clientForm input").focusout(function() {
		
			if($(this).val() !="")
			{
				if(questionsFilled.indexOf($(this).attr("id")) < 0)
				{
					questionsFilled.push($(this).attr("id"));
					setFilledNumber("+");
				}
			}else {
				if(questionsFilled.indexOf($(this).attr("id")) > -1)
				{
					removeFilledQquestion($(this).attr("id"));
					setFilledNumber("-");
				}
			}
			
			
		});
	
	$("#clientForm textarea").focusin(function() {
		fieldNumber++;
		$("#clientForm").children('.form-group.main').eq(fieldNumber).css("display","block");
		
		currentQuestion++;
		if(currentQuestion > numberOfQuestions)
		{
			currentGroupNumber++;
			$("#groupNumber_"+currentGroupNumber).removeClass("hidden");
			numberOfQuestions = parseInt($("#group_"+currentGroupNumber).text());
			currentQuestion = 1;
		}
		
		});
	
	
	$("#clientForm textarea").focusout(function() {
		
		if($(this).val() !="")
		{
			if(questionsFilled.indexOf($(this).attr("id")) < 0)
			{
				questionsFilled.push($(this).attr("id"));
				setFilledNumber("+");
			}
		}else {
			if(questionsFilled.indexOf($(this).attr("id")) > -1)
			{
				removeFilledQquestion($(this).attr("id"));
				setFilledNumber("-");
			}
		}
		
		
	});
	
	$("#clientForm select.main").change(function() {
		
		if($(this).val() !="")
		{
			
			if(questionsFilled.indexOf($(this).attr("id")) < 0)
			{
				fieldNumber++;
				questionsFilled.push($(this).attr("id"));
				$("#clientForm").children('.form-group.main').eq(fieldNumber).css("display","block");
				setFilledNumber("+");
				
				$("#clientForm").children('.form-group.sub').css("background-color","initial");
				$("#clientForm").children('.form-group.sub').css("border","initial");
				$("#clientForm").children('.form-group.sub').css("padding-bottom","10px");
				$("#clientForm").children('.form-group.sub').css("font-weight","normal");
				$("#clientForm").children('.form-group.sub').css("color","#73879C");
				
				
				currentQuestion++;
				if(currentQuestion > numberOfQuestions)
				{
					currentGroupNumber++;
					$("#groupNumber_"+currentGroupNumber).removeClass("hidden");
					numberOfQuestions = parseInt($("#group_"+currentGroupNumber).text());
					currentQuestion = 1;
				}
				
				
			}
		}else {
			if(questionsFilled.indexOf($(this).attr("id")) > -1)
			{
				removeFilledQquestion($(this).attr("id"));
				setFilledNumber("-");
			}
		}
		});
	
	$("#clientForm select.main.hassubs").change(function() {
		
		$("#clientForm").children('.form-group.sub').eq(1).css("display","block");
		$("#clientForm").children('.form-group.sub').eq(1).css("background-color","#d4edda");
		$("#clientForm").children('.form-group.sub').eq(1).css("border","1px solid #c3e6cb");
		$("#clientForm").children('.form-group.sub').eq(1).css("padding-bottom","50px");
		$("#clientForm").children('.form-group.sub').eq(1).css("font-weight","bold");
		$("#clientForm").children('.form-group.sub').eq(1).css("color","#155724");
		
		if(questionsWithSubs.indexOf($(this).attr("id")) < 0)
		{
			questionsWithSubs[$(this).attr("id")] = 1;
		}
		
		
		
		});
	
	
	$("#clientForm select.sub").change(function() {
		
		var questionID = $(this).attr("id");
		var splittedID = questionID.split("_");
		var newQuestionID = "question_"+splittedID[1];
		
		var child = parseInt(questionsWithSubs[newQuestionID]);
		
		$("#clientForm").children('.form-group.sub').eq(child).css("display","initial");
		$("#clientForm").children('.form-group.sub').eq(child).css("background-color","initial");
		$("#clientForm").children('.form-group.sub').eq(child).css("border","initial");
		$("#clientForm").children('.form-group.sub').eq(child).css("padding-bottom","10px");
		$("#clientForm").children('.form-group.sub').eq(child).css("font-weight","normal");
		$("#clientForm").children('.form-group.sub').eq(child).css("color","#73879C");
		
		child++;
		$("#clientForm").children('.form-group.sub').eq(child).css("display","block");
		$("#clientForm").children('.form-group.sub').eq(child).css("background-color","#d4edda");
		$("#clientForm").children('.form-group.sub').eq(child).css("border","1px solid #c3e6cb");
		$("#clientForm").children('.form-group.sub').eq(child).css("padding-bottom","50px");
		$("#clientForm").children('.form-group.sub').eq(child).css("font-weight","bold");
		$("#clientForm").children('.form-group.sub').eq(child).css("color","#155724");
		questionsWithSubs[newQuestionID] = child;
		
		
	});
	
	$("#butreset").click(function() {
		$("#clientForm .form-group").css("display","none");
		$("#clientForm .form-group").first().css("display","block");
		$("#clientForm .questionGroupName").addClass("hidden");
		$("#clientForm .questionGroupName").first().removeClass("hidden");
		currentQuestion = 1;
		currentGroupNumber = 1;
		
		questionsFilled = [];
	    questionsWithSubs = [];
		fieldNumber = 0; //only for showing the next field
		$("#filled").text(0);
		$('#butSend').prop("disabled", true);
		$('#filled').css("color","red");
	});
	
	function removeFilledQquestion(field)
	{
		console.log(questionsFilled);
		var index = questionsFilled.indexOf(field);
		if (index > -1) {
			questionsFilled.splice(index, 1);
			}
	}
	
	function setFilledNumber(direction) {
		
		var questionsFiled = parseInt($("#filled").text());
		if(direction == "+")
		{
			questionsFiled++;
			if(questionsTotal == questionsFiled)
			{
				$('#butSend').removeAttr("disabled");
				$('#filled').css("color","#155724");
			}
			
		}else if(direction == "-") {
			questionsFiled--;
			if(questionsTotal > questionsFiled)
			{
				$('#butSend').prop("disabled", true);
				$('#filled').css("color","red");
			}
		}
		
		$("#filled").text(questionsFiled);
	}
	
});
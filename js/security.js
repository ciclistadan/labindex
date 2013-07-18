
//onload function
$(function setEvents(){


//verify credentials using login.php
$("#login_btn").click(function login(){
	$('#login_btn').toggleClass('hidden');

	var user = $('#user').val();
	var pass = $('#pass').val();
	var dataString = "user="+user+"&pass="+pass;

	//perform a POST request for validation
		$.ajax({
			type: "POST",
			url: "utility/login.php",
  			data: dataString,
			dataType: "json",
   			success: function(json){

				if(json.status==1){
					$('.user_panel').html('');
					refreshUserPanel();
					toggleVerified();
					}
				else if(json.status==9){
					$('.user_panel').html('');
					refreshUserPanel();
					toggleVerified();
					}
				else{
					$('#msg').html(json.msg);
					$('#login_btn').toggleClass('hidden');
					}
				}
  			});
	});

//assign event hadler to logout button
$("#logout_btn").click(function(){
	//perform a POST request for validation
		$.ajax({
			type: "POST",
			url: "utility/logout.php",
   			success: function(){
				$('#logout').submit();
				}
  			});
	});


function refreshUserPanel(){
	$.ajax({
			type: "POST",
			url: "utility/user_panel.php",
			dataType: "html",
   			success: function(data){
				$('.user_panel').html(data);
				setEvents();
				}
  			});
	};

function toggleVerified(){
	$('body').toggleClass('verified');

	};
});
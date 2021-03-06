<?php
require_once ("fbCredentials.php"); //Facebook AppID, AppSecret
ob_start();
session_start();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>PicBook</title>
		<!-- CSS files -->
		<link href="css/styles.css" rel="stylesheet">
		<link rel="stylesheet" href="css/foundation.css" />     
		<link rel="stylesheet" href="css/foundation-icons.css" />
		<!-- Load External js Lib-->
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js"></script>
		<script src="js/modernizr.js"></script>
	</head>
	<body id="bodyBg">
		
		
		
		<div id="fb-root"></div>
		<script>
	window.fbAsyncInit = function() {
		// init the FB JS SDK
		FB.init({
		appId      : '<?php echo $AppId; ?>',                        // App ID from the app dashboard
		status     : true,                                 // Check Facebook Login status
		xfbml      : true                                  // Look for social plugins on the page
	});
	$('#supersized-loader').show();
	$("#fblogin").hide();
	// Additional initialization code such as adding Event Listeners goes here
	FB.getLoginStatus(function(response) {
		if (response.status === 'connected') {
			// the user is logged in and has authenticated your
			// app, and response.authResponse supplies
			// the user's ID, a valid access token, a signed
			// request, and the time the access token
			// and signed request each expire
	
			var uid = response.authResponse.userID;
			var accessToken = response.authResponse.accessToken;
			//Get User Name
			FB.api('/me?fields=name', function(respo) {
				$("#UserName").html(respo.name);
				$("#title").html(respo.name + "'s Albums");
				$("#fblogin").hide();
				$("#fbicon").hide();
				$('#ProfilePic').attr('src', 'http://graph.facebook.com/' + respo.id + '/picture?width=500&height=500');
				//Get All ablums of user
				FB.api('/me/albums', showAlbums);
			});
		} else if (response.status === 'not_authorized') {
			// the user is logged in to Facebook,
			// but has not authenticated your app
			$("#fblogin").show();
			$('#supersized-loader').hide();
		} else {
			// the user isn't logged in to Facebook.
			$("#fblogin").show();
			$('#supersized-loader').hide();
		}
	});
};

	// Load the SDK asynchronously
	(function(){
		// If we've already installed the SDK, we're done
		if (document.getElementById('facebook-jssdk')) {return;}
	
		// Get the first script element, which we'll use to find the parent node
		var firstScriptElement = document.getElementsByTagName('script')[0];
	
		// Create a new script element and set its id
		var facebookJS = document.createElement('script');
		facebookJS.id = 'facebook-jssdk';
	
		// Set the new script's source to the source of the Facebook JS SDK
		facebookJS.src = '//connect.facebook.net/en_US/all.js';
	
		// Insert the Facebook JS SDK into the DOM
		firstScriptElement.parentNode.insertBefore(facebookJS, firstScriptElement);
	}());
		</script>
		
		<div class="row">
			<div class="large-12 columns">

				<div class="row" style="margin-top: 50px;">

					<!--SuprizedMe full screen slider -->
					<div id="slider" style="display:none">
						<div id="backalbum">
							<button id="backtoalbum" style="margin-top:-50px" class="button small">
								Back to Albums
							</button>
					
						</div>
						<!--Thumbnail Navigation-->
						<div id="prevthumb"></div>
						<div id="nextthumb"></div>

						<!--Arrow Navigation-->
						<a style="margin-top: 50%;margin-left: -5%;" id="prevslide" class="load-item"></a>
						<a style="margin-top: 50%;margin-right: -5%;" id="nextslide" class="load-item"></a>

						<div id="thumb-tray" class="load-item">
							<div id="thumb-back"></div>
							<div id="thumb-forward"></div>
						</div>

						<!--Time Bar-->
						<div id="progress-back" class="load-item">
							<div id="progress-bar"></div>
						</div>

						<!--Control Bar-->
						<div id="controls-wrapper" class="load-item">
							<div id="controls">

								<a id="play-button"><img id="pauseplay" src="img/pause.png"/></a>

								<!--Slide counter-->
								<div id="slidecounter">
									<span class="slidenumber"></span> / <span class="totalslides"></span>
								</div>

								<!--Slide captions displayed here-->
								<div id="slidecaption"></div>

								<!--Thumb Tray button-->
								<a id="tray-button"><img id="tray-arrow" src="img/button-tray-up.png"/></a>

								<!--Navigation-->
								<ul id="slide-list"></ul>

							</div>
						</div>
					</div>

					<!-- Side Bar -->

					<div class="large-4 small-12 columns connect container" style="display:none">

						<img id="ProfilePic" src="http://placehold.it/250x250&text=Logo">

						<div class="hide-for-small panel">
							<h3 id="UserName">Header</h3>

							<button id="download_album_all" class="button small" style="width:100%">
								Download all
							</button>
							<br>
							<button id="download_album_select" class="button small" style="width:100%">
								Download selected
							</button>
							<br>
							<button id="move_album_all" class="button small success" style="width:100%">
								Move all
							</button>
							<br>
							<button id="move_album_select" class="button small success" style="width:100%">
								Move selected
							</button>
							<br>
							<button class="button alert tiny" id="logout" style="width:100%">
								Logout
							</button>
						</div>
					</div>

					<!-- End Side Bar -->

					<!-- Thumbnails -->

					<div class="large-8 columns connect container" style="display: none;">
						<div id="albums" class="row">

						</div>

						<!-- End Thumbnails -->

					</div>
					<center>
                                            <h1 id="fbicon">Login with Facebook account</h1>
						<br>
						<input type="button" style="background: url('img/fb-login.png') no-repeat;width:134px;height:54px;" id="fblogin" />

					</center>
				</div>
			</div>

		</div>

		<!--Model window for Download -->
		<a href="#" data-reveal-id="myModal" id="openmodal" style"display:none;"></a>
		<div id="myModal" class="reveal-modal" data-reveal>
			<div class="modal-header">
				<h4 id="myModalLabel">Please wait while we are preparing your files</h4>
			</div>
			<div class="modal-body">
				<!-- Progress    -->
				<div id="downloadprogress">
					<img src="img/progress-modal.gif"/>
				</div>
			</div>
			<div id="downloadlink" style="display:none">
				<!--Download Button -->
				<a href="" id="hrefDownload" class="button">Click Here to Download</a>
			</div>
			<a class="close-reveal-modal" id="closemodal">&#215;</a>
		</div>

	</body>
	<!-- JS files   -->
	<script>var appId =  '<?php echo $AppId; ?>';</script>
	<script src="js/foundation.min.js"></script>
	<script>
		$(document).foundation();
	</script>
	<link rel="stylesheet" href="css/supersized.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="css/supersized.shutter.css" type="text/css" media="screen" />
	<script type="text/javascript" src="js/jquery.easing.min.js"></script>
	<script type="text/javascript" src="js/supersized.3.2.7.min.js"></script>
	<script type="text/javascript" src="js/supersized.shutter.min.js"></script>  
	<script src="js/scripts.js"></script>
</html>

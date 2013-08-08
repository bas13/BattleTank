
<!DOCTYPE html>

<html>
<head>
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script src="<?= base_url() ?>/js/jquery.timers.js"></script>
<link rel="stylesheet" type="text/css"
	href="<?= base_url() ?>/css/battle.css" />
<script>

		var otherUser = "<?= $otherUser->login ?>";
		var user = "<?= $user->login ?>";
		var status = "<?= $status ?>";
		
		$(function(){
			$('body').everyTime(2000,function(){
					if (status == 'waiting') {
						$.getJSON('<?= base_url() ?>arcade/checkInvitation',function(data, text, jqZHR){
								if (data && data.status=='rejected') {
									alert("Sorry, your invitation to battle was declined!");
									window.location.href = '<?= base_url() ?>arcade/index';
								}
								if (data && data.status=='accepted') {
									status = 'battling';
									$('#status').html('Battling ' + otherUser);
								}
								
						});
					}
					var url = "<?= base_url() ?>combat/getMsg";
					$.getJSON(url, function (data,text,jqXHR){
						if (data && data.status=='success') {
							var conversation = $('[name=conversation]').val();
							var msg = data.message;
							if (msg.length > 0)
								$('[name=conversation]').val(conversation + "\n" + otherUser + ": " + msg);
						}
					});
			});

			$('form').submit(function(){
				var arguments = $(this).serialize();
				var url = "<?= base_url() ?>combat/postMsg";
				$.post(url,arguments, function (data,textStatus,jqXHR){
						var conversation = $('[name=conversation]').val();
						var msg = $('[name=msg]').val();
						$('[name=conversation]').val(conversation + "\n" + user + ": " + msg);
						});
				return false;
				});	
		});
	
	</script>


</head>
<body>
	<!-- <h1>Battle Field</h1> -->

	<div id="container">
		<!--<div id="info">
	<div>
	Hello <?= $user->fullName() ?>  <?= anchor('account/logout','(Logout)') ?>  <?= anchor('account/updatePasswordForm','(Change Password)') ?>
	</div>
	
	<div id='status'> 
	<?php 
		if ($status == "battling")
			echo "Battling " . $otherUser->login;
		else
			echo "Wating on " . $otherUser->login;
	?>
	</div> -->

		<?php 

		/*echo form_textarea('conversation');

		echo form_open();
		echo form_input('msg');
		echo form_submit('Send','Send');
		echo form_close();*/

		?>



		<canvas id="battleCanvas" width="700px" height="400px"></canvas>

</body>

<script>
	var tank1 = new Image();
	var tank2 = new Image();

	tank1.src = "<?= base_url() ?>images/tank1.jpg";
	tank2.src = "<?= base_url() ?>images/tank2.jpg";

	var canvas = document.getElementById("battleCanvas");
	var context = canvas.getContext("2d");

	var mouseX = 0;
	var mouseY = 0;

	// Tank 1 info
	var currentX = 15;
	var currentY = 15;

	var currentAngle = 0;
	
	var gunAngle = 0;

	// Tank 2 info
	var currentX2 = canvas.width - 15; 
	var currentY2 = canvas.height - 15;

	var currentAngle2 = 180;
	
	var gunAngle2 = 180;

	// Tank 1
	context.drawImage(tank1, currentX - 15, currentY - 15, 30, 30);
	context.drawImage(tank1, currentX, currentY, 30, 5);

	// Tank 2
	context.drawImage(tank2, currentX2 - 15, currentY2 - 15, 30, 30);
	context.drawImage(tank2, currentX2, currentY2, 30, 5);

	$(document).keydown(function(e) {
		if (e.keyCode == 87) {
			move('forward');
		} else if (e.keyCode == 65) {
			turn('left');
		} else if (e.keyCode == 68) {
			turn('right');
		} else if (e.keyCode == 83) {
			move('backward');
		}
	});

	
	$(document).mousemove(function(e) {
		mouseX = e.pageX - canvas.offsetLeft;
		mouseY = e.pageY - canvas.offsetTop;
		var ratio = (mouseY - currentY) / (mouseX - currentX);
		if ((mouseX - currentX) < 0) {
			gunAngle = (Math.PI + Math.atan(ratio)) * (180 / Math.PI);
		} else {
			gunAngle =  Math.atan(ratio) * (180 / Math.PI);
		}
		
		context.clearRect(0, 0, canvas.width, canvas.height);
		context.save();
		context.translate(currentX, currentY);
		context.rotate(currentAngle * Math.PI / 180);
		context.drawImage(tank1, -15, -15, 30, 30);
		context.restore();
		
		context.save();
		context.translate(currentX, currentY);
		context.rotate(gunAngle * Math.PI / 180);
		context.drawImage(tank1, 0, 0, 30, 5);
		context.restore();


		// Tank 2
		context.drawImage(tank2, currentX2 - 15, currentY2 - 15, 30, 30);
		context.drawImage(tank2, currentX2, currentY2, 30, 5);

	});

	function move(direction) {
		if (direction === 'forward') {
			currentX += Math.cos(currentAngle * Math.PI / 180);
			currentY += Math.sin(currentAngle * Math.PI / 180);

			context.clearRect(0, 0, canvas.width, canvas.height);
			context.save();
			context.translate(currentX, currentY);
			context.rotate(currentAngle * Math.PI / 180);
			context.drawImage(tank1, -15, -15, 30, 30);
			context.restore();
			
			context.save();
			context.translate(currentX, currentY);
			context.rotate(gunAngle * Math.PI / 180);
			context.drawImage(tank1, 0, 0, 30, 5);
			context.restore();

			// Tank 2
			context.drawImage(tank2, currentX2 - 15, currentY2 - 15, 30, 30);
			context.drawImage(tank2, currentX2, currentY2, 30, 5);
			
			
		} else if (direction === 'backward') {
			currentX -= Math.cos(currentAngle * Math.PI / 180);
			currentY -= Math.sin(currentAngle * Math.PI / 180);

			context.clearRect(0, 0, canvas.width, canvas.height);
			context.save();
			context.translate(currentX, currentY);
			context.rotate(currentAngle * Math.PI / 180);
			context.drawImage(tank1, -15, -15, 30, 30);
			context.restore();
			
			context.save();
			context.translate(currentX, currentY);
			context.rotate(gunAngle * Math.PI / 180);
			context.drawImage(tank1, 0, 0, 30, 5);
			context.restore();

			// Tank 2
			context.drawImage(tank2, currentX2 - 15, currentY2 - 15, 30, 30);
			context.drawImage(tank2, currentX2, currentY2, 30, 5);
		}
	}

	function turn(direction) {
		if (direction === 'left') {
			currentAngle -= 1;
			context.clearRect(0, 0, canvas.width, canvas.height);
			context.save();
			context.translate(currentX, currentY);
			context.rotate(currentAngle * Math.PI / 180);
			context.drawImage(tank1, -15, -15, 30, 30);
			context.restore();
			
			context.save();
			context.translate(currentX, currentY);
			context.rotate(gunAngle * Math.PI / 180);
			context.drawImage(tank1, 0, 0, 30, 5);
			context.restore();

			// Tank 2
			context.drawImage(tank2, currentX2 - 15, currentY2 - 15, 30, 30);
			context.drawImage(tank2, currentX2, currentY2, 30, 5);
			
		} else if (direction === 'right') {
			currentAngle += 1;
			context.clearRect(0, 0, canvas.width, canvas.height);
			context.save();
			context.translate(currentX, currentY);
			context.rotate(currentAngle * Math.PI / 180);
			context.drawImage(tank1, -15, -15, 30, 30);
			context.restore();
			
			context.save();
			context.translate(currentX, currentY);
			context.rotate(gunAngle * Math.PI / 180);
			context.drawImage(tank1, 0, 0, 30, 5);
			context.restore();

			// Tank 2
			context.drawImage(tank2, currentX2 - 15, currentY2 - 15, 30, 30);
			context.drawImage(tank2, currentX2, currentY2, 30, 5);
		}
	}

	$(function(){
		$('body').everyTime(2000,function(){
					$.getJSON('<?= base_url() ?>combat/getBattleState',function(data, text, jqZHR){

							
					});
		});
	});
	
</script>

</html>


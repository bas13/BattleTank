
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
		var battleid = "<?= $battleid ?>";
		
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
	<h1>Battle Field</h1>

	<div id="container">
		<div id="info">
			<div>
				Hello
				<?= $user->fullName() ?>
				<?= anchor('account/logout','(Logout)') ?>
				<?= anchor('account/updatePasswordForm','(Change Password)') ?>
			</div>

			<div id='status'>
				<?php 
				if ($status == "battling")
					echo "Battling " . $otherUser->login;
				else
					echo "Wating on " . $otherUser->login;
				?>
			</div>

			<?php 

			echo form_textarea('conversation');

			echo form_open();
			echo form_input('msg');
			echo form_submit('Send','Send');
			echo form_close();

			?>
		</div>
	</div>



	<canvas id="battleCanvas" width="700px" height="400px"></canvas>

	<div style="border: solid 3 px;">
		<p id='test'></p>
	</div>


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

	$(function(){
		
		$(document).everyTime(100,function(){
			$.getJSON('<?= base_url() ?>combat/getBattleState',function(data, text, jqZHR){
				if (data && data.status=='success') {
					$('#test').html(data.hit);
					if (battleid == 1) {	
						if (data.x1 != null) {
							currentX2 = data.x1;
						}

						if (data.y1 != null) {
							currentY2 = data.y1;
						}

					    if (data.x2 != null) {
						    currentAngle2 = data.x2;
					    }

					    if (data.angle != null) {
						    gunAngle2 = data.angle;
					    }
					}
					else {
						if (data.x1 != null) {
							currentX = data.x1;
						}

						if (data.y1 != null) {
							currentY = data.y1;
						}

						if (data.x2 != null) {
							currentAngle = data.x2;
						}

						if (data.angle != null) {
							gunAngle = data.angle;
						}
					}
				}	
				});
			});

		$(document).everyTime(1000,function(){
			sendTankState();
			animateTanks();
		});
		
		initializeTanks();

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

			if (battleid == 1) {
				var ratio = (mouseY - currentY) / (mouseX - currentX);
				if ((mouseX - currentX) < 0) {
					gunAngle = (Math.PI + Math.atan(ratio)) * (180 / Math.PI);
				} else {
					gunAngle =  Math.atan(ratio) * (180 / Math.PI);
				}
			}
			else {
				var ratio = (mouseY - currentY2) / (mouseX - currentX2);
				if ((mouseX - currentX2) < 0) {
					gunAngle2 = (Math.PI + Math.atan(ratio)) * (180 / Math.PI);
				} else {
					gunAngle2 =  Math.atan(ratio) * (180 / Math.PI);
				}
				
			}
			//sendTankState();
			animateTanks();
		});
	});
	
	function initializeTanks() {
		context.clearRect(0, 0, canvas.width, canvas.height);
		
		// Tank 1
		context.drawImage(tank1, currentX - 15, currentY - 15, 30, 30);
		context.drawImage(tank1, currentX, currentY, 30, 5);

		// Tank 2
		context.save();
		context.translate(currentX2, currentY2);
		context.rotate(currentAngle2 * Math.PI / 180);
		context.drawImage(tank2, -15, -15, 30, 30);
		context.restore();

		context.save();
		context.translate(currentX2, currentY2);
		context.rotate(gunAngle2 * Math.PI / 180);
		context.drawImage(tank2, 0, 0, 30, 5);
		context.restore();
		//sendTankState();
	}

	function move(direction) {
		if (direction === 'forward') {
			if (battleid == 1) {
				currentX += Math.cos(currentAngle * Math.PI / 180);
				currentY += Math.sin(currentAngle * Math.PI / 180);
			}
			else {
				currentX2 += Math.cos(currentAngle2 * Math.PI / 180);
				currentY2 += Math.sin(currentAngle2 * Math.PI / 180);
			}
		} 
		else if (direction === 'backward') {
			if (battleid == 1) {
				currentX -= Math.cos(currentAngle * Math.PI / 180);
				currentY -= Math.sin(currentAngle * Math.PI / 180);
			}
			else {
				currentX2 -= Math.cos(currentAngle2 * Math.PI / 180);
				currentY2 -= Math.sin(currentAngle2 * Math.PI / 180);
			}
		}
		//sendTankState();
		animateTanks();
	}

	function turn(direction) {
		if (direction === 'left') {
			if (battleid == 1) {
				currentAngle -= 1;
			}
			else {
				currentAngle2 -= 1;
			}
			
		}
		else if (direction === 'right') {
			if (battleid == 1) {
				currentAngle += 1;
			}
			else {
				currentAngle2 += 1;
			}
		}
		//sendTankState();
		animateTanks();
	}


	function animateTanks() {
		context.clearRect(0, 0, canvas.width, canvas.height);

		// Tank 1
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
		context.save();
		context.translate(currentX2, currentY2);
		context.rotate(currentAngle2 * Math.PI / 180);
		context.drawImage(tank2, -15, -15, 30, 30);
		context.restore();
		
		context.save();
		context.translate(currentX2, currentY2);
		context.rotate(gunAngle2 * Math.PI / 180);
		context.drawImage(tank2, 0, 0, 30, 5);
		context.restore();
	}

	function sendTankState() {
		var url = '<?= base_url() ?>combat/updateBattleState';
		var jobj = {};
		if (battleid == 1) {
		    jobj['x1'] = currentX;
		    jobj['y1'] = currentY;
		    jobj['x2'] = currentAngle;
		    jobj['y2'] = 0;
		    jobj['angle'] = gunAngle;
		    jobj['shot'] = 0;
		    jobj['hit'] = 0;
		}
		else {
		    jobj['x1'] = currentX2;
		    jobj['y1'] = currentY2;
		    jobj['x2'] = currentAngle2;
		    jobj['y2'] = 0;
		    jobj['angle'] = gunAngle2;
		    jobj['shot'] = 0;
		    jobj['hit'] = 0;
		}

		var jtext = "json=" + JSON.stringify(jobj);

		$.ajax({
			url : url,
			data:  jtext,
			type: 'POST'
		});	
	}	
	
</script>

</html>


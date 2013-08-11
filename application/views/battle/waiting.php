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
	$('body').everyTime(200,function(){
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
			else {
				window.location.href = '<?= base_url() ?>combat/index';
			}
	});
});
</script>
</head>
<body>
<h3>Waiting for opponent!</h3>
</body>
</html>
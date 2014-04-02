<?php
session_start();
$sid = session_id();
$timeoutSeconds = 30;
?>
<!DOCTYPE html>
<html>
        <head>
                <meta charset="utf-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <title></title>
                <meta name="description" content="">
                <meta name="viewport" content="width=device-width, initial-scale=1">
        </head>
        <body>
                <div>Count:<div id="count"></div></div>
                <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
                <script>
                        var timeoutSeconds = <?php echo $timeoutSeconds; ?>;
                        function startPoll() {
                                $.ajax({
                                        url: "rocketUpdate.php",
                                        data: "sid=<?php echo $sid; ?>",
                                        dataType: 'json',
                                        crossDomain: true,
                                        success: function(data) {
                                                $('#count').html(data.count);
                                        }
                                });
                                setTimeout(startPoll, timeoutSeconds * 1000 * 0.5);
                        }
                        $().ready(function () {
                                startPoll();
                        });
                </script>
        </body>
</html>
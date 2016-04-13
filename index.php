<?php
    require_once "recaptchalib.php";
    require_once "constants.php";

    $response = null;
    $reCaptcha = new ReCaptcha($secret);

    if ($_POST["g-recaptcha-response"]) {
        $response = $reCaptcha->verifyResponse(
            $_SERVER["REMOTE_ADDR"],
            $_POST["g-recaptcha-response"]
        );
    }

    if ($response != null && $response->success) {
            function died($error) {
                echo "I'm sorry, but there were error(s) found with the form you submitted. ";
                echo "These errors appear below.<br /><br />";
                echo $error."<br />";
                echo "Please go back and fix these errors.";
                die();
            }

            if(!isset($_POST['name']) ||
                !isset($_POST['email']) ||
                !isset($_POST['message'])) {
                    died('We are sorry, but there appears to be a problem with the form you submitted.');
            }

            $name = $_POST['name'];
            $email_from = $_POST['email'];
            $message = $_POST['message'];
            $email_subject = "New message from $name";

            $error_message = "";
            $email_exp = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';
            if(!preg_match($email_exp,$email_from)) {
                $error_message .= 'The Email Address you entered does not appear to be valid.<br />';
            }
            $string_exp = "/^[A-Za-z .'-]+$/";
            if(!preg_match($string_exp,$name)) {
                $error_message .= 'The Name you entered does not appear to be valid.<br />';
            }
            if(strlen($error_message) > 0) {
                died($error_message);
            }
            $email_message = "Form details below.\n\n";

            function clean_string($string) {
                $bad = array("content-type","bcc:","to:","cc:","href");
                return str_replace($bad,"",$string);
            }

            $email_message .= "Name: ".clean_string($name)."\n";
            $email_message .= "Email: ".clean_string($email_from)."\n";   
            $email_message .= "Message: ".clean_string($message)."\n";

            // create email headers
            $headers = 'From: '.$email_from."\r\n".
            'Reply-To: '.$email_from."\r\n" .
            'X-Mailer: PHP/' . phpversion();
            @mail($email_to, $email_subject, $email_message, $headers);
            header('Location:thanks.html');
    }
?>

<html>
    <head>
        <title>Chris Conley</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link href="css/styles.css" rel="stylesheet">
        <link href="favicon.ico" rel="icon">

        <style>
            .popover {
                width: 101px;
            }

            .modal-footer
            {
                    padding-bottom: 0px;
            }
        </style>
        <script src='https://www.google.com/recaptcha/api.js'></script>
    </head>
    <body>

        <!-- This is the code for the nav bar -->
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <script src="js/bootstrap.js"></script>

        <div class="navbar navbar-inverse navbar-static-top">
            <div class="container">

                <div class="navbar-header">
                    <a href="http://csconley.com" class="navbar-brand">Chris Conley</a>

                    <button class="navbar-toggle" data-toggle="collapse" data-target=".navHeaderCollapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>

                <div class="collapse navbar-collapse navHeaderCollapse">
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="http://blog.csconley.com">Blog</a><li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Projects/Profiles <b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li><a href="https://github.com/chrisc93" target="_blank">Github</a></li>
                                    <li><a href="https://www.linkedin.com/pub/chris-conley/90/922/3b7" target="_blank">LinkedIn</a></li>
                                    <li><a href="https://twitter.com/ChrisConley804" target="_blank">Twitter</a></li>
                                    <li><a href="http://chat.csconley.com" target="_blank">Chat beta</a></li>
                                    <li><a href="http://forum.xda-developers.com/member.php?u=5276780" target="_blank">XDA Developers</a></li>
                                </ul>
                        <li><a href="http://csconley.com/about.php">About</a><li>
                        <li><a href="#contact" data-toggle="modal">Contact</a><li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="contact" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form class="form-horizontal" name="commentform" method="post" action="">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Contact Me</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="col-lg-2 control-label" for="name">Name:</label>
                                <div class="col-lg-10">
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Full Name"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-2 control-label" for="email">Email:</label>
                                <div class="col-lg-10">
                                    <input type="email" class="form-control" id="email" name="email" placeholder="you@example.com"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-2 control-label" for="message">Message:</label>
                                <div class="col-lg-10">
                                    <textarea rows="8" class="form-control" id="message" name="message" placeholder="Your message"></textarea>
                                </div>
                            </div>
			    <div class="form-group">
			    	<div class="col-lg-12">
                            	    <div class="pull-right g-recaptcha" data-sitekey="6LcZwvYSAAAAAKWKfCnlkt1HWr5M0qw4es-favdy"></div>
			    	</div>
			    </div>
                        </div>
                        <div class="modal-footer">
                            <a class="btn btn-default" data-dismiss="modal">Cancel</a>
                            <button class="btn btn-primary" type="submit" name="send_btn" id="send_btn" data-placement="top">Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="col-md-9">
                <div class="jumbotron">
                    <h1 align="center" style="color: #777;">Welcome</h1>
                    <p>
                        You have landed on my personal website! This is just somewhere for me to keep
                        track of important events that occur throughout my life and also to attempt some web development.
                        There are lots of links in the navigation bar so please feel free to check them out. And don't hesitate to contact me if you have any questions.
                    </p>
                    <br />
                    <p>- Chris</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="list-group">
                    <div class="panel panel-default panel-body">
                        <h3 align="center">Byte-sized Bio</h3>
                        <p>I am a recent graduate of Virginia Tech with a bachelor of science degree in computer science.</p>
                        <p>Currently I am working as a consultant for <a href="https://www.captechconsulting.com" target="_blank">CapTech</a> in Richmond, VA.</p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>:: {{ env('APP_NAME') }} || @yield('title') ::</title>
    <meta name="viewport" content="width=device-width" />

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body bgcolor="#FFFFFF" style="font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif; margin:0; padding:0;">

<table style="width: 100%;" bgcolor="#009CB3">
    <tr>
        <td></td>
        <td class="header container" style="display:block!important; max-width:600px!important; margin:0 auto!important; clear:both!important;">

            <div class="content" style="padding:15px; max-width:600px; margin:0 auto; display:block;">
                <table bgcolor="#009CB3">
                    <tr>
                        <td style="color: white; font-size: xx-large">
                            <a style="text-decoration: none; color: white" href="{{ env('DOMAIN_URL') }}"> {{ env('APP_NAME') }} Mailing Service</a>
                        </td>
                    </tr>
                </table>
            </div>

        </td>
        <td></td>
    </tr>
</table><!-- /HEADER -->
<table class="body-wrap" style="width: 100%;">
    <tr>
        <td></td>
        <td class="container" style="display:block!important; max-width:600px!important; margin:0 auto!important; clear:both!important;" bgcolor="#FFFFFF">

            <div class="content" style="padding:15px; max-width:600px; margin:0 auto; display:block;">
                <table>
                    <tr>
                        <td>
                            <div class="row">
                                @yield('content')
                            </div>
                            <br><br>
                            <!-- Callout Panel -->
                            <a style="text-decoration: none;" href="{{ env('DOMAIN_URL') }}">
                                <p class="callout" style="margin-bottom: 16px; font-weight: normal; font-size:14px; line-height:1.6; padding:15px; background-color:#ECF8FF;">
                                    Click Here To Access The Application
                                </p>
                            </a>
                            <p style="margin-bottom: 15px; font-weight: normal; font-size:14px; line-height:1.6; padding:15px; background-color:#ECF8FF;">
                                <br>Date and Time: {{ date('g:ia \o\n l jS F Y') }}
                                <br>Note: This is an auto generated email. if you did not initiate this action kindly ignore this mail.
                                <br>We recommend that you contact us at
                                <a href="{{ env('DEVELOPER_SITE_ADDRESS') }}">{{ env('DEVELOPER_SITE_NAME') }}</a> for further assistance.
                            </p>
                            <!-- /Callout Panel -->

                            <!-- social & contact -->
                            <table class="social" width="100%">
                                <tr>
                                    <td>

                                        <!-- column 2 -->
                                        <table align="left" class="column">
                                            <tr>
                                                <td>

                                                    <h5 class="">Contact Info:</h5>
                                                    <p style="margin-bottom: 10px; font-weight: normal; font-size:14px; line-height:1.6;">Phone: <strong>{{ env('DEVELOPER_SITE_NUMBER') }}</strong><br/>
                                                        Email: <strong><a href="emailto:{{ env('DEVELOPER_SITE_EMAIL') }}">{{ env('DEVELOPER_SITE_EMAIL') }}</a></strong></p>

                                                </td>
                                            </tr>
                                        </table><!-- /column 2 -->

                                        <span class="clear" style="display: block; clear: both;"></span>

                                    </td>
                                </tr>
                            </table><!-- /social & contact -->

                        </td>
                    </tr>
                </table>
            </div><!-- /content -->

        </td>
        <td></td>
    </tr>
</table><!-- /BODY -->

<!-- FOOTER -->
<table class="footer-wrap" style="width: 100%;	clear:both!important;">
    <tr>
        <td></td>
        <td class="container" style="display:block!important; max-width:600px!important; margin:0 auto!important; clear:both!important;">

            <!-- content -->
            <div class="content" style="padding:15px; max-width:600px; margin:0 auto; display:block;">
                <table>
                    <tr>
                        <td align="center">
                            <p style="border-top: 1px solid rgb(215,215,215); padding-top:15px; font-size:14px; font-weight: bold;">
                                <a style="color: #009CB3;" href="#">Terms</a> |
                                <a style="color: #009CB3;" href="#">Privacy</a> |
                                <a style="color: #009CB3;" href="#"><unsubscribe>Unsubscribe</unsubscribe></a>
                                &copy; {{ date("Y") }} <a href="{{ env('DEVELOPER_SITE_ADDRESS') }}">{{ env('DEVELOPER_SITE_NAME') }}</a> All rights reserved
                            </p>
                        </td>
                    </tr>
                </table>
            </div><!-- /content -->

        </td>
        <td></td>
    </tr>
</table><!-- /FOOTER -->

</body>
</html>
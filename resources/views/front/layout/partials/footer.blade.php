<!-- BEGIN PRE-FOOTER -->
<div class="page-prefooter">
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12 footer-block">
                <h2>About</h2>

                <p> A School Portal for the Smart School Management System..</p>
            </div>
            {{--<div class="col-md-3 col-sm-6 col-xs-12 footer-block">--}}
                {{--<h2>Follow Us On</h2>--}}
                {{--<ul class="social-icons">--}}
                    {{--<li>--}}
                        {{--<a href="javascript:;" data-original-title="facebook" class="facebook"></a>--}}
                    {{--</li>--}}
                    {{--<li>--}}
                        {{--<a href="javascript:;" data-original-title="twitter" class="twitter"></a>--}}
                    {{--</li>--}}
                    {{--<li>--}}
                        {{--<a href="javascript:;" data-original-title="instagram" class="instagram"></a>--}}
                    {{--</li>--}}
                    {{--<li>--}}
                        {{--<a href="javascript:;" data-original-title="linkedin" class="linkedin"></a>--}}
                    {{--</li>--}}
                {{--</ul>--}}
            {{--</div>--}}
            <div class="col-md-3 col-sm-6 col-xs-12 footer-block">
                <h2>Contacts</h2>
                <address class="margin-bottom-40"> Phone: {{ env('DEVELOPER_SITE_NUMBER') }}
                    <br> Email:
                    <a href="mailto:{{ env('DEVELOPER_SITE_EMAIL') }}">{{ env('DEVELOPER_SITE_EMAIL') }}</a>
                </address>
            </div>
        </div>
    </div>
</div>
<!-- END PRE-FOOTER -->
<!-- BEGIN INNER FOOTER -->
<div class="page-footer">
    <div class="container"> {{ date('Y') }} &copy; {{ env('APP_NAME') . ' by ' }}
        <a href="{{ env('DEVELOPER_SITE_ADDRESS') }}" title="{{ env('DEVELOPER_SITE_NAME') }}" target="_blank">
            {{ env('DEVELOPER_SITE_NAME') }}
        </a>
    </div>
    <div class="scroll-to-top">
        <i class="icon-arrow-up"></i>
    </div>
</div>
<!-- END INNER FOOTER -->
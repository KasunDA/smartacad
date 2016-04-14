<a target='_blank' href="{{ env('DEVELOPER_SITE_ADDRESS') }}"><img
            src="{{ $message->embed(asset('assets/custom/img/admin/logo2.png')) }}">
</a>
<h3>Hi, <strong><i>{{ $user->fullNames() }}</i></strong></h3>
<div class="lead" style="margin-bottom: 10px; font-weight: normal; font-size:17px; line-height:1.6;">
    <?php $content = explode("\n", $content); ?>
    @foreach($content as $line)
        <p>{!! $line !!}</p>
    @endforeach
    <a href="http://{{ env('DOMAIN_URL') }}/auth/verify/{{ $hashIds->encode($user->user_id) }}/{{ $user->verification_code }}">Verify</a>
</div>
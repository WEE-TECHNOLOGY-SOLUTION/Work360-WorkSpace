@if (!empty($settings['google_sign_in_image']))
    <a href="{{ route('login.google') }}" class="btn btn-primary btn-block mt-2 login_button"  style="width: min(100%, 410px); height: clamp(41px, 5vw, 41px); object-fit: cover">
        <img src="{{ get_file($settings['google_sign_in_image']) }}" alt="google loging icon"><snap style="color: white;">Continue with Google </snap>
    </a>
@else
    <a class="btn btn-primary btn-block mt-2 login_button" href="{{ route('login.google') }}">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="18" height="18" style="margin-right: 24px;">
            <path fill="#ffffff" d="M44.5 20H24v8.5h11.7c-1.3 3.7-4.5 6.5-8.7 6.5-5 0-9-4-9-9s4-9 9-9c2.4 0 4.6 1 6.3 2.6l6.3-6.3C36.3 9.7 30.5 7 24 7c-9.4 0-17 7.6-17 17s7.6 17 17 17c8.6 0 16-6.3 17-15v-6z"/>
        </svg>
    </a>
@endif
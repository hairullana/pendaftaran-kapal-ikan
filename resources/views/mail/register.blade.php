<p>Silahkan konfirmasi email</p>

<p>Cara 1: klik link konfirmasi berikut <a href="{{ $data['link'] }}">konfirmasi email</a></p>
<p>Cara 2: request (POST) ke {{ route('confirmation_api') }} dengan mengirim data <b>email</b> dan <b>otp</b></p>

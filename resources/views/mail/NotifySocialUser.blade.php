@component('mail::message')

<h2>Prezado/a {{ $user->name }}</h2>

Suas permissões foram definidas para o papel de <strong> {{ implode( ', ', $user->getRoleNames()->toArray() ) }} </strong>.

@component('mail::button', ['url' => "http://localhost"])
Ir para o Acervo App
@endcomponent

Atenciosamente,
{{ config('app.name') }}

@endcomponent
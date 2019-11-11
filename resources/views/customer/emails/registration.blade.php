@component('mail::message')
# New customer registration!

A new customer has registered their interest in being awesome:

Name: <strong>{{ $customer->getName() }}</strong>

Email: <{{ $customer->getPeeringemail() }}>

ASN: <strong>{{ $customer->getAutsys() }}</strong>

Date: <strong>{{ $customer->getDatejoin()->format('d/m/Y H:i') }}</strong>

Thanks,<br>
{{ config('app.name') }}
@endcomponent

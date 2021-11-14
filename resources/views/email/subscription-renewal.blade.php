@component('mail::message')
# Odnowienie usługi

Szanowni Państwo,

Ważność zakupionej usługi {{ $subscriptionType->slug == 'hosting' ? 'hostingu' : ($subscriptionType->slug == 'domain' ? 'domeny' : '') }} dobiega końca. W załączniku przesyłamy fakturę proforma. Prosimy o jej opłacenie, aby odnowić ważność usługi na kolejny okres. Po zaksięgowaniu kwoty niezwłocznie zostanie wysłana faktura VAT.

W razie pytań prosimy o kontakt.

--<br>
Zencore IT Services<br>
91-502 Łódź<br>
ul. Centralna 21a<br>
NIP: 7262626997<br>
REGON: 365924610<br>
tel. 733-830-833<br>
www.zencore.pl

@endcomponent

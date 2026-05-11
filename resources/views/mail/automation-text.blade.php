{{ $mailBody }}

---
@if ($ticketNumber)
Ticket: {{ $ticketNumber }}@if ($deviceLabel) — {{ $deviceLabel }}@endif

@endif
Bizo
© {{ date('Y') }} Bizo

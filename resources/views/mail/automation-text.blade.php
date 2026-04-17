{{ $mailBody }}

---
@if ($ticketNumber)
Ticket: {{ $ticketNumber }}@if ($deviceLabel) — {{ $deviceLabel }}@endif

@endif
MSW Repair
© {{ date('Y') }} MSW Repair

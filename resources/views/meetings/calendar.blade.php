@extends('layouts.app')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Meetings Calendar</h1>
    <a href="{{ route('meetings.index') }}" class="btn btn-outline-secondary">Back to list</a>
  </div>

  <div id="calendar" class="border rounded p-2"></div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  const container = document.getElementById('calendar');
  async function load(start, end){
    const params = new URLSearchParams();
    if(start) params.set('start', start.toISOString());
    if(end) params.set('end', end.toISOString());
    const res = await fetch('/meetings-feed?'+params.toString());
    const events = await res.json();
    render(events);
  }
  function render(events){
    container.innerHTML = '';
    if(!Array.isArray(events) || events.length===0){
      container.innerHTML = '<div class="text-muted p-3">No meetings scheduled in this range.</div>';
      return;
    }
    const list = document.createElement('div');
    list.className = 'vstack gap-2';
    events.forEach(ev => {
      const item = document.createElement('a');
      item.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
      item.href = ev.url || '#';
      const start = ev.start ? new Date(ev.start) : null;
      const end = ev.end ? new Date(ev.end) : null;
      item.innerHTML = `<div><strong>${ev.title || 'Meeting'}</strong><div class="small text-muted">${start?start.toLocaleString():''}${end?' — '+end.toLocaleTimeString():''}</div></div>`;
      list.appendChild(item);
    });
    container.appendChild(list);
  }
  const now = new Date();
  const weekAhead = new Date();
  weekAhead.setDate(now.getDate()+7);
  load(now, weekAhead);
});
</script>
@endsection
@endsection

@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">Meetings Calendar</h1>
        <a href="{{ route('meetings.create') }}" class="btn btn-primary">Schedule Meeting</a>
    </div>

    <div id="calendar"></div>
</div>
@endsection

@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/main.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 'auto',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        events: {
            url: '{{ route('meetings.feed') }}',
        },
        eventClick: function(info) {
            if (info.event.url) {
                window.location.href = info.event.url;
                info.jsEvent.preventDefault();
            }
        }
    });
    calendar.render();
});
</script>
@endsection



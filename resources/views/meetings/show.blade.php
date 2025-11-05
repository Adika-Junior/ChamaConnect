@extends('layouts.app')

@section('content')
<div class="container py-4">
  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
  @endif

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">{{ $meeting->title }}</h1>
    <div class="d-flex gap-2">
      <a href="{{ route('meetings.calendar') }}" class="btn btn-outline-secondary">Calendar</a>
      <div class="dropdown">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">Add to Calendar</button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="{{ route('meetings.ics', $meeting) }}">Apple/ICS</a></li>
          @php
            $start = $meeting->scheduled_at?->format('Ymd\THis\Z');
            $end = $meeting->scheduled_at && $meeting->duration ? $meeting->scheduled_at->copy()->addMinutes($meeting->duration)->format('Ymd\THis\Z') : $meeting->scheduled_at?->format('Ymd\THis\Z');
            $text = urlencode($meeting->title);
            $details = urlencode(($meeting->description ?? '')."\n".($meeting->meeting_link ?? ''));
            $location = urlencode($meeting->meeting_link ?? '');
          @endphp
          <li><a class="dropdown-item" target="_blank" href="https://www.google.com/calendar/render?action=TEMPLATE&text={{ $text }}&dates={{ $start }}/{{ $end }}&details={{ $details }}&location={{ $location }}&sf=true&output=xml">Google Calendar</a></li>
          <li><a class="dropdown-item" target="_blank" href="https://outlook.live.com/calendar/0/deeplink/compose?path=/calendar/action/compose&rru=addevent&startdt={{ $meeting->scheduled_at?->toIso8601String() }}&enddt={{ ($meeting->scheduled_at && $meeting->duration ? $meeting->scheduled_at->copy()->addMinutes($meeting->duration) : $meeting->scheduled_at)?->toIso8601String() }}&subject={{ $text }}&body={{ $details }}&location={{ $location }}">Outlook</a></li>
        </ul>
      </div>
      @can('update', $meeting)
        <a href="{{ route('meetings.edit', $meeting) }}" class="btn btn-primary">Edit</a>
      @endcan
    </div>
  </div>

  <div class="row g-3">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-body vstack gap-2">
          <div class="text-muted">Scheduled: {{ optional($meeting->scheduled_at)->format('D, M j Y, H:i') }} ({{ $meeting->duration ? $meeting->duration.' min' : 'N/A' }})</div>
          <div>{{ $meeting->description }}</div>

          @if($meeting->type === 'online' || $meeting->type === 'hybrid')
          <div class="mt-2">
            <a href="{{ $meeting->meeting_link ?? '#' }}" target="_blank" class="btn btn-success">Join Meeting</a>
          </div>
          @endif
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card">
        <div class="card-header">Participants</div>
        <div class="card-body">
          <div class="text-muted small">Waiting room {{ $meeting->has_waiting_room ? 'enabled' : 'disabled' }} • {{ $meeting->is_locked ? 'Locked' : 'Open' }}</div>
        </div>
      </div>
      <div class="card mt-3">
        <div class="card-header">Recordings</div>
        <div class="card-body">
          @if($meeting->recordings && $meeting->recordings->count())
            <ul class="list-group">
              @foreach($meeting->recordings as $rec)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <div>
                    @if($rec->processing_status === 'completed' || !$rec->processing_status)
                      <a href="{{ asset('storage/'.$rec->file_path) }}" target="_blank">{{ $rec->file_name }}</a>
                    @else
                      <span>{{ $rec->file_name }}</span>
                    @endif
                    @if($rec->processing_status === 'processing')
                      <span class="badge bg-warning ms-2">Processing...</span>
                    @elseif($rec->processing_status === 'failed')
                      <span class="badge bg-danger ms-2">Failed</span>
                    @elseif($rec->processing_status === 'pending')
                      <span class="badge bg-info ms-2">Pending</span>
                    @endif
                  </div>
                  <span class="text-muted small">{{ optional($rec->created_at)->diffForHumans() }}</span>
                </li>
              @endforeach
            </ul>
          @else
            <div class="text-muted small">No recordings yet. If you recently uploaded, processing may take a few minutes.</div>
          @endif
        </div>
      </div>
      <div class="card mt-3">
        <div class="card-header">Transcripts</div>
        <div class="card-body">
          @if($meeting->transcripts && $meeting->transcripts->count())
            <ul class="list-group">
              @foreach($meeting->transcripts as $tr)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <div>
                    @if($tr->file_path)
                      <a href="{{ asset('storage/'.$tr->file_path) }}" target="_blank">{{ $tr->file_name ?? 'Transcript file' }}</a>
                    @else
                      <span class="text-truncate" style="max-width: 240px;">{{ Str::limit($tr->content, 60) }}</span>
                    @endif
                  </div>
                  <div class="d-flex align-items-center gap-2">
                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('meetings.transcripts.download.txt', [$meeting, $tr]) }}">Download TXT</a>
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('meetings.transcripts.print', [$meeting, $tr]) }}" target="_blank">Print/PDF</a>
                    <span class="text-muted small">{{ optional($tr->created_at)->diffForHumans() }}</span>
                  </div>
                </li>
              @endforeach
            </ul>
          @else
            <div class="text-muted small">No transcripts yet.</div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">{{ $meeting->title }}</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('meetings.edit', $meeting) }}" class="btn btn-secondary">Edit</a>
            <form method="POST" action="{{ route('meetings.destroy', $meeting) }}" onsubmit="return confirm('Delete this meeting?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
    </div>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row g-3">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="mb-2 text-muted">Type: <strong>{{ ucfirst($meeting->type) }}</strong> • Status: <strong>{{ ucfirst($meeting->status ?? 'scheduled') }}</strong></div>
                    <div class="mb-2">Scheduled: <strong>{{ $meeting->scheduled_at?->format('Y-m-d H:i') }}</strong> ({{ $meeting->duration ? $meeting->duration.' min' : '—' }})</div>
                    <div class="mb-2">Participants: <strong>{{ $meeting->participants->count() }}</strong> / {{ config('services.webrtc.max_participants', 2) }}</div>
                    @if($meeting->has_waiting_room)
                        <div class="mb-2 d-flex align-items-center gap-2">
                          <span class="badge bg-info">Waiting Room Enabled</span>
                          @if($meeting->organizer_id === auth()->id())
                            <form method="POST" action="{{ route('meetings.control', $meeting) }}" class="d-inline">
                              @csrf
                              <input type="hidden" name="action" value="admit_all">
                              <button class="btn btn-sm btn-outline-primary">Admit All</button>
                            </form>
                          @endif
                        </div>
                    @endif
                    @if($meeting->password)
                        <div class="mb-2"><span class="badge bg-warning">Password Protected</span></div>
                    @endif
                    @if($meeting->is_locked)
                        <div class="mb-2"><span class="badge bg-danger">Meeting Locked</span></div>
                    @endif
                    @if($meeting->meeting_link)
                        <div class="mb-2">Link: <a href="{{ $meeting->meeting_link }}" target="_blank" rel="noopener">Open meeting link</a></div>
                    @endif
                    @if($meeting->contribution)
                        <div class="mb-2">Contribution: <a href="{{ route('contributions.show', $meeting->contribution) }}">{{ $meeting->contribution->title }}</a></div>
                    @endif
                    <p class="mb-0">{{ $meeting->description }}</p>
                </div>
            </div>

            @if($meeting->chat)
            <div class="card mt-3">
                <div class="card-header">Chat</div>
                <div class="card-body">
                    <a class="btn btn-outline-primary" href="{{ route('chats.show', $meeting->chat) }}">Open meeting chat</a>
                </div>
            </div>
            @endif

            <div class="card mt-3">
                <div class="card-header">Recordings & Transcripts</div>
                <div class="card-body">
                    <div class="mb-3 d-flex flex-wrap gap-2">
                        <button id="startCallBtn" class="btn btn-success">Join Meeting</button>
                        <button id="endCallBtn" class="btn btn-outline-danger" disabled>Leave Meeting</button>
                        <button id="toggleMicBtn" class="btn btn-outline-secondary" disabled>Mute Mic</button>
                        <button id="toggleCamBtn" class="btn btn-outline-secondary" disabled>Turn Camera Off</button>
                        <button id="screenShareBtn" class="btn btn-outline-info" disabled>Share Screen</button>
                        <button id="stopShareBtn" class="btn btn-outline-warning" disabled style="display:none;">Stop Sharing</button>
                        <button id="recordBtn" class="btn btn-outline-danger" disabled>Start Recording</button>
                        <button id="stopRecordBtn" class="btn btn-danger" disabled style="display:none;">Stop Recording</button>
                        <button id="toggleChatBtn" class="btn btn-outline-primary" disabled>Chat</button>
                        <button id="toggleVbBtn" class="btn btn-outline-dark" disabled>Virtual Background: Off</button>
                    </div>
                    <div class="position-relative">
                        <div id="videoContainer" class="row g-2 mb-3">
                            <div class="col-md-12 mb-2">
                                <div class="position-relative">
                                    <video id="localVideo" autoplay playsinline muted class="w-100 border rounded" style="max-height: 300px; background: #000;"></video>
                                    <span class="badge bg-primary position-absolute top-0 start-0 m-2">You</span>
                                </div>
                            </div>
                            <div id="remoteVideosContainer" class="col-12">
                                <p class="text-muted text-center">Waiting for other participants...</p>
                            </div>
                        </div>
                        
                        <!-- In-Meeting Chat Panel -->
                        <div id="chatPanel" class="position-absolute top-0 end-0 bg-white border rounded shadow-lg p-3" style="width: 300px; max-height: 400px; display: none; z-index: 1000;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">Meeting Chat</h6>
                                <button id="closeChatBtn" class="btn btn-sm btn-outline-secondary">&times;</button>
                            </div>
                            <div id="chatMessages" class="overflow-auto mb-2" style="max-height: 300px; min-height: 200px;">
                                <p class="text-muted small">No messages yet...</p>
                            </div>
                            <div class="input-group input-group-sm">
                                <input type="text" id="chatInput" class="form-control" placeholder="Type a message...">
                                <button id="sendChatBtn" class="btn btn-primary">Send</button>
                            </div>
                        </div>
                    </div>
                    <form class="row g-2" method="POST" action="{{ route('meetings.recordings.store', $meeting) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="col-auto">
                            <input type="file" name="recording" class="form-control" accept="audio/*,video/*">
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary" type="submit">Upload Recording</button>
                        </div>
                    </form>
                    <form class="row g-2 mt-2" method="POST" action="{{ route('meetings.transcripts.store', $meeting) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="col-12">
                            <textarea name="content" class="form-control" rows="3" placeholder="Paste transcript text..."></textarea>
                        </div>
                        <div class="col-md-8">
                            <input type="file" name="transcript_file" class="form-control" accept="text/plain,application/pdf">
                        </div>
                        <div class="col-md-4 d-grid">
                            <button class="btn btn-outline-primary" type="submit">Upload Transcript</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Participants @if($meeting->organizer_id === auth()->id()) <span class="badge bg-primary">Host</span> @endif</div>
                <ul id="participantsList" class="list-group list-group-flush">
                    @forelse($meeting->participants as $u)
                        <li class="list-group-item d-flex justify-content-between align-items-center" data-participant-id="{{ $u->id }}">
                            <span>{{ $u->name }}</span>
                            @if($meeting->organizer_id === auth()->id() && $u->id !== auth()->id())
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-sm btn-outline-secondary participant-mute" data-id="{{ $u->id }}" title="Mute">🔇</button>
                                    <button class="btn btn-sm btn-outline-danger participant-remove" data-id="{{ $u->id }}" title="Remove">✕</button>
                                </div>
                            @endif
                        </li>
                    @empty
                        <li class="list-group-item text-muted">No participants yet.</li>
                    @endforelse
                </ul>
                @php $isParticipant = auth()->check() ? $meeting->participants->contains('id', auth()->id()) : false; @endphp
                @auth
                <div class="card-body d-grid gap-2">
                    @if($meeting->is_locked && $meeting->organizer_id !== auth()->id() && !auth()->user()->isAdmin())
                        <div class="alert alert-warning mb-0">
                            <small>This meeting is locked. Only the host can allow new participants.</small>
                        </div>
                    @elseif($meeting->has_waiting_room && $meeting->organizer_id !== auth()->id())
                        @if(!$isParticipant)
                            <form method="POST" action="{{ route('meetings.join', $meeting) }}">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100">Join Waiting Room</button>
                            </form>
                            <small class="text-muted text-center">The host will admit you to the meeting.</small>
                        @else
                            <div class="alert alert-info mb-2">
                                <small>You're in the waiting room. Waiting for host to admit you...</small>
                            </div>
                            <form method="POST" action="{{ route('meetings.leave', $meeting) }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger w-100">Leave</button>
                            </form>
                        @endif
                    @elseif(!$isParticipant)
                        <form method="POST" action="{{ route('meetings.join', $meeting) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">Join Meeting</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('meetings.leave', $meeting) }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger w-100">Leave Meeting</button>
                        </form>
                    @endif
                </div>
                @endauth
            </div>
            <div class="card mt-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Breakout Rooms (beta)</span>
                    @if($meeting->organizer_id === auth()->id() || (auth()->check() && auth()->user()->isAdmin()))
                        <button id="createBreakoutBtn" class="btn btn-sm btn-outline-primary">+ Create</button>
                    @endif
                </div>
                <ul id="breakoutRoomsList" class="list-group list-group-flush">
                    <li class="list-group-item text-muted">No breakout rooms yet.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://unpkg.com/janus-gateway@0.12.4/janus.js"></script>
<script>
document.addEventListener('DOMContentLoaded', async () => {
  const meetingId = {{ $meeting->id }};
  const userId = {{ auth()->id() ?? 'null' }};
  const localVideo = document.getElementById('localVideo');
  const remoteVideosContainer = document.getElementById('remoteVideosContainer');
  const startBtn = document.getElementById('startCallBtn');
  const endBtn = document.getElementById('endCallBtn');
  const toggleMicBtn = document.getElementById('toggleMicBtn');
  const toggleCamBtn = document.getElementById('toggleCamBtn');
  const screenShareBtn = document.getElementById('screenShareBtn');
  const stopShareBtn = document.getElementById('stopShareBtn');
  const recordBtn = document.getElementById('recordBtn');
  const stopRecordBtn = document.getElementById('stopRecordBtn');
  const toggleChatBtn = document.getElementById('toggleChatBtn');
  const toggleVbBtn = document.getElementById('toggleVbBtn');
  const chatPanel = document.getElementById('chatPanel');
  const chatMessages = document.getElementById('chatMessages');
  const chatInput = document.getElementById('chatInput');
  const sendChatBtn = document.getElementById('sendChatBtn');
  const closeChatBtn = document.getElementById('closeChatBtn');

  const isHost = {{ $meeting->organizer_id === auth()->id() ? 'true' : 'false' }};
  let janus = null;
  let publisherHandle = null;
  let subscriberHandles = new Map(); // Map of publisher IDs to subscriber handles
  let localStream = null;
  let screenStream = null;
  let micEnabled = true;
  let camEnabled = true;
  let vbEnabled = false;
  let remoteStreams = new Map(); // Map of publisher IDs to video elements
  let roomInfo = null;
  let mediaRecorder = null;
  let recordedChunks = [];

  // Check if Janus is available
  if (!Janus.isWebrtcSupported()) {
    alert('WebRTC not supported in this browser');
    return;
  }

  async function joinMeeting() {
    try {
      // Get Janus room info from backend
      const response = await fetch('{{ route("meetings.janus.session", $meeting) }}', {
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
      });
      roomInfo = await response.json();

      if (roomInfo.error) {
        alert('Failed to connect: ' + roomInfo.error);
        return;
      }

      // Get user media first
      localStream = await navigator.mediaDevices.getUserMedia({
        video: { width: 1280, height: 720 },
        audio: true
      });
      localVideo.srcObject = localStream;
      
      // Initialize Janus connection
      janus = new Janus({
        server: roomInfo.ws_url,
        success: function() {
          console.log('Janus connected');
          
          // Attach to VideoRoom plugin as publisher
          janus.attach({
            plugin: 'janus.plugin.videoroom',
            opaqueId: 'ttms-user-' + userId,
            success: function(handle) {
              publisherHandle = handle;
              
              // Join room as publisher
              const register = {
                request: 'join',
                room: roomInfo.room_id,
                ptype: 'publisher',
                display: 'User ' + userId
              };
              
              handle.send({
                message: register,
                success: function(result) {
                  console.log('Joined room as publisher:', result);
                  
                  // List existing publishers and subscribe
                  if (result && result.publishers) {
                    result.publishers.forEach(publisher => {
                      subscribeToPublisher(publisher.id, publisher.display);
                    });
                  }
                  
                  // Publish our stream
                  publishOwnFeed(handle);
                  
                  // Listen for new publishers
                  handle.on('message', function(msg, jsep) {
                    if (msg && msg.unpublished) {
                      // Publisher left
                      removeRemoteVideo(msg.unpublished);
                    } else if (msg && msg.publishers) {
                      // New publisher joined
                      msg.publishers.forEach(publisher => {
                        subscribeToPublisher(publisher.id, publisher.display);
                      });
                    }
                    
                    if (jsep) {
                      handle.handleRemoteJsep({ jsep: jsep });
                    }
                  });
                },
                error: function(error) {
                  console.error('Failed to join room:', error);
                  alert('Failed to join meeting room');
                }
              });
            },
            error: function(error) {
              console.error('Failed to attach plugin:', error);
              alert('Failed to connect to meeting room');
            },
            iceState: function(state) {
              console.log('ICE state:', state);
            },
            webrtcState: function(on) {
              console.log('WebRTC state:', on ? 'up' : 'down');
            }
          });
        },
        error: function(error) {
          console.error('Janus connection error:', error);
          alert('Failed to connect to Janus server');
        },
        destroyed: function() {
          console.log('Janus connection destroyed');
        }
      });

      // Update UI
      startBtn.disabled = true;
      endBtn.disabled = false;
      toggleMicBtn.disabled = false;
      toggleCamBtn.disabled = false;
    } catch (err) {
      console.error('Join meeting error:', err);
      alert('Failed to join meeting: ' + err.message);
      if (localStream) {
        localStream.getTracks().forEach(t => t.stop());
      }
    }
  }

  function publishOwnFeed(handle) {
    handle.createOffer({
      tracks: [
        { kind: 'audio', capture: true, recv: false },
        { kind: 'video', capture: true, recv: false }
      ],
      success: function(jsep) {
        handle.send({
          message: {
            request: 'configure',
            audio: true,
            video: true
          },
          jsep: jsep
        });
      },
      error: function(error) {
        console.error('Failed to create offer:', error);
      }
    });
  }

  function subscribeToPublisher(publisherId, display) {
    if (subscriberHandles.has(publisherId)) {
      return; // Already subscribed
    }

    const remoteDiv = document.createElement('div');
    remoteDiv.className = 'col-md-6 mb-2';
    remoteDiv.id = 'remote-' + publisherId;
    remoteDiv.innerHTML = `
      <div class="position-relative">
        <video id="remote-${publisherId}-video" autoplay playsinline class="w-100 border rounded" style="max-height: 300px; background: #000;"></video>
        <span class="badge bg-success position-absolute top-0 start-0 m-2">${display}</span>
      </div>
    `;
    remoteVideosContainer.innerHTML = ''; // Clear waiting message
    remoteVideosContainer.appendChild(remoteDiv);
    
    const remoteVideo = document.getElementById(`remote-${publisherId}-video`);
    
    // Create separate handle for subscriber
    janus.attach({
      plugin: 'janus.plugin.videoroom',
      opaqueId: 'ttms-subscriber-' + publisherId,
      success: function(subHandle) {
        subscriberHandles.set(publisherId, subHandle);
        
        // Join as subscriber
        subHandle.send({
          message: {
            request: 'join',
            room: roomInfo.room_id,
            ptype: 'subscriber',
            streams: [{ feed: publisherId }]
          },
          success: function(result) {
            console.log('Subscribed to publisher:', publisherId);
            
            // Create offer for subscription
            subHandle.createOffer({
              tracks: [
                { kind: 'audio', capture: false, recv: true },
                { kind: 'video', capture: false, recv: true }
              ],
              success: function(jsep) {
                subHandle.send({
                  message: {
                    request: 'start',
                    room: roomInfo.room_id
                  },
                  jsep: jsep
                });
              }
            });
            
            // Handle incoming stream
            subHandle.on('message', function(msg, jsep) {
              if (jsep) {
                subHandle.handleRemoteJsep({ jsep: jsep });
              }
            });
            
            subHandle.on('remoteStream', function(stream) {
              remoteVideo.srcObject = stream;
              remoteStreams.set(publisherId, { video: remoteVideo, div: remoteDiv });
            });
          }
        });
      }
    });
  }

  function removeRemoteVideo(publisherId) {
    const remote = remoteStreams.get(publisherId);
    if (remote) {
      remote.div.remove();
      remoteStreams.delete(publisherId);
    }
    
    const subHandle = subscriberHandles.get(publisherId);
    if (subHandle) {
      subHandle.detach();
      subscriberHandles.delete(publisherId);
    }
  }

  function leaveMeeting() {
    if (localStream) {
      localStream.getTracks().forEach(track => track.stop());
      localStream = null;
    }
    
    if (publisherHandle) {
      publisherHandle.send({
        message: { request: 'leave' }
      });
      publisherHandle.detach();
      publisherHandle = null;
    }
    
    // Detach all subscriber handles
    subscriberHandles.forEach((handle, id) => {
      handle.detach();
    });
    subscriberHandles.clear();
    
    if (janus) {
      janus.destroy();
      janus = null;
    }
    
    // Clear remote videos
    remoteStreams.forEach((remote, id) => {
      remote.div.remove();
    });
    remoteStreams.clear();
    
    localVideo.srcObject = null;
    remoteVideosContainer.innerHTML = '<p class="text-muted text-center">Waiting for other participants...</p>';
    
    // Update UI
    startBtn.disabled = false;
    endBtn.disabled = true;
    toggleMicBtn.disabled = true;
    toggleCamBtn.disabled = true;
  }

  startBtn.addEventListener('click', joinMeeting);
  endBtn.addEventListener('click', leaveMeeting);
  
  toggleMicBtn.addEventListener('click', () => {
    if (localStream) {
      micEnabled = !micEnabled;
      localStream.getAudioTracks().forEach(t => t.enabled = micEnabled);
      toggleMicBtn.textContent = micEnabled ? 'Mute Mic' : 'Unmute Mic';
    }
  });
  
  toggleCamBtn.addEventListener('click', () => {
    if (localStream) {
      camEnabled = !camEnabled;
      localStream.getVideoTracks().forEach(t => t.enabled = camEnabled);
      toggleCamBtn.textContent = camEnabled ? 'Turn Camera Off' : 'Turn Camera On';
    }
  });

  // Screen Sharing
  screenShareBtn.addEventListener('click', async () => {
    try {
      screenStream = await navigator.mediaDevices.getDisplayMedia({
        video: { cursor: 'always' },
        audio: true
      });
      
      // Replace video track in publisher
      if (publisherHandle && localStream) {
        const videoTrack = screenStream.getVideoTracks()[0];
        publisherHandle.replaceTrack({ track: videoTrack });
        localVideo.srcObject = screenStream;
        
        screenShareBtn.style.display = 'none';
        stopShareBtn.style.display = 'inline-block';
        
        // Stop sharing when user clicks browser stop button
        videoTrack.onended = () => {
          stopScreenShare();
        };
      }
    } catch (err) {
      console.error('Screen share error:', err);
      alert('Failed to share screen');
    }
  });

  stopShareBtn.addEventListener('click', stopScreenShare);
  
  function stopScreenShare() {
    if (screenStream) {
      screenStream.getTracks().forEach(track => track.stop());
      screenStream = null;
    }
    
    if (publisherHandle && localStream) {
      const videoTrack = localStream.getVideoTracks()[0];
      if (videoTrack) {
        publisherHandle.replaceTrack({ track: videoTrack });
        localVideo.srcObject = localStream;
      }
    }
    
    screenShareBtn.style.display = 'inline-block';
    stopShareBtn.style.display = 'none';
  }

  // Recording
  recordBtn.addEventListener('click', () => {
    if (!localStream) return;
    
    recordedChunks = [];
    const options = { mimeType: 'video/webm;codecs=vp9,opus' };
    
    try {
      mediaRecorder = new MediaRecorder(localStream, options);
      
      mediaRecorder.ondataavailable = (event) => {
        if (event.data.size > 0) {
          recordedChunks.push(event.data);
        }
      };
      
      mediaRecorder.onstop = () => {
        const blob = new Blob(recordedChunks, { type: 'video/webm' });
        const formData = new FormData();
        formData.append('recording', blob, `meeting-${meetingId}-${Date.now()}.webm`);
        formData.append('_token', '{{ csrf_token() }}');
        
        fetch('{{ route("meetings.recordings.store", $meeting) }}', {
          method: 'POST',
          body: formData
        }).then(() => {
          alert('Recording saved!');
        });
      };
      
      mediaRecorder.start();
      recordBtn.style.display = 'none';
      stopRecordBtn.style.display = 'inline-block';
    } catch (err) {
      console.error('Recording error:', err);
      alert('Failed to start recording');
    }
  });

  stopRecordBtn.addEventListener('click', () => {
    if (mediaRecorder && mediaRecorder.state !== 'inactive') {
      mediaRecorder.stop();
      recordBtn.style.display = 'inline-block';
      stopRecordBtn.style.display = 'none';
    }
  });

  // Chat Panel
  let chatOpen = false;
  toggleChatBtn.addEventListener('click', () => {
    chatOpen = !chatOpen;
    chatPanel.style.display = chatOpen ? 'block' : 'none';
  });

  closeChatBtn.addEventListener('click', () => {
    chatOpen = false;
    chatPanel.style.display = 'none';
  });

  sendChatBtn.addEventListener('click', sendChatMessage);
  chatInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') sendChatMessage();
  });

  function sendChatMessage() {
    const message = chatInput.value.trim();
    if (!message) return;

    // Send to backend
    fetch('{{ route("chats.show", $meeting->chat ?? 0) }}/messages', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({ message })
    }).then(() => {
      chatInput.value = '';
    });

    // Add to UI immediately
    addChatMessage(userId, message);
  }

  function addChatMessage(userId, message) {
    const msgDiv = document.createElement('div');
    msgDiv.className = 'mb-2';
    msgDiv.innerHTML = `<strong>User ${userId}:</strong> ${message}`;
    if (chatMessages.querySelector('.text-muted')) {
      chatMessages.innerHTML = '';
    }
    chatMessages.appendChild(msgDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
  }

  // Host Controls
  document.querySelectorAll('.participant-mute').forEach(btn => {
    btn.addEventListener('click', () => {
      const participantId = btn.dataset.id;
      controlParticipant('mute', participantId);
    });
  });

  document.querySelectorAll('.participant-remove').forEach(btn => {
    btn.addEventListener('click', () => {
      const participantId = btn.dataset.id;
      if (confirm('Remove this participant from the meeting?')) {
        controlParticipant('remove', participantId);
      }
    });
  });

  function controlParticipant(action, participantId) {
    fetch('{{ route("meetings.control", $meeting) }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({ action, participant_id: parseInt(participantId) })
    });
  }

  // Listen for control events (host muting/removing you)
  if (window.Echo) {
    window.Echo.private('meeting.' + meetingId)
      .listen('.App\\Events\\MeetingControl', (e) => {
        if (e.participant_id === userId) {
          if (e.action === 'mute' && localStream) {
            localStream.getAudioTracks().forEach(t => t.enabled = false);
            micEnabled = false;
            toggleMicBtn.textContent = 'Unmute Mic';
          } else if (e.action === 'remove') {
            alert('You have been removed from the meeting');
            leaveMeeting();
          }
        }

        // Host admitted all waiting participants: auto-join if user is waiting
        if (e.action === 'admit_all') {
          const waitingJoinBtn = document.querySelector('form[action$="/meetings/'+meetingId+'/join"] button');
          const waitingJoinForm = document.querySelector('form[action$="/meetings/'+meetingId+'/join"]');
          if (waitingJoinForm) {
            waitingJoinForm.submit();
          }
        }
      });
  }

  // Listen for chat messages
  if (window.Echo && {{ $meeting->chat ? $meeting->chat->id : 'null' }}) {
    window.Echo.private('chat.' + {{ $meeting->chat ? $meeting->chat->id : 'null' }})
      .listen('.App\\Events\\MessageSent', (e) => {
        addChatMessage(e.user.id, e.message.content);
      });
  }

  // Enable buttons after joining
  const originalJoinMeeting = joinMeeting;
  joinMeeting = async function() {
    await originalJoinMeeting();
    screenShareBtn.disabled = false;
    recordBtn.disabled = false;
    toggleChatBtn.disabled = false;
    toggleVbBtn.disabled = false;
  };

  // Virtual Background (basic client-side blur)
  toggleVbBtn.addEventListener('click', () => {
    vbEnabled = !vbEnabled;
    if (vbEnabled) {
      localVideo.classList.add('vb-blur');
      toggleVbBtn.textContent = 'Virtual Background: On';
    } else {
      localVideo.classList.remove('vb-blur');
      toggleVbBtn.textContent = 'Virtual Background: Off';
    }
  });

  // Breakout rooms (minimal UI)
  const breakoutRoomsList = document.getElementById('breakoutRoomsList');
  const createBreakoutBtn = document.getElementById('createBreakoutBtn');

  async function refreshBreakoutRooms() {
    try {
      const res = await fetch('{{ route("meetings.breakouts.index", $meeting) }}');
      const rooms = await res.json();
      breakoutRoomsList.innerHTML = '';
      if (!rooms.length) {
        breakoutRoomsList.innerHTML = '<li class="list-group-item text-muted">No breakout rooms yet.</li>';
      }
      rooms.forEach(r => {
        const li = document.createElement('li');
        li.className = 'list-group-item d-flex justify-content-between align-items-center';
        li.innerHTML = `<span>${r.name}</span>`;
        if ({{ $meeting->organizer_id === auth()->id() ? 'true' : (auth()->check() && auth()->user()->isAdmin() ? 'true' : 'false') }}) {
          const del = document.createElement('button');
          del.className = 'btn btn-sm btn-outline-danger';
          del.textContent = 'Delete';
          del.addEventListener('click', async () => {
            if (!confirm('Delete this breakout room?')) return;
            await fetch(`{{ url('meetings/'.$meeting->id.'/breakout-rooms') }}/${r.id}`, {
              method: 'DELETE',
              headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            refreshBreakoutRooms();
          });
          const right = document.createElement('div');
          right.appendChild(del);
          li.appendChild(right);
        }
        breakoutRoomsList.appendChild(li);
      });
    } catch (e) {
      // ignore
    }
  }

  if (breakoutRoomsList) {
    refreshBreakoutRooms();
  }

  if (createBreakoutBtn) {
    createBreakoutBtn.addEventListener('click', async () => {
      const name = prompt('Breakout room name');
      if (!name) return;
      await fetch('{{ route("meetings.breakouts.store", $meeting) }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ name })
      });
      refreshBreakoutRooms();
    });
  }
});
</script>
<style>
  /* Simple blur virtual background for local preview */
  #localVideo.vb-blur {
    filter: blur(8px) saturate(1.1) contrast(1.05);
  }
</style>
@endsection



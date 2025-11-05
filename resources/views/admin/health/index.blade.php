@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6">
  <h1 class="text-2xl font-semibold mb-4">Health Dashboard</h1>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white p-4 rounded shadow">
      <div class="text-slate-500 text-sm">Database</div>
      <div class="text-lg font-semibold {{ $dbOk ? 'text-green-700' : 'text-red-700' }}">{{ $dbOk ? 'OK' : 'ERROR' }}</div>
      @unless($dbOk)
      <div class="text-xs text-red-700 break-words mt-1">{{ $dbMsg }}</div>
      @endunless
    </div>

    <div class="bg-white p-4 rounded shadow">
      <div class="text-slate-500 text-sm">Cache</div>
      <div class="text-lg font-semibold {{ $cacheOk ? 'text-green-700' : 'text-red-700' }}">{{ $cacheOk ? 'OK' : 'ERROR' }}</div>
    </div>

    <div class="bg-white p-4 rounded shadow">
      <div class="text-slate-500 text-sm">Queue</div>
      <div class="text-lg font-semibold">Driver: {{ $queueDriver }}</div>
      @if($queueSize > 0)
      <div class="text-sm {{ $queueSize > 100 ? 'text-yellow-700' : 'text-slate-600' }}">Size: {{ $queueSize }}</div>
      @endif
    </div>
  </div>

  @if(isset($webhookErrorRate))
  <div class="bg-white p-4 rounded shadow mb-6">
    <div class="text-slate-500 text-sm">Webhook Error Rate</div>
    <div class="text-2xl font-semibold {{ $webhookErrorRate > 5 ? 'text-red-700' : ($webhookErrorRate > 1 ? 'text-yellow-700' : 'text-green-700') }}">
      {{ $webhookErrorRate }}%
    </div>
    <div class="text-xs text-slate-500 mt-1">Based on processed + failed events</div>
  </div>
  @endif

  @if(!empty($redisStats))
  <div class="bg-white p-4 rounded shadow mb-6">
    <div class="text-lg font-semibold mb-2">Redis Stats</div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
      @if(isset($redisStats['used_memory_human']))
      <div>
        <div class="text-slate-500">Memory Used</div>
        <div class="font-semibold">{{ $redisStats['used_memory_human'] }}</div>
      </div>
      @endif
      @if(isset($redisStats['connected_clients']))
      <div>
        <div class="text-slate-500">Clients</div>
        <div class="font-semibold">{{ $redisStats['connected_clients'] }}</div>
      </div>
      @endif
      @if(isset($redisStats['keyspace_hits']) && isset($redisStats['keyspace_misses']))
      @php
        $total = ($redisStats['keyspace_hits'] ?? 0) + ($redisStats['keyspace_misses'] ?? 0);
        $hitRate = $total > 0 ? round((($redisStats['keyspace_hits'] ?? 0) / $total) * 100, 1) : 0;
      @endphp
      <div>
        <div class="text-slate-500">Cache Hit Rate</div>
        <div class="font-semibold">{{ $hitRate }}%</div>
      </div>
      @endif
    </div>
  </div>
  @endif

  <div class="bg-white p-4 rounded shadow mb-6">
    <div class="flex items-center justify-between">
      <div class="text-lg font-semibold">Webhook Events</div>
      <a class="text-blue-600 text-sm" href="{{ route('admin.payments.webhooks.index') }}">Open Webhooks Dashboard</a>
    </div>
    <div class="grid grid-cols-3 gap-4 mt-3">
      <div>
        <div class="text-slate-500 text-sm">Received</div>
        <div class="text-xl font-semibold">{{ $webhookCounts['received'] }}</div>
      </div>
      <div>
        <div class="text-slate-500 text-sm">Processed</div>
        <div class="text-xl font-semibold">{{ $webhookCounts['processed'] }}</div>
      </div>
      <div>
        <div class="text-slate-500 text-sm">Failed</div>
        <div class="text-xl font-semibold text-red-700">{{ $webhookCounts['failed'] }}</div>
      </div>
    </div>
    <div class="mt-4 overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50">
          <tr>
            <th class="px-4 py-2 text-left">ID</th>
            <th class="px-4 py-2 text-left">Provider</th>
            <th class="px-4 py-2 text-left">Status</th>
            <th class="px-4 py-2 text-left">Processed At</th>
            <th class="px-4 py-2 text-left">Error</th>
          </tr>
        </thead>
        <tbody>
          @foreach($recentWebhookEvents as $e)
          <tr class="border-t">
            <td class="px-4 py-2">#{{ $e->id }}</td>
            <td class="px-4 py-2">{{ $e->provider }}</td>
            <td class="px-4 py-2">{{ $e->status }}</td>
            <td class="px-4 py-2">{{ $e->processed_at }}</td>
            <td class="px-4 py-2 truncate" title="{{ $e->error }}">{{ $e->error }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <div class="text-slate-500 text-xs">Generated at {{ now() }}</div>
</div>
@endsection



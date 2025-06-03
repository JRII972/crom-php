{{-- Onglets pour naviguer entre la description et les sessions --}}
<div role="tablist" class="tabs tabs-lifted">
  <a role="tab" class="tab {{ $activeTab === 'description' ? 'tab-active' : '' }}" id="tab-description">Description</a>
  <a role="tab" class="tab {{ $activeTab === 'sessions' ? 'tab-active' : '' }}" id="tab-sessions">Sessions ({{ count($sessions ?? []) }})</a>
</div>

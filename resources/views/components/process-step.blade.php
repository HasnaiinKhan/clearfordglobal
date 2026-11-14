@props(['step'])

<div class="process-step animate-on-scroll">
    <div class="step-number" aria-hidden="true">{{ $step['step'] }}</div>
    <div class="step-content">
        <h3 class="step-title">{{ $step['title'] }}</h3>
        <p class="step-desc">{{ $step['description'] }}</p>
    </div>
</div>

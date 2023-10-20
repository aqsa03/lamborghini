@props(['status'])

@if ($status === App\Enums\EpisodeStatus::DRAFT->value)
<div class="badge__episode_draft">
@elseif ($status === App\Enums\EpisodeStatus::PUBLISHED->value)
<div class="badge__episode_published">
@else
<div class="badge__episode_defaultstatus">
@endif
    {{ trans("general.".strtolower($slot)) }}
</div>

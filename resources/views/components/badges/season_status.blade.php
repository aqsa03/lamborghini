@props(['status'])

@if ($status === App\Enums\SeasonStatus::DRAFT->value)
<div class="badge__season_draft">
@elseif ($status === App\Enums\SeasonStatus::PUBLISHED->value)
<div class="badge__season_published">
@else
<div class="badge__season_defaultstatus">
@endif
    {{ trans("general.".strtolower($slot)) }}
</div>

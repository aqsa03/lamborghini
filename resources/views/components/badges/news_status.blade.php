@props(['status'])

@if ($status === App\Enums\NewsStatus::DRAFT->value)
<div class="badge__news_draft">
@elseif ($status === App\Enums\NewsStatus::PUBLISHED->value)
<div class="badge__news_published">
@else
<div class="badge__news_defaultstatus">
@endif
    {{ trans("general.".strtolower($slot)) }}
</div>

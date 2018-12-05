<div style="margin-left: {{ $doc->depth * .75 }}rem"{!! $doc->type == 'Menu Heading' ? ' class="font-weight-bold"' : '' !!}>
    {{ $doc->title }}
</div>
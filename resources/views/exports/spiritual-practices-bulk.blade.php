<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bulk Export - {{ $type }}</title>
    <style>
        @page { margin: 2cm; }
        body { font-family: 'Times New Roman', serif; line-height: 1.6; color: #1a1a1a; }
        .header { text-align: center; margin-bottom: 2cm; border-bottom: 2px solid #8b4513; padding-bottom: 1cm; }
        .type { text-transform: uppercase; letter-spacing: 2px; color: #8b4513; font-size: 12pt; margin-bottom: 0.5cm; }
        h1 { font-size: 32pt; margin: 0; color: #000; }
        
        .item-container { page-break-after: always; }
        .item-container:last-child { page-break-after: auto; }
        .item-header { text-align: center; margin-bottom: 1.5cm; padding-bottom: 0.5cm; border-bottom: 1px dashed #ccc; }
        .item-title { font-size: 24pt; margin: 0; color: #000; }
        .category { font-style: italic; color: #666; margin-top: 0.5cm; }
        
        .content { margin-top: 1cm; text-align: justify; font-size: 12pt; }
        .section { margin-top: 1cm; }
        h3 { color: #8b4513; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9pt; color: #999; border-top: 1px solid #eee; padding-top: 10pt; }
    </style>
</head>
<body>
    <div class="header">
        <div class="type">EXPORT BUNDLE</div>
        <h1>{{ $type }}s</h1>
        <div style="margin-top: 1cm;">Exported on {{ $date }}</div>
    </div>
    
    @foreach($items as $item)
    <div class="item-container">
        <div class="item-header">
            <h2 class="item-title">{{ $item->title }}</h2>
            <div class="category">{{ $item->category }}</div>
        </div>
        <div class="content">
            @if($item->description)
                <div class="section">
                    <h3>Description</h3>
                    {!! $item->description !!}
                </div>
            @endif
            @if($item->content)
                <div class="section">
                    <h3>Content/Instructions</h3>
                    {!! $item->content !!}
                </div>
            @endif
            @if($type === 'Incantation' && $item->spoken_text)
                <div class="section">
                    <h3>Spoken Text</h3>
                    {!! $item->spoken_text !!}
                </div>
                @if($item->intended_outcome)
                <div class="section">
                    <h3>Intended Outcome</h3>
                    {!! $item->intended_outcome !!}
                </div>
                @endif
            @endif
            @if($type === 'Ritual' && $item->symbolic_meaning)
                <div class="section">
                    <h3>Symbolic Meaning</h3>
                    {!! $item->symbolic_meaning !!}
                </div>
                @if($item->steps)
                <div class="section">
                    <h3>Steps</h3>
                    <ol>
                    @foreach($item->steps as $step)
                        <li>{{ is_array($step) && isset($step['step']) ? $step['step'] : $step }}</li>
                    @endforeach
                    </ol>
                </div>
                @endif
            @endif
        </div>
    </div>
    @endforeach

    <div class="footer">
        &copy; {{ date('Y') }} Watered - {{ $type }} Export
    </div>
</body>
</html>

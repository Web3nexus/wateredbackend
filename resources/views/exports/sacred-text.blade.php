<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $collection->name }}</title>
    <style>
        @page {
            margin: 2cm;
        }

        body {
            font-family: 'Times New Roman', serif;
            line-height: 1.6;
            color: #1a1a1a;
            background-color: #fff;
        }

        .header {
            text-align: center;
            margin-bottom: 3cm;
            border-bottom: 2px solid #8b4513;
            padding-bottom: 1cm;
        }

        .tradition {
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #8b4513;
            font-size: 12pt;
            margin-bottom: 0.5cm;
        }

        h1 {
            font-size: 32pt;
            margin: 0;
            color: #000;
        }

        .category {
            font-style: italic;
            color: #666;
            margin-top: 0.5cm;
        }

        .chapter {
            page-break-before: always;
            margin-bottom: 2cm;
        }

        .chapter-header {
            text-align: center;
            margin-bottom: 1.5cm;
        }

        .chapter-number {
            font-weight: bold;
            color: #8b4513;
            font-size: 14pt;
        }

        .chapter-title {
            font-size: 24pt;
            margin: 10pt 0;
        }

        .entry {
            margin-bottom: 1.5cm;
            position: relative;
        }

        .entry-number {
            font-weight: bold;
            color: #8b4513;
            display: inline-block;
            margin-right: 10pt;
        }

        .entry-text {
            font-size: 13pt;
            text-align: justify;
        }

        .translation {
            margin-top: 10pt;
            padding-left: 20pt;
            border-left: 1px solid #ddd;
            font-style: italic;
            color: #444;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 9pt;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 10pt;
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100pt;
            color: rgba(139, 69, 19, 0.03);
            z-index: -1000;
            white-space: nowrap;
        }
    </style>
</head>

<body>
    <div class="watermark">WATERED</div>

    <div class="header">
        <div class="tradition">{{ $collection->tradition->name ?? '' }}</div>
        <h1>{{ $collection->name }}</h1>
        <div class="category">{{ $collection->category->name ?? '' }}</div>
        <div style="margin-top: 2cm;">
            Exported on {{ $date }}
        </div>
    </div>

    @foreach($collection->chapters as $chapter)
        <div class="chapter">
            <div class="chapter-header">
                <div class="chapter-number">CHAPTER {{ $chapter->number }}</div>
                <h2 class="chapter-title">{{ $chapter->name }}</h2>
                @if($chapter->description)
                    <div style="font-style: italic; color: #555;">{{ $chapter->description }}</div>
                @endif
            </div>

            @foreach($chapter->entries as $entry)
                <div class="entry">
                    <div class="entry-text">
                        <span class="entry-number">{{ $entry->number }}</span>
                        {!! nl2br(e($entry->text)) !!}
                    </div>

                    @foreach($entry->translations as $translation)
                        @if($translation->language_code !== 'original')
                            <div class="translation">
                                <strong>{{ strtoupper($translation->language_code) }}:</strong>
                                {{ $translation->text }}
                            </div>
                        @endif
                    @endforeach
                </div>
            @endforeach
        </div>
    @endforeach

    <div class="footer">
        &copy; {{ date('Y') }} Watered - Sacred Text Export
    </div>
</body>

</html>
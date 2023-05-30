{{-- Multiple find-replace view for commands. For variables that can be used, see the actual view itself. --}}
@include('form-items.combined.multiple-find-replace-with-label', [
    'data' => [
        'subjectSelector' => [
            ['closest'  => '.command-views'],
            ['find'     => '.test-view .input-container :input[name]'],
        ]
    ]
])
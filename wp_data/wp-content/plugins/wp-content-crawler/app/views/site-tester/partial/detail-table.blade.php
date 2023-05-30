{{--
    Required variables:
        array $tableData: A key-value pair array. Each key is a section name. Its value contains an array, which is
            a key-value pair where keys are the names of the items, and values are their values.

 --}}
<div class="container-fluid">
    <div class="row">

        <table class="detail-table">
            <tbody>
            @foreach($tableData as $sectionName => $data)
                @include('site-tester.partial.section-title', [
                    'title' => $sectionName
                ])

                @foreach($data as $dataName => $dataContent)
                    @include('site-tester.partial.single-detail', [
                        'name'      => $dataName,
                        'content'   => $dataContent
                    ])
                @endforeach
            @endforeach
            </tbody>
        </table>

    </div>
</div>
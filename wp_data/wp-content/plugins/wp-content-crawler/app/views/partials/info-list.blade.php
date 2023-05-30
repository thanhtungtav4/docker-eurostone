@if(\WPCCrawler\Objects\Informing\Informer::getInfos())
    <div class="info-list-container">
        @if (!isset($noTitle) || !$noTitle)
            <span class="title">{{ _wpcc("Information") }}</span>
        @endif
        <ul>
            @foreach(\WPCCrawler\Objects\Informing\Informer::getInfos() as $info)
                <li>
                    <?php /** @param WPCCrawler\Objects\Informing\Information $info */ ?>
                    <div class="message">
                        <span class="name">{{ _wpcc('Message') }}:</span>
                        <span class="description">{{ $info->getMessage() }}</span>
                    </div>

                    @if($info->getDetails())
                        <div class="details">
                            <span class="name">{{ _wpcc('Details') }}:</span>
                            <span class="description">{{ $info->getDetails() }}</span>
                        </div>
                    @endif

                    <div class="type">
                        <span class="name">{{ _wpcc('Type') }}:</span>
                        <span class="description">{{ $info->getType() }}</span>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
@endif
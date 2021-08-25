<tr>
    <td class="work-status">
        <span class="label label-success">Завершаны</span>
    </td>
    <td class="subject">
        <div class="table-content">
            <p class="blue-grey-500">
                <a href="{{ route('workshop::subtitles::view', ['release' => $release->getId()]) }}" title="Перайсці да перакладу">
                    {{ $release->getMovieTranslatedName() }}
                </a>
            </p>
            <p>
                <a href="{{ route('workshop::subtitles::view', ['release' => $release->getId()]) }}" title="Перайсці да перакладу">
                    <span class="blue-grey-400">{{ $release->getRipName() }}</span>
                </a>
            </p>
            <span class="blue-grey-400">{{ $release->getCreatedAt()->format('j/n/Y') }}</span>
            <span class="label label-sm label-success">Завершаны</span>
        </div>
    </td>
    <td class="work-progress">
        <div class="progress progress-xs table-content">
            <div class="progress-bar progress-bar-success progress-bar-indicating" style="width: {{ $release->getReadiness() }}"
                 role="progressbar">
                <span class="sr-only">{{ $release->getReadiness() }}</span>
            </div>
        </div>
        <span>{{ $release->getReadiness() }}</span>
    </td>
    <td class="members">
        <div class="addMember">
            <ul class="addMember-items">
                @foreach ($releaseMembers['selected'][$release->getId()] as $member)
                    <li class="addMember-item">
                        <img class="avatar" src="{{ $member['avatar'] }}" title="{{ $member['id'] }}">
                    </li>
                @endforeach
            </ul>
        </div>
    </td>
    <td class="actions">
        <div class="table-content">
            <i data-toggle="tooltip" data-original-title="Налады" class="icon wb-wrench" onclick="$(location).attr('href', '{{ $release->getUrl() }}');"></i>
            <i class="icon wb-stats-bars" data-url="{{ route('workshop::releases::statistic::view', ['release' => $release->getId()]) }}" data-toggle="slidePanel" data-original-title="Статыстыка" ></i>
            <i data-toggle="tooltip" data-original-title="Файл субтытраў" class="icon ion-closed-captioning" onclick="$(location).attr('href', '{{ route('workshop::releases::subrip::view', ['release' => $release->getId()]) }}');"></i>
            <i data-toggle="tooltip" data-original-title="Пераклад" class="icon wb-indent-increase" onclick="$(location).attr('href', '{{ route('workshop::subtitles::view', ['release' => $release->getId()]) }}');"></i>
        </div>
    </td>
</tr>

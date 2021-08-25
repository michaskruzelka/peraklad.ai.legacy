<tr>
    <td class="work-status">
        <span class="label label-warning">Выдалены</span>
    </td>
    <td class="subject">
        <div class="table-content">
            <p class="blue-grey-500">{{ $release->getMovieTranslatedName() }}</p>
            <p><span class="blue-grey-400">{{ $release->getRipName() }}</span></p>
            <span class="blue-grey-400">{{ $release->getCreatedAt()->format('j/n/Y') }}</span>
            <span class="label label-sm label-warning">Выдалены</span>
        </div>
    </td>
    <td class="work-progress">
        <div class="progress progress-xs table-content">
            <div class="progress-bar progress-bar-warning progress-bar-indicating" style="width: {{ $release->getReadiness() }}"
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
        <i data-toggle="tooltip" data-original-title="Налады" class="icon wb-wrench" onclick="$(location).attr('href', '{{ $release->getUrl() }}');"></i>
    </td>
</tr>
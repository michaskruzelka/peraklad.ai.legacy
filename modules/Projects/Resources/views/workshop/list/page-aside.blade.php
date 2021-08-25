<section class="page-aside-section" id="releasesSearch">
    <h5 class="page-aside-title">Пошук</h5>
    <div class="list-group">
        <span class="list-group-item">
            <div class="form-group">
                <select class="form-control" id="users-list-selector">
                    <optgroup>
                        <option value="{{ route('workshop::projects::list', [
                            'userId' => 'all'
                        ]) }}" @if ('all' == $usId) selected @endif>усе чужыя праекты</option>
                        <option value="{{ route('workshop::projects::my') }}" @if (Auth::id() == $usId) selected @endif>{{ Auth::id() }}</option>
                    </optgroup>
                    <optgroup>
                        @foreach ($users as $userId)
                            <option value="{{ route('workshop::projects::list', [
                                'userId' => $userId
                            ]) }}"
                            @if($userId == $usId) selected @endif
                            >{{ $userId }}</option>
                        @endforeach
                    </optgroup>
                </select>
            </div>
            @if ('all' != $usId)
            <div class="form-group">
                <select class="form-control" id="part-list-selector">
                    <option value="{{ route('workshop::projects::list', [
                        'userId' => $usId,
                        'in' => 'owner'
                    ]) }}" @if ('owner' == $in) selected @endif>кіруе</option>
                    <option value="{{ route('workshop::projects::list', [
                        'userId' => $usId,
                        'in' => 'member'
                    ]) }}" @if ('member' == $in) selected @endif>удзельнічае</option>
                </select>
            </div>
            @endif
            <div class="form-group">
                <select class="form-control" id="mode-list-selector">
                    <option value="{{ route('workshop::projects::list', [
                        'userId' => $usId,
                        'in' => $in,
                        'mode' => 'all'
                    ]) }}" @if ('all' == $mode) selected @endif>усе рэжымы</option>
                    <option value="{{ route('workshop::projects::list', [
                        'userId' => $usId,
                        'in' => $in,
                        'mode' => 'private'
                    ]) }}" @if ('private' == $mode) selected @endif>прыватны</option>
                    <option value="{{ route('workshop::projects::list', [
                        'userId' => $usId,
                        'in' => $in,
                        'mode' => 'public'
                    ]) }}" @if ('public' == $mode) selected @endif>публічны</option>
                </select>
            </div>
            <form method="get" action="{{ route($routeName, $routeBaseParams + [
                'page' => 1,
                'in' => $in,
                'mode' => $mode,
                'state' => 'all'
            ]) }}">
                <div class="form-group">
                    <div class="input-group">
                        <input type="text" maxlength="100" class="form-control" name="search" value="{{ request()->get('search') }}" placeholder="Увядзіце назву...">
                        <span class="input-group-btn">
                          <button type="submit" class="btn btn-primary"><i class="wb-search" aria-hidden="true"></i></button>
                        </span>
                    </div>
                </div>
            </form>
            @if ( ! empty($years))
                <div class="form-group">
                    <div class="input-group">
                        @foreach ($years as $year => $count)
                            <input type="checkbox" name="year" value="{{ route($routeName, $routeBaseParams + [
                                'page' => 1,
                                'in' => $in,
                                'mode' => $mode,
                                'state' => 'all',
                                'year' => $year
                            ]) }}" data-labelauty="{{ $year }}|{{ $year }}" @if (request()->get('year') == $year) checked @endif
                            />
                        @endforeach
                    </div>
                </div>
            @endif
            @if ( ! empty($langs))
                <div class="form-group">
                    <div class="input-group">
                        @foreach ($langs as $count => $lang)
                            <input type="checkbox" name="lang" value="{{ route($routeName, $routeBaseParams + [
                                'page' => 1,
                                'in' => $in,
                                'mode' => $mode,
                                'state' => 'all',
                                'lang' => $lang['iso']
                            ]) }}" data-labelauty="{{ $lang['be'] }}|{{ $lang['be'] }}" @if (request()->get('lang') == $lang['iso']) checked @endif
                            />
                        @endforeach
                    </div>
                </div>
            @endif
        </span>
    </div>
</section>

<section class="page-aside-section">
    <h5 class="page-aside-title">Тлумачэнні</h5>
    <div class="list-group">
        <span class="list-group-item">

        </span>
    </div>
</section>

<script>
    $('[name="year"], [name="lang"]').labelauty();
    $('[name="year"], [name="lang"]').on('change', function() {
        var url = $(this).val();
        if ( ! $(this).is(':checked')) {
            url = url.replace(/&?((year)|(lang))=[^&]*/gi, "");
        }
        $(location).attr("href", url);
    });
    var select2Config = {
        width: "style",
        language: "be"
    };
    $('#users-list-selector').select2(select2Config).on('change', function(e) {
        e.preventDefault();
        $(location).attr("href", $(e.currentTarget).val());
    });
    select2Config['minimumResultsForSearch'] = 'Infinity';
    $('#part-list-selector, #mode-list-selector').select2(select2Config).on('change', function(e) {
        e.preventDefault();
        $(location).attr("href", $(e.currentTarget).val());
    });
</script>